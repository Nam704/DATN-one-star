<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('name', 'employee')->get();
        $permissions = Permission::all();
        return view('admin.role.index', compact('roles', 'permissions'));
    }

    public function detail($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.role.detail', compact('role', 'permissions', 'rolePermissions'));
    }


    public function showPermissions($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.role.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        DB::beginTransaction();
        try {
            // Sync permissions
            $role->permissions()->sync($request->input('permissions', []));

            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Phân quyền thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating permissions: ' . $e->getMessage());
        }
    }
}
