<?php

namespace App\Console\Commands;

use App\Models\EnrollmentPayment;
use App\Notifications\MonthlyPaymentReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending monthly payment reminders...');

        // Get current month
        $currentMonth = Carbon::now()->startOfMonth();

        // Get all unpaid enrollment payments for the current month
        $unpaidPayments = EnrollmentPayment::with(['enrollment.student', 'enrollment.student.parents'])
            ->where('payment_status', 'unpaid')
            ->whereYear('month', $currentMonth->year)
            ->whereMonth('month', $currentMonth->month)
            ->get();

        $sentCount = 0;

        foreach ($unpaidPayments as $payment) {
            try {
                $student = $payment->enrollment->student;

                // Send to student
                $student->notify(new MonthlyPaymentReminderNotification($payment));
                $sentCount++;

                // Send to parent(s)
                foreach ($student->parents as $parent) {
                    $parent->notify(new MonthlyPaymentReminderNotification($payment));
                    $sentCount++;
                }

                $this->info("Sent payment reminder to: {$student->name} - {$payment->getFormattedAmount()}");
            } catch (\Exception $e) {
                $this->error("Failed to send payment reminder for payment {$payment->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully sent {$sentCount} payment reminder emails for " . $unpaidPayments->count() . " unpaid enrollments.");

        return Command::SUCCESS;
    }
}
