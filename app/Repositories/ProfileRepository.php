<?php

namespace App\Repositories;

use App\Models\User;

class ProfileRepository
{
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }
}
