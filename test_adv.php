<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Course;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$service = app(ScheduleService::class);
$teacher1 = User::where('role', 'Teacher')->first();
$teacher2 = User::where('role', 'Teacher')->where('id', '!=', $teacher1->id)->first();

if (!$teacher2) {
    $teacher2 = User::create([
        'name' => 'Test Teacher 2',
        'email' => 'teacher2@test.com',
        'password' => bcrypt('password'),
        'role' => 'Teacher'
    ]);
}

echo "Starting advanced tests...\n";

DB::beginTransaction();
try {
    $student1 = User::create(['name' => 'S1', 'email' => 's1@test.com', 'password' => bcrypt('p'), 'role' => 'Student']);
    $student2 = User::create(['name' => 'S2', 'email' => 's2@test.com', 'password' => bcrypt('p'), 'role' => 'Student']);
    $course = Course::create(['title' => 'Test Course', 'description' => 'Test Course']);

    // Setup base enrollment
    $enrollmentA = Enrollment::create([
        'student_id' => $student1->id,
        'course_id' => $course->id,
        'days_per_week' => 1,
        'session_duration' => 60,
        'status' => 'active',
        'start_date' => Carbon::now()->startOfMonth(),
    ]);
    $patternA = ['Monday' => ['active' => true, 'slots' => [['time' => '10:00', 'duration' => 60]]]];
    $enrollmentA->setSchedulePattern($patternA);
    $startsAtA = Carbon::parse('next Monday 10:00');
    Schedule::create([
        'enrollment_id' => $enrollmentA->id,
        'student_id' => $student1->id,
        'teacher_id' => $teacher1->id,
        'course_id' => $course->id,
        'starts_at' => $startsAtA,
        'ends_at' => $startsAtA->copy()->addMinutes(60),
        'status' => 'scheduled'
    ]);

    // 5. Failure Scenario: Student Conflict (Student is busy with another teacher)
    $enrollmentB = Enrollment::create([
        'student_id' => $student1->id, // SAME STUDENT
        'course_id' => Course::create(['title'=>'Course B'])->id,
        'days_per_week' => 1, 'session_duration' => 60, 'status' => 'active',
    ]);
    $dataB = [
        'student_id' => $enrollmentB->student_id, 'course_id' => $enrollmentB->course_id,
        'teacher_id' => $teacher2->id, // DIFFERENT TEACHER
        'start_date' => Carbon::now()->format('Y-m'), 'days' => ['Monday'],
        'schedule_times' => ['Monday' => ['10:30']], 'durations' => ['Monday' => [30]]
    ];
    try {
        $service->storeSchedule($dataB);
        echo "FAIL (5): Expected student conflict, but it succeeded.\n";
    } catch (\Exception $e) {
        echo "PASS (5): Caught Student Conflict -> " . $e->getMessage() . "\n";
    }

    // 6. Failure Scenario: Teacher Conflict (Teacher is busy with another student)
    $enrollmentC = Enrollment::create([
        'student_id' => $student2->id, // DIFFERENT STUDENT
        'course_id' => $course->id,
        'days_per_week' => 1, 'session_duration' => 60, 'status' => 'active',
    ]);
    $dataC = [
        'student_id' => $enrollmentC->student_id, 'course_id' => $enrollmentC->course_id,
        'teacher_id' => $teacher1->id, // SAME TEACHER
        'start_date' => Carbon::now()->format('Y-m'), 'days' => ['Monday'],
        'schedule_times' => ['Monday' => ['10:30']], 'durations' => ['Monday' => [30]]
    ];
    try {
        $service->storeSchedule($dataC);
        echo "FAIL (6): Expected teacher conflict, but it succeeded.\n";
    } catch (\Exception $e) {
        echo "PASS (6): Caught Teacher Conflict -> " . $e->getMessage() . "\n";
    }

    // 7. Success Scenario: generateMonthlySchedules
    try {
        $res = $service->generateMonthlySchedules($enrollmentA, Carbon::now(), $teacher1->id);
        echo "PASS (7): generateMonthlySchedules succeeded -> " . $res['message'] . "\n";
    } catch (\Exception $e) {
        echo "FAIL (7): generateMonthlySchedules threw exception -> " . $e->getMessage() . "\n";
    }

    // 8. Success Scenario: Back-to-back classes do NOT conflict
    $dataD = [
        'student_id' => $enrollmentC->student_id, 'course_id' => $enrollmentC->course_id,
        'teacher_id' => $teacher1->id,
        'start_date' => Carbon::now()->format('Y-m'), 'days' => ['Monday'],
        'schedule_times' => ['Monday' => ['11:00']], // 11:00 starts exactly when 10:00-11:00 ends
        'durations' => ['Monday' => [30]]
    ];
    try {
        $service->storeSchedule($dataD);
        echo "PASS (8): Back-to-back classes booked successfully.\n";
    } catch (\Exception $e) {
        echo "FAIL (8): Unexpected conflict for back-to-back -> " . $e->getMessage() . "\n";
    }

    // 9. Failure Scenario: updateSchedulePattern conflict
    $patternUpdateData = [
        'teacher_id' => $teacher1->id, // Change teacher
        'days' => ['Monday'],
        'schedule_times' => ['Monday' => ['11:00']], // 11:00 conflicts with the class we just created in step 8
        'durations' => ['Monday' => [60]],
        'day_active' => ['Monday' => 1]
    ];
    try {
        // We update enrollment A to 11:00 with teacher 1. Teacher 1 is already booked at 11:00 with student 2.
        $res = $service->updateSchedulePattern($enrollmentA, $patternUpdateData, Carbon::now());
        if ($res['success']) {
            echo "FAIL (9): updateSchedulePattern succeeded but it should have conflicted.\n";
        } else {
            echo "FAIL (9): Expected Exception, got array return.\n";
        }
    } catch (\Exception $e) {
        echo "PASS (9): updateSchedulePattern blocked by conflict -> " . $e->getMessage() . "\n";
    }

} catch (\Exception $e) {
    echo "ERROR during setup: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "Tests finished, DB rolled back.\n";
}
