<?php

namespace App\Notifications;

use App\Models\Resource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $resource;

    /**
     * Create a new notification instance.
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
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
            ->subject('New Learning Resource: ' . $this->resource->title)
            ->markdown('emails.resource-added', [
                'resource' => $this->resource,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'resource_added',
            'title' => 'New Learning Resource',
            'message' => $this->resource->title . ' from ' . $this->resource->teacher->name,
            'resource_id' => $this->resource->id,
            'resource_title' => $this->resource->title,
            'resource_type' => $this->resource->type,
            'teacher_name' => $this->resource->teacher->name,
            'url' => route('student.resources.index', [], false),
        ];
    }
}
