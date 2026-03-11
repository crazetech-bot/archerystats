@extends('layouts.app')

@section('title', 'Archers')
@section('header', 'Archers')
@section('subheader', 'Manage registered archers')

@section('header-actions')
    @if(auth()->user()->isClubAdmin())
        <a href="{{ route('archers.import') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-black tracking-wide shadow-md
                  transition-all active:scale-95"
           style="background:#e2e8f0; color:#1e293b; font-family:'Barlow',sans-serif;">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            IMPORT CSV
        </a>
        <a href="{{ route('archers.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-black tracking-wide shadow-md
                  transition-all active:scale-95"
           style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            ADD ARCHER
        </a>
    @endif
@endsection

@section('content')

@php
    $hasFilters = request()->hasAny(['search','club_id','state'])
                 && collect(request()->only(['search','club_id','state']))->filter()->isNotEmpty();
@endphp

{{-- Stats bar --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @php
        $male   = \App\Models\Archer::where('gender','male')->count();
        $female = \App\Models\Archer::where('gender','female')->count();
    @endphp
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #f59e0b;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total</p>
        <p class="text-4xl font-black text-slate-900 mt-1" style="font-family:'Barlow',sans-serif;">{{ $totalArchers }}</p>
        <p class="text-xs text-slate-500 mt-1">Registered archers</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #3b82f6;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Male</p>
        <p class="text-4xl font-black text-blue-600 mt-1" style="font-family:'Barlow',sans-serif;">{{ $male }}</p>
        <p class="text-xs text-slate-500 mt-1">Male archers</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #ec4899;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Female</p>
        <p class="text-4xl font-black text-pink-500 mt-1" style="font-family:'Barlow',sans-serif;">{{ $female }}</p>
        <p class="text-xs text-slate-500 mt-1">Female archers</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #64748b;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Showing</p>
        <p class="text-4xl font-black text-slate-700 mt-1" style="font-family:'Barlow',sans-serif;">{{ $archers->total() }}</p>
        <p class="text-xs text-slate-500 mt-1">{{ $hasFilters ? 'Matching filters' : 'On all pages' }}</p>
    </div>
</div>

{{-- Filter bar --}}
<form method="GET" action="{{ route('archers.index') }}"
      class="bg-white rounded-2xl shadow-sm mb-5 p-4 flex flex-wrap gap-3 items-end"
      style="border: 1px solid #e2e8f0;">

    {{-- Search --}}
    <div class="flex-1 min-w-[180px]">
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Name, MAREOS ID, Ref No…"
               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
    </div>

    {{-- Club --}}
    <div class="min-w-[150px]">
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Club</label>
        <select name="club_id"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
            <option value="">All Clubs</option>
            @foreach($clubs as $club)
                <option value="{{ $club->id }}" @selected(request('club_id') == $club->id)>{{ $club->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- State --}}
    <div class="min-w-[150px]">
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">State</label>
        <select name="state"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
            <option value="">All States</option>
            @foreach($states as $s)
                <option value="{{ $s }}" @selected(request('state') === $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>

    {{-- Buttons --}}
    <div class="flex gap-2">
        <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm transition-all active:scale-95"
                style="background: linear-gradient(135deg,#4338ca,#6366f1);">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            Filter
        </button>
        @if($hasFilters)
            <a href="{{ route('archers.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all active:scale-95">
                ✕ Reset
            </a>
        @endif
    </div>
</form>

{{-- Result count --}}
@if($hasFilters)
    <p class="text-xs text-slate-500 mb-3 px-1">
        Showing <span class="font-bold text-slate-700">{{ $archers->total() }}</span> of
        <span class="font-bold text-slate-700">{{ $totalArchers }}</span> archers
        &mdash; <a href="{{ route('archers.index') }}" class="text-indigo-600 hover:underline">Clear filters</a>
    </p>
@endif

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-x-auto" style="border: 1px solid #e2e8f0;">
    <table class="min-w-full">
        <thead>
            <tr style="background: #0f172a;">
                <th class="w-12 py-3.5 pl-5 pr-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest"></th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">MAREOS ID</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Name</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Gender</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Age</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Division</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">Club</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden lg:table-cell">State</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden lg:table-cell">Para-Archery</th>
                <th class="px-4 py-3.5 text-right pr-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($archers as $archer)
                <tr class="transition-colors hover:bg-amber-50/40 group">

                    {{-- Photo --}}
                    <td class="py-3.5 pl-5 pr-3">
                        <img src="{{ $archer->photo_url }}" alt="{{ $archer->full_name }}"
                             class="h-10 w-10 rounded-full object-cover bg-slate-100 ring-2 ring-white shadow-sm">
                    </td>

                    {{-- MAREOS ID --}}
                    <td class="px-4 py-3.5">
                        @if($archer->mareos_id)
                            <span class="inline-block text-xs font-mono font-bold px-2.5 py-1 rounded-lg"
                                  style="background:#0f766e; color:#ccfbf1;">
                                {{ $archer->mareos_id }}
                            </span>
                        @else
                            <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>

                    {{-- Name --}}
                    <td class="px-4 py-3.5">
                        <a href="{{ route('archers.show', $archer) }}"
                           class="text-sm font-bold text-slate-900 hover:text-amber-600 transition-colors">
                            {{ $archer->full_name }}
                        </a>
                        <p class="text-xs text-slate-400 font-mono">{{ $archer->ref_no ?? '—' }}</p>
                    </td>

                    {{-- Gender --}}
                    <td class="px-4 py-3.5 hidden sm:table-cell">
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

                    {{-- Age --}}
                    <td class="px-4 py-3.5 hidden sm:table-cell">
                        @if($archer->age !== null)
                            <span class="text-sm font-bold text-slate-700">{{ $archer->age }}</span>
                            <span class="text-xs text-slate-400"> yrs</span>
                        @else
                            <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>

                    {{-- Division --}}
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        @if($archer->division)
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full"
                                  style="background:rgba(245,158,11,0.12); color:#92400e; border: 1px solid rgba(245,158,11,0.3);">
                                {{ $archer->division }}
                            </span>
                        @endif
                        @if(!empty($archer->divisions))
                            @foreach(array_filter($archer->divisions, fn($d) => $d !== $archer->division) as $div)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full mt-0.5 inline-block"
                                      style="background:rgba(245,158,11,0.07); color:#92400e; border: 1px solid rgba(245,158,11,0.2);">
                                    {{ $div }}
                                </span>
                            @endforeach
                        @endif
                        @if(!$archer->division && empty($archer->divisions))
                            <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>

                    {{-- Club --}}
                    <td class="px-4 py-3.5 text-sm text-slate-600 hidden md:table-cell">
                        {{ $archer->club?->name ?? '—' }}
                    </td>

                    {{-- State --}}
                    <td class="px-4 py-3.5 text-sm text-slate-600 hidden lg:table-cell">
                        {{ $archer->state ?? '—' }}
                    </td>

                    {{-- Para-Archery --}}
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        @if($archer->para_archery)
                            <span class="inline-block text-xs font-bold px-2.5 py-1 rounded-full"
                                  style="background:#f3e8ff; color:#7e22ce; border:1px solid #e9d5ff;">
                                PARA
                            </span>
                            @if($archer->wheelchair)
                                <span class="inline-block text-xs font-bold px-2 py-1 rounded-full ml-1"
                                      style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca;">
                                    W/C
                                </span>
                            @endif
                        @else
                            <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5 pr-5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('archers.show', $archer) }}"
                               class="inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                               style="background:#0f172a; color:#ffffff;">
                                View
                            </a>
                            @if(auth()->user()->isClubAdmin())
                                <a href="{{ route('archers.edit', $archer) }}"
                                   class="inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                                   style="background:#f59e0b; color:#0f172a;">
                                    Edit
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('archers.destroy', $archer) }}"
                                      x-data
                                      @submit.prevent="if(confirm('Delete {{ $archer->ref_no }}?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center text-xs font-bold text-red-600 hover:text-white bg-red-50 hover:bg-red-600 border border-red-200 hover:border-red-600 px-3 py-1.5 rounded-lg transition-all">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-600">No archers found</p>
                            @if($hasFilters)
                                <a href="{{ route('archers.index') }}"
                                   class="text-sm font-bold text-indigo-600 hover:underline">
                                    Clear filters
                                </a>
                            @elseif(auth()->user()->isClubAdmin())
                                <a href="{{ route('archers.create') }}"
                                   class="text-sm font-bold px-4 py-2 rounded-xl transition-colors"
                                   style="background:#f59e0b; color:#0f172a;">
                                    Add your first archer
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $archers->links() }}</div>
@endsection
