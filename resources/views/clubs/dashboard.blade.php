@extends('layouts.app')

@section('title', $club->name . ' — Dashboard')
@section('og_image', $club->logo ? asset('storage/' . $club->logo) : '')
@section('og_description', $club->name . ' · Club Dashboard · Archery Stats')
@section('header', $club->name)
@section('subheader', 'Club Performance Dashboard')

@section('header-actions')
    <a href="{{ route('clubs.show', $club) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Club Profile
    </a>
    <a href="{{ route('clubs.members', $club) }}"
       class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
       style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
        Members
    </a>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ range: '{{ $range }}' }">

    {{-- Date range + archer filter --}}
    <form method="GET" action="{{ route('clubs.dashboard', $club) }}" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Period</label>
            <select name="range" x-model="range"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="last7days">Last 7 Days</option>
                <option value="last30days">Last 30 Days</option>
                <option value="this_year">This Year</option>
                <option value="last_year">Last Year</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
        <div x-show="range === 'custom'" x-cloak class="flex items-end gap-2">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}"
                       class="rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}"
                       class="rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Archer</label>
            <select name="archer_id"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All Archers</option>
                @foreach($club->archers->sortBy('full_name') as $a)
                    <option value="{{ $a->id }}" {{ $archerFilter == $a->id ? 'selected' : '' }}>
                        {{ $a->full_name }} ({{ $a->ref_no }})
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="px-5 py-2 rounded-xl text-sm font-black text-white shadow-sm transition-all active:scale-95"
                style="background: linear-gradient(135deg,#4338ca,#6366f1); font-family:'Barlow',sans-serif;">
            Apply
        </button>
        @if($archerFilter)
            <a href="{{ route('clubs.dashboard', array_merge(request()->except('archer_id'), ['club' => $club])) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                Clear Filter
            </a>
        @endif
    </form>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #6366f1;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Archers</p>
            <p class="text-4xl font-black text-slate-900 mt-1" style="font-family:'Barlow',sans-serif;">{{ $totalArchers }}</p>
            <p class="text-xs text-slate-500 mt-1">Club members</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #10b981;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active</p>
            <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#10b981;">{{ $activeArchers }}</p>
            <p class="text-xs text-slate-500 mt-1">This period</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #f59e0b;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Best Score</p>
            <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#f59e0b;">{{ $bestScore ?: '—' }}</p>
            <p class="text-xs text-slate-500 mt-1">Highest session</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #3b82f6;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Club Avg</p>
            <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#3b82f6;">{{ $clubAvg ?: '—' }}</p>
            <p class="text-xs text-slate-500 mt-1">Avg score</p>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Chart 1: Score Trend --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
            <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Club Score Trend</h3>
            </div>
            <div class="p-4">
                @if(count($trendLabels) > 0)
                    <canvas id="trendChart" height="200"></canvas>
                @else
                    <div class="flex items-center justify-center h-40 text-slate-400 text-sm">No sessions in this period.</div>
                @endif
            </div>
        </div>

        {{-- Chart 2: Leaderboard --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
            <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Top Archers</h3>
            </div>
            <div class="p-4">
                @if($leaderboard->isNotEmpty())
                    <canvas id="leaderboardChart" height="200"></canvas>
                @else
                    <div class="flex items-center justify-center h-40 text-slate-400 text-sm">No data for this period.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Archer Performance Summary Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
            <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Archer Performance</h3>
        </div>
        @if($archerStats->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Archer</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Sessions</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Best</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Avg</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Hit Rate</th>
                        <th class="text-right px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($archerStats as $stat)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $stat['archer']->photo_url }}" alt="{{ $stat['archer']->full_name }}"
                                     class="h-8 w-8 rounded-full object-cover flex-shrink-0">
                                <div>
                                    <p class="font-semibold text-slate-900 text-sm">{{ $stat['archer']->full_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $stat['archer']->ref_no }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-700 font-medium">{{ $stat['sessions'] }}</td>
                        <td class="px-4 py-3 text-center font-semibold" style="color:#f59e0b;">{{ $stat['best'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-center font-semibold" style="color:#3b82f6;">{{ $stat['avg'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">
                            @if($stat['sessions'] > 0)
                                <div class="flex items-center justify-center gap-1">
                                    <div class="h-1.5 w-16 rounded-full bg-slate-200 overflow-hidden">
                                        <div class="h-full rounded-full" style="width:{{ $stat['hit_rate'] }}%; background:#6366f1;"></div>
                                    </div>
                                    <span class="text-xs">{{ $stat['hit_rate'] }}%</span>
                                </div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('clubs.dashboard', array_merge(request()->query(), ['club' => $club->id, 'archer_id' => $stat['archer']->id])) }}"
                               class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                               style="background:#eef2ff; color:#4338ca;">
                                Results →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="px-6 py-10 text-center text-slate-400 text-sm">No archer data available.</div>
        @endif
    </div>

    {{-- All Results Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="flex items-center justify-between px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
            <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">
                All Results
            </h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background:rgba(255,255,255,0.12); color:#c7d2fe;">
                {{ $resultSessions->count() }} {{ Str::plural('session', $resultSessions->count()) }}
            </span>
        </div>
        @if($resultSessions->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Archer</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Round</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Score</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">X</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Gold</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Hit%</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="text-right px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($resultSessions as $session)
                    @php
                        $sc       = $session->score;
                        $hits     = $sc?->hit_count   ?? 0;
                        $misses   = $sc?->miss_count  ?? 0;
                        $arrows   = $hits + $misses;
                        $hitPct   = $arrows > 0 ? round($hits / $arrows * 100) : null;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3 text-slate-500 whitespace-nowrap">
                            <p class="font-medium text-slate-800">{{ $session->date->format('d M Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $session->date->format('l') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ $session->archer->photo_url }}" alt="{{ $session->archer->full_name }}"
                                     class="h-7 w-7 rounded-full object-cover flex-shrink-0">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $session->archer->full_name }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $session->archer->ref_no }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <p>{{ $session->roundType->name }}</p>
                            @if($session->effective_distance)
                                <p class="text-xs text-slate-400">{{ $session->effective_distance }}m</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-lg font-black" style="font-family:'Barlow',sans-serif; color:#6366f1;">
                                {{ $sc?->total_score ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-slate-700">
                            {{ $sc?->x_count > 0 ? $sc->x_count : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center font-semibold" style="color:#f59e0b;">
                            {{ $sc?->gold_count > 0 ? $sc->gold_count : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-600">
                            @if($hitPct !== null)
                                <div class="flex items-center justify-center gap-1">
                                    <div class="h-1.5 w-12 rounded-full bg-slate-200 overflow-hidden">
                                        <div class="h-full rounded-full" style="width:{{ $hitPct }}%; background:#10b981;"></div>
                                    </div>
                                    {{ $hitPct }}%
                                </div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                  style="{{ $session->is_competition ? 'background:#fef3c7; color:#92400e;' : 'background:#f0fdf4; color:#166534;' }}">
                                {{ $session->is_competition ? 'Competition' : 'Training' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('sessions.scorecard', $session) }}"
                               class="text-xs font-semibold px-3 py-1.5 rounded-lg"
                               style="background:#eef2ff; color:#4338ca;">View →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="px-6 py-10 text-center text-slate-400 text-sm">No sessions in this period.</div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const trendLabels = @json($trendLabels);
const trendData   = @json($trendData);
const lbLabels    = @json($leaderboard->pluck('archer')->map(fn($a) => $a['full_name'] ?? $a->full_name)->toArray());
const lbData      = @json($leaderboard->pluck('avg')->toArray());

if (document.getElementById('trendChart')) {
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Club Avg Score',
                data: trendData,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#6366f1',
                pointRadius: 4,
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxRotation: 45, font: { size: 11 } } },
                y: { beginAtZero: false, grid: { color: '#f1f5f9' },
                     title: { display: true, text: 'Avg Score', color: '#94a3b8', font: { size: 11 } } }
            }
        }
    });
}

if (document.getElementById('leaderboardChart')) {
    new Chart(document.getElementById('leaderboardChart'), {
        type: 'bar',
        data: {
            labels: lbLabels,
            datasets: [{
                label: 'Avg Score',
                data: lbData,
                backgroundColor: 'rgba(99,102,241,0.75)',
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                y: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
}
</script>
@endpush
