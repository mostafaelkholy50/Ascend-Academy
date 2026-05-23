<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use App\Models\Report;
use App\Models\Schedule;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $enrollments = Enrollment::all();

        foreach ($enrollments as $enrollment) {
            // Find a teacher from their schedules
            $schedule = Schedule::where('enrollment_id', $enrollment->id)->first();
            $teacherId = $schedule ? $schedule->teacher_id : null;

            if (!$teacherId) continue;

            Report::create([
                'teacher_id' => $teacherId,
                'student_id' => $enrollment->student_id,
                'course_id' => $enrollment->course_id,
                'level' => $enrollment->course->level ?? 'Beginner',
                'mastery_score' => rand(70, 95),
                'strengths' => 'Great participation and vocabulary.',
                'weaknesses' => 'Needs to work on grammar consistency.',
                'behavior' => 'Excellent',
                'notes' => 'Progressing well through the curriculum.',
                'report_date' => now()->subDays(rand(1, 15)),
            ]);
        }
    }
}
