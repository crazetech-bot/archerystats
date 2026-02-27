<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ArcherController;
use App\Http\Controllers\ArcherPerformanceController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ClubInvitationController;
use App\Http\Controllers\CoachArcherController;
use App\Http\Controllers\CoachArcherInvitationController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\EliminationMatchController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StateTeamController;
use App\Http\Controllers\TrainingSessionController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Guest-only auth routes
Route::middleware(['guest'])->group(function () {
    Route::get('/register',        [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',       [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password',[ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Default redirect
Route::get('/', function () {
    if (auth()->check() && auth()->user()->role === 'archer' && auth()->user()->archer) {
        return redirect()->route('archers.show', auth()->user()->archer);
    }
    if (auth()->check() && auth()->user()->role === 'coach' && auth()->user()->coach) {
        return redirect()->route('coaches.show', auth()->user()->coach);
    }
    if (auth()->check() && auth()->user()->role === 'club_admin' && auth()->user()->club) {
        return redirect()->route('clubs.dashboard', auth()->user()->club);
    }
    if (auth()->check() && auth()->user()->role === 'state_admin') {
        return redirect()->route('state-teams.index');
    }
    return redirect()->route('archers.index');
});

// Club invitation responses (no auth required — token-based)
Route::get('/club-invitations/{token}/accept',  [ClubInvitationController::class, 'accept'])->name('club-invitations.accept');
Route::get('/club-invitations/{token}/decline', [ClubInvitationController::class, 'decline'])->name('club-invitations.decline');

// Coach-archer invitation responses (no auth required — token-based)
Route::get('/coach-archer-invitations/{token}/accept',  [CoachArcherInvitationController::class, 'accept'])->name('coach-archer-invitations.accept');
Route::get('/coach-archer-invitations/{token}/decline', [CoachArcherInvitationController::class, 'decline'])->name('coach-archer-invitations.decline');

// Protected routes
Route::middleware(['auth'])->group(function () {

    // Email verification
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // create must come before {archer} to avoid route conflict
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/archers/create', [ArcherController::class, 'create'])->name('archers.create');
        Route::post('/archers', [ArcherController::class, 'store'])->name('archers.store');
    });

    // Edit/update: admins + the archer themselves (controller enforces own-only for archer role)
    Route::middleware(['role:super_admin,club_admin,archer'])->group(function () {
        Route::get('/archers/{archer}/edit', [ArcherController::class, 'edit'])->name('archers.edit');
        Route::put('/archers/{archer}', [ArcherController::class, 'update'])->name('archers.update');
    });

    // Coaches + admins + state_admin: view archer list
    // Archers list: admins + state_admin only (coaches no longer have access)
    Route::middleware(['role:super_admin,club_admin,state_admin'])->group(function () {
        Route::get('/archers', [ArcherController::class, 'index'])->name('archers.index');
    });

    // Archer profile: admins, the archer themselves, state_admin, and coach (own assigned archers only — controller enforces)
    Route::middleware(['role:super_admin,club_admin,archer,state_admin,coach'])->group(function () {
        Route::get('/archers/{archer}', [ArcherController::class, 'show'])->name('archers.show');
        Route::get('/archers/{archer}/performance', [ArcherPerformanceController::class, 'show'])->name('archers.performance');
    });

    // Achievements: admins + the archer themselves (controller enforces own-only for archer role)
    Route::middleware(['role:super_admin,club_admin,archer'])->group(function () {
        Route::post('/archers/{archer}/achievements', [AchievementController::class, 'store'])->name('achievements.store');
        Route::delete('/archers/{archer}/achievements/{achievement}', [AchievementController::class, 'destroy'])->name('achievements.destroy');
    });

    // Coaches
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/coaches/create', [CoachController::class, 'create'])->name('coaches.create');
        Route::post('/coaches', [CoachController::class, 'store'])->name('coaches.store');
    });

    // Edit/update: admins + the coach themselves (controller enforces own-only for coach role)
    Route::middleware(['role:super_admin,club_admin,coach'])->group(function () {
        Route::get('/coaches/{coach}/edit', [CoachController::class, 'edit'])->name('coaches.edit');
        Route::put('/coaches/{coach}', [CoachController::class, 'update'])->name('coaches.update');
    });

    Route::middleware(['role:super_admin,club_admin,coach'])->group(function () {
        Route::get('/coaches', [CoachController::class, 'index'])->name('coaches.index');
        Route::get('/coaches/{coach}', [CoachController::class, 'show'])->name('coaches.show');

        // Coach sub-modules: assigned archers
        Route::get('/coaches/{coach}/archers',                          [CoachArcherController::class, 'index'])->name('coaches.archers.index');
        Route::post('/coaches/{coach}/archers',                         [CoachArcherController::class, 'store'])->name('coaches.archers.store');
        Route::delete('/coaches/{coach}/archers/{archer}',              [CoachArcherController::class, 'destroy'])->name('coaches.archers.destroy');
        Route::delete('/coach-archer-invitations/{invitation}/cancel',  [CoachArcherInvitationController::class, 'cancel'])->name('coach-archer-invitations.cancel');

        // Coach sub-modules: training sessions
        Route::get('/coaches/{coach}/training',                      [TrainingSessionController::class, 'index'])->name('coaches.training.index');
        Route::get('/coaches/{coach}/training/create',               [TrainingSessionController::class, 'create'])->name('coaches.training.create');
        Route::post('/coaches/{coach}/training',                     [TrainingSessionController::class, 'store'])->name('coaches.training.store');
        Route::get('/coaches/{coach}/training/{training}',           [TrainingSessionController::class, 'show'])->name('coaches.training.show');
        Route::get('/coaches/{coach}/training/{training}/edit',      [TrainingSessionController::class, 'edit'])->name('coaches.training.edit');
        Route::put('/coaches/{coach}/training/{training}',           [TrainingSessionController::class, 'update'])->name('coaches.training.update');
        Route::delete('/coaches/{coach}/training/{training}',        [TrainingSessionController::class, 'destroy'])->name('coaches.training.destroy');

        // Coach sub-modules: club archer results (read-only)
        Route::get('/coaches/{coach}/club-results',          [SessionController::class, 'coachView'])->name('coaches.club-results');
        Route::get('/coaches/{coach}/club-results/{session}',[SessionController::class, 'coachShowSession'])->name('coaches.club-results.show');
    });

    Route::middleware(['role:super_admin'])->group(function () {
        Route::delete('/coaches/{coach}', [CoachController::class, 'destroy'])->name('coaches.destroy');
    });

    // Clubs — create/delete/import: super_admin only
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/clubs',                    [ClubController::class, 'index'])->name('clubs.index');
        Route::get('/clubs/create',             [ClubController::class, 'create'])->name('clubs.create');
        Route::post('/clubs',                   [ClubController::class, 'store'])->name('clubs.store');
        Route::get('/clubs/import',             [ClubController::class, 'importForm'])->name('clubs.import');
        Route::post('/clubs/import',            [ClubController::class, 'import'])->name('clubs.import.store');
        Route::get('/clubs/import/template',    [ClubController::class, 'importTemplate'])->name('clubs.import.template');
        Route::delete('/clubs/{club}',          [ClubController::class, 'destroy'])->name('clubs.destroy');
    });

    // State Teams — full CRUD: super_admin + state_admin
    Route::middleware(['role:super_admin,state_admin'])->group(function () {
        Route::get('/state-teams',                   [StateTeamController::class, 'index'])->name('state-teams.index');
        Route::get('/state-teams/create',            [StateTeamController::class, 'create'])->name('state-teams.create');
        Route::post('/state-teams',                  [StateTeamController::class, 'store'])->name('state-teams.store');
        Route::get('/state-teams/{stateTeam}',       [StateTeamController::class, 'show'])->name('state-teams.show');
        Route::get('/state-teams/{stateTeam}/edit',  [StateTeamController::class, 'edit'])->name('state-teams.edit');
        Route::put('/state-teams/{stateTeam}',       [StateTeamController::class, 'update'])->name('state-teams.update');
        Route::delete('/state-teams/{stateTeam}',    [StateTeamController::class, 'destroy'])->name('state-teams.destroy');
    });

    // Clubs — view/edit/dashboard/members: super_admin + club_admin (own)
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/clubs/{club}',                               [ClubController::class, 'show'])->name('clubs.show');
        Route::get('/clubs/{club}/edit',                          [ClubController::class, 'edit'])->name('clubs.edit');
        Route::put('/clubs/{club}',                               [ClubController::class, 'update'])->name('clubs.update');
        Route::get('/clubs/{club}/dashboard',                     [ClubController::class, 'dashboard'])->name('clubs.dashboard');
        Route::get('/clubs/{club}/members',                       [ClubController::class, 'members'])->name('clubs.members');
        Route::post('/clubs/{club}/archers/{archer}',             [ClubController::class, 'inviteArcher'])->name('clubs.archers.add');
        Route::delete('/clubs/{club}/archers/{archer}',           [ClubController::class, 'removeArcher'])->name('clubs.archers.remove');
        Route::post('/clubs/{club}/coaches/{coach}',              [ClubController::class, 'inviteCoach'])->name('clubs.coaches.add');
        Route::delete('/clubs/{club}/coaches/{coach}',            [ClubController::class, 'removeCoach'])->name('clubs.coaches.remove');
        Route::delete('/club-invitations/{invitation}/cancel',    [ClubInvitationController::class, 'cancel'])->name('club-invitations.cancel');
    });

    // Elimination Matches — create before {eliminationMatch} to avoid route conflict
    Route::middleware(['role:super_admin,club_admin,coach,archer'])->group(function () {
        Route::get('/elimination-matches',                                  [EliminationMatchController::class, 'index'])->name('elimination-matches.index');
        Route::get('/elimination-matches/create',                           [EliminationMatchController::class, 'create'])->name('elimination-matches.create');
        Route::post('/elimination-matches',                                 [EliminationMatchController::class, 'store'])->name('elimination-matches.store');
        Route::get('/elimination-matches/{eliminationMatch}/scorecard',     [EliminationMatchController::class, 'scorecard'])->name('elimination-matches.scorecard');
        Route::put('/elimination-matches/{eliminationMatch}/scores',        [EliminationMatchController::class, 'saveScores'])->name('elimination-matches.saveScores');
        Route::delete('/elimination-matches/{eliminationMatch}',            [EliminationMatchController::class, 'destroy'])->name('elimination-matches.destroy');
    });

    // Sessions & Scorecards (all authenticated roles including archer)
    Route::middleware(['role:super_admin,club_admin,coach,archer'])->group(function () {
        Route::get('/archers/{archer}/sessions',        [SessionController::class, 'index'])->name('sessions.index');
        Route::get('/archers/{archer}/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
        Route::post('/archers/{archer}/sessions',       [SessionController::class, 'store'])->name('sessions.store');
        Route::get('/sessions/{session}/scorecard',     [SessionController::class, 'scorecard'])->name('sessions.scorecard');
        Route::put('/sessions/{session}/scores',        [SessionController::class, 'saveScores'])->name('sessions.saveScores');
        Route::get('/sessions/{session}',               [SessionController::class, 'show'])->name('sessions.show');
        Route::delete('/sessions/{session}',            [SessionController::class, 'destroy'])->name('sessions.destroy');
    });

    // Super admin only
    Route::middleware(['role:super_admin'])->group(function () {
        Route::delete('/archers/{archer}', [ArcherController::class, 'destroy'])->name('archers.destroy');

        Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::delete('/admin/settings/logo', [SettingController::class, 'removeLogo'])->name('admin.settings.logo.remove');
        Route::post('/admin/settings/registration', [SettingController::class, 'updateRegistration'])->name('admin.settings.registration');

        // Admin user management
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{user}/password', [AdminUserController::class, 'updatePassword'])->name('admin.users.password');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/admin/accounts/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.accounts.toggleStatus');
    });

});
