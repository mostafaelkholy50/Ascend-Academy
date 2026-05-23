<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use App\Filters\CourseFilter;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseService
{
    protected $repository;
    protected $filter;

    public function __construct(CourseRepository $repository, CourseFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function getIndexData(Request $request, int $perPage = 15)
    {
        $query = $this->repository->getCoursesQuery();
        $query = $this->filter->apply($query, $request);

        return $query->latest()->paginate($perPage);
    }

    public function storeCourse(array $data, $photo = null)
    {
        // Set default values for removed fields (as in original code)
        $data['level'] = 'Beginner';
        $data['age_group'] = 'Adults';
        $data['language'] = 'English';
        $data['duration_weeks'] = 12;
        
        if (!isset($data['is_free'])) {
            $data['is_free'] = 0;
        }

        if ($photo) {
            $data['photo'] = $photo->store('courses', 'public');
        }

        return $this->repository->create($data);
    }

    public function updateCourse(Course $course, array $data, $photo = null)
    {
        if ($photo) {
            $oldPhoto = $course->photo;
            $data['photo'] = $photo->store('courses', 'public');
            
            $updated = $this->repository->update($course, $data);
            
            if ($updated && $oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
            return $updated;
        }

        return $this->repository->update($course, $data);
    }

    public function deleteCourse(Course $course)
    {
        // Delete photo if exists
        if ($course->photo) {
            Storage::disk('public')->delete($course->photo);
        }
        
        return $this->repository->delete($course);
    }
}
