<?php

namespace App\Services;

use App\Repositories\StudentDashboardRepository;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StudentDashboardService
{
    protected $repository;

    public function __construct(StudentDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardData(User $student): array
    {
        $enrollments = $this->repository->getEnrollments($student);
        $progressStats = $this->repository->getCourseProgressStats($student);
        
        $todaySchedules = $this->repository->getTodaySchedules($student);
        $weekSchedules = $this->repository->getWeekSchedules($student);
        
        $recentReports = $this->repository->getRecentReports($student);
        $recentResources = $this->repository->getRecentResources($student);
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $unpaidPayments = $this->repository->getUnpaidPayments($student, $currentMonth, $currentYear);

        // Calculate progress for each enrollment using aggregated stats
        foreach ($enrollments as $enrollment) {
            $stats = $progressStats->get($enrollment->course_id);
            
            $totalSessions = $stats->total ?? 0;
            $completedSessions = $stats->completed ?? 0;

            $enrollment->progress = $totalSessions > 0
                ? round(($completedSessions / $totalSessions) * 100)
                : 0;
            
            $enrollment->total_sessions = $totalSessions;
            $enrollment->completed_sessions = $completedSessions;
        }

        // Calculate statistics using repository counts
        $stats = [
            'total_courses' => $enrollments->count(),
            'today_classes' => $todaySchedules->count(),
            'completed_sessions' => $this->repository->getCompletedSchedulesCount($student),
            'total_sessions' => $this->repository->getTotalSchedulesCount($student),
            'completed_this_week' => $this->repository->getCompletedThisWeekCount($student),
        ];

        return compact(
            'enrollments',
            'todaySchedules',
            'weekSchedules',
            'recentReports',
            'recentResources',
            'stats',
            'unpaidPayments'
        );
    }
}
