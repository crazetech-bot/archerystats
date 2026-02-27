@extends('layouts.app')

@section('title', 'Training Session — ' . $training->date->format('d M Y'))
@section('header', 'Training Session')
@section('subheader', $training->date->format('l, d M Y') . ' · ' . $coach->full_name)

@section('header-actions')
    <a href="{{ route('coaches.training.index', $coach) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
    @if(auth()->user()->isClubAdmin())
        <a href="{{ route('coaches.training.edit', [$coach, $training]) }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow-md transition-all hover:opacity-90"
           style="background: linear-gradient(135deg, #d97706, #f59e0b);">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
            Edit
        </a>
        <form method="POST" action="{{ route('coaches.training.destroy', [$coach, $training]) }}"
              x-data @submit.prevent="if(confirm('Delete this training session?')) $el.submit()">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow-md transition-all hover:opacity-90"
                    style="background: linear-gradient(135deg, #dc2626, #ef4444);">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
                Delete
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Session Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
            <span class="h-6 w-6 rounded-lg bg-teal-100 flex items-center justify-center">
                <svg class="h-3.5 w-3.5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
            </span>
            Session Details
        </h3>
        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @foreach([
                'Date'       => $training->date->format('d M Y'),
                'Location'   => $training->location ?? '—',
                'Focus Area' => $training->focus_area ?? '—',
                'Duration'   => $training->duration_label,
                'Attendees'  => $training->archers->count() . ' archers',
                'Logged by'  => $coach->full_name,
            ] as $label => $value)
            <div class="bg-gray-50 rounded-xl px-4 py-3">
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</dt>
                <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $value }}</dd>
            </div>
            @endforeach
        </dl>

        @if($training->notes)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Notes</p>
            <p class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed">{{ $training->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Shooting Assignment --}}
    @if($training->roundType)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
            <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
                </svg>
            </span>
            <div class="flex-1">
                <h2 class="text-sm font-bold text-gray-900">Shooting Assignment</h2>
                <p class="text-xs text-gray-500">Archer sessions created for this training</p>
            </div>
        </div>

        {{-- Round type summary --}}
        <div class="px-6 pt-5 pb-4 flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Round Type</p>
                <p class="text-base font-bold text-gray-900 mt-0.5">{{ $training->roundType->name }}</p>
            </div>
            @if($training->roundType->distance_meters || $training->distance_meters)
            <div class="bg-indigo-50 rounded-xl px-4 py-2 text-center">
                <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wider">Distance</p>
                <p class="text-lg font-black text-indigo-700" style="font-family:'Barlow',sans-serif;">
                    {{ $training->distance_meters ?? $training->roundType->distance_meters }}m
                </p>
            </div>
            @endif
            @if($training->roundType->target_face_cm || $training->target_face_cm)
            <div class="bg-indigo-50 rounded-xl px-4 py-2 text-center">
                <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wider">Face</p>
                <p class="text-lg font-black text-indigo-700" style="font-family:'Barlow',sans-serif;">
                    {{ $training->target_face_cm ?? $training->roundType->target_face_cm }}cm
                </p>
            </div>
            @endif
            <div class="bg-gray-50 rounded-xl px-4 py-2 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Max Score</p>
                <p class="text-lg font-black text-gray-700" style="font-family:'Barlow',sans-serif;">
                    {{ $training->roundType->max_score }}
                </p>
            </div>
        </div>

        {{-- Per-archer session status table --}}
        @php $assignedSessions = $training->assignedSessions->keyBy('archer_id'); @endphp
        @if($training->archers->isNotEmpty())
        <div class="overflow-x-auto border-t border-gray-100">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Archer</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Score</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($training->archers as $archer)
                    @php $as = $assignedSessions->get($archer->id); @endphp
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $archer->photo_url }}" alt="{{ $archer->full_name }}"
                                     class="h-8 w-8 rounded-lg object-cover flex-shrink-0">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $archer->full_name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $archer->ref_no }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if(!$as)
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(156,163,175,0.15); color:#6b7280; border:1px solid rgba(156,163,175,0.3);">
                                    Not Assigned
                                </span>
                            @elseif($as->score?->total_score > 0)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(16,185,129,0.12); color:#065f46; border:1px solid rgba(16,185,129,0.25);">
                                    ✓ Scored
                                </span>
                            @else
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-black text-gray-900" style="font-family:'Barlow',sans-serif;">
                            {{ $as?->score?->total_score ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($as)
                            <a href="{{ route('sessions.scorecard', $as) }}"
                               class="text-xs font-bold text-indigo-500 hover:text-indigo-700 transition-colors whitespace-nowrap">
                                {{ $as->score ? 'View →' : 'Enter Score →' }}
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    {{-- Elimination Matches --}}
    @if($training->eliminationMatches->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #fdf4ff, #fae8ff);">
            <span class="h-8 w-8 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Elimination Matches</h2>
                <p class="text-xs text-gray-500">{{ $training->eliminationMatches->count() }} match(es) assigned to this session</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Archer A</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Archer B</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Category</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($training->eliminationMatches as $em)
                    @php
                        $catColors = ['outdoor' => '#059669', 'indoor' => '#4338ca', 'mssm' => '#db2777'];
                        $catColor  = $catColors[$em->category] ?? '#64748b';
                        $catLabel  = $em->category === 'mssm' ? 'MSSM' : ucfirst($em->category);
                        $nameA     = $em->archer_a_id ? $em->archerA->full_name : $em->archer_a_name;
                        $nameB     = $em->archer_b_id ? $em->archerB->full_name : $em->archer_b_name;
                    @endphp
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-gray-900">{{ $nameA }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-gray-900">{{ $nameB }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold text-white"
                                  style="background: {{ $catColor }};">{{ $catLabel }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($em->status === 'completed')
                                <span class="text-xs font-bold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(16,185,129,0.12); color:#065f46; border:1px solid rgba(16,185,129,0.25);">✓ Completed</span>
                            @else
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">In Progress</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('elimination-matches.scorecard', $em) }}"
                               class="text-xs font-bold text-purple-500 hover:text-purple-700 transition-colors whitespace-nowrap">
                                {{ $em->status === 'completed' ? 'View →' : 'Score →' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Attendance --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
            <span class="h-8 w-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Attendance</h2>
                <p class="text-xs text-gray-500">{{ $training->archers->count() }} archer(s) attended</p>
            </div>
        </div>

        @if($training->archers->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-gray-400">No attendance recorded for this session.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-6">
                @foreach($training->archers as $archer)
                <a href="{{ route('archers.show', $archer) }}"
                   class="flex items-center gap-3 p-3 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                    <img src="{{ $archer->photo_url }}" alt="{{ $archer->full_name }}"
                         class="h-10 w-10 rounded-xl object-cover flex-shrink-0">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $archer->full_name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $archer->ref_no }}</p>
                    </div>
                    <svg class="h-4 w-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </a>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
