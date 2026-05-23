<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'manage news',
            'guard_name' => 'web'
        ]);

        $superAdmin = \Spatie\Permission\Models\Role::where('name', 'SuperAdmin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        $permission = \Spatie\Permission\Models\Permission::where('name', 'manage news')->first();
        if ($permission) {
            $permission->delete();
        }
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
