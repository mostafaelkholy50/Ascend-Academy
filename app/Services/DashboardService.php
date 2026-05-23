<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    protected $repository;

    public function __construct(DashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardData()
    {
        $user = auth()->user();
        $data = [];

        // Common Data (Admin/SuperAdmin)
        if ($user->hasRole(['Admin', 'SuperAdmin'])) {
            $userCounts = $this->repository->getUserCountsByRole();
            $data['totalUsers'] = $userCounts->sum();
            $data['totalStudents'] = $userCounts->get('Student', 0);
            $data['totalTeachers'] = $userCounts->get('Teacher', 0);
            $data['totalParents'] = $userCounts->get('Parent', 0);
            $data['newInquiries'] = $this->repository->getPendingInquiriesCount();
            $data['recentEnrollments'] = $this->repository->getRecentEnrollments();
            $data['recentInquiries'] = $this->repository->getRecentInquiries();
            
            // Trends for charts
            $data['enrollmentTrends'] = $this->repository->getMonthlyEnrollmentTrends();
        }

        // Deep Analysis & Comparisons (For relevant roles)
        if ($user->hasRole(['Admin', 'SuperAdmin', 'Accountant', 'QualityControl'])) {
            $data['comparison'] = $this->repository->getMonthlyComparisonData();
            
            if ($user->hasRole(['Admin', 'SuperAdmin'])) {
                $data['conversionRate'] = $this->repository->getInquiryConversionRate();
                $data['topCourses'] = $this->repository->getTopCoursesPerformance();
                $data['teacherRankings'] = $this->repository->getTeacherPerformanceRanking();
            }
        }

        // Accounting Data
        if ($user->can('manage accounting') || $user->hasRole(['Admin', 'SuperAdmin', 'Accountant'])) {
            $currentMonth = now();
            $lastMonth = now()->subMonth();
            $data['monthlyRevenue'] = $this->repository->getRevenueForMonth($currentMonth->month, $currentMonth->year);
            $lastMonthRevenue = $this->repository->getRevenueForMonth($lastMonth->month, $lastMonth->year);
            $data['revenueGrowth'] = $lastMonthRevenue > 0
                ? round((($data['monthlyRevenue'] - $lastMonthRevenue) / $lastMonthRevenue) * 100)
                : 0;
            $data['revenueTrends'] = $this->repository->getMonthlyRevenueTrends();
        }

        // Quality/Education Data
        if ($user->can('manage quality') || $user->hasRole(['Admin', 'SuperAdmin', 'QualityControl'])) {
            $data['evaluationsSummary'] = $this->repository->getEvaluationsSummary();
            $data['attendanceSummary'] = $this->repository->getAttendanceSummary();
        }

        return $data;
    }
}
