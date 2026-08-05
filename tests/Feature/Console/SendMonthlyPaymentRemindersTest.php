<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentPayment;
use App\Models\User;
use App\Notifications\MonthlyPaymentReminderNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('send monthly payment reminders skips when a previous run is still locked', function () {
    Carbon::setTestNow(Carbon::today()->setTime(8, 0));
    Notification::fake();

    Cache::put('cron_lock:payment_send_reminders', true, now()->addMinutes(55));

    $student = User::factory()->create(['role' => 'Student']);
    $course = Course::create(['title' => 'Math']);
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
        'session_duration' => 60,
        'start_date' => now()->subMonth(),
    ]);

    EnrollmentPayment::create([
        'enrollment_id' => $enrollment->id,
        'month' => now()->startOfMonth(),
        'amount' => 100,
        'currency' => 'CAD',
        'payment_status' => 'unpaid',
    ]);

    $this->artisan('payment:send-reminders')
        ->expectsOutputToContain('Skipping: another payment reminder run is still active.')
        ->assertSuccessful();

    Notification::assertNothingSent();
});

test('send monthly payment reminders stops at the configured email limit', function () {
    Carbon::setTestNow(Carbon::today()->setTime(8, 0));
    Notification::fake();

    $student = User::factory()->create(['role' => 'Student']);
    $parent1 = User::factory()->create(['role' => 'Parent', 'class_reminders_enabled' => true]);
    $parent2 = User::factory()->create(['role' => 'Parent', 'class_reminders_enabled' => true]);
    $student->parents()->attach([$parent1->id, $parent2->id]);

    $course = Course::create(['title' => 'Math']);

    for ($i = 0; $i < 15; $i++) {
        $loopCourse = Course::create(['title' => 'Math ' . ($i + 1)]);
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $loopCourse->id,
            'status' => 'active',
            'session_duration' => 60,
            'start_date' => now()->subMonth(),
        ]);

        EnrollmentPayment::create([
            'enrollment_id' => $enrollment->id,
            'month' => now()->startOfMonth(),
            'amount' => 100,
            'currency' => 'CAD',
            'payment_status' => 'unpaid',
        ]);
    }

    $this->artisan('payment:send-reminders')
        ->expectsOutputToContain('Skipped')
        ->assertSuccessful();

    Notification::assertSentTo($student, MonthlyPaymentReminderNotification::class, 10);
    Notification::assertSentTo($parent1, MonthlyPaymentReminderNotification::class, 10);
    Notification::assertSentTo($parent2, MonthlyPaymentReminderNotification::class, 10);
});
