<?php

use App\Models\Course;
use App\Models\Schedule;
use App\Models\User;
use App\Notifications\ClassReminderNotification;
use App\Notifications\TeacherDailyScheduleNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

it('sends class reminders and prevents duplicate emails using cache', function () {
    Notification::fake();
    Cache::flush();

    // Create a teacher and a student
    $teacher = User::factory()->create(['role' => 'Teacher']);
    $student = User::factory()->create(['role' => 'Student']);
    $course = Course::factory()->create();

    // Create a schedule for today
    $now = Carbon::now();
    $schedule = Schedule::factory()->create([
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'starts_at' => $now->copy()->addHours(2),
        'ends_at' => $now->copy()->addHours(3),
        'status' => 'scheduled',
    ]);

    // Run the command for the FIRST time
    $this->artisan('class:send-reminders')->assertSuccessful();

    // Assert that notifications were sent ONCE
    Notification::assertSentTo($teacher, TeacherDailyScheduleNotification::class);
    Notification::assertSentTo($student, ClassReminderNotification::class);

    // Clear the faked notifications so we can track the next run
    Notification::fake();

    // Run the command for the SECOND time (simulating cron running twice)
    $this->artisan('class:send-reminders')->assertSuccessful();

    // Assert that NO notifications were sent this time because of the Cache lock
    Notification::assertNothingSent();
});
