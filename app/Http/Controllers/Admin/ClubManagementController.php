<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archer;
use App\Models\Club;
use App\Models\Coach;
use App\Models\User;
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
        $totalArchers = Archer::withoutGlobalScopes()->count();
        $totalCoaches = Coach::withoutGlobalScopes()->count();

        return view('admin.clubs.index', compact(
            'clubs', 'totalClubs', 'activeClubs', 'totalArchers', 'totalCoaches'
        ));
    }

    public function show(Club $club): View
    {
        $club->load(['users', 'archers.user', 'coaches.user']);

        $archerCount  = Archer::withoutGlobalScopes()->where('club_id', $club->id)->count();
        $coachCount   = Coach::withoutGlobalScopes()->where('club_id', $club->id)->count();
        $adminUsers   = User::where('club_id', $club->id)
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
