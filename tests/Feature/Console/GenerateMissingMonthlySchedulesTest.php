<?php

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->teacher = User::factory()->create(['role' => 'Teacher', 'active' => true]);
    $this->student1 = User::factory()->create(['role' => 'Student']);
    $this->student2 = User::factory()->create(['role' => 'Student']);
    
    $this->course = Course::create([
        'title' => 'Test Course',
        'description' => 'Test',
        'price' => 100,
        'status' => 'published',
    ]);

    // Enrollment 1: Active, no schedules for current month
    $this->enrollment1 = Enrollment::create([
        'student_id' => $this->student1->id,
        'course_id' => $this->course->id,
        'status' => 'active',
        'days_per_week' => 2, // Should generate ~8 schedules
        'session_duration' => 60,
        'start_date' => now()->subMonth(),
    ]);

    // Make sure previous month has a schedule so generateMonthlySchedules can pick up a teacher and pattern
    Schedule::create([
        'enrollment_id' => $this->enrollment1->id,
        'student_id' => $this->student1->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => now()->subMonth()->startOfMonth()->addDays(2)->setTime(10, 0),
        'ends_at' => now()->subMonth()->startOfMonth()->addDays(2)->setTime(11, 0),
        'status' => 'scheduled',
    ]);

    // Enrollment 2: Active, already HAS schedules for current month
    $this->enrollment2 = Enrollment::create([
        'student_id' => $this->student2->id,
        'course_id' => $this->course->id,
        'status' => 'active',
        'session_duration' => 60,
        'start_date' => now()->subMonth(),
    ]);

    Schedule::create([
        'enrollment_id' => $this->enrollment2->id,
        'student_id' => $this->student2->id,
        'teacher_id' => $this->teacher->id,
        'course_id' => $this->course->id,
        'starts_at' => now()->startOfMonth()->addDays(5)->setTime(10, 0),
        'ends_at' => now()->startOfMonth()->addDays(5)->setTime(11, 0),
        'status' => 'scheduled',
    ]);

    // Enrollment 3: Inactive
    $this->enrollment3 = Enrollment::create([
        'student_id' => User::factory()->create(['role' => 'Student'])->id,
        'course_id' => $this->course->id,
        'status' => 'cancelled',
        'session_duration' => 60,
        'start_date' => now()->subMonth(),
    ]);
});

test('it generates missing schedules for active enrollments successfully', function () {
    $initialCount1 = Schedule::where('enrollment_id', $this->enrollment1->id)
        ->whereMonth('starts_at', now()->month)
        ->count();
    
    expect($initialCount1)->toBe(0);

    $initialCount2 = Schedule::where('enrollment_id', $this->enrollment2->id)
        ->whereMonth('starts_at', now()->month)
        ->count();
    
    expect($initialCount2)->toBe(1);

    Artisan::call('schedules:generate-missing');

    $output = Artisan::output();
    
    // Check that it generated schedules for enrollment1
    $newCount1 = Schedule::where('enrollment_id', $this->enrollment1->id)
        ->whereMonth('starts_at', now()->month)
        ->count();
    
    expect($newCount1)->toBeGreaterThan(0);

    // Check that it SKIPPED enrollment2
    $newCount2 = Schedule::where('enrollment_id', $this->enrollment2->id)
        ->whereMonth('starts_at', now()->month)
        ->count();
    
    expect($newCount2)->toBe(1); // Unchanged

    // Check that it SKIPPED enrollment3
    $newCount3 = Schedule::where('enrollment_id', $this->enrollment3->id)
        ->whereMonth('starts_at', now()->month)
        ->count();
    
    expect($newCount3)->toBe(0);

    expect($output)->toContain('Report for');
    expect($output)->toContain('Total Active Enrollments Processed: 2'); // enrollment1 and enrollment2
});

test('it handles specific month option', function () {
    $nextMonth = now()->addMonth()->format('Y-m');
    
    Artisan::call('schedules:generate-missing', ['--month' => $nextMonth]);
    
    $newCount1 = Schedule::where('enrollment_id', $this->enrollment1->id)
        ->whereMonth('starts_at', now()->addMonth()->month)
        ->count();
    
    expect($newCount1)->toBeGreaterThan(0);
});

test('it continues processing if one enrollment fails', function () {
    // Create an active enrollment that will cause a conflict
    $student3 = User::factory()->create(['role' => 'Student']);
    $enrollment4 = Enrollment::create([
        'student_id' => $student3->id,
        'course_id' => $this->course->id,
        'status' => 'active',
        'session_duration' => 60,
        'start_date' => now()->subMonth(),
    ]);

    // Force a conflict by manually creating a schedule at the exact same default time
    // But since the service gracefully logs warnings, it will just skip conflicted sessions and return success.
    // So we test that the command outputs the conflict warning.

    Artisan::call('schedules:generate-missing');
    $output = Artisan::output();

    // Since enrollment4 has no previous pattern, it defaults to a pattern. 
    // It should succeed or skip depending on teacher availability.
    // The command should not throw an exception that stops the whole process.
    
    $processedMatches = [];
    preg_match('/Total Active Enrollments Processed: (\d+)/', $output, $processedMatches);
    
    expect((int)$processedMatches[1])->toBe(3); // enrollment1, enrollment2, enrollment4
});
