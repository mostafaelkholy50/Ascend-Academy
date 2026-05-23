<?php

namespace App\Services;

use App\Repositories\StudentResourceRepository;
use App\Models\User;
use Illuminate\Http\Request;

class StudentResourceService
{
    protected $repository;

    public function __construct(StudentResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getIndexData(User $student, Request $request): array
    {
        $resources = $this->repository->getResourcesQuery($student, $request)->paginate(15);
        $courses = $this->repository->getCoursesForStudent($student);

        return compact('resources', 'courses');
    }

    public function getResource(User $student, int $id)
    {
        return $this->repository->getResource($student, $id);
    }
}
