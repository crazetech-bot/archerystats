<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Coach;
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
        $coaches = Coach::with('user', 'club')
            ->orderBy('ref_no')
            ->paginate(20);

        return view('coaches.index', compact('coaches'));
    }

    public function create(): View
    {
        $clubs  = Club::where('active', true)->orderBy('name')->get();
        $states = Coach::MALAYSIAN_STATES;

        return view('coaches.create', compact('clubs', 'states'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'date_of_birth'=> ['nullable', 'date', 'before:today'],
            'gender'       => ['nullable', 'in:male,female'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'team'          => ['nullable', 'string', 'max:100'],
            'coaching_level'=> ['nullable', 'string', 'in:' . implode(',', Coach::COACHING_LEVELS)],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['required', 'string', 'max:100'],
            'address_line'  => ['nullable', 'string', 'max:500'],
            'postcode'      => ['nullable', 'string', 'max:10'],
            'club_id'       => ['nullable', 'exists:clubs,id'],
            'new_club_name' => ['nullable', 'string', 'max:150'],
            'notes'         => ['nullable', 'string'],
            'photo'         => ['nullable', 'file', 'mimes:bmp,jpg,jpeg,webp', 'max:2048'],
        ]);

        $coach = DB::transaction(function () use ($validated, $request) {
            $clubId = $this->resolveClub($validated);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make(str()->random(16)),
                'role'     => 'coach',
                'club_id'  => $clubId,
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('coaches', 'public');
            }

            return Coach::create([
                'user_id'      => $user->id,
                'club_id'      => $clubId,
                'date_of_birth'=> $validated['date_of_birth'] ?? null,
                'gender'       => $validated['gender'] ?? null,
                'phone'        => $validated['phone'] ?? null,
                'team'          => $validated['team'] ?? null,
                'coaching_level'=> $validated['coaching_level'] ?? null,
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
        $coach->load('user', 'club');
        return view('coaches.show', compact('coach'));
    }

    public function edit(Coach $coach): View
    {
        $clubs  = Club::where('active', true)->orderBy('name')->get();
        $states = Coach::MALAYSIAN_STATES;

        return view('coaches.edit', compact('coach', 'clubs', 'states'));
    }

    public function update(Request $request, Coach $coach): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email,' . $coach->user_id],
            'date_of_birth'=> ['nullable', 'date', 'before:today'],
            'gender'       => ['nullable', 'in:male,female'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'team'          => ['nullable', 'string', 'max:100'],
            'coaching_level'=> ['nullable', 'string', 'in:' . implode(',', Coach::COACHING_LEVELS)],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['required', 'string', 'max:100'],
            'address_line'  => ['nullable', 'string', 'max:500'],
            'postcode'      => ['nullable', 'string', 'max:10'],
            'club_id'       => ['nullable', 'exists:clubs,id'],
            'new_club_name' => ['nullable', 'string', 'max:150'],
            'notes'         => ['nullable', 'string'],
            'photo'         => ['nullable', 'file', 'mimes:bmp,jpg,jpeg,webp', 'max:2048'],
        ]);

        DB::transaction(function () use ($validated, $request, $coach) {
            $clubId = $this->resolveClub($validated);

            $coach->user->update([
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'club_id' => $clubId,
            ]);

            $photoPath = $coach->photo;
            if ($request->hasFile('photo')) {
                if ($coach->photo) {
                    Storage::disk('public')->delete($coach->photo);
                }
                $photoPath = $request->file('photo')->store('coaches', 'public');
            }

            $coach->update([
                'club_id'      => $clubId,
                'date_of_birth'=> $validated['date_of_birth'] ?? null,
                'gender'       => $validated['gender'] ?? null,
                'phone'        => $validated['phone'] ?? null,
                'team'          => $validated['team'] ?? null,
                'coaching_level'=> $validated['coaching_level'] ?? null,
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
