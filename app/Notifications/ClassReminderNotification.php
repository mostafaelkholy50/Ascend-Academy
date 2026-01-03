<?php

namespace App\Notifications;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $schedule;

    /**
     * Create a new notification instance.
     */
    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
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
            ->subject('Class Reminder: ' . $this->schedule->course->title)
            ->markdown('emails.class-reminder', [
                'schedule' => $this->schedule,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'class_reminder',
            'title' => 'Class Reminder',
            'message' => $this->schedule->course->title . ' at ' . $this->schedule->starts_at->format('g:i A'),
            'schedule_id' => $this->schedule->id,
            'course_name' => $this->schedule->course->title,
            'starts_at' => $this->schedule->starts_at->toISOString(),
            'zoom_link' => $this->schedule->zoom_link,
            'url' => $notifiable->role === 'Teacher' ? route('teacher.schedules.index') : route('student.schedule.weekly'),
        ];
    }
}
