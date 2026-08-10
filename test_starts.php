<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$enrollment = App\Models\Enrollment::whereHas('student', function($q) { 
    $q->where('name', 'like', '%Cheyanne%'); 
})->first();

if (!$enrollment) {
    echo "No enrollment found\n";
    exit;
}

$starts = App\Models\Schedule::where('enrollment_id', $enrollment->id)
    ->whereYear('starts_at', 2026)
    ->whereMonth('starts_at', 8)
    ->pluck('starts_at')
    ->map(function ($date) {
        return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
    })
    ->flip()
    ->toArray();

echo json_encode($starts, JSON_PRETTY_PRINT);
