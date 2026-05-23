<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $enrollments = Enrollment::all();
        $teachers = User::roleTeacher()->get();

        if ($enrollments->isEmpty() || $teachers->isEmpty()) {
            return;
        }

        foreach ($enrollments as $enrollment) {
            $teacher = $teachers->random();
            $startDate = Carbon::parse($enrollment->start_date);
            
            // Create 8 past schedules and 4 upcoming
            for ($i = -8; $i <= 4; $i++) {
                $startsAt = $startDate->copy()->addWeeks($i)->setTime(16, 0);
                $endsAt = $startsAt->copy()->addMinutes((int)$enrollment->session_duration);
                
                $status = $i < 0 ? 'completed' : 'scheduled';
                
                Schedule::create([
                    'enrollment_id' => $enrollment->id,
                    'course_id' => $enrollment->course_id,
                    'teacher_id' => $teacher->id,
                    'student_id' => $enrollment->student_id,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'zoom_link' => 'https://zoom.us/j/123456789',
                    'status' => $status,
                    'notes' => 'Fake session note',
                ]);
            }
        }
    }
}
