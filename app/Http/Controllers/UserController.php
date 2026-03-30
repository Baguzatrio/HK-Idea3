<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['divisi'])
            ->get()
            ->map(function ($user) {
                return [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'divisi' => $user->divisi?->nama ?? '-',
                    'role'   => '-',
                ];
            });

        return Inertia::render('MasterData/User/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        // return Inertia::render('User/Create');
    }

    public function store(Request $request)
    {
        // Implementasi setelah form dibuat
    }

    public function edit(User $user)
    {
        // return Inertia::render('User/Edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        // Implementasi setelah form dibuat
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}