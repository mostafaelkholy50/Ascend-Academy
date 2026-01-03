<?php

/**
 * Cron Scheduler File
 * 
 * هذا الملف يتم استدعاؤه من Cron Job في Hostinger
 * 
 * رابط الملف يكون:
 * /home/username/public_html/cron-scheduler.php
 * 
 * أو إذا Laravel في مجلد فرعي:
 * /home/username/public_html/your-folder/cron-scheduler.php
 */

// تعيين مسار المشروع - عدّل هذا حسب مكان مشروعك
define('LARAVEL_START', microtime(true));

// تحميل autoloader
require __DIR__.'/vendor/autoload.php';

// تحميل Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';

// إنشاء Kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// تشغيل الـ schedule
$status = $kernel->call('schedule:run');

// طباعة النتيجة للـ log
echo "Cron executed at: " . date('Y-m-d H:i:s') . "\n";
echo "Status: " . ($status === 0 ? 'Success' : 'Failed') . "\n";

$kernel->terminate(new \Symfony\Component\Console\Input\ArgvInput, $status);

exit($status);