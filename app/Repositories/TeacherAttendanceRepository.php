<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\User;

class TeacherAttendanceRepository
{
    public function getScheduleForTeacher(int $scheduleId, int $teacherId): ?Schedule
    {
        return Schedule::where('id', $scheduleId)
            ->where('teacher_id', $teacherId)
            ->first();
    }

    public function updateOrCreateAttendance(array $attributes, array $values): Attendance
    {
        return Attendance::updateOrCreate($attributes, $values);
    }
}
