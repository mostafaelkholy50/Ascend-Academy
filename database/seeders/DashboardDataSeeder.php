<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use App\Models\Attendance;
use App\Models\StudentEvaluation;
use App\Models\Schedule;
use Carbon\Carbon;

class DashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure we have Students and Teachers
        if (User::count() < 10) {
            $this->call([
                RolePermissionSeeder::class,
                TeacherSeeder::class,
                StudentSeeder::class,
            ]);
        }

        // 1.1 Seed Inquiries
        if (\App\Models\Inquiry::count() < 30) {
            for ($i = 0; $i < 50; $i++) {
                \App\Models\Inquiry::create([
                    'full_name' => 'Demo User ' . $i,
                    'email' => "demo{$i}@example.com",
                    'phone' => '123456789',
                    'status' => rand(0, 1) ? 'pending' : 'contacted',
                    'created_at' => Carbon::now()->subMonths(rand(0, 5)),
                ]);
            }
        }

        $students = User::role('Student')->get();
        $teachers = User::role('Teacher')->get();
        
        // 2. Ensure we have Courses
        if (Course::count() == 0) {
            $this->call(CourseSeeder::class);
        }
        $courses = Course::all();

        // 3. Create Historical Enrollments (Last 6 Months)
        for ($i = 0; $i < 20; $i++) {
            $date = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(1, 28));
            Enrollment::updateOrCreate(
                [
                    'student_id' => $students->random()->id,
                    'course_id' => $courses->random()->id,
                ],
                [
                    'start_date' => $date,
                    'status' => 'active',
                    'days_per_week' => rand(1, 3),
                    'session_duration' => '60',
                    'admin_price' => rand(100, 300),
                    'currency' => 'CAD',
                    'created_at' => $date,
                ]
            );
        }

        // 4. Create Historical Payments
        $enrollments = Enrollment::all();
        foreach ($enrollments as $enrollment) {
            // Last 3 months of payments
            for ($m = 0; $m < 3; $m++) {
                $payDate = $enrollment->created_at->copy()->addMonths($m)->addDays(rand(1, 5));
                if ($payDate->isPast()) {
                    EnrollmentPayment::updateOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'month' => $payDate->copy()->startOfMonth(),
                        ],
                        [
                            'amount' => $enrollment->admin_price,
                            'currency' => $enrollment->currency,
                            'payment_status' => 'paid',
                            'paid_at' => $payDate,
                        ]
                    );
                }
            }
        }

        // 5. Create Attendance Records
        foreach ($enrollments->take(10) as $enrollment) {
            for ($d = 0; $d < 10; $d++) {
                $attendanceDate = Carbon::now()->subDays($d * 3);
                Attendance::updateOrCreate(
                    [
                        'schedule_id' => 1, // Placeholder
                        'student_id' => $enrollment->student_id,
                    ],
                    [
                        'teacher_id' => $teachers->random()->id,
                        'student_present' => rand(0, 10) > 1,
                        'teacher_present' => true,
                        'created_at' => $attendanceDate,
                    ]
                );
            }
        }

        // 6. Create Student Evaluations
        foreach ($enrollments->take(10) as $enrollment) {
            $score = rand(70, 100);
            StudentEvaluation::updateOrCreate(
                [
                    'student_id' => $enrollment->student_id,
                    'teacher_id' => $teachers->random()->id,
                    'evaluation_month' => Carbon::now()->subMonth()->month,
                    'evaluation_year' => Carbon::now()->subMonth()->year,
                ],
                [
                    'course_id' => $enrollment->course_id,
                    'evaluation_date' => Carbon::now()->subMonth(),
                    'q1_score' => rand(7, 10),
                    'q2_score' => rand(7, 10),
                    'total_score' => $score,
                    'notes' => 'Great progress this month.',
                ]
            );
        }

        $this->command->info('Dashboard demo data seeded successfully!');
    }
}
