<?php

use App\Models\User;
use App\Models\Schedule;
use App\Models\Course;
use App\Models\Enrollment;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Create roles if they don't exist
    if (!Role::where('name', 'Teacher')->exists()) {
        Role::create(['name' => 'Teacher']);
    }
    if (!Role::where('name', 'Student')->exists()) {
        Role::create(['name' => 'Student']);
    }
});

test('teacher can view only their students in current month', function () {
    // Arrange
    $teacher = User::factory()->teacher()->create(['role' => 'Teacher']);
    $teacher->assignRole('Teacher');

    $student1 = User::factory()->student()->create(['role' => 'Student', 'name' => 'Student One']);
    $student1->assignRole('Student');

    $student2 = User::factory()->student()->create(['role' => 'Student', 'name' => 'Student Two']);
    $student2->assignRole('Student');

    $student3 = User::factory()->student()->create(['role' => 'Student', 'name' => 'Student Three']);
    $student3->assignRole('Student');

    $course = Course::create(['title' => 'Test Course']);

    $enrollment1 = Enrollment::create([
        'student_id' => $student1->id,
        'course_id' => $course->id,
        'status' => 'active'
    ]);

    $enrollment3 = Enrollment::create([
        'student_id' => $student3->id,
        'course_id' => $course->id,
        'status' => 'active'
    ]);

    // Create schedule for student 1 with this teacher in CURRENT MONTH
    Schedule::create([
        'teacher_id' => $teacher->id,
        'student_id' => $student1->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment1->id,
        'starts_at' => now(),
        'ends_at' => now()->addHour(),
        'status' => 'scheduled'
    ]);

    // Create schedule for student 3 with this teacher in PREVIOUS MONTH
    Schedule::create([
        'teacher_id' => $teacher->id,
        'student_id' => $student3->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment3->id,
        'starts_at' => now()->subMonth(),
        'ends_at' => now()->subMonth()->addHour(),
        'status' => 'scheduled'
    ]);

    // Act
    $response = $this->actingAs($teacher)->get(route('teacher.my-students'));

    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.my-students');
    $response->assertSee('Student One');
    $response->assertDontSee('Student Two');
    $response->assertDontSee('Student Three');
});

test('teacher sees empty state when no students in current month', function () {
    // Arrange
    $teacher = User::factory()->teacher()->create(['role' => 'Teacher']);
    $teacher->assignRole('Teacher');

    // Act
    $response = $this->actingAs($teacher)->get(route('teacher.my-students'));

    // Assert
    $response->assertStatus(200);
    $response->assertSee('No Students Yet');
});

test('teacher does not see students with only cancelled schedules in current month', function () {
    // Arrange
    $teacher = User::factory()->teacher()->create(['role' => 'Teacher']);
    $teacher->assignRole('Teacher');

    $student = User::factory()->student()->create(['role' => 'Student', 'name' => 'Cancelled Student']);
    $student->assignRole('Student');

    $course = Course::create(['title' => 'Test Course']);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active'
    ]);

    // Create cancelled schedule for student in CURRENT MONTH
    Schedule::create([
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'starts_at' => now(),
        'ends_at' => now()->addHour(),
        'status' => 'cancelled'
    ]);

    // Act
    $response = $this->actingAs($teacher)->get(route('teacher.my-students'));

    // Assert
    $response->assertStatus(200);
    $response->assertDontSee('Cancelled Student');
    $response->assertSee('No Students Yet');
});
