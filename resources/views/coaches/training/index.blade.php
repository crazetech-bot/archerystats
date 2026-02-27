@extends('layouts.app')

@section('title', 'Training Sessions — ' . $coach->full_name)
@section('header', 'Training Sessions')
@section('subheader', $coach->ref_no . ' · ' . $coach->full_name)

@section('header-actions')
    <a href="{{ route('coaches.show', $coach) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
    @if(auth()->user()->isClubAdmin() || auth()->user()->role === 'coach')
    <a href="{{ route('coaches.training.create', $coach) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow-md transition-all hover:opacity-90"
       style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        New Session
    </a>
    @endif
@endsection

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Stats bar --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        @php
            $total     = $sessions->total();
            $lastDate  = $sessions->first()?->date?->format('d M Y') ?? '—';
            $avgArchers = $sessions->isNotEmpty()
                ? round($sessions->sum('archers_count') / max($sessions->count(), 1), 1)
                : 0;
        @endphp
        @foreach([
            ['Total Sessions', $total, 'teal'],
            ['Last Session', $lastDate, 'indigo'],
            ['Avg Attendees', $avgArchers, 'emerald'],
        ] as [$label, $val, $color])
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 text-center">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</p>
            <p class="text-xl font-bold text-{{ $color }}-600 mt-1">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($sessions->isEmpty())
            <div class="px-6 py-16 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                <p class="text-sm font-medium text-gray-500">No training sessions logged yet</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Focus Area</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Attendees</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($sessions as $ts)
                    <tr class="hover:bg-teal-50/30 transition-colors cursor-pointer"
                        onclick="window.location='{{ route('coaches.training.show', [$coach, $ts]) }}'">
                        <td class="px-6 py-3">
                            <p class="font-semibold text-gray-900">{{ $ts->date->format('d M Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $ts->date->format('l') }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $ts->location ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $ts->focus_area ?? '—' }}</td>
                        <td class="px-6 py-3 text-center text-gray-600">{{ $ts->duration_label }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center justify-center h-7 w-7 rounded-full text-xs font-bold text-teal-700 bg-teal-100">
                                {{ $ts->archers_count }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('coaches.training.show', [$coach, $ts]) }}"
                                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                    View
                                </a>
                                @if(auth()->user()->isClubAdmin() || auth()->user()->role === 'coach')
                                <a href="{{ route('coaches.training.edit', [$coach, $ts]) }}"
                                   class="text-xs font-medium text-amber-600 hover:text-amber-800 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('coaches.training.destroy', [$coach, $ts]) }}"
                                      x-data @submit.prevent="if(confirm('Delete this training session?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-medium text-red-600 hover:text-red-800 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 transition-colors">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($sessions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $sessions->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
