<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Report;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display a listing of reports created by the teacher
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();

        $query = Report::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->latest('report_date');

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }

        $reports = $query->paginate(15);

        // Get all students this teacher has taught
        $students = User::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('name')->get();

        // Get all courses this teacher teaches
        $courses = Course::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('title')->get();

        return view('teacher.reports.index', compact('reports', 'students', 'courses'));
    }

    /**
     * Show the form for creating a new report
     */
    public function create(Request $request)
    {
        $teacher = Auth::user();

        // Get students from completed schedules
        $students = User::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)
              ->where('status', 'completed');
        })->orderBy('name')->get();

        // Pre-select student if provided
        $selectedStudent = $request->query('student_id');

        // Get courses for the selected student
        $courses = [];
        if ($selectedStudent) {
            $courses = Course::whereHas('schedules', function($q) use ($teacher, $selectedStudent) {
                $q->where('teacher_id', $teacher->id)
                  ->where('student_id', $selectedStudent);
            })->orderBy('title')->get();
        }

        return view('teacher.reports.create', compact('students', 'courses', 'selectedStudent'));
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'level' => 'nullable|string|max:100',
            'mastery_score' => 'nullable|integer|min:0|max:100',
            'strengths' => 'nullable|string|max:1000',
            'weaknesses' => 'nullable|string|max:1000',
            'behavior' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'report_date' => 'required|date|before_or_equal:today',
        ]);

        try {
            $report = Report::create([
                'teacher_id' => Auth::id(),
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
                'level' => $request->level,
                'mastery_score' => $request->mastery_score,
                'strengths' => $request->strengths,
                'weaknesses' => $request->weaknesses,
                'behavior' => $request->behavior,
                'notes' => $request->notes,
                'report_date' => $request->report_date,
            ]);

            // Load relationships for email
            $report->load(['student', 'teacher', 'course']);

            // Send email notification to student
            try {
                $report->student->notify(new \App\Notifications\ReportAddedNotification($report));
            } catch (\Exception $e) {
                \Log::error('Failed to send report notification to student: ' . $e->getMessage());
            }

            // Send email notification to parent(s)
            try {
                $parents = $report->student->parents;
                foreach ($parents as $parent) {
                    $parent->notify(new \App\Notifications\ReportAddedNotification($report));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send report notification to parents: ' . $e->getMessage());
            }

            return redirect()->route('teacher.reports.show', $report->id)
                ->with('success', 'Report created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create report: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified report
     */
    public function show(Report $report)
    {
        // Ensure teacher can only view their own reports
        if ($report->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $report->load(['student', 'course', 'teacher']);

        return view('teacher.reports.show', compact('report'));
    }

    /**
     * Show the form for editing the report
     */
    public function edit(Report $report)
    {
        // Ensure teacher can only edit their own reports
        if ($report->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $teacher = Auth::user();

        $students = User::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('name')->get();

        $courses = Course::whereHas('schedules', function($q) use ($teacher, $report) {
            $q->where('teacher_id', $teacher->id)
              ->where('student_id', $report->student_id);
        })->orderBy('title')->get();

        return view('teacher.reports.edit', compact('report', 'students', 'courses'));
    }

    /**
     * Update the specified report
     */
    public function update(Request $request, Report $report)
    {
        // Ensure teacher can only update their own reports
        if ($report->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'level' => 'nullable|string|max:100',
            'mastery_score' => 'nullable|integer|min:0|max:100',
            'strengths' => 'nullable|string|max:1000',
            'weaknesses' => 'nullable|string|max:1000',
            'behavior' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'report_date' => 'required|date|before_or_equal:today',
        ]);

        $report->update($request->only([
            'student_id', 'course_id', 'level', 'mastery_score',
            'strengths', 'weaknesses', 'behavior', 'notes', 'report_date'
        ]));

        return back()->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified report
     */
    public function destroy(Report $report)
    {
        // Ensure teacher can only delete their own reports
        if ($report->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $report->delete();

        return redirect()->route('teacher.reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Get courses for a specific student (AJAX endpoint)
     */
    public function getStudentCourses(User $student)
    {
        $teacher = Auth::user();

        $courses = Course::whereHas('schedules', function($q) use ($teacher, $student) {
            $q->where('teacher_id', $teacher->id)
              ->where('student_id', $student->id);
        })->orderBy('title')->get(['id', 'title']);

        return response()->json($courses);
    }

    /**
     * Quick create report from schedule
     */
    public function quickCreate(Schedule $schedule)
    {
        // Ensure teacher owns this schedule
        if ($schedule->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if schedule is completed
        if ($schedule->status !== 'completed') {
            return back()->with('error', 'Can only create reports for completed sessions.');
        }

        $schedule->load(['student', 'course']);

        return view('teacher.reports.quick-create', compact('schedule'));
    }
}
