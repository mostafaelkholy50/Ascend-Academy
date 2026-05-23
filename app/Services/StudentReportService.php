<?php

namespace App\Services;

use App\Repositories\StudentReportRepository;
use App\Models\User;
use Illuminate\Http\Request;

class StudentReportService
{
    protected $repository;

    public function __construct(StudentReportRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getIndexData(User $student, Request $request): array
    {
        $reports = $this->repository->getReportsQuery($student, $request)->paginate(15)->withQueryString();
        $courses = $this->repository->getCoursesWithReports($student);
        $teachers = $this->repository->getTeachersWithReports($student);

        return compact('reports', 'courses', 'teachers');
    }

    public function getReport(User $student, int $id)
    {
        return $this->repository->getReport($student, $id);
    }
}
