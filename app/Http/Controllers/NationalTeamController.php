<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\Coach;
use App\Models\User;
use Illuminate\View\View;

class NationalTeamController extends Controller
{
    public function index(): View
    {
        $podium  = Archer::with('user', 'club', 'stateTeam')
                         ->where('national_team', 'Podium')
                         ->orderBy('ref_no')
                         ->get();

        $pelapis = Archer::with('user', 'club', 'stateTeam')
                         ->where('national_team', 'Pelapis Kebangsaan')
                         ->orderBy('ref_no')
                         ->get();

        $para    = Archer::with('user', 'club', 'stateTeam')
                         ->where('national_team', 'PARA')
                         ->orderBy('ref_no')
                         ->get();

        $coaches = Coach::with('user', 'club', 'stateTeam')
                        ->where('national_team', true)
                        ->orderBy('ref_no')
                        ->get();

        $admins  = User::where('role', 'national_team')
                       ->with('club')
                       ->orderBy('name')
                       ->get();

        return view('national-team.index', compact('podium', 'pelapis', 'para', 'coaches', 'admins'));
    }
}
