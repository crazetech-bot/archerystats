@extends('layouts.app')

@section('title', 'Coaches')
@section('header', 'Coaches')
@section('subheader', 'Manage registered coaches')

@section('header-actions')
    @if(auth()->user()->isClubAdmin())
        <a href="{{ route('coaches.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-black tracking-wide shadow-md
                  transition-all active:scale-95"
           style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            ADD COACH
        </a>
    @endif
@endsection

@section('content')

{{-- Stats bar --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @php
        $total  = $coaches->total();
        $male   = \App\Models\Coach::whereHas('user')->where('gender', 'male')->count();
        $female = \App\Models\Coach::whereHas('user')->where('gender', 'female')->count();
    @endphp
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #f59e0b;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total</p>
        <p class="text-4xl font-black text-slate-900 mt-1" style="font-family:'Barlow',sans-serif;">{{ $total }}</p>
        <p class="text-xs text-slate-500 mt-1">Matching coaches</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #3b82f6;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Male</p>
        <p class="text-4xl font-black text-blue-600 mt-1" style="font-family:'Barlow',sans-serif;">{{ $male }}</p>
        <p class="text-xs text-slate-500 mt-1">Male coaches</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #ec4899;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Female</p>
        <p class="text-4xl font-black text-pink-500 mt-1" style="font-family:'Barlow',sans-serif;">{{ $female }}</p>
        <p class="text-xs text-slate-500 mt-1">Female coaches</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #64748b;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Showing</p>
        <p class="text-4xl font-black text-slate-700 mt-1" style="font-family:'Barlow',sans-serif;">{{ $coaches->count() }}</p>
        <p class="text-xs text-slate-500 mt-1">On this page</p>
    </div>
</div>

{{-- Filter bar --}}
<form method="GET" action="{{ route('coaches.index') }}"
      class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 mb-5">
    <div class="flex flex-wrap gap-3 items-end">

        {{-- Search --}}
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Search</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Name, email or ref no…"
                       class="block w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-sm
                              focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
            </div>
        </div>

        {{-- Club --}}
        <div class="w-44">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Club</label>
            <select name="club_id"
                    class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-3
                           focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                <option value="">All Clubs</option>
                @foreach($clubs as $club)
                    <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                        {{ $club->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- State --}}
        <div class="w-44">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">State</label>
            <select name="state"
                    class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-3
                           focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state }}" {{ request('state') === $state ? 'selected' : '' }}>
                        {{ $state }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- National Team --}}
        <div class="w-44">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">National Team</label>
            <select name="national_team"
                    class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-3
                           focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
                <option value="">All</option>
                <option value="1" {{ request('national_team') === '1' ? 'selected' : '' }}>National Team Coach</option>
                <option value="0" {{ request('national_team') === '0' ? 'selected' : '' }}>Non-National Team</option>
            </select>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-2 flex-shrink-0">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-all hover:opacity-90"
                    style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                Filter
            </button>
            @if(request()->hasAny(['search','club_id','state','national_team']))
            <a href="{{ route('coaches.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                Reset
            </a>
            @endif
        </div>

    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
    <div class="overflow-x-auto">
    <table class="min-w-full">
        <thead>
            <tr style="background: #0f172a;">
                <th class="w-12 py-3.5 pl-5 pr-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest"></th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Ref No</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Name</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Gender</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Club</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">State</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden md:table-cell">National Team</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden lg:table-cell">Coaching Level</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden lg:table-cell">Sports Science</th>
                <th class="px-4 py-3.5 text-right pr-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($coaches as $coach)
                <tr class="transition-colors hover:bg-amber-50/40 group">
                    <td class="py-3.5 pl-5 pr-3">
                        <img src="{{ $coach->photo_url }}" alt="{{ $coach->full_name }}"
                             class="h-10 w-10 rounded-full object-cover bg-slate-100 ring-2 ring-white shadow-sm">
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-block text-xs font-mono font-bold px-2.5 py-1 rounded-lg"
                              style="background:#0f172a; color:#f59e0b;">
                            {{ $coach->ref_no ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('coaches.show', $coach) }}"
                           class="text-sm font-bold text-slate-900 hover:text-amber-600 transition-colors">
                            {{ $coach->full_name }}
                        </a>
                        <p class="text-xs text-slate-400">{{ $coach->user->email }}</p>
                    </td>
                    <td class="px-4 py-3.5">
                        @if($coach->gender === 'male')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-full">♂ Male</span>
                        @elseif($coach->gender === 'female')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-pink-700 bg-pink-50 border border-pink-200 px-2.5 py-1 rounded-full">♀ Female</span>
                        @else
                            <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-sm text-slate-600 hidden sm:table-cell">
                        {{ $coach->club?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-slate-600 hidden md:table-cell">
                        {{ $coach->state ?: '—' }}
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        @if($coach->national_team)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                                  style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">
                                ★ National Team
                            </span>
                        @else
                            <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        @if($coach->coaching_level && $coach->coaching_level !== 'None')
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                  style="background:rgba(13,148,136,0.10); color:#065f46; border:1px solid rgba(13,148,136,0.25);">
                                {{ $coach->coaching_level }}
                            </span>
                        @else
                            <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        @if($coach->sports_science_course)
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                  style="background:rgba(99,102,241,0.10); color:#3730a3; border:1px solid rgba(99,102,241,0.25);">
                                {{ $coach->sports_science_course }}
                            </span>
                        @else
                            <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 pr-5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('coaches.show', $coach) }}"
                               class="inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                               style="background:#0f172a; color:#ffffff;">
                                View
                            </a>
                            @if(auth()->user()->isClubAdmin() || auth()->user()->role === 'national_team')
                                <a href="{{ route('coaches.edit', $coach) }}"
                                   class="inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                                   style="background:#f59e0b; color:#0f172a;">
                                    Edit
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('coaches.destroy', $coach) }}"
                                      x-data @submit.prevent="if(confirm('Delete {{ $coach->ref_no }}?')) $el.submit()">
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
                    <td colspan="10" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-600">No coaches found</p>
                            @if(request()->hasAny(['search','club_id','state','national_team']))
                                <a href="{{ route('coaches.index') }}"
                                   class="text-sm font-semibold text-teal-600 hover:text-teal-800">
                                    Clear filters
                                </a>
                            @elseif(auth()->user()->isClubAdmin())
                                <a href="{{ route('coaches.create') }}"
                                   class="text-sm font-bold px-4 py-2 rounded-xl"
                                   style="background:#f59e0b; color:#0f172a;">
                                    Add your first coach
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<div class="mt-5">{{ $coaches->links() }}</div>
@endsection
