<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Builder;

class ScheduleRepository
{
    /**
     * Get query for schedules.
     *
     * @return Builder
     */
    public function getSchedulesQuery()
    {
        return Schedule::with(['student', 'teacher', 'course', 'enrollment']);
    }

    /**
     * Get schedule with relations.
     *
     * @param Schedule $schedule
     * @return Schedule
     */
    public function getScheduleWithRelations(Schedule $schedule)
    {
        return $schedule->load(['student', 'teacher', 'course', 'enrollment', 'attendance']);
    }

    /**
     * Create a new schedule.
     *
     * @param array $data
     * @return Schedule
     */
    public function create(array $data)
    {
        return Schedule::create($data);
    }

    /**
     * Update a schedule.
     *
     * @param Schedule $schedule
     * @param array $data
     * @return bool
     */
    public function update(Schedule $schedule, array $data)
    {
        return $schedule->update($data);
    }

    /**
     * Delete a schedule.
     *
     * @param Schedule $schedule
     * @return bool|null
     */
    public function delete(Schedule $schedule)
    {
        return $schedule->delete();
    }

    /**
     * Bulk cancel upcoming schedules for an enrollment.
     *
     * @param Enrollment $enrollment
     * @return int
     */
    public function bulkCancel(Enrollment $enrollment)
    {
        return Schedule::where('enrollment_id', $enrollment->id)
            ->where('status', 'scheduled')
            ->where('starts_at', '>', now())
            ->update(['status' => 'cancelled']);
    }

    /**
     * Bulk delete schedules for an enrollment.
     *
     * @param Enrollment $enrollment
     * @return int
     */
    public function bulkDelete(Enrollment $enrollment)
    {
        return Schedule::where('enrollment_id', $enrollment->id)->delete();
    }
}
