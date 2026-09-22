<?php

namespace App\Http\Controllers;

use App\Mail\ClubInvitationMail;
use App\Models\Archer;
use App\Models\Club;
use App\Models\ClubInvitation;
use App\Models\Coach;
use App\Models\Scopes\ClubScope;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ClubInvitationController extends Controller
{
    private const EXPIRY_DAYS = 7;

    /** Club admin sends an invitation (existing user or bare email). */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role'  => ['required', 'in:archer,coach'],
        ]);

        $club = $this->resolveClub();
        if (! $club) {
            return back()->with('error', 'No club context — open this page from your club.');
        }

        $email = strtolower(trim($validated['email']));
        $type  = $validated['role'];

        // Existing platform user?
        $user      = User::where('email', $email)->first();
        $invitable = null;

        if ($user) {
            $invitable = $type === 'archer'
                ? Archer::withoutGlobalScope(ClubScope::class)->where('user_id', $user->id)->first()
                : Coach::withoutGlobalScope(ClubScope::class)->where('user_id', $user->id)->first();

            if ($user && ! $invitable && in_array($user->role, ['archer', 'coach'])) {
                return back()->with('error',
                    "That email belongs to a registered {$user->role} — choose the matching role to invite them.");
            }

            // Already a member of this club?
            if ($invitable && $invitable->clubs()->where('clubs.id', $club->id)->exists()) {
                return back()->with('error', 'That person is already a member of this club.');
            }
        }

        // Duplicate pending invitation?
        $pendingExists = ClubInvitation::where('club_id', $club->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->where(function ($q) use ($email, $invitable, $type) {
                $q->where('email', $email);
                if ($invitable) {
                    $q->orWhere(fn ($qq) => $qq
                        ->where('invitable_type', $type)
                        ->where('invitable_id', $invitable->id));
                }
            })
            ->exists();

        if ($pendingExists) {
            return back()->with('error', 'An invitation for that person is already pending.');
        }

        $invitation = ClubInvitation::create([
            'club_id'        => $club->id,
            'invitable_type' => $type,
            'invitable_id'   => $invitable?->id,
            'email'          => $email,
            'token'          => Str::random(64),
            'status'         => 'pending',
            'invited_at'     => now(),
            'expires_at'     => now()->addDays(self::EXPIRY_DAYS),
        ]);

        $this->sendInvitationMail($invitation);

        return back()->with('success', "Invitation sent to {$email}.");
    }

    /** Public token link — accept. */
    public function accept(string $token): RedirectResponse
    {
        $invitation = ClubInvitation::where('token', $token)->with('club')->firstOrFail();

        if (! $invitation->isPending()) {
            $msg = $invitation->isExpired()
                ? 'This invitation has expired.'
                : 'This invitation has already been responded to.';
            return redirect('/')->with('error', $msg);
        }

        // Email-only invite: no profile yet — send them to register with the token.
        if (! $invitation->invitable_id) {
            return redirect()->route('register', ['invite' => $invitation->token]);
        }

        $member = $invitation->invitable_model;
        if (! $member) {
            return redirect('/')->with('error', 'The invited account no longer exists.');
        }

        $hasPrimary = $member->clubs()->wherePivot('primary_club', true)->exists();

        $member->clubs()->syncWithoutDetaching([
            $invitation->club_id => [
                'primary_club' => ! $hasPrimary,
                'joined_at'    => now(),
            ],
        ]);

        // Keep legacy primary column in sync if this became their primary club.
        if (! $hasPrimary) {
            $member->update(['club_id' => $invitation->club_id]);
        }

        $invitation->update(['status' => 'accepted', 'responded_at' => now()]);

        return redirect()->route('login')->with('success',
            'Welcome to ' . $invitation->club->name . '! Log in to get started.');
    }

    /** Public token link — decline. */
    public function decline(string $token): RedirectResponse
    {
        $invitation = ClubInvitation::where('token', $token)->with('club')->firstOrFail();

        if (! $invitation->isPending()) {
            return redirect('/')->with('info', 'This invitation is no longer active.');
        }

        $invitation->update(['status' => 'declined', 'responded_at' => now()]);

        return redirect('/')->with('info',
            'You have declined the invitation from ' . $invitation->club->name . '.');
    }

    /** Club admin — resend with a fresh token/expiry. */
    public function resend(ClubInvitation $invitation): RedirectResponse
    {
        $this->authorizeClubAdmin($invitation->club_id);

        if ($invitation->status === 'accepted') {
            return back()->with('error', 'That invitation was already accepted.');
        }

        $invitation->update([
            'token'        => Str::random(64),
            'status'       => 'pending',
            'invited_at'   => now(),
            'responded_at' => null,
            'expires_at'   => now()->addDays(self::EXPIRY_DAYS),
        ]);

        $this->sendInvitationMail($invitation->fresh());

        return back()->with('success', 'Invitation re-sent to ' . ($invitation->email ?? $invitation->displayName()) . '.');
    }

    /** Club admin — cancel (status enum has no "cancelled"; declined mirrors coach flow). */
    public function cancel(ClubInvitation $invitation): RedirectResponse
    {
        $this->authorizeClubAdmin($invitation->club_id);

        $invitation->update(['status' => 'declined', 'responded_at' => now()]);

        return back()->with('success', 'Invitation cancelled.');
    }

    // ─────────────────────────────────────────────────────────────

    private function resolveClub(): ?Club
    {
        if (app()->has('currentClub')) {
            return app('currentClub');
        }
        return auth()->user()?->club;
    }

    private function authorizeClubAdmin(int $clubId): void
    {
        $user = auth()->user();
        if ($user->role === 'super_admin') {
            return;
        }
        if ($user->role === 'club_admin' && (int) $user->club_id === $clubId) {
            return;
        }
        abort(403);
    }

    private function sendInvitationMail(ClubInvitation $invitation): void
    {
        $isExisting = (bool) $invitation->invitable_id;

        $acceptUrl = $isExisting
            ? route('club-invitations.accept', $invitation->token)
            : route('register', ['invite' => $invitation->token]);

        $declineUrl = route('club-invitations.decline', $invitation->token);

        try {
            Mail::to($invitation->email)->send(new ClubInvitationMail(
                $invitation,
                $invitation->displayName(),
                $acceptUrl,
                $declineUrl,
            ));
        } catch (\Throwable $e) {
            Log::error('Failed to send club invitation email: ' . $e->getMessage());
        }
    }
}
