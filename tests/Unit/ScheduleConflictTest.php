<?php

use App\Models\Schedule;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->teacher = User::factory()->create(['role' => 'Teacher']);
    $this->student = User::factory()->create(['role' => 'Student']);
    $this->course = Course::factory()->create();
    
    $this->enrollment = Enrollment::factory()->create([
        'student_id' => $this->student->id,
        'course_id' => $this->course->id,
    ]);

    // Create an existing schedule from 1:30 PM to 2:00 PM
    $this->existingSchedule = Schedule::create([
        'enrollment_id' => $this->enrollment->id,
        'course_id' => $this->course->id,
        'teacher_id' => $this->teacher->id,
        'student_id' => $this->student->id,
        'starts_at' => Carbon::today()->setTime(13, 30),
        'ends_at' => Carbon::today()->setTime(14, 0),
        'status' => 'scheduled',
    ]);
});

test('it allows scheduling exactly when another schedule ends (back-to-back)', function () {
    $newStartsAt = Carbon::today()->setTime(14, 0); // 2:00 PM
    $newEndsAt = Carbon::today()->setTime(14, 30);  // 2:30 PM

    // Teacher conflict
    $teacherConflict = Schedule::hasTeacherConflict($this->teacher->id, $newStartsAt, $newEndsAt);
    expect($teacherConflict)->toBeFalse();

    // Student conflict
    $studentConflict = Schedule::hasStudentConflict($this->student->id, $newStartsAt, $newEndsAt);
    expect($studentConflict)->toBeFalse();
});

test('it allows scheduling exactly before another schedule starts (back-to-back)', function () {
    $newStartsAt = Carbon::today()->setTime(13, 0); // 1:00 PM
    $newEndsAt = Carbon::today()->setTime(13, 30);  // 1:30 PM

    // Teacher conflict
    $teacherConflict = Schedule::hasTeacherConflict($this->teacher->id, $newStartsAt, $newEndsAt);
    expect($teacherConflict)->toBeFalse();

    // Student conflict
    $studentConflict = Schedule::hasStudentConflict($this->student->id, $newStartsAt, $newEndsAt);
    expect($studentConflict)->toBeFalse();
});

test('it detects true overlaps', function () {
    $newStartsAt = Carbon::today()->setTime(13, 45); // 1:45 PM
    $newEndsAt = Carbon::today()->setTime(14, 15);  // 2:15 PM

    // Teacher conflict
    $teacherConflict = Schedule::hasTeacherConflict($this->teacher->id, $newStartsAt, $newEndsAt);
    expect($teacherConflict)->toBeTrue();

    // Student conflict
    $studentConflict = Schedule::hasStudentConflict($this->student->id, $newStartsAt, $newEndsAt);
    expect($studentConflict)->toBeTrue();
});
