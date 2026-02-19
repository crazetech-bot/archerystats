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
        $archers = Archer::with('user', 'club')
            ->orderBy('ref_no')
            ->paginate(20);

        return view('archers.index', compact('archers'));
    }

    public function create(): View
    {
        $clubs     = Club::where('active', true)->orderBy('name')->get();
        $states    = Archer::MALAYSIAN_STATES;
        $divisions = Archer::DIVISIONS;

        return view('archers.create', compact('clubs', 'states', 'divisions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender'        => ['required', 'in:male,female'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'team'          => ['nullable', 'string', 'max:100'],
            'hand'          => ['nullable', 'in:right,left'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['required', 'string', 'max:100'],
            'address_line'  => ['nullable', 'string', 'max:500'],
            'postcode'      => ['nullable', 'string', 'max:10'],
            'address_state' => ['nullable', 'string', 'max:100'],
            'club_id'       => ['nullable', 'exists:clubs,id'],
            'new_club_name' => ['nullable', 'string', 'max:150'],
            'divisions'     => ['nullable', 'array'],
            'divisions.*'   => ['in:Recurve,Compound,Barebow,Traditional'],
            'notes'         => ['nullable', 'string'],
            'photo'         => ['nullable', 'file', 'mimes:bmp,jpg,jpeg,webp', 'max:2048'],
        ]);

        $archer = DB::transaction(function () use ($validated, $request) {
            $clubId = $this->resolveClub($validated);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make(str()->random(16)),
                'role'     => 'archer',
                'club_id'  => $clubId,
            ]);

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
                'divisions'     => $validated['divisions'] ?? [],
                'photo'         => $photoPath,
                'notes'         => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('archers.show', $archer)
            ->with('success', "Archer {$archer->ref_no} created successfully.");
    }

    public function show(Archer $archer): View
    {
        $archer->load('user', 'club');
        return view('archers.show', compact('archer'));
    }

    public function edit(Archer $archer): View
    {
        $clubs     = Club::where('active', true)->orderBy('name')->get();
        $states    = Archer::MALAYSIAN_STATES;
        $divisions = Archer::DIVISIONS;

        return view('archers.edit', compact('archer', 'clubs', 'states', 'divisions'));
    }

    public function update(Request $request, Archer $archer): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email,' . $archer->user_id],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender'        => ['required', 'in:male,female'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'team'          => ['nullable', 'string', 'max:100'],
            'hand'          => ['nullable', 'in:right,left'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['required', 'string', 'max:100'],
            'address_line'  => ['nullable', 'string', 'max:500'],
            'postcode'      => ['nullable', 'string', 'max:10'],
            'address_state' => ['nullable', 'string', 'max:100'],
            'club_id'       => ['nullable', 'exists:clubs,id'],
            'new_club_name' => ['nullable', 'string', 'max:150'],
            'divisions'     => ['nullable', 'array'],
            'divisions.*'   => ['in:Recurve,Compound,Barebow,Traditional'],
            'notes'         => ['nullable', 'string'],
            'photo'         => ['nullable', 'file', 'mimes:bmp,jpg,jpeg,webp', 'max:2048'],
        ]);

        DB::transaction(function () use ($validated, $request, $archer) {
            $clubId = $this->resolveClub($validated);

            $archer->user->update([
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'club_id' => $clubId,
            ]);

            $photoPath = $archer->photo;
            if ($request->hasFile('photo')) {
                if ($archer->photo) {
                    Storage::disk('public')->delete($archer->photo);
                }
                $photoPath = $request->file('photo')->store('archers', 'public');
            }

            $archer->update([
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
                'divisions'     => $validated['divisions'] ?? [],
                'photo'         => $photoPath,
                'notes'         => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('archers.show', $archer)
            ->with('success', 'Archer profile updated successfully.');
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
