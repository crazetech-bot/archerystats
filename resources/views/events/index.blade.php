@extends('layouts.app')

@section('title', 'Events')
@section('header', 'Events')
@section('subheader', 'Club calendar & RSVP')

@php
    $typeDot = [
        'training'    => '#0d9488',
        'competition' => '#e11d48',
        'meeting'     => '#4338ca',
        'social'      => '#d97706',
        'other'       => '#6b7280',
    ];
    $prev = $month->copy()->subMonth()->format('Y-m');
    $next = $month->copy()->addMonth()->format('Y-m');
@endphp

@section('content')
<div class="space-y-6">

    @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="rounded-xl bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 text-sm">{{ session('info') }}</div>
    @endif

    {{-- Create event (admins & coaches) --}}
    @if($composeAudiences)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ open: false }">
        <button type="button" @click="open = !open"
                class="w-full flex items-center gap-3 px-6 py-4 border-b border-gray-100 text-left"
                style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
            <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
            </span>
            <div class="flex-1">
                <h2 class="text-sm font-bold text-gray-900">Create Event</h2>
                <p class="text-xs text-gray-500">{{ $coachMode ? 'For your assigned archers' : 'For your club' }}</p>
            </div>
            <svg class="h-5 w-5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>
        <form x-show="open" x-cloak method="POST" action="{{ route('events.store') }}" class="p-6 space-y-4"
              x-data="{ emailIt: false }"
              @submit="if (emailIt && !confirm('Create event AND email the audience?')) $event.preventDefault()">
            @csrf
            @include('events._fields', ['event' => null, 'composeAudiences' => $composeAudiences, 'coachMode' => $coachMode])
            <div class="flex flex-wrap items-center gap-4">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                    <input type="checkbox" name="pinned" value="1" class="rounded border-gray-300 text-amber-600 focus:ring-amber-400">
                    Pin to members' dashboard
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                    <input type="checkbox" name="send_email" value="1" x-model="emailIt" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                    Email the audience
                </label>
                <button type="submit" class="ml-auto px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:opacity-90"
                        style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                    Create Event
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Month calendar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">{{ $month->format('F Y') }}</h2>
            <div class="ml-auto flex items-center gap-1">
                <a href="{{ route('events.index', ['month' => $prev]) }}" class="h-8 w-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50" aria-label="Previous month">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </a>
                <a href="{{ route('events.index') }}" class="px-3 h-8 rounded-lg border border-gray-200 flex items-center text-xs font-semibold text-gray-600 hover:bg-gray-50">Today</a>
                <a href="{{ route('events.index', ['month' => $next]) }}" class="h-8 w-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50" aria-label="Next month">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <div style="min-width:680px">
                <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-100">
                    @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d)
                        <div class="px-2 py-2 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $d }}</div>
                    @endforeach
                </div>
                @foreach($weeks as $week)
                <div class="grid grid-cols-7">
                    @foreach($week as $cell)
                    @php $isToday = $cell['date']->isToday(); @endphp
                    <div class="min-h-[92px] border-b border-r border-gray-50 p-1.5 {{ $cell['inMonth'] ? '' : 'bg-gray-50/50' }}">
                        <div class="text-xs font-semibold mb-1 flex items-center justify-center h-6 w-6 rounded-full {{ $isToday ? 'bg-indigo-600 text-white' : ($cell['inMonth'] ? 'text-gray-600' : 'text-gray-300') }}">
                            {{ $cell['date']->day }}
                        </div>
                        <div class="space-y-1">
                            @foreach(array_slice($cell['items'], 0, 3) as $it)
                                @if($it['kind'] === 'event')
                                    @php $ev = $it['model']; @endphp
                                    <a href="{{ route('events.show', $ev) }}" class="flex items-center gap-1 text-[11px] leading-tight rounded px-1 py-0.5 hover:bg-gray-100 truncate" title="{{ $ev->title }}">
                                        <span class="h-2 w-2 rounded-full flex-shrink-0" style="background: {{ $typeDot[$ev->event_type] ?? '#6b7280' }}"></span>
                                        <span class="truncate text-gray-700 font-medium">{{ $ev->starts_at->format('g:i') }} {{ $ev->title }}</span>
                                    </a>
                                @else
                                    <a href="{{ $it['url'] }}" class="flex items-center gap-1 text-[11px] leading-tight rounded px-1 py-0.5 hover:bg-gray-100 truncate opacity-70" title="{{ $it['kind'] === 'training' ? 'Training: ' : 'Match: ' }}{{ $it['label'] }}">
                                        <span class="text-[10px] flex-shrink-0">{{ $it['kind'] === 'training' ? '🎯' : '⚔️' }}</span>
                                        <span class="truncate text-gray-500 italic">{{ $it['label'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                            @if(count($cell['items']) > 3)
                                <div class="text-[10px] text-gray-400 px-1">+{{ count($cell['items']) - 3 }} more</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        <div class="flex flex-wrap gap-3 px-6 py-3 text-[11px] text-gray-500 border-t border-gray-50">
            @foreach($typeDot as $t => $col)
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full" style="background:{{ $col }}"></span>{{ \App\Models\ClubEvent::TYPES[$t] }}</span>
            @endforeach
            <span class="flex items-center gap-1">🎯 Training</span>
            <span class="flex items-center gap-1">⚔️ Match</span>
        </div>
    </div>

    {{-- Upcoming list --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Upcoming Events</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($upcoming as $ev)
            @php $mine = $myRsvps[$ev->id] ?? null; @endphp
            <div class="px-6 py-4 flex flex-wrap items-center gap-4">
                <div class="flex flex-col items-center justify-center h-12 w-12 rounded-xl bg-indigo-50 flex-shrink-0">
                    <span class="text-[10px] font-bold text-indigo-500 uppercase">{{ $ev->starts_at->format('M') }}</span>
                    <span class="text-lg font-black text-indigo-700 leading-none">{{ $ev->starts_at->format('j') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('events.show', $ev) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-700">{{ $ev->title }}</a>
                    @if($ev->pinned)<span class="ml-1 text-xs">📌</span>@endif
                    <p class="text-xs text-gray-400 truncate">
                        {{ $ev->starts_at->format('D g:i A') }}
                        @if($ev->location) · {{ $ev->location }} @endif
                        · <span class="font-semibold" style="color:{{ $typeDot[$ev->event_type] ?? '#6b7280' }}">{{ $ev->typeLabel() }}</span>
                    </p>
                </div>
                <form method="POST" action="{{ route('events.rsvp', $ev) }}" class="flex items-center gap-1 flex-shrink-0">
                    @csrf
                    @foreach(['going' => 'Going', 'maybe' => 'Maybe', 'not_going' => 'No'] as $val => $lbl)
                        <button type="submit" name="response" value="{{ $val }}"
                                class="text-xs font-bold px-2.5 py-1.5 rounded-lg border transition
                                       {{ $mine === $val
                                          ? ($val === 'going' ? 'bg-emerald-500 border-emerald-500 text-white' : ($val === 'maybe' ? 'bg-amber-400 border-amber-400 text-white' : 'bg-gray-400 border-gray-400 text-white'))
                                          : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                            {{ $lbl }}
                        </button>
                    @endforeach
                </form>
            </div>
            @empty
                <div class="px-6 py-10 text-center text-sm text-gray-400">No upcoming events.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
