<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Course;
use App\Models\User;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user();

        // Build the query
        $query = Report::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->latest('report_date');

        // Apply filters
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }

        // Get paginated reports
        $reports = $query->paginate(15)->withQueryString();

        // Get courses for filter dropdown (only courses the student has reports for)
        $courses = Course::whereHas('reports', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get();

        // Get teachers for filter dropdown (only teachers who have written reports for this student)
        $teachers = User::where('role', 'teacher')
            ->whereHas('teacherReports', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })->get();

        return view('student.reports.index', compact('reports', 'courses', 'teachers'));
    }

    public function show($id)
    {
        $student = auth()->user();

        // Get the report and ensure it belongs to the authenticated student
        $report = Report::with(['teacher', 'course', 'student'])
            ->where('student_id', $student->id)
            ->findOrFail($id);

        return view('student.reports.show', compact('report'));
    }
}
