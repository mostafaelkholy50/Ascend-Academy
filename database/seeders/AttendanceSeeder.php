<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Attendance;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $pastSchedules = Schedule::where('status', 'completed')->get();

        foreach ($pastSchedules as $schedule) {
            Attendance::create([
                'schedule_id' => $schedule->id,
                'student_id' => $schedule->student_id,
                'teacher_id' => $schedule->teacher_id,
                'student_present' => rand(0, 10) > 1, // 90% attendance
                'teacher_present' => true,
                'remark' => 'Normal session',
            ]);
        }
    }
}
