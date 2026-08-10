<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Schedule;
use Carbon\Carbon;

$teacherId = 128;
$studentId = 114;
$courseId = 31; // General English for Kids

$startsAt = Carbon::parse('2026-08-10 16:30:00');
$endsAt = Carbon::parse('2026-08-10 17:00:00');

$conflict = Schedule::getTeacherConflict($teacherId, $startsAt, $endsAt, null, $studentId, $courseId);

if ($conflict) {
    echo "QUERY CONFLICT FOUND: Schedule ID {$conflict->id} ({$conflict->course->title})\n";
} else {
    echo "QUERY NO CONFLICT FOUND\n";
}
