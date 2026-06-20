<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Coach;
use App\Models\TrainingSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Bulk-mark attendance for a coach's training session. Statuses arrive keyed by
     * archer id; each is upserted (one row per archer per session — re-saving updates,
     * never duplicates). Only archers belonging to the coach's club are accepted.
     */
    public function store(Coach $coach, TrainingSession $training, Request $request): RedirectResponse
    {
        // Session must belong to this coach, and the user must be able to access the club.
        abort_unless($training->coach_id === $coach->id, 404);
        $this->authorizeCoachClub($coach);

        $validated = $request->validate([
            'status'   => ['nullable', 'array'],
            'status.*' => ['in:' . implode(',', Attendance::STATUSES)],
        ]);

        $clubArcherIds = $coach->clubArchers()->pluck('id')->all();
        $marked = 0;

        DB::transaction(function () use ($training, $validated, $clubArcherIds, &$marked) {
            foreach (($validated['status'] ?? []) as $archerId => $status) {
                $archerId = (int) $archerId;
                if (! in_array($archerId, $clubArcherIds, true)) {
                    continue; // ignore anything outside the coach's club
                }
                Attendance::updateOrCreate(
                    ['training_session_id' => $training->id, 'archer_id' => $archerId],
                    ['status' => $status]
                );
                $marked++;
            }
        });

        return redirect()
            ->route('coaches.training.show', [$coach, $training])
            ->with('success', "Attendance saved for {$marked} archer(s).");
    }

    /**
     * A user may mark attendance for a coach's sessions if they are a super admin,
     * the club admin of that coach's club, or that coach themselves. Anything else 403s.
     */
    private function authorizeCoachClub(Coach $coach): void
    {
        $user = auth()->user();

        $allowed = $user->isAdmin()
            || ($user->isClubAdmin() && $user->club_id === $coach->club_id)
            || ($user->coach && $user->coach->id === $coach->id);

        abort_unless($allowed, 403);
    }
}
