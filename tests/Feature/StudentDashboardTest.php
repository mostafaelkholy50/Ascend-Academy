<?php

use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('student can view dashboard successfully', function () {
    // Arrange
    $user = User::factory()->student()->create();
    
    // Act
    $response = $this->actingAs($user)->get(route('student.dashboard'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('student.dashboard');
});

test('non-student cannot view student dashboard', function () {
    // Arrange
    $user = User::factory()->teacher()->create(); // Teacher trying to access student dashboard
    
    // Act
    $response = $this->actingAs($user)->get(route('student.dashboard'));
    
    // Assert
    $response->assertStatus(403);
});
