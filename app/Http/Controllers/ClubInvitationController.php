<?php

namespace App\Http\Controllers;

use App\Models\ClubInvitation;
use Illuminate\Http\RedirectResponse;

class ClubInvitationController extends Controller
{
    public function accept(string $token): RedirectResponse
    {
        $invitation = ClubInvitation::where('token', $token)->firstOrFail();

        if (! $invitation->isPending()) {
            $msg = $invitation->isExpired()
                ? 'This invitation has expired.'
                : 'This invitation has already been responded to.';
            return redirect('/')->with('error', $msg);
        }

        $invitable = $invitation->invitable_model;

        if ($invitable) {
            $invitable->update(['club_id' => $invitation->club_id]);
            $invitable->user?->update(['club_id' => $invitation->club_id]);
        }

        $invitation->update([
            'status'       => 'accepted',
            'responded_at' => now(),
        ]);

        // Redirect to their profile after accepting
        $redirect = match ($invitation->invitable_type) {
            'archer' => $invitable ? route('archers.show', $invitable) : '/',
            'coach'  => $invitable ? route('coaches.show', $invitable) : '/',
            default  => '/',
        };

        return redirect($redirect)
            ->with('success', 'You have successfully joined ' . $invitation->club->name . '!');
    }

    public function decline(string $token): RedirectResponse
    {
        $invitation = ClubInvitation::where('token', $token)->firstOrFail();

        if (! $invitation->isPending()) {
            return redirect('/')->with('info', 'This invitation is no longer active.');
        }

        $invitation->update([
            'status'       => 'declined',
            'responded_at' => now(),
        ]);

        return redirect('/')
            ->with('info', 'You have declined the invitation to join ' . $invitation->club->name . '.');
    }

    public function cancel(ClubInvitation $invitation): RedirectResponse
    {
        $user = auth()->user();
        if ($user->role === 'club_admin' && $user->club_id !== $invitation->club_id) {
            abort(403);
        }

        $invitation->update([
            'status'       => 'declined',
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Invitation cancelled.');
    }
}
