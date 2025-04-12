<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define modules
        $modules = [
            'users',
            'roles',
            'products',
            'categories',
            'orders',
            'blogs',
            'vouchers',
            'reports',
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
                'view-orders',
                'edit-orders',
                'view-categories',
                'dashboard-access',
            ])->get();

            $employeeRole->permissions()->attach($employeePermissions->pluck('id')->toArray());
        }
    }
}
