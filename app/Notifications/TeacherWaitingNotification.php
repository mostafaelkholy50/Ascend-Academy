<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherWaitingNotification extends Notification
{
    protected $schedule;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $teacherName = $this->schedule->teacher->name;
        $studentName = $this->schedule->student->name;
        $courseName = $this->schedule->course->name;

        return (new MailMessage)
            ->subject('Academy Alert: Teacher is Waiting in Class')
            ->greeting("Hello,")
            ->line("This is an automated alert from Ascend Academy.")
            ->line("Teacher **{$teacherName}** is currently waiting for **{$studentName}** in the class: **{$courseName}**.")
            ->line("The session started at: " . $this->schedule->starts_at->format('g:i A'))
            ->action('Join Class Now', $this->schedule->zoom_link ?? url('/'))
            ->line('Please ensure the student joins as soon as possible to make the most of the session.')
            ->line('Thank you for your cooperation!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'schedule_id' => $this->schedule->id,
            'message' => "Teacher {$this->schedule->teacher->name} is waiting for {$this->schedule->student->name}.",
            'type' => 'teacher_waiting'
        ];
    }
}
