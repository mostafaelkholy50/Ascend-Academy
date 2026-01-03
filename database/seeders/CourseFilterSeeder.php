<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseFilterSeeder extends Seeder
{
    public function run(): void
    {
        // Update all courses that don't have filter values
        $levels = ['Beginner', 'Intermediate', 'Advanced'];
        $ageGroups = ['Kids', 'Teens', 'Adults'];
        $languages = ['English', 'Arabic'];
        
        $courses = Course::whereNull('level')->orWhereNull('age_group')->orWhereNull('language')->get();
        
        foreach ($courses as $course) {
            $course->update([
                'level' => $course->level ?? $levels[array_rand($levels)],
                'age_group' => $course->age_group ?? $ageGroups[array_rand($ageGroups)],
                'language' => $course->language ?? $languages[array_rand($languages)],
                'is_free' => $course->is_free ?? (rand(0, 3) == 0), // 25% chance of being free
            ]);
        }
        
        $this->command->info('Updated ' . $courses->count() . ' courses with filter values!');
    }
}
