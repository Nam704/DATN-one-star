<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

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

        // Kiểm tra xem người dùng có role hay không trước
        if ($user && $user->role) {
            // Kiểm tra quyền thông qua role
            $hasPermission = false;

            if ($user->role->name === 'admin') {
                $hasPermission = true; // Admin có tất cả quyền
            } else {
                // Kiểm tra quyền trên role
                $hasPermission = $user->role->permissions->contains('name', $permission);
            }

            return response()->json([
                'user' => $user->name,
                'role' => $user->role->name,
                'permission' => $permission,
                'has_permission' => $hasPermission,
                'all_permissions' => $user->role->permissions()->pluck('name')->toArray()
            ]);
        }

        return response()->json([
            'user' => $user ? $user->name : 'Không xác định',
            'role' => 'Không có vai trò',
            'permission' => $permission,
            'has_permission' => false,
            'all_permissions' => []
        ], 400);
    }

    public function refreshPermissions()
    {
        try {
            // Chạy seeder quyền
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\PermissionSeeder',
                '--force' => true
            ]);

            return redirect()->route('admin.permissions.check')
                ->with('success', 'Dữ liệu quyền đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.permissions.check')
                ->with('error', 'Có lỗi xảy ra khi cập nhật dữ liệu quyền: ' . $e->getMessage());
        }
    }
}
