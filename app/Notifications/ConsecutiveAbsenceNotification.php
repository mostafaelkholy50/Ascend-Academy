<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsecutiveAbsenceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $student;
    public $absenceCount;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $student, int $absenceCount)
    {
        $this->student = $student;
        $this->absenceCount = $absenceCount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
            ->subject('Student Consecutive Absences Alert')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is an automated alert to notify you about a student\'s consecutive absences.')
            ->line('Student: **' . $this->student->name . '**')
            ->line('Consecutive Absences: **' . $this->absenceCount . '** classes.')
            ->action('View Student Profile', route('admin.students.show', $this->student->id))
            ->line('Please follow up with the student or their parents to check on the situation.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Consecutive Absences Alert',
            'message' => 'Student ' . $this->student->name . ' has been absent for ' . $this->absenceCount . ' consecutive classes.',
            'student_id' => $this->student->id,
            'absence_count' => $this->absenceCount,
            'type' => 'absence_alert',
        ];
    }
}
