<?php

namespace App\Http\Controllers;

use App\Mail\ClubEventMail;
use App\Models\Club;
use App\Models\ClubEvent;
use App\Models\ClubEventRsvp;
use App\Models\Coach;
use App\Models\EliminationMatch;
use App\Models\Scopes\ClubScope;
use App\Models\TrainingSession;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EventController extends Controller
{
    /** Month calendar + upcoming list. */
    public function index(Request $request): View|RedirectResponse
    {
        $user  = auth()->user();
        $base  = ClubEvent::visibleTo($user);

        if (in_array($user->role, ['super_admin', 'club_admin']) && ! $base) {
            return redirect()->route('admin.clubs.index')
                ->with('info', 'Open a club to view its events.');
        }

        [$composeAudiences, $coachMode] = $this->composeContext($user);

        // Month window (?month=YYYY-MM), padded to whole weeks (Mon–Sun).
        $month     = $this->parseMonth($request->query('month'));
        $gridStart = $month->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $gridEnd   = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        // Events in the visible grid, grouped by day.
        $monthEvents = ($base ? (clone $base) : ClubEvent::whereRaw('1=0'))
            ->with(['coach.user'])->withCount('rsvps')
            ->whereBetween('starts_at', [$gridStart, $gridEnd])
            ->orderBy('starts_at')->get();

        $byDay = [];
        foreach ($monthEvents as $e) {
            $byDay[$e->starts_at->format('Y-m-d')][] = ['kind' => 'event', 'model' => $e];
        }
        foreach ($this->otherItems($user, $gridStart, $gridEnd) as $item) {
            $byDay[$item['date']][] = $item;
        }

        // 6-week grid.
        $weeks = [];
        $cursor = $gridStart->copy();
        while ($cursor <= $gridEnd) {
            $row = [];
            for ($i = 0; $i < 7; $i++) {
                $key = $cursor->format('Y-m-d');
                $row[] = [
                    'date'    => $cursor->copy(),
                    'inMonth' => $cursor->month === $month->month,
                    'items'   => $byDay[$key] ?? [],
                ];
                $cursor->addDay();
            }
            $weeks[] = $row;
        }

        // Upcoming list (next events from today) + this user's RSVPs.
        $upcoming = ($base ? (clone $base) : ClubEvent::whereRaw('1=0'))
            ->with(['coach.user'])->withCount('rsvps')
            ->where(fn ($q) => $q->where('starts_at', '>=', now()->startOfDay())
                                 ->orWhere('ends_at', '>=', now()))
            ->orderBy('starts_at')->limit(15)->get();

        $myRsvps = ClubEventRsvp::where('user_id', $user->id)
            ->whereIn('club_event_id', $upcoming->pluck('id'))
            ->pluck('response', 'club_event_id');

        return view('events.index', compact(
            'weeks', 'month', 'upcoming', 'myRsvps', 'composeAudiences', 'coachMode'
        ));
    }

    /** Event detail: description, RSVP, attendee lists. */
    public function show(ClubEvent $event): View
    {
        $this->authorizeView($event);

        $event->load(['coach.user', 'creator', 'club', 'rsvps.user']);

        $grouped = $event->rsvps->groupBy('response');
        $myRsvp  = $event->rsvps->firstWhere('user_id', auth()->id())?->response;

        return view('events.show', [
            'event'    => $event,
            'going'    => $grouped->get('going', collect()),
            'maybe'    => $grouped->get('maybe', collect()),
            'notGoing' => $grouped->get('not_going', collect()),
            'myRsvp'   => $myRsvp,
            'canManage'=> $this->canManage($event),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $this->validated($request);

        if ($user->role === 'coach') {
            $coach = $user->coach;
            if (! $coach || ! $coach->club_id) {
                return back()->with('error', 'Your coach profile has no club — cannot create events.');
            }
            $clubId   = $coach->club_id;
            $audience = 'my_archers';
            $coachId  = $coach->id;
        } else {
            $club = $this->resolveClub();
            if (! $club) {
                return back()->with('error', 'No club context — open this page from your club.');
            }
            $clubId   = $club->id;
            $audience = in_array($data['audience'] ?? 'all', ['all', 'archers', 'coaches']) ? $data['audience'] : 'all';
            $coachId  = null;
        }

        $event = ClubEvent::create([
            'club_id'            => $clubId,
            'created_by_user_id' => $user->id,
            'coach_id'           => $coachId,
            'title'              => $data['title'],
            'description'        => $data['description'] ?? null,
            'location'           => $data['location'] ?? null,
            'event_type'         => $data['event_type'],
            'starts_at'          => $data['starts_at'],
            'ends_at'            => $data['ends_at'] ?? null,
            'audience'           => $audience,
            'pinned'             => (bool) ($data['pinned'] ?? false),
            'send_email'         => (bool) ($data['send_email'] ?? false),
        ]);

        $note = '';
        if ($event->send_email) {
            $sent = $this->emailAudience($event);
            $event->update(['emailed_at' => now(), 'email_count' => $sent]);
            $note = " Emailed to {$sent} member" . ($sent === 1 ? '' : 's') . '.';
        }

        return redirect()->route('events.show', $event)->with('success', 'Event created.' . $note);
    }

    public function edit(ClubEvent $event): View
    {
        $this->authorizeManageOrAbort($event);
        $coachMode = auth()->user()->role === 'coach';
        [$composeAudiences] = $this->composeContext(auth()->user());

        return view('events.edit', compact('event', 'composeAudiences', 'coachMode'));
    }

    public function update(Request $request, ClubEvent $event): RedirectResponse
    {
        $this->authorizeManageOrAbort($event);
        $data = $this->validated($request);

        $payload = [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'location'    => $data['location'] ?? null,
            'event_type'  => $data['event_type'],
            'starts_at'   => $data['starts_at'],
            'ends_at'     => $data['ends_at'] ?? null,
        ];
        // Coaches can't change audience (locked to my_archers); admins can.
        if (auth()->user()->role !== 'coach' && ! $event->coach_id) {
            $payload['audience'] = in_array($data['audience'] ?? 'all', ['all', 'archers', 'coaches']) ? $data['audience'] : 'all';
        }

        $event->update($payload);

        return redirect()->route('events.show', $event)->with('success', 'Event updated.');
    }

    public function destroy(ClubEvent $event): RedirectResponse
    {
        $this->authorizeManageOrAbort($event);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }

    public function togglePin(ClubEvent $event): RedirectResponse
    {
        $this->authorizeManageOrAbort($event);
        $event->update(['pinned' => ! $event->pinned]);

        return back()->with('success', $event->pinned
            ? 'Event pinned — members see it on their dashboard until they RSVP.'
            : 'Event unpinned.');
    }

    /** Member RSVP (going / maybe / not_going). */
    public function rsvp(Request $request, ClubEvent $event): RedirectResponse
    {
        $this->authorizeView($event);

        $validated = $request->validate([
            'response' => ['required', 'in:going,maybe,not_going'],
        ]);

        ClubEventRsvp::updateOrCreate(
            ['club_event_id' => $event->id, 'user_id' => auth()->id()],
            ['response' => $validated['response'], 'responded_at' => now()],
        );

        return back()->with('success', 'Your RSVP has been saved: ' . ClubEventRsvp::RESPONSES[$validated['response']] . '.');
    }

    // ─────────────────────────────────────────────────────────────

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'location'    => ['nullable', 'string', 'max:200'],
            'event_type'  => ['required', 'in:training,competition,meeting,social,other'],
            'starts_at'   => ['required', 'date'],
            'ends_at'     => ['nullable', 'date', 'after_or_equal:starts_at'],
            'audience'    => ['nullable', 'in:all,archers,coaches,my_archers'],
            'pinned'      => ['nullable', 'boolean'],
            'send_email'  => ['nullable', 'boolean'],
        ]);
    }

    /** [composeAudiences|null, coachMode] — who can create and in what mode. */
    private function composeContext($user): array
    {
        if (in_array($user->role, ['super_admin', 'club_admin'])) {
            return [['all' => 'Everyone', 'archers' => 'Archers only', 'coaches' => 'Coaches only'], false];
        }
        if ($user->role === 'coach' && $user->coach) {
            return [['my_archers' => 'My assigned archers'], true];
        }
        return [null, false];
    }

    private function parseMonth(?string $value): Carbon
    {
        try {
            return $value ? Carbon::createFromFormat('Y-m', $value)->startOfMonth() : now()->startOfMonth();
        } catch (\Throwable $e) {
            return now()->startOfMonth();
        }
    }

    /** Read-only markers: existing training sessions + elimination matches. */
    private function otherItems($user, Carbon $start, Carbon $end): array
    {
        [$clubIds, $coachIds] = $this->viewerScope($user);
        $items = [];

        try {
            if ($coachIds->isNotEmpty()) {
                $trainings = TrainingSession::whereIn('coach_id', $coachIds)
                    ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                    ->get(['id', 'date', 'focus_area', 'coach_id']);
                foreach ($trainings as $t) {
                    $items[] = [
                        'date'  => Carbon::parse($t->date)->format('Y-m-d'),
                        'kind'  => 'training',
                        'label' => $t->focus_area ?: 'Training',
                        'url'   => route('coaches.training.show', [$t->coach_id, $t->id]),
                    ];
                }
            }
            if ($clubIds->isNotEmpty()) {
                $matches = EliminationMatch::whereIn('club_id', $clubIds)
                    ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                    ->get(['id', 'date', 'competition_name']);
                foreach ($matches as $m) {
                    $items[] = [
                        'date'  => Carbon::parse($m->date)->format('Y-m-d'),
                        'kind'  => 'match',
                        'label' => $m->competition_name ?: 'Match',
                        'url'   => route('elimination-matches.scorecard', $m->id),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Calendar side-items failed: ' . $e->getMessage());
        }

        return $items;
    }

    /** [clubIds, coachIds] the viewer's calendar covers. */
    private function viewerScope($user): array
    {
        if (in_array($user->role, ['super_admin', 'club_admin'])) {
            $club = $this->resolveClub();
            $clubIds  = collect($club ? [$club->id] : []);
            $coachIds = $club
                ? Coach::withoutGlobalScope(ClubScope::class)
                    ->whereHas('clubs', fn ($q) => $q->where('clubs.id', $club->id))
                    ->orWhere('club_id', $club->id)->pluck('id')
                : collect();
            return [$clubIds, $coachIds];
        }
        if ($user->role === 'coach' && $user->coach) {
            $coach   = $user->coach;
            $clubIds = $coach->clubs()->pluck('clubs.id')
                ->when($coach->club_id, fn ($c) => $c->push($coach->club_id))->unique()->values();
            return [$clubIds, collect([$coach->id])];
        }
        if ($user->archer) {
            $archer  = $user->archer;
            $clubIds = $archer->clubs()->pluck('clubs.id')
                ->when($archer->club_id, fn ($c) => $c->push($archer->club_id))->unique()->values();
            return [$clubIds, $archer->coaches()->pluck('coaches.id')];
        }
        return [collect(), collect()];
    }

    private function emailAudience(ClubEvent $event): int
    {
        $emails = collect();

        if ($event->audience === 'my_archers' && $event->coach) {
            $emails = $event->coach->archers()->withoutGlobalScope(ClubScope::class)
                ->with('user')->get()->map(fn ($m) => $m->user?->email);
        } else {
            $club = $event->club;
            if (in_array($event->audience, ['all', 'archers'])) {
                $emails = $emails->merge($club->archers()->withoutGlobalScope(ClubScope::class)
                    ->with('user')->get()->map(fn ($m) => $m->user?->email));
            }
            if (in_array($event->audience, ['all', 'coaches'])) {
                $emails = $emails->merge($club->coaches()->withoutGlobalScope(ClubScope::class)
                    ->with('user')->get()->map(fn ($m) => $m->user?->email));
            }
        }

        $emails = $emails->filter()->unique()
            ->reject(fn ($e) => strcasecmp($e, auth()->user()->email) === 0)->values();

        $sent = 0;
        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new ClubEventMail($event));
                $sent++;
            } catch (\Throwable $e) {
                Log::error('Failed to send event email: ' . $e->getMessage());
            }
        }
        return $sent;
    }

    private function resolveClub(): ?Club
    {
        if (app()->has('currentClub')) {
            return app('currentClub');
        }
        return auth()->user()?->club;
    }

    private function canManage(ClubEvent $event): bool
    {
        $user = auth()->user();
        return $user->role === 'super_admin'
            || ($user->role === 'club_admin' && (int) $user->club_id === (int) $event->club_id)
            || ($user->role === 'coach' && $user->coach && $event->coach_id === $user->coach->id);
    }

    private function authorizeManageOrAbort(ClubEvent $event): void
    {
        abort_unless($this->canManage($event), 403);
    }

    /** A user may view an event only if it's in their visible set. */
    private function authorizeView(ClubEvent $event): void
    {
        $visible = ClubEvent::visibleTo(auth()->user());
        abort_unless($visible && (clone $visible)->whereKey($event->id)->exists(), 403);
    }
}
