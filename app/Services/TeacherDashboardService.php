<?php

namespace App\Services;

use App\Repositories\TeacherDashboardRepository;
use App\Services\StudentEvaluationService;
use App\Models\User;
use Carbon\Carbon;

class TeacherDashboardService
{
    protected $repository;
    protected $evaluationService;

    public function __construct(TeacherDashboardRepository $repository, StudentEvaluationService $evaluationService)
    {
        $this->repository = $repository;
        $this->evaluationService = $evaluationService;
    }

    public function getDashboardData(User $teacher): array
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        $weekSchedules = $this->repository->getWeekSchedules($teacher, $weekStart, $weekEnd);

        // Filter today's schedules from already loaded week schedules
        $todaySchedules = $weekSchedules->filter(function($schedule) {
            return $schedule->starts_at->isToday();
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
        $thisMonthHours = $this->repository->getThisMonthHours($teacher);
        
        // Add evaluation bonus if earned
        $teacherHour = \App\Models\TeacherHour::where('teacher_id', $teacher->id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();
            
        $bonusHours = 0;
        if ($teacherHour && str_contains($teacherHour->notes ?? '', 'Evaluation Bonus')) {
            $thisMonthHours += 0.5;
            $bonusHours = 0.5;
        }

        $stats = [
            'total_students' => $myStudents->count(),
            'today_classes' => $todaySchedules->count(),
            'this_month_hours' => $thisMonthHours,
            'bonus_hours' => $bonusHours,
            'pending_reports' => $studentsNeedingReports->count(),
            'completed_this_week' => $completedThisWeek->count(),
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
