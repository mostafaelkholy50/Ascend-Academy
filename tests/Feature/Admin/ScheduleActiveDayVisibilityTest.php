<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
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
            'Sunday' => [
                'active' => true,
                'slots' => [
                    ['time' => '08:00', 'duration' => 60],
                ],
            ],
            'Saturday' => [
                'active' => false,
                'slots' => [
                    ['time' => '08:00', 'duration' => 60],
                ],
            ],
        ],
    ]);
});

test('active days generate monthly schedules while inactive days do not', function () {
    $service = app(ScheduleService::class);

    $result = $service->generateMonthlySchedules($this->enrollment, '2026-07', $this->teacher->id);

    expect($result['success'])->toBeTrue();

    $sundayCount = Schedule::where('enrollment_id', $this->enrollment->id)
        ->whereDate('starts_at', '2026-07-05')
        ->count();

    $saturdayCount = Schedule::where('enrollment_id', $this->enrollment->id)
        ->whereDate('starts_at', '2026-07-04')
        ->count();

    expect($sundayCount)->toBe(1);
    expect($saturdayCount)->toBe(0);
});

test('inactive day schedules are hidden from admin calendar', function () {
    Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::create(2026, 7, 4, 8, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 4, 9, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::create(2026, 7, 5, 8, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 5, 9, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    $service = app(ScheduleService::class);
    $data = $service->getCalendarData(Request::create('/admin/schedules', 'GET', [
        'week' => '2026-06-29',
    ]));

    $weekDays = collect($data['weekDays']);

    $saturday = $weekDays->firstWhere('date', Carbon::create(2026, 7, 4, 0, 0, 0, 'Africa/Cairo'));
    $sunday = $weekDays->firstWhere('date', Carbon::create(2026, 7, 5, 0, 0, 0, 'Africa/Cairo'));

    expect($saturday['schedules'])->toHaveCount(0);
    expect($sunday['schedules'])->toHaveCount(1);
});
