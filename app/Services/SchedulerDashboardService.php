<?php

namespace App\Services;

use App\Repositories\SchedulerDashboardRepository;
use App\Models\User;
use Illuminate\Http\Request;

class SchedulerDashboardService
{
    protected $repository;

    public function __construct(SchedulerDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardData(User $user, Request $request): array
    {
        $search = $request->input('search');
        
        $todaySchedules = $this->repository->getTodaySchedules();
        $upcomingSchedules = $this->repository->getUpcomingSchedules();
        $totalStudents = $this->repository->getStudentsCount();
        $totalTeachers = $this->repository->getTeachersCount();
        $pendingAttendance = $this->repository->getPendingAttendanceCount();

        $monthlyRevenue = 0;
        if ($user->can('manage accounting')) {
            $monthlyRevenue = $this->repository->getMonthlyRevenue();
        }

        $totalReports = 0;
        if ($user->can('manage quality')) {
            $totalReports = $this->repository->getReportsCount();
        }

        $searchResults = [];
        if ($search) {
            $searchResults = $this->repository->searchUsers($search);
        }

        return compact(
            'todaySchedules',
            'upcomingSchedules',
            'totalStudents',
            'totalTeachers',
            'pendingAttendance',
            'monthlyRevenue',
            'totalReports',
            'searchResults',
            'search'
        );
    }
}
