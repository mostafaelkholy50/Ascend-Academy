<?php

/**
 * Generate Missing Schedules Cron File
 * 
 * هذا الملف مخصص لتشغيل أمر إنشاء الجداول المفقودة (schedules:generate-missing) مباشرة 
 * من خلال لوحة التحكم Hostinger أو متصفح الويب (إذا تم إعداده لذلك).
 * 
 * يمكنك ضبط Cron Job في استضافة Hostinger كالتالي:
 * /usr/bin/php /home/username/public_html/cron-generate-schedules.php
 */

define('LARAVEL_START', microtime(true));

// تحميل autoloader
require __DIR__.'/vendor/autoload.php';

// تحميل Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';

// إنشاء Kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Starting schedule generation...\n";

// تشغيل أمر الجداول المفقودة
$status = $kernel->call('schedules:generate-missing');

// جلب تفاصيل ما حدث (الـ Output)
$output = Artisan::output();

// طباعة النتيجة
echo "Executed at: " . date('Y-m-d H:i:s') . "\n";
echo "Status: " . ($status === 0 ? 'Success' : 'Failed') . "\n";
echo "---------------------------------\n";
echo $output;
echo "\n---------------------------------\n";

$kernel->terminate(new \Symfony\Component\Console\Input\ArgvInput, $status);

exit($status);
