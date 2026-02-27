@extends('layouts.app')

@section('title', 'Elimination Match Scorecard')
@section('header', 'Elimination Match')
@section('subheader', $nameA . ' vs ' . $nameB)

@section('header-actions')
    <a href="{{ route('elimination-matches.index') }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 border border-gray-200 hover:bg-gray-50 transition-colors">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        All Matches
    </a>
@endsection

@section('content')
@php
    $catColors  = ['outdoor' => '#059669', 'indoor' => '#4338ca', 'mssm' => '#db2777'];
    $catColor   = $catColors[$match->category] ?? '#64748b';
    $catLabel   = $match->category === 'mssm' ? 'MSSM' : ucfirst($match->category);
    $isCompleted = $match->status === 'completed';
    $isCompound  = $match->format === 'cumulative';
@endphp

{{-- ═══════════════════════════════════════════════════════════
     COMMON MATCH HEADER (static PHP — no Alpine needed)
     ═══════════════════════════════════════════════════════════ --}}
<div class="rounded-2xl bg-white shadow-sm border border-gray-100 mb-5 overflow-hidden">
    <div class="px-5 py-4 flex flex-wrap items-center gap-4"
         style="background: linear-gradient(135deg, #1e293b, #0f172a);">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2 py-0.5 rounded-full text-xs font-bold text-white"
                      style="background: {{ $catColor }};">{{ $catLabel }}</span>
                @if($isCompound)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold text-white"
                          style="background: #d97706;">Compound · 50 m</span>
                @endif
                @if($match->competition_name)
                    <span class="text-amber-300 text-xs font-semibold">{{ $match->competition_name }}</span>
                @endif
            </div>
            <p class="text-white font-black text-lg mt-1">
                {{ $nameA }} <span style="color:#f59e0b;">vs</span> {{ $nameB }}
            </p>
            <p class="text-slate-400 text-xs mt-0.5">
                {{ $match->date->format('d M Y') }}
                @if($match->location) &nbsp;·&nbsp; {{ $match->location }} @endif
            </p>
        </div>
        <div>
            @if($isCompleted)
                <span class="px-3 py-1 rounded-full text-xs font-bold"
                      style="background:#dcfce7; color:#15803d;">Completed</span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-bold"
                      style="background:#fef3c7; color:#92400e;">In Progress</span>
            @endif
        </div>
    </div>

    {{-- Archer labels row --}}
    <div class="grid grid-cols-2 divide-x divide-gray-100 text-center">
        <div class="py-3 px-4" style="background: #eef2ff;">
            <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Archer A</p>
            <p class="text-sm font-black text-indigo-800 mt-0.5">{{ $nameA }}</p>
            <p class="text-xs text-indigo-400">{{ $refA }}</p>
        </div>
        <div class="py-3 px-4" style="background: #ecfdf5;">
            <p class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Archer B</p>
            <p class="text-sm font-black text-emerald-800 mt-0.5">{{ $nameB }}</p>
            <p class="text-xs text-emerald-400">{{ $refB }}</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     COMPOUND CUMULATIVE SCORECARD
     ═══════════════════════════════════════════════════════════ --}}
@if($isCompound)

{{-- Compound rules reminder --}}
<div class="rounded-xl border px-4 py-2.5 mb-5 flex flex-wrap gap-x-5 gap-y-1"
     style="background: #fffbeb; border-color: #fcd34d;">
    <span class="text-xs text-amber-700 font-medium">5 ends · 3 arrows/end · 15 arrows total</span>
    <span class="text-xs text-amber-700 font-medium">Max score: 150 pts</span>
    <span class="text-xs text-amber-700 font-bold">Highest cumulative total wins</span>
    <span class="text-xs text-amber-700 font-medium">Tied → Shoot-off (1 arrow)</span>
    <span class="text-xs text-slate-500">Valid: X · 10–5 · M &nbsp;|&nbsp; Scores 1–4 not valid on this face</span>
</div>

