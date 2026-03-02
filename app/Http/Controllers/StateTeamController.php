<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\Coach;
use App\Models\StateTeam;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StateTeamController extends Controller
{
    // ── helpers ──────────────────────────────────────────────────────────────

    /** Abort 403 if a state_admin tries to access a team they don't own. */
    private function authorizeTeam(StateTeam $stateTeam): void
    {
        if (auth()->user()->role === 'super_admin') {
            return;
        }
        if ($stateTeam->admin_user_id !== auth()->id()) {
            abort(403, 'You can only manage your own state team.');
        }
    }

    /** Abort 403 for actions only super_admin may perform. */
    private function superAdminOnly(): void
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Only super admins can perform this action.');
        }
    }

    // ── CRUD ─────────────────────────────────────────────────────────────────

    public function index(): View|RedirectResponse
    {
        // state_admin: redirect to their own team immediately
        if (auth()->user()->role === 'state_admin') {
            $team = auth()->user()->managedStateTeam;
            if ($team) {
                return redirect()->route('state-teams.show', $team);
            }
        }

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
        $this->superAdminOnly();
        return view('state-teams.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->superAdminOnly();

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
            'logo'                => ['nullable', 'file', 'mimes:png,bmp,jpg,jpeg,webp', 'max:2048'],
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
        $this->authorizeTeam($stateTeam);

        $stateTeam->loadCount(['archers', 'coaches']);
        $stateTeam->load([
            'admin',
            'archers' => fn($q) => $q->with('user', 'club')->orderBy('ref_no'),
            'coaches' => fn($q) => $q->with('user', 'club')->orderBy('id'),
        ]);

        // Coaches eligible to be appointed (all users with is_coach flag or coach role)
        $coachUsers = User::where(fn($q) => $q->where('role', 'coach')->orWhere('is_coach', true))
            ->with('coach')
            ->orderBy('name')
            ->get();

        return view('state-teams.show', compact('stateTeam', 'coachUsers'));
    }

    public function appointAdmin(Request $request, StateTeam $stateTeam): RedirectResponse
    {
        $this->authorizeTeam($stateTeam);

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($request->user_id);

        if (! $user->is_coach && $user->role !== 'coach') {
            return back()->with('error', 'Only coaches can be appointed as state team admin.');
        }

        // Promote to state_admin while preserving coach flag
        $user->update([
            'role'     => 'state_admin',
            'is_coach' => true,
        ]);

        // Ensure coach profile exists
        if (! $user->coach) {
            Coach::create(['user_id' => $user->id]);
        }

        // Record as this team's designated admin
        $stateTeam->update(['admin_user_id' => $user->id]);

        return redirect()->route('state-teams.show', $stateTeam)
            ->with('success', "{$user->name} has been appointed as admin of {$stateTeam->name}.");
    }

    public function edit(StateTeam $stateTeam): View
    {
        $this->authorizeTeam($stateTeam);
        return view('state-teams.edit', compact('stateTeam'));
    }

    public function update(Request $request, StateTeam $stateTeam): RedirectResponse
    {
        $this->authorizeTeam($stateTeam);

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
            'logo'                => ['nullable', 'file', 'mimes:png,bmp,jpg,jpeg,webp', 'max:2048'],
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
        $this->superAdminOnly();

        if ($stateTeam->logo) {
            Storage::disk('public')->delete($stateTeam->logo);
        }
        $stateTeam->delete();

        return redirect()->route('state-teams.index')
            ->with('success', 'State team deleted.');
    }
}
