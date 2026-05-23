<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parents = User::factory(10)->parent()->create();
        
        foreach ($parents as $parent) {
            $parent->assignRole('Parent');
        }
    }
}
