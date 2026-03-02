@extends('layouts.app')

@section('title', 'Live Scoring — Real Time')
@section('header', 'Live Scoring')
@section('subheader', 'Real-Time Scoreboard')

@section('header-actions')
    {{-- Refresh interval selector --}}
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open"
                class="inline-flex items-center gap-2 text-sm font-bold px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
            <span x-text="$store.live.refreshLabel"></span>
        </button>
        <div x-show="open" @click.outside="open = false" x-cloak
             class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-slate-200 z-50 overflow-hidden">
            @foreach([
                [15,  '15 seconds'],
                [30,  '30 seconds'],
                [60,  '1 minute'],
                [120, '2 minutes'],
                [180, '3 minutes'],
                [300, '5 minutes'],
            ] as [$secs, $label])
            <button class="w-full text-left px-4 py-2.5 text-sm font-semibold hover:bg-amber-50 text-slate-700 transition-colors"
                    @click="$store.live.setInterval({{ $secs }}, '{{ $label }}'); open = false">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Date picker --}}
    <form method="GET" action="{{ route('live-scoring.realtime') }}" class="flex items-center gap-2">
        @if($user->role === 'national_team' || $user->role === 'super_admin')
            <input type="hidden" name="club_id"               value="{{ request('club_id') }}">
            <input type="hidden" name="state_team_id"         value="{{ request('state_team_id') }}">
            <input type="hidden" name="national_team_filter"  value="{{ request('national_team_filter') }}">
        @endif
        <input type="date" name="date" value="{{ $date }}"
               class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
               onchange="this.form.submit()">
    </form>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-5"
     x-data="liveScoring"
     x-init="init()">

    {{-- Filters (national_team / super_admin only) --}}
    @if(in_array($user->role, ['national_team', 'super_admin']))
    <form method="GET" action="{{ route('live-scoring.realtime') }}"
          class="bg-white rounded-2xl shadow-sm p-4 flex flex-wrap items-end gap-3"
          style="border: 1px solid #e2e8f0;">
        <input type="hidden" name="date" value="{{ $date }}">

        {{-- Club --}}
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Club</label>
            <select name="club_id"
                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All Clubs</option>
                @foreach($clubs as $club)
                    <option value="{{ $club->id }}" @selected(request('club_id') == $club->id)>{{ $club->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- State --}}
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">State Team</label>
            <select name="state_team_id"
                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All States</option>
                @foreach($stateTeams as $st)
                    <option value="{{ $st->id }}" @selected(request('state_team_id') == $st->id)>{{ $st->state }}</option>
                @endforeach
            </select>
        </div>

        {{-- National team --}}
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">National Team</label>
            <select name="national_team_filter"
                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All</option>
                @foreach($ntOptions as $opt)
                    <option value="{{ $opt }}" @selected(request('national_team_filter') === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="px-5 py-2 rounded-xl text-sm font-black text-white shadow-md transition-all active:scale-95"
                style="background: linear-gradient(135deg, #4338ca, #6366f1);">
            Filter
        </button>
        @if(request()->hasAny(['club_id', 'state_team_id', 'national_team_filter']))
        <a href="{{ route('live-scoring.realtime', ['date' => $date]) }}"
           class="px-4 py-2 rounded-xl text-sm font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
            Clear
        </a>
        @endif
    </form>
    @endif

    {{-- Status bar --}}
    <div class="flex items-center justify-between text-xs font-semibold text-slate-400">
        <div class="flex items-center gap-2">
            {{-- WebSocket status dot --}}
            <span class="h-2 w-2 rounded-full"
                  :class="wsConnected ? 'bg-emerald-400 animate-pulse' : 'bg-slate-300'"
                  title="WebSocket"></span>
            <span x-text="wsConnected ? 'Live (WebSocket)' : 'Polling mode'"></span>
        </div>
        <span>Last updated: <span x-text="lastUpdated"></span></span>
    </div>

    {{-- Scoreboard --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">

        {{-- Header --}}
        <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #f59e0b;">
            <svg class="h-5 w-5 flex-shrink-0" style="color:#fbbf24;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
            </svg>
            <div>
                <h2 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">
                    LIVE SCOREBOARD
                </h2>
                <p class="text-xs text-slate-400 font-medium">
                    {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                    &nbsp;·&nbsp;
                    <span x-text="rows.length + ' archer' + (rows.length === 1 ? '' : 's')"></span>
                </p>
            </div>
            <div class="ml-auto flex items-center gap-2">
                {{-- Countdown ring --}}
                <div class="relative h-8 w-8" title="Next poll">
                    <svg class="h-8 w-8 -rotate-90" viewBox="0 0 32 32">
                        <circle cx="16" cy="16" r="13" fill="none" stroke="#334155" stroke-width="3"/>
                        <circle cx="16" cy="16" r="13" fill="none" stroke="#f59e0b" stroke-width="3"
                                stroke-dasharray="81.68"
                                :stroke-dashoffset="81.68 - (81.68 * countdown / $store.live.interval)"
                                style="transition: stroke-dashoffset 1s linear;"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-xs font-black text-amber-400"
                          x-text="countdown"></span>
                </div>
            </div>
        </div>

        {{-- Empty state --}}
        <template x-if="rows.length === 0">
            <div class="px-6 py-16 text-center">
                <svg class="h-12 w-12 text-slate-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497"/>
                </svg>
                <p class="text-slate-500 font-semibold">No scored sessions found for this date.</p>
                <p class="text-slate-400 text-sm mt-1">Scores will appear here as archers record them.</p>
            </div>
        </template>

        {{-- Table --}}
        <template x-if="rows.length > 0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead style="background:#f8fafc; border-bottom:1px solid #e2e8f0; position:sticky; top:0; z-index:10;">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest w-12">Pos</th>
                            <th class="px-4 py-3 text-left   text-xs font-bold text-slate-500 uppercase tracking-widest">Name</th>
                            <th class="px-4 py-3 text-left   text-xs font-bold text-slate-500 uppercase tracking-widest hidden md:table-cell">Club</th>
                            <th class="px-4 py-3 text-left   text-xs font-bold text-slate-500 uppercase tracking-widest hidden lg:table-cell">State</th>
                            {{-- Dynamic distance columns --}}
                            <template x-for="d in maxDistances" :key="'h'+d">
                                <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:table-cell"
                                    x-text="'Dist ' + d"></th>
                            </template>
                            <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:table-cell">10+X</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:table-cell">X</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest hidden lg:table-cell">Avg/Arrow</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="row in rows" :key="row.session_id">
                            <tr class="transition-colors duration-500"
                                :class="{
                                    'bg-amber-50/60': row.position === 1,
                                    'bg-emerald-50/60 border-l-4 border-emerald-400': row.flash,
                                }">
                                {{-- Position --}}
                                <td class="px-4 py-3 text-center">
                                    <template x-if="row.position === 1">
                                        <span class="inline-flex items-center justify-center h-7 w-7 rounded-full text-xs font-black"
                                              style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
                                            1
                                        </span>
                                    </template>
                                    <template x-if="row.position > 1">
                                        <span class="text-sm font-bold text-slate-600" x-text="row.position"></span>
                                    </template>
                                </td>

                                {{-- Name --}}
                                <td class="px-4 py-3">
                                    <p class="font-bold text-slate-800" x-text="row.name"></p>
                                </td>

                                {{-- Club --}}
                                <td class="px-4 py-3 text-slate-500 font-medium hidden md:table-cell" x-text="row.club"></td>

                                {{-- State --}}
                                <td class="px-4 py-3 text-slate-500 font-medium hidden lg:table-cell" x-text="row.state"></td>

                                {{-- Dynamic distance cells --}}
                                <template x-for="(dist, di) in row.distances" :key="'d'+di">
                                    <td class="px-4 py-3 text-center font-semibold text-slate-700 hidden sm:table-cell">
                                        <span x-text="dist !== null ? dist : '—'"></span>
                                    </td>
                                </template>

                                {{-- Total --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xl font-black"
                                          :style="row.position === 1 ? 'color:#f59e0b; font-family:Barlow,sans-serif;' : 'color:#0f172a; font-family:Barlow,sans-serif;'"
                                          x-text="row.total"></span>
                                </td>

                                {{-- 10+X --}}
                                <td class="px-4 py-3 text-center font-black text-amber-500 hidden sm:table-cell"
                                    style="font-family:'Barlow',sans-serif;" x-text="row.tens_plus_x"></td>

                                {{-- X --}}
                                <td class="px-4 py-3 text-center font-black text-emerald-600 hidden sm:table-cell"
                                    style="font-family:'Barlow',sans-serif;" x-text="row.x_count"></td>

                                {{-- Avg/Arrow --}}
                                <td class="px-4 py-3 text-center text-slate-600 font-semibold hidden lg:table-cell"
                                    x-text="row.avg_per_arrow"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>
    </div>

</div>
@endsection

@push('scripts')
<script>
{{-- Pusher CDN — loaded only when WebSocket is configured --}}
@if(config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key'))
</script>
<script src="https://js.pusher.com/8.3.0/pusher.min.js"></script>
<script>
@endif

// ── Alpine global store for the refresh interval ──────────────────────────
document.addEventListener('alpine:init', () => {
    Alpine.store('live', {
        interval:     60,
        refreshLabel: '1 minute',
        setInterval(secs, label) {
            this.interval     = secs;
            this.refreshLabel = label;
            Alpine.store('liveRefreshTrigger', Date.now()); // notify component
        },
    });
});

// ── Main component ─────────────────────────────────────────────────────────
function liveScoring() {
    return {
        rows:         @json($scoreboard),
        maxDistances: @json($maxDistances),
        wsConnected:  false,
        lastUpdated:  '—',
        countdown:    60,
        _timer:       null,
        _countTimer:  null,

        init() {
            this.startPolling();
            this.initWebSocket();
            this.$watch(() => Alpine.store('liveRefreshTrigger'), () => {
                this.restartPolling();
            });
        },

        // ── Polling ───────────────────────────────────────────────────────
        startPolling() {
            const secs = Alpine.store('live').interval;
            this.countdown = secs;

            clearInterval(this._timer);
            clearInterval(this._countTimer);

            this._timer = setInterval(() => this.fetchData(), secs * 1000);

            this._countTimer = setInterval(() => {
                this.countdown = Math.max(0, this.countdown - 1);
            }, 1000);
        },

        restartPolling() {
            this.startPolling();
        },

        async fetchData() {
            try {
                const params = new URLSearchParams(window.location.search);
                const resp   = await fetch(`{{ route('live-scoring.realtime.data') }}?` + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await resp.json();
                this.rows         = json.rows;
                this.maxDistances = json.max_distances ?? this.maxDistances;
                this.lastUpdated  = new Date().toLocaleTimeString();
                this.countdown   = Alpine.store('live').interval;
            } catch (e) {
                console.warn('Live scoring poll failed:', e);
            }
        },

        // ── WebSocket ─────────────────────────────────────────────────────
        initWebSocket() {
            @if(config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key'))
            if (typeof Pusher === 'undefined') return;

            const pusher  = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster', 'ap1') }}',
            });

            const channel = pusher.subscribe('live-scoring');

            channel.bind('pusher:subscription_succeeded', () => {
                this.wsConnected = true;
            });

            channel.bind('ScoreUpdated', (data) => {
                this.handleScoreUpdate(data);
                this.lastUpdated = new Date().toLocaleTimeString();
                this.countdown   = Alpine.store('live').interval; // reset poll countdown
            });

            pusher.connection.bind('disconnected', () => { this.wsConnected = false; });
            pusher.connection.bind('connected',    () => { this.wsConnected = true;  });
            @endif
        },

        // ── Handle a single-row broadcast update ─────────────────────────
        handleScoreUpdate(updatedRow) {
            const idx = this.rows.findIndex(r => r.session_id === updatedRow.session_id);

            if (idx !== -1) {
                Object.assign(this.rows[idx], updatedRow);
            } else {
                this.rows.push({ ...updatedRow, flash: false });
            }

            // Expand maxDistances if this row has more segments than before
            const newMax = Math.max(...this.rows.map(r => (r.distances || []).length));
            if (newMax > this.maxDistances) {
                this.maxDistances = newMax;
                this.rows.forEach(r => {
                    while ((r.distances || []).length < this.maxDistances) {
                        r.distances = [...(r.distances || []), null];
                    }
                });
            }

            this.resort();

            // Flash the updated row green for 2 s
            const newIdx = this.rows.findIndex(r => r.session_id === updatedRow.session_id);
            if (newIdx !== -1) {
                this.rows[newIdx].flash = true;
                setTimeout(() => { this.rows[newIdx].flash = false; }, 2000);
            }
        },

        // ── Re-sort and re-rank in place ──────────────────────────────────
        resort() {
            this.rows.sort((a, b) =>
                b.total - a.total || b.x_count - a.x_count || b.tens_plus_x - a.tens_plus_x
            );
            this.rows.forEach((r, i) => r.position = i + 1);
        },
    };
}
</script>
@endpush
