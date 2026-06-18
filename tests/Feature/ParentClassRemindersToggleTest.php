<?php

use App\Models\User;
use App\Models\Schedule;
use App\Models\Course;
use App\Notifications\ClassReminderNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Notifications\TeacherDailyScheduleNotification;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('new parent has class reminders enabled by default', function () {
    $parent = User::factory()->create(['role' => 'Parent']);
    $parent->refresh();
    expect($parent->class_reminders_enabled)->toBeTrue();
});

test('admin can disable and enable class reminders for a parent', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $parent = User::factory()->create([
        'role' => 'Parent',
        'class_reminders_enabled' => true,
    ]);

    // Admin disables it
    $response = $this->actingAs($admin)->patch(route('admin.parents.update', $parent->id), [
        'name' => $parent->name,
        'email' => $parent->email,
        'phone' => $parent->phone,
        'class_reminders_enabled' => '0',
    ]);

    $response->assertSessionHasNoErrors();
    $parent->refresh();
    expect($parent->class_reminders_enabled)->toBeFalse();

    // Admin enables it
    $response = $this->actingAs($admin)->patch(route('admin.parents.update', $parent->id), [
        'name' => $parent->name,
        'email' => $parent->email,
        'phone' => $parent->phone,
        'class_reminders_enabled' => '1',
    ]);

    $response->assertSessionHasNoErrors();
    $parent->refresh();
    expect($parent->class_reminders_enabled)->toBeTrue();
});

test('parent cannot modify class reminders setting via profile update', function () {
    $parent = User::factory()->create([
        'role' => 'Parent',
        'class_reminders_enabled' => true,
    ]);

    // Parent attempts to disable it
    $response = $this->actingAs($parent)->patch(route('parent.profile.update'), [
        'name' => 'Updated Parent Name',
        'email' => $parent->email,
        'phone' => '123456789',
        'class_reminders_enabled' => '0', // Attempt to disable
    ]);

    $parent->refresh();
    // It should still be true (ignored)
    expect($parent->class_reminders_enabled)->toBeTrue();
});

test('class reminders command respects parent toggle setting', function () {
    Notification::fake();

    // Arrange: Create a parent, student (child), teacher, course, and a scheduled class in the next 2 hours
    $parent = User::factory()->create(['role' => 'Parent', 'class_reminders_enabled' => true]);
    $student = User::factory()->create(['role' => 'Student']);
    $teacher = User::factory()->create(['role' => 'Teacher']);
    
    $parent->children()->attach($student->id);
    $course = Course::create(['title' => 'Arabic Language']);

    $schedule = Schedule::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'starts_at' => Carbon::now()->addHours(2),
        'ends_at' => Carbon::now()->addHours(3),
        'status' => 'scheduled',
    ]);

    // 1. First run: Parent toggle is ENABLED
    $this->artisan('class:send-reminders')
        ->assertSuccessful();

    // Check notifications were sent to student, parent, and teacher
    Notification::assertSentTo([$student, $parent], ClassReminderNotification::class);
    Notification::assertSentTo($teacher, TeacherDailyScheduleNotification::class);

    // Reset notification fake
    Notification::fake();

    // 2. Second run: Parent toggle is DISABLED
    $parent->update(['class_reminders_enabled' => false]);

    $this->artisan('class:send-reminders')
        ->assertSuccessful();

    // Student and Parent should NOT get reminders, but Teacher should STILL get the reminder
    Notification::assertNotSentTo([$student, $parent], ClassReminderNotification::class);
    Notification::assertSentTo($teacher, TeacherDailyScheduleNotification::class);
});
