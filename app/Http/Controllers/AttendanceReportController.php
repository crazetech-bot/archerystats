<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\Attendance;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AttendanceReportController extends Controller
{
    /**
     * Per-archer attendance rates across training sessions the user can see.
     * Scope reuses the app's existing club model (users.club_id + coach club links);
     * super admins see every club. Filterable by club, team and date range.
     */
    public function index(Request $request): View
    {
        $user    = auth()->user();
        $clubIds = $this->accessibleClubIds($user);

        $clubFilter = $request->integer('club_id') ?: null;
        $team       = $request->input('team');
        $from       = $request->input('from');
        $to         = $request->input('to');

        // Narrow to a single club only if the user may access it.
        $scopeClubIds = ($clubFilter && $clubIds->contains($clubFilter))
            ? collect([$clubFilter])
            : $clubIds;

        $attendances = Attendance::with(['archer.user'])
            ->whereHas('trainingSession.coach', fn($q) => $q->whereIn('club_id', $scopeClubIds))
            ->when($from, fn($q) => $q->whereHas('trainingSession', fn($t) => $t->whereDate('date', '>=', $from)))
            ->when($to,   fn($q) => $q->whereHas('trainingSession', fn($t) => $t->whereDate('date', '<=', $to)))
            ->when($team, fn($q) => $q->whereHas('archer', fn($a) => $a->where('team', $team)))
            ->get();

        $report = $attendances
            ->groupBy('archer_id')
            ->map(function (Collection $items) {
                $counts = ['present' => 0, 'late' => 0, 'absent' => 0, 'excused' => 0];
                foreach ($items as $a) {
                    if (isset($counts[$a->status])) {
                        $counts[$a->status]++;
                    }
                }
                $total    = array_sum($counts);
                $attended = $counts['present'] + $counts['late'];

                return [
                    'archer' => $items->first()->archer,
                    'counts' => $counts,
                    'total'  => $total,
                    'rate'   => $total ? round($attended / $total * 100, 1) : 0.0,
                ];
            })
            ->filter(fn($r) => $r['archer'] !== null)
            ->sortByDesc('rate')
            ->values();

        $clubs = $user->isAdmin()
            ? Club::orderBy('name')->get(['id', 'name'])
            : Club::whereIn('id', $clubIds)->orderBy('name')->get(['id', 'name']);

        $teams = Archer::whereIn('club_id', $clubIds)
            ->whereNotNull('team')->where('team', '!=', '')
            ->distinct()->orderBy('team')->pluck('team');

        $showClubFilter = $clubs->count() > 1;

        return view('attendance.report', compact(
            'report', 'clubs', 'teams', 'clubFilter', 'team', 'from', 'to', 'showClubFilter'
        ));
    }

    /** Club ids the user may see attendance for. */
    private function accessibleClubIds($user): Collection
    {
        if ($user->isAdmin()) {
            return Club::pluck('id');
        }

        $ids = collect();
        if ($user->club_id) {
            $ids->push($user->club_id);
        }
        if ($user->coach) {
            $ids->push($user->coach->club_id);
            $ids = $ids->merge($user->coach->clubs()->pluck('clubs.id')); // multi-club coaches
        }

        return $ids->filter()->unique()->values();
    }
}
