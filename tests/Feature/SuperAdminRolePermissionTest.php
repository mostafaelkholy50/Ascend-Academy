<?php

use App\Models\User;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Route;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'SuperAdmin']);
    Role::create(['name' => 'Teacher']);
    
    // Add missing route to avoid view error in header
    Route::get('admin/superadmin/profile', function() { return 'profile'; })->name('superadmin.profile.show');
});

test('super admin can view index', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'SuperAdmin']);
    $user->assignRole('SuperAdmin');
    
    // Act
    $response = $this->actingAs($user)->get(route('superadmin.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('superadmin.index');
});

test('super admin can store role', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'SuperAdmin']);
    $user->assignRole('SuperAdmin');
    
    // Act
    $response = $this->actingAs($user)->post(route('superadmin.roles.store'), [
        'name' => 'NewRole',
    ]);
    
    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('roles', [
        'name' => 'NewRole',
    ]);
});

test('super admin can store user', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'SuperAdmin']);
    $user->assignRole('SuperAdmin');
    
    // Act
    $response = $this->actingAs($user)->post(route('superadmin.users.store'), [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => 'password123',
        'roles' => ['Teacher'],
    ]);
    
    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('users', [
        'email' => 'testuser@example.com',
    ]);
});
