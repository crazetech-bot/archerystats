@extends('layouts.app')

@section('title', $event->title)
@section('header', 'Event')
@section('subheader', $event->club->name)

@section('header-actions')
<div class="flex items-center gap-2">
    <a href="{{ route('events.index', ['month' => $event->starts_at->format('Y-m')]) }}"
       class="text-xs font-semibold px-3 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition">← Calendar</a>
    @if($canManage)
        <a href="{{ route('events.edit', $event) }}"
           class="text-xs font-semibold px-3 py-2 rounded-xl border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition">Edit</a>
    @endif
</div>
@endsection

@php
    $typeColor = [
        'training'    => '#0d9488',
        'competition' => '#e11d48',
        'meeting'     => '#4338ca',
        'social'      => '#d97706',
        'other'       => '#6b7280',
    ][$event->event_type] ?? '#6b7280';
@endphp

@section('content')
<div class="space-y-6 max-w-3xl">

    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Event card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5" style="background: linear-gradient(135deg, #0f172a, #1e293b); border-bottom: 3px solid {{ $typeColor }};">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white" style="background: {{ $typeColor }}">{{ $event->typeLabel() }}</span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background:rgba(255,255,255,.12);color:#cbd5e1">{{ $event->audienceLabel() }}</span>
                @if($event->pinned)<span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background:#fef3c7;color:#92400e">📌 Pinned</span>@endif
                @if($event->isPast())<span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background:rgba(255,255,255,.12);color:#94a3b8">Past</span>@endif
            </div>
            <h1 class="text-2xl font-black text-white">{{ $event->title }}</h1>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="flex flex-wrap gap-x-8 gap-y-3">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">When</p>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">
                        {{ $event->starts_at->format('l, d M Y') }}<br>
                        {{ $event->starts_at->format('g:i A') }}@if($event->ends_at) – {{ $event->ends_at->format('g:i A') }}@endif
                    </p>
                </div>
                @if($event->location)
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Where</p>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $event->location }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Organizer</p>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">
                        {{ $event->creator?->name ?? 'Club' }}@if($event->coach) (Coach)@endif
                    </p>
                </div>
            </div>
            @if($event->description)
                <p class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed border-t border-gray-50 pt-4">{{ $event->description }}</p>
            @endif
        </div>
    </div>

    {{-- RSVP --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Your RSVP</p>
        <form method="POST" action="{{ route('events.rsvp', $event) }}" class="flex flex-wrap gap-2">
            @csrf
            @foreach(['going' => ['Going', '#10b981'], 'maybe' => ['Maybe', '#f59e0b'], 'not_going' => ['Not going', '#6b7280']] as $val => [$lbl, $col])
                <button type="submit" name="response" value="{{ $val }}"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold border-2 transition"
                        style="{{ $myRsvp === $val ? "background:$col;border-color:$col;color:#fff" : "border-color:#e5e7eb;color:#6b7280" }}">
                    {{ $lbl }}
                </button>
            @endforeach
        </form>
        @if($myRsvp)
            <p class="text-xs text-gray-400 mt-2">You responded: <span class="font-semibold">{{ \App\Models\ClubEventRsvp::RESPONSES[$myRsvp] }}</span>. You can change it anytime.</p>
        @endif
    </div>

    {{-- Attendees --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach([['Going', $going, '#10b981'], ['Maybe', $maybe, '#f59e0b'], ['Not going', $notGoing, '#9ca3af']] as [$label, $list, $col])
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 flex items-center justify-between" style="border-bottom:2px solid {{ $col }}">
                <span class="text-sm font-bold text-gray-800">{{ $label }}</span>
                <span class="text-sm font-black" style="color:{{ $col }}">{{ $list->count() }}</span>
            </div>
            <div class="divide-y divide-gray-50 max-h-56 overflow-y-auto">
                @forelse($list as $r)
                    <div class="px-4 py-2 text-sm text-gray-600 truncate">{{ $r->user?->name ?? 'Member' }}</div>
                @empty
                    <div class="px-4 py-3 text-xs text-gray-400">Nobody yet.</div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    {{-- Organizer controls --}}
    @if($canManage)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-wrap items-center gap-3">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Organizer</p>
        @if($event->emailed_at)
            <span class="text-xs font-semibold px-2 py-1 rounded bg-emerald-50 text-emerald-600">✉ Emailed to {{ $event->email_count }}</span>
        @endif
        <div class="ml-auto flex items-center gap-2">
            <form method="POST" action="{{ route('events.pin', $event) }}">
                @csrf
                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition {{ $event->pinned ? 'border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100' : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    {{ $event->pinned ? 'Unpin' : 'Pin' }}
                </button>
            </form>
            <a href="{{ route('events.edit', $event) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition">Edit</a>
            <form method="POST" action="{{ route('events.destroy', $event) }}"
                  onsubmit="return confirm('Delete this event?') && confirm('Final confirmation — delete it for everyone (RSVPs included)?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">Delete</button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
