<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Resource;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        // Get all schedules for the week at once (reduces queries)
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        $weekSchedules = Schedule::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->whereBetween('starts_at', [$weekStart, $weekEnd])
            ->orderBy('starts_at')
            ->get();

        // Filter today's schedules from already loaded week schedules
        $todaySchedules = $weekSchedules->filter(function($schedule) {
            return $schedule->starts_at->isToday();
        })->values();

        // Get my students with optimized query
        $myStudents = User::whereHas('schedules', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->with(['enrollments.course'])
            ->withCount([
                'schedules as total_sessions' => function($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                },
                'schedules as completed_sessions' => function($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id)
                      ->where('status', 'completed');
                }
            ])
            ->take(6)
            ->get();

        // Calculate student progress from loaded counts
        $myStudents->each(function($student) {
            $student->progress = $student->total_sessions > 0
                ? round(($student->completed_sessions / $student->total_sessions) * 100)
                : 0;
        });

        // Get students needing reports (completed sessions without reports in last 7 days)
        $studentsNeedingReports = Schedule::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->where('status', 'completed')
            ->where('starts_at', '>=', now()->subDays(7))
            ->whereDoesntHave('attendance') // Can be used as proxy for report creation
            ->orderBy('starts_at', 'desc')
            ->take(5)
            ->get()
            ->unique('student_id');

        // Recent reports
        $recentReports = Report::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->latest('report_date')
            ->take(5)
            ->get();

        // Recent resources
        $recentResources = Resource::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->take(5)
            ->get();

        // Calculate statistics efficiently
        $completedThisWeek = $weekSchedules->where('status', 'completed');
        $stats = [
            'total_students' => $myStudents->count(),
            'today_classes' => $todaySchedules->count(),
            'this_month_hours' => Schedule::where('teacher_id', $teacher->id)
                ->where('status', 'completed')
                ->whereMonth('starts_at', now()->month)
                ->whereYear('starts_at', now()->year)
                ->get()
                ->sum(fn($schedule) => $schedule->getDurationInHours()),
            'pending_reports' => $studentsNeedingReports->count(),
            'completed_this_week' => $completedThisWeek->count(),
        ];

        return view('teacher.dashboard', compact(
            'teacher',
            'todaySchedules',
            'weekSchedules',
            'myStudents',
            'studentsNeedingReports',
            'recentReports',
            'recentResources',
            'stats'
        ));
    }
}
