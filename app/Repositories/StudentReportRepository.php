<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Filters\StudentReportFilter;

class StudentReportRepository
{
    protected $filter;

    public function __construct(StudentReportFilter $filter)
    {
        $this->filter = $filter;
    }

    public function getReportsQuery(User $student, Request $request): Builder
    {
        $query = Report::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->latest('report_date');

        $this->filter->apply($query, $request);

        return $query;
    }

    public function getCoursesWithReports(User $student): Collection
    {
        return Course::whereHas('reports', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get();
    }

    public function getTeachersWithReports(User $student): Collection
    {
        return User::roleTeacher()
            ->whereHas('teacherReports', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })->get();
    }

    public function getReport(User $student, int $id): Report
    {
        return Report::with(['teacher', 'course', 'student'])
            ->where('student_id', $student->id)
            ->findOrFail($id);
    }
}
