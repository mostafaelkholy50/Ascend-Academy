<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\User;
use App\Models\Course;
use App\Models\Schedule;
use App\Filters\TeacherReportFilter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TeacherReportRepository
{
    public function getPaginatedReports(User $teacher, Request $request, TeacherReportFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        $query = Report::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->latest('report_date');

        return $filter->apply($query, $request)->paginate($perPage);
    }

    public function getTeacherStudents(User $teacher, bool $onlyCompleted = false): Collection
    {
        return User::whereHas('schedules', function($q) use ($teacher, $onlyCompleted) {
            $q->where('teacher_id', $teacher->id);
            if ($onlyCompleted) {
                $q->where('status', 'completed');
            }
        })->orderBy('name')->get();
    }

    public function getTeacherCourses(User $teacher, ?int $studentId = null): Collection
    {
        return Course::whereHas('schedules', function($q) use ($teacher, $studentId) {
            $q->where('teacher_id', $teacher->id);
            if ($studentId) {
                $q->where('student_id', $studentId);
            }
        })->orderBy('title')->get();
    }

    public function createReport(array $data): Report
    {
        return Report::create($data);
    }

    public function updateReport(Report $report, array $data): bool
    {
        return $report->update($data);
    }

    public function deleteReport(Report $report): bool
    {
        return $report->delete();
    }
}
