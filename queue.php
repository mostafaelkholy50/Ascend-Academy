<?php
/**
 * Queue Worker for Hostinger
 * 
 * هذا الملف يشغل Queue Worker لمعالجة الإيميلات والإشعارات
 * 
 * Cron Job Setting:
 * * * * * * /usr/bin/php /home/u147363979/domains/ascend-quran-academy.com/public_html/queue-worker.php >> /dev/null 2>&1
 */

define('LARAVEL_START', microtime(true));

// تحميل Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// معالجة job واحد فقط في كل مرة (أفضل لـ Hostinger)
$status = $kernel->call('queue:work', [
    '--once' => true,           // معالجة job واحد فقط
    '--queue' => 'default',     // اسم الـ queue
    '--tries' => 3,             // عدد المحاولات
    '--timeout' => 60,          // timeout بالثواني
    '--memory' => 128,          // حد الذاكرة
]);

echo "[" . date('Y-m-d H:i:s') . "] Queue worker executed\n";
echo "Status: " . ($status === 0 ? 'Success' : 'Failed') . "\n";

$kernel->terminate(new \Symfony\Component\Console\Input\ArgvInput, $status);
exit($status);