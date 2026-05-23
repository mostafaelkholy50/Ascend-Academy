<?php

use App\Models\User;
use App\Models\Schedule;
use App\Models\Course;
use Carbon\Carbon;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Role::create(['name' => 'Student']);
});

test('student can view weekly schedule', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->get(route('student.schedule.weekly'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('student.schedule-weekly');
    $response->assertViewHas('schedulesByDay');
});

test('student can view daily schedule', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->get(route('student.schedule.daily'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('student.schedule-daily');
    $response->assertViewHas('schedules');
});
