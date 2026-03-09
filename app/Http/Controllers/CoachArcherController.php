<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\Club;
use App\Models\Coach;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('coaches.archers.index', compact(
            'coach', 'available', 'isNationalTeamContext',
            'assignedArchers', 'totalAssigned',
            'clubs', 'states', 'nationalTeamOptions'
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

    public function destroy(Coach $coach, Archer $archer): RedirectResponse
    {
        if (! auth()->user()->isClubAdmin()) {
            abort(403, 'Only club administrators can remove archers from a coach roster.');
        }

        $coach->archers()->detach($archer->id);

        return back()->with('success', "Archer {$archer->ref_no} removed from coach roster.");
    }
}
