<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\PricingTier;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::roleStudent()->get();
        $courses = Course::all();
        $pricingTiers = PricingTier::all();

        if ($students->isEmpty() || $courses->isEmpty() || $pricingTiers->isEmpty()) {
            return;
        }

        foreach ($students as $student) {
            // Enroll in 1-2 random courses
            $enrolledCourses = $courses->random(rand(1, 2));
            
            foreach ($enrolledCourses as $course) {
                $tier = $pricingTiers->random();
                
                Enrollment::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'start_date' => now()->subMonths(rand(1, 3)),
                    'status' => 'active',
                    'days_per_week' => $tier->days_per_week,
                    'session_duration' => $tier->session_duration,
                    'admin_price' => $tier->price_cad,
                    'currency' => 'CAD',
                    'schedule_pattern' => ['Monday' => '16:00', 'Wednesday' => '18:00'],
                ]);
            }
        }
    }
}
