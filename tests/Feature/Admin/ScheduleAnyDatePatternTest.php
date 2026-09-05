<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Tests for editing schedule pattern from any date (including past).
 * Ensures that selecting a start date like 2026-09-01 correctly updates
 * all schedules from that date onward, as requested for enrollment 22.
 */
test('can update pattern from any date including past and it modifies from chosen date', function () {
    Carbon::setTestNow(Carbon::create(2026, 9, 5, 12, 0, 0));

    $teacher = User::factory()->create(['role' => 'Teacher']);
    $student = User::factory()->create(['role' => 'Student']);
    $course = Course::create(['title' => 'AnyDate Course', 'description' => 'desc', 'level' => 'Beginner', 'age_group' => 'Kids', 'language' => 'English']);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'start_date' => Carbon::create(2026, 8, 1),
        'status' => 'active',
        'days_per_week' => 2,
        'session_duration' => 90,
        'currency' => 'USD',
        'admin_price' => 100,
        'schedule_pattern' => [
            'Wednesday' => ['active' => true, 'slots' => [['time' => '02:30', 'duration' => 90]]],
            'Friday' => ['active' => true, 'slots' => [['time' => '02:30', 'duration' => 90]]],
        ]
    ]);

    $service = app(ScheduleService::class);

    // Create schedules exactly as in the bug report: Aug28, Sep02, Sep04 are 90 min
    $dates = [
        '2026-08-28 02:30:00',
        '2026-09-02 02:30:00',
        '2026-09-04 02:30:00',
        '2026-09-09 02:30:00',
        '2026-09-11 02:30:00',
        '2026-09-16 02:30:00',
        '2026-09-18 02:30:00',
        '2026-09-23 02:30:00',
        '2026-09-25 02:30:00',
    ];
    foreach ($dates as $d) {
        $start = Carbon::parse($d);
        Schedule::create([
            'enrollment_id' => $enrollment->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'starts_at' => $start,
            'ends_at' => $start->copy()->addMinutes(90),
            'status' => 'scheduled',
        ]);
    }

    // User selects 2026-09-01 (past relative to Sep 5) and changes duration to 30
    $data = [
        'teacher_id' => $teacher->id,
        'day_active' => [
            'Sunday' => 0, 'Monday' => 0, 'Tuesday' => 0,
            'Wednesday' => 1, 'Thursday' => 0, 'Friday' => 1, 'Saturday' => 0,
        ],
        'schedule_times' => [
            'Sunday' => ['02:30'], 'Monday' => ['02:30'], 'Tuesday' => ['02:30'],
            'Wednesday' => ['02:30'], 'Thursday' => ['02:30'], 'Friday' => ['02:30'], 'Saturday' => ['02:30'],
        ],
        'durations' => [
            'Sunday' => [30], 'Monday' => [30], 'Tuesday' => [30],
            'Wednesday' => [30], 'Thursday' => [30], 'Friday' => [30], 'Saturday' => [30],
        ],
    ];

    $applyFrom = Carbon::create(2026, 9, 1);
    $result = $service->updateSchedulePattern($enrollment, $data, $applyFrom);

    expect($result['success'])->toBeTrue();

    // Aug28 is before Sep01, should remain 90 min
    $aug28 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-08-28')->first();
    expect($aug28)->not->toBeNull();
    expect($aug28->getDurationInMinutes())->toBe(90);

    // Sep02 and Sep04 are >= Sep01, should be updated to 30 min even though they are past (Sep 5 is today)
    $sep2 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-09-02')->first();
    $sep4 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-09-04')->first();
    expect($sep2)->not->toBeNull();
    expect($sep2->getDurationInMinutes())->toBe(30);
    expect($sep4)->not->toBeNull();
    expect($sep4->getDurationInMinutes())->toBe(30);

    // Future dates should also be 30
    $sep9 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-09-09')->first();
    expect($sep9->getDurationInMinutes())->toBe(30);

    Carbon::setTestNow();
});

