<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->teacher = User::factory()->create(['role' => 'Teacher', 'name' => 'John Doe']);
    $this->student = User::factory()->create(['role' => 'Student', 'name' => 'Jane Smith']);
    $this->course = Course::create([
        'title' => 'Quran Basics', 
        'description' => 'Test', 
        'level' => 'Beginner', 
        'age_group' => 'Kids', 
        'language' => 'English'
    ]);
    
    $this->enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'course_id' => $this->course->id,
        'start_date' => Carbon::today()->startOfMonth(),
        'status' => 'active',
        'days_per_week' => 2,
        'session_duration' => 60,
        'currency' => 'USD',
        'admin_price' => 10,
        'schedule_pattern' => ['Saturday' => '10:00', 'Tuesday' => '10:00']
    ]);

    // Create a schedule in the past
    Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => Carbon::now()->subDays(2)->startOfDay()->addHours(10),
        'ends_at' => Carbon::now()->subDays(2)->startOfDay()->addHours(11),
        'status' => 'completed',
    ]);

    // Create an upcoming schedule (Tomorrow)
    $this->upcomingDate1 = Carbon::tomorrow()->startOfDay();
    Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => $this->upcomingDate1->copy()->addHours(10),
        'ends_at' => $this->upcomingDate1->copy()->addHours(11),
        'status' => 'scheduled',
    ]);

    // Create another upcoming schedule (In 4 days)
    $this->upcomingDate2 = Carbon::today()->addDays(4)->startOfDay();
    Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => $this->upcomingDate2->copy()->addHours(10),
        'ends_at' => $this->upcomingDate2->copy()->addHours(11),
        'status' => 'scheduled',
    ]);
});

test('updateSchedulePattern successfully replaces upcoming schedules', function () {
    $service = app(ScheduleService::class);
    
    // We want to change the pattern to Saturday and Thursday at 12:00
    $data = [
        'teacher_id' => $this->teacher->id,
        'durations' => [
            'Monday' => [60],
            'Wednesday' => [60],
        ],
        'days' => ['Saturday', 'Thursday'],
        'schedule_times' => [
            'Saturday' => ['12:00', '18:00'],
            'Thursday' => ['12:00'],
        ],
    ];

    $result = $service->updateSchedulePattern($this->enrollment, $data, Carbon::create(2026, 8, 1));

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toContain('Pattern updated successfully');

    $this->enrollment->refresh();
    expect($this->enrollment->days_per_week)->toBe(2);
    $pattern = $this->enrollment->getSchedulePattern();
    expect($pattern)->toHaveKey('Saturday');
    expect($pattern['Saturday']['active'])->toBeTrue();
    expect($pattern['Saturday']['slots'])->toContain(['time' => '12:00', 'duration' => 60]);
    expect($pattern['Saturday']['slots'])->toContain(['time' => '18:00', 'duration' => 60]);
    expect($pattern)->toHaveKey('Thursday');
    expect($pattern['Thursday']['active'])->toBeTrue();
    expect($pattern['Thursday']['slots'])->toContain(['time' => '12:00', 'duration' => 60]);

    // Past completed schedules matching old pattern should NOT be gone
    $oldPastSchedules = Schedule::where('enrollment_id', $this->enrollment->id)
        ->whereTime('starts_at', '10:00:00')
        ->where('status', 'completed')
        ->count();
    expect($oldPastSchedules)->toBe(1);

    // New schedules (past and future) should be created
    $newSchedules = Schedule::where('enrollment_id', $this->enrollment->id)
        ->whereTime('starts_at', '12:00:00')
        ->get();
    expect($newSchedules->count())->toBeGreaterThan(0);
});

