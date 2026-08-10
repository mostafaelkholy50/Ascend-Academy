<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$studentId = 114;
$enrollments = App\Models\Enrollment::with('course')->where('student_id', $studentId)->get()->map(function($e) {
    return [
        'id' => $e->id,
        'course' => $e->course->title ?? 'N/A',
        'course_id' => $e->course_id,
        'status' => $e->status
    ];
});

echo json_encode($enrollments->toArray(), JSON_PRETTY_PRINT);
