<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\StateTeam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StateTeamController extends Controller
{
    public function index(): View
    {
        $stateTeams = StateTeam::withCount(['archers', 'coaches'])
            ->orderBy('name')
            ->paginate(20);

        $totalTeams   = StateTeam::count();
        $activeTeams  = StateTeam::where('active', true)->count();
        $totalArchers = Archer::whereNotNull('state_team_id')->count();

        return view('state-teams.index', compact('stateTeams', 'totalTeams', 'activeTeams', 'totalArchers'));
    }

    public function create(): View
    {
        return view('state-teams.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:150', 'unique:state_teams,name'],
            'state'               => ['nullable', 'string', 'max:100'],
            'description'         => ['nullable', 'string'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'founded_year'        => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'contact_email'       => ['nullable', 'email', 'max:150'],
            'contact_phone'       => ['nullable', 'string', 'max:30'],
            'website'             => ['nullable', 'url', 'max:200'],
            'address'             => ['nullable', 'string', 'max:300'],
            'logo'                => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'active'              => ['nullable', 'boolean'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('state-teams', 'public');
        }

        $stateTeam = StateTeam::create(array_merge($validated, [
            'logo'   => $logoPath,
            'active' => (bool) ($validated['active'] ?? true),
        ]));

        return redirect()->route('state-teams.show', $stateTeam)
            ->with('success', 'State team created successfully.');
    }

    public function show(StateTeam $stateTeam): View
    {
        $stateTeam->loadCount(['archers', 'coaches']);
        $stateTeam->load([
            'archers' => fn($q) => $q->with('user', 'club')->orderBy('ref_no'),
            'coaches' => fn($q) => $q->with('user', 'club')->orderBy('id'),
        ]);

        return view('state-teams.show', compact('stateTeam'));
    }

    public function edit(StateTeam $stateTeam): View
    {
        return view('state-teams.edit', compact('stateTeam'));
    }

    public function update(Request $request, StateTeam $stateTeam): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:150', 'unique:state_teams,name,' . $stateTeam->id],
            'state'               => ['nullable', 'string', 'max:100'],
            'description'         => ['nullable', 'string'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'founded_year'        => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'contact_email'       => ['nullable', 'email', 'max:150'],
            'contact_phone'       => ['nullable', 'string', 'max:30'],
            'website'             => ['nullable', 'url', 'max:200'],
            'address'             => ['nullable', 'string', 'max:300'],
            'logo'                => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'active'              => ['nullable', 'boolean'],
        ]);

        $logoPath = $stateTeam->logo;
        if ($request->hasFile('logo')) {
            if ($stateTeam->logo) {
                Storage::disk('public')->delete($stateTeam->logo);
            }
            $logoPath = $request->file('logo')->store('state-teams', 'public');
        }

        $stateTeam->update(array_merge($validated, [
            'logo'   => $logoPath,
            'active' => $request->boolean('active'),
        ]));

        return redirect()->route('state-teams.show', $stateTeam)
            ->with('success', 'State team updated successfully.');
    }

    public function destroy(StateTeam $stateTeam): RedirectResponse
    {
        if ($stateTeam->logo) {
            Storage::disk('public')->delete($stateTeam->logo);
        }
        $stateTeam->delete();

        return redirect()->route('state-teams.index')
            ->with('success', 'State team deleted.');
    }
}
