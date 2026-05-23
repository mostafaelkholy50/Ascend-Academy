<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class StudentRepository
{
    public function getStudentsQuery(): Builder
    {
        return User::where('role', 'Student')->with(['parents', 'enrollments.course']);
    }

    public function findOrFail(int $id): User
    {
        return User::where('role', 'Student')->findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $student, array $data): bool
    {
        return $student->update($data);
    }

    public function delete(User $student): ?bool
    {
        return $student->delete();
    }
}
