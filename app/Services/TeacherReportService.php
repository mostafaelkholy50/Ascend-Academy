<?php

namespace App\Services;

use App\Repositories\TeacherReportRepository;
use App\Models\User;
use App\Models\Report;
use App\Filters\TeacherReportFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeacherReportService
{
    protected $repository;

    public function __construct(TeacherReportRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getIndexData(User $teacher, Request $request, TeacherReportFilter $filter): array
    {
        $reports = $this->repository->getPaginatedReports($teacher, $request, $filter);
        $students = $this->repository->getTeacherStudents($teacher);
        $courses = $this->repository->getTeacherCourses($teacher);

        return compact('reports', 'students', 'courses');
    }

    public function getCreateData(User $teacher, Request $request): array
    {
        $students = $this->repository->getTeacherStudents($teacher, true); // only completed
        $selectedStudent = $request->query('student_id');
        $courses = $selectedStudent ? $this->repository->getTeacherCourses($teacher, $selectedStudent) : [];

        return compact('students', 'courses', 'selectedStudent');
    }

    public function getEditData(User $teacher, Report $report): array
    {
        $students = $this->repository->getTeacherStudents($teacher);
        $courses = $this->repository->getTeacherCourses($teacher, $report->student_id);

        return compact('report', 'students', 'courses');
    }

    public function storeReport(User $teacher, array $data): Report
    {
        $data['teacher_id'] = $teacher->id;
        $report = $this->repository->createReport($data);

        $this->sendReportNotifications($report);

        return $report;
    }

    public function updateReport(Report $report, array $data): bool
    {
        return $this->repository->updateReport($report, $data);
    }

    public function deleteReport(Report $report): bool
    {
        return $this->repository->deleteReport($report);
    }

    protected function sendReportNotifications(Report $report): void
    {
        // Load relationships for email
        $report->load(['student', 'teacher', 'course']);

        // Send email notification to student
        try {
            $report->student->notify(new \App\Notifications\ReportAddedNotification($report));
        } catch (\Exception $e) {
            Log::error('Failed to send report notification to student: ' . $e->getMessage());
        }

        // Send email notification to parent(s)
        try {
            $parents = $report->student->parents;
            if ($parents) {
                foreach ($parents as $parent) {
                    $parent->notify(new \App\Notifications\ReportAddedNotification($report));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send report notification to parents: ' . $e->getMessage());
        }
    }
}
