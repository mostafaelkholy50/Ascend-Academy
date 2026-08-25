<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$student = App\Models\User::where('name', 'like', '%Faiad%')->first();
if (!$student) {
    echo "Student not found\n";
    exit;
}
echo "Student ID: " . $student->id . "\n";

$enrollments = App\Models\Enrollment::where('student_id', $student->id)->get();
echo "Enrollments:\n";
foreach ($enrollments as $en) {
    echo "ID: {$en->id}, Course: {$en->course_id}, Status: {$en->status}, Pattern: " . json_encode($en->getSchedulePattern()) . "\n";
}

$schedules = App\Models\Schedule::where('student_id', $student->id)->get();
echo "Schedules:\n";
foreach ($schedules as $s) {
    echo "ID: {$s->id}, Course: {$s->course_id}, Enrollment: {$s->enrollment_id}, Starts: {$s->starts_at}, Ends: {$s->ends_at}\n";
}