<div x-data="{
    nameA: {{ Js::from($nameA) }},
    nameB: {{ Js::from($nameB) }},
    sets: {{ Js::from($setsInit) }},
    shootOff: {
        a: {{ Js::from($match->shoot_off_a ?? '') }},
        b: {{ Js::from($match->shoot_off_b ?? '') }}
    },
    nearestCenter: {{ Js::from($match->nearest_to_center ?? '') }},
    isCompleted: {{ $isCompleted ? 'true' : 'false' }},

    init() {
        for (let i = 0; i < 5; i++) {
            for (let j = 0; j < 3; j++) {
                if (this.sets[i].a[j] === 'X') this.sets[i].a[j] = '10';
                if (this.sets[i].b[j] === 'X') this.sets[i].b[j] = '10';
            }
        }
        if (this.shootOff.a === 'X') this.shootOff.a = '10';
        if (this.shootOff.b === 'X') this.shootOff.b = '10';
    },

    arrowVal(v) {
        v = String(v).toUpperCase().trim();
        if (v === 'X') return 10;
        if (v === 'M' || v === '') return 0;
        const n = parseInt(v);
        return (!isNaN(n) && n >= 5 && n <= 10) ? n : 0;
    },

    isBlank(v) { return String(v).trim() === ''; },

    endAllEntered(i) {
        return this.sets[i].a.every(v => !this.isBlank(v))
            && this.sets[i].b.every(v => !this.isBlank(v));
    },

    endTotal(archer, i) {
        return this.sets[i][archer].reduce((sum, v) =>
            sum + (this.isBlank(v) ? 0 : this.arrowVal(v)), 0);
    },

    runningTotal(archer, upTo) {
        let sum = 0;
        for (let i = 0; i <= upTo; i++) {
            if (!this.endAllEntered(i)) return '-';
            sum += this.endTotal(archer, i);
        }
        return sum;
    },

    get matchTotals() {
        let a = 0, b = 0;
        for (let i = 0; i < 5; i++) {
            if (!this.endAllEntered(i)) return { a: null, b: null };
            a += this.endTotal('a', i);
            b += this.endTotal('b', i);
        }
        return { a, b };
    },

    get isTied() {
        const t = this.matchTotals;
        return t.a !== null && t.a === t.b;
    },

    get matchWinner() {
        const t = this.matchTotals;
        if (t.a === null) return null;
        if (t.a > t.b) return 'a';
        if (t.b > t.a) return 'b';
        if (!this.isBlank(this.shootOff.a) && !this.isBlank(this.shootOff.b)) {
            const soA = this.arrowVal(this.shootOff.a);
            const soB = this.arrowVal(this.shootOff.b);
            if (soA > soB) return 'a';
            if (soB > soA) return 'b';
            return this.nearestCenter || null;
        }
        return null;
    },

    get matchOver() { return this.matchWinner !== null; },

    get matchResultText() {
        const w = this.matchWinner;
        const t = this.matchTotals;
        if (!w) return '';
        const name = w === 'a' ? this.nameA : this.nameB;
        if (!this.isTied) {
            const score = w === 'a' ? (t.a + '\u2013' + t.b) : (t.b + '\u2013' + t.a);
            return name + ' wins  ' + score;
        }
        const soA = this.arrowVal(this.shootOff.a);
        const soB = this.arrowVal(this.shootOff.b);
        if (soA !== soB) return name + ' wins via Shoot-Off';
        return name + ' wins via Shoot-Off (nearest to center)';
    },

    handleInput(event, archer, setIdx, arrowIdx) {
        let val = event.target.value.toUpperCase().trim();
        const VALID = ['X', '10', '9', '8', '7', '6', '5', 'M'];
        if (VALID.includes(val)) {
            if (val === 'X') val = '10';
            this.sets[setIdx][archer][arrowIdx] = val;
            event.target.value = val;
        } else if (val === '1') {
            // Partial input — wait for the second digit to complete '10'
        } else {
            this.sets[setIdx][archer][arrowIdx] = '';
            event.target.value = '';
        }
    },

    handleBlur(event, archer, setIdx, arrowIdx) {
        const val = event.target.value.toUpperCase().trim();
        const VALID = ['X', '10', '9', '8', '7', '6', '5', 'M'];
        if (!VALID.includes(val)) {
            this.sets[setIdx][archer][arrowIdx] = '';
            event.target.value = '';
        }
    },

    handleSOInput(event, archer) {
        let val = event.target.value.toUpperCase().trim();
        const VALID = ['X', '10', '9', '8', '7', '6', '5', 'M'];
        if (VALID.includes(val)) {
            if (val === 'X') val = '10';
            this.shootOff[archer] = val;
            event.target.value = val;
        } else if (val === '1') {
            // Partial input for '10'
        } else {
            this.shootOff[archer] = '';
            event.target.value = '';
        }
    },

    handleSOBlur(event, archer) {
        const val = event.target.value.toUpperCase().trim();
        const VALID = ['X', '10', '9', '8', '7', '6', '5', 'M'];
        if (!VALID.includes(val)) {
            this.shootOff[archer] = '';
            event.target.value = '';
        }
    }
}">

    {{-- Match Result Banner --}}
    <div x-show="matchOver" x-cloak
         class="rounded-2xl p-4 mb-5 border-2 text-center"
         style="background:#f0fdf4; border-color:#22c55e;">
        <svg class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
             style="color:#22c55e;" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-black text-lg" style="color:#15803d;" x-text="matchResultText"></p>
    </div>

    {{-- Scorecard Form --}}
    <form id="matchForm" method="POST" action="{{ route('elimination-matches.saveScores', $match) }}">
        @csrf
        @method('PUT')

        {{-- Compound Scoring Table --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 mb-5 overflow-hidden">
            <div class="px-5 py-3" style="background: linear-gradient(135deg, #d97706, #b45309);">
                <h2 class="text-white font-bold text-sm section-header">End Scores &mdash; Cumulative</h2>
                <p class="text-amber-100 text-xs mt-0.5">Max 30 pts per end · Max 150 pts total</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th rowspan="2" class="px-3 py-2 text-left text-xs font-bold uppercase tracking-widest text-slate-400 border-r border-gray-200"
                                style="min-width:48px;">End</th>
                            <th colspan="4" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-widest border-r border-gray-200"
                                style="color:#4338ca; background:#eef2ff;">{{ $nameA }} (A)</th>
                            <th colspan="4" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-widest border-r border-gray-200"
                                style="color:#059669; background:#ecfdf5;">{{ $nameB }} (B)</th>
                            <th colspan="2" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-widest text-amber-500"
                                style="background:#fffbeb;">Running Total</th>
                        </tr>
                        <tr style="background:#f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#eef2ff;">Arr 1</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#eef2ff;">Arr 2</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#eef2ff;">Arr 3</th>
                            <th class="px-2 py-1.5 text-center text-xs font-bold text-indigo-600 border-r border-gray-200" style="background:#e0e7ff;">Total</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#ecfdf5;">Arr 1</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#ecfdf5;">Arr 2</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#ecfdf5;">Arr 3</th>
                            <th class="px-2 py-1.5 text-center text-xs font-bold text-emerald-600 border-r border-gray-200" style="background:#d1fae5;">Total</th>
                            <th class="px-2 py-1.5 text-center text-xs font-bold text-amber-600" style="background:#fffbeb;">A</th>
                            <th class="px-2 py-1.5 text-center text-xs font-bold text-amber-600" style="background:#fffbeb;">B</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @for($i = 0; $i < 5; $i++)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- End number --}}
                            <td class="px-3 py-2 text-center border-r border-gray-200">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-black text-white"
                                      style="background: linear-gradient(135deg, #d97706, #f59e0b);">{{ $i + 1 }}</span>
                            </td>

                            {{-- Archer A arrows --}}
                            @for($j = 0; $j < 3; $j++)
                            <td class="px-2 py-2 text-center" style="background:#f5f7ff;">
                                <input type="text"
                                       name="arrows[a][{{ $i }}][{{ $j }}]"
                                       x-model="sets[{{ $i }}].a[{{ $j }}]"
                                       @input="handleInput($event, 'a', {{ $i }}, {{ $j }})"
                                       @blur="handleBlur($event, 'a', {{ $i }}, {{ $j }})"
                                       @keydown.enter.prevent=""
                                       maxlength="2"
                                       placeholder="—"
                                       class="w-10 h-8 text-center text-sm font-bold rounded-lg border uppercase"
                                       style="border-color:#c7d2fe; background:#eef2ff; color:#4338ca;"
                                       {{ $isCompleted ? 'readonly' : '' }}>
                            </td>
                            @endfor

                            {{-- Archer A end total --}}
                            <td class="px-3 py-2 text-center font-black text-indigo-700 border-r border-gray-200"
                                style="background:#e0e7ff; min-width:44px;">
                                <span x-text="endTotal('a', {{ $i }})">0</span>
                            </td>

                            {{-- Archer B arrows --}}
                            @for($j = 0; $j < 3; $j++)
                            <td class="px-2 py-2 text-center" style="background:#f0fdf4;">
                                <input type="text"
                                       name="arrows[b][{{ $i }}][{{ $j }}]"
                                       x-model="sets[{{ $i }}].b[{{ $j }}]"
                                       @input="handleInput($event, 'b', {{ $i }}, {{ $j }})"
                                       @blur="handleBlur($event, 'b', {{ $i }}, {{ $j }})"
                                       @keydown.enter.prevent=""
                                       maxlength="2"
                                       placeholder="—"
                                       class="w-10 h-8 text-center text-sm font-bold rounded-lg border uppercase"
                                       style="border-color:#a7f3d0; background:#ecfdf5; color:#059669;"
                                       {{ $isCompleted ? 'readonly' : '' }}>
                            </td>
                            @endfor

                            {{-- Archer B end total --}}
                            <td class="px-3 py-2 text-center font-black text-emerald-700 border-r border-gray-200"
                                style="background:#d1fae5; min-width:44px;">
                                <span x-text="endTotal('b', {{ $i }})">0</span>
                            </td>

                            {{-- Running totals --}}
                            <td class="px-3 py-2 text-center font-black" style="background:#fffbeb; min-width:36px;">
                                <span x-text="runningTotal('a', {{ $i }})" style="color:#92400e;"></span>
                            </td>
                            <td class="px-3 py-2 text-center font-black" style="background:#fffbeb; min-width:36px;">
                                <span x-text="runningTotal('b', {{ $i }})" style="color:#92400e;"></span>
                            </td>
                        </tr>
                        @endfor

                        {{-- Match Total row --}}
                        <tr style="background:#f8fafc; border-top: 2px solid #e2e8f0;">
                            <td class="px-3 py-2.5 text-xs font-black uppercase tracking-widest text-slate-500 border-r border-gray-200">
                                Total
                            </td>
                            <td colspan="3" style="background:#e0e7ff;"></td>
                            <td class="px-3 py-2.5 text-center font-black text-lg border-r border-gray-200"
                                style="background:#e0e7ff; color:#4338ca;">
                                <span x-text="matchTotals.a !== null ? matchTotals.a : '—'"></span>
                            </td>
                            <td colspan="3" style="background:#d1fae5;"></td>
                            <td class="px-3 py-2.5 text-center font-black text-lg border-r border-gray-200"
                                style="background:#d1fae5; color:#059669;">
                                <span x-text="matchTotals.b !== null ? matchTotals.b : '—'"></span>
                            </td>
                            <td colspan="2" style="background:#fffbeb;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Shoot-Off Section (shown when tied after 5 ends) --}}
        <div x-show="isTied" x-cloak
             class="rounded-2xl bg-white shadow-sm border-2 mb-5 overflow-hidden"
             style="border-color:#f59e0b;">
            <div class="px-5 py-3" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <h2 class="text-white font-bold text-sm section-header">Shoot-Off (Tied after 5 ends)</h2>
                <p class="text-amber-100 text-xs mt-0.5">One arrow each — highest score wins. Equal score: nearest to center wins.</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-6">
                    <div class="text-center">
                        <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#4338ca;">
                            {{ $nameA }} (A)
                        </p>
                        <input type="text"
                               name="shoot_off_a"
                               x-model="shootOff.a"
                               @input="handleSOInput($event, 'a')"
                               @blur="handleSOBlur($event, 'a')"
                               maxlength="2"
                               placeholder="X / 10–5 / M"
                               class="w-24 h-12 text-center text-xl font-black rounded-xl border-2 uppercase mx-auto block"
                               style="border-color:#c7d2fe; background:#eef2ff; color:#4338ca;"
                               {{ $isCompleted ? 'readonly' : '' }}>
                        <p class="text-xs text-indigo-400 mt-1.5 font-bold"
                           x-text="!isBlank(shootOff.a) ? ('Value: ' + arrowVal(shootOff.a)) : '—'"></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#059669;">
                            {{ $nameB }} (B)
                        </p>
                        <input type="text"
                               name="shoot_off_b"
                               x-model="shootOff.b"
                               @input="handleSOInput($event, 'b')"
                               @blur="handleSOBlur($event, 'b')"
                               maxlength="2"
                               placeholder="X / 10–5 / M"
                               class="w-24 h-12 text-center text-xl font-black rounded-xl border-2 uppercase mx-auto block"
                               style="border-color:#a7f3d0; background:#ecfdf5; color:#059669;"
                               {{ $isCompleted ? 'readonly' : '' }}>
                        <p class="text-xs text-emerald-400 mt-1.5 font-bold"
                           x-text="!isBlank(shootOff.b) ? ('Value: ' + arrowVal(shootOff.b)) : '—'"></p>
                    </div>
                </div>

                {{-- Shoot-off result --}}
                <div x-show="!isBlank(shootOff.a) && !isBlank(shootOff.b)" class="mt-4 text-center">
                    <p class="text-sm font-bold"
                       x-show="arrowVal(shootOff.a) > arrowVal(shootOff.b)"
                       style="color:#4338ca;">{{ $nameA }} wins the shoot-off!</p>
                    <p class="text-sm font-bold"
                       x-show="arrowVal(shootOff.b) > arrowVal(shootOff.a)"
                       style="color:#059669;">{{ $nameB }} wins the shoot-off!</p>

                    {{-- Equal score: nearest-to-center manual selection --}}
                    <div x-show="arrowVal(shootOff.a) === arrowVal(shootOff.b)">
                        <p class="text-sm font-bold mb-3" style="color:#f59e0b;">
                            Equal score — tick the archer nearest to center:
                        </p>
                        <div class="flex gap-3 justify-center flex-wrap">
                            {{-- Archer A button --}}
                            <button type="button"
                                    @if(!$isCompleted)
                                    @click="nearestCenter = nearestCenter === 'a' ? '' : 'a'"
                                    @endif
                                    class="px-4 py-2.5 rounded-xl font-bold text-sm border-2 transition-all flex items-center gap-2"
                                    :class="isCompleted && nearestCenter !== 'a' ? 'opacity-40 cursor-default' : ''"
                                    :style="nearestCenter === 'a'
                                        ? 'background:#eef2ff; border-color:#4338ca; color:#4338ca;'
                                        : 'background:#f8fafc; border-color:#cbd5e1; color:#64748b;'">
                                <span x-show="nearestCenter === 'a'"
                                      class="inline-flex h-5 w-5 items-center justify-center rounded-full text-white text-xs font-black"
                                      style="background:#4338ca;">✓</span>
                                <span x-show="nearestCenter !== 'a'"
                                      class="inline-flex h-5 w-5 items-center justify-center rounded-full border-2"
                                      style="border-color:#cbd5e1;"></span>
                                {{ $nameA }} (A) — Nearest
                            </button>

                            {{-- Archer B button --}}
                            <button type="button"
                                    @if(!$isCompleted)
                                    @click="nearestCenter = nearestCenter === 'b' ? '' : 'b'"
                                    @endif
                                    class="px-4 py-2.5 rounded-xl font-bold text-sm border-2 transition-all flex items-center gap-2"
                                    :class="isCompleted && nearestCenter !== 'b' ? 'opacity-40 cursor-default' : ''"
                                    :style="nearestCenter === 'b'
                                        ? 'background:#ecfdf5; border-color:#059669; color:#059669;'
                                        : 'background:#f8fafc; border-color:#cbd5e1; color:#64748b;'">
                                <span x-show="nearestCenter === 'b'"
                                      class="inline-flex h-5 w-5 items-center justify-center rounded-full text-white text-xs font-black"
                                      style="background:#059669;">✓</span>
                                <span x-show="nearestCenter !== 'b'"
                                      class="inline-flex h-5 w-5 items-center justify-center rounded-full border-2"
                                      style="border-color:#cbd5e1;"></span>
                                {{ $nameB }} (B) — Nearest
                            </button>
                        </div>

                        <p x-show="nearestCenter"
                           class="text-xs font-semibold mt-2" style="color:#059669;"
                           x-text="nearestCenter === 'a'
                               ? '{{ $nameA }} declared shoot-off winner (nearest to center)'
                               : '{{ $nameB }} declared shoot-off winner (nearest to center)'"></p>
                        <p x-show="!nearestCenter"
                           class="text-xs mt-2" style="color:#92400e;">
                            Tick which archer's arrow was physically closer to the center of the target.
                        </p>

                        <input type="hidden" name="nearest_to_center" :value="nearestCenter">
                    </div>
                </div>
            </div>
        </div>

        {{-- Save / Delete --}}
        @if(!$isCompleted)
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-3 rounded-xl text-sm font-bold text-white shadow-sm"
                        style="background: linear-gradient(135deg, #d97706, #f59e0b);">
                    Save Match
                </button>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-sm font-semibold text-slate-500">This match is completed and locked.</p>
                @if(in_array(auth()->user()->role, ['super_admin', 'club_admin']))
                    <form method="POST" action="{{ route('elimination-matches.destroy', $match) }}"
                          onsubmit="return confirm('Delete this match record?')" class="mt-2 inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700">
                            Delete Match
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </form>

