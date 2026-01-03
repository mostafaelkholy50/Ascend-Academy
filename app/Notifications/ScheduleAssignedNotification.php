<?php

namespace App\Notifications;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $schedule;
    protected $isMultiple;
    protected $count;

    /**
     * Create a new notification instance.
     */
    public function __construct(Schedule $schedule, bool $isMultiple = false, int $count = 1)
    {
        $this->schedule = $schedule;
        $this->isMultiple = $isMultiple;
        $this->count = $count;
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
        $subject = $this->isMultiple 
            ? "New Schedules Assigned: {$this->count} Classes"
            : 'New Schedule Assigned: ' . $this->schedule->course->title;

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.schedule-assigned', [
                'schedule' => $this->schedule,
                'isMultiple' => $this->isMultiple,
                'count' => $this->count,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        $message = $this->isMultiple 
            ? "{$this->count} new classes assigned"
            : 'New class: ' . $this->schedule->course->title;

        return [
            'type' => 'schedule_assigned',
            'title' => 'New Schedule Assigned',
            'message' => $message,
            'schedule_id' => $this->schedule->id,
            'course_name' => $this->schedule->course->title,
            'student_name' => $this->schedule->student->name,
            'starts_at' => $this->schedule->starts_at->toISOString(),
            'url' => route('teacher.schedules.index'),
        ];
    }
}
