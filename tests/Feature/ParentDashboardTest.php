<?php

use App\Models\User;
use App\Models\Schedule;
use App\Models\Enrollment;
use App\Models\Course;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Parent']);
});

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

test('parent dashboard shows schedule in parent timezone', function () {
    $parent = User::factory()->parent()->create([
        'role' => 'Parent',
        'timezone' => 'America/New_York',
    ]);
    $parent->assignRole('Parent');
    $student = User::factory()->student()->create(['role' => 'Student']);
    $parent->children()->attach($student->id);
    $teacher = User::factory()->teacher()->create(['role' => 'Teacher']);
    $course = Course::create(['title' => 'Timezone Course']);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
        'enrollment_date' => now(),
    ]);

    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'starts_at' => Carbon::today('Africa/Cairo')->setTime(16, 0),
        'ends_at' => Carbon::today('Africa/Cairo')->setTime(17, 0),
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($parent)->get(route('parent.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('9:00 AM');
    $response->assertDontSee('4:00 PM');
});