</div>{{-- end compound x-data --}}

{{-- ═══════════════════════════════════════════════════════════
     SET-POINT (RECURVE) SCORECARD
     ═══════════════════════════════════════════════════════════ --}}
@else

{{-- Set System Rules reminder --}}
<div class="rounded-xl border px-4 py-2.5 mb-5 flex flex-wrap gap-x-5 gap-y-1"
     style="background: #eff6ff; border-color: #bfdbfe;">
    <span class="text-xs text-indigo-600 font-medium">Max 5 sets · 3 arrows/set</span>
    <span class="text-xs text-indigo-600 font-medium">Set winner: 2 pts · Tie: 1 pt each</span>
    <span class="text-xs text-indigo-600 font-bold">First to 6 pts wins</span>
    <span class="text-xs text-indigo-600 font-medium">5–5 → Shoot-off</span>
    <span class="text-xs text-slate-400">Valid: X · 10–1 · M (miss)</span>
</div>

<div x-data="{
    nameA: {{ Js::from($nameA) }},
    nameB: {{ Js::from($nameB) }},
    sets: {{ Js::from($setsInit) }},
    shootOff: {
        a: {{ Js::from($match->shoot_off_a ?? '') }},
        b: {{ Js::from($match->shoot_off_b ?? '') }}
    },
    nearestCenter: {{ Js::from($match->nearest_to_center ?? '') }},
    isCompleted: {{ $isCompleted ? 'true' : 'false' }},

    arrowVal(v) {
        v = String(v).toUpperCase().trim();
        if (v === 'X') return 10;
        if (v === 'M' || v === '0' || v === '') return 0;
        const n = parseInt(v);
        return (!isNaN(n) && n >= 1 && n <= 10) ? n : 0;
    },

    isBlank(v) {
        return String(v).trim() === '';
    },

    setAllEntered(i) {
        return this.sets[i].a.every(v => !this.isBlank(v))
            && this.sets[i].b.every(v => !this.isBlank(v));
    },

    setTotal(archer, i) {
        return this.sets[i][archer].reduce((sum, v) => sum + (this.isBlank(v) ? 0 : this.arrowVal(v)), 0);
    },

    setWinner(i) {
        if (!this.setAllEntered(i)) return null;
        const a = this.setTotal('a', i);
        const b = this.setTotal('b', i);
        if (a > b) return 'a';
        if (b > a) return 'b';
        return 'tie';
    },

    setPts(i) {
        const w = this.setWinner(i);
        if (w === null) return { a: 0, b: 0 };
        if (w === 'a') return { a: 2, b: 0 };
        if (w === 'b') return { a: 0, b: 2 };
        return { a: 1, b: 1 };
    },

    runningPts(archer, upTo) {
        let total = 0;
        for (let i = 0; i <= upTo; i++) {
            const w = this.setWinner(i);
            if (w === null) return '-';
            if (w === archer) total += 2;
            else if (w === 'tie') total += 1;
        }
        return total;
    },

    get matchWinnerSetIdx() {
        let ptsA = 0, ptsB = 0;
        for (let i = 0; i < 5; i++) {
            const w = this.setWinner(i);
            if (w === null) return -1;
            if (w === 'a') ptsA += 2;
            else if (w === 'b') ptsB += 2;
            else { ptsA += 1; ptsB += 1; }
            if (ptsA >= 6 || ptsB >= 6) return i;
        }
        return -1;
    },

    get is55() {
        let ptsA = 0, ptsB = 0;
        for (let i = 0; i < 5; i++) {
            const w = this.setWinner(i);
            if (w === null) return false;
            if (w === 'a') ptsA += 2;
            else if (w === 'b') ptsB += 2;
            else { ptsA += 1; ptsB += 1; }
        }
        return ptsA === 5 && ptsB === 5;
    },

    get matchWinner() {
        const idx = this.matchWinnerSetIdx;
        if (idx !== -1) {
            let ptsA = 0, ptsB = 0;
            for (let i = 0; i <= idx; i++) {
                const w = this.setWinner(i);
                if (w === 'a') ptsA += 2;
                else if (w === 'b') ptsB += 2;
                else { ptsA += 1; ptsB += 1; }
            }
            return ptsA >= 6 ? 'a' : 'b';
        }
        if (this.is55 && !this.isBlank(this.shootOff.a) && !this.isBlank(this.shootOff.b)) {
            const soA = this.arrowVal(this.shootOff.a);
            const soB = this.arrowVal(this.shootOff.b);
            if (soA > soB) return 'a';
            if (soB > soA) return 'b';
            return this.nearestCenter || null;
        }
        return null;
    },

    get matchOver() {
        return this.matchWinner !== null;
    },

    get finalPts() {
        let ptsA = 0, ptsB = 0;
        const limit = this.matchWinnerSetIdx !== -1 ? this.matchWinnerSetIdx : 4;
        for (let i = 0; i <= limit; i++) {
            const w = this.setWinner(i);
            if (w === null) break;
            if (w === 'a') ptsA += 2;
            else if (w === 'b') ptsB += 2;
            else { ptsA += 1; ptsB += 1; }
        }
        return { a: ptsA, b: ptsB };
    },

    get matchResultText() {
        const w = this.matchWinner;
        const s = this.finalPts;
        if (w === 'a') {
            if (this.is55) return this.nameA + ' wins via Shoot-Off (nearest to center)';
            return this.nameA + ' wins  ' + s.a + '\u2013' + s.b;
        }
        if (w === 'b') {
            if (this.is55) return this.nameB + ' wins via Shoot-Off (nearest to center)';
            return this.nameB + ' wins  ' + s.b + '\u2013' + s.a;
        }
        return '';
    },

    handleInput(event, archer, setIdx, arrowIdx) {
        let val = event.target.value.toUpperCase().trim();
        if (val === 'X' || val === 'M') {
            // valid as-is
        } else {
            const n = parseInt(val);
            if (!isNaN(n) && n >= 1 && n <= 10) {
                val = String(n);
            } else {
                val = '';
            }
        }
        this.sets[setIdx][archer][arrowIdx] = val;
        event.target.value = val;
    },

    handleSOInput(event, archer) {
        let val = event.target.value.toUpperCase().trim();
        if (val === 'X' || val === 'M') {
            // valid
        } else {
            const n = parseInt(val);
            if (!isNaN(n) && n >= 1 && n <= 10) {
                val = String(n);
            } else {
                val = '';
            }
        }
        this.shootOff[archer] = val;
        event.target.value = val;
    }
}">

    {{-- Match Result Banner --}}
    <div x-show="matchOver" x-cloak
         class="rounded-2xl p-4 mb-5 border-2 text-center"
         style="background:#f0fdf4; border-color:#22c55e;">
        <svg class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
             style="color:#22c55e;" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-black text-lg" style="color:#15803d;" x-text="matchResultText"></p>
    </div>

    {{-- Scorecard Form --}}
    <form id="matchForm" method="POST" action="{{ route('elimination-matches.saveScores', $match) }}">
        @csrf
        @method('PUT')

        {{-- Scoring table --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 mb-5 overflow-hidden">
            <div class="px-5 py-3" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                <h2 class="text-white font-bold text-sm section-header">Set Scores</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th rowspan="2" class="px-3 py-2 text-left text-xs font-bold uppercase tracking-widest text-slate-400 border-r border-gray-200"
                                style="min-width:48px;">Set</th>
                            <th colspan="4" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-widest border-r border-gray-200"
                                style="color:#4338ca; background:#eef2ff;">{{ $nameA }} (A)</th>
                            <th colspan="4" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-widest border-r border-gray-200"
                                style="color:#059669; background:#ecfdf5;">{{ $nameB }} (B)</th>
                            <th rowspan="2" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-widest text-slate-400 border-r border-gray-200"
                                style="min-width:56px;">Winner</th>
                            <th rowspan="2" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-widest text-slate-400 border-r border-gray-200"
                                style="min-width:56px;">Pts</th>
                            <th colspan="2" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-widest text-amber-500"
                                style="background:#fffbeb;">Running</th>
                        </tr>
                        <tr style="background:#f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#eef2ff;">Arr 1</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#eef2ff;">Arr 2</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#eef2ff;">Arr 3</th>
                            <th class="px-2 py-1.5 text-center text-xs font-bold text-indigo-600 border-r border-gray-200" style="background:#e0e7ff;">Total</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#ecfdf5;">Arr 1</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#ecfdf5;">Arr 2</th>
                            <th class="px-2 py-1.5 text-center text-xs font-semibold text-slate-400" style="background:#ecfdf5;">Arr 3</th>
                            <th class="px-2 py-1.5 text-center text-xs font-bold text-emerald-600 border-r border-gray-200" style="background:#d1fae5;">Total</th>
                            <th class="px-2 py-1.5 text-center text-xs font-bold text-amber-600" style="background:#fffbeb;">A</th>
                            <th class="px-2 py-1.5 text-center text-xs font-bold text-amber-600" style="background:#fffbeb;">B</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @for($i = 0; $i < 5; $i++)
                        <tr :class="{ 'opacity-30 pointer-events-none': matchWinnerSetIdx !== -1 && {{ $i }} > matchWinnerSetIdx }"
                            class="hover:bg-gray-50 transition-colors">
                            {{-- Set number --}}
                            <td class="px-3 py-2 text-center border-r border-gray-200">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-black text-white"
                                      style="background: linear-gradient(135deg, #4338ca, #6366f1);">{{ $i + 1 }}</span>
                            </td>

                            {{-- Archer A arrows --}}
                            @for($j = 0; $j < 3; $j++)
                            <td class="px-2 py-2 text-center" style="background:#f5f7ff;">
                                <input type="text"
                                       name="arrows[a][{{ $i }}][{{ $j }}]"
                                       x-model="sets[{{ $i }}].a[{{ $j }}]"
                                       @input="handleInput($event, 'a', {{ $i }}, {{ $j }})"
                                       @keydown.enter.prevent=""
                                       maxlength="2"
                                       placeholder="—"
                                       class="w-10 h-8 text-center text-sm font-bold rounded-lg border uppercase"
                                       style="border-color:#c7d2fe; background:#eef2ff; color:#4338ca;"
                                       {{ $isCompleted ? 'readonly' : '' }}>
                            </td>
                            @endfor

                            {{-- Archer A total --}}
                            <td class="px-3 py-2 text-center font-black text-indigo-700 border-r border-gray-200"
                                style="background:#e0e7ff; min-width:44px;">
                                <span x-text="setTotal('a', {{ $i }})">0</span>
                            </td>

                            {{-- Archer B arrows --}}
                            @for($j = 0; $j < 3; $j++)
                            <td class="px-2 py-2 text-center" style="background:#f0fdf4;">
                                <input type="text"
                                       name="arrows[b][{{ $i }}][{{ $j }}]"
                                       x-model="sets[{{ $i }}].b[{{ $j }}]"
                                       @input="handleInput($event, 'b', {{ $i }}, {{ $j }})"
                                       @keydown.enter.prevent=""
                                       maxlength="2"
                                       placeholder="—"
                                       class="w-10 h-8 text-center text-sm font-bold rounded-lg border uppercase"
                                       style="border-color:#a7f3d0; background:#ecfdf5; color:#059669;"
                                       {{ $isCompleted ? 'readonly' : '' }}>
                            </td>
                            @endfor

                            {{-- Archer B total --}}
                            <td class="px-3 py-2 text-center font-black text-emerald-700 border-r border-gray-200"
                                style="background:#d1fae5; min-width:44px;">
                                <span x-text="setTotal('b', {{ $i }})">0</span>
                            </td>

                            {{-- Set winner --}}
                            <td class="px-3 py-2 text-center font-bold border-r border-gray-200" style="min-width:56px;">
                                <span x-show="setWinner({{ $i }}) === 'a'" style="color:#4338ca;">A</span>
                                <span x-show="setWinner({{ $i }}) === 'b'" style="color:#059669;">B</span>
                                <span x-show="setWinner({{ $i }}) === 'tie'" style="color:#f59e0b;">Tie</span>
                                <span x-show="setWinner({{ $i }}) === null" class="text-slate-300">—</span>
                            </td>

                            {{-- Set points --}}
                            <td class="px-3 py-2 text-center border-r border-gray-200" style="min-width:56px;">
                                <span x-show="setWinner({{ $i }}) === 'a'" class="text-xs font-bold px-1.5 py-0.5 rounded"
                                      style="background:#eef2ff; color:#4338ca;">2–0</span>
                                <span x-show="setWinner({{ $i }}) === 'b'" class="text-xs font-bold px-1.5 py-0.5 rounded"
                                      style="background:#ecfdf5; color:#059669;">0–2</span>
                                <span x-show="setWinner({{ $i }}) === 'tie'" class="text-xs font-bold px-1.5 py-0.5 rounded"
                                      style="background:#fffbeb; color:#92400e;">1–1</span>
                                <span x-show="setWinner({{ $i }}) === null" class="text-slate-300 text-xs">—</span>
                            </td>

                            {{-- Running pts A --}}
                            <td class="px-3 py-2 text-center font-black" style="background:#fffbeb; min-width:36px;">
                                <span x-text="runningPts('a', {{ $i }})" style="color:#92400e;"></span>
                            </td>

                            {{-- Running pts B --}}
                            <td class="px-3 py-2 text-center font-black" style="background:#fffbeb; min-width:36px;">
                                <span x-text="runningPts('b', {{ $i }})" style="color:#92400e;"></span>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Shoot-Off Section (shown when 5-5) --}}
        <div x-show="is55" x-cloak
             class="rounded-2xl bg-white shadow-sm border-2 mb-5 overflow-hidden"
             style="border-color:#f59e0b;">
            <div class="px-5 py-3" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <h2 class="text-white font-bold text-sm section-header">Shoot-Off (5–5)</h2>
                <p class="text-amber-100 text-xs mt-0.5">One arrow each — highest score wins. Equal score: nearest to center wins.</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-6">
                    <div class="text-center">
                        <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#4338ca;">
                            {{ $nameA }} (A)
                        </p>
                        <input type="text"
                               name="shoot_off_a"
                               x-model="shootOff.a"
                               @input="handleSOInput($event, 'a')"
                               maxlength="2"
                               placeholder="X / 10–1 / M"
                               class="w-24 h-12 text-center text-xl font-black rounded-xl border-2 uppercase mx-auto block"
                               style="border-color:#c7d2fe; background:#eef2ff; color:#4338ca;"
                               {{ $isCompleted ? 'readonly' : '' }}>
                        <p class="text-xs text-indigo-400 mt-1.5 font-bold"
                           x-text="!isBlank(shootOff.a) ? ('Value: ' + arrowVal(shootOff.a)) : '—'"></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#059669;">
                            {{ $nameB }} (B)
                        </p>
                        <input type="text"
                               name="shoot_off_b"
                               x-model="shootOff.b"
                               @input="handleSOInput($event, 'b')"
                               maxlength="2"
                               placeholder="X / 10–1 / M"
                               class="w-24 h-12 text-center text-xl font-black rounded-xl border-2 uppercase mx-auto block"
                               style="border-color:#a7f3d0; background:#ecfdf5; color:#059669;"
                               {{ $isCompleted ? 'readonly' : '' }}>
                        <p class="text-xs text-emerald-400 mt-1.5 font-bold"
                           x-text="!isBlank(shootOff.b) ? ('Value: ' + arrowVal(shootOff.b)) : '—'"></p>
                    </div>
                </div>

                {{-- Shoot-off result --}}
                <div x-show="!isBlank(shootOff.a) && !isBlank(shootOff.b)" class="mt-4 text-center">
                    <p class="text-sm font-bold"
                       x-show="arrowVal(shootOff.a) > arrowVal(shootOff.b)"
                       style="color:#4338ca;">{{ $nameA }} wins the shoot-off!</p>
                    <p class="text-sm font-bold"
                       x-show="arrowVal(shootOff.b) > arrowVal(shootOff.a)"
                       style="color:#059669;">{{ $nameB }} wins the shoot-off!</p>

                    {{-- Equal score: nearest-to-center manual selection --}}
                    <div x-show="arrowVal(shootOff.a) === arrowVal(shootOff.b)">
                        <p class="text-sm font-bold mb-3" style="color:#f59e0b;">
                            Equal score — tick the archer nearest to center:
                        </p>
                        <div class="flex gap-3 justify-center flex-wrap">
                            {{-- Archer A button --}}
                            <button type="button"
                                    @if(!$isCompleted)
                                    @click="nearestCenter = nearestCenter === 'a' ? '' : 'a'"
                                    @endif
                                    class="px-4 py-2.5 rounded-xl font-bold text-sm border-2 transition-all flex items-center gap-2"
                                    :class="isCompleted && nearestCenter !== 'a' ? 'opacity-40 cursor-default' : ''"
                                    :style="nearestCenter === 'a'
                                        ? 'background:#eef2ff; border-color:#4338ca; color:#4338ca;'
                                        : 'background:#f8fafc; border-color:#cbd5e1; color:#64748b;'">
                                <span x-show="nearestCenter === 'a'"
                                      class="inline-flex h-5 w-5 items-center justify-center rounded-full text-white text-xs font-black"
                                      style="background:#4338ca;">✓</span>
                                <span x-show="nearestCenter !== 'a'"
                                      class="inline-flex h-5 w-5 items-center justify-center rounded-full border-2"
                                      style="border-color:#cbd5e1;"></span>
                                {{ $nameA }} (A) — Nearest
                            </button>

                            {{-- Archer B button --}}
                            <button type="button"
                                    @if(!$isCompleted)
                                    @click="nearestCenter = nearestCenter === 'b' ? '' : 'b'"
                                    @endif
                                    class="px-4 py-2.5 rounded-xl font-bold text-sm border-2 transition-all flex items-center gap-2"
                                    :class="isCompleted && nearestCenter !== 'b' ? 'opacity-40 cursor-default' : ''"
                                    :style="nearestCenter === 'b'
                                        ? 'background:#ecfdf5; border-color:#059669; color:#059669;'
                                        : 'background:#f8fafc; border-color:#cbd5e1; color:#64748b;'">
                                <span x-show="nearestCenter === 'b'"
                                      class="inline-flex h-5 w-5 items-center justify-center rounded-full text-white text-xs font-black"
                                      style="background:#059669;">✓</span>
                                <span x-show="nearestCenter !== 'b'"
                                      class="inline-flex h-5 w-5 items-center justify-center rounded-full border-2"
                                      style="border-color:#cbd5e1;"></span>
                                {{ $nameB }} (B) — Nearest
                            </button>
                        </div>

                        {{-- Confirmation text --}}
                        <p x-show="nearestCenter"
                           class="text-xs font-semibold mt-2" style="color:#059669;"
                           x-text="nearestCenter === 'a'
                               ? '{{ $nameA }} declared shoot-off winner (nearest to center)'
                               : '{{ $nameB }} declared shoot-off winner (nearest to center)'"></p>
                        <p x-show="!nearestCenter"
                           class="text-xs mt-2" style="color:#92400e;">
                            Tick which archer's arrow was physically closer to the center of the target.
                        </p>

                        {{-- Hidden field submitted with the form --}}
                        <input type="hidden" name="nearest_to_center" :value="nearestCenter">
                    </div>
                </div>
            </div>
        </div>

        {{-- Save button --}}
        @if(!$isCompleted)
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-3 rounded-xl text-sm font-bold text-white shadow-sm"
                        style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                    Save Match
                </button>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-sm font-semibold text-slate-500">This match is completed and locked.</p>
                @if(in_array(auth()->user()->role, ['super_admin', 'club_admin']))
                    <form method="POST" action="{{ route('elimination-matches.destroy', $match) }}"
                          onsubmit="return confirm('Delete this match record?')" class="mt-2 inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700">
                            Delete Match
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </form>

</div>{{-- end set-point x-data --}}

@endif
@endsection
