<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function checkPermission(Request $request)
    {
        $user = Auth::user();
        $roleName = $user->role->name;

        $rolePermissions = $user->role->permissions()->pluck('name')->toArray();

        $allPermissions = Permission::all();
        $allRoles = Role::all();

        // Lấy ra danh sách permission theo module
        $groupedPermissions = Permission::all()->groupBy('module');

        return view('admin.permissions.check', compact(
            'user',
            'roleName',
            'rolePermissions',
            'allPermissions',
            'allRoles',
            'groupedPermissions'
        ));
    }

    public function testAccess(Request $request, $permission)
    {
        $user = Auth::user();

        $hasPermission = $user->hasPermission($permission);

        return response()->json([
            'user' => $user->name,
            'role' => $user->role->name,
            'permission' => $permission,
            'has_permission' => $hasPermission,
            'all_permissions' => $user->role->permissions()->pluck('name')->toArray()
        ]);
    }
}
