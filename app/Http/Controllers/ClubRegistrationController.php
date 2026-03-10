<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ClubRegistrationController extends Controller
{
    public function showForm(): View
    {
        return view('auth.register-club');
    }

    public function register(Request $request): RedirectResponse
    {
        if (Setting::get('reg_club_open', '1') !== '1') {
            return back()->withErrors(['club_name' => 'Club registration is currently suspended.'])->withInput();
        }

        $validated = $request->validate([
            'club_name'             => ['required', 'string', 'max:255', 'unique:clubs,name'],
            'slug'                  => ['required', 'string', 'max:100', 'alpha_dash', 'unique:clubs,slug'],
            'state'                 => ['nullable', 'string', 'max:100'],
            'admin_name'            => ['required', 'string', 'max:255'],
            'admin_email'           => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        DB::transaction(function () use ($validated) {
            $club = Club::create([
                'name'   => $validated['club_name'],
                'slug'   => $validated['slug'],
                'state'  => $validated['state'] ?? null,
                'active' => true,
            ]);

            User::create([
                'name'     => $validated['admin_name'],
                'email'    => $validated['admin_email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'club_admin',
                'club_id'  => $club->id,
            ]);
        });

        $slug = $validated['slug'];
        $rootDomain = config('app.root_domain', 'sportdns.com');

        return redirect()->route('club-register.success', ['slug' => $slug]);
    }

    public function success(Request $request): View
    {
        $slug = $request->query('slug', '');
        $rootDomain = config('app.root_domain', 'sportdns.com');
        $subdomain  = $slug ? "http://{$slug}.{$rootDomain}" : null;

        return view('auth.register-club-success', compact('slug', 'subdomain'));
    }

    /**
     * AJAX: check slug availability while the user types.
     */
    public function checkSlug(Request $request)
    {
        $slug = $request->input('slug', '');
        $available = !Club::where('slug', $slug)->exists();

        return response()->json(['available' => $available]);
    }
}
