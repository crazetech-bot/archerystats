<?php

namespace App\Http\Controllers;

use App\Models\CoachArcherInvitation;
use Illuminate\Http\RedirectResponse;

class CoachArcherInvitationController extends Controller
{
    public function accept(string $token): RedirectResponse
    {
        $invitation = CoachArcherInvitation::where('token', $token)
            ->with(['coach', 'archer'])
            ->firstOrFail();

        if (! $invitation->isPending()) {
            $msg = $invitation->isExpired()
                ? 'This invitation has expired.'
                : 'This invitation has already been responded to.';
            return redirect('/')->with('error', $msg);
        }

        // Assign archer to coach via pivot
        $invitation->coach->archers()->syncWithoutDetaching([$invitation->archer_id]);

        $invitation->update([
            'status'       => 'accepted',
            'responded_at' => now(),
        ]);

        $redirect = $invitation->archer
            ? route('archers.show', $invitation->archer)
            : '/';

        return redirect($redirect)
            ->with('success', 'You have been successfully assigned under coach ' . $invitation->coach->full_name . '!');
    }

    public function decline(string $token): RedirectResponse
    {
        $invitation = CoachArcherInvitation::where('token', $token)
            ->with('coach')
            ->firstOrFail();

        if (! $invitation->isPending()) {
            return redirect('/')->with('info', 'This invitation is no longer active.');
        }

        $invitation->update([
            'status'       => 'declined',
            'responded_at' => now(),
        ]);

        return redirect('/')
            ->with('info', 'You have declined the assignment request from coach ' . $invitation->coach->full_name . '.');
    }

    public function cancel(CoachArcherInvitation $invitation): RedirectResponse
    {
        $user = auth()->user();

        // Only the coach or an admin can cancel
        if ($user->role === 'coach' && $user->coach?->id !== $invitation->coach_id) {
            abort(403);
        }

        $invitation->update([
            'status'       => 'declined',
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Invitation cancelled.');
    }
}
