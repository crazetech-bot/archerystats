@extends('layouts.app')

@section('title', 'Sessions — ' . $archer->full_name)
@section('header', 'Sessions')
@section('subheader', $archer->ref_no . ' — ' . $archer->full_name)

@section('header-actions')
    <a href="{{ route('archers.show', $archer) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Profile
    </a>
    @if(in_array(auth()->user()->role, ['super_admin', 'club_admin', 'archer']))
    <a href="{{ route('sessions.create', $archer) }}"
       class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
       style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        NEW SESSION
    </a>
    @endif
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- Stats bar --}}
    @php
        $allSessions = $sessions->getCollection();
        $bestScore = $allSessions->max(fn($s) => $s->score?->total_score ?? 0);
        $latestDate = $allSessions->first()?->date;
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 text-center shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #f59e0b;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Sessions</p>
            <p class="text-4xl font-black text-slate-900" style="font-family:'Barlow',sans-serif;">{{ $sessions->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 text-center shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #f59e0b;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Best Score</p>
            <p class="text-4xl font-black" style="color:#f59e0b; font-family:'Barlow',sans-serif;">{{ $bestScore ?: '—' }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 text-center shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #64748b;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Last Session</p>
            <p class="text-2xl font-black text-slate-700" style="font-family:'Barlow',sans-serif;">{{ $latestDate ? $latestDate->format('d M Y') : '—' }}</p>
        </div>
    </div>

    {{-- Sessions list --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
        <div class="flex items-center gap-3 px-5 py-4" style="background:#0f172a; border-bottom:3px solid #f59e0b;">
            <svg class="h-5 w-5 flex-shrink-0" style="color:#f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
            </svg>
            <div>
                <h2 class="text-sm font-black text-white" style="font-family:'Barlow',sans-serif;">ALL SESSIONS</h2>
                <p class="text-xs text-slate-400 font-medium">Click a row to open the scorecard</p>
            </div>
        </div>

        @if($sessions->isEmpty())
            <div class="px-6 py-20 text-center">
                <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-600">No sessions yet</p>
                @if(in_array(auth()->user()->role, ['super_admin', 'club_admin', 'archer']))
                <p class="text-xs text-slate-400 mt-1 font-medium">Start a new session to record scores</p>
                <a href="{{ route('sessions.create', $archer) }}"
                   class="mt-5 inline-flex items-center gap-2 text-sm font-black px-5 py-2.5 rounded-xl shadow-md transition-all active:scale-95"
                   style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
                    NEW SESSION
                </a>
                @else
                <p class="text-xs text-slate-400 mt-1 font-medium">Sessions are recorded by the archer or club admin.</p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead style="background:#f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Round</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:table-cell">Location</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">Score</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:table-cell">10+X</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:table-cell">X</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:table-cell">Type</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sessions as $s)
                            <tr class="hover:bg-amber-50/30 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('sessions.scorecard', $s) }}'">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-800">{{ $s->date->format('d M Y') }}</p>
                                    <p class="text-xs text-slate-400 font-medium">{{ $s->date->format('l') }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-800">{{ $s->roundType->name }}</p>
                                    <p class="text-xs text-slate-400 font-medium">{{ $s->roundType->num_ends }} ends × {{ $s->roundType->arrows_per_end }} arrows</p>
                                </td>
                                <td class="px-5 py-4 text-slate-600 font-medium hidden sm:table-cell">{{ $s->location ?? '—' }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if($s->score?->total_score > 0)
                                        <span class="text-2xl font-black" style="color:#0f172a; font-family:'Barlow',sans-serif;">{{ $s->score->total_score }}</span>
                                    @else
                                        <span class="text-slate-300 font-black text-xl">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center font-black hidden sm:table-cell" style="color:#f59e0b; font-family:'Barlow',sans-serif;">
                                    {{ $s->score?->gold_count ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-center font-black text-emerald-600 hidden sm:table-cell" style="font-family:'Barlow',sans-serif;">
                                    {{ $s->score?->x_count ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-center hidden sm:table-cell">
                                    @if($s->is_competition)
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg"
                                              style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">
                                            Competition
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Training</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('sessions.scorecard', $s) }}"
                                           class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                                           style="background:#0f172a; color:#ffffff;">
                                            Open
                                        </a>
                                        @if(in_array(auth()->user()->role, ['super_admin', 'club_admin', 'archer']))
                                        <form method="POST" action="{{ route('sessions.destroy', $s) }}"
                                              x-data @submit.prevent="if(confirm('Delete this session?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="text-xs font-bold text-red-500 hover:text-white bg-red-50 hover:bg-red-500 border border-red-200 hover:border-red-500 px-3 py-1.5 rounded-lg transition-all">
                                                Del
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($sessions->hasPages())
                <div class="px-6 py-4" style="border-top: 1px solid #f1f5f9;">
                    {{ $sessions->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
