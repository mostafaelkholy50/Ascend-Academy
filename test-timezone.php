<?php

/**
 * Quick Timezone Testing Script
 * 
 * This script helps you test timezone conversions without logging in
 * Run with: php test-timezone.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Timezone Conversion Test ===\n\n";

// Get a schedule to test
$schedule = \App\Models\Schedule::first();

if (!$schedule) {
    echo "❌ No schedules found in database. Please create a schedule first.\n";
    exit(1);
}

echo "Testing Schedule ID: {$schedule->id}\n";
echo "Course: {$schedule->course->title}\n";
echo "Original stored time: {$schedule->starts_at->format('Y-m-d H:i:s')}\n\n";

echo "=== Timezone Conversions ===\n\n";

// Test different timezones
$timezones = [
    'Africa/Cairo' => 'Egypt (Teacher/Admin View)',
    'America/New_York' => 'New York (EST/EDT)',
    'America/Los_Angeles' => 'Los Angeles (PST/PDT)',
    'Europe/London' => 'London (GMT/BST)',
    'Asia/Dubai' => 'Dubai (GST)',
    'Asia/Tokyo' => 'Tokyo (JST)',
];

foreach ($timezones as $tz => $label) {
    $converted = $schedule->getStartsAtInTimezone($tz);
    $offset = $converted->format('P');

    echo "📍 {$label}:\n";
    echo "   Time: {$converted->format('g:i A')} ({$converted->format('H:i')})\n";
    echo "   Full: {$converted->format('Y-m-d H:i:s T')}\n";
    echo "   Offset: UTC{$offset}\n\n";
}

echo "=== How to Set User Timezone ===\n\n";
echo "Run this SQL to test with a specific user:\n";
echo "UPDATE users SET timezone = 'America/New_York' WHERE email = 'your-student@example.com';\n\n";

echo "✅ Test complete!\n";
