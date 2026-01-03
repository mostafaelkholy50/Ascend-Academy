<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of reports
     */
    public function index(Request $request)
    {
        $query = Report::with(['student', 'teacher', 'course'])
            ->latest('report_date');

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
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

        // Filter by mastery score range
        if ($request->filled('mastery_min')) {
            $query->where('mastery_score', '>=', $request->mastery_min);
        }
        if ($request->filled('mastery_max')) {
            $query->where('mastery_score', '<=', $request->mastery_max);
        }

        $reports = $query->paginate(15);

        // Statistics
        $totalReports = Report::count();
        $averageMastery = Report::whereNotNull('mastery_score')->avg('mastery_score');
        $recentReports = Report::where('report_date', '>=', now()->subDays(30))->count();

        // Get filter options
        $students = User::where('role', 'student')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $courses = Course::orderBy('title')->get();

        return view('admin.reports.index', compact(
            'reports',
            'students',
            'teachers',
            'courses',
            'totalReports',
            'averageMastery',
            'recentReports'
        ));
    }

    /**
     * Display the specified report
     */
    public function show(Report $report)
    {
        $report->load(['student', 'teacher', 'course']);

        return view('admin.reports.show', compact('report'));
    }
}
