<?php

namespace App\Services;

use App\Repositories\ParentDashboardRepository;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParentDashboardService
{
    protected $repository;

    public function __construct(ParentDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardData(User $parent, Request $request)
    {
        $children = $this->repository->getChildren($parent);
        $childrenIds = $children->pluck('id');

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $allSchedules = $this->repository->getSchedulesForChildren($childrenIds, $weekStart, $weekEnd, $request);
        $todaySchedules = $this->repository->getTodaySchedules($childrenIds, $request);
        $upcomingSchedules = $this->repository->getUpcomingSchedules($childrenIds, $request);
        $weekAttendances = $this->repository->getAttendancesForChildren($childrenIds, $weekStart, $weekEnd);
        $latestReports = $this->repository->getLatestReports($childrenIds);

        // Calculate statistics for each child
        foreach ($children as $child) {
            $child->active_courses = $child->enrollments
                ->where('status', 'active')
                ->count();

            $child->today_classes = $todaySchedules->where('student_id', $child->id)->count();

            $childWeekSchedules = $allSchedules->where('student_id', $child->id)->count();

            $childPresentCount = $weekAttendances->where('student_id', $child->id)
                ->where('student_present', true)
                ->count();

            $child->attendance_rate = $childWeekSchedules > 0
                ? round(($childPresentCount / $childWeekSchedules) * 100)
                : 0;

            $child->latest_report = $latestReports->where('student_id', $child->id)->first();
        }

        $stats = [
            'total_children' => $children->count(),
            'total_active_courses' => $children->sum('active_courses'),
            'today_total_classes' => $todaySchedules->count(),
            'pending_reports' => $this->repository->getPendingReportsCount($childrenIds),
        ];

        $unpaidPayments = $this->repository->getUnpaidPayments($childrenIds, now()->month, now()->year);

        return [
            'children' => $children,
            'todaySchedules' => $todaySchedules,
            'upcomingSchedules' => $upcomingSchedules,
            'stats' => $stats,
            'unpaidPayments' => $unpaidPayments,
        ];
    }
}