test('update from future date keeps past schedules untouched', function () {
    Carbon::setTestNow(Carbon::create(2026, 9, 5, 12, 0, 0));

    $teacher = User::factory()->create(['role' => 'Teacher']);
    $student = User::factory()->create(['role' => 'Student']);
    $course = Course::create(['title' => 'Future Course', 'description' => 'desc', 'level' => 'Beginner', 'age_group' => 'Kids', 'language' => 'English']);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'start_date' => Carbon::create(2026, 9, 1),
        'status' => 'active',
        'days_per_week' => 1,
        'session_duration' => 60,
        'currency' => 'USD',
        'admin_price' => 100,
        'schedule_pattern' => [
            'Tuesday' => ['active' => true, 'slots' => [['time' => '10:00', 'duration' => 60]]],
        ]
    ]);

    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'starts_at' => Carbon::create(2026, 9, 1, 10, 0, 0), // Tuesday, past relative to Sep5
        'ends_at' => Carbon::create(2026, 9, 1, 11, 0, 0),
        'status' => 'scheduled',
    ]);
    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'starts_at' => Carbon::create(2026, 9, 15, 10, 0, 0), // Tuesday, future
        'ends_at' => Carbon::create(2026, 9, 15, 11, 0, 0),
        'status' => 'scheduled',
    ]);

    $service = app(ScheduleService::class);
    $data = [
        'teacher_id' => $teacher->id,
        'day_active' => ['Monday' => 0, 'Tuesday' => 1, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0],
        'schedule_times' => ['Monday' => ['12:00'], 'Tuesday' => ['14:00'], 'Wednesday' => ['12:00'], 'Thursday' => ['12:00'], 'Friday' => ['12:00'], 'Saturday' => ['12:00'], 'Sunday' => ['12:00']],
        'durations' => ['Monday' => [30], 'Tuesday' => [30], 'Wednesday' => [30], 'Thursday' => [30], 'Friday' => [30], 'Saturday' => [30], 'Sunday' => [30]],
    ];

    // Apply from Sep 10 (future) - Sep1 should remain 10:00
    $result = $service->updateSchedulePattern($enrollment, $data, Carbon::create(2026, 9, 10));
    expect($result['success'])->toBeTrue();

    $sep1 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-09-01')->first();
    expect($sep1->starts_at->format('H:i'))->toBe('10:00'); // unchanged

    $sep15 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-09-15')->first();
    expect($sep15->starts_at->format('H:i'))->toBe('14:00'); // updated

    Carbon::setTestNow();
});

test('can update pattern to any weekday from any date', function () {
    $teacher = User::factory()->create(['role' => 'Teacher']);
    $student = User::factory()->create(['role' => 'Student']);
    $course = Course::create(['title' => 'Any Weekday Course', 'description' => 'desc', 'level' => 'Beginner', 'age_group' => 'Kids', 'language' => 'English']);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'start_date' => Carbon::create(2026, 9, 1),
        'status' => 'active',
        'days_per_week' => 1,
        'session_duration' => 60,
        'currency' => 'USD',
        'admin_price' => 100,
        'schedule_pattern' => [
            'Monday' => ['active' => true, 'slots' => [['time' => '09:00', 'duration' => 60]]],
        ]
    ]);

    Schedule::create([
        'enrollment_id' => $enrollment->id,
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'starts_at' => Carbon::create(2026, 9, 8, 9, 0, 0), // Monday
        'ends_at' => Carbon::create(2026, 9, 8, 10, 0, 0),
        'status' => 'scheduled',
    ]);

    $service = app(ScheduleService::class);
    // Change to Tuesday from Sep 1
    $data = [
        'teacher_id' => $teacher->id,
        'day_active' => ['Monday' => 0, 'Tuesday' => 1, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0],
        'schedule_times' => ['Monday' => ['09:00'], 'Tuesday' => ['15:00'], 'Wednesday' => ['12:00'], 'Thursday' => ['12:00'], 'Friday' => ['12:00'], 'Saturday' => ['12:00'], 'Sunday' => ['12:00']],
        'durations' => ['Monday' => [60], 'Tuesday' => [45], 'Wednesday' => [60], 'Thursday' => [60], 'Friday' => [60], 'Saturday' => [60], 'Sunday' => [60]],
    ];

    $result = $service->updateSchedulePattern($enrollment, $data, Carbon::create(2026, 9, 1));
    expect($result['success'])->toBeTrue();

    $pattern = $enrollment->fresh()->getSchedulePattern();
    expect($pattern)->toHaveKey('Tuesday');
    expect($pattern['Tuesday']['slots'][0]['time'])->toBe('15:00');
    expect(isset($pattern['Monday']))->toBeFalse();
});

