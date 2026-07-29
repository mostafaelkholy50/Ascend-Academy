<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $parents = User::roleParent()->get();
        
        if ($parents->isEmpty()) {
            $parents = User::factory(5)->parent()->create();
        }

        $students = User::factory(20)->student()->create();
        
        foreach ($students as $student) {
            $student->assignRole(UserRole::Student->value);
            $parent = $parents->random();
            DB::table('children')->insert([
                'parent_id' => $parent->id,
                'child_id' => $student->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
