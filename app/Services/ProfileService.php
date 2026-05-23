<?php

namespace App\Services;

use App\Repositories\ProfileRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    protected $repository;

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getProfileData(User $user)
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'member_since' => $user->created_at->format('F Y'),
        ];

        return compact('user', 'stats');
    }

    public function updateProfile(User $user, array $data)
    {
        return $this->repository->update($user, $data);
    }

    public function updatePassword(User $user, string $newPassword)
    {
        return $this->repository->update($user, [
            'password' => Hash::make($newPassword),
        ]);
    }

    public function updateAvatar(User $user, $avatar)
    {
        $oldAvatar = $user->avatar;
        $path = $avatar->store('avatars', 'public');
        
        $updated = $this->repository->update($user, ['avatar' => $path]);
        
        if ($updated && $oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }
        
        return $updated;
    }

    public function deleteAvatar(User $user)
    {
        $oldAvatar = $user->avatar;

        $updated = $this->repository->update($user, ['avatar' => null]);

        if ($updated && $oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return $updated;
    }
}
