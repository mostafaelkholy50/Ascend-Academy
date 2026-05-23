<?php

namespace App\Services;

use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class NotificationService
{
    protected $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getIndexData(Authenticatable $user): array
    {
        $notifications = $this->repository->getPaginatedNotifications($user);
        
        return compact('notifications');
    }

    public function getUnreadData(Authenticatable $user): array
    {
        return [
            'notifications' => $this->repository->getUnreadNotifications($user),
            'count' => $this->repository->getUnreadCount($user),
        ];
    }

    public function markAsRead(Authenticatable $user, string $id): bool
    {
        $notification = $this->repository->findNotification($user, $id);
        $notification->markAsRead();
        
        return true;
    }

    public function markAllAsRead(Authenticatable $user): bool
    {
        $user->unreadNotifications->markAsRead();
        
        return true;
    }
}
