<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArcherController extends Controller
{
    public function index(): View
    {
        $archers = Archer::with('user', 'club', 'stateTeam')
            ->orderBy('ref_no')
            ->paginate(20);

        return view('archers.index', compact('archers'));
    }

    public function create(): View
    {
        $clubs      = Club::where('active', true)->orderBy('name')->get();
        $stateTeams = \App\Models\StateTeam::where('active', true)->orderBy('name')->get();
        $states     = Archer::MALAYSIAN_STATES;
        $divisions  = Archer::DIVISIONS;

        return view('archers.create', compact('clubs', 'stateTeams', 'states', 'divisions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender'        => ['required', 'in:male,female'],
            'phone'         => ['required', 'string', 'max:20'],
            'team'          => ['nullable', 'string', 'max:100'],
            'hand'          => ['required', 'in:right,left'],
            'state'         => ['required', 'string', 'max:100'],
            'country'       => ['required', 'string', 'max:100'],
            'address_line'  => ['required', 'string', 'max:500'],
            'postcode'      => ['nullable', 'string', 'max:10'],
            'address_state' => ['nullable', 'string', 'max:100'],
            'club_id'       => ['nullable', 'exists:clubs,id'],
            'new_club_name' => ['nullable', 'string', 'max:150'],
            'divisions'      => ['nullable', 'array'],
            'divisions.*'    => ['in:Recurve,Compound,Barebow,Traditional'],
            'notes'          => ['nullable', 'string'],
            'photo'          => ['nullable', 'file', 'mimes:bmp,jpg,jpeg,webp', 'max:2048'],
            'arrow_type'     => ['nullable', 'string', 'max:100'],
            'arrow_size'     => ['nullable', 'string', 'max:100'],
            'arrow_length'   => ['nullable', 'numeric', 'min:0', 'max:999'],
            'limb_type'      => ['nullable', 'string', 'max:100'],
            'limb_length'    => ['nullable', 'numeric', 'min:0', 'max:999'],
            'limb_poundage'             => ['nullable', 'numeric', 'min:0', 'max:999'],
            'actual_poundage'           => ['nullable', 'numeric', 'min:0', 'max:999'],
            'classification'            => ['required', 'in:U12,U15,U18,Open'],
            'pb_unofficial_36_score'    => ['nullable', 'integer', 'min:0', 'max:9999'],
            'pb_unofficial_36_date'     => ['nullable', 'date'],
            'pb_unofficial_72_score'    => ['nullable', 'integer', 'min:0', 'max:9999'],
            'pb_unofficial_72_date'     => ['nullable', 'date'],
            'pb_official_36_score'      => ['nullable', 'integer', 'min:0', 'max:9999'],
            'pb_official_36_date'       => ['nullable', 'date'],
            'pb_official_36_tournament' => ['nullable', 'string', 'max:200'],
            'pb_official_72_score'      => ['nullable', 'integer', 'min:0', 'max:9999'],
            'pb_official_72_date'       => ['nullable', 'date'],
            'pb_official_72_tournament' => ['nullable', 'string', 'max:200'],
            'password'                  => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation'     => ['required'],
            // Extended fields
            'mareos_id'                 => ['nullable', 'string', 'max:100', 'unique:archers,mareos_id'],
            'wareos_id'                 => ['nullable', 'string', 'max:100', 'unique:archers,wareos_id'],
            'division'                  => ['required', 'in:Recurve,Compound,Barebow,Traditional'],
            'para_archery'              => ['required', 'boolean'],
            'state_team_id'             => ['nullable', 'exists:state_teams,id'],
            'state_team'                => ['nullable', 'string', 'max:100'],
            'national_team'             => ['nullable', 'string', 'max:50'],
            'nric'                      => ['required', 'digits:12'],
            'passport_number'           => ['nullable', 'string', 'max:20'],
            'passport_expiry_date'      => ['nullable', 'date'],
            'place_of_birth'            => ['required', 'string', 'max:200'],
            'next_of_kin_name'          => ['nullable', 'string', 'max:200'],
            'next_of_kin_relationship'  => ['nullable', 'string', 'max:100'],
            'next_of_kin_email'         => ['nullable', 'email', 'max:200'],
            'next_of_kin_phone'         => ['nullable', 'string', 'max:20'],
            'school'                    => ['nullable', 'string', 'max:200'],
            'school_address'            => ['nullable', 'string'],
            'school_postcode'           => ['nullable', 'string', 'max:10'],
            'school_state'              => ['nullable', 'string', 'max:100'],
            'status'                    => ['required', 'in:active,no_longer_active,injury'],
            'injury_date'               => ['nullable', 'date', 'required_if:status,injury'],
            'injury_type'               => ['nullable', 'string', 'max:200'],
            'injury_return_date'        => ['nullable', 'date'],
        ]);

        $archer = DB::transaction(function () use ($validated, $request) {
            $clubId = $this->resolveClub($validated);

            $existingUser = User::where('email', $validated['email'])->first();
            if ($existingUser) {
                if ($existingUser->archer) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'email' => 'This email already has an archer profile.',
                    ]);
                }
                $user = $existingUser;
                $user->update(['password' => Hash::make($validated['password'])]);
            } else {
                $user = User::create([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role'     => 'archer',
                    'club_id'  => $clubId,
                ]);
            }

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('archers', 'public');
            }

            return Archer::create([
                'user_id'       => $user->id,
                'club_id'       => $clubId,
                'date_of_birth' => $validated['date_of_birth'],
                'gender'        => $validated['gender'],
                'phone'         => $validated['phone'] ?? null,
                'team'          => $validated['team'] ?? null,
                'hand'          => $validated['hand'] ?? null,
                'state'         => $validated['state'] ?? null,
                'country'       => $validated['country'],
                'address_line'  => $validated['address_line'] ?? null,
                'postcode'      => $validated['postcode'] ?? null,
                'address_state' => $validated['address_state'] ?? null,
                'divisions'      => $validated['divisions'] ?? [],
                'photo'          => $photoPath,
                'notes'          => $validated['notes'] ?? null,
                'arrow_type'     => $validated['arrow_type']     ?? null,
                'arrow_size'     => $validated['arrow_size']     ?? null,
                'arrow_length'   => $validated['arrow_length']   ?? null,
                'limb_type'      => $validated['limb_type']      ?? null,
                'limb_length'    => $validated['limb_length']    ?? null,
                'limb_poundage'             => $validated['limb_poundage']             ?? null,
                'actual_poundage'           => $validated['actual_poundage']           ?? null,
                'classification'            => $validated['classification']            ?? null,
                'pb_unofficial_36_score'    => $validated['pb_unofficial_36_score']    ?? null,
                'pb_unofficial_36_date'     => $validated['pb_unofficial_36_date']     ?? null,
                'pb_unofficial_72_score'    => $validated['pb_unofficial_72_score']    ?? null,
                'pb_unofficial_72_date'     => $validated['pb_unofficial_72_date']     ?? null,
                'pb_official_36_score'      => $validated['pb_official_36_score']      ?? null,
                'pb_official_36_date'       => $validated['pb_official_36_date']       ?? null,
                'pb_official_36_tournament' => $validated['pb_official_36_tournament'] ?? null,
                'pb_official_72_score'      => $validated['pb_official_72_score']      ?? null,
                'pb_official_72_date'       => $validated['pb_official_72_date']       ?? null,
                'pb_official_72_tournament' => $validated['pb_official_72_tournament'] ?? null,
                // Extended fields
                'mareos_id'                => $validated['mareos_id']               ?? null,
                'wareos_id'                => $validated['wareos_id']               ?? null,
                'division'                 => $validated['division']                ?? null,
                'para_archery'             => $validated['para_archery']            ?? false,
                'state_team_id'            => $validated['state_team_id']           ?? null,
                'state_team'               => $validated['state_team']              ?? null,
                'national_team'            => $validated['national_team']           ?? 'No',
                'nric'                     => $validated['nric']                    ?? null,
                'passport_number'          => $validated['passport_number']         ?? null,
                'passport_expiry_date'     => $validated['passport_expiry_date']    ?? null,
                'place_of_birth'           => $validated['place_of_birth']          ?? null,
                'next_of_kin_name'         => $validated['next_of_kin_name']        ?? null,
                'next_of_kin_relationship' => $validated['next_of_kin_relationship'] ?? null,
                'next_of_kin_email'        => $validated['next_of_kin_email']       ?? null,
                'next_of_kin_phone'        => $validated['next_of_kin_phone']       ?? null,
                'school'                   => $validated['school']                  ?? null,
                'school_address'           => $validated['school_address']          ?? null,
                'school_postcode'          => $validated['school_postcode']         ?? null,
                'school_state'             => $validated['school_state']            ?? null,
                'status'                   => $validated['status']                  ?? 'active',
                'injury_date'              => ($validated['status'] ?? '') === 'injury' ? ($validated['injury_date']        ?? null) : null,
                'injury_type'              => ($validated['status'] ?? '') === 'injury' ? ($validated['injury_type']        ?? null) : null,
                'injury_return_date'       => ($validated['status'] ?? '') === 'injury' ? ($validated['injury_return_date'] ?? null) : null,
            ]);
        });

        return redirect()->route('archers.show', $archer)
            ->with('success', "Archer {$archer->ref_no} created successfully.");
    }

    public function show(Archer $archer): View
    {
        $user = auth()->user();
        if ($user->role === 'archer' && $user->archer?->id !== $archer->id) {
            abort(403, 'You can only view your own profile.');
        }
        if ($user->role === 'coach') {
            $isAssigned = $user->coach?->archers()->where('archers.id', $archer->id)->exists();
            if (! $isAssigned) {
                abort(403, 'You can only view profiles of your assigned archers.');
            }
        }

        $archer->load('user', 'club', 'stateTeam');

        $trainingSessions = $archer->trainingSessions()
            ->with(['coach.user', 'roundType'])
            ->orderByDesc('date')
            ->get();

        return view('archers.show', compact('archer', 'trainingSessions'));
    }

    public function edit(Archer $archer): View
    {
        $user = auth()->user();
        if ($user->role === 'archer' && $user->archer?->id !== $archer->id) {
            abort(403);
        }

        $clubs      = Club::where('active', true)->orderBy('name')->get();
        $stateTeams = \App\Models\StateTeam::where('active', true)->orderBy('name')->get();
        $states     = Archer::MALAYSIAN_STATES;
        $divisions  = Archer::DIVISIONS;

        return view('archers.edit', compact('archer', 'clubs', 'stateTeams', 'states', 'divisions'));
    }

    public function update(Request $request, Archer $archer): RedirectResponse
    {
        $user = auth()->user();
        if ($user->role === 'archer' && $user->archer?->id !== $archer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email,' . $archer->user_id],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender'        => ['required', 'in:male,female'],
            'phone'         => ['required', 'string', 'max:20'],
            'team'          => ['nullable', 'string', 'max:100'],
            'hand'          => ['required', 'in:right,left'],
            'state'         => ['required', 'string', 'max:100'],
            'country'       => ['required', 'string', 'max:100'],
            'address_line'  => ['required', 'string', 'max:500'],
            'postcode'      => ['nullable', 'string', 'max:10'],
            'address_state' => ['nullable', 'string', 'max:100'],
            'club_id'       => ['nullable', 'exists:clubs,id'],
            'new_club_name' => ['nullable', 'string', 'max:150'],
            'divisions'      => ['nullable', 'array'],
            'divisions.*'    => ['in:Recurve,Compound,Barebow,Traditional'],
            'notes'          => ['nullable', 'string'],
            'photo'          => ['nullable', 'file', 'mimes:bmp,jpg,jpeg,webp', 'max:2048'],
            'arrow_type'     => ['nullable', 'string', 'max:100'],
            'arrow_size'     => ['nullable', 'string', 'max:100'],
            'arrow_length'   => ['nullable', 'numeric', 'min:0', 'max:999'],
            'limb_type'      => ['nullable', 'string', 'max:100'],
            'limb_length'    => ['nullable', 'numeric', 'min:0', 'max:999'],
            'limb_poundage'             => ['nullable', 'numeric', 'min:0', 'max:999'],
            'actual_poundage'           => ['nullable', 'numeric', 'min:0', 'max:999'],
            'classification'            => ['required', 'in:U12,U15,U18,Open'],
            'pb_unofficial_36_score'    => ['nullable', 'integer', 'min:0', 'max:9999'],
            'pb_unofficial_36_date'     => ['nullable', 'date'],
            'pb_unofficial_72_score'    => ['nullable', 'integer', 'min:0', 'max:9999'],
            'pb_unofficial_72_date'     => ['nullable', 'date'],
            'pb_official_36_score'      => ['nullable', 'integer', 'min:0', 'max:9999'],
            'pb_official_36_date'       => ['nullable', 'date'],
            'pb_official_36_tournament' => ['nullable', 'string', 'max:200'],
            'pb_official_72_score'      => ['nullable', 'integer', 'min:0', 'max:9999'],
            'pb_official_72_date'       => ['nullable', 'date'],
            'pb_official_72_tournament' => ['nullable', 'string', 'max:200'],
            'password'                  => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation'     => ['nullable'],
            // Extended fields
            'mareos_id'                 => ['nullable', 'string', 'max:100', 'unique:archers,mareos_id,' . $archer->id],
            'wareos_id'                 => ['nullable', 'string', 'max:100', 'unique:archers,wareos_id,' . $archer->id],
            'division'                  => ['required', 'in:Recurve,Compound,Barebow,Traditional'],
            'para_archery'              => ['required', 'boolean'],
            'state_team_id'             => ['nullable', 'exists:state_teams,id'],
            'state_team'                => ['nullable', 'string', 'max:100'],
            'national_team'             => ['nullable', 'string', 'max:50'],
            'nric'                      => ['required', 'digits:12'],
            'passport_number'           => ['nullable', 'string', 'max:20'],
            'passport_expiry_date'      => ['nullable', 'date'],
            'place_of_birth'            => ['required', 'string', 'max:200'],
            'next_of_kin_name'          => ['nullable', 'string', 'max:200'],
            'next_of_kin_relationship'  => ['nullable', 'string', 'max:100'],
            'next_of_kin_email'         => ['nullable', 'email', 'max:200'],
            'next_of_kin_phone'         => ['nullable', 'string', 'max:20'],
            'school'                    => ['nullable', 'string', 'max:200'],
            'school_address'            => ['nullable', 'string'],
            'school_postcode'           => ['nullable', 'string', 'max:10'],
            'school_state'              => ['nullable', 'string', 'max:100'],
            'status'                    => ['required', 'in:active,no_longer_active,injury'],
            'injury_date'               => ['nullable', 'date', 'required_if:status,injury'],
            'injury_type'               => ['nullable', 'string', 'max:200'],
            'injury_return_date'        => ['nullable', 'date'],
        ]);

        // Resolve the requested club
        $requestedClubId = $this->resolveClub($validated);

        DB::transaction(function () use ($validated, $request, $archer, $requestedClubId) {
            $clubId = $requestedClubId;

            $userUpdate = [
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'club_id' => $clubId,
            ];
            if (!empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }

            $archer->user->update($userUpdate);

            $photoPath = $archer->photo;
            if ($request->hasFile('photo')) {
                if ($archer->photo) {
                    Storage::disk('public')->delete($archer->photo);
                }
                $photoPath = $request->file('photo')->store('archers', 'public');
            }

            $archer->update([
                'club_id'       => $requestedClubId,
                'date_of_birth' => $validated['date_of_birth'],
                'gender'        => $validated['gender'],
                'phone'         => $validated['phone'] ?? null,
                'team'          => $validated['team'] ?? null,
                'hand'          => $validated['hand'] ?? null,
                'state'         => $validated['state'] ?? null,
                'country'       => $validated['country'],
                'address_line'  => $validated['address_line'] ?? null,
                'postcode'      => $validated['postcode'] ?? null,
                'address_state' => $validated['address_state'] ?? null,
                'divisions'      => $validated['divisions'] ?? [],
                'photo'          => $photoPath,
                'notes'          => $validated['notes'] ?? null,
                'arrow_type'     => $validated['arrow_type']     ?? null,
                'arrow_size'     => $validated['arrow_size']     ?? null,
                'arrow_length'   => $validated['arrow_length']   ?? null,
                'limb_type'      => $validated['limb_type']      ?? null,
                'limb_length'    => $validated['limb_length']    ?? null,
                'limb_poundage'             => $validated['limb_poundage']             ?? null,
                'actual_poundage'           => $validated['actual_poundage']           ?? null,
                'classification'            => $validated['classification']            ?? null,
                'pb_unofficial_36_score'    => $validated['pb_unofficial_36_score']    ?? null,
                'pb_unofficial_36_date'     => $validated['pb_unofficial_36_date']     ?? null,
                'pb_unofficial_72_score'    => $validated['pb_unofficial_72_score']    ?? null,
                'pb_unofficial_72_date'     => $validated['pb_unofficial_72_date']     ?? null,
                'pb_official_36_score'      => $validated['pb_official_36_score']      ?? null,
                'pb_official_36_date'       => $validated['pb_official_36_date']       ?? null,
                'pb_official_36_tournament' => $validated['pb_official_36_tournament'] ?? null,
                'pb_official_72_score'      => $validated['pb_official_72_score']      ?? null,
                'pb_official_72_date'       => $validated['pb_official_72_date']       ?? null,
                'pb_official_72_tournament' => $validated['pb_official_72_tournament'] ?? null,
                // Extended fields
                'mareos_id'                => $validated['mareos_id']               ?? null,
                'wareos_id'                => $validated['wareos_id']               ?? null,
                'division'                 => $validated['division']                ?? null,
                'para_archery'             => $validated['para_archery']            ?? false,
                'state_team_id'            => $validated['state_team_id']           ?? null,
                'state_team'               => $validated['state_team']              ?? null,
                'national_team'            => $validated['national_team']           ?? 'No',
                'nric'                     => $validated['nric']                    ?? null,
                'passport_number'          => $validated['passport_number']         ?? null,
                'passport_expiry_date'     => $validated['passport_expiry_date']    ?? null,
                'place_of_birth'           => $validated['place_of_birth']          ?? null,
                'next_of_kin_name'         => $validated['next_of_kin_name']        ?? null,
                'next_of_kin_relationship' => $validated['next_of_kin_relationship'] ?? null,
                'next_of_kin_email'        => $validated['next_of_kin_email']       ?? null,
                'next_of_kin_phone'        => $validated['next_of_kin_phone']       ?? null,
                'school'                   => $validated['school']                  ?? null,
                'school_address'           => $validated['school_address']          ?? null,
                'school_postcode'          => $validated['school_postcode']         ?? null,
                'school_state'             => $validated['school_state']            ?? null,
                'status'                   => $validated['status']                  ?? 'active',
                'injury_date'              => ($validated['status'] ?? '') === 'injury' ? ($validated['injury_date']        ?? null) : null,
                'injury_type'              => ($validated['status'] ?? '') === 'injury' ? ($validated['injury_type']        ?? null) : null,
                'injury_return_date'       => ($validated['status'] ?? '') === 'injury' ? ($validated['injury_return_date'] ?? null) : null,
            ]);
        });

        $redirect = redirect()->route('archers.show', $archer);

        return $redirect->with('success', 'Archer profile updated successfully.');
    }

    public function destroy(Archer $archer): RedirectResponse
    {
        DB::transaction(function () use ($archer) {
            if ($archer->photo) {
                Storage::disk('public')->delete($archer->photo);
            }
            $user = $archer->user;
            $archer->delete();
            $user->delete();
        });

        return redirect()->route('archers.index')
            ->with('success', 'Archer deleted successfully.');
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
