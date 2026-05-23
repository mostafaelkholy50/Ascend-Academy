<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'General English for Kids',
                'description' => 'A comprehensive English course designed specifically for children to build a strong foundation in listening, speaking, reading, and writing.',
                'level' => 'Beginner',
                'age_group' => 'Kids',
                'language' => 'English',
                'is_free' => false,
            ],
            [
                'title' => 'Intermediate Arabic for Teens',
                'description' => 'Enhance your Arabic language skills with our interactive course for teenagers focusing on conversation and grammar.',
                'level' => 'Intermediate',
                'age_group' => 'Teens',
                'language' => 'Arabic',
                'is_free' => false,
            ],
            [
                'title' => 'Advanced English Business Communication',
                'description' => 'Master the art of professional English communication for adults in a business environment.',
                'level' => 'Advanced',
                'age_group' => 'Adults',
                'language' => 'English',
                'is_free' => false,
            ],
            [
                'title' => 'Introduction to English',
                'description' => 'A free introductory course to start your journey with the English language.',
                'level' => 'Beginner',
                'age_group' => 'Adults',
                'language' => 'English',
                'is_free' => true,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(['title' => $courseData['title']], $courseData);
        }
    }
}