test('updateSchedulePattern rolls back on conflict', function () {
    $service = app(ScheduleService::class);
    
    // Create another student with an upcoming schedule that conflicts inside the regeneration range
    $otherStudent = User::factory()->create(['role' => 'Student', 'name' => 'Alice']);
    $otherCourse = Course::create(['title' => 'Arabic', 'description' => 'Test', 'level' => 'Beginner', 'age_group' => 'Kids', 'language' => 'English']);
    $otherEnrollment = Enrollment::create([
        'student_id' => $otherStudent->id,
        'course_id' => $otherCourse->id,
        'start_date' => Carbon::today()->startOfMonth(),
        'status' => 'active',
        'days_per_week' => 1,
        'session_duration' => 60,
    ]);

    Carbon::setTestNow(Carbon::create(2026, 8, 3, 12, 0, 0));
    $conflictDate = Carbon::create(2026, 8, 6, 0, 0, 0);
    
    Schedule::create([
        'enrollment_id' => $otherEnrollment->id,
        'student_id' => $otherStudent->id,
        'teacher_id' => $this->teacher->id, // SAME TEACHER
        'course_id' => $otherCourse->id,
        'starts_at' => $conflictDate->copy()->addHours(12),
        'ends_at' => $conflictDate->copy()->addHours(13),
        'status' => 'scheduled',
    ]);

    // Our student tries to change pattern to Thursday at 12:00 with the same teacher
    $data = [
        'teacher_id' => $this->teacher->id,
        'durations' => [
            'Monday' => [60],
            'Wednesday' => [60],
        ],
        'days' => ['Saturday', 'Thursday'],
        'schedule_times' => [
            'Saturday' => ['12:00', '18:00'],
            'Thursday' => ['12:00'],
        ],
    ];

    $scheduleCountBefore = Schedule::count();

    try {
    $service->updateSchedulePattern($this->enrollment, $data, Carbon::create(2026, 8, 1));
        $this->fail('Expected exception was not thrown');
    } catch (\Exception $e) {
        expect($e->getMessage())->toContain('Teacher conflict');
    }

    // Assert transaction rolled back (no new schedules, old ones remain)
    expect(Schedule::count())->toBe($scheduleCountBefore);
    $this->enrollment->refresh();
    expect($this->enrollment->getSchedulePattern())->toHaveKey('Saturday');
    expect($this->enrollment->getSchedulePattern())->toHaveKey('Tuesday');
});

test('updateSchedulePattern keeps edited days active by default', function () {
    $service = app(ScheduleService::class);

    $this->enrollment->update([
        'schedule_pattern' => [
            'Monday' => [
                'active' => false,
                'slots' => [['time' => '10:00', 'duration' => 60]],
            ],
            'Wednesday' => [
                'active' => false,
                'slots' => [['time' => '10:00', 'duration' => 60]],
            ],
        ],
    ]);

    $data = [
        'teacher_id' => $this->teacher->id,
        'durations' => [
            'Monday' => [60],
            'Wednesday' => [60],
        ],
        'days' => ['Monday', 'Wednesday'],
        'schedule_times' => [
            'Monday' => ['10:00'],
            'Wednesday' => ['10:00'],
        ],
    ];

    $result = $service->updateSchedulePattern($this->enrollment, $data, Carbon::create(2026, 8, 1));

    expect($result['success'])->toBeTrue();

    $this->enrollment->refresh();
    $pattern = $this->enrollment->getSchedulePattern();

    expect($pattern['Monday']['active'])->toBeTrue();
    expect($pattern['Wednesday']['active'])->toBeTrue();
});

test('edit pattern page shows all days to allow adding new ones', function () {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
    $admin = User::factory()->create(['role' => 'Admin']);
    $admin->assignRole('Admin');
    Route::get('/profile-fallback', fn () => 'ok')->name('admin.profile.show');
    Route::get('/profile-fallback-edit', fn () => 'ok')->name('admin.profile.edit');

    $this->enrollment->update([
        'schedule_pattern' => [
            'Monday' => [
                'active' => true,
                'slots' => [['time' => '08:00', 'duration' => 60]],
            ],
            'Wednesday' => [
                'active' => false,
                'slots' => [['time' => '10:30', 'duration' => 45]],
            ],
        ],
    ]);

    $this->actingAs($admin);

    $response = $this->get(route('admin.schedules.edit-pattern', $this->enrollment->id));

    $response->assertStatus(200);
    $response->assertSee('Monday');
    $response->assertSee('Wednesday');
    $response->assertSee('08:00');
    $response->assertSee('10:30');
    
    // It should see other days because we want the admin to be able to add them
    $response->assertSee('Tuesday');
    $response->assertSee('Thursday');
});

