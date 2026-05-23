<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
});

test('teacher can view dashboard', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.dashboard'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.dashboard');
    $response->assertViewHas('teacher');
    $response->assertViewHas('todaySchedules');
    $response->assertViewHas('weekSchedules');
    $response->assertViewHas('myStudents');
    $response->assertViewHas('stats');
});

test('non-teacher cannot view dashboard', function () {
    // Arrange
    $user = User::factory()->student()->create(['role' => 'Student']);
    Role::create(['name' => 'Student']);
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.dashboard'));
    
    // Assert
    $response->assertStatus(403);
});
