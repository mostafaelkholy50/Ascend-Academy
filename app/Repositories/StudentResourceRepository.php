<?php

namespace App\Repositories;

use App\Models\Resource;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Filters\StudentResourceFilter;

class StudentResourceRepository
{
    protected $filter;

    public function __construct(StudentResourceFilter $filter)
    {
        $this->filter = $filter;
    }

    public function getResourcesQuery(User $student, Request $request): Builder
    {
        $query = Resource::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->latest();

        $this->filter->apply($query, $request);

        return $query;
    }

    public function getCoursesForStudent(User $student): Collection
    {
        return Course::whereHas('enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->orderBy('title')->get();
    }

    public function getResource(User $student, int $id): Resource
    {
        return Resource::with(['teacher', 'course'])
            ->where('student_id', $student->id)
            ->findOrFail($id);
    }
}
