<?php

namespace App\Notifications;

use App\Models\StudentEvaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentEvaluationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $evaluation;

    /**
     * Create a new notification instance.
     */
    public function __construct(StudentEvaluation $evaluation)
    {
        $this->evaluation = $evaluation;
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
        $studentName = $this->evaluation->student->name;
        
        return (new MailMessage)
            ->subject('New Monthly Evaluation for ' . $studentName)
            ->markdown('emails.student-evaluation', [
                'evaluation' => $this->evaluation,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        $studentName = $this->evaluation->student->name;
        
        return [
            'type' => 'student_evaluation',
            'title' => 'New Monthly Evaluation',
            'message' => 'New evaluation for ' . $studentName . ' from ' . $this->evaluation->teacher->name,
            'evaluation_id' => $this->evaluation->id,
            'student_name' => $studentName,
            'teacher_name' => $this->evaluation->teacher->name,
            'url' => route('parent.children.evaluations', ['child' => $this->evaluation->student_id], false),
        ];
    }
}
