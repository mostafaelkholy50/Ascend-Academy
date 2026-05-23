<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\TeacherHour;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeacherHourRepository
{
    /**
     * Get paginated teachers with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTeachers(\Illuminate\Http\Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = User::role('Teacher');

        $query = (new \App\Filters\TeacherHourFilter)->apply($query, $request);

        return $query->orderBy('name')->paginate($perPage);
    }

    /**
     * Get payroll records for specific teachers and period.
     *
     * @param array|Collection $teacherIds
     * @param int $month
     * @param int $year
     * @return Collection
     */
    public function getPayrollRecords($teacherIds, int $month, int $year): Collection
    {
        return TeacherHour::whereIn('teacher_id', $teacherIds)
            ->forYearMonth($year, $month)
            ->get()
            ->keyBy('teacher_id');
    }

    /**
     * Update or create payroll record.
     *
     * @param array $attributes
     * @param array $values
     * @return TeacherHour
     */
    public function updateOrCreate(array $attributes, array $values): TeacherHour
    {
        return TeacherHour::updateOrCreate($attributes, $values);
    }
}
