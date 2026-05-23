<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;

class CourseRepository
{
    public function getCoursesQuery(): Builder
    {
        return Course::withCount(['enrollments', 'schedules']);
    }

    public function findOrFail(int $id): Course
    {
        return Course::findOrFail($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(Course $course, array $data): bool
    {
        return $course->update($data);
    }

    public function delete(Course $course): ?bool
    {
        return $course->delete();
    }
}
