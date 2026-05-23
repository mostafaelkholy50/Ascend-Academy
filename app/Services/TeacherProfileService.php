<?php

namespace App\Services;

use App\Repositories\TeacherProfileRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class TeacherProfileService
{
    protected $repository;

    public function __construct(TeacherProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getProfileData(User $teacher): array
    {
        $stats = $this->repository->getTeacherStats($teacher->id);
        $stats['member_since'] = $teacher->created_at->format('F Y');

        return [
            'user' => $teacher,
            'stats' => $stats,
        ];
    }

    public function updateProfile(User $teacher, array $data): bool
    {
        return $this->repository->updateUser($teacher, $data);
    }

    public function updatePassword(User $teacher, string $newPassword): bool
    {
        return $this->repository->updateUser($teacher, [
            'password' => Hash::make($newPassword)
        ]);
    }

    public function updateAvatar(User $teacher, UploadedFile $avatar): bool
    {
        if ($teacher->avatar && Storage::disk('public')->exists($teacher->avatar)) {
            Storage::disk('public')->delete($teacher->avatar);
        }

        $path = $avatar->store('avatars', 'public');
        
        return $this->repository->updateUser($teacher, ['avatar' => $path]);
    }

    public function deleteAvatar(User $teacher): bool
    {
        if ($teacher->avatar && Storage::disk('public')->exists($teacher->avatar)) {
            Storage::disk('public')->delete($teacher->avatar);
        }

        return $this->repository->updateUser($teacher, ['avatar' => null]);
    }
}
