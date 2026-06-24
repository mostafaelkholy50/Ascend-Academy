<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class TestTeacherScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get or create the teacher
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@test.com'],
            [
                'name' => 'Test Teacher',
                'password' => Hash::make('password'),
                'role' => 'Teacher',
                'active' => true,
            ]
        );
        $teacher->assignRole('Teacher');

        // 2. Ensure a course exists
        $course = Course::first();
        if (!$course) {
            $course = Course::factory()->create();
        }

        // 3. Create some dummy students
        $students = [];
        for ($i = 1; $i <= 3; $i++) {
            $student = User::firstOrCreate(
                ['email' => "student{$i}@test.com"],
                [
                    'name' => "Student {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'Student',
                    'active' => true,
                ]
            );
            $student->assignRole('Student');
            $students[] = $student;

            // Ensure enrollment
            // Ensure enrollment
            Enrollment::firstOrCreate([
                'student_id' => $student->id,
                'course_id' => $course->id,
            ], [
                'status' => 'active',
            ]);
        }

        // 4. Create Schedule Data (Current Week)
        // Clear old test schedules for this teacher to prevent infinite clutter
        Schedule::where('teacher_id', $teacher->id)->delete();

        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();

        // Create sessions for different days of this week
        $scheduleData = [
            // Yesterday / Past Days
            ['day_offset' => -1, 'hour' => 10, 'duration' => 60, 'student' => $students[0], 'status' => 'completed', 'attend' => true],
            ['day_offset' => -1, 'hour' => 14, 'duration' => 30, 'student' => $students[1], 'status' => 'scheduled', 'attend' => 'student_absent'],
            
            // Today
            ['day_offset' => 0, 'hour' => 9, 'duration' => 45, 'student' => $students[2], 'status' => 'scheduled', 'attend' => 'teacher_absent'],
            ['day_offset' => 0, 'hour' => $now->hour + 1, 'duration' => 60, 'student' => $students[0], 'status' => 'scheduled', 'attend' => null], // Upcoming
            ['day_offset' => 0, 'hour' => $now->hour + 3, 'duration' => 30, 'student' => $students[1], 'status' => 'scheduled', 'attend' => null], // Upcoming
            
            // Tomorrow
            ['day_offset' => 1, 'hour' => 11, 'duration' => 90, 'student' => $students[2], 'status' => 'scheduled', 'attend' => null],
            ['day_offset' => 1, 'hour' => 15, 'duration' => 45, 'student' => $students[0], 'status' => 'scheduled', 'attend' => null],
            
            // In 2 Days
            ['day_offset' => 2, 'hour' => 18, 'duration' => 60, 'student' => $students[1], 'status' => 'scheduled', 'attend' => null],
        ];

        foreach ($scheduleData as $data) {
            $startsAt = $now->copy()->addDays($data['day_offset'])->setHour($data['hour'])->setMinute(0)->setSecond(0);
            $endsAt = $startsAt->copy()->addMinutes($data['duration']);

            $enrollment = Enrollment::where('student_id', $data['student']->id)->where('course_id', $course->id)->first();

            $schedule = Schedule::create([
                'enrollment_id' => $enrollment->id,
                'course_id' => $course->id,
                'teacher_id' => $teacher->id,
                'student_id' => $data['student']->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'zoom_link' => 'https://zoom.us/j/123456789',
                'status' => $data['status'],
                'notes' => 'Test session',
            ]);

            // Add attendance records if specified
            if ($data['attend']) {
                $studentPresent = true;
                $teacherPresent = true;
                
                if ($data['attend'] === 'student_absent') $studentPresent = false;
                if ($data['attend'] === 'teacher_absent') $teacherPresent = false;

                Attendance::create([
                    'schedule_id' => $schedule->id,
                    'student_id' => $data['student']->id,
                    'teacher_id' => $teacher->id,
                    'student_present' => $studentPresent,
                    'teacher_present' => $teacherPresent,
                    'remark' => 'Automatically generated for testing',
                ]);
            }
        }
    }
}
