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
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;

$lockKey = 'cron_lock:queue_worker';

// Acquire lock for up to 10 minutes to prevent concurrent executions
if (!Cache::add($lockKey, true, 600)) {
    echo "[" . date('Y-m-d H:i:s') . "] Queue worker is already running. Exiting.\n";
    exit(0);
}

try {
    // معالجة جميع المهام المعلقة بالخلفية وإيقاف التشغيل عند انتهائها (أفضل لـ Hostinger)
    $status = $kernel->call('queue:work', [
        '--stop-when-empty' => true, // تشغيل كافة المهام المعلقة ثم التوقف
        '--queue' => 'default',      // اسم الـ queue
        '--tries' => 1,              // بدون إعادة إرسال تلقائية: الرسائل الفاشلة تذهب إلى failed_jobs لمراجعتها (منع تكرار الإيميلات)
        '--max-time' => 50,          // التوقف بأمان قبل دقيقة لتجنب قتله من السيرفر
        '--memory' => 128,           // حد الذاكرة
    ]);

    echo "[" . date('Y-m-d H:i:s') . "] Queue worker executed\n";
    echo "Status: " . ($status === 0 ? 'Success' : 'Failed') . "\n";
} finally {
    Cache::forget($lockKey);
}

$kernel->terminate(new \Symfony\Component\Console\Input\ArgvInput, $status);
exit($status);