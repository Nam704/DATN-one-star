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
            'statistics',
            'banners',
            'products',
            'categories',
            'orders',
            'blogs',
            'vouchers',
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
            // For the statistics module, only create 'view' permission
            if ($module === 'statistics') {
                Permission::create([
                    'name' => "view-$module",
                    'display_name' => 'View Statistics',
                    'description' => 'Can view statistics',
                    'module' => $module,
                ]);
            }else if ($module === 'orders') {
                // chỉ tạo quyền view và edit cho orders
                foreach (['view', 'edit','delete'] as $action) {
                    Permission::create([
                        'name' => "$action-$module",
                        'display_name' => ucfirst($action) . ' ' . ucfirst($module),
                        'description' => 'Can ' . $action . ' ' . $module,
                        'module' => $module,
                    ]);
                }
            } else if ($module === 'contacts') {
                // chỉ tạo quyền view và edit cho liên hệ
                foreach (['view', 'edit','delete'] as $action) {
                    Permission::create([
                        'name' => "$action-$module",
                        'display_name' => ucfirst($action) . ' ' . ucfirst($module),
                        'description' => 'Can ' . $action . ' ' . $module,
                        'module' => $module,
                    ]);
                }
            } 
            else {
                foreach ($actions as $action) {
                    Permission::create([
                        'name' => "$action-$module",
                        'display_name' => ucfirst($action) . ' ' . ucfirst($module),
                        'description' => 'Can ' . $action . ' ' . $module,
                        'module' => $module,
                    ]);
                }
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
                'view-orders',
                'view-categories',
                'view-suppliers',
                'view-imports',
                'view-attributes',
                'view-brands',
                'dashboard-access',
                'view-banners'
            ])->get();

            $employeeRole->permissions()->attach($employeePermissions->pluck('id')->toArray());
        }

    }
}
