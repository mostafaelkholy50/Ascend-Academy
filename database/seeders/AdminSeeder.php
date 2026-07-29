<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create SuperAdmin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@ascend.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::SuperAdmin->value,
                'active' => true,
            ]
        );
        $superAdmin->assignRole(UserRole::SuperAdmin->value);
        
        // Give SuperAdmin user all permissions explicitly
        $permissions = \Spatie\Permission\Models\Permission::all();
        $superAdmin->syncPermissions($permissions);
        // 2. Create Regular Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@ascend.com'],
            [
                'name' => 'Regular Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin->value,
                'active' => true,
            ]
        );
        $admin->assignRole(UserRole::Admin->value);
        // 3. Create SchedulerManager user
        $schedulerManager = User::firstOrCreate(
            ['email' => 'scheduler@ascend.com'],
            [
                'name' => 'Scheduler Manager',
                'password' => Hash::make('password'),
                'role' => UserRole::SchedulerManager->value,
                'active' => true,
            ]
        );
        $schedulerManager->assignRole(UserRole::SchedulerManager->value);
    }
}
