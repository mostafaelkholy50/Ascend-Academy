<?php

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\User;
use App\Services\TeacherScheduleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('it builds printable monthly schedule data for a teacher', function () {
    Carbon::setTestNow(Carbon::create(2026, 7, 25, 12, 0, 0, 'Africa/Cairo'));

    $service = app(TeacherScheduleService::class);

    $teacher = User::factory()->teacher()->create([
        'role' => 'Teacher',
        'timezone' => 'Africa/Cairo',
    ]);
    $student = User::factory()->student()->create();
    $course = Course::factory()->create();
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
        'session_duration' => 60,
    ]);

    $attendedSchedule = Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'starts_at' => Carbon::create(2026, 7, 6, 10, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 6, 11, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    Attendance::create([
        'schedule_id' => $attendedSchedule->id,
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'student_present' => true,
        'teacher_present' => true,
    ]);

    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'starts_at' => Carbon::create(2026, 7, 20, 10, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 20, 11, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    $data = $service->getPrintableMonthlyData($teacher, '2026-07');

    expect($data['teacher']->id)->toBe($teacher->id);
    expect($data['targetMonth']->format('Y-m'))->toBe('2026-07');
    expect($data['monthDays'])->toHaveCount(31);
    expect($data['monthDays'])->toHaveKey('2026-07-06');
    expect($data['monthDays']['2026-07-06']['schedules'])->toHaveCount(1);
    expect($data['monthDays']['2026-07-20']['schedules'])->toHaveCount(1);

    Carbon::setTestNow();
});

test('it does not include schedules for inactive days in printable monthly data', function () {
    Carbon::setTestNow(Carbon::create(2026, 7, 25, 12, 0, 0, 'Africa/Cairo'));

    $service = app(TeacherScheduleService::class);

    $teacher = User::factory()->teacher()->create([
        'role' => 'Teacher',
        'timezone' => 'Africa/Cairo',
    ]);
    $student = User::factory()->student()->create();
    $course = Course::factory()->create();
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
        'session_duration' => 60,
        'schedule_pattern' => [
            'Monday' => [
                'active' => false,
                'slots' => [['time' => '10:00', 'duration' => 60]],
            ],
            'Thursday' => [
                'active' => true,
                'slots' => [['time' => '10:00', 'duration' => 60]],
            ],
        ],
    ]);

    // Monday (Inactive)
    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'starts_at' => Carbon::create(2026, 7, 6, 10, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 6, 11, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    // Thursday (Active)
    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'starts_at' => Carbon::create(2026, 7, 9, 10, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 9, 11, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    $data = $service->getPrintableMonthlyData($teacher, '2026-07');

    expect($data['monthDays']['2026-07-06']['schedules'])->toHaveCount(0);
    expect($data['monthDays']['2026-07-09']['schedules'])->toHaveCount(1);

    Carbon::setTestNow();
});

test('it does not include schedules for inactive days in weekly data', function () {
    $service = app(TeacherScheduleService::class);

    $teacher = User::factory()->teacher()->create([
        'role' => 'Teacher',
        'timezone' => 'Africa/Cairo',
    ]);
    $student = User::factory()->student()->create();
    $course = Course::factory()->create();
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
        'session_duration' => 60,
        'schedule_pattern' => [
            'Monday' => [
                'active' => false,
                'slots' => [['time' => '10:00', 'duration' => 60]],
            ],
            'Thursday' => [
                'active' => true,
                'slots' => [['time' => '10:00', 'duration' => 60]],
            ],
        ],
    ]);

    $weekStart = now()->startOfWeek();

    // Monday (Inactive)
    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'starts_at' => $weekStart->copy()->next('Monday')->setTime(10, 0),
        'ends_at' => $weekStart->copy()->next('Monday')->setTime(11, 0),
        'status' => 'scheduled',
    ]);

    // Thursday (Active)
    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'starts_at' => $weekStart->copy()->next('Thursday')->setTime(10, 0),
        'ends_at' => $weekStart->copy()->next('Thursday')->setTime(11, 0),
        'status' => 'scheduled',
    ]);

    $request = new \Illuminate\Http\Request(['week' => $weekStart->format('Y-m-d')]);
    $data = $service->getWeeklyData($teacher, $request);
    
    $allSchedules = collect($data['schedulesByDay'])->flatMap(fn($day) => $day['schedules']);

    expect($allSchedules)->toHaveCount(1);
    expect($allSchedules->first()->starts_at->format('l'))->toBe('Thursday');
});

test('it does not include schedules for inactive days in daily data', function () {
    $service = app(TeacherScheduleService::class);

    $teacher = User::factory()->teacher()->create([
        'role' => 'Teacher',
        'timezone' => 'Africa/Cairo',
    ]);
    $student = User::factory()->student()->create();
    $course = Course::factory()->create();
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
        'session_duration' => 60,
        'schedule_pattern' => [
            'Monday' => [
                'active' => false,
                'slots' => [['time' => '10:00', 'duration' => 60]],
            ],
        ],
    ]);

    $monday = now()->next('Monday');

    // Monday (Inactive)
    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'starts_at' => $monday->copy()->setTime(10, 0),
        'ends_at' => $monday->copy()->setTime(11, 0),
        'status' => 'scheduled',
    ]);

    $request = new \Illuminate\Http\Request(['date' => $monday->format('Y-m-d')]);
    $data = $service->getDailyData($teacher, $request);
    
    expect($data['schedules'])->toHaveCount(0);
});
