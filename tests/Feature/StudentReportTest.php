<?php

use App\Models\User;
use App\Models\Report;
use App\Models\Course;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
    Role::create(['name' => 'Student']);
});

test('student can view reports index', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->get(route('student.reports.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('student.reports.index');
});

test('student can view specific report', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    $teacher = User::factory()->teacher()->create();
    $teacher->assignRole('Teacher');
    
    // Create course manually since factory doesn't exist
    $course = Course::create(['title' => 'Math']);
    
    $report = Report::create([
        'student_id' => $user->id,
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'report_date' => now()->format('Y-m-d'),
        'content' => 'Excellent progress',
    ]);
    
    // Act
    $response = $this->actingAs($user)->get(route('student.reports.show', $report->id));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('student.reports.show');
    $response->assertViewHas('report');
});

test('student cannot view report of another student', function () {
    // Arrange
    $user = User::factory()->student()->create();
    $user->assignRole('Student');
    
    $anotherStudent = User::factory()->student()->create();
    $anotherStudent->assignRole('Student');
    
    $teacher = User::factory()->teacher()->create();
    $teacher->assignRole('Teacher');
    
    $course = Course::create(['title' => 'Math']);
    
    $report = Report::create([
        'student_id' => $anotherStudent->id,
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'report_date' => now()->format('Y-m-d'),
        'content' => 'Excellent progress',
    ]);
    
    // Act
    $response = $this->actingAs($user)->get(route('student.reports.show', $report->id));
    
    // Assert
    $response->assertStatus(302); // Redirects due to errorResponse in catch block
});
