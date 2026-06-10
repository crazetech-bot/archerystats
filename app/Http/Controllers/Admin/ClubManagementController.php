<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClubActivatedMail;
use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubManagementController extends Controller
{
    public function index(): View
    {
        $clubs = Club::withCount(['archers', 'coaches', 'users'])
            ->orderBy('name')
            ->get();

        $totalClubs   = $clubs->count();
        $activeClubs  = $clubs->where('active', true)->count();
        // Count distinct archers/coaches across all pivot memberships
        $totalArchers = \DB::table('archer_clubs')->distinct('archer_id')->count('archer_id');
        $totalCoaches = \DB::table('coach_clubs')->distinct('coach_id')->count('coach_id');

        return view('admin.clubs.index', compact(
            'clubs', 'totalClubs', 'activeClubs', 'totalArchers', 'totalCoaches'
        ));
    }

    public function show(Club $club): View
    {
        $club->load(['users', 'archers.user', 'coaches.user']);

        $archerCount = $club->archers()->count();
        $coachCount  = $club->coaches()->count();
        $adminUsers  = User::where('club_id', $club->id)
                           ->whereIn('role', ['club_admin', 'super_admin'])
                           ->get();

        return view('admin.clubs.show', compact('club', 'archerCount', 'coachCount', 'adminUsers'));
    }

    public function toggle(Club $club): RedirectResponse
    {
        $wasActive = $club->active;
        $club->update(['active' => !$club->active]);

        // Approving a pending club (off -> on): notify its admins it's now live.
        if (! $wasActive && $club->active) {
            $admins = User::where('club_id', $club->id)
                ->where('role', 'club_admin')
                ->get();
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new ClubActivatedMail($club, $admin));
                } catch (\Throwable $e) {
                    Log::error('Failed to send club-activated email: ' . $e->getMessage());
                }
            }
        }

        $status = $club->active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Club \"{$club->name}\" has been {$status}.");
    }

    public function update(Request $request, Club $club): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'slug'     => ['required', 'string', 'max:100', 'alpha_dash',
                           "unique:clubs,slug,{$club->id}"],
            'location' => ['nullable', 'string', 'max:255'],
            'state'    => ['nullable', 'string', 'max:100'],
            'active'   => ['boolean'],
        ]);

        $club->update($validated);

        return redirect()->route('admin.clubs.show', $club)->with('success', 'Club updated.');
    }

    public function destroy(Club $club): RedirectResponse
    {
        $name = $club->name;
        $slug = $club->slug;

        DB::transaction(fn () => $this->purgeClub($club));

        return redirect()->route('admin.clubs.index')
            ->with('success', "Club \"{$name}\" and its subdomain ({$slug}.sportdns.com) have been removed.");
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:clubs,id'],
        ]);

        $clubs = Club::whereIn('id', $validated['ids'])->get();

        DB::transaction(function () use ($clubs) {
            foreach ($clubs as $club) {
                $this->purgeClub($club);
            }
        });

        return redirect()->route('admin.clubs.index')
            ->with('success', $clubs->count() . ' club(s) and their subdomains have been removed.');
    }

    /**
     * Delete a club and its dependents.
     *
     * Club-admin accounts belonging to the club are deleted (super admins are
     * platform-level and kept; the current user is never deleted). Removing the
     * club row cascades its invitations, transfer requests and archer/coach
     * membership pivots, and nulls club_id on remaining archers/coaches (detach).
     * Dropping the row also frees the slug, so the subdomain stops resolving
     * (IdentifyTenant returns 503 for it).
     *
     * Must be called inside a DB transaction.
     */
    private function purgeClub(Club $club): void
    {
        $admins = User::where('club_id', $club->id)
            ->where('role', 'club_admin')
            ->where('id', '!=', auth()->id())
            ->get();

        foreach ($admins as $admin) {
            if ($admin->archer) {
                if ($admin->archer->photo) {
                    Storage::disk('public')->delete($admin->archer->photo);
                }
                $admin->archer->delete();
            }
            if ($admin->coach) {
                if ($admin->coach->photo) {
                    Storage::disk('public')->delete($admin->coach->photo);
                }
                $admin->coach->delete();
            }
            $admin->delete();
        }

        if ($club->logo) {
            Storage::disk('public')->delete($club->logo);
        }

        $club->delete();
    }
}
