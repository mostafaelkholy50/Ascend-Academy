<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schedules = \App\Models\Schedule::where('teacher_id', 19)->where('starts_at', '>=', '2026-06-01')->take(20)->get();
foreach ($schedules as $s) {
    echo $s->starts_at->toDateTimeString() . ' - ' . $s->status . "\n";
}

$count = \App\Models\Schedule::where('teacher_id', 19)->whereYear('starts_at', 2026)->whereMonth('starts_at', 6)->count();
echo "Count using whereMonth: $count\n";

$targetMonth = \Carbon\Carbon::parse('2026-06', 'Asia/Riyadh');
$startOfMonthApp = $targetMonth->copy()->startOfMonth()->setTimezone(config('app.timezone'));
$endOfMonthApp = $targetMonth->copy()->endOfMonth()->setTimezone(config('app.timezone'));

$countBetween = \App\Models\Schedule::where('teacher_id', 19)
    ->whereBetween('starts_at', [$startOfMonthApp, $endOfMonthApp])
    ->count();
echo "Count using whereBetween App Timezone: $countBetween\n";

$startOfMonthUtc = $targetMonth->copy()->startOfMonth()->setTimezone('UTC');
$endOfMonthUtc = $targetMonth->copy()->endOfMonth()->setTimezone('UTC');
$countBetweenUtc = \App\Models\Schedule::where('teacher_id', 19)
    ->whereBetween('starts_at', [$startOfMonthUtc, $endOfMonthUtc])
    ->count();
echo "Count using whereBetween UTC (current broken logic): $countBetweenUtc\n";

