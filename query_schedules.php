<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schedules = \App\Models\Schedule::with(['attendance', 'course', 'teacher'])
    ->where('student_id', 78)
    ->orderBy('starts_at', 'desc')
    ->get();

if ($schedules->isEmpty()) {
    echo "No schedules found for student 78.\n";
}

foreach ($schedules as $s) {
    $date = $s->starts_at ? $s->starts_at->format('Y-m-d H:i') : 'N/A';
    $course = $s->course->title ?? 'N/A';
    $teacher = $s->teacher->name ?? 'N/A';
    
    $student_present = 'N/A';
    $teacher_present = 'N/A';
    $remark = '';
    
    if ($s->attendance) {
        $student_present = $s->attendance->student_present ? 'Yes' : 'No';
        $teacher_present = $s->attendance->teacher_present ? 'Yes' : 'No';
        $remark = $s->attendance->remark;
    }
    
    echo "ID: {$s->id} | Date: {$date} | Status: {$s->status} | Course: {$course} | Teacher: {$teacher} | Student Present: {$student_present} | Teacher Present: {$teacher_present} | Remark: {$remark}\n";
}
