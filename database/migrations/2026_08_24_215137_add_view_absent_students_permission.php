<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the permission
        $permission = Permission::firstOrCreate(['name' => 'view_absent_students']);

        // Automatically assign it to SuperAdmin and Admin
        $superAdmin = Role::where('name', 'SuperAdmin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permission);
        }

        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', 'view_absent_students')->delete();
    }
};
