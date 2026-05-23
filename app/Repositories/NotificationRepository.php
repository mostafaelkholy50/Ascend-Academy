<?php

namespace App\Repositories;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NotificationRepository
{
    public function getPaginatedNotifications(Authenticatable $user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()->paginate($perPage);
    }

    public function getUnreadNotifications(Authenticatable $user, int $limit = 5): Collection
    {
        return $user->unreadNotifications()->take($limit)->get();
    }

    public function getUnreadCount(Authenticatable $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function findNotification(Authenticatable $user, string $id)
    {
        return $user->notifications()->findOrFail($id);
    }
}
