@extends('layouts.app')

@section('title', 'Score Review — ' . $session->archer->full_name)
@section('header', 'Score Review')
@section('subheader', $session->archer->full_name . ' · ' . $session->roundType->name . ' — ' . $session->date->format('d M Y'))

@section('header-actions')
    <a href="{{ route('coaches.club-results', $coach) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Results
    </a>
@endsection

@section('content')
@php
    $rt    = $session->roundType;
    $score = $session->score;
    $ends  = $score->ends->keyBy('end_number');
    $ape   = $rt->arrows_per_end;
    $endsPerSet = 6;
    $totalSets  = (int) ceil($rt->num_ends / $endsPerSet);
@endphp

<div class="max-w-5xl mx-auto space-y-5">

    {{-- Header card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 flex flex-wrap gap-4 items-center justify-between border-b border-gray-100"
             style="background: linear-gradient(135deg, #f0fdfa, #ccfbf1);">
            <div class="flex items-center gap-4">
                <img src="{{ $session->archer->photo_url }}" alt="{{ $session->archer->full_name }}"
                     class="h-12 w-12 rounded-2xl object-cover border-2 border-white shadow">
                <div>
                    <p class="text-base font-bold text-gray-900">{{ $session->archer->full_name }}</p>
                    <p class="text-xs text-gray-500 font-mono">{{ $session->archer->ref_no }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 text-xs">
                <span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 font-semibold text-gray-700">
                    {{ $session->roundType->name }}
                </span>
                <span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 font-semibold text-gray-700">
                    {{ $session->date->format('d M Y') }}
                </span>
                @if($session->location)
                <span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600">
                    {{ $session->location }}
                </span>
                @endif
                @if($session->is_competition)
                <span class="px-3 py-1.5 rounded-lg font-semibold text-amber-700 bg-amber-100 border border-amber-200">
                    Competition{{ $session->competition_name ? ': ' . $session->competition_name : '' }}
                </span>
                @endif
            </div>
        </div>

        {{-- Summary stats --}}
        <div class="grid grid-cols-4 divide-x divide-gray-100">
            @foreach([
                ['Total Score', $score?->total_score ?? 0, 'text-indigo-700'],
                ['10+X', $score?->gold_count ?? 0, 'text-amber-600'],
                ['X', $score?->x_count ?? 0, 'text-emerald-600'],
                ['Misses', $score?->miss_count ?? 0, 'text-red-500'],
            ] as [$label, $val, $color])
            <div class="px-4 py-4 text-center">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ $label }}</p>
                <p class="text-2xl font-black {{ $color }} mt-0.5">{{ $val }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Scorecard (read-only) --}}
    @for($set = 1; $set <= $totalSets; $set++)
    @php
        $setStart = ($set - 1) * $endsPerSet + 1;
        $setEnd   = min($set * $endsPerSet, $rt->num_ends);
        $setTotal = 0;
        $setGold  = 0;
        $setX     = 0;
        $runningTotal = 0;
        for ($e = 1; $e < $setStart; $e++) {
            $runningTotal += ($ends->get($e)?->end_total ?? 0);
        }
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($totalSets > 1)
        <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between" style="background: #f0fdfa;">
            <span class="text-xs font-bold text-teal-700 uppercase tracking-wider">Score Sheet {{ $set }}</span>
            <span class="text-xs text-gray-400">Ends {{ $setStart }}–{{ $setEnd }}</span>
        </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-2.5 text-center text-xs font-bold text-gray-500 uppercase w-12">End</th>
                        @for($a = 1; $a <= $ape; $a++)
                        <th class="px-3 py-2.5 text-center text-xs font-bold text-gray-500 uppercase w-14">Arrow {{ $a }}</th>
                        @endfor
                        <th class="px-3 py-2.5 text-center text-xs font-bold text-gray-700 uppercase w-14">Sum</th>
                        <th class="px-3 py-2.5 text-center text-xs font-bold text-gray-700 uppercase w-14">Tot.</th>
                        <th class="px-3 py-2.5 text-center text-xs font-bold text-amber-600 uppercase w-14">10+X</th>
                        <th class="px-3 py-2.5 text-center text-xs font-bold text-emerald-600 uppercase w-10">X</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @for($e = $setStart; $e <= $setEnd; $e++)
                    @php
                        $end       = $ends->get($e);
                        $arrowVals = $end?->arrow_values ?? array_fill(0, $ape, null);
                        $endSum    = $end?->end_total ?? 0;
                        $endGold   = collect($arrowVals)->filter(fn($v) => $v === 'X' || $v === 10)->count();
                        $endX      = collect($arrowVals)->filter(fn($v) => $v === 'X')->count();
                        $runningTotal += $endSum;
                        $setTotal += $endSum;
                        $setGold  += $endGold;
                        $setX     += $endX;
                        $allFilled = collect($arrowVals)->every(fn($v) => $v !== null && $v !== '');
                    @endphp
                    <tr class="{{ $allFilled ? 'bg-emerald-50/40' : '' }}">
                        <td class="px-4 py-2 text-center font-bold text-gray-500 text-xs">{{ $e }}</td>
                        @foreach($arrowVals as $val)
                        <td class="px-3 py-2 text-center">
                            @if($val !== null && $val !== '')
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-sm font-bold
                                    {{ $val === 'X' ? 'bg-emerald-100 text-emerald-700' : ($val === 'M' ? 'bg-red-100 text-red-600' : ($val == 10 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700')) }}">
                                    {{ $val }}
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-gray-50 text-gray-300 text-lg">·</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-3 py-2 text-center font-bold text-gray-800">{{ $endSum }}</td>
                        <td class="px-3 py-2 text-center font-bold text-indigo-700">{{ $runningTotal }}</td>
                        <td class="px-3 py-2 text-center font-semibold text-amber-600">{{ $endGold }}</td>
                        <td class="px-3 py-2 text-center font-semibold text-emerald-600">{{ $endX }}</td>
                    </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-teal-200 bg-teal-50/60">
                        <td colspan="{{ $ape + 1 }}" class="px-4 py-2.5 text-right text-xs font-bold text-teal-700 uppercase tracking-wider">
                            Sheet {{ $set }} Total
                        </td>
                        <td class="px-3 py-2.5 text-center text-base font-black text-indigo-700">{{ $setTotal }}</td>
                        <td class="px-3 py-2.5 text-center text-base font-black text-indigo-700">{{ $runningTotal }}</td>
                        <td class="px-3 py-2.5 text-center font-bold text-amber-600">{{ $setGold }}</td>
                        <td class="px-3 py-2.5 text-center font-bold text-emerald-600">{{ $setX }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endfor

</div>
@endsection
