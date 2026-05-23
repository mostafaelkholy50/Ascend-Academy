<?php

namespace App\Services;

use App\Repositories\StudentProfileRepository;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StudentProfileService
{
    protected $repository;

    public function __construct(StudentProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function updateProfile(User $user, array $data): bool
    {
        return $this->repository->updateProfile($user, $data);
    }

    public function updatePassword(User $user, string $password): bool
    {
        return $this->repository->updatePassword($user, $password);
    }

    public function updateAvatar(User $user, UploadedFile $file): bool
    {
        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $file->store('avatars', 'public');

        return $this->repository->updateAvatar($user, $path);
    }

    public function deleteAvatar(User $user): bool
    {
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        return $this->repository->updateAvatar($user, null);
    }

    public function getProfileData(User $user): array
    {
        $stats = $this->repository->getStats($user);
        return compact('user', 'stats');
    }
}
