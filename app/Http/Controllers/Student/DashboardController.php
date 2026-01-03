<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Resource;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Get student's enrollments with courses (eager load to avoid N+1)
        $enrollments = $student->enrollments()
            ->with('course')
            ->where('status', 'active')
            ->get();

        // Get all schedules at once for efficiency
        $allSchedules = Schedule::where('student_id', $student->id)
            ->with(['teacher', 'course'])
            ->get();

        // Calculate progress for each enrollment using already loaded schedules
        foreach ($enrollments as $enrollment) {
            $courseSchedules = $allSchedules->where('course_id', $enrollment->course_id);
            $totalSessions = $courseSchedules->count();
            $completedSessions = $courseSchedules->where('status', 'completed')->count();

            $enrollment->progress = $totalSessions > 0
                ? round(($completedSessions / $totalSessions) * 100)
                : 0;
            
            $enrollment->total_sessions = $totalSessions;
            $enrollment->completed_sessions = $completedSessions;
        }

        // Filter schedules from already loaded collection
        $todaySchedules = $allSchedules->filter(function($schedule) {
            return $schedule->starts_at->isToday() && $schedule->status === 'scheduled';
        })->sortBy('starts_at')->values();

        $weekSchedules = $allSchedules->filter(function($schedule) {
            return $schedule->starts_at->isCurrentWeek();
        })->sortBy('starts_at')->values();

        // Recent reports from teachers
        $recentReports = Report::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->latest('report_date')
            ->take(5)
            ->get();

        // Recent resources assigned to student
        $recentResources = Resource::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->latest()
            ->take(5)
            ->get();

        // Calculate statistics from already loaded data
        $completedSchedules = $allSchedules->where('status', 'completed');
        $stats = [
            'total_courses' => $enrollments->count(),
            'today_classes' => $todaySchedules->count(),
            'completed_sessions' => $completedSchedules->count(),
            'total_sessions' => $allSchedules->count(),
            'completed_this_week' => $completedSchedules->filter(function($schedule) {
                return $schedule->starts_at->isCurrentWeek();
            })->count(),
        ];

        // Check for unpaid payments for the current month
        $unpaidPayments = EnrollmentPayment::whereHas('enrollment', function($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->where('status', 'active');
            })
            ->whereMonth('month', now()->month)
            ->whereYear('month', now()->year)
            ->where('payment_status', '!=', 'paid')
            ->with(['enrollment.course'])
            ->get();

        return view('student.dashboard', compact(
            'student',
            'enrollments',
            'todaySchedules',
            'weekSchedules',
            'recentReports',
            'recentResources',
            'stats',
            'unpaidPayments'
        ));
    }
}
