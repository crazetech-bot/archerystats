<?php

namespace App\Http\Controllers;

use App\Mail\CoachArcherInvitationMail;
use App\Models\Archer;
use App\Models\Coach;
use App\Models\CoachArcherInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CoachArcherController extends Controller
{
    public function index(Coach $coach): View
    {
        $coach->load(['archers.user', 'archers.club']);

        // Available archers: not yet assigned to this coach
        // Coaches may only assign archers from their own club;
        // admins can assign from any club (cross-club uses invitation flow).
        $assignedIds = $coach->archers->pluck('id');
        $availableQuery = Archer::with('user', 'club')
            ->whereNotIn('id', $assignedIds);

        if (auth()->user()->role === 'coach') {
            $availableQuery->where('club_id', $coach->club_id);
        }

        $available = $availableQuery->orderBy('ref_no')->get();

        // Pending cross-club invitations
        $pendingInvitations = CoachArcherInvitation::where('coach_id', $coach->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with(['archer.user', 'archer.club'])
            ->orderByDesc('created_at')
            ->get();

        return view('coaches.archers.index', compact('coach', 'available', 'pendingInvitations'));
    }

    public function store(Coach $coach, Request $request): RedirectResponse
    {
        $request->validate([
            'archer_id' => ['required', 'exists:archers,id'],
        ]);

        $archer = Archer::findOrFail($request->archer_id);

        // Already assigned
        if ($coach->archers()->where('archers.id', $archer->id)->exists()) {
            return back()->withErrors(['archer_id' => 'This archer is already assigned to this coach.']);
        }

        if ($archer->club_id === null || $archer->club_id === $coach->club_id) {
            // No club or same club — assign directly, no confirmation needed
            $coach->archers()->syncWithoutDetaching([$archer->id]);
            return back()->with('success', "Archer {$archer->ref_no} assigned to coach.");
        }

        // Different club — send a 72-hour confirmation invitation
        $existing = CoachArcherInvitation::where('coach_id', $coach->id)
            ->where('archer_id', $archer->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return back()->withErrors(['archer_id' => 'A pending invitation already exists for this archer.']);
        }

        $invitation = CoachArcherInvitation::create([
            'coach_id'   => $coach->id,
            'archer_id'  => $archer->id,
            'token'      => Str::random(64),
            'status'     => 'pending',
            'expires_at' => now()->addHours(72),
        ]);

        Mail::to($archer->user->email)->send(new CoachArcherInvitationMail($invitation));

        return back()->with('success', "Invitation sent to {$archer->ref_no} ({$archer->user->email}). They have 72 hours to confirm.");
    }

    public function destroy(Coach $coach, Archer $archer): RedirectResponse
    {
        if (! auth()->user()->isClubAdmin()) {
            abort(403, 'Only club administrators can remove archers from a coach roster.');
        }

        $coach->archers()->detach($archer->id);

        return back()->with('success', "Archer {$archer->ref_no} removed from coach roster.");
    }
}
