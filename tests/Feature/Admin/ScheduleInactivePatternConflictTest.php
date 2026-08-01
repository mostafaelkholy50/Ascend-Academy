<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
    $this->admin = User::factory()->create(['role' => 'Admin']);
    $this->admin->assignRole('Admin');

    $this->teacher = User::factory()->create(['role' => 'Teacher', 'name' => 'Ms. Samar Ali Al-Shamy', 'active' => true]);
    $this->studentA = User::factory()->create(['role' => 'Student', 'name' => 'Amna']);
    $this->studentB = User::factory()->create(['role' => 'Student', 'name' => 'Fatma']);
    $this->course = Course::create([
        'title' => 'Qur’an Memorization',
        'description' => 'Test',
        'status' => 'published',
    ]);

    $this->pausedEnrollment = Enrollment::create([
        'student_id' => $this->studentA->id,
        'course_id' => $this->course->id,
        'status' => 'active',
        'start_date' => Carbon::create(2026, 2, 1, 0, 0, 0, 'Africa/Cairo'),
        'days_per_week' => 1,
        'session_duration' => 60,
        'schedule_pattern' => [
            'Monday' => [
                'active' => false,
                'slots' => [
                    ['time' => '20:30', 'duration' => 60],
                ],
            ],
        ],
    ]);

    $this->activeEnrollment = Enrollment::create([
        'student_id' => $this->studentB->id,
        'course_id' => $this->course->id,
        'status' => 'active',
        'start_date' => Carbon::create(2026, 2, 1, 0, 0, 0, 'Africa/Cairo'),
        'days_per_week' => 1,
        'session_duration' => 60,
        'schedule_pattern' => [
            'Monday' => [
                'active' => true,
                'slots' => [
                    ['time' => '20:30', 'duration' => 60],
                ],
            ],
        ],
    ]);
});

test('storeSchedule ignores conflicts coming from paused schedule patterns', function () {
    Schedule::create([
        'enrollment_id' => $this->pausedEnrollment->id,
        'student_id' => $this->studentA->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::create(2026, 2, 2, 20, 30, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 2, 2, 21, 30, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    $service = app(ScheduleService::class);

    $result = $service->storeSchedule([
        'student_id' => $this->studentB->id,
        'course_id' => $this->course->id,
        'teacher_id' => $this->teacher->id,
        'start_date' => '2026-02-01',
        'days' => ['Monday'],
        'schedule_times' => [
            'Monday' => ['20:30'],
        ],
        'durations' => [
            'Monday' => [60],
        ],
    ]);

    expect($result)->toBe(4);

    $newSchedule = Schedule::where('student_id', $this->studentB->id)->first();
    expect($newSchedule)->not->toBeNull();
    expect($newSchedule->starts_at->format('Y-m-d H:i'))->toBe('2026-02-02 20:30');
});

test('edit pattern page preserves paused days so they can be resumed later', function () {
    $this->actingAs($this->admin);

    $response = $this->get(route('admin.schedules.edit-pattern', $this->pausedEnrollment->id));

    $response->assertOk();
    $response->assertSee('Monday');
    $response->assertSee('20:30');
    $response->assertSee('Ms. Samar Ali Al-Shamy');
});

test('toggle all can resume paused schedules and keep conflicts clean afterward', function () {
    Schedule::create([
        'enrollment_id' => $this->pausedEnrollment->id,
        'student_id' => $this->studentA->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::create(2026, 2, 2, 20, 30, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 2, 2, 21, 30, 0, 'Africa/Cairo'),
        'status' => 'cancelled',
    ]);

    $this->actingAs($this->admin);

    $response = $this->post(route('admin.schedules.toggle-all', $this->pausedEnrollment->id));

    $response->assertRedirect(route('admin.schedules.index', ['view' => 'list']));
    $this->pausedEnrollment->refresh();

    $pattern = $this->pausedEnrollment->getSchedulePattern();
    expect($pattern['Monday']['active'])->toBeTrue();
});
