<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ClubManagementController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ArcherController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\ArcherPerformanceController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ClubInvitationController;
use App\Http\Controllers\CoachArcherController;
use App\Http\Controllers\CoachArcherInvitationController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\EliminationMatchController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\LiveScoringRealtimeController;
use App\Http\Controllers\ClubLandingController;
use App\Http\Controllers\ClubRegistrationController;
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

// Club self-registration — public, root domain only (no auth required)
Route::middleware(['root.domain'])->group(function () {
    Route::get('/register-club',            [ClubRegistrationController::class, 'showForm'])->name('club-register.form');
    Route::post('/register-club',           [ClubRegistrationController::class, 'register'])->name('club-register');
    Route::get('/register-club/success',    [ClubRegistrationController::class, 'success'])->name('club-register.success');
    Route::get('/register-club/check-slug', [ClubRegistrationController::class, 'checkSlug'])->name('club-register.check-slug');
});

// Root path — public landing page on subdomains, dashboard redirect otherwise
Route::get('/', function () {
    // On a club subdomain: show public landing page for guests
    if (app()->has('currentClub') && !auth()->check()) {
        return app(ClubLandingController::class)->show();
    }
    // Logged-in role redirects
    if (auth()->check() && auth()->user()->role === 'archer' && auth()->user()->archer) {
        return redirect()->route('archers.show', auth()->user()->archer);
    }
    if (auth()->check() && auth()->user()->role === 'coach' && auth()->user()->coach) {
        return redirect()->route('coaches.show', auth()->user()->coach);
    }
    return redirect()->route('archers.index');
})->name('club.landing');

// User manual — public, SEO-facing (guest CTA + role-tabbed guides)
Route::get('/manual', fn () => view('manual.index'))->name('manual.index');

// Coach-archer invitation responses (no auth required — token-based)
Route::get('/coach-archer-invitations/{token}/accept',  [CoachArcherInvitationController::class, 'accept'])->name('coach-archer-invitations.accept');
Route::get('/coach-archer-invitations/{token}/decline', [CoachArcherInvitationController::class, 'decline'])->name('coach-archer-invitations.decline');

// Club membership invitation responses (no auth required — token-based)
Route::get('/club-invitations/{token}/accept',  [ClubInvitationController::class, 'accept'])->name('club-invitations.accept');
Route::get('/club-invitations/{token}/decline', [ClubInvitationController::class, 'decline'])->name('club-invitations.decline');

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

    // Everything below requires a verified (activated) account.
    Route::middleware(['verified'])->group(function () {

    // create/import must come before {archer} to avoid route conflict
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/archers/create',          [ArcherController::class, 'create'])->name('archers.create');
        Route::post('/archers',                [ArcherController::class, 'store'])->name('archers.store');
        Route::get('/archers/import',          [ArcherController::class, 'importForm'])->name('archers.import');
        Route::post('/archers/import',         [ArcherController::class, 'import'])->name('archers.import.store');
        Route::get('/archers/import/template', [ArcherController::class, 'importTemplate'])->name('archers.import.template');
    });

    // Edit/update: admins + the archer themselves (controller enforces own-only for archer role)
    Route::middleware(['role:super_admin,club_admin,archer'])->group(function () {
        Route::get('/archers/{archer}/edit', [ArcherController::class, 'edit'])->name('archers.edit');
        Route::put('/archers/{archer}', [ArcherController::class, 'update'])->name('archers.update');
    });

    // Archers list: admins only
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/archers', [ArcherController::class, 'index'])->name('archers.index');
    });

    // Archer profile: admins, the archer themselves, coach
    Route::middleware(['role:super_admin,club_admin,archer,coach'])->group(function () {
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

    // Edit/update: admins + the coach themselves
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
        Route::post('/coaches/{coach}/archers/invite',                  [CoachArcherController::class, 'invite'])->name('coaches.archers.invite');
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

        // Attendance: mark a session's roster + per-archer report
        Route::post('/coaches/{coach}/training/{training}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/report',                              [AttendanceReportController::class, 'index'])->name('attendance.report');

        // Coach sub-modules: club archer results (read-only)
        Route::get('/coaches/{coach}/club-results',          [SessionController::class, 'coachView'])->name('coaches.club-results');
        Route::get('/coaches/{coach}/club-results/{session}',[SessionController::class, 'coachShowSession'])->name('coaches.club-results.show');
    });

    Route::middleware(['role:super_admin'])->group(function () {
        Route::delete('/coaches/{coach}', [CoachController::class, 'destroy'])->name('coaches.destroy');
    });

    // Live scoring — real-time scoreboard
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/live-scoring/realtime',       [LiveScoringRealtimeController::class, 'index'])->name('live-scoring.realtime');
        Route::get('/live-scoring/realtime/data',  [LiveScoringRealtimeController::class, 'data'])->name('live-scoring.realtime.data');
    });

    // Elimination Matches — read-only views
    Route::middleware(['role:super_admin,club_admin,coach,archer'])->group(function () {
        Route::get('/elimination-matches',                              [EliminationMatchController::class, 'index'])->name('elimination-matches.index');
        Route::get('/elimination-matches/{eliminationMatch}/scorecard', [EliminationMatchController::class, 'scorecard'])->name('elimination-matches.scorecard');
    });

    // Elimination Matches — write (create must stay before {eliminationMatch})
    Route::middleware(['role:super_admin,club_admin,coach,archer'])->group(function () {
        Route::get('/elimination-matches/create',                    [EliminationMatchController::class, 'create'])->name('elimination-matches.create');
        Route::post('/elimination-matches',                          [EliminationMatchController::class, 'store'])->name('elimination-matches.store');
        Route::put('/elimination-matches/{eliminationMatch}/scores', [EliminationMatchController::class, 'saveScores'])->name('elimination-matches.saveScores');
        Route::delete('/elimination-matches/{eliminationMatch}',     [EliminationMatchController::class, 'destroy'])->name('elimination-matches.destroy');
    });

    // Sessions — read-only views
    Route::middleware(['role:super_admin,club_admin,coach,archer'])->group(function () {
        Route::get('/archers/{archer}/sessions',    [SessionController::class, 'index'])->name('sessions.index');
        Route::get('/sessions/{session}/scorecard', [SessionController::class, 'scorecard'])->name('sessions.scorecard');
        Route::get('/sessions/{session}',           [SessionController::class, 'show'])->name('sessions.show');
    });

    // Sessions — write (create must stay before {session})
    Route::middleware(['role:super_admin,club_admin,coach,archer'])->group(function () {
        Route::get('/archers/{archer}/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
        Route::post('/archers/{archer}/sessions',       [SessionController::class, 'store'])->name('sessions.store');
        Route::put('/sessions/{session}/scores',        [SessionController::class, 'saveScores'])->name('sessions.saveScores');
        Route::delete('/sessions/{session}',            [SessionController::class, 'destroy'])->name('sessions.destroy');
    });

    // Club admin — club public page settings
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/settings/club-page',  [SettingController::class, 'clubPage'])->name('settings.club-page');
        Route::post('/settings/club-page', [SettingController::class, 'updateClubPage'])->name('settings.club-page.update');
    });

    // Club events & RSVP (calendar for everyone; organising for admins & coaches)
    Route::middleware(['role:super_admin,club_admin,coach,archer'])->group(function () {
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::post('/events/{event}/rsvp', [EventController::class, 'rsvp'])->name('events.rsvp');
    });
    Route::middleware(['role:super_admin,club_admin,coach'])->group(function () {
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::post('/events/{event}/pin', [EventController::class, 'togglePin'])->name('events.pin');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    });

    // Club announcements (feed for everyone; posting for admins & coaches)
    Route::middleware(['role:super_admin,club_admin,coach,archer'])->group(function () {
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    });
    Route::middleware(['role:super_admin,club_admin,coach'])->group(function () {
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::post('/announcements/{announcement}/pin', [AnnouncementController::class, 'togglePin'])->name('announcements.pin');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

    // Club membership management (club admins)
    Route::middleware(['role:super_admin,club_admin'])->group(function () {
        Route::get('/members',                                   [MembershipController::class, 'index'])->name('members.index');
        Route::post('/members/invite',                           [ClubInvitationController::class, 'store'])->name('members.invite');
        Route::post('/members/invitations/{invitation}/resend',  [ClubInvitationController::class, 'resend'])->name('members.invitations.resend');
        Route::delete('/members/invitations/{invitation}',       [ClubInvitationController::class, 'cancel'])->name('members.invitations.cancel');
        Route::post('/members/transfers/{transferRequest}/approve', [MembershipController::class, 'approveTransfer'])->name('members.transfers.approve');
        Route::post('/members/transfers/{transferRequest}/decline', [MembershipController::class, 'declineTransfer'])->name('members.transfers.decline');
        Route::delete('/members/{type}/{id}',                    [MembershipController::class, 'removeMember'])->name('members.remove');
    });

    // My Clubs actions (self-service; controller enforces self-or-admin)
    Route::middleware(['role:super_admin,club_admin,archer'])->group(function () {
        Route::post('/archers/{archer}/clubs/{club}/primary', [MembershipController::class, 'setPrimaryArcher'])->name('archers.clubs.primary');
        Route::delete('/archers/{archer}/clubs/{club}',       [MembershipController::class, 'leaveArcher'])->name('archers.clubs.leave');
        Route::post('/archers/{archer}/transfer-request',     [MembershipController::class, 'requestTransfer'])->name('archers.transfer-request');
        Route::delete('/transfer-requests/{transferRequest}', [MembershipController::class, 'cancelTransfer'])->name('transfer-requests.cancel');
    });
    Route::middleware(['role:super_admin,club_admin,coach'])->group(function () {
        Route::post('/coaches/{coach}/clubs/{club}/primary', [MembershipController::class, 'setPrimaryCoach'])->name('coaches.clubs.primary');
        Route::delete('/coaches/{coach}/clubs/{club}',       [MembershipController::class, 'leaveCoach'])->name('coaches.clubs.leave');
    });

    // Super admin only — root domain access enforced by root.domain middleware
    Route::middleware(['root.domain', 'role:super_admin'])->group(function () {
        Route::delete('/archers/{archer}', [ArcherController::class, 'destroy'])->name('archers.destroy');

        // Platform settings
        Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::delete('/admin/settings/logo', [SettingController::class, 'removeLogo'])->name('admin.settings.logo.remove');
        Route::delete('/admin/settings/seo-image', [SettingController::class, 'removeSeoImage'])->name('admin.settings.seo.image.remove');
        Route::post('/admin/settings/registration', [SettingController::class, 'updateRegistration'])->name('admin.settings.registration');
        Route::post('/admin/settings/registration-policy', [SettingController::class, 'updateRegistrationPolicy'])->name('admin.settings.registrationPolicy');
        Route::post('/admin/settings/popups', [SettingController::class, 'updatePopups'])->name('admin.settings.popups');

        // Club management (super admin platform dashboard)
        Route::get('/admin/clubs', [ClubManagementController::class, 'index'])->name('admin.clubs.index');
        Route::post('/admin/clubs/bulk-destroy', [ClubManagementController::class, 'bulkDestroy'])->name('admin.clubs.bulkDestroy');
        Route::get('/admin/clubs/{club}', [ClubManagementController::class, 'show'])->name('admin.clubs.show');
        Route::post('/admin/clubs/{club}/toggle', [ClubManagementController::class, 'toggle'])->name('admin.clubs.toggle');
        Route::put('/admin/clubs/{club}', [ClubManagementController::class, 'update'])->name('admin.clubs.update');
        Route::delete('/admin/clubs/{club}', [ClubManagementController::class, 'destroy'])->name('admin.clubs.destroy');

        // Admin user management
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::post('/admin/users/bulk-destroy', [AdminUserController::class, 'bulkDestroy'])->name('admin.users.bulkDestroy');
        Route::put('/admin/users/{user}/password', [AdminUserController::class, 'updatePassword'])->name('admin.users.password');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/admin/accounts/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.accounts.toggleStatus');
        Route::post('/admin/users/{user}/promote', [AdminUserController::class, 'promote'])->name('admin.users.promote');
    });

    }); // end verified group

});
