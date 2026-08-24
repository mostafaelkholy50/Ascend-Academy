<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(78);
if ($user) {
    echo "User 78: {$user->name} | Role: {$user->role}\n";
} else {
    echo "User 78 not found.\n";
}

$enrollments = \App\Models\Enrollment::where('student_id', 78)->get();
echo "Enrollments for 78: " . $enrollments->count() . "\n";
