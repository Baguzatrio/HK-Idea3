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

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

require __DIR__.'/auth.php';