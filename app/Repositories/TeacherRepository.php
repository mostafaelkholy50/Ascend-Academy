<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeacherRepository
{
    /**
     * Get query for teachers.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getTeachersQuery()
    {
        return User::where('role', 'Teacher')
            ->withCount(['teacherSchedules', 'teacherReports']);
    }

    /**
     * Get teacher with relations.
     *
     * @param User $teacher
     * @return User
     */
    public function getTeacherWithRelations(User $teacher)
    {
        return $teacher->load([
            'teacherSchedules.student',
            'teacherReports.student',
            'teacherHours',
            'teacherResources'
        ]);
    }

    /**
     * Create a new teacher.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        return User::create($data);
    }

    /**
     * Update a teacher.
     *
     * @param User $teacher
     * @param array $data
     * @return bool
     */
    public function update(User $teacher, array $data)
    {
        return $teacher->update($data);
    }

    /**
     * Delete a teacher.
     *
     * @param User $teacher
     * @return bool|null
     */
    public function delete(User $teacher)
    {
        return $teacher->delete();
    }
}
