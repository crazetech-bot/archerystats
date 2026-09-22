<?php

namespace App\Http\Controllers;

use App\Mail\CoachArcherInvitationMail;
use App\Models\Archer;
use App\Models\Club;
use App\Models\Coach;
use App\Models\CoachArcherInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CoachArcherController extends Controller
{
    public function index(Coach $coach, Request $request): View
    {
        $userRole = auth()->user()->role;
        $isNationalTeamContext = $userRole === 'national_team'
            || ($userRole === 'coach' && $coach->national_team);

        // ── Filterable assigned-archer query ──────────────────────────────
        $assignedQuery = Archer::with('user', 'club')
            ->whereHas('coaches', fn ($q) => $q->where('coaches.id', $coach->id));

        if ($search = trim($request->get('search', ''))) {
            $assignedQuery->where(function ($q) use ($search) {
                $q->where('mareos_id', 'like', "%{$search}%")
                  ->orWhere('ref_no', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }
        if ($clubId = $request->get('club_id')) {
            $assignedQuery->where('club_id', $clubId);
        }
        if ($state = $request->get('state')) {
            $assignedQuery->where('state', $state);
        }
        if ($request->filled('national_team')) {
            $assignedQuery->where('national_team', $request->get('national_team'));
        }

        $assignedArchers = $assignedQuery->orderBy('ref_no')->get();
        $totalAssigned   = $coach->archers()->count();

        // ── Available archers for assignment form ─────────────────────────
        $assignedIds    = $coach->archers()->pluck('archers.id');
        $availableQuery = Archer::with('user', 'club')->whereNotIn('id', $assignedIds);

        if ($isNationalTeamContext) {
            $availableQuery->where('national_team', '!=', 'No')->whereNotNull('national_team');
        } elseif ($userRole === 'coach') {
            $availableQuery->where('club_id', $coach->club_id);
        } elseif ($userRole === 'club_admin') {
            $availableQuery->where('club_id', auth()->user()->club_id);
        }

        $available           = $availableQuery->orderBy('ref_no')->get();
        $clubs               = Club::where('active', true)->orderBy('name')->get();
        $states              = Archer::MALAYSIAN_STATES;
        $nationalTeamOptions = array_filter(Archer::NATIONAL_TEAM_OPTIONS, fn ($o) => $o !== 'No');

        $pendingInvitations = CoachArcherInvitation::where('coach_id', $coach->id)
            ->where('status', 'pending')->where('expires_at', '>', now())
            ->with('archer.user')
            ->orderByDesc('created_at')->get();

        return view('coaches.archers.index', compact(
            'coach', 'available', 'isNationalTeamContext',
            'assignedArchers', 'totalAssigned',
            'clubs', 'states', 'nationalTeamOptions', 'pendingInvitations'
        ));
    }

    public function store(Coach $coach, Request $request): RedirectResponse
    {
        $request->validate([
            'archer_id' => ['required', 'exists:archers,id'],
        ]);

        $archer = Archer::findOrFail($request->archer_id);

        // Already assigned
        if ($coach->archers()->where('archers.id', $archer->id)->exists()) {
            return back()->withErrors(['archer_id' => 'This archer is already assigned to this coach.']);
        }

        // National team context restriction (national_team role OR national team coach)
        $userRole = auth()->user()->role;
        $isNationalTeamContext = $userRole === 'national_team'
            || ($userRole === 'coach' && $coach->national_team);

        if ($isNationalTeamContext && (empty($archer->national_team) || $archer->national_team === 'No')) {
            return back()->withErrors(['archer_id' => 'Only archers with a national team status (Podium, Pelapis Kebangsaan, or PARA) can be assigned here.']);
        }

        $coach->archers()->syncWithoutDetaching([$archer->id]);

        return back()->with('success', "Archer {$archer->ref_no} assigned to coach.");
    }

    /** Send an assignment invitation instead of assigning directly. */
    public function invite(Coach $coach, Request $request): RedirectResponse
    {
        $request->validate([
            'archer_id' => ['required', 'exists:archers,id'],
        ]);

        $archer = Archer::findOrFail($request->archer_id);

        if ($coach->archers()->where('archers.id', $archer->id)->exists()) {
            return back()->withErrors(['archer_id' => 'This archer is already assigned to this coach.']);
        }

        $userRole = auth()->user()->role;
        $isNationalTeamContext = $userRole === 'national_team'
            || ($userRole === 'coach' && $coach->national_team);

        if ($isNationalTeamContext && (empty($archer->national_team) || $archer->national_team === 'No')) {
            return back()->withErrors(['archer_id' => 'Only archers with a national team status can be invited here.']);
        }

        $email = $archer->user?->email;
        if (! $email) {
            return back()->withErrors(['archer_id' => 'This archer has no login account to email — use direct assign instead.']);
        }

        // Unique (coach_id, archer_id): re-invite resets the row after a decline/expiry.
        $existing = CoachArcherInvitation::where('coach_id', $coach->id)
            ->where('archer_id', $archer->id)->first();

        if ($existing && $existing->isPending()) {
            return back()->withErrors(['archer_id' => 'An invitation to this archer is already pending.']);
        }

        $invitation = CoachArcherInvitation::updateOrCreate(
            ['coach_id' => $coach->id, 'archer_id' => $archer->id],
            [
                'token'        => Str::random(64),
                'status'       => 'pending',
                'responded_at' => null,
                'expires_at'   => now()->addDays(7),
            ]
        );

        try {
            Mail::to($email)->send(new CoachArcherInvitationMail($invitation));
        } catch (\Throwable $e) {
            Log::error('Failed to send coach-archer invitation email: ' . $e->getMessage());
        }

        return back()->with('success', "Invitation sent to archer {$archer->ref_no}.");
    }

    public function destroy(Coach $coach, Archer $archer): RedirectResponse
    {
        if (! auth()->user()->isClubAdmin()) {
            abort(403, 'Only club administrators can remove archers from a coach roster.');
        }

        $coach->archers()->detach($archer->id);

        return back()->with('success', "Archer {$archer->ref_no} removed from coach roster.");
    }
}
