<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class TeacherDailyScheduleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $schedules;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $schedules)
    {
        $this->schedules = $schedules;
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
        $teacherName = $notifiable->name ?? 'Teacher';
        $date = $this->schedules->first()?->starts_at?->format('l, F j') ?? 'today';

        return (new MailMessage)
            ->subject("Your schedule for {$date} — {$teacherName}")
            ->markdown('emails.teacher-daily-schedule', [
                'schedules' => $this->schedules,
                'teacher' => $notifiable,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'teacher_daily_schedule',
            'title' => 'Daily Schedule',
            'message' => 'You have ' . $this->schedules->count() . ' classes scheduled for the next 24 hours.',
            'url' => route('teacher.schedule.index', [], false),
        ];
    }
}
