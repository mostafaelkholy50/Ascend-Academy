<?php

namespace App\Repositories;

use App\Models\Resource;
use App\Models\User;
use App\Models\Course;
use App\Filters\TeacherResourceFilter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TeacherResourceRepository
{
    public function getPaginatedResources(User $teacher, Request $request, TeacherResourceFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        $query = Resource::with(['student', 'course'])
            ->where('teacher_id', $teacher->id)
            ->latest();

        return $filter->apply($query, $request)->paginate($perPage);
    }

    public function getTeacherStudents(User $teacher): Collection
    {
        return User::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('name')->get();
    }

    public function getTeacherCourses(User $teacher): Collection
    {
        return Course::whereHas('schedules', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->orderBy('title')->get();
    }

    public function createResource(array $data): Resource
    {
        return Resource::create($data);
    }

    public function updateResource(Resource $resource, array $data): bool
    {
        return $resource->update($data);
    }

    public function deleteResource(Resource $resource): bool
    {
        return $resource->delete();
    }
}
