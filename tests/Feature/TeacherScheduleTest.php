<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
});

test('teacher can view weekly schedule', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.schedule.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.schedule-weekly');
    $response->assertViewHas('schedulesByDay');
});

test('teacher can view daily schedule', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.schedule.daily'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.schedule-daily');
    $response->assertViewHas('schedules');
});
