<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisiController;
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
        $divisis = Divisi::with([])
            ->orderBy('no_urut')
            ->get()
            ->map(fn($d) => [
                'id'      => $d->id,
                'kode'    => $d->kode,
                'nama'    => $d->nama,
                'logo'    => $d->logo,
                'no_urut' => $d->no_urut,
                'permissions' => [],
            ]);

        return Inertia::render('Dashboard', [
            'divisis' => $divisis,
        ]);
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data
    Route::resource('users', UserController::class);
    Route::resource('divisis', DivisiController::class)->except(['create', 'edit', 'show']);

    // Master Data - Role (mockup)
    Route::get('/roles', function () {
        return Inertia::render('MasterData/Role/Index', [
            'roles'       => [],
            'permissions' => [],
        ]);
    })->name('roles.index');

    // Master Data - Permission (mockup)
    Route::get('/permissions', function () {
        $divisis = Divisi::orderBy('nama')->get(['id', 'nama']);

        return Inertia::render('MasterData/Permission/Index', [
            'permissions' => [],
            'divisis'     => $divisis,
        ]);
    })->name('permissions.index');

});

require __DIR__.'/auth.php';