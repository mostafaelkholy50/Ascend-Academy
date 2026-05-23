<?php

namespace App\Services;

use App\Repositories\RolePermissionRepository;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionService
{
    protected $repository;

    public function __construct(RolePermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getIndexData(): array
    {
        $users = $this->repository->getUsersPaginated(20);
        $roles = $this->repository->getAllRolesWithPermissions();
        $permissions = $this->repository->getAllPermissions();
        $countries = ['Canada', 'USA', 'UK', 'Egypt', 'KSA', 'UAE', 'Australia', 'Germany', 'France'];

        return compact('users', 'roles', 'permissions', 'countries');
    }

    public function getManageRolesData(): array
    {
        $roles = $this->repository->getAllRolesWithPermissions();
        $permissions = $this->repository->getAllPermissions();
        $countries = ['Canada', 'USA', 'UK', 'Egypt', 'KSA', 'UAE', 'Australia', 'Germany', 'France'];

        return compact('roles', 'permissions', 'countries');
    }

    public function createRole(string $name): Role
    {
        return $this->repository->createRole($name);
    }

    public function createPermission(string $name)
    {
        return $this->repository->createPermission($name);
    }

    public function updateRolePermissions(Role $role, array $data): bool
    {
        $role->syncPermissions($data['permissions'] ?? []);
        
        return $role->update([
            'allowed_countries' => $data['allowed_countries'] ?? [],
            'can_access_payroll' => $data['can_access_payroll'] ?? false,
        ]);
    }

    public function assignRole(User $user, array $data): bool
    {
        $user->syncRoles($data['roles'] ?? []);
        $user->syncPermissions($data['permissions'] ?? []);
        
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'country' => $data['country'] ?? null,
            'allowed_countries' => $data['allowed_countries'] ?? [],
            'can_access_payroll' => $data['can_access_payroll'] ?? false,
        ];

        if (!empty($data['password'])) {
            $userData['password'] = $data['password']; // Will be hashed by model cast
        }

        // Also update the 'role' column for legacy support if needed
        if (!empty($data['roles'])) {
            $userData['role'] = $data['roles'][0];
        }

        return $user->update($userData);
    }

    public function storeUser(array $data): User
    {
        $user = $this->repository->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['roles'][0] ?? null,
            'phone' => $data['phone'] ?? null,
            'country' => $data['country'] ?? null,
            'allowed_countries' => $data['allowed_countries'] ?? [],
            'active' => true,
        ]);

        $user->syncRoles($data['roles']);

        return $user;
    }

    public function deleteUser(User $user): bool
    {
        if (auth()->id() === $user->id) {
            throw new \Exception("You cannot delete your own account.");
        }

        return $this->repository->deleteUser($user);
    }
}
