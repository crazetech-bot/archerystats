@extends('layouts.app')

@section('title', 'Scorecard — ' . $session->archer->full_name)
@section('header', 'Scorecard')
@section('subheader', $session->roundType->name . ' — ' . $session->date->format('d M Y'))

@section('header-actions')
    <a href="{{ route('sessions.index', $session->archer) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        All Sessions
    </a>
@endsection

@section('content')
@php
    $rt          = $session->roundType;
    $score       = $session->score;
    $ends        = $score->ends->keyBy('end_number');
    $ape         = $rt->arrows_per_end;
    $endsPerSet  = 6;
    $totalSets   = (int) ceil($rt->num_ends / $endsPerSet);

    // Build per-set distance labels and scoring systems for multi-distance rounds
    $distSegments   = $rt->distance_segments ?? null;
    $segmentLabels  = [];
    $segmentScoring = [];
    if ($distSegments) {
        $setIdx = 1;
        foreach ($distSegments as $seg) {
            $pages  = (int) ceil(($seg['num_ends'] ?? $endsPerSet) / $endsPerSet);
            $segSys = $seg['scoring'] ?? $scoringSystem;
            for ($p = 0; $p < $pages; $p++) {
                $segmentLabels[$setIdx]  = $seg['label'] ?? ($seg['distance'] . 'm · ' . $seg['face'] . 'cm');
                $segmentScoring[$setIdx] = $segSys;
                $setIdx++;
            }
        }
    }

    // Build per-set valid hints only when scoring differs across sets
    $validHintMap = [];
    if ($segmentScoring && count(array_unique($segmentScoring)) > 1) {
        foreach ($segmentScoring as $setNum => $sys) {
            $validHintMap[$setNum] = match($sys) {
                'compound',
                'reduced'       => 'X &nbsp;·&nbsp; 10–5 &nbsp;·&nbsp; M (miss)',
                'six_ring'      => 'X &nbsp;·&nbsp; 10–6 &nbsp;·&nbsp; M (miss)',
                'standard_x11'  => 'X (=11 pts) &nbsp;·&nbsp; 10–1 &nbsp;·&nbsp; M (miss)',
                'six_ring_x11'  => 'X (=11 pts) &nbsp;·&nbsp; 10–6 &nbsp;·&nbsp; M (miss)',
                'field'         => 'X (=6 pts) &nbsp;·&nbsp; 6–1 &nbsp;·&nbsp; M (miss)',
                '3d'            => '20 &nbsp;·&nbsp; 17 &nbsp;·&nbsp; 10 &nbsp;·&nbsp; M (miss)',
                'clout'         => '5–1 &nbsp;·&nbsp; M (miss)',
                default         => 'X &nbsp;·&nbsp; 10–1 &nbsp;·&nbsp; M (miss)',
            };
        }
    }

    $initialEnds = [];
    for ($e = 1; $e <= $rt->num_ends; $e++) {
        $end = $ends->get($e);
        $arrows = [];
        for ($a = 0; $a < $ape; $a++) {
            $val = $end?->arrow_values[$a] ?? null;
            $arrows[] = $val === null ? '' : (string) $val;
        }
        $initialEnds[] = ['arrows' => $arrows];
    }

    $validHint = match($scoringSystem) {
        'compound',
        'reduced'       => 'X &nbsp;·&nbsp; 10–5 &nbsp;·&nbsp; M (miss)',
        'six_ring'      => 'X &nbsp;·&nbsp; 10–6 &nbsp;·&nbsp; M (miss)',
        'standard_x11'  => 'X (=11 pts) &nbsp;·&nbsp; 10–1 &nbsp;·&nbsp; M (miss)',
        'six_ring_x11'  => 'X (=11 pts) &nbsp;·&nbsp; 10–6 &nbsp;·&nbsp; M (miss)',
        'field'         => 'X (=6 pts) &nbsp;·&nbsp; 6–1 &nbsp;·&nbsp; M (miss)',
        '3d'            => '20 &nbsp;·&nbsp; 17 &nbsp;·&nbsp; 10 &nbsp;·&nbsp; M (miss)',
        'clout'         => '5–1 &nbsp;·&nbsp; M (miss)',
        default         => 'X &nbsp;·&nbsp; 10–1 &nbsp;·&nbsp; M (miss)',
    };
    $goldLabel = match($scoringSystem) {
        'field'  => 'X',
        '3d'     => '20s',
        'clout'  => '5s',
        default  => '10+X',
    };
    $maxScore = $rt->num_ends * $ape * $rt->max_score_per_arrow;
