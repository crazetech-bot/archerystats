<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewUserRegisteredMail;
use App\Models\Archer;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        $clubs       = Club::orderBy('name')->pluck('name');
        $currentClub = app()->has('currentClub') ? app('currentClub') : null;
        $regOpen = [
            'archer' => Setting::get('reg_archer_open', '1') === '1',
            'coach'  => Setting::get('reg_coach_open',  '1') === '1',
            'club'   => Setting::get('reg_club_open',   '1') === '1',
        ];
        return view('auth.register', compact('clubs', 'regOpen', 'currentClub'));
    }

    public function register(Request $request): RedirectResponse
    {
        $currentClub = app()->has('currentClub') ? app('currentClub') : null;

        // On a subdomain, allow existing emails so archers/coaches can join multiple clubs
        $emailRule = $currentClub
            ? ['required', 'email']
            : ['required', 'email', 'unique:users,email'];

        $validated = $request->validate([
            'role'                  => ['required', 'in:archer,coach,club_admin'],
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => $emailRule,
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'club_name'             => ['required_if:role,club_admin', 'nullable', 'string', 'max:255', 'unique:clubs,name'],
        ]);

        $typeMap = ['archer' => 'archer', 'coach' => 'coach', 'club_admin' => 'club'];
        $type    = $typeMap[$validated['role']] ?? null;
        if ($type && Setting::get('reg_' . $type . '_open', '1') !== '1') {
            return back()
                ->withErrors(['role' => ucfirst($type) . ' registration is currently suspended.'])
                ->withInput();
        }

        // On a subdomain, club_admin registration is not allowed
        if ($currentClub && $validated['role'] === 'club_admin') {
            return back()
                ->withErrors(['role' => 'Club registration is done at the main platform. Please choose Archer or Coach.'])
                ->withInput();
        }

        // On a subdomain: if the email belongs to an existing user, add them to this club
        if ($currentClub) {
            $existingUser = User::where('email', $validated['email'])->first();

            if ($existingUser) {
                // Verify password matches
                if (! Hash::check($validated['password'], $existingUser->password)) {
                    return back()
                        ->withErrors(['email' => 'An account with this email already exists. Please enter the correct password to join this club.'])
                        ->withInput();
                }

                // Only archers and coaches can join additional clubs
                if (! in_array($existingUser->role, ['archer', 'coach'])) {
                    return back()
                        ->withErrors(['email' => 'This account type cannot join additional clubs.'])
                        ->withInput();
                }

                $joined = DB::transaction(function () use ($existingUser, $currentClub) {
                    if ($existingUser->role === 'archer') {
                        $archer = $existingUser->archer;
                        if ($archer && ! $archer->clubs()->where('clubs.id', $currentClub->id)->exists()) {
                            $archer->clubs()->attach($currentClub->id, ['primary_club' => false, 'joined_at' => now()]);
                        }
                    } elseif ($existingUser->role === 'coach') {
                        $coach = $existingUser->coach;
                        if ($coach && ! $coach->clubs()->where('clubs.id', $currentClub->id)->exists()) {
                            $coach->clubs()->attach($currentClub->id, ['primary_club' => false, 'joined_at' => now()]);
                        }
                    }
                    return $existingUser;
                });

                Auth::login($joined);

                if ($joined->role === 'archer') {
                    return redirect()->route('archers.show', $joined->archer)
                        ->with('success', 'You have joined ' . $currentClub->name . '!');
                }
                return redirect()->route('coaches.show', $joined->coach)
                    ->with('success', 'You have joined ' . $currentClub->name . '!');
            }
        }

        $user = DB::transaction(function () use ($validated, $currentClub) {
            $clubId = $currentClub?->id;

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'],
                'club_id'  => $clubId,
            ]);

            if ($validated['role'] === 'archer') {
                $archer = Archer::create(['user_id' => $user->id, 'club_id' => $clubId]);
                if ($currentClub) {
                    $archer->clubs()->attach($currentClub->id, ['primary_club' => true, 'joined_at' => now()]);
                }
            } elseif ($validated['role'] === 'coach') {
                $user->update(['is_coach' => true]);
                $coach = Coach::create(['user_id' => $user->id, 'club_id' => $clubId]);
                if ($currentClub) {
                    $coach->clubs()->attach($currentClub->id, ['primary_club' => true, 'joined_at' => now()]);
                }
            } elseif ($validated['role'] === 'club_admin') {
                $club = Club::create([
                    'name'   => $validated['club_name'],
                    'active' => true,
                ]);
                $user->update(['club_id' => $club->id]);
            }

            return $user;
        });

        Auth::login($user);

        // Notify all super_admins by email
        $user->load('club');
        User::where('role', 'super_admin')->each(function (User $admin) use ($user) {
            Mail::to($admin->email)->send(new NewUserRegisteredMail($user));
        });

        if ($user->role === 'archer') {
            return redirect()->route('archers.show', $user->archer)
                ->with('success', 'Registration successful! Please complete your profile.');
        }
        if ($user->role === 'coach') {
            return redirect()->route('coaches.show', $user->coach)
                ->with('success', 'Registration successful! Please complete your profile.');
        }
        return redirect()->route('archers.index')
            ->with('success', 'Registration successful!');
    }
}
