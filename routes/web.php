<?php

use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ArcherController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\SessionController;
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

    // Coaches
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/coaches/create', [CoachController::class, 'create'])->name('coaches.create');
        Route::post('/coaches', [CoachController::class, 'store'])->name('coaches.store');
        Route::get('/coaches/{coach}/edit', [CoachController::class, 'edit'])->name('coaches.edit');
        Route::put('/coaches/{coach}', [CoachController::class, 'update'])->name('coaches.update');
    });

    Route::middleware(['role:super_admin,club_admin,coach'])->group(function () {
        Route::get('/coaches', [CoachController::class, 'index'])->name('coaches.index');
        Route::get('/coaches/{coach}', [CoachController::class, 'show'])->name('coaches.show');
    });

    Route::middleware(['role:super_admin'])->group(function () {
        Route::delete('/coaches/{coach}', [CoachController::class, 'destroy'])->name('coaches.destroy');
    });

    // Sessions & Scorecards (all authenticated roles)
    Route::get('/archers/{archer}/sessions',        [SessionController::class, 'index'])->name('sessions.index');
    Route::get('/archers/{archer}/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
    Route::post('/archers/{archer}/sessions',       [SessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}/scorecard',     [SessionController::class, 'scorecard'])->name('sessions.scorecard');
    Route::put('/sessions/{session}/scores',        [SessionController::class, 'saveScores'])->name('sessions.saveScores');
    Route::get('/sessions/{session}',               [SessionController::class, 'show'])->name('sessions.show');
    Route::delete('/sessions/{session}',            [SessionController::class, 'destroy'])->name('sessions.destroy');

    // Super admin only
    Route::middleware(['role:super_admin'])->group(function () {
        Route::delete('/archers/{archer}', [ArcherController::class, 'destroy'])->name('archers.destroy');

        Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::delete('/admin/settings/logo', [SettingController::class, 'removeLogo'])->name('admin.settings.logo.remove');
    });

});
