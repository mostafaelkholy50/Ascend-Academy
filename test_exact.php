<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$enrollment = App\Models\Enrollment::find(45);
$monthStart = \Carbon\Carbon::parse('2026-08');

$existingStarts = App\Models\Schedule::where('enrollment_id', $enrollment->id)
    ->whereYear('starts_at', $monthStart->year)
    ->whereMonth('starts_at', $monthStart->month)
    ->pluck('starts_at')
    ->map(function ($date) {
        return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
    })
    ->flip();

echo "Existing Starts Count: " . $existingStarts->count() . "\n";
echo "Has 16:00? " . (isset($existingStarts['2026-08-03 16:00:00']) ? 'Yes' : 'No') . "\n";

$startsAt = \Carbon\Carbon::parse('2026-08-03 16:00:00');
echo "Isset check: " . (isset($existingStarts[$startsAt->format('Y-m-d H:i:s')]) ? 'Yes' : 'No') . "\n";

