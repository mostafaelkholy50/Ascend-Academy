<?php

use App\Models\User;
use App\Models\Schedule;
use App\Models\Enrollment;
use App\Models\Course;
use Carbon\Carbon;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('parent can view dashboard successfully', function () {
    // Arrange
    $parent = User::factory()->parent()->create();
    $student = User::factory()->student()->create();
    
    // Attach student to parent
    $parent->children()->attach($student->id);
    
    // Create course
    $course = Course::create(['title' => 'Test Course']);
    
    // Create enrollment
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
        'enrollment_date' => now(),
    ]);
    
    // Act
    $response = $this->actingAs($parent)->get(route('parent.dashboard'));
    
    // Assert
    $response->assertStatus(200);
});

test('non-parent cannot view dashboard due to request validation/authorization', function () {
    // Arrange
    $user = User::factory()->teacher()->create();
    
    // Act
    $response = $this->actingAs($user)->get(route('parent.dashboard'));
    
    // Assert
    $response->assertStatus(403);
});

test('parent can search on dashboard', function () {
    // Arrange
    $parent = User::factory()->parent()->create();
    $student = User::factory()->student()->create();
    $parent->children()->attach($student->id);
    
    // Act
    $response = $this->actingAs($parent)->get(route('parent.dashboard', ['search' => 'test']));
    
    // Assert
    $response->assertStatus(200);
});
