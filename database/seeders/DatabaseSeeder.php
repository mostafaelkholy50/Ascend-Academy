<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Role & Permission System
            RolePermissionSeeder::class,
            
            // Core System Data
            AdminSeeder::class,
            PricingTierSeeder::class,
            CourseSeeder::class,
            CourseFilterSeeder::class,
            NewsSeeder::class,

            // Users
            TeacherSeeder::class,
            ParentSeeder::class,
            StudentSeeder::class,

            // Academic Data (Lifecycle)
            EnrollmentSeeder::class,
            ScheduleSeeder::class,
            AttendanceSeeder::class,
            ReportSeeder::class,
            BookSeeder::class,
        ]);
    }
}
