<?php

use App\Models\User;
use App\Models\Schedule;
use App\Models\Course;
use App\Notifications\ClassReminderNotification;
use App\Notifications\TeacherDailyScheduleNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('send class reminders successfully groups teacher schedules', function () {
    Carbon::setTestNow(Carbon::today()->setTime(8, 0));
    Notification::fake();

    $teacher1 = User::factory()->create(['role' => 'Teacher']);
    $teacher2 = User::factory()->create(['role' => 'Teacher']);
    
    $student = User::factory()->create(['role' => 'Student']);
    $course = Course::create(['title' => 'Math']);

    // Teacher 1 has two schedules
    Schedule::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher1->id,
        'course_id' => $course->id,
        'starts_at' => Carbon::now()->addHours(2),
        'ends_at' => Carbon::now()->addHours(3),
        'status' => 'scheduled',
    ]);
    Schedule::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher1->id,
        'course_id' => $course->id,
        'starts_at' => Carbon::now()->addHours(5),
        'ends_at' => Carbon::now()->addHours(6),
        'status' => 'scheduled',
    ]);

    // Teacher 2 has one schedule
    Schedule::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher2->id,
        'course_id' => $course->id,
        'starts_at' => Carbon::now()->addHours(10),
        'ends_at' => Carbon::now()->addHours(11),
        'status' => 'scheduled',
    ]);

    $this->artisan('class:send-reminders')
        ->expectsOutputToContain('Successfully sent 5 reminder emails')
        ->expectsOutputToContain('Teacher digests: 2, class reminders: 3')
        ->assertSuccessful();

    // Students get individual reminders (3 schedules -> 3 reminders)
    Notification::assertSentTo($student, ClassReminderNotification::class, 3);

    // Teachers get 1 daily digest each
    Notification::assertSentTo($teacher1, TeacherDailyScheduleNotification::class, function ($notification) {
        return $notification->schedules->count() === 2;
    });

    Notification::assertSentTo($teacher2, TeacherDailyScheduleNotification::class, function ($notification) {
        return $notification->schedules->count() === 1;
    });
});

test('send class reminders handles normal schedules without failing', function () {
    Carbon::setTestNow(Carbon::today()->setTime(8, 0));
    Notification::fake();

    $teacher = User::factory()->create(['role' => 'Teacher']);
    $student = User::factory()->create(['role' => 'Student']);
    $course = Course::create(['title' => 'Math']);

    // Schedule 1: valid
    Schedule::create([
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'starts_at' => Carbon::now()->addHours(2),
        'ends_at' => Carbon::now()->addHours(3),
        'status' => 'scheduled',
    ]);

    $this->artisan('class:send-reminders')
        ->expectsOutputToContain('Successfully sent 2 reminder emails')
        ->assertSuccessful();

    // Check that the valid schedule still sent the student reminder and teacher digest
    Notification::assertSentTo($student, ClassReminderNotification::class);
    Notification::assertSentTo($teacher, TeacherDailyScheduleNotification::class);
});
