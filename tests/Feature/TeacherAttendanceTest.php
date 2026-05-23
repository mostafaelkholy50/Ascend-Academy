<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
});

test('teacher can store attendance', function () {
    // Arrange
    $teacher = User::factory()->teacher()->create(['role' => 'Teacher']);
    $teacher->assignRole('Teacher');

    $student = User::factory()->student()->create();

    $course = Course::create([
        'title' => 'Test Course',
        'description' => 'Test Description',
        'price' => 100,
        'duration_weeks' => 4,
        'sessions_per_week' => 2,
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    $schedule = Schedule::create([
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'starts_at' => now(),
        'ends_at' => now()->addHour(),
        'status' => 'scheduled',
    ]);

    // Act
    $response = $this->actingAs($teacher)->postJson(route('teacher.attendance.store'), [
        'schedule_id' => $schedule->id,
        'student_id' => $student->id,
        'teacher_present' => true,
        'student_present' => true,
    ]);

    // Assert
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    $this->assertDatabaseHas('attendances', [
        'schedule_id' => $schedule->id,
        'student_id' => $student->id,
        'teacher_present' => 1,
    ]);
    $this->assertDatabaseHas('schedules', [
        'id' => $schedule->id,
        'status' => 'completed',
    ]);
});
