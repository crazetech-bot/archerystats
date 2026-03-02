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
        $clubs  = Club::orderBy('name')->pluck('name');
        $regOpen = [
            'archer' => Setting::get('reg_archer_open', '1') === '1',
            'coach'  => Setting::get('reg_coach_open',  '1') === '1',
            'club'   => Setting::get('reg_club_open',   '1') === '1',
        ];
        return view('auth.register', compact('clubs', 'regOpen'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role'                  => ['required', 'in:archer,coach,club_admin'],
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
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

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'],
            ]);

            if ($validated['role'] === 'archer') {
                Archer::create(['user_id' => $user->id]);
            } elseif ($validated['role'] === 'coach') {
                $user->update(['is_coach' => true]);
                Coach::create(['user_id' => $user->id]);
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