test('update pattern writes detailed schedule change log', function () {
    Carbon::setTestNow(Carbon::create(2026, 9, 5, 12, 0, 0));

    $originalLog = app('log');
    $logger = \Mockery::mock(\Psr\Log\LoggerInterface::class);
    $logger->shouldReceive('info')->once()->with(
        'Schedule pattern updated',
        \Mockery::on(function (array $context) {
            return is_array($context);
        })
    );
    Log::swap($logger);

    try {
        $teacher = User::factory()->create([
            'role' => 'Teacher',
            'timezone' => 'Asia/Riyadh',
        ]);
        $student = User::factory()->create(['role' => 'Student']);
        $course = Course::create([
            'title' => 'Logging Course',
            'description' => 'desc',
            'level' => 'Beginner',
            'age_group' => 'Kids',
            'language' => 'English',
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'start_date' => Carbon::create(2026, 8, 1),
            'status' => 'active',
            'days_per_week' => 2,
            'session_duration' => 90,
            'currency' => 'USD',
            'admin_price' => 100,
            'schedule_pattern' => [
                'Wednesday' => ['active' => true, 'slots' => [['time' => '02:30', 'duration' => 90]]],
                'Friday' => ['active' => true, 'slots' => [['time' => '02:30', 'duration' => 90]]],
            ],
        ]);

        $service = app(ScheduleService::class);

        collect([
            '2026-09-02 02:30:00',
            '2026-09-04 02:30:00',
            '2026-09-09 02:30:00',
            '2026-09-11 02:30:00',
            '2026-09-16 02:30:00',
        ])->each(function (string $datetime) use ($enrollment, $student, $teacher, $course) {
            $start = Carbon::parse($datetime);

            Schedule::create([
                'enrollment_id' => $enrollment->id,
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'course_id' => $course->id,
                'starts_at' => $start,
                'ends_at' => $start->copy()->addMinutes(90),
                'status' => 'scheduled',
            ]);
        });

        $result = $service->updateSchedulePattern($enrollment, [
            'teacher_id' => $teacher->id,
            'day_active' => [
                'Sunday' => 0,
                'Monday' => 0,
                'Tuesday' => 0,
                'Wednesday' => 1,
                'Thursday' => 0,
                'Friday' => 1,
                'Saturday' => 0,
            ],
            'schedule_times' => [
                'Sunday' => ['02:30'],
                'Monday' => ['02:30'],
                'Tuesday' => ['02:30'],
                'Wednesday' => ['02:30'],
                'Thursday' => ['02:30'],
                'Friday' => ['02:30'],
                'Saturday' => ['02:30'],
            ],
            'durations' => [
                'Sunday' => [30],
                'Monday' => [30],
                'Tuesday' => [30],
                'Wednesday' => [30],
                'Thursday' => [30],
                'Friday' => [30],
                'Saturday' => [30],
            ],
        ], Carbon::create(2026, 9, 1));

        expect($result['success'])->toBeTrue();

        $logger->shouldHaveReceived('info')->once()->with(
            'Schedule pattern updated',
            \Mockery::on(function (array $context) use ($enrollment) {
                return ($context['enrollment_id'] ?? null) === $enrollment->id
                    && ($context['apply_from_date'] ?? null) === '2026-09-01'
                    && ($context['teacher_timezone'] ?? null) === 'Asia/Riyadh'
                    && ($context['changed_days'] ?? []) === ['Wednesday', 'Friday']
                    && ($context['added_days'] ?? []) === []
                    && ($context['removed_days'] ?? []) === []
                    && ($context['updated_days'] ?? []) === ['Wednesday', 'Friday']
                    && ($context['affected_schedule_days'] ?? []) === ['Wednesday', 'Friday']
                    && ($context['deleted_sessions'] ?? null) === 5
                    && ($context['created_sessions'] ?? null) === 5
                    && collect($context['entered_pattern'] ?? [])->pluck('day')->all() === ['Wednesday', 'Friday']
                    && (($context['pattern_changes'][0]['day'] ?? null) === 'Wednesday')
                    && (($context['pattern_changes'][0]['status'] ?? null) === 'updated')
                    && (($context['pattern_changes'][0]['before']['slots'][0]['duration_minutes'] ?? null) === 90)
                    && (($context['pattern_changes'][0]['after']['slots'][0]['duration_minutes'] ?? null) === 30)
                    && (($context['pattern_changes'][1]['day'] ?? null) === 'Friday')
                    && (($context['pattern_changes'][1]['status'] ?? null) === 'updated')
                    && (($context['entered_pattern'][0]['slots'][0]['time'] ?? null) === '02:30')
                    && (($context['entered_pattern'][0]['slots'][0]['duration_minutes'] ?? null) === 30)
                    && (($context['entered_pattern'][0]['slots'][0]['timezone'] ?? null) === 'Asia/Riyadh');
            })
        );
    } finally {
        Log::swap($originalLog);
        Carbon::setTestNow();
    }
});

