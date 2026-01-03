<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['schedule.course', 'student', 'teacher'])
            ->latest('created_at');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('starts_at', '>=', $request->date_from);
            });
        }
        if ($request->filled('date_to')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('starts_at', '<=', $request->date_to);
            });
        }

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
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        // Filter by attendance status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'both_present':
                    $query->where('student_present', true)
                          ->where('teacher_present', true);
                    break;
                case 'student_absent':
                    $query->where('student_present', false);
                    break;
                case 'teacher_absent':
                    $query->where('teacher_present', false);
                    break;
                case 'both_absent':
                    $query->where('student_present', false)
                          ->where('teacher_present', false);
                    break;
            }
        }

        $attendances = $query->paginate(15);

        // Statistics
        $totalSessions = Attendance::count();
        $bothPresent = Attendance::where('student_present', true)
            ->where('teacher_present', true)
            ->count();
        $partialAttendance = Attendance::where(function($q) {
            $q->where('student_present', true)->where('teacher_present', false)
              ->orWhere('student_present', false)->where('teacher_present', true);
        })->count();
        $bothAbsent = Attendance::where('student_present', false)
            ->where('teacher_present', false)
            ->count();

        // Get filter options
        $students = User::where('role', 'student')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $courses = Course::orderBy('title')->get();

        return view('admin.attendances.index', compact(
            'attendances',
            'students',
            'teachers',
            'courses',
            'totalSessions',
            'bothPresent',
            'partialAttendance',
            'bothAbsent'
        ));
    }

    /**
     * Display the specified attendance record
     */
    public function show(Attendance $attendance)
    {
        $attendance->load(['schedule.course', 'student', 'teacher']);

        return view('admin.attendances.show', compact('attendance'));
    }
}
