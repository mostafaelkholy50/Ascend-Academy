<?php

use App\Models\User;
use App\Models\Schedule;
use App\Models\Course;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Student']);
});

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

test('student dashboard shows schedule in student timezone', function () {
    $student = User::factory()->student()->create([
        'role' => 'Student',
        'timezone' => 'Asia/Dubai',
    ]);
    $student->assignRole('Student');
    $teacher = User::factory()->teacher()->create(['role' => 'Teacher']);
    $course = Course::create(['title' => 'Timezone Course']);

    Schedule::create([
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'starts_at' => Carbon::today('Africa/Cairo')->setTime(10, 0),
        'ends_at' => Carbon::today('Africa/Cairo')->setTime(11, 0),
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('12:00 PM');
    $response->assertDontSee('10:00 AM');
});
