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
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.role.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
        ]);

        DB::beginTransaction();
        try {
            // Create role
            $role = Role::create([
                'name' => $request->name,
            ]);

            // Attach permissions
            if ($request->has('permissions')) {
                $role->permissions()->attach($request->permissions);
            }

            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating role: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $id,
        ]);

        DB::beginTransaction();
        try {
            // Update role
            $role->update([
                'name' => $request->name,
            ]);

            // Sync permissions
            $role->permissions()->sync($request->input('permissions', []));

            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating role: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        // Don't allow deleting admin, employee, or user roles
        $role = Role::findOrFail($id);
        $protectedRoles = ['admin', 'employee', 'user'];

        if (in_array($role->name, $protectedRoles)) {
            return redirect()->back()->with('error', 'Cannot delete protected role');
        }

        DB::beginTransaction();
        try {
            // Detach all permissions
            $role->permissions()->detach();

            // Delete the role
            $role->delete();

            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting role: ' . $e->getMessage());
        }
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
            return redirect()->route('admin.roles.index')->with('success', 'Permissions updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating permissions: ' . $e->getMessage());
        }
    }
}
