<?php

use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'SchedulerManager']);
    Role::create(['name' => 'Parent']);
    Role::create(['name' => 'Student']);
    Role::create(['name' => 'Teacher']);
});

test('scheduler manager can view dashboard successfully', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'SchedulerManager']);
    $user->assignRole('SchedulerManager');
    
    // Act
    $response = $this->actingAs($user)->get(route('scheduler.dashboard'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('scheduler.dashboard');
});

test('unauthorized user cannot view scheduler dashboard', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'Parent']);
    $user->assignRole('Parent');
    
    // Act
    $response = $this->actingAs($user)->get(route('scheduler.dashboard'));
    
    // Assert
    $response->assertStatus(403); // Forbidden by Spatie middleware
});

test('scheduler dashboard search works', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'SchedulerManager']);
    $user->assignRole('SchedulerManager');
    
    $student = User::factory()->student()->create(['name' => 'John Doe']);
    
    // Act
    $response = $this->actingAs($user)->get(route('scheduler.dashboard', ['search' => 'John']));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('searchResults');
    
    $results = $response->viewData('searchResults');
    expect($results->pluck('id'))->toContain($student->id);
});
