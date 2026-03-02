@extends('layouts.app')

@section('title', $archer->full_name . ' — Performance Analytics')
@section('header', 'Performance Analytics')
@section('subheader', $archer->ref_no . ' · ' . $archer->full_name)

@section('header-actions')
    <a href="{{ route('archers.show', $archer) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Profile
    </a>
@endsection

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Date Range Filter --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
        <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #6366f1;">
            <svg class="h-5 w-5 flex-shrink-0" style="color:#818cf8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Date Range</h3>
        </div>
        <div class="p-5">
            <form method="GET" action="{{ route('archers.performance', $archer) }}"
                  x-data="{ range: '{{ $range }}' }">

                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">Period</label>
                        <select name="range" x-model="range"
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="current_session">Current Session</option>
                            <option value="last_session">Last Session</option>
                            <option value="last7days">Last 7 Days</option>
                            <option value="last30days">Last 30 Days</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_year">This Year</option>
                            <option value="last_year">Last Year</option>
                            <option value="custom">Custom Range…</option>
                        </select>
                    </div>

                    <div x-show="range === 'custom'" x-cloak class="flex items-end gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">From</label>
                            <input type="date" name="from"
                                   value="{{ $range === 'custom' ? $from : '' }}"
                                   class="rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">To</label>
                            <input type="date" name="to"
                                   value="{{ $range === 'custom' ? $to : '' }}"
                                   class="rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>

                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-sm font-black text-white shadow-md transition-all active:scale-95"
                            style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                        Apply
                    </button>
                </div>

                <p class="mt-3 text-xs text-slate-400 font-medium">
                    Showing data from
                    <span class="text-slate-600 font-bold">{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</span>
                    to
                    <span class="text-slate-600 font-bold">{{ \Carbon\Carbon::parse($to)->format('d M Y') }}</span>
                </p>
            </form>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['Total Sessions', $totalSessions,          '#6366f1'],
            ['Best Score',     $bestScore ?: '—',       '#f59e0b'],
            ['Avg Score',      $avgScore  ?: '—',       '#10b981'],
            ['Avg Score/Arrow', $avgPerArrow ?: '—',    '#0ea5e9'],
        ] as [$label, $value, $color])
        <div class="bg-white rounded-2xl shadow-sm p-5 flex flex-col justify-between"
             style="border: 1px solid #e2e8f0; border-top: 4px solid {{ $color }};">
            <p class="text-xs font-bold uppercase tracking-widest" style="color:{{ $color }};">{{ $label }}</p>
            <p class="text-4xl font-black text-slate-900 mt-2 leading-none" style="font-family:'Barlow',sans-serif;">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    @if($totalSessions === 0)
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center" style="border: 1px solid #e2e8f0;">
            <svg class="h-12 w-12 text-slate-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
            </svg>
            <p class="text-slate-400 font-semibold">No sessions with recorded scores found for this date range.</p>
            <p class="text-slate-300 text-sm mt-1">Try selecting a wider range or "All Time".</p>
        </div>
    @endif

    @if($totalSessions > 0)

    {{-- Charts row: Trend + Round Type --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Chart 1: Score Trend --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
            <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #6366f1;">
                <svg class="h-5 w-5 flex-shrink-0" style="color:#818cf8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                </svg>
                <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Score Trend</h3>
            </div>
            <div class="p-5">
                <canvas id="trendChart" height="200"></canvas>
            </div>
        </div>

        {{-- Chart 2: Total Score by Date --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
            <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #f59e0b;">
                <svg class="h-5 w-5 flex-shrink-0" style="color:#fbbf24;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
                <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Total Score by Date</h3>
            </div>
            <div class="p-5">
                <canvas id="zoneChart" height="200"></canvas>
                {{-- Legend --}}
                <div class="flex flex-wrap gap-3 mt-3 justify-center">
                    <div class="flex items-center gap-1.5 text-xs font-medium text-slate-600 px-2 py-1 rounded-lg"
                         style="background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.3);">
                        <span class="h-2.5 w-2.5 rounded-full flex-shrink-0" style="background:#f59e0b;"></span>
                        <span style="color:#92400e; font-weight:700;">Competition</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-medium text-slate-600 px-2 py-1 rounded-lg"
                         style="background:rgba(99,102,241,0.10); border:1px solid rgba(99,102,241,0.25);">
                        <span class="h-2.5 w-2.5 rounded-full flex-shrink-0" style="background:#6366f1;"></span>
                        <span style="color:#3730a3; font-weight:700;">Training</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart 3: Competition vs Training --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
        <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #10b981;">
            <svg class="h-5 w-5 flex-shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
            </svg>
            <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Competition vs Training</h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                <div class="lg:col-span-2">
                    <canvas id="compVsTrainChart" height="160"></canvas>
                </div>
                <div class="space-y-3">
                    @foreach([
                        ['Competition', 'competition', '#f59e0b', 'rgba(245,158,11,0.1)', 'rgba(245,158,11,0.3)'],
                        ['Training',    'training',    '#10b981', 'rgba(16,185,129,0.1)',  'rgba(16,185,129,0.2)'],
                    ] as [$label, $key, $color, $bg, $border])
                    <div class="rounded-xl p-4" style="background:{{ $bg }}; border:1px solid {{ $border }};">
                        <p class="text-xs font-bold uppercase tracking-widest" style="color:{{ $color }};">{{ $label }}</p>
                        <p class="text-2xl font-black text-slate-900 mt-1" style="font-family:'Barlow',sans-serif;">
                            {{ $compVsTrainData[$key]['count'] }}
                            <span class="text-sm font-semibold text-slate-400">sessions</span>
                        </p>
                        <p class="text-xs text-slate-500 mt-1">
                            Avg: <strong class="text-slate-700">{{ $compVsTrainData[$key]['avg'] ?: '—' }}</strong>
                            &nbsp;|&nbsp;
                            Best: <strong class="text-slate-700">{{ $compVsTrainData[$key]['best'] ?: '—' }}</strong>
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Sessions Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
        <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #94a3b8;">
            <svg class="h-5 w-5 flex-shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
            </svg>
            <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">
                Sessions ({{ $totalSessions }})
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Round Type</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Score</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">X</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Hits</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Type</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($sessionsTable as $s)
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="px-5 py-3.5 font-semibold text-slate-800 whitespace-nowrap">{{ $s->date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-slate-600 font-medium">{{ $s->roundType->name }}</td>
                        <td class="px-5 py-3.5 text-right font-black text-slate-900" style="font-family:'Barlow',sans-serif;">{{ $s->score?->total_score ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-right text-slate-500 font-medium">{{ $s->score?->x_count ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-right text-slate-500 font-medium">{{ $s->score?->hit_count ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($s->is_competition)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">
                                    Competition
                                </span>
                            @else
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(16,185,129,0.10); color:#065f46; border:1px solid rgba(16,185,129,0.2);">
                                    Training
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('sessions.scorecard', $s) }}"
                               class="text-xs font-bold text-indigo-500 hover:text-indigo-700 transition-colors">View →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Arrow Analysis --}}
    @if($arrowAnalysis)
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
        <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #7c3aed;">
            <svg class="h-5 w-5 flex-shrink-0" style="color:#a78bfa;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
            </svg>
            <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Arrow Analysis</h3>
            <span class="ml-auto text-xs font-bold px-2.5 py-1 rounded-lg" style="background:rgba(124,58,237,0.25); color:#c4b5fd;">
                {{ $arrowAnalysis['arrows_per_end'] }}-arrow ends
            </span>
        </div>
        <div class="p-5">
            @if(collect($arrowAnalysis['positions'])->every(fn($p) => $p['count'] === 0))
                <p class="text-slate-500 text-sm">No arrow-level data available for this date range.</p>
            @else
            {{-- Callout badges --}}
            <div class="flex flex-wrap gap-3 mb-5">
                <div class="flex items-center gap-2 rounded-xl px-4 py-3"
                     style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2);">
                    <svg class="h-4 w-4 flex-shrink-0" style="color:#ef4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                    </svg>
                    <span class="text-sm font-semibold text-slate-700">
                        Weakest: <strong style="color:#dc2626;">Arrow {{ $arrowAnalysis['weakest'] }}</strong>
                        <span class="text-slate-400 font-normal ml-1">avg {{ $arrowAnalysis['positions'][$arrowAnalysis['weakest']]['avg'] }}</span>
                    </span>
                </div>
                <div class="flex items-center gap-2 rounded-xl px-4 py-3"
                     style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2);">
                    <svg class="h-4 w-4 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181"/>
                    </svg>
                    <span class="text-sm font-semibold text-slate-700">
                        Strongest: <strong style="color:#059669;">Arrow {{ $arrowAnalysis['strongest'] }}</strong>
                        <span class="text-slate-400 font-normal ml-1">avg {{ $arrowAnalysis['positions'][$arrowAnalysis['strongest']]['avg'] }}</span>
                    </span>
                </div>
            </div>
            <canvas id="arrowChart" height="{{ $arrowAnalysis['arrows_per_end'] <= 3 ? 80 : 120 }}"></canvas>
            @endif
        </div>
    </div>
    @endif

    @endif {{-- end if totalSessions > 0 --}}

</div>
@endsection

@push('scripts')
<script>
const trendLabels  = @json($trendLabels);
const trendData    = @json($trendData);
const trendMeta    = @json($trendMeta);
const zoneDatasets = @json($zoneDatasets);
const compVsTrain  = @json($compVsTrainData);

Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = '#64748b';

@if($arrowAnalysis)
const arrowAnalysis = @json($arrowAnalysis);
const arrowLabels   = Object.keys(arrowAnalysis.positions).map(p => 'Arrow ' + p);
const arrowAvgs     = Object.values(arrowAnalysis.positions).map(d => d.avg ?? null);

const numericAvgs = arrowAvgs.filter(v => typeof v === 'number');
const minX = numericAvgs.length ? Math.min(...numericAvgs) - 1 : 0;

const arrowColors = Object.keys(arrowAnalysis.positions).map(p => {
    const pos = parseInt(p);
    if (pos === arrowAnalysis.weakest)   return 'rgba(239,68,68,0.80)';
    if (pos === arrowAnalysis.strongest) return 'rgba(16,185,129,0.80)';
    return 'rgba(124,58,237,0.65)';
});

const arrowBorders = Object.keys(arrowAnalysis.positions).map(p => {
    const pos = parseInt(p);
    if (pos === arrowAnalysis.weakest)   return '#dc2626';
    if (pos === arrowAnalysis.strongest) return '#059669';
    return '#7c3aed';
});

new Chart(document.getElementById('arrowChart'), {
    type: 'bar',
    data: {
        labels: arrowLabels,
        datasets: [{
            label: 'Avg Score',
            data: arrowAvgs,
            backgroundColor: arrowColors,
            borderColor: arrowBorders,
            borderWidth: 1.5,
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        animation: {
            duration: 800,
            easing: 'easeOutQuart',
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (item) => {
                        const pos = item.dataIndex + 1;
                        const d   = arrowAnalysis.positions[pos];
                        const tag = pos === arrowAnalysis.weakest   ? ' ← weakest'
                                  : pos === arrowAnalysis.strongest ? ' ← strongest'
                                  : '';
                        return [` Avg: ${item.raw}${tag}`, ` Scored: ${d.count} arrows`];
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: false,
                min: Math.max(0, minX),
                grid: { color: '#f1f5f9' },
                title: { display: true, text: 'Average Score', color: '#94a3b8' }
            },
            y: { grid: { display: false } }
        }
    }
});
@endif

@if($totalSessions > 0)
// Score Trend
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Score',
            data: trendData,
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
            pointBackgroundColor: trendMeta.map(m => m.competition ? '#f59e0b' : '#6366f1'),
            pointBorderColor: trendMeta.map(m => m.competition ? '#d97706' : '#4f46e5'),
            pointRadius: 5,
            pointHoverRadius: 7,
            borderWidth: 2.5,
            fill: true,
            tension: 0.35,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (item) => {
                        const m = trendMeta[item.dataIndex];
                        return [' Score: ' + item.raw, ' Round: ' + m.round, ' ' + (m.competition ? 'Competition' : 'Training')];
                    }
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { maxRotation: 45 } },
            y: { beginAtZero: false, grid: { color: '#f1f5f9' } }
        }
    }
});

// Total Score by Date (bar — amber = competition, indigo = training)
new Chart(document.getElementById('zoneChart'), {
    type: 'bar',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Total Score',
            data: trendData,
            backgroundColor: trendMeta.map(m => m.competition ? 'rgba(245,158,11,0.80)' : 'rgba(99,102,241,0.75)'),
            borderColor:     trendMeta.map(m => m.competition ? '#d97706' : '#4f46e5'),
            borderWidth: 1.5,
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (item) => {
                        const m = trendMeta[item.dataIndex];
                        return [' Score: ' + item.raw, ' Round: ' + m.round, ' ' + (m.competition ? 'Competition' : 'Training')];
                    }
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { maxRotation: 45 } },
            y: { beginAtZero: false, grid: { color: '#f1f5f9' },
                 title: { display: true, text: 'Total Score', color: '#94a3b8' } }
        }
    }
});

// Competition vs Training
new Chart(document.getElementById('compVsTrainChart'), {
    type: 'bar',
    data: {
        labels: ['Sessions', 'Avg Score', 'Best Score'],
        datasets: [
            {
                label: 'Competition',
                data: [compVsTrain.competition.count, compVsTrain.competition.avg, compVsTrain.competition.best],
                backgroundColor: 'rgba(245,158,11,0.75)',
                borderColor: '#f59e0b',
                borderWidth: 1.5,
                borderRadius: 6,
            },
            {
                label: 'Training',
                data: [compVsTrain.training.count, compVsTrain.training.avg, compVsTrain.training.best],
                backgroundColor: 'rgba(16,185,129,0.75)',
                borderColor: '#10b981',
                borderWidth: 1.5,
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } }
        }
    }
});
@endif
</script>
@endpush
