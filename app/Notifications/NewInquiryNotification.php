<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInquiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $inquiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
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
        $typeLabels = [
            'trial' => 'Free Trial Request',
            'contact' => 'Contact Message',
            'registration' => 'Registration Form',
        ];

        $typeLabel = $typeLabels[$this->inquiry->type] ?? 'Registration';

        return (new MailMessage)
            ->subject('New Registration: ' . $typeLabel)
            ->markdown('emails.new-inquiry', [
                'inquiry' => $this->inquiry,
                'typeLabel' => $typeLabel,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        $typeLabels = [
            'trial' => 'Free Trial Request',
            'contact' => 'Contact Inquiry',
            'registration' => 'Registration Inquiry',
        ];

        $typeLabel = $typeLabels[$this->inquiry->type] ?? 'New Inquiry';

        return [
            'type' => 'inquiry',
            'title' => 'New Inquiry: ' . $typeLabel,
            'message' => 'From: ' . $this->inquiry->name,
            'inquiry_id' => $this->inquiry->id,
            'inquiry_type' => $this->inquiry->type,
            'url' => route('admin.inquiries.index'),
        ];
    }
}
