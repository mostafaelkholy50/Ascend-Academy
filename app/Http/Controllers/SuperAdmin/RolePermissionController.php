<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuperAdminUserRequest;
use App\Http\Requests\UpdateRolePermissionsRequest;
use App\Http\Requests\AssignRoleRequest;
use App\Services\RolePermissionService;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Exception;

class RolePermissionController extends Controller
{
    protected $service;

    public function __construct(RolePermissionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $data = $this->service->getIndexData();
            return view('superadmin.index', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل البيانات.');
        }
    }

    public function manageRoles()
    {
        try {
            $data = $this->service->getManageRolesData();
            return view('superadmin.roles', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل الأدوار.');
        }
    }

    public function storeRole(Request $request)
    {
        try {
            $request->validate(['name' => 'required|unique:roles,name']);
            $this->service->createRole($request->name);
            return back()->with('success', 'Role created successfully.');
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء إنشاء الدور.');
        }
    }

    public function updateRolePermissions(UpdateRolePermissionsRequest $request, Role $role)
    {
        try {
            $this->service->updateRolePermissions($role, $request->validated());
            return back()->with('success', 'Permissions and Regional settings updated for role ' . $role->name);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحديث الصلاحيات.');
        }
    }

    public function assignRole(AssignRoleRequest $request, User $user)
    {
        try {
            $this->service->assignRole($user, $request->validated());
            return back()->with('success', 'Authority and Location settings updated for ' . $user->name);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحديث الصلاحيات للمستخدم.');
        }
    }

    public function storeUser(StoreSuperAdminUserRequest $request)
    {
        try {
            $user = $this->service->storeUser($request->validated());
            return back()->with('success', "User account created successfully for {$user->name}");
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء إنشاء الحساب.');
        }
    }

    public function destroyUser(User $user)
    {
        try {
            $this->service->deleteUser($user);
            return back()->with('success', "Account for {$user->name} has been removed.");
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function storePermission(Request $request)
    {
        try {
            $request->validate(['name' => 'required|unique:permissions,name']);
            $this->service->createPermission($request->name);
            return back()->with('success', 'Permission created successfully.');
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء إنشاء الصلاحية.');
        }
    }
}
