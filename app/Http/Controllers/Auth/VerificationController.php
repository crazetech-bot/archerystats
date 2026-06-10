<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ClubActivatedMail;
use App\Mail\ClubPendingApprovalMail;
use App\Mail\NewUserRegisteredMail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectVerified($request->user());
        }

        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): View|RedirectResponse
    {
        $user = $request->user();

        $justVerified = false;
        if (! $user->hasVerifiedEmail()) {
            $request->fulfill();
            event(new Verified($user));
            $justVerified = true;
        }

        // Club admins need a second gate: super-admin approval (or auto-activation).
        if ($user->role === 'club_admin') {
            return $this->handleClubVerification($user, $justVerified);
        }

        // Archers / coaches are fully active once verified — notify super-admins.
        if ($justVerified) {
            $this->notifySuperAdmins($user);
        }

        return $this->redirectVerified($user);
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectVerified($request->user());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent! Check your inbox.');
    }

    /**
     * After a club admin verifies their email, either auto-activate the club or
     * leave it pending and email the super-admins for manual approval.
     */
    private function handleClubVerification(User $user, bool $justVerified): View|RedirectResponse
    {
        $club = $user->club;
        $mode = Setting::get('club_activation_mode', 'manual');

        // Already approved (e.g. re-clicking the link after activation) — let them in.
        if ($club && $club->active) {
            return $this->redirectVerified($user);
        }

        if ($club && $mode === 'auto') {
            $club->update(['active' => true]);

            if ($justVerified) {
                try {
                    Mail::to($user->email)->send(new ClubActivatedMail($club, $user));
                } catch (\Throwable $e) {
                    Log::error('Failed to send club-activated email: ' . $e->getMessage());
                }
            }

            Auth::logout();
            return redirect()->route('login')->with('success',
                'Your club is now live! Log in at ' . $club->slug . '.' . config('app.root_domain', 'sportdns.com'));
        }

        // Manual mode — notify super-admins, keep the club pending.
        if ($justVerified && $club) {
            foreach (User::where('role', 'super_admin')->get() as $admin) {
                try {
                    Mail::to($admin->email)->send(new ClubPendingApprovalMail($club, $user));
                } catch (\Throwable $e) {
                    Log::error('Failed to send club-pending email: ' . $e->getMessage());
                }
            }
        }

        Auth::logout();
        return view('auth.club-pending', ['club' => $club]);
    }

    private function notifySuperAdmins(User $user): void
    {
        $user->load('club');
        foreach (User::where('role', 'super_admin')->get() as $admin) {
            try {
                Mail::to($admin->email)->send(new NewUserRegisteredMail($user));
            } catch (\Throwable $e) {
                Log::error('Failed to send new-user notification: ' . $e->getMessage());
            }
        }
    }

    private function redirectVerified($user): RedirectResponse
    {
        if ($user->role === 'archer' && $user->archer) {
            return redirect()->route('archers.show', $user->archer);
        }
        if ($user->role === 'coach' && $user->coach) {
            return redirect()->route('coaches.show', $user->coach);
        }
        return redirect()->route('archers.index');
    }
}
