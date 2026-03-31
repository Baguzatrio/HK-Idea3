<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

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
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ]);

    User::create([
        'name'      => $request->name,
        'email'     => $request->email,
        'password'  => Hash::make($request->password),
        'divisi_id' => $request->divisi_id ?? null,
    ]);

    return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
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