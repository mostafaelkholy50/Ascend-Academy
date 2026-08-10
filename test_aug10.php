<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$studentId = 114;
$schedules = App\Models\Schedule::with(['course'])->where('student_id', $studentId)
    ->whereDate('starts_at', '2026-08-10')
    ->get()
    ->map(function($s) {
        return [
            'course' => $s->course->title ?? 'N/A',
            'start' => $s->starts_at->format('H:i'),
            'end' => $s->ends_at->format('H:i'),
        ];
    });

echo json_encode($schedules->toArray(), JSON_PRETTY_PRINT);
