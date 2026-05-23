<?php

use App\Models\User;
use App\Models\Report;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
});

test('teacher can view reports index', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.reports.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.reports.index');
});

test('teacher can store report', function () {
    // Arrange
    $teacher = User::factory()->teacher()->create(['role' => 'Teacher']);
    $teacher->assignRole('Teacher');
    
    $student = User::factory()->student()->create();
    
    // Act
    $response = $this->actingAs($teacher)->post(route('teacher.reports.store'), [
        'student_id' => $student->id,
        'report_date' => now()->format('Y-m-d'),
        'level' => 'Beginner',
        'mastery_score' => 90,
    ]);
    
    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('reports', [
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'level' => 'Beginner',
        'mastery_score' => 90,
    ]);
});
