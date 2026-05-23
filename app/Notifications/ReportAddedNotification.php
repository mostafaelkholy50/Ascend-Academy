<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;

    /**
     * Create a new notification instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
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
        $courseName = $this->report->course ? $this->report->course->title : 'General';
        
        return (new MailMessage)
            ->subject('New Progress Report: ' . $courseName)
            ->markdown('emails.report-added', [
                'report' => $this->report,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        $courseName = $this->report->course ? $this->report->course->title : 'General';
        
        $url = method_exists($notifiable, 'isParent') && $notifiable->isParent() 
            ? route('parent.children.evaluations', ['child' => $this->report->student_id], false)
            : route('student.reports.index', [], false);
        
        return [
            'type' => 'report_added',
            'title' => 'New Progress Report',
            'message' => 'New report for ' . $courseName . ' from ' . $this->report->teacher->name,
            'report_id' => $this->report->id,
            'course_name' => $courseName,
            'teacher_name' => $this->report->teacher->name,
            'url' => $url,
        ];
    }
}
