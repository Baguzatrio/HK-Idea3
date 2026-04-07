<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with(['divisi', 'roles'])
            ->get()
            ->map(function ($permission) {
                return [
                    'id'             => $permission->id,
                    'nama'           => $permission->name,
                    'nama_divisi'    => $permission->divisi?->nama ?? '-',
                    'divisi_id'      => $permission->divisi_id,
                    'judul_report'   => $permission->judul_report ?? '-',
                    'nama_report'    => $permission->nama_report ?? '-',
                    'link_dashboard' => $permission->link_dashboard ?? '',
                    'roles'          => $permission->roles->map(fn($r) => [
                        'id'   => $r->id,
                        'nama' => $r->name,
                    ]),
                ];
            });

        $divisis = Divisi::orderBy('nama')->get(['id', 'nama']);

        return Inertia::render('MasterData/Permission/Index', [
            'permissions' => $permissions,
            'divisis'     => $divisis,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'           => 'required|string|max:255|unique:permissions,name',
            'divisi_id'      => 'nullable|exists:divisi,id',
            'judul_report'   => 'nullable|string|max:255',
            'nama_report'    => 'nullable|string|max:255',
            'link_dashboard' => 'nullable|string|max:255',
        ]);

        Permission::create([
            'name'           => $request->nama,
            'guard_name'     => 'web',
            'divisi_id'      => $request->divisi_id,
            'judul_report'   => $request->judul_report,
            'nama_report'    => $request->nama_report,
            'link_dashboard' => $request->link_dashboard,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil ditambahkan.');
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'nama'           => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'divisi_id'      => 'nullable|exists:divisi,id',
            'judul_report'   => 'nullable|string|max:255',
            'nama_report'    => 'nullable|string|max:255',
            'link_dashboard' => 'nullable|string|max:255',
        ]);

        $permission->update([
            'name'           => $request->nama,
            'divisi_id'      => $request->divisi_id,
            'judul_report'   => $request->judul_report,
            'nama_report'    => $request->nama_report,
            'link_dashboard' => $request->link_dashboard,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil diupdate.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil dihapus.');
    }
}