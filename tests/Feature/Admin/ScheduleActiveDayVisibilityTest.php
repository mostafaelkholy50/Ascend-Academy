<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ScheduleService;
use App\Services\TeacherScheduleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);

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

test('scheduled sessions visible to teachers are also visible in the admin calendar', function () {
    $saturdaySchedule = Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::create(2026, 7, 4, 8, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 4, 9, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    $sundaySchedule = Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::create(2026, 7, 5, 8, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 5, 9, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    $teacherData = app(TeacherScheduleService::class)->getWeeklyData($this->teacher, Request::create('/teacher/schedule', 'GET', [
        'week' => '2026-06-29',
    ]));

    $adminData = app(ScheduleService::class)->getCalendarData(Request::create('/admin/schedules', 'GET', [
        'week' => '2026-06-29',
    ]));

    $teacherScheduleIds = collect($teacherData['schedulesByDay'])
        ->flatMap(fn ($day) => $day['schedules'])
        ->pluck('id')
        ->sort()
        ->values()
        ->all();

    $adminScheduleIds = collect($adminData['weekDays'])
        ->flatMap(fn ($day) => $day['schedules'])
        ->pluck('id')
        ->sort()
        ->values()
        ->all();

    expect($teacherScheduleIds)->toBe([$saturdaySchedule->id, $sundaySchedule->id]);
    expect($adminScheduleIds)->toBe($teacherScheduleIds);
});

test('scheduled sessions visible to teachers are also visible in the printable admin schedule', function () {
    $saturdaySchedule = Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::create(2026, 7, 4, 8, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 4, 9, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    $admin = User::factory()->create(['role' => 'Admin']);
    $admin->assignRole('Admin');
    $this->actingAs($admin);

    $response = $this->get(route('scheduler.schedules.print', [
        'teacher_id' => $this->teacher->id,
        'month' => '2026-07',
    ]));

    $response->assertOk();
    $response->assertSee($this->student->name);
    $response->assertViewHas('monthDays', function (array $monthDays) use ($saturdaySchedule) {
        return $monthDays['2026-07-04']['schedules']->pluck('id')->contains($saturdaySchedule->id);
    });
});

test('admin calendar stacks simultaneous scheduled sessions in the same hour cell', function () {
    $otherTeacher = User::factory()->create(['role' => 'Teacher', 'active' => true]);
    $otherStudent = User::factory()->create(['role' => 'Student', 'name' => 'Visible Calendar Student']);
    $otherEnrollment = Enrollment::create([
        'student_id' => $otherStudent->id,
        'course_id' => $this->course->id,
        'status' => 'active',
        'start_date' => Carbon::create(2026, 7, 1, 0, 0, 0, 'Africa/Cairo'),
        'days_per_week' => 1,
        'session_duration' => 60,
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

    Schedule::create([
        'enrollment_id' => $otherEnrollment->id,
        'student_id' => $otherStudent->id,
        'teacher_id' => $otherTeacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::create(2026, 7, 5, 8, 0, 0, 'Africa/Cairo'),
        'ends_at' => Carbon::create(2026, 7, 5, 9, 0, 0, 'Africa/Cairo'),
        'status' => 'scheduled',
    ]);

    $admin = User::factory()->create(['role' => 'Admin']);
    $admin->assignRole('Admin');

    $response = $this->actingAs($admin)->get(route('admin.schedules.index', [
        'view' => 'calendar',
        'week' => '2026-06-29',
    ]));

    $response->assertOk();
    $response->assertSee($this->student->name);
    $response->assertSee('Visible Calendar Student');
    $response->assertSee('schedule-block relative', false);
    $response->assertDontSee('schedule-block absolute', false);
});