@endphp

@include('partials.help-panel', [
    'title' => 'How to use the scorecard',
    'color' => 'indigo',
    'items' => [
        'Click a cell and type your arrow value. Valid values are shown in the hint bar above each segment.',
        '<strong>Standard (recurve/barebow):</strong> X, 10–1, or M (miss). X counts as 10 pts.',
        '<strong>Compound (reduced):</strong> X, 10–5, or M. X counts as 10 pts.',
        '<strong>Field:</strong> X (=6), 6–1, or M. <strong>3D:</strong> 20, 17, 10, or M.',
        'The <strong>end total</strong> and <strong>running total</strong> update automatically as you type.',
        'Use <strong>Next Set / Previous Set</strong> to move between groups of 6 ends.',
        'Scores are <strong>saved automatically</strong> when you move between sets — you can close the page and return later.',
        'The <strong>summary bar</strong> at the top shows your Total, X count, 10+X count, Hits, and Misses.',
    ],
])

<div class="max-w-5xl mx-auto space-y-5"
     x-data="{
         ends:           {{ Js::from($initialEnds) }},
         endsPerSet:     {{ $endsPerSet }},
         currentSet:     1,
         scoringSystem:  '{{ $scoringSystem }}',
         segmentScoring: {{ Js::from($segmentScoring) }},

         get totalSets() {
             return Math.ceil(this.ends.length / this.endsPerSet);
         },

         get currentScoringSystem() {
             return this.segmentScoring[this.currentSet] || this.scoringSystem;
         },

         arrowVal(v) {
             v = String(v).toUpperCase().trim();
             if (v === 'M' || v === '') return 0;
             const sys = this.currentScoringSystem;
             if (sys === 'field') {
                 if (v === 'X') return 6;
                 const n = parseInt(v);
                 return (!isNaN(n) && n >= 1 && n <= 6) ? n : 0;
             }
             if (sys === '3d') {
                 const n = parseInt(v);
                 return [20, 17, 10].includes(n) ? n : 0;
             }
             if (sys === 'clout') {
                 const n = parseInt(v);
                 return (!isNaN(n) && n >= 1 && n <= 5) ? n : 0;
             }
             if (sys === 'reduced') {
                 if (v === 'X') return 10;
                 const n = parseInt(v);
                 return (!isNaN(n) && n >= 5 && n <= 10) ? n : 0;
             }
             // standard / compound
             if (v === 'X') return 10;
             const n = parseInt(v);
             return (!isNaN(n) && n >= 1 && n <= 10) ? n : 0;
         },

         isX(v) { return String(v).toUpperCase().trim() === 'X'; },

         isGold(v) {
             v = String(v).toUpperCase().trim();
             const sys = this.currentScoringSystem;
             if (sys === 'field')  return v === 'X';
             if (sys === '3d')     return v === '20';
             if (sys === 'clout')  return v === '5';
             return v === 'X' || v === '10';  // standard / compound / reduced
         },

         endSum(i)     { return this.ends[i].arrows.reduce((s, v) => s + this.arrowVal(v), 0); },
         endGold(i)    { return this.ends[i].arrows.filter(v => this.isGold(v)).length; },
         endX(i)       { return this.ends[i].arrows.filter(v => this.isX(v)).length; },
         endComplete(i){ return this.ends[i].arrows.every(v => String(v).trim() !== '' && v !== null); },

         endRunning(i) {
             let t = 0;
             for (let j = 0; j <= i; j++) t += this.endSum(j);
             return t;
         },

         setTotal(setNum) {
             const start = (setNum - 1) * this.endsPerSet;
             const end   = Math.min(setNum * this.endsPerSet, this.ends.length);
             let t = 0;
             for (let i = start; i < end; i++) t += this.endSum(i);
             return t;
         },

         setGold(setNum) {
             const start = (setNum - 1) * this.endsPerSet;
             const end   = Math.min(setNum * this.endsPerSet, this.ends.length);
             let t = 0;
             for (let i = start; i < end; i++) t += this.endGold(i);
             return t;
         },

         setX(setNum) {
             const start = (setNum - 1) * this.endsPerSet;
             const end   = Math.min(setNum * this.endsPerSet, this.ends.length);
             let t = 0;
             for (let i = start; i < end; i++) t += this.endX(i);
             return t;
         },

         grandTotal() { return this.ends.reduce((s, _, i) => s + this.endSum(i), 0); },
         totalGold()  { return this.ends.reduce((s, _, i) => s + this.endGold(i), 0); },
         totalX()     { return this.ends.reduce((s, _, i) => s + this.endX(i), 0); },

         handleInput(endIdx, arrowIdx, event) {
             let v = event.target.value.toUpperCase().trim();
             const sys = this.currentScoringSystem;

             if (v !== '') {
                 let valid = false;
                 if (sys === 'field') {
                     const n = parseInt(v);
                     valid = v === 'X' || v === 'M' || (!isNaN(n) && n >= 1 && n <= 6);
                 } else if (sys === '3d') {
                     const n = parseInt(v);
                     if (v === 'M')                      valid = true;
                     else if ([20, 17, 10].includes(n))  valid = true;
                     else if (v.length === 1 && ['1','2'].includes(v)) valid = true; // partial prefix
                     else valid = false;
                 } else if (sys === 'clout') {
                     const n = parseInt(v);
                     valid = v === 'M' || (!isNaN(n) && n >= 1 && n <= 5);
                 } else if (sys === 'compound') {
                     const n = parseInt(v);
                     valid = v === 'X' || v === 'M' || v === '1' || (!isNaN(n) && n >= 6 && n <= 10);
                 } else if (sys === 'reduced') {
                     const n = parseInt(v);
                     valid = v === 'X' || v === 'M' || v === '1' || (!isNaN(n) && n >= 5 && n <= 10);
                 } else if (sys === 'six_ring' || sys === 'six_ring_x11') {
                     const n = parseInt(v);
                     valid = v === 'X' || v === 'M' || v === '1' || (!isNaN(n) && n >= 6 && n <= 10);
                 } else {
                     const n = parseInt(v);
                     valid = v === 'X' || v === 'M' || (!isNaN(n) && n >= 1 && n <= 10);
                 }
                 if (!valid) v = '';
                 else if (v !== 'X' && v !== 'M' && !isNaN(parseInt(v))) v = String(parseInt(v));
             }

             event.target.value = v;
             this.ends[endIdx].arrows[arrowIdx] = v;
         }
     }">

    {{-- ── Session Info + Live Totals ───────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f8faff, #f0f4ff);">
            <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
            </span>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-sm font-bold text-gray-900">{{ $session->archer->full_name }}</h2>
                    <span class="text-xs font-mono text-indigo-600 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-lg">{{ $session->archer->ref_no }}</span>
                    @if($session->is_competition)
                        <span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-lg">Competition</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $session->archer->club?->name ?? '—' }}
                    @if($session->location) &mdash; {{ $session->location }}@endif
                    @if($session->is_competition && $session->competition_name) &mdash; {{ $session->competition_name }}@endif
                </p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-bold text-gray-800">{{ $session->roundType->name }}</p>
                <p class="text-xs text-gray-500">{{ $session->date->format('d M Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 divide-x divide-gray-100">
            <div class="px-6 py-4 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Score</p>
                <p class="text-3xl font-black text-indigo-700" x-text="grandTotal()">{{ $score->total_score }}</p>
                <p class="text-xs text-gray-400">of {{ $maxScore }}</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ $goldLabel }}</p>
                <p class="text-3xl font-black text-amber-600" x-text="totalGold()">{{ $score->gold_count }}</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">X</p>
                <p class="text-3xl font-black text-emerald-600" x-text="totalX()">{{ $score->x_count }}</p>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-3 text-sm font-medium flex items-center gap-2">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Scorecard Card ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Card header + dot indicator --}}
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #fffbeb, #fef3c7);">
            <div class="flex items-center gap-3">
                <span class="h-8 w-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Score Entry</h2>
                    @if($validHintMap)
                        @foreach($validHintMap as $setNum => $hint)
                        <p class="text-xs text-gray-500"
                           x-show="currentSet === {{ $setNum }}"
                           @if($setNum > 1) style="display:none" @endif>{!! "Valid: $hint" !!}</p>
                        @endforeach
                    @else
                        <p class="text-xs text-gray-500">{!! "Valid: $validHint" !!}</p>
                    @endif
                </div>
            </div>
            @if($totalSets > 1)
            <div class="flex items-center gap-1.5" title="Jump to sheet">
                @for($s = 1; $s <= $totalSets; $s++)
                    <button type="button" @click="currentSet = {{ $s }}"
                            :class="currentSet === {{ $s }}
                                ? 'w-7 h-2.5 rounded-full bg-amber-500'
                                : 'w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400'"
                            class="transition-all duration-200">
                    </button>
                @endfor
            </div>
            @endif
        </div>

        {{-- Set navigation (only when there are multiple sets) --}}
        @if($totalSets > 1)
        <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
            <button type="button"
                    @click="if(currentSet > 1) currentSet--"
                    :disabled="currentSet === 1"
                    :class="currentSet === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100 active:scale-95'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition-all shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
                Previous
            </button>

            <div class="text-center">
                <p class="text-sm font-bold text-gray-800">
                    Score Sheet&nbsp;<span x-text="currentSet"></span>&nbsp;of&nbsp;{{ $totalSets }}
                </p>
                <p class="text-xs text-gray-500"
                   x-text="`Ends ${(currentSet - 1) * endsPerSet + 1}–${Math.min(currentSet * endsPerSet, ends.length)}`">
                </p>
                @if($segmentLabels)
                <p class="text-xs font-semibold text-emerald-600 mt-0.5"
                   x-text="({{ Js::from($segmentLabels) }})[currentSet] || ''"></p>
                @endif
            </div>

            <button type="button"
                    @click="if(currentSet < totalSets) currentSet++"
                    :disabled="currentSet >= totalSets"
                    :class="currentSet >= totalSets ? 'opacity-40 cursor-not-allowed' : 'hover:opacity-90 active:scale-95'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white shadow-md transition-all"
                    style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                Next
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </button>
        </div>
        @endif

        <form method="POST" action="{{ route('sessions.saveScores', $session) }}">
            @csrf @method('PUT')

            {{-- Hidden inputs — all ends always submitted regardless of current set --}}
            @for($e = 1; $e <= $rt->num_ends; $e++)
                @for($a = 0; $a < $ape; $a++)
                    <input type="hidden"
                           name="arrows[{{ $e }}][{{ $a }}]"
                           x-bind:value="ends[{{ $e - 1 }}].arrows[{{ $a }}]">
                @endfor
            @endfor

            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">End</th>
                            @for($a = 1; $a <= $ape; $a++)
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $a }}</th>
                            @endfor
                            <th class="px-3 py-3 text-center text-xs font-bold text-amber-600 uppercase tracking-wider bg-amber-50/60">Sum</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-indigo-600 uppercase tracking-wider bg-indigo-50/60">Tot.</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $goldLabel }}</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">X</th>
                        </tr>
                    </thead>

                    @for($set = 1; $set <= $totalSets; $set++)
                        @php
                            $setStart = ($set - 1) * $endsPerSet + 1;
                            $setEnd   = min($set * $endsPerSet, $rt->num_ends);
                        @endphp
                        <tbody x-show="currentSet === {{ $set }}"
                               @if($set > 1) style="display: none;" @endif
                               class="divide-y divide-gray-100">

                            {{-- Segment header row for multi-distance rounds --}}
                            @if($segmentLabels && isset($segmentLabels[$set]))
                            <tr class="bg-emerald-50 border-b border-emerald-200">
                                <td colspan="{{ $ape + 5 }}" class="px-4 py-2">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">
                                            {{ $segmentLabels[$set] }}
                                        </span>
                                        @if(isset($validHintMap[$set]))
                                        <span class="text-xs text-emerald-600 font-medium">
                                            &mdash; Valid: {!! $validHintMap[$set] !!}
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endif

                            @for($e = $setStart; $e <= $setEnd; $e++)
                                @php $ei = $e - 1; @endphp
                                <tr :class="endComplete({{ $ei }}) ? 'bg-emerald-50/50' : 'bg-white'"
                                    class="transition-colors">
                                    <td class="px-3 py-2">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">{{ $e }}</span>
                                    </td>
                                    @for($a = 0; $a < $ape; $a++)
                                        <td class="px-2 py-2 text-center">
                                            <input type="text"
                                                   maxlength="2"
                                                   x-model="ends[{{ $ei }}].arrows[{{ $a }}]"
                                                   @input="handleInput({{ $ei }}, {{ $a }}, $event)"
                                                   placeholder="—"
                                                   class="w-12 text-center rounded-lg border border-gray-200 bg-white py-2 text-sm font-bold text-gray-800 uppercase
                                                          focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/20 outline-none transition
                                                          placeholder:text-gray-300">
                                        </td>
                                    @endfor
                                    <td class="px-3 py-2 text-center bg-amber-50/40">
                                        <span class="inline-block min-w-[2.5rem] rounded-lg bg-amber-50 border border-amber-100 px-2 py-1.5 text-sm font-black text-amber-700"
                                              x-text="endSum({{ $ei }})"></span>
                                    </td>
                                    <td class="px-3 py-2 text-center bg-indigo-50/40">
                                        <span class="inline-block min-w-[2.5rem] rounded-lg bg-indigo-50 border border-indigo-100 px-2 py-1.5 text-sm font-black text-indigo-700"
                                              x-text="endRunning({{ $ei }})"></span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="text-sm font-bold text-gray-600" x-text="endGold({{ $ei }})"></span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="text-sm font-bold text-gray-600" x-text="endX({{ $ei }})"></span>
                                    </td>
                                </tr>
                            @endfor

                            {{-- Sheet subtotal --}}
                            <tr class="border-t-2 border-dashed border-gray-200 bg-gray-50">
                                <td class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider"
                                    colspan="{{ $ape + 1 }}">
                                    @if($totalSets === 1) Grand Total @else Sheet {{ $set }} Total @endif
                                </td>
                                <td class="px-3 py-3 text-center bg-amber-50/60">
                                    <span class="inline-block min-w-[2.5rem] rounded-lg bg-amber-100 border border-amber-200 px-2 py-1.5 text-base font-black text-amber-800"
                                          x-text="setTotal({{ $set }})"></span>
                                </td>
                                <td class="px-3 py-3 text-center bg-indigo-50/60">
                                    {{-- Running cumulative total at end of this set --}}
                                    @php $lastEndOfSet = min($set * $endsPerSet, $rt->num_ends) - 1; @endphp
                                    <span class="inline-block min-w-[2.5rem] rounded-lg bg-indigo-100 border border-indigo-200 px-2 py-1.5 text-base font-black text-indigo-800"
                                          x-text="endRunning({{ $lastEndOfSet }})"></span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="text-base font-black text-gray-700" x-text="setGold({{ $set }})"></span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="text-base font-black text-gray-700" x-text="setX({{ $set }})"></span>
                                </td>
                            </tr>

                        </tbody>
                    @endfor

                </table>
            </div>

            {{-- Save bar --}}
            <div class="flex items-center justify-between gap-4 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-500">
                    <span class="font-semibold text-gray-700">{{ $rt->num_ends }}</span> ends ×
                    <span class="font-semibold text-gray-700">{{ $ape }}</span> arrows =
                    <span class="font-semibold text-gray-700">{{ $rt->num_ends * $ape }}</span> total arrows
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('sessions.index', $session->archer) }}"
                       class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-95 flex items-center gap-2"
                            style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Save Scores
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Delete --}}
    @php
        $canDelete = auth()->user()->isClubAdmin()
            || (auth()->user()->role === 'archer' && auth()->user()->archer?->id === $session->archer_id);
    @endphp
    @if($canDelete)
    <div class="flex justify-end pb-4">
        <form method="POST" action="{{ route('sessions.destroy', $session) }}"
              x-data @submit.prevent="if(confirm('Delete this session and all scores?')) $el.submit()">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700 hover:underline transition-colors">
                Delete this session
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
