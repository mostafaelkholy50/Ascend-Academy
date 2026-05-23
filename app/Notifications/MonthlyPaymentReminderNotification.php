<?php

namespace App\Notifications;

use App\Models\EnrollmentPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonthlyPaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(EnrollmentPayment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Reminder: ' . $this->payment->getMonthName())
            ->markdown('emails.payment-reminder', [
                'payment' => $this->payment,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_reminder',
            'title' => 'Payment Reminder',
            'message' => 'Payment due for ' . $this->payment->getMonthName() . ': ' . $this->payment->getFormattedAmount(),
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'month' => $this->payment->month->format('Y-m'),
            'url' => route('student.dashboard', [], false),
        ];
    }
}
