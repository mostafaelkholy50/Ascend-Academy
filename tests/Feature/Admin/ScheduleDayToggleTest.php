<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Admin');
    
    $this->teacher = User::factory()->create(['role' => 'Teacher', 'active' => true]);
    $this->student = User::factory()->create(['role' => 'Student']);
    $this->course = Course::create([
        'title' => 'Test Course',
        'description' => 'Test',
        'status' => 'published',
    ]);

    $this->enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'course_id' => $this->course->id,
        'status' => 'active',
        'start_date' => Carbon::create(2026, 7, 1, 0, 0, 0, 'Africa/Cairo'),
        'days_per_week' => 2,
        'session_duration' => 60,
        'schedule_pattern' => [
            'Monday' => [
                'active' => true,
                'slots' => [
                    ['time' => '08:00', 'duration' => 60],
                ],
            ],
            'Wednesday' => [
                'active' => true,
                'slots' => [
                    ['time' => '08:00', 'duration' => 60],
                ],
            ],
        ],
    ]);
});

test('toggling active day cancels upcoming sessions and updates pattern status', function () {
    // Create upcoming sessions
    $mondaySession = Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::now()->addDays(5)->next('Monday')->setTime(8, 0),
        'ends_at' => Carbon::now()->addDays(5)->next('Monday')->setTime(9, 0),
        'status' => 'scheduled',
    ]);

    $wednesdaySession = Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::now()->addDays(5)->next('Wednesday')->setTime(8, 0),
        'ends_at' => Carbon::now()->addDays(5)->next('Wednesday')->setTime(9, 0),
        'status' => 'scheduled',
    ]);

    $this->actingAs($this->admin);

    $response = $this->post(route('admin.schedules.toggle-day', [$this->enrollment->id, 'Monday']));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify Monday is deactivated in database pattern
    $this->enrollment->refresh();
    $pattern = $this->enrollment->getSchedulePattern();
    expect($pattern['Monday']['active'])->toBeFalse();
    expect($pattern['Wednesday']['active'])->toBeTrue();

    // Verify Monday session is cancelled while Wednesday remains scheduled
    $mondaySession->refresh();
    $wednesdaySession->refresh();
    expect($mondaySession->status)->toBe('cancelled');
    expect($wednesdaySession->status)->toBe('scheduled');
});
