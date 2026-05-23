<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\RolePermissionController;

Route::middleware(['auth', 'role:SuperAdmin'])->prefix('admin/superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [RolePermissionController::class, 'index'])->name('index');
    Route::get('/roles', [RolePermissionController::class, 'manageRoles'])->name('roles.index');
    Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.update-permissions');
    Route::post('/assign-role/{user}', [RolePermissionController::class, 'assignRole'])->name('assign-role');
    Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
    Route::post('/users', [RolePermissionController::class, 'storeUser'])->name('users.store');
    Route::delete('/users/{user}', [RolePermissionController::class, 'destroyUser'])->name('users.destroy');
});
