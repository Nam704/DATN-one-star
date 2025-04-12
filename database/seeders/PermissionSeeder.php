<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạm thời tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ trong bảng role_permissions
        DB::table('role_permissions')->truncate();

        // Xóa dữ liệu cũ trong bảng permissions
        DB::table('permissions')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Define modules (cập nhật đầy đủ các module từ routes)
        $modules = [
            'users',
            'roles',
            'products',
            'categories',
            'orders',
            'blogs',
            'vouchers',
            'reports',
            'suppliers',
            'imports',
            'attributes',
            'brands',
            'contacts',
        ];

        // Define actions
        $actions = [
            'view',
            'create',
            'edit',
            'delete'
        ];

        // Create permissions for each module and action
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => "$action-$module",
                    'display_name' => ucfirst($action) . ' ' . ucfirst($module),
                    'description' => 'Can ' . $action . ' ' . $module,
                    'module' => $module,
                ]);
            }
        }

        // Also create some special permissions
        $specialPermissions = [
            [
                'name' => 'dashboard-access',
                'display_name' => 'Access Dashboard',
                'description' => 'Can access admin dashboard',
                'module' => 'dashboard',
            ],
            [
                'name' => 'settings-access',
                'display_name' => 'Access Settings',
                'description' => 'Can access system settings',
                'module' => 'settings',
            ],
        ];

        foreach ($specialPermissions as $permission) {
            Permission::create($permission);
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        $allPermissions = Permission::all();

        if ($adminRole) {
            $adminRole->permissions()->attach($allPermissions->pluck('id')->toArray());
        }

        // Assign limited permissions to employee role
        $employeeRole = Role::where('name', 'employee')->first();

        if ($employeeRole) {
            $employeePermissions = Permission::whereIn('name', [
                'view-products',
                'edit-products',
                'create-products',
                'view-orders',
                'edit-orders',
                'view-categories',
                'view-suppliers',
                'view-imports',
                'view-attributes',
                'view-brands',
                'dashboard-access',
                'view-reports',
            ])->get();

            $employeeRole->permissions()->attach($employeePermissions->pluck('id')->toArray());
        }

        // Assign basic permissions to user role
        $userRole = Role::where('name', 'user')->first();

        if ($userRole) {
            $userPermissions = Permission::whereIn('name', [
                'view-products',
                'view-categories',
                'view-brands',
                'view-blogs',
            ])->get();

            $userRole->permissions()->attach($userPermissions->pluck('id')->toArray());
        }
    }
}
