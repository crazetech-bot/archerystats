@extends('layouts.app')

@section('title', 'National Team')
@section('header', 'National Team')
@section('subheader', 'Archers representing Malaysia at national level')

@section('content')

@php
    $total = $podium->count() + $pelapis->count() + $para->count();

    $sections = [
        [
            'label'   => 'Podium',
            'archers' => $podium,
            'color'   => '#f59e0b',
            'bg'      => 'rgba(245,158,11,0.10)',
            'text'    => '#92400e',
            'border'  => 'rgba(245,158,11,0.35)',
            'top'     => '#f59e0b',
        ],
        [
            'label'   => 'Pelapis Kebangsaan',
            'archers' => $pelapis,
            'color'   => '#4338ca',
            'bg'      => 'rgba(67,56,202,0.08)',
            'text'    => '#312e81',
            'border'  => 'rgba(67,56,202,0.3)',
            'top'     => '#4338ca',
        ],
        [
            'label'   => 'PARA',
            'archers' => $para,
            'color'   => '#7c3aed',
            'bg'      => 'rgba(124,58,237,0.08)',
            'text'    => '#4c1d95',
            'border'  => 'rgba(124,58,237,0.3)',
            'top'     => '#7c3aed',
        ],
    ];

@endphp

{{-- Stats bar --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #0f172a;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total</p>
        <p class="text-4xl font-black text-slate-900 mt-1" style="font-family:'Barlow',sans-serif;">{{ $total }}</p>
        <p class="text-xs text-slate-500 mt-1">National team archers</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #f59e0b;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Podium</p>
        <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#f59e0b;">{{ $podium->count() }}</p>
        <p class="text-xs text-slate-500 mt-1">Podium squad</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #4338ca;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pelapis</p>
        <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#4338ca;">{{ $pelapis->count() }}</p>
        <p class="text-xs text-slate-500 mt-1">Pelapis Kebangsaan</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #7c3aed;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">PARA</p>
        <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#7c3aed;">{{ $para->count() }}</p>
        <p class="text-xs text-slate-500 mt-1">Para athletes</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #0d9488;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Coaches</p>
        <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#0d9488;">{{ $coaches->count() }}</p>
        <p class="text-xs text-slate-500 mt-1">Registered coaches</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #64748b;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Admin</p>
        <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#64748b;">{{ $admins->count() }}</p>
        <p class="text-xs text-slate-500 mt-1">National Team admin</p>
    </div>
</div>

{{-- Archer Sections --}}
@foreach($sections as $section)
<div class="mb-8">
    {{-- Section header --}}
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full"
              style="background:{{ $section['bg'] }}; color:{{ $section['text'] }}; border: 1px solid {{ $section['border'] }};">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
            </svg>
            {{ $section['label'] }}
        </span>
        <span class="text-xs font-bold text-slate-400">{{ $section['archers']->count() }} {{ Str::plural('archer', $section['archers']->count()) }}</span>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0; border-top: 3px solid {{ $section['top'] }};">
        <table class="min-w-full">
            <thead>
                <tr style="background:#0f172a;">
                    <th class="w-12 py-3 pl-5 pr-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest"></th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Ref No</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Gender</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Division</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">State Team</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Club</th>
                    <th class="px-4 py-3 pr-5 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($section['archers'] as $archer)
                    <tr class="transition-colors hover:bg-slate-50 group">
                        <td class="py-3 pl-5 pr-3">
                            <img src="{{ $archer->photo_url }}" alt="{{ $archer->full_name }}"
                                 class="h-10 w-10 rounded-full object-cover bg-slate-100 ring-2 ring-white shadow-sm">
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block text-xs font-mono font-bold px-2.5 py-1 rounded-lg"
                                  style="background:#0f172a; color:#f59e0b;">
                                {{ $archer->ref_no ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('archers.show', $archer) }}"
                               class="text-sm font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                {{ $archer->full_name }}
                            </a>
                            <p class="text-xs text-slate-400">{{ $archer->user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($archer->gender === 'male')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-full">
                                    ♂ Male
                                </span>
                            @elseif($archer->gender === 'female')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-pink-700 bg-pink-50 border border-pink-200 px-2.5 py-1 rounded-full">
                                    ♀ Female
                                </span>
                            @else
                                <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($archer->division)
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full"
                                      style="background:rgba(245,158,11,0.12); color:#92400e; border: 1px solid rgba(245,158,11,0.3);">
                                    {{ $archer->division }}
                                </span>
                            @else
                                <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600 hidden md:table-cell">
                            {{ $archer->stateTeam?->name ?? ($archer->state_team ?? '—') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600 hidden md:table-cell">
                            {{ $archer->club?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 pr-5 text-right">
                            <a href="{{ route('archers.show', $archer) }}"
                               class="inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                               style="background:#0f172a; color:#ffffff;">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center">
                            <p class="text-sm text-slate-400">No archers in {{ $section['label'] }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endforeach

{{-- Coaches Section --}}
<div class="mb-8">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full"
              style="background:rgba(13,148,136,0.10); color:#065f46; border: 1px solid rgba(13,148,136,0.3);">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-1.342m-7.482 0a49.773 49.773 0 0 1-3.741 1.342m7.482 0a49.773 49.773 0 0 1 3.741 1.342"/>
            </svg>
            Coaches
        </span>
        <span class="text-xs font-bold text-slate-400">{{ $coaches->count() }} {{ Str::plural('coach', $coaches->count()) }}</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0; border-top: 3px solid #0d9488;">
        <table class="min-w-full">
            <thead>
                <tr style="background:#0f172a;">
                    <th class="w-12 py-3 pl-5 pr-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest"></th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Ref No</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Gender</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Coaching Level</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">State Team</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Club</th>
                    <th class="px-4 py-3 pr-5 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($coaches as $coach)
                    <tr class="transition-colors hover:bg-slate-50 group">
                        <td class="py-3 pl-5 pr-3">
                            <img src="{{ $coach->photo_url }}" alt="{{ $coach->full_name }}"
                                 class="h-10 w-10 rounded-full object-cover bg-slate-100 ring-2 ring-white shadow-sm">
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block text-xs font-mono font-bold px-2.5 py-1 rounded-lg"
                                  style="background:#0f172a; color:#14b8a6;">
                                {{ $coach->ref_no ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('coaches.show', $coach) }}"
                               class="text-sm font-bold text-slate-900 hover:text-teal-600 transition-colors">
                                {{ $coach->full_name }}
                            </a>
                            <p class="text-xs text-slate-400">{{ $coach->user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($coach->gender === 'male')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-full">
                                    ♂ Male
                                </span>
                            @elseif($coach->gender === 'female')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-pink-700 bg-pink-50 border border-pink-200 px-2.5 py-1 rounded-full">
                                    ♀ Female
                                </span>
                            @else
                                <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            @if($coach->coaching_level && $coach->coaching_level !== 'None')
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                      style="background:rgba(13,148,136,0.10); color:#065f46; border:1px solid rgba(13,148,136,0.25);">
                                    {{ $coach->coaching_level }}
                                </span>
                            @else
                                <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600 hidden md:table-cell">
                            {{ $coach->stateTeam?->name ?? ($coach->state ?? '—') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600 hidden md:table-cell">
                            {{ $coach->club?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 pr-5 text-right">
                            <a href="{{ route('coaches.show', $coach) }}"
                               class="inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                               style="background:#0f172a; color:#ffffff;">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center">
                            <p class="text-sm text-slate-400">No coaches registered</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Admin Section --}}
<div class="mb-8">
    <div class="flex items-center gap-3 mb-3">
        <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full"
              style="background:rgba(100,116,139,0.10); color:#334155; border: 1px solid rgba(100,116,139,0.3);">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
            </svg>
            Admin
        </span>
        <span class="text-xs font-bold text-slate-400">{{ $admins->count() }} {{ Str::plural('user', $admins->count()) }}</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0; border-top: 3px solid #64748b;">
        <table class="min-w-full">
            <thead>
                <tr style="background:#0f172a;">
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Club</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($admins as $admin)
                    <tr class="transition-colors hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <p class="text-sm font-bold text-slate-900">{{ $admin->name }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-slate-600">{{ $admin->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600 hidden md:table-cell">
                            {{ $admin->club?->name ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center">
                            <p class="text-sm text-slate-400">No National Team admin users found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
