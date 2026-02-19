<?php

use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ArcherController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Default redirect
Route::get('/', fn () => redirect()->route('archers.index'));

// Protected routes
Route::middleware(['auth'])->group(function () {

    // create must come before {archer} to avoid route conflict
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/archers/create', [ArcherController::class, 'create'])->name('archers.create');
        Route::post('/archers', [ArcherController::class, 'store'])->name('archers.store');
        Route::get('/archers/{archer}/edit', [ArcherController::class, 'edit'])->name('archers.edit');
        Route::put('/archers/{archer}', [ArcherController::class, 'update'])->name('archers.update');
    });

    // Coaches + admins: view
    Route::middleware(['role:super_admin,club_admin,coach'])->group(function () {
        Route::get('/archers', [ArcherController::class, 'index'])->name('archers.index');
        Route::get('/archers/{archer}', [ArcherController::class, 'show'])->name('archers.show');
    });

    // Super admin only
    Route::middleware(['role:super_admin'])->group(function () {
        Route::delete('/archers/{archer}', [ArcherController::class, 'destroy'])->name('archers.destroy');

        Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::delete('/admin/settings/logo', [SettingController::class, 'removeLogo'])->name('admin.settings.logo.remove');
    });

});
