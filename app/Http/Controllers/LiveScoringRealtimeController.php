<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\ArcherySession;
use App\Models\Club;
use App\Models\StateTeam;
use App\Services\LiveScoringFormatterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveScoringRealtimeController extends Controller
{
    public function index(Request $request): View
    {
        $user      = auth()->user();
        $date      = $request->input('date', today()->toDateString());
        $sessions  = $this->fetchSessions($request, $date);
        $scoreboard = app(LiveScoringFormatterService::class)->formatScoreboard($sessions);

        // Filters for national_team / super_admin UI
        $clubs       = Club::orderBy('name')->get(['id', 'name']);
        $stateTeams  = StateTeam::orderBy('state')->get(['id', 'state', 'name']);
        $ntOptions   = array_filter(Archer::NATIONAL_TEAM_OPTIONS ?? [], fn($o) => $o !== 'No');

        return view('live-scoring.realtime', compact(
            'scoreboard', 'user', 'date',
            'clubs', 'stateTeams', 'ntOptions',
        ));
    }

    public function data(Request $request): JsonResponse
    {
        $date      = $request->input('date', today()->toDateString());
        $sessions  = $this->fetchSessions($request, $date);
        $scoreboard = app(LiveScoringFormatterService::class)->formatScoreboard($sessions);

        return response()->json(['rows' => $scoreboard]);
    }

    // -------------------------------------------------------------------------

    private function fetchSessions(Request $request, string $date)
    {
        $user  = auth()->user();
        $query = ArcherySession::with(['archer.club', 'archer.stateTeam', 'roundType', 'score.ends'])
            ->whereHas('score')
            ->whereDate('date', $date);

        if ($user->role === 'club_admin') {
            $query->whereHas('archer', fn($q) => $q->where('club_id', $user->club_id));

        } elseif ($user->role === 'state_admin') {
            $stateTeam = $user->managedStateTeam;
            if ($stateTeam) {
                $query->whereHas('archer', fn($q) => $q->where('state_team_id', $stateTeam->id));
            } else {
                $query->whereRaw('1 = 0'); // no managed team → empty board
            }

        } else {
            // national_team + super_admin: optional filters
            if ($clubId = $request->input('club_id')) {
                $query->whereHas('archer', fn($q) => $q->where('club_id', $clubId));
            }
            if ($stateTeamId = $request->input('state_team_id')) {
                $query->whereHas('archer', fn($q) => $q->where('state_team_id', $stateTeamId));
            }
            if ($nt = $request->input('national_team_filter')) {
                $query->whereHas('archer', fn($q) => $q->where('national_team', $nt));
            }
        }

        return $query->get();
    }
}
