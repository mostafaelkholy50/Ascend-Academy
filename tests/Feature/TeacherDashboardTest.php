<?php

use App\Models\User;
use App\Models\Schedule;
use App\Models\Course;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'Teacher']);
});

test('teacher can view dashboard', function () {
    // Arrange
    $user = User::factory()->teacher()->create(['role' => 'Teacher']);
    $user->assignRole('Teacher');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.dashboard'));
    
    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('teacher.dashboard');
    $response->assertViewHas('teacher');
    $response->assertViewHas('todaySchedules');
    $response->assertViewHas('weekSchedules');
    $response->assertViewHas('myStudents');
    $response->assertViewHas('stats');
    $response->assertSeeText('Today at a glance');
    $response->assertSeeText('Useful numbers, not decoration');
    $response->assertSeeText('This Month');
    $response->assertSeeText('This Week Summary');
    $response->assertSeeText('Pending Evaluations');
    $response->assertSee('grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 md:gap-4');
    $response->assertSee('grid grid-cols-2 gap-2 sm:gap-3');
});

test('non-teacher cannot view dashboard', function () {
    // Arrange
    $user = User::factory()->student()->create(['role' => 'Student']);
    Role::create(['name' => 'Student']);
    $user->assignRole('Student');
    
    // Act
    $response = $this->actingAs($user)->get(route('teacher.dashboard'));
    
    // Assert
    $response->assertStatus(403);
});

test('teacher dashboard shows schedule in teacher timezone', function () {
    $teacher = User::factory()->teacher()->create([
        'role' => 'Teacher',
        'timezone' => 'America/New_York',
    ]);
    $teacher->assignRole('Teacher');
    $student = User::factory()->student()->create(['role' => 'Student']);
    $course = Course::create(['title' => 'Timezone Course']);

    Schedule::create([
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'starts_at' => Carbon::today('Africa/Cairo')->setTime(10, 0),
        'ends_at' => Carbon::today('Africa/Cairo')->setTime(11, 0),
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($teacher)->get(route('teacher.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('3:00 AM');
    $response->assertDontSee('10:00 AM');
});
