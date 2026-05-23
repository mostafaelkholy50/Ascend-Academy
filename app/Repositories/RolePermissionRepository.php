<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RolePermissionRepository
{
    public function getUsersPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return User::with('roles', 'permissions')->paginate($perPage);
    }

    public function getAllRolesWithPermissions(): Collection
    {
        return Role::with('permissions')->get();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    public function createRole(string $name): Role
    {
        return Role::create(['name' => $name]);
    }

    public function createPermission(string $name): Permission
    {
        return Permission::create(['name' => $name]);
    }

    public function updateRole(Role $role, array $data): bool
    {
        return $role->update($data);
    }

    public function updateUser(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }
}
