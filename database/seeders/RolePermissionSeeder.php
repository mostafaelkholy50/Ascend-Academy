<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage permissions',
            'manage schedules',
            'manage accounting',
            'manage quality',
            'view dashboard',
            'manage availability',
            'view evaluations',
            'add evaluations',
            'edit evaluations',
            'delete evaluations',
            'edit-teacher-rate',
            'edit profile',
            'manage books',
            'view books',
            'manage news',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        
        // SuperAdmin - gets everything via Gate::before in AppServiceProvider
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $superAdminRole->syncPermissions($permissions);

        // Admin
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo([
            'manage users',
            'manage schedules',
            'view dashboard',
            'edit profile',
            'manage quality',
            'view evaluations',
            'add evaluations',
            'edit evaluations',
        ]);

        // Teacher
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $teacherRole->givePermissionTo([
            'view dashboard',
            'edit profile',
        ]);

        // Parent
        $parentRole = Role::firstOrCreate(['name' => 'Parent']);
        $parentRole->givePermissionTo([
            'view dashboard',
            'edit profile',
        ]);

        // Student
        $studentRole = Role::firstOrCreate(['name' => 'Student']);
        $studentRole->givePermissionTo([
            'view dashboard',
            'edit profile',
        ]);

        // New future roles
        Role::firstOrCreate(['name' => 'SchedulerManager'])->givePermissionTo([
            'manage schedules',
            'view dashboard',
            'edit profile',
            'manage availability'
        ]);
        Role::firstOrCreate(['name' => 'Accountant'])->givePermissionTo(['manage accounting', 'view dashboard']);
        Role::firstOrCreate(['name' => 'QualityControl'])->syncPermissions([
            'manage quality',
            'view dashboard',
            'view evaluations',
            'add evaluations',
            'edit evaluations',
        ]);
    }
}
