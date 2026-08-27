<?php

namespace App\Console\Commands;

use App\Models\EnrollmentPayment;
use App\Notifications\MonthlyPaymentReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendMonthlyPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send monthly payment reminder emails to students and parents with unpaid enrollments';

    /**
     * Keep the command gentle on shared hosting mail limits.
     */
    private const MAX_PAYMENTS_PER_RUN = 15;
    private const MAX_EMAILS_PER_RUN = 30;
    private const LOCK_TTL_MINUTES = 55;

    /**
     * Spread queued emails apart so the SMTP server never sees a burst.
     * 300s = 5 minutes between messages; 30 emails ≈ 2.5 hours.
     */
    private const EMAIL_STAGGER_SECONDS = 300;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending monthly payment reminders...');

        $lockKey = 'cron_lock:payment_send_reminders';
        if (!Cache::add($lockKey, true, now()->addMinutes(self::LOCK_TTL_MINUTES))) {
            $this->warn('Skipping: another payment reminder run is still active.');
            return Command::SUCCESS;
        }

        try {
            // Get current month
            $currentMonth = Carbon::now()->startOfMonth();

            // Get all unpaid enrollment payments for the current month
            $unpaidPayments = EnrollmentPayment::with(['enrollment.student', 'enrollment.student.parents'])
                ->where('payment_status', 'unpaid')
                ->whereYear('month', $currentMonth->year)
                ->whereMonth('month', $currentMonth->month)
                ->orderBy('id')
                ->limit(self::MAX_PAYMENTS_PER_RUN)
                ->get();

            $sentCount = 0;
            $skippedCount = 0;
            $emailIndex = 0;

            foreach ($unpaidPayments as $payment) {
                if ($sentCount >= self::MAX_EMAILS_PER_RUN) {
                    $skippedCount++;
                    continue;
                }

                $cacheKey = 'payment_reminder_sent_' . $payment->id . '_' . $currentMonth->format('Y-m');
                if (!Cache::add($cacheKey, true, now()->addDays(30))) {
                    continue;
                }

                try {
                    $student = $payment->enrollment->student;
                    $remainingEmails = self::MAX_EMAILS_PER_RUN - $sentCount;

                    // Send to student
                    if ($remainingEmails > 0) {
                        $student->notify(
                            (new MonthlyPaymentReminderNotification($payment))
                                ->delay(now()->addSeconds(self::EMAIL_STAGGER_SECONDS * $emailIndex++))
                        );
                        $sentCount++;
                        $remainingEmails--;
                    }

                    // Send to parent(s)
                    foreach ($student->parents as $parent) {
                        if ($remainingEmails <= 0) {
                            $skippedCount++;
                            break;
                        }

                        $parent->notify(
                            (new MonthlyPaymentReminderNotification($payment))
                                ->delay(now()->addSeconds(self::EMAIL_STAGGER_SECONDS * $emailIndex++))
                        );
                        $sentCount++;
                        $remainingEmails--;
                    }

                    $this->info("Sent payment reminder to: {$student->name} - {$payment->getFormattedAmount()}");
                } catch (\Exception $e) {
                    Cache::forget($cacheKey);
                    $this->error("Failed to send payment reminder for payment {$payment->id}: " . $e->getMessage());
                }
            }

            $this->info("Successfully sent {$sentCount} payment reminder emails for " . $unpaidPayments->count() . " unpaid enrollments.");

            if ($skippedCount > 0) {
                $this->warn("Skipped {$skippedCount} reminders to stay within the run limit.");
            }

            return Command::SUCCESS;
        } finally {
            Cache::forget($lockKey);
        }
    }
}
