<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = collect();
        $teachers = $teachers->merge(User::factory(3)->teacher()->state(['gender' => 'Male'])->create());
        $teachers = $teachers->merge(User::factory(3)->teacher()->state(['gender' => 'Female'])->create());

        foreach ($teachers as $teacher) {
            $teacher->assignRole(UserRole::Teacher->value);
        }
    }
}
