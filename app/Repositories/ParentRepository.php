<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ParentRepository
{
    public function getParentsQuery(): Builder
    {
        return User::where('role', 'Parent')->with('children');
    }

    public function findOrFail(int $id): User
    {
        return User::where('role', 'Parent')->findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $parent, array $data): bool
    {
        return $parent->update($data);
    }

    public function delete(User $parent): ?bool
    {
        return $parent->delete();
    }
}
