<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Carbon\Carbon;

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
        'duration_minutes' => 60,
        'days' => ['Saturday', 'Thursday'],
        'schedule_times' => [
            'Saturday' => '12:00',
            'Thursday' => '12:00',
        ],
    ];

    $result = $service->updateSchedulePattern($this->enrollment, $data);

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toContain('Pattern updated successfully');

    $this->enrollment->refresh();
    expect($this->enrollment->days_per_week)->toBe(2);
    expect($this->enrollment->schedule_pattern)->toHaveKey('Saturday', '12:00');
    expect($this->enrollment->schedule_pattern)->toHaveKey('Thursday', '12:00');

    // Past schedule should still exist
    $pastSchedules = Schedule::where('enrollment_id', $this->enrollment->id)
        ->where('starts_at', '<', now())
        ->get();
    expect($pastSchedules->count())->toBe(1);

    // Old upcoming schedules should be gone (at 10:00)
    $oldUpcomingSchedules = Schedule::where('enrollment_id', $this->enrollment->id)
        ->where('starts_at', '>', now())
        ->whereTime('starts_at', '10:00:00')
        ->count();
    expect($oldUpcomingSchedules)->toBe(0);

    // New upcoming schedules should be created
    $newUpcomingSchedules = Schedule::where('enrollment_id', $this->enrollment->id)
        ->where('starts_at', '>', now())
        ->whereTime('starts_at', '12:00:00')
        ->get();
    expect($newUpcomingSchedules->count())->toBeGreaterThan(0);
});

test('updateSchedulePattern rolls back on conflict', function () {
    $service = app(ScheduleService::class);
    
    // Create another student with an upcoming schedule on Thursday at 12:00 with the SAME teacher
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

    // Find the next Thursday
    $nextThursday = Carbon::parse('next Thursday')->startOfDay();
    
    Schedule::create([
        'enrollment_id' => $otherEnrollment->id,
        'student_id' => $otherStudent->id,
        'teacher_id' => $this->teacher->id, // SAME TEACHER
        'course_id' => $otherCourse->id,
        'starts_at' => $nextThursday->copy()->addHours(12),
        'ends_at' => $nextThursday->copy()->addHours(13),
        'status' => 'scheduled',
    ]);

    // Our student tries to change pattern to Thursday at 12:00 with the same teacher
    $data = [
        'teacher_id' => $this->teacher->id,
        'duration_minutes' => 60,
        'days' => ['Saturday', 'Thursday'],
        'schedule_times' => [
            'Saturday' => '12:00',
            'Thursday' => '12:00',
        ],
    ];

    $scheduleCountBefore = Schedule::count();

    try {
        $service->updateSchedulePattern($this->enrollment, $data);
        $this->fail('Expected exception was not thrown');
    } catch (\Exception $e) {
        expect($e->getMessage())->toContain("Teacher conflict on {$nextThursday->format('l, M d, Y')} at 12:00 PM (Teacher John Doe is booked with Student Alice for Arabic)");
    }

    // Assert transaction rolled back (no new schedules, old ones remain)
    expect(Schedule::count())->toBe($scheduleCountBefore);
    $this->enrollment->refresh();
    expect($this->enrollment->schedule_pattern)->toHaveKey('Tuesday', '10:00'); // old pattern remains
});
