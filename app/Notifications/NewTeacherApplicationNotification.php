<?php

namespace App\Notifications;

use App\Models\TeacherApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTeacherApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(TeacherApplication $application)
    {
        $this->application = $application;
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
            ->subject('New Teacher Application: ' . $this->application->name)
            ->markdown('emails.new-teacher-application', [
                'application' => $this->application,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'teacher_application',
            'title' => 'New Teacher Application',
            'message' => 'From: ' . $this->application->name,
            'application_id' => $this->application->id,
            'url' => route('admin.teacher-applications.index', [], false),
        ];
    }
}
