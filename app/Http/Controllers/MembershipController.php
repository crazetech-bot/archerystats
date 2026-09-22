<?php

namespace App\Http\Controllers;

use App\Mail\ClubTransferRequestMail;
use App\Mail\ClubTransferResultMail;
use App\Models\Archer;
use App\Models\Club;
use App\Models\ClubInvitation;
use App\Models\ClubTransferRequest;
use App\Models\Coach;
use App\Models\Scopes\ClubScope;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MembershipController extends Controller
{
    private const TRANSFER_EXPIRY_DAYS = 14;

    /** Club admin dashboard: roster, pending invitations, incoming transfers. */
    public function index(): View|RedirectResponse
    {
        $club = $this->resolveClub();
        if (! $club) {
            return redirect()->route('admin.clubs.index')
                ->with('info', 'Open a club to manage its members.');
        }

        $archers = $club->archers()->withoutGlobalScope(ClubScope::class)
            ->with('user')->orderBy('archers.id')->get();
        $coaches = $club->coaches()->withoutGlobalScope(ClubScope::class)
            ->with('user')->orderBy('coaches.id')->get();

        $invitations = ClubInvitation::where('club_id', $club->id)
            ->orderByDesc('invited_at')->limit(50)->get();

        $transfers = ClubTransferRequest::where('to_club_id', $club->id)
            ->where('status', 'pending')->where('expires_at', '>', now())
            ->with(['archer.user', 'fromClub'])
            ->orderByDesc('created_at')->get();

        return view('members.index', compact('club', 'archers', 'coaches', 'invitations', 'transfers'));
    }

    /** Remove a member from the club (detach pivot; re-point primary if needed). */
    public function removeMember(Request $request, string $type, int $id): RedirectResponse
    {
        abort_unless(in_array($type, ['archer', 'coach']), 404);

        $club = $this->resolveClub();
        abort_unless($club, 404);
        $this->authorizeClubAdmin($club->id);

        $member = $type === 'archer'
            ? Archer::withoutGlobalScope(ClubScope::class)->with('user')->findOrFail($id)
            : Coach::withoutGlobalScope(ClubScope::class)->with('user')->findOrFail($id);

        $membership = $member->clubs()->where('clubs.id', $club->id)->first();
        if (! $membership) {
            return back()->with('error', 'That person is not a member of this club.');
        }

        $wasPrimary = (bool) $membership->pivot->primary_club;
        $member->clubs()->detach($club->id);

        if ($wasPrimary) {
            $next = $member->clubs()->withoutGlobalScope(ClubScope::class)
                ->orderByPivot('joined_at', 'desc')->first();
            if ($next) {
                $member->clubs()->updateExistingPivot($next->id, ['primary_club' => true]);
            }
            $member->update(['club_id' => $next?->id]);
        }

        $name = $member->user?->name ?? 'Member';
        return back()->with('success', "{$name} has been removed from {$club->name}.");
    }

    // ── My Clubs: set primary / leave ────────────────────────────

    public function setPrimaryArcher(Archer $archer, Club $club): RedirectResponse
    {
        return $this->setPrimary($archer, $club);
    }

    public function setPrimaryCoach(Coach $coach, Club $club): RedirectResponse
    {
        return $this->setPrimary($coach, $club);
    }

    public function leaveArcher(Archer $archer, Club $club): RedirectResponse
    {
        return $this->leave($archer, $club);
    }

    public function leaveCoach(Coach $coach, Club $club): RedirectResponse
    {
        return $this->leave($coach, $club);
    }

    private function setPrimary(Archer|Coach $member, Club $club): RedirectResponse
    {
        $this->authorizeSelfOrAdmin($member);

        if (! $member->clubs()->where('clubs.id', $club->id)->exists()) {
            return back()->with('error', 'Not a member of that club.');
        }

        foreach ($member->clubs()->withoutGlobalScope(ClubScope::class)->get() as $c) {
            $member->clubs()->updateExistingPivot($c->id, ['primary_club' => $c->id === $club->id]);
        }
        $member->update(['club_id' => $club->id]);

        return back()->with('success', $club->name . ' is now the primary club.');
    }

    private function leave(Archer|Coach $member, Club $club): RedirectResponse
    {
        $this->authorizeSelfOrAdmin($member);

        $membership = $member->clubs()->where('clubs.id', $club->id)->first();
        if (! $membership) {
            return back()->with('error', 'Not a member of that club.');
        }

        $isPrimary  = (bool) $membership->pivot->primary_club;
        $clubCount  = $member->clubs()->withoutGlobalScope(ClubScope::class)->count();

        if ($isPrimary && $clubCount <= 1) {
            return back()->with('error', 'You cannot leave your only club. Join another club first.');
        }

        $member->clubs()->detach($club->id);

        if ($isPrimary) {
            $next = $member->clubs()->withoutGlobalScope(ClubScope::class)
                ->orderByPivot('joined_at', 'desc')->first();
            if ($next) {
                $member->clubs()->updateExistingPivot($next->id, ['primary_club' => true]);
                $member->update(['club_id' => $next->id]);
            }
        }

        return back()->with('success', 'Left ' . $club->name . '.');
    }

    // ── Transfers ────────────────────────────────────────────────

    /** Archer requests to transfer their primary membership to another club. */
    public function requestTransfer(Request $request, Archer $archer): RedirectResponse
    {
        $this->authorizeSelfOrAdmin($archer);

        $validated = $request->validate([
            'to_club_id' => ['required', 'exists:clubs,id'],
        ]);

        $toClub = Club::findOrFail($validated['to_club_id']);

        if (! $toClub->active) {
            return back()->with('error', 'That club is not active.');
        }
        if ($archer->clubs()->where('clubs.id', $toClub->id)->exists()) {
            return back()->with('error', 'Already a member of that club — use "set primary" instead.');
        }
        $hasPending = ClubTransferRequest::where('archer_id', $archer->id)
            ->where('status', 'pending')->where('expires_at', '>', now())->exists();
        if ($hasPending) {
            return back()->with('error', 'There is already a pending transfer request.');
        }

        $transfer = ClubTransferRequest::create([
            'archer_id'    => $archer->id,
            'from_club_id' => $archer->club_id,
            'to_club_id'   => $toClub->id,
            'token'        => Str::random(64),
            'status'       => 'pending',
            'expires_at'   => now()->addDays(self::TRANSFER_EXPIRY_DAYS),
        ]);

        // Notify the receiving club's admins.
        $admins = User::where('role', 'club_admin')->where('club_id', $toClub->id)->get();
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new ClubTransferRequestMail($transfer));
            } catch (\Throwable $e) {
                Log::error('Failed to send transfer-request email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Transfer request sent to ' . $toClub->name . ' for approval.');
    }

    public function cancelTransfer(ClubTransferRequest $transferRequest): RedirectResponse
    {
        $this->authorizeSelfOrAdmin($transferRequest->archer);

        if ($transferRequest->status !== 'pending') {
            return back()->with('error', 'That request is no longer pending.');
        }

        $transferRequest->update(['status' => 'cancelled', 'responded_at' => now()]);

        return back()->with('success', 'Transfer request cancelled.');
    }

    public function approveTransfer(ClubTransferRequest $transferRequest): RedirectResponse
    {
        $this->authorizeClubAdmin($transferRequest->to_club_id);

        if (! $transferRequest->isPending()) {
            return back()->with('error', 'That request is no longer pending.');
        }

        $archer = $transferRequest->archer;

        // New club becomes primary; previous primary (if any) is demoted to secondary.
        foreach ($archer->clubs()->withoutGlobalScope(ClubScope::class)->get() as $c) {
            $archer->clubs()->updateExistingPivot($c->id, ['primary_club' => false]);
        }
        $archer->clubs()->syncWithoutDetaching([
            $transferRequest->to_club_id => ['primary_club' => true, 'joined_at' => now()],
        ]);
        $archer->clubs()->updateExistingPivot($transferRequest->to_club_id, ['primary_club' => true]);
        $archer->update(['club_id' => $transferRequest->to_club_id]);

        $transferRequest->update(['status' => 'approved', 'responded_at' => now()]);

        $this->notifyTransferResult($transferRequest, true);

        $name = $archer->user?->name ?? 'The archer';
        return back()->with('success', "{$name}'s transfer has been approved.");
    }

    public function declineTransfer(ClubTransferRequest $transferRequest): RedirectResponse
    {
        $this->authorizeClubAdmin($transferRequest->to_club_id);

        if (! $transferRequest->isPending()) {
            return back()->with('error', 'That request is no longer pending.');
        }

        $transferRequest->update(['status' => 'declined', 'responded_at' => now()]);

        $this->notifyTransferResult($transferRequest, false);

        return back()->with('success', 'Transfer request declined.');
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

    /** Admins, the club admin of the member's primary club, or the member themself. */
    private function authorizeSelfOrAdmin(Archer|Coach $member): void
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return;
        }
        if ($user->role === 'club_admin' && $member->club_id && (int) $user->club_id === (int) $member->club_id) {
            return;
        }
        if ($member->user_id === $user->id) {
            return;
        }
        abort(403);
    }

    private function notifyTransferResult(ClubTransferRequest $transfer, bool $approved): void
    {
        $email = $transfer->archer?->user?->email;
        if (! $email) {
            return;
        }
        try {
            Mail::to($email)->send(new ClubTransferResultMail($transfer, $approved));
        } catch (\Throwable $e) {
            Log::error('Failed to send transfer-result email: ' . $e->getMessage());
        }
    }
}
