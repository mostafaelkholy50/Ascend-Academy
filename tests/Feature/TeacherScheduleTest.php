<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
});

test('teacher can view weekly schedule', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.schedule.index'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.schedule-weekly');
    $response->assertViewHas('schedulesByDay');
    $response->assertSee('Print Month');
    $response->assertSee('printMonthModal');
    $response->assertSee('Choose a month to open the printable schedule.');
});

test('teacher can view daily schedule', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.schedule.daily'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.schedule-daily');
    $response->assertViewHas('schedules');
});

test('teacher can view printable monthly schedule', function () {
    $user = User::factory()->teacher()->create([
        'role' => 'Teacher',
        'timezone' => 'Africa/Cairo',
    ]);
    $user->assignRole('Teacher');

    $response = $this->actingAs($user)->get(route('teacher.schedule.print', [
        'month' => '2026-07',
    ]));

    $response->assertStatus(200);
    $response->assertViewIs('admin.schedules.print');
    $response->assertSee($user->name);
    $response->assertSee('Monthly Schedule Report');
});

test('printable schedule spans multi-hour sessions across the timetable', function () {
    Carbon::setTestNow(Carbon::create(2026, 8, 3, 12, 0, 0, 'Africa/Cairo'));

    $teacher = User::factory()->teacher()->create([
        'role' => 'Teacher',
        'timezone' => 'Africa/Cairo',
    ]);
    $teacher->assignRole('Teacher');
    $student = User::factory()->student()->create();
    $course = Course::factory()->create();
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
        'session_duration' => 120,
    ]);

    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'starts_at' => Carbon::create(2026, 8, 5, 10, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 8, 5, 12, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($teacher)->get(route('teacher.schedule.print', [
        'month' => '2026-08',
    ]));

    $response->assertStatus(200);
    $response->assertSee('colspan="2"', false);
    $response->assertSee($student->name);

    Carbon::setTestNow();
});

test('teacher redirects to schedule after login', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher', 'password' => bcrypt('password')]);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
        ->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    
    // Assert
    $response->assertRedirect(route('teacher.schedule.index'));
});
