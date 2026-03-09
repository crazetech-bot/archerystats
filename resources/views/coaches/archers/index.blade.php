@extends('layouts.app')

@section('title', 'Assigned Archers — ' . $coach->full_name)
@section('header', 'Assigned Archers')
@section('subheader', $coach->ref_no . ' · ' . $coach->full_name)

@section('header-actions')
    <a href="{{ route('coaches.show', $coach) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Coach
    </a>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-5">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="rounded-xl px-5 py-3 text-sm font-medium text-emerald-800 bg-emerald-50 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl px-5 py-3 text-sm font-medium text-red-800 bg-red-50 border border-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Assign New Archer --}}
    @if($available->isNotEmpty() && (auth()->user()->isClubAdmin() || auth()->user()->role === 'coach' || auth()->user()->role === 'national_team'))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1 flex items-center gap-2">
            <span class="h-6 w-6 rounded-lg bg-teal-100 flex items-center justify-center">
                <svg class="h-3.5 w-3.5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
            </span>
            Assign Archer
        </h3>
        <p class="text-xs text-gray-400 mb-4 ml-8">
            @if($isNationalTeamContext)
                Only archers with a national team status (Podium, Pelapis Kebangsaan, PARA) are shown.
            @endif
            Selected archer will be assigned immediately.
        </p>
        <form method="POST" action="{{ route('coaches.archers.store', $coach) }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <label for="archer_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Select Archer</label>
                <select id="archer_id" name="archer_id"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                               focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition
                               @error('archer_id') border-red-400 bg-red-50 @enderror">
                    <option value="">— Choose archer —</option>
                    @foreach($available as $archer)
                        <option value="{{ $archer->id }}">
                            {{ $archer->ref_no }} — {{ $archer->full_name }}
                            {{ $archer->club ? '(' . $archer->club->name . ')' : '(No Club)' }}
                            @if($isNationalTeamContext)
                                [{{ $archer->national_team }}]
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('archer_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:opacity-90 flex-shrink-0"
                    style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                Assign
            </button>
        </form>
    </div>
    @endif

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('coaches.archers.index', $coach) }}"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4">
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
                           placeholder="Name, MAREOS ID or ref no…"
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
            <div class="w-40">
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
                    @foreach($nationalTeamOptions as $opt)
                        <option value="{{ $opt }}" {{ request('national_team') === $opt ? 'selected' : '' }}>
                            {{ $opt }}
                        </option>
                    @endforeach
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
                <a href="{{ route('coaches.archers.index', $coach) }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Reset
                </a>
                @endif
            </div>

        </div>
    </form>

    {{-- Assigned Archers Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f0fdfa, #ccfbf1);">
            <div class="flex items-center gap-3">
                <span class="h-8 w-8 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.75 3.75 0 11-6.75 0 3.75 3.75 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Assigned Archers</h2>
                    <p class="text-xs text-gray-500">
                        {{ $assignedArchers->count() }} shown
                        @if($totalAssigned !== $assignedArchers->count())
                            of {{ $totalAssigned }} total
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if($assignedArchers->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.75 3.75 0 11-6.75 0 3.75 3.75 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                @if(request()->hasAny(['search','club_id','state','national_team']))
                    <p class="text-sm font-medium text-gray-500">No archers match your filters</p>
                    <a href="{{ route('coaches.archers.index', $coach) }}"
                       class="mt-2 inline-block text-sm font-semibold text-teal-600 hover:text-teal-800">Clear filters</a>
                @else
                    <p class="text-sm font-medium text-gray-500">No archers assigned yet</p>
                    <p class="text-xs text-gray-400 mt-1">Use the form above to assign archers to this coach.</p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="py-3 pl-5 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12"></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">MAREOS ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Age</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Division</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Club</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">State</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">National Team</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Para Archery</th>
                        <th class="px-4 py-3 pr-5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($assignedArchers as $archer)
                    <tr class="hover:bg-teal-50/30 transition-colors">
                        <td class="py-3 pl-5 pr-3">
                            <img src="{{ $archer->photo_url }}" alt="{{ $archer->full_name }}"
                                 class="h-9 w-9 rounded-full object-cover bg-gray-100 ring-2 ring-white shadow-sm flex-shrink-0">
                        </td>
                        <td class="px-4 py-3">
                            @if($archer->mareos_id)
                                <span class="font-mono text-xs font-bold text-teal-700 bg-teal-50 px-2 py-1 rounded-lg">
                                    {{ $archer->mareos_id }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('archers.show', $archer) }}"
                               class="font-semibold text-gray-900 hover:text-teal-700 transition-colors">
                                {{ $archer->full_name }}
                            </a>
                            <p class="text-xs text-gray-400">{{ $archer->user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($archer->gender === 'male')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full">♂ M</span>
                            @elseif($archer->gender === 'female')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-pink-700 bg-pink-50 border border-pink-200 px-2 py-0.5 rounded-full">♀ F</span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if($archer->age !== null)
                                <span class="text-xs font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded-lg">
                                    {{ $archer->age }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if(!empty($archer->divisions))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($archer->divisions as $div)
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                              style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">
                                            {{ $div }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 hidden md:table-cell">
                            {{ $archer->club?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 hidden md:table-cell">
                            {{ $archer->state ?: '—' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($archer->national_team && $archer->national_team !== 'No')
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap"
                                      style="background:rgba(67,56,202,0.10); color:#312e81; border:1px solid rgba(67,56,202,0.25);">
                                    {{ $archer->national_team }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($archer->para_archery)
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                      style="background:rgba(124,58,237,0.10); color:#4c1d95; border:1px solid rgba(124,58,237,0.25);">
                                    PARA
                                </span>
                                @if($archer->wheelchair)
                                    <span class="ml-1 text-xs font-semibold px-2 py-0.5 rounded-full"
                                          style="background:rgba(239,68,68,0.10); color:#991b1b; border:1px solid rgba(239,68,68,0.25);">
                                        W/C
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 pr-5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('archers.show', $archer) }}"
                                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                    Profile
                                </a>
                                <a href="{{ route('archers.performance', $archer) }}"
                                   class="text-xs font-medium text-teal-600 hover:text-teal-800 px-2.5 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 transition-colors">
                                    Analytics
                                </a>
                                <a href="{{ route('sessions.index', $archer) }}"
                                   class="text-xs font-medium text-amber-600 hover:text-amber-800 px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 transition-colors">
                                    Sessions
                                </a>
                                @if(auth()->user()->isClubAdmin())
                                <form method="POST" action="{{ route('coaches.archers.destroy', [$coach, $archer]) }}"
                                      x-data @submit.prevent="if(confirm('Remove {{ $archer->ref_no }} from roster?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-medium text-red-600 hover:text-red-800 px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 transition-colors">
                                        Remove
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
        @endif
    </div>

</div>
@endsection
