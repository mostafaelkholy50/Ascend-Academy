<?php

namespace App\Services;

use App\Repositories\StudentRepository;
use App\Filters\StudentFilter;
use App\Models\User;
use App\Models\Children;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentService
{
    protected $repository;
    protected $filter;

    public function __construct(StudentRepository $repository, StudentFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function getIndexData(Request $request, int $perPage = 15)
    {
        $query = $this->repository->getStudentsQuery();
        $query = $this->filter->apply($query, $request);

        return $query->latest()->paginate($perPage);
    }

    public function storeStudent(array $data)
    {
        return DB::transaction(function () use ($data) {
            $student = $this->repository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'country' => $data['country'] ?? null,
                'role' => 'Student',
                'active' => true,
            ]);

            if (!empty($data['parent_id'])) {
                Children::create([
                    'parent_id' => $data['parent_id'],
                    'child_id' => $student->id,
                ]);
            }

            return $student;
        });
    }

    public function updateStudent(User $student, array $data)
    {
        return $this->repository->update($student, $data);
    }

    public function updatePassword(User $student, string $password)
    {
        return $student->update([
            'password' => Hash::make($password),
        ]);
    }

    public function deleteStudent(User $student)
    {
        return DB::transaction(function () use ($student) {
            // Delete payments for all enrollments of this student
            foreach ($student->enrollments as $enrollment) {
                $enrollment->payments()->delete();
                $enrollment->delete();
            }
            
            // Delete other related records
            $student->schedules()->delete();
            $student->attendances()->delete();
            $student->reports()->delete();
            $student->resources()->delete();
            
            // Delete the student
            return $this->repository->delete($student);
        });
    }
}