test('editing pattern from the selected date updates only sessions from that date onward', function () {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
    $admin = User::factory()->create(['role' => 'Admin']);
    $admin->assignRole('Admin');

    Carbon::setTestNow(Carbon::create(2026, 9, 5, 12, 0, 0));

    $teacher = User::factory()->create(['role' => 'Teacher', 'name' => 'Ms. Samar Ali Al-Shamy']);
    $student = User::factory()->create(['role' => 'Student', 'name' => 'Test Student']);
    $course = Course::create([
        'title' => 'Quran Pattern Course',
        'description' => 'Test',
        'level' => 'Beginner',
        'age_group' => 'Kids',
        'language' => 'English'
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'start_date' => Carbon::create(2026, 8, 1),
        'status' => 'active',
        'days_per_week' => 2,
        'session_duration' => 90,
        'currency' => 'USD',
        'admin_price' => 10,
        'schedule_pattern' => [
            'Wednesday' => ['active' => true, 'slots' => [['time' => '02:30', 'duration' => 90]]],
            'Friday' => ['active' => true, 'slots' => [['time' => '02:30', 'duration' => 90]]],
        ],
    ]);

    collect([
        '2026-08-28 02:30:00',
        '2026-09-02 02:30:00',
        '2026-09-04 02:30:00',
        '2026-09-09 02:30:00',
        '2026-09-11 02:30:00',
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

    $this->actingAs($admin);

    $response = $this->put(route('admin.schedules.update-pattern', $enrollment->id), [
        'apply_from_date' => '2026-09-01',
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
    ]);

    $response->assertRedirect(route('admin.schedules.index', ['view' => 'list']));
    $response->assertSessionHas('success');

    $aug28 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-08-28')->first();
    $sep2 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-09-02')->first();
    $sep4 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-09-04')->first();
    $sep9 = Schedule::where('enrollment_id', $enrollment->id)->whereDate('starts_at', '2026-09-09')->first();

    expect($aug28->getDurationInMinutes())->toBe(90);
    expect($sep2->getDurationInMinutes())->toBe(30);
    expect($sep4->getDurationInMinutes())->toBe(30);
    expect($sep9->getDurationInMinutes())->toBe(30);

    Carbon::setTestNow();
});

test('updateSchedulePattern ignores days that are not checked in the form', function () {
    $service = app(ScheduleService::class);
    
    // Simulating a form submission where ALL days have schedule_times (because hidden inputs are submitted)
    // BUT only Monday and Wednesday are checked (day_active = 1)
    $data = [
        'teacher_id' => $this->teacher->id,
        'day_active' => [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 0,
            'Wednesday' => 1,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
        ],
        'durations' => [
            'Sunday' => [60],
            'Monday' => [60],
            'Tuesday' => [60],
            'Wednesday' => [45],
            'Thursday' => [60],
            'Friday' => [60],
            'Saturday' => [60],
        ],
        'schedule_times' => [
            'Sunday' => ['12:00'], // Hidden input value
            'Monday' => ['08:00'], // Checked day
            'Tuesday' => ['12:00'], // Hidden input value
            'Wednesday' => ['10:30'], // Checked day
            'Thursday' => ['12:00'], // Hidden input value
            'Friday' => ['12:00'], // Hidden input value
            'Saturday' => ['12:00'], // Hidden input value
        ],
    ];

    $result = $service->updateSchedulePattern($this->enrollment, $data, Carbon::create(2026, 8, 1));

    expect($result['success'])->toBeTrue();

    $this->enrollment->refresh();
    $pattern = $this->enrollment->getSchedulePattern();

    // Only Monday and Wednesday should exist in the pattern
    expect(array_keys($pattern))->toEqualCanonicalizing(['Monday', 'Wednesday']);
    
    // The other days should NOT exist at all (they should not have times scheduled)
    expect(isset($pattern['Sunday']))->toBeFalse();
    expect(isset($pattern['Tuesday']))->toBeFalse();
    expect(isset($pattern['Thursday']))->toBeFalse();
});
