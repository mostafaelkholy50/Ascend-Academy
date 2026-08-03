<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Attendance;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
});

test('teacher can view hours and earnings', function () {
    // Arrange
    $teacher = User::factory()->teacher()->create([
        'role' => 'Teacher',
        'hourly_rate' => 50,
    ]);
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
        'starts_at' => now()->startOfMonth()->addDays(5),
        'ends_at' => now()->startOfMonth()->addDays(5)->addHour(),
        'status' => 'completed',
    ]);
    
    Attendance::create([
        'schedule_id' => $schedule->id,
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'teacher_present' => true,
        'student_present' => true,
    ]);
    
    // Act
    $response = $this->actingAs($teacher)->get(route('teacher.hours.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.hours.index');
    $response->assertViewHas('totalHours');
    $response->assertViewHas('totalEarnings');
    $response->assertSeeText('My Hours');
    $response->assertSeeText('Earnings');
    $response->assertSee('grid grid-cols-3 gap-2 sm:gap-4 md:gap-6');
    $response->assertSeeText('Total Hours Worked');
    $response->assertSeeText('Hourly Rate');
    $response->assertSeeText('Total Earnings');
    $response->assertSeeText('Attended Sessions');
    $response->assertSee('md:hidden divide-y divide-gray-200');
    $response->assertSee('text-xs sm:text-sm px-2 py-2 sm:px-3 sm:py-2');
});
