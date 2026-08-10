<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$service = app(\App\Services\ScheduleService::class);

// Find the enrollment
$enrollment = App\Models\Enrollment::whereHas('student', function($q) { 
    $q->where('name', 'like', '%Cheyanne%'); 
})->where('course_id', 31)->first();

$data = [
    'student_id' => $enrollment->student_id,
    'course_id' => $enrollment->course_id,
    'teacher_id' => App\Models\User::where('role', 'Teacher')->first()->id,
    'start_date' => '2026-08',
    'days' => ['Monday'],
    'schedule_times' => [
        'Monday' => ['16:30']
    ],
    'durations' => [
        'Monday' => [30]
    ]
];

try {
    $service->storeSchedule($data);
    echo "Success\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
