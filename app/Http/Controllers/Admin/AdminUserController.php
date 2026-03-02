<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'role'                  => ['required', 'in:super_admin,club_admin,state_admin,national_team'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $clubId = null;

        if ($request->role === 'club_admin') {
            if ($request->club_source === 'new') {
                $request->validate([
                    'new_club_name' => ['required', 'string', 'max:150', 'unique:clubs,name'],
                ]);
                $club   = Club::create(['name' => trim($request->new_club_name), 'active' => true]);
                $clubId = $club->id;
            } else {
                $request->validate([
                    'club_id' => ['required', 'exists:clubs,id'],
                ]);
                $clubId = $request->club_id;
            }
        }

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => $request->role,
            'club_id'           => $clubId,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.settings')->with('success', 'Admin user created successfully.');
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('admin.settings')->with('success', 'Password updated for ' . $user->name . '.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.settings')->with('error', 'You cannot suspend your own account.');
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'reactivated' : 'suspended';

        return redirect()->route('admin.settings')->with('success', $user->name . ' has been ' . $label . '.');
    }

    public function promote(Request $request, User $user): RedirectResponse
    {
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.settings')->with('error', 'Cannot change the role of a super admin.');
        }

        $request->validate([
            'role'    => ['required', 'in:club_admin,state_admin,national_team'],
            'club_id' => ['required_if:role,club_admin', 'nullable', 'exists:clubs,id'],
        ]);

        $wasCoach = $user->role === 'coach' || $user->is_coach;

        $user->update([
            'role'     => $request->role,
            'is_coach' => $wasCoach,
            'club_id'  => $request->role === 'club_admin' ? $request->club_id : null,
        ]);

        // Ensure the coach profile exists if they carry the coach flag
        if ($wasCoach && ! $user->coach) {
            Coach::create(['user_id' => $user->id]);
        }

        $label = $request->role === 'club_admin' ? 'Club Admin' : 'State Admin';
        $coachNote = $wasCoach ? ' (coach privileges retained)' : '';

        return redirect()->route('admin.settings')->with('success', "{$user->name} has been appointed as {$label}{$coachNote}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.settings')->with('error', 'You cannot delete your own account.');
        }

        DB::transaction(function () use ($user) {
            if ($user->role === 'archer' && $user->archer) {
                if ($user->archer->photo) {
                    Storage::disk('public')->delete($user->archer->photo);
                }
                $user->archer->delete();
            } elseif ($user->role === 'coach' && $user->coach) {
                if ($user->coach->photo) {
                    Storage::disk('public')->delete($user->coach->photo);
                }
                $user->coach->delete();
            }
            $user->delete();
        });

        return redirect()->route('admin.settings')->with('success', 'Account deleted successfully.');
    }
}
