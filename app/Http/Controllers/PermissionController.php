<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use App\Models\Divisi;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::get()
            ->map(function ($p) {
                // To avoid relation error if not defined on Spatie model, let's fetch divisi name via Divisi model
                $divisi = Divisi::find($p->divisi_id);
                return [
                    'id'             => $p->id,
                    'nama'           => $p->name, // from Spatie column
                    'divisi_id'      => $p->divisi_id,
                    'nama_divisi'    => $divisi ? $divisi->nama : '-',
                    'judul_report'   => $p->judul_report,
                    'nama_report'    => $p->nama_report,
                    'link_dashboard' => $p->link_dashboard,
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
            'link_dashboard' => 'nullable|url|max:1000',
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
            'link_dashboard' => 'nullable|url|max:1000',
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
