<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$s = App\Models\Schedule::find(707);
echo "Raw from DB: " . $s->getRawOriginal('starts_at') . "\n";
echo "Carbon format: " . $s->starts_at->format('Y-m-d H:i:s') . "\n";

$starts = App\Models\Schedule::where('id', 707)->pluck('starts_at');
echo "Pluck returns type: " . gettype($starts[0]) . "\n";
if (is_object($starts[0])) {
    echo "Pluck class: " . get_class($starts[0]) . "\n";
    echo "Pluck formatted: " . $starts[0]->format('Y-m-d H:i:s') . "\n";
} else {
    echo "Pluck value: " . $starts[0] . "\n";
    $parsed = \Carbon\Carbon::parse($starts[0]);
    echo "Parsed from pluck: " . $parsed->format('Y-m-d H:i:s') . " (Timezone: " . $parsed->timezone->getName() . ")\n";
}

