<?php

use App\Models\Schedule;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Carbon\Carbon;


use App\Services\ScheduleService;

beforeEach(function () {
    $this->teacher = User::factory()->create(['role' => 'Teacher', 'name' => 'John Doe']);
    $this->student = User::factory()->create(['role' => 'Student', 'name' => 'Jane Smith']);
    $this->course = Course::create(['title' => 'Quran Basics', 'description' => 'Test', 'level' => 'Beginner', 'age_group' => 'Kids', 'language' => 'English']);
    
    $this->enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'course_id' => $this->course->id,
        'start_date' => Carbon::parse('2026-02-01'),
        'status' => 'active',
        'days_per_week' => 1,
        'session_duration' => 60,
        'currency' => 'USD',
        'admin_price' => 10,
    ]);

    // Create an existing schedule on Sunday, Feb 01, 2026 at 8:30 PM
    $startsAt = Carbon::parse('2026-02-01 20:30:00');
    $this->existingSchedule = Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'course_id' => $this->course->id,
        'teacher_id' => $this->teacher->id,
        'student_id' => $this->student->id,
        'starts_at' => $startsAt,
        'ends_at' => $startsAt->copy()->addMinutes(60),
        'status' => 'scheduled',
    ]);
});

test('storeSchedule rolls back and throws exception with names on conflict', function () {
    $service = app(ScheduleService::class);
    
    $student2 = User::factory()->create(['role' => 'Student', 'name' => 'Alice']);

    $data = [
        'student_id' => $student2->id,
        'course_id' => $this->course->id,
        'teacher_id' => $this->teacher->id,
        'start_date' => '2026-02-01',
        'durations' => [
            'Sunday' => [60, 60],
        ],
        'days' => ['Sunday'],
        'schedule_times' => [
            'Sunday' => ['20:30', '21:45'],
        ],
    ];

    $scheduleCountBefore = Schedule::count();

    try {
        $service->storeSchedule($data);
        $this->fail('Expected exception was not thrown');
    } catch (\Exception $e) {
        expect($e->getMessage())->toContain('Teacher conflict on Sunday, Feb 01, 2026 at 8:30 PM (Teacher John Doe is booked with Student Jane Smith for Quran Basics)');
    }

    // Assert transaction rolled back (no new schedules created)
    expect(Schedule::count())->toBe($scheduleCountBefore);
});

test('storeSchedule rolls back and throws exception for student conflict', function () {
    $service = app(ScheduleService::class);
    
    $course2 = Course::create(['title' => 'Arabic Basics', 'description' => 'Test', 'level' => 'Beginner', 'age_group' => 'Kids', 'language' => 'English']);
    $teacher2 = User::factory()->create(['role' => 'Teacher', 'name' => 'Sara']);

    $data = [
        // Same student (Jane Smith) who is already booked at 8:30 PM for Quran Basics with John Doe
        'student_id' => $this->student->id, 
        'course_id' => $course2->id,
        'teacher_id' => $teacher2->id,
        'start_date' => '2026-02-01',
        'durations' => [
            'Sunday' => [60, 60],
        ],
        'days' => ['Sunday'],
        'schedule_times' => [
            'Sunday' => ['20:30'],
        ],
    ];

    $scheduleCountBefore = Schedule::count();

    try {
        $service->storeSchedule($data);
        $this->fail('Expected exception was not thrown');
    } catch (\Exception $e) {
        expect($e->getMessage())->toContain('Student conflict on Sunday, Feb 01, 2026 at 8:30 PM (Student Jane Smith is booked with Teacher John Doe for Quran Basics)');
    }

    // Assert transaction rolled back
    expect(Schedule::count())->toBe($scheduleCountBefore);
});

test('generateMonthlySchedules rolls back and returns error on conflict', function () {
    $service = app(ScheduleService::class);
    
    $scheduleCountBefore = Schedule::count();

    // The method generates for the given month, Feb 2026.
    // It will find the existing schedule and conflict when creating for other Sundays or the same.
    // Wait, generateMonthlySchedules skips existing ones: `if ($exists) { continue; }`.
    // We will create a conflict with a DIFFERENT student/enrollment, so it doesn't skip it as "existing for same enrollment".
    
    $student2 = User::factory()->create(['role' => 'Student', 'name' => 'Alice']);
    $enrollment2 = Enrollment::create([
        'student_id' => $student2->id,
        'course_id' => $this->course->id,
        'start_date' => Carbon::parse('2026-02-01'),
        'status' => 'active',
        'days_per_week' => 1,
        'session_duration' => 60,
        'currency' => 'USD',
        'admin_price' => 10,
    ]);
    
    // enrollment2 wants to book Sunday 8:30 PM with the same teacher
    // this should conflict with existingSchedule (student1, teacher1)
    
    // Fake the pattern for enrollment2
    $enrollment2->days_per_week = 1;
    $enrollment2->session_duration = 60;
    $enrollment2->save();
    $enrollment2->setSchedulePattern(['Sunday' => '20:30']);

    $result = $service->generateMonthlySchedules($enrollment2, '2026-02', $this->teacher->id);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('Cannot create schedule due to conflicts:');
    expect($result['message'])->toContain('Teacher conflict on Sunday, Feb 01, 2026 at 8:30 PM (Teacher John Doe is booked with Student Jane Smith for Quran Basics)');

    // Assert transaction rolled back (no new schedules created for student2)
    expect(Schedule::where('student_id', $student2->id)->count())->toBe(0);
    expect(Schedule::count())->toBe($scheduleCountBefore);
});
