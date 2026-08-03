<?php

namespace App\Services;

use App\Repositories\TeacherDashboardRepository;
use App\Repositories\TeacherHoursRepository;
use App\Services\StudentEvaluationService;
use App\Models\User;
use App\Models\TeacherHour;
use Carbon\Carbon;

class TeacherDashboardService
{
    protected $repository;
    protected $evaluationService;
    protected $hoursRepository;

    public function __construct(TeacherDashboardRepository $repository, StudentEvaluationService $evaluationService, TeacherHoursRepository $hoursRepository)
    {
        $this->repository = $repository;
        $this->evaluationService = $evaluationService;
        $this->hoursRepository = $hoursRepository;
    }

    public function getDashboardData(User $teacher): array
    {
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();
        
        $weekSchedules = $this->repository->getWeekSchedules($teacher, $weekStart, $weekEnd);

        // Filter today's schedules from already loaded week schedules
        $todaySchedules = $weekSchedules->filter(function($schedule) {
            return $schedule->starts_at->isToday();
        })->values();
        $upcomingTodaySchedules = $todaySchedules->filter(function ($schedule) use ($now) {
            return $schedule->starts_at->isFuture();
        })->values();

        $myStudents = $this->repository->getMyStudents($teacher);

        // Calculate student progress from loaded counts
        $myStudents->each(function($student) {
            $student->progress = $student->total_sessions > 0
                ? round(($student->completed_sessions / $student->total_sessions) * 100)
                : 0;
        });

        // Use StudentEvaluationService for pending and recent evaluations
        $studentsNeedingReports = $this->evaluationService->getPendingEvaluations($teacher);
        $recentReports = $this->evaluationService->getTeacherEvaluations($teacher)->take(5);
        $recentResources = $this->repository->getRecentResources($teacher);

        // Calculate statistics efficiently
        $completedThisWeek = $weekSchedules->where('status', 'completed');
        $scheduledThisWeek = $weekSchedules->where('status', 'scheduled');
        $thisMonthHours = $this->repository->getThisMonthHours($teacher);
        $thisMonthEarnings = 0;
        $hoursMonthTotal = 0;
        
        // Add evaluation bonus if earned
        $teacherHour = TeacherHour::where('teacher_id', $teacher->id)
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->first();
            
        $bonusHours = 0;
        if ($teacherHour && str_contains($teacherHour->notes ?? '', 'Evaluation Bonus')) {
            $thisMonthHours += 0.5;
            $bonusHours = 0.5;
        }

        $monthWindowStart = $now->copy()->startOfMonth();
        $monthWindowEnd = $now->copy()->endOfMonth();
        $monthAttendances = $this->hoursRepository->getAttendancesQueryForMonth($teacher, $monthWindowStart, $monthWindowEnd)
            ->with('schedule')
            ->get();

        $hoursMonthTotal = $monthAttendances->sum(function ($attendance) {
            if (!$attendance->schedule) {
                return 0;
            }

            $duration = $attendance->schedule->getDurationInHours();

            if (!$attendance->student_present && $attendance->remark === 'Waited Half Time') {
                return $duration / 2;
            }

            return $duration;
        });

        if ($bonusHours > 0) {
            $hoursMonthTotal += $bonusHours;
        }

        $thisMonthEarnings = $hoursMonthTotal * ($teacher->hourly_rate ?? 0);

        $todayHours = $todaySchedules->sum(fn ($schedule) => $schedule->getDurationInHours());
        $upcomingHoursToday = $upcomingTodaySchedules->sum(fn ($schedule) => $schedule->getDurationInHours());
        $completionRate = $weekSchedules->count() > 0
            ? round(($completedThisWeek->count() / $weekSchedules->count()) * 100)
            : 0;
        $averageSessionLength = $completedThisWeek->count() > 0
            ? round($completedThisWeek->sum(fn ($schedule) => $schedule->getDurationInHours()) / $completedThisWeek->count(), 1)
            : 0;

        $stats = [
            'total_students' => $myStudents->count(),
            'today_classes' => $todaySchedules->count(),
            'upcoming_today' => $upcomingTodaySchedules->count(),
            'today_hours' => $todayHours,
            'upcoming_hours_today' => $upcomingHoursToday,
            'this_month_hours' => $thisMonthHours,
            'this_month_earnings' => $thisMonthEarnings,
            'bonus_hours' => $bonusHours,
            'pending_reports' => $studentsNeedingReports->count(),
            'completed_this_week' => $completedThisWeek->count(),
            'scheduled_this_week' => $scheduledThisWeek->count(),
            'completion_rate' => $completionRate,
            'average_session_length' => $averageSessionLength,
        ];

        return compact(
            'teacher',
            'todaySchedules',
            'weekSchedules',
            'myStudents',
            'studentsNeedingReports',
            'recentReports',
            'recentResources',
            'stats'
        );
    }
}