test('update pattern logs added and removed days separately', function () {
    Carbon::setTestNow(Carbon::create(2026, 9, 5, 12, 0, 0));

    $originalLog = app('log');
    $logger = \Mockery::mock(\Psr\Log\LoggerInterface::class);
    $logger->shouldReceive('info')->once()->with(
        'Schedule pattern updated',
        \Mockery::on(function (array $context) {
            return ($context['added_days'] ?? []) === ['Tuesday']
                && ($context['removed_days'] ?? []) === ['Monday']
                && ($context['updated_days'] ?? []) === []
                && ($context['changed_days'] ?? []) === ['Monday', 'Tuesday']
                && (($context['pattern_changes'][0]['day'] ?? null) === 'Monday')
                && (($context['pattern_changes'][0]['status'] ?? null) === 'removed')
                && (($context['pattern_changes'][0]['before']['slots'][0]['time'] ?? null) === '09:00')
                && (($context['pattern_changes'][0]['after'] ?? null) === null)
                && (($context['pattern_changes'][1]['day'] ?? null) === 'Tuesday')
                && (($context['pattern_changes'][1]['status'] ?? null) === 'added')
                && (($context['pattern_changes'][1]['before'] ?? null) === null)
                && (($context['pattern_changes'][1]['after']['slots'][0]['time'] ?? null) === '15:00');
        })
    );
    Log::swap($logger);

    try {
        $teacher = User::factory()->create([
            'role' => 'Teacher',
            'timezone' => 'Asia/Riyadh',
        ]);
        $student = User::factory()->create(['role' => 'Student']);
        $course = Course::create([
            'title' => 'Added Removed Logging Course',
            'description' => 'desc',
            'level' => 'Beginner',
            'age_group' => 'Kids',
            'language' => 'English',
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'start_date' => Carbon::create(2026, 8, 1),
            'status' => 'active',
            'days_per_week' => 1,
            'session_duration' => 60,
            'currency' => 'USD',
            'admin_price' => 100,
            'schedule_pattern' => [
                'Monday' => ['active' => true, 'slots' => [['time' => '09:00', 'duration' => 60]]],
            ],
        ]);

        Schedule::create([
            'enrollment_id' => $enrollment->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'starts_at' => Carbon::create(2026, 9, 1, 9, 0, 0),
            'ends_at' => Carbon::create(2026, 9, 1, 10, 0, 0),
            'status' => 'scheduled',
        ]);

        $service = app(ScheduleService::class);
        $result = $service->updateSchedulePattern($enrollment, [
            'teacher_id' => $teacher->id,
            'day_active' => [
                'Sunday' => 0,
                'Monday' => 0,
                'Tuesday' => 1,
                'Wednesday' => 0,
                'Thursday' => 0,
                'Friday' => 0,
                'Saturday' => 0,
            ],
            'schedule_times' => [
                'Sunday' => ['12:00'],
                'Monday' => ['09:00'],
                'Tuesday' => ['15:00'],
                'Wednesday' => ['12:00'],
                'Thursday' => ['12:00'],
                'Friday' => ['12:00'],
                'Saturday' => ['12:00'],
            ],
            'durations' => [
                'Sunday' => [60],
                'Monday' => [60],
                'Tuesday' => [45],
                'Wednesday' => [60],
                'Thursday' => [60],
                'Friday' => [60],
                'Saturday' => [60],
            ],
        ], Carbon::create(2026, 9, 1));

        expect($result['success'])->toBeTrue();
    } finally {
        Log::swap($originalLog);
        Carbon::setTestNow();
    }
});
