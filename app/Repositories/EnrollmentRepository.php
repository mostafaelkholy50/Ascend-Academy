<?php

namespace App\Repositories;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentRepository
{
    public function getEnrollmentsQuery(): Builder
    {
        return Enrollment::with(['student', 'course']);
    }

    public function findOrFail(int $id): Enrollment
    {
        return Enrollment::findOrFail($id);
    }

    public function create(array $data): Enrollment
    {
        return Enrollment::create($data);
    }

    public function update(Enrollment $enrollment, array $data): bool
    {
        return $enrollment->update($data);
    }

    public function delete(Enrollment $enrollment): ?bool
    {
        return $enrollment->delete();
    }
}
