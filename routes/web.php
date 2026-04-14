<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Models\Divisi;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();

        $divisis = Divisi::with(['permissions'])
            ->orderBy('no_urut')
            ->get()
            ->map(function ($d) use ($user) {
                // Filter izin mana yang boleh dilihat oleh user bersangkutan
                $allowedPermissions = $d->permissions->filter(function ($p) use ($user) {
                    return $user->hasRole('super_admin') || $user->hasPermissionTo($p->name);
                });

                return [
                    'id'      => $d->id,
                    'kode'    => $d->kode,
                    'nama'    => $d->nama,
                    'logo'    => $d->logo,
                    'no_urut' => $d->no_urut,
                    'permissions' => $allowedPermissions->values()->map(fn($p) => [
                        'id'             => $p->id,
                        'nama'           => $p->name ?? $p->nama,
                        'nama_report'    => $p->nama_report,
                        'judul_report'   => $p->judul_report,
                        'link_dashboard' => $p->link_dashboard,
                    ])->toArray(),
                ];
            })->filter(function ($d) {
                return count($d['permissions']) > 0;
            })->values();

        return Inertia::render('Dashboard', [
            'divisis' => $divisis,
        ]);
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('divisis', DivisiController::class)->except(['create', 'edit', 'show']);

        Route::resource('roles', RoleController::class)->except(['create', 'edit', 'show']);
        Route::resource('permissions', PermissionController::class)->except(['create', 'edit', 'show']);
    });

});

require __DIR__.'/auth.php';