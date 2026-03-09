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
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArcherController extends Controller
{
    public function index(Request $request): View
    {
        $clubs               = Club::where('active', true)->orderBy('name')->get();
        $states              = Archer::MALAYSIAN_STATES;
        $nationalTeamOptions = array_filter(Archer::NATIONAL_TEAM_OPTIONS, fn ($o) => $o !== 'No');

        $auth  = auth()->user();
        $query = Archer::with('user', 'club', 'stateTeam');

        // Club admins only see archers from their own club
        if ($auth->role === 'club_admin' && $auth->club_id) {
            $query->where('club_id', $auth->club_id);
        }

        if ($search = trim($request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('mareos_id', 'like', "%{$search}%")
                  ->orWhere('ref_no', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($clubId = $request->get('club_id')) {
            $query->where('club_id', $clubId);
        }

        if ($state = $request->get('state')) {
            $query->where('state', $state);
        }

        if ($request->filled('national_team')) {
            $query->where('national_team', $request->get('national_team'));
        }

        $archers      = $query->orderBy('ref_no')->paginate(20)->withQueryString();
        $totalArchers = Archer::count();

        return view('archers.index', compact('archers', 'totalArchers', 'clubs', 'states', 'nationalTeamOptions'));
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
            'photo'          => ['nullable', 'file', 'mimes:png,bmp,jpg,jpeg,webp', 'max:2048'],
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
            'wheelchair'                => ['nullable', 'boolean'],
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
                'wheelchair'              => ($validated['para_archery'] ?? false) ? ($validated['wheelchair'] ?? null) : null,
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

    // ── CSV Import ────────────────────────────────────────────────────────────

    public function importForm(): View
    {
        return view('archers.import');
    }

    public function importTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $columns = [
            'name', 'email', 'nric', 'date_of_birth', 'gender', 'place_of_birth',
            'hand', 'classification', 'state', 'country',
            'phone', 'club_name', 'division', 'divisions',
            'state_team', 'national_team', 'mareos_id', 'wareos_id',
            'address_line', 'postcode', 'address_state', 'notes',
            'status', 'para_archery',
        ];

        $example = [
            'Ahmad Fariz bin Zakaria', 'ahmad.fariz@example.com', '030414071234', '2003-04-14',
            'male', 'Kuala Lumpur',
            'right', 'Open', 'Selangor', 'Malaysia',
            '0123456789', 'Selangor Archery Club', 'Recurve', 'Recurve;Barebow',
            'Selangor', 'No', '', '',
            'No 12 Jalan Utama', '47500', 'Selangor', '',
            'active', '0',
        ];

        return response()->stream(function () use ($columns, $example) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            fputcsv($out, $example);
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="archers_template.csv"',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle  = fopen($request->file('csv_file')->getRealPath(), 'r');
        $headers = array_map('trim', fgetcsv($handle));

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 1;

        $validClassifications = ['U12', 'U15', 'U18', 'Open'];
        $validGenders         = ['male', 'female'];
        $validHands           = ['right', 'left'];
        $validDivisions       = ['Recurve', 'Compound', 'Barebow', 'Traditional'];
        $validStatuses        = ['active', 'no_longer_active', 'injury'];

        while (($data = fgetcsv($handle)) !== false) {
            $row++;

            if (count($data) !== count($headers)) {
                $errors[] = "Row {$row}: column count mismatch — skipped.";
                $skipped++;
                continue;
            }

            $r = array_combine($headers, array_map('trim', $data));

            // Required fields
            foreach (['name', 'email', 'nric', 'date_of_birth', 'gender', 'place_of_birth', 'hand', 'classification', 'state', 'country'] as $field) {
                if (empty($r[$field])) {
                    $errors[] = "Row {$row}: '{$field}' is required — skipped.";
                    $skipped++;
                    continue 2;
                }
            }

            if (!filter_var($r['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$row}: invalid email '{$r['email']}' — skipped.";
                $skipped++;
                continue;
            }

            if (User::where('email', $r['email'])->whereHas('archer')->exists()) {
                $errors[] = "Row {$row}: email '{$r['email']}' already has an archer profile — skipped.";
                $skipped++;
                continue;
            }

            if (!in_array($r['gender'], $validGenders)) {
                $errors[] = "Row {$row}: gender must be 'male' or 'female' — skipped.";
                $skipped++;
                continue;
            }

            if (!in_array($r['hand'], $validHands)) {
                $errors[] = "Row {$row}: hand must be 'right' or 'left' — skipped.";
                $skipped++;
                continue;
            }

            if (!in_array($r['classification'], $validClassifications)) {
                $errors[] = "Row {$row}: classification must be one of U12, U15, U18, Open — skipped.";
                $skipped++;
                continue;
            }

            // Parse date
            try {
                $dob = \Carbon\Carbon::parse($r['date_of_birth'])->toDateString();
            } catch (\Exception $e) {
                $errors[] = "Row {$row}: invalid date_of_birth '{$r['date_of_birth']}' (use YYYY-MM-DD) — skipped.";
                $skipped++;
                continue;
            }

            // Club lookup
            $clubId = null;
            if (!empty($r['club_name'])) {
                $club = \App\Models\Club::whereRaw('LOWER(name) = ?', [strtolower($r['club_name'])])->first();
                if (!$club) {
                    $errors[] = "Row {$row}: club '{$r['club_name']}' not found — archer imported without club.";
                }
                $clubId = $club?->id;
            }

            // Divisions
            $divisions = [];
            if (!empty($r['divisions'])) {
                $divisions = array_filter(
                    array_map('trim', explode(';', $r['divisions'])),
                    fn($d) => in_array($d, $validDivisions)
                );
                $divisions = array_values($divisions);
            }

            // Primary division
            $division = in_array($r['division'] ?? '', $validDivisions) ? $r['division'] : null;

            // Status
            $status = in_array($r['status'] ?? '', $validStatuses) ? $r['status'] : 'active';

            DB::transaction(function () use ($r, $dob, $clubId, $divisions, $division, $status) {
                $existingUser = User::where('email', $r['email'])->first();
                if ($existingUser) {
                    $user = $existingUser;
                    $user->update(['club_id' => $clubId ?? $user->club_id]);
                } else {
                    $user = User::create([
                        'name'     => $r['name'],
                        'email'    => $r['email'],
                        'password' => Hash::make(Str::random(24)),
                        'role'     => 'archer',
                        'club_id'  => $clubId,
                    ]);
                }

                Archer::create([
                    'user_id'        => $user->id,
                    'club_id'        => $clubId,
                    'date_of_birth'  => $dob,
                    'gender'         => $r['gender'],
                    'nric'           => $r['nric'] ?: null,
                    'place_of_birth' => $r['place_of_birth'],
                    'phone'          => $r['phone'] ?: null,
                    'hand'           => $r['hand'],
                    'classification' => $r['classification'],
                    'state'          => $r['state'],
                    'country'        => $r['country'],
                    'address_line'   => $r['address_line'] ?: null,
                    'postcode'       => $r['postcode'] ?: null,
                    'address_state'  => $r['address_state'] ?: null,
                    'division'       => $division,
                    'divisions'      => $divisions,
                    'state_team'     => $r['state_team'] ?: null,
                    'national_team'  => $r['national_team'] ?: 'No',
                    'mareos_id'      => $r['mareos_id'] ?: null,
                    'wareos_id'      => $r['wareos_id'] ?: null,
                    'notes'          => $r['notes'] ?: null,
                    'status'         => $status,
                    'para_archery'   => ($r['para_archery'] ?? '0') === '1',
                    'active'         => true,
                ]);
            });

            $imported++;
        }

        fclose($handle);

        $msg = "Import complete: {$imported} archer(s) imported";
        if ($skipped) {
            $msg .= ", {$skipped} skipped";
        }

        return redirect()->route('archers.import')
            ->with('import_success', $msg)
            ->with('import_errors', $errors);
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
            'photo'          => ['nullable', 'file', 'mimes:png,bmp,jpg,jpeg,webp', 'max:2048'],
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
            'wheelchair'                => ['nullable', 'boolean'],
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
                'wheelchair'              => ($validated['para_archery'] ?? false) ? ($validated['wheelchair'] ?? null) : null,
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

    public function updateNationalTeam(Request $request, Archer $archer): RedirectResponse
    {
        $validated = $request->validate([
            'national_team' => ['required', 'in:No,Podium,Pelapis Kebangsaan,PARA'],
        ]);

        $archer->update(['national_team' => $validated['national_team']]);

        return redirect()->back()->with('success', 'National Team status updated for ' . $archer->full_name . '.');
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
