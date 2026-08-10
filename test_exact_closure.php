<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Schedule;
use Carbon\Carbon;

$teacherId = 128;
$studentId = 114;
$courseId = 31; // General English for Kids

$existingTeacherSchedules = Schedule::with(['teacher', 'course'])
    ->where('teacher_id', $teacherId)
    ->where('status', '!=', 'cancelled')
    ->whereBetween('starts_at', [Carbon::parse('2026-08-01'), Carbon::parse('2026-08-31')->endOfDay()])
    ->get();

$startsAt1 = Carbon::parse('2026-08-10 16:30:00');
$endsAt1 = Carbon::parse('2026-08-10 17:00:00');

foreach ($existingTeacherSchedules as $schedule) {
    $overlap = $schedule->starts_at < $endsAt1 && $schedule->ends_at > $startsAt1;
    $relevant = $schedule->isConflictRelevantFor($startsAt1);
    
    // Exact closure logic:
    $data = [
        'student_id' => $studentId,
        'course_id' => $courseId
    ];
    $ignored = ($schedule->student_id == $data['student_id'] && $schedule->course_id == $data['course_id']);

    if ($overlap) {
        echo "4:30 PM -> Schedule ID {$schedule->id} ({$schedule->course->title}) - Overlap: " . ($overlap ? 'YES' : 'NO') . " - Relevant: " . ($relevant ? 'YES' : 'NO') . " - Ignored: " . ($ignored ? 'YES' : 'NO') . "\n";
    }
}
