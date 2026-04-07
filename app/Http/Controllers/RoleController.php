<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->get()
            ->map(function ($role) {
                return [
                    'id'          => $role->id,
                    'nama'        => $role->name,
                    'permissions' => $role->permissions->map(fn($p) => [
                        'id'   => $p->id,
                        'nama' => $p->name,
                    ]),
                ];
            });

        $permissions = Permission::orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'   => $p->id,
                'nama' => $p->name,
            ]);

        return Inertia::render('MasterData/Role/Index', [
            'roles'       => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'       => $request->nama,
            'guard_name' => 'web',
        ]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'nama'          => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $request->nama,
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}