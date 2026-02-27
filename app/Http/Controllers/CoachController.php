<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Coach;
use App\Models\StateTeam;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CoachController extends Controller
{
    public function index(): View
    {
        $coaches = Coach::with('user', 'club', 'stateTeam')
            ->withCount('archers')
            ->orderBy('ref_no')
            ->paginate(20);

        return view('coaches.index', compact('coaches'));
    }

    public function create(): View
    {
        $clubs      = Club::where('active', true)->orderBy('name')->get();
        $stateTeams = StateTeam::where('active', true)->orderBy('name')->get();
        $states     = Coach::MALAYSIAN_STATES;

        return view('coaches.create', compact('clubs', 'stateTeams', 'states'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email'],
            'date_of_birth'=> ['nullable', 'date', 'before:today'],
            'gender'       => ['nullable', 'in:male,female'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'state_team_id' => ['nullable', 'exists:state_teams,id'],
            'team'          => ['nullable', 'string', 'max:100'],
            'coaching_level'        => ['nullable', 'string', 'in:' . implode(',', Coach::COACHING_LEVELS)],
            'sports_science_course' => ['nullable', 'string', 'in:' . implode(',', Coach::SPORTS_SCIENCE_COURSES)],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['required', 'string', 'max:100'],
            'address_line'  => ['nullable', 'string', 'max:500'],
            'postcode'      => ['nullable', 'string', 'max:10'],
            'club_id'       => ['nullable', 'exists:clubs,id'],
            'new_club_name' => ['nullable', 'string', 'max:150'],
            'notes'                 => ['nullable', 'string'],
            'photo'                 => ['nullable', 'file', 'mimes:bmp,jpg,jpeg,webp', 'max:2048'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $coach = DB::transaction(function () use ($validated, $request) {
            $clubId = $this->resolveClub($validated);

            $existingUser = User::where('email', $validated['email'])->first();
            if ($existingUser) {
                if ($existingUser->coach) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'email' => 'This email already has a coach profile.',
                    ]);
                }
                $user = $existingUser;
                $user->update(['password' => Hash::make($validated['password'])]);
                if ($user->role === 'archer') {
                    $user->update(['role' => 'coach']);
                }
            } else {
                $user = User::create([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role'     => 'coach',
                    'club_id'  => $clubId,
                ]);
            }

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('coaches', 'public');
            }

            return Coach::create([
                'user_id'       => $user->id,
                'club_id'       => $clubId,
                'state_team_id' => $validated['state_team_id'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender'        => $validated['gender'] ?? null,
                'phone'         => $validated['phone'] ?? null,
                'team'          => $validated['team'] ?? null,
                'coaching_level'        => $validated['coaching_level'] ?? null,
                'sports_science_course' => $validated['sports_science_course'] ?? null,
                'state'         => $validated['state'] ?? null,
                'country'       => $validated['country'],
                'address_line'  => $validated['address_line'] ?? null,
                'postcode'      => $validated['postcode'] ?? null,
                'photo'         => $photoPath,
                'notes'         => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('coaches.show', $coach)
            ->with('success', "Coach {$coach->ref_no} created successfully.");
    }

    public function show(Coach $coach): View
    {
        $coach->load('user', 'club', 'stateTeam');
        return view('coaches.show', compact('coach'));
    }

    public function edit(Coach $coach): View
    {
        $user = auth()->user();
        if ($user->role === 'coach' && $user->coach?->id !== $coach->id) {
            abort(403);
        }

        $clubs      = Club::where('active', true)->orderBy('name')->get();
        $stateTeams = StateTeam::where('active', true)->orderBy('name')->get();
        $states     = Coach::MALAYSIAN_STATES;

        return view('coaches.edit', compact('coach', 'clubs', 'stateTeams', 'states'));
    }

    public function update(Request $request, Coach $coach): RedirectResponse
    {
        $user = auth()->user();
        if ($user->role === 'coach' && $user->coach?->id !== $coach->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email,' . $coach->user_id],
            'date_of_birth'=> ['nullable', 'date', 'before:today'],
            'gender'       => ['nullable', 'in:male,female'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'state_team_id' => ['nullable', 'exists:state_teams,id'],
            'team'          => ['nullable', 'string', 'max:100'],
            'coaching_level'        => ['nullable', 'string', 'in:' . implode(',', Coach::COACHING_LEVELS)],
            'sports_science_course' => ['nullable', 'string', 'in:' . implode(',', Coach::SPORTS_SCIENCE_COURSES)],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['required', 'string', 'max:100'],
            'address_line'  => ['nullable', 'string', 'max:500'],
            'postcode'      => ['nullable', 'string', 'max:10'],
            'club_id'       => ['nullable', 'exists:clubs,id'],
            'new_club_name' => ['nullable', 'string', 'max:150'],
            'notes'                 => ['nullable', 'string'],
            'photo'                 => ['nullable', 'file', 'mimes:bmp,jpg,jpeg,webp', 'max:2048'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable'],
        ]);

        DB::transaction(function () use ($validated, $request, $coach) {
            $clubId = $this->resolveClub($validated);

            $userUpdate = [
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'club_id' => $clubId,
            ];
            if (!empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }

            $coach->user->update($userUpdate);

            $photoPath = $coach->photo;
            if ($request->hasFile('photo')) {
                if ($coach->photo) {
                    Storage::disk('public')->delete($coach->photo);
                }
                $photoPath = $request->file('photo')->store('coaches', 'public');
            }

            $coach->update([
                'club_id'       => $clubId,
                'state_team_id' => $validated['state_team_id'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender'        => $validated['gender'] ?? null,
                'phone'         => $validated['phone'] ?? null,
                'team'          => $validated['team'] ?? null,
                'coaching_level'        => $validated['coaching_level'] ?? null,
                'sports_science_course' => $validated['sports_science_course'] ?? null,
                'state'         => $validated['state'] ?? null,
                'country'       => $validated['country'],
                'address_line'  => $validated['address_line'] ?? null,
                'postcode'      => $validated['postcode'] ?? null,
                'photo'         => $photoPath,
                'notes'         => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('coaches.show', $coach)
            ->with('success', 'Coach profile updated successfully.');
    }

    public function destroy(Coach $coach): RedirectResponse
    {
        DB::transaction(function () use ($coach) {
            if ($coach->photo) {
                Storage::disk('public')->delete($coach->photo);
            }
            $user = $coach->user;
            $coach->delete();
            $user->delete();
        });

        return redirect()->route('coaches.index')
            ->with('success', 'Coach deleted successfully.');
    }

    private function resolveClub(array $validated): ?int
    {
        if (!empty($validated['new_club_name'])) {
            $club = Club::firstOrCreate(
                ['name' => trim($validated['new_club_name'])],
                ['active' => true]
            );
            return $club->id;
        }

        return $validated['club_id'] ?? null;
    }
}
