<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Archer;
use App\Models\Club;
use App\Models\ClubInvitation;
use App\Models\Coach;
use App\Models\Scopes\ClubScope;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request): View
    {
        $clubs       = Club::orderBy('name')->pluck('name');
        $currentClub = app()->has('currentClub') ? app('currentClub') : null;
        $regOpen = [
            'archer' => Setting::get('reg_archer_open', '1') === '1',
            'coach'  => Setting::get('reg_coach_open',  '1') === '1',
            'club'   => Setting::get('reg_club_open',   '1') === '1',
        ];

        $invite = $this->resolvePendingEmailInvite($request->query('invite'));

        return view('auth.register', compact('clubs', 'regOpen', 'currentClub', 'invite'));
    }

    public function register(Request $request): RedirectResponse
    {
        $currentClub = app()->has('currentClub') ? app('currentClub') : null;

        // Club-invitation signup: a valid email-only invite locks email + role
        // and joins the new account to the inviting club.
        $invite = $this->resolvePendingEmailInvite($request->input('invite_token'));
        if ($invite) {
            $request->merge([
                'email' => $invite->email,
                'role'  => $invite->invitable_type,
            ]);
        }

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
                    // Bypass ClubScope: in tenant context the member is (by definition)
                    // not yet part of the current club, so scoped relations hide them.
                    $member = $existingUser->role === 'archer'
                        ? Archer::withoutGlobalScope(ClubScope::class)->where('user_id', $existingUser->id)->first()
                        : Coach::withoutGlobalScope(ClubScope::class)->where('user_id', $existingUser->id)->first();

                    if ($member && ! $member->clubs()->where('clubs.id', $currentClub->id)->exists()) {
                        // First club becomes primary (covers previously unaffiliated
                        // members); joining an additional club stays secondary.
                        $hasPrimary = $member->clubs()->wherePivot('primary_club', true)->exists();

                        $member->clubs()->attach($currentClub->id, [
                            'primary_club' => ! $hasPrimary,
                            'joined_at'    => now(),
                        ]);

                        if (! $hasPrimary) {
                            $member->update(['club_id' => $currentClub->id]);
                            if (! $existingUser->club_id) {
                                $existingUser->update(['club_id' => $currentClub->id]);
                            }
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

        // Open/closed registration toggles gate NEW account creation only.
        // Existing members joining another club (handled above) and club
        // invitations (explicit admission by a club admin) both bypass them.
        $typeMap = ['archer' => 'archer', 'coach' => 'coach', 'club_admin' => 'club'];
        $type    = $typeMap[$validated['role']] ?? null;
        if (! $invite && $type && Setting::get('reg_' . $type . '_open', '1') !== '1') {
            return back()
                ->withErrors(['role' => ucfirst($type) . ' registration is currently suspended.'])
                ->withInput();
        }

        // Which club (if any) the new account joins: an email invitation wins,
        // then the subdomain's tenant club.
        $joinClub = $invite?->club ?? $currentClub;

        $user = DB::transaction(function () use ($validated, $joinClub, $invite) {
            $clubId = $joinClub?->id;

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'],
                'club_id'  => $clubId,
            ]);

            if ($validated['role'] === 'archer') {
                $archer = Archer::create(['user_id' => $user->id, 'club_id' => $clubId]);
                if ($joinClub) {
                    $archer->clubs()->attach($joinClub->id, ['primary_club' => true, 'joined_at' => now()]);
                }
            } elseif ($validated['role'] === 'coach') {
                $user->update(['is_coach' => true]);
                $coach = Coach::create(['user_id' => $user->id, 'club_id' => $clubId]);
                if ($joinClub) {
                    $coach->clubs()->attach($joinClub->id, ['primary_club' => true, 'joined_at' => now()]);
                }
            } elseif ($validated['role'] === 'club_admin') {
                // Club starts inactive (pending) — its subdomain stays offline until the
                // admin verifies their email and a super admin approves the club.
                $club = Club::create([
                    'name'   => $validated['club_name'],
                    'active' => false,
                ]);
                $user->update(['club_id' => $club->id]);
            }

            return $user;
        });

        if ($invite) {
            $invite->update(['status' => 'accepted', 'responded_at' => now()]);
        }

        // Log them in so they can see the "verify your email" notice and resend it,
        // but the account is unverified — the `verified` middleware blocks app access
        // until they click the link.
        Auth::login($user);

        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::error('Failed to send verification email on registration: ' . $e->getMessage());
        }

        return redirect()->route('verification.notice');
    }

    /** Valid, still-pending, email-only club invitation for the given token (or null). */
    private function resolvePendingEmailInvite(?string $token): ?ClubInvitation
    {
        if (! $token) {
            return null;
        }

        $invite = ClubInvitation::where('token', $token)->with('club')->first();

        if (! $invite || ! $invite->isPending() || $invite->invitable_id) {
            return null;
        }

        return $invite;
    }
}
