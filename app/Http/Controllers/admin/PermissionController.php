<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('admin.permissions.index') ->with('success', 'Permission added successfully');;
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id
        ]);

        $permission->update([
            'name' => $request->name
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        // Do not delete if permission is assigned to roles
       $assigned = DB::table('role_has_permissions')
        ->where('permission_id', $permission->id)
        ->exists();

        if ($assigned) {
            return back()->with('error', 'Permission is assigned to roles.');
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}

