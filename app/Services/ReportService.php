<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use App\Filters\ReportFilter;
use App\Models\Report;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class ReportService
{
    protected $repository;
    protected $filter;

    public function __construct(ReportRepository $repository, ReportFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function getIndexData(Request $request, int $perPage = 15)
    {
        $query = $this->repository->getReportsQuery()->latest('report_date');
        $query = $this->filter->apply($query, $request);

        // Statistics (using clone to keep the base query with filters)
        $totalReports = (clone $query)->count();
        $averageMastery = (clone $query)->whereNotNull('mastery_score')->avg('mastery_score');
        $recentReports = (clone $query)->where('report_date', '>=', now()->subDays(30))->count();

        $reports = $query->paginate($perPage);

        // Get filter options
        $students = User::where('role', 'student')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $courses = Course::orderBy('title')->get();

        return compact(
            'reports',
            'students',
            'teachers',
            'courses',
            'totalReports',
            'averageMastery',
            'recentReports'
        );
    }
}
