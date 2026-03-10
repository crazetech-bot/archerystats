<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubManagementController extends Controller
{
    public function index(): View
    {
        $clubs = Club::withCount(['archers', 'coaches', 'users'])
            ->orderBy('name')
            ->get();

        $totalClubs   = $clubs->count();
        $activeClubs  = $clubs->where('active', true)->count();
        // Count distinct archers/coaches across all pivot memberships
        $totalArchers = \DB::table('archer_clubs')->distinct('archer_id')->count('archer_id');
        $totalCoaches = \DB::table('coach_clubs')->distinct('coach_id')->count('coach_id');

        return view('admin.clubs.index', compact(
            'clubs', 'totalClubs', 'activeClubs', 'totalArchers', 'totalCoaches'
        ));
    }

    public function show(Club $club): View
    {
        $club->load(['users', 'archers.user', 'coaches.user']);

        $archerCount = $club->archers()->count();
        $coachCount  = $club->coaches()->count();
        $adminUsers  = User::where('club_id', $club->id)
                           ->whereIn('role', ['club_admin', 'super_admin'])
                           ->get();

        return view('admin.clubs.show', compact('club', 'archerCount', 'coachCount', 'adminUsers'));
    }

    public function toggle(Club $club): RedirectResponse
    {
        $club->update(['active' => !$club->active]);

        $status = $club->active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Club \"{$club->name}\" has been {$status}.");
    }

    public function update(Request $request, Club $club): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'slug'     => ['required', 'string', 'max:100', 'alpha_dash',
                           "unique:clubs,slug,{$club->id}"],
            'location' => ['nullable', 'string', 'max:255'],
            'state'    => ['nullable', 'string', 'max:100'],
            'active'   => ['boolean'],
        ]);

        $club->update($validated);

        return redirect()->route('admin.clubs.show', $club)->with('success', 'Club updated.');
    }
}
