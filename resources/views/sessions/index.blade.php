@extends('layouts.app')

@section('title', 'Sessions — ' . $archer->full_name)
@section('header', 'Sessions')
@section('subheader', $archer->ref_no . ' — ' . $archer->full_name)

@section('header-actions')
    <a href="{{ route('archers.show', $archer) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Profile
    </a>
    <a href="{{ route('sessions.create', $archer) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow-md transition-all hover:opacity-90"
       style="background: linear-gradient(135deg, #4338ca, #6366f1);">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        New Session
    </a>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-3 text-sm font-medium flex items-center gap-2">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats bar --}}
    @php
        $allSessions = $sessions->getCollection();
        $bestScore = $allSessions->max(fn($s) => $s->score?->total_score ?? 0);
        $latestDate = $allSessions->first()?->date;
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Sessions</p>
            <p class="text-3xl font-black text-indigo-700">{{ $sessions->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Best Score</p>
            <p class="text-3xl font-black text-amber-600">{{ $bestScore ?: '—' }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Last Session</p>
            <p class="text-xl font-black text-gray-700">{{ $latestDate ? $latestDate->format('d M Y') : '—' }}</p>
        </div>
    </div>

    {{-- Sessions list --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f8faff, #f0f4ff);">
            <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">All Sessions</h2>
                <p class="text-xs text-gray-500">Click a row to open the scorecard</p>
            </div>
        </div>

        @if($sessions->isEmpty())
            <div class="px-6 py-16 text-center">
                <svg class="h-12 w-12 text-gray-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                </svg>
                <p class="text-sm font-semibold text-gray-400">No sessions yet</p>
                <p class="text-xs text-gray-400 mt-1">Start a new session to record scores</p>
                <a href="{{ route('sessions.create', $archer) }}"
                   class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-white px-5 py-2.5 rounded-xl shadow-md transition-all hover:opacity-90"
                   style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                    New Session
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Round</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Location</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">10+X</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">X</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Type</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sessions as $s)
                            <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('sessions.scorecard', $s) }}'">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-800">{{ $s->date->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $s->date->format('l') }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-800">{{ $s->roundType->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $s->roundType->num_ends }} ends × {{ $s->roundType->arrows_per_end }} arrows</p>
                                </td>
                                <td class="px-5 py-4 text-gray-600 hidden sm:table-cell">{{ $s->location ?? '—' }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if($s->score?->total_score > 0)
                                        <span class="text-lg font-black text-indigo-700">{{ $s->score->total_score }}</span>
                                    @else
                                        <span class="text-gray-300 font-bold">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-amber-600 hidden sm:table-cell">
                                    {{ $s->score?->gold_count ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-emerald-600 hidden sm:table-cell">
                                    {{ $s->score?->x_count ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-center hidden sm:table-cell">
                                    @if($s->is_competition)
                                        <span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-1 rounded-lg">Competition</span>
                                    @else
                                        <span class="text-xs text-gray-400">Training</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('sessions.scorecard', $s) }}"
                                           class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            Open
                                        </a>
                                        <form method="POST" action="{{ route('sessions.destroy', $s) }}"
                                              x-data @submit.prevent="if(confirm('Delete this session?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-400 hover:text-red-600 hover:underline">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($sessions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $sessions->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
