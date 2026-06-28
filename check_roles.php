<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = 0;
foreach (\App\Models\User::where('role', 'Teacher')->get() as $u) {
    if (!$u->hasRole('Teacher')) {
        $count++;
    }
}
echo "Missing Spatie Teacher roles: " . $count . "\n";
