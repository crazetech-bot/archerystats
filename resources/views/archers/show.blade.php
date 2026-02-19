@extends('layouts.app')

@section('title', $archer->full_name . ' — Profile')
@section('header', 'Archer Profile')
@section('subheader', $archer->ref_no)

@section('header-actions')
    <a href="{{ route('archers.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
    <a href="{{ route('sessions.create', $archer) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow-md transition-all hover:opacity-90"
       style="background: linear-gradient(135deg, #059669, #10b981);">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        New Session
    </a>
    @if(auth()->user()->isClubAdmin())
        <a href="{{ route('archers.edit', $archer) }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow-md transition-all hover:opacity-90"
           style="background: linear-gradient(135deg, #d97706, #f59e0b);">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
            Edit
        </a>
    @endif
    @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('archers.destroy', $archer) }}"
              x-data @submit.prevent="if(confirm('Permanently delete {{ $archer->ref_no }}?')) $el.submit()">
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
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Photo + Identity --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Profile card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-24 w-full" style="background: linear-gradient(135deg, #4338ca, #6366f1);"></div>
                <div class="px-5 pb-5">
                    <div class="-mt-12 mb-4">
                        <img src="{{ $archer->photo_url }}"
                             alt="{{ $archer->full_name }}"
                             class="h-24 w-24 rounded-2xl object-cover bg-gray-100 border-4 border-white shadow-lg">
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ $archer->full_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $archer->user->email }}</p>

                    <div class="mt-3 flex flex-wrap gap-1.5">
                        <span class="text-xs font-mono font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-lg">
                            {{ $archer->ref_no ?? 'PENDING' }}
                        </span>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-lg
                                     {{ $archer->active ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-gray-500 bg-gray-50 border border-gray-200' }}">
                            {{ $archer->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Divisions + Classification --}}
            @if(!empty($archer->divisions) || $archer->classification)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
                @if(!empty($archer->divisions))
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2.5">Division(s)</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($archer->divisions as $div)
                            <span class="text-sm font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl">
                                {{ $div }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($archer->classification)
                    @if(!empty($archer->divisions))<div class="border-t border-gray-100"></div>@endif
                    @php
                        $clsCfg = ['U12' => ['bg-sky-50','border-sky-200','text-sky-700','Under 12'], 'U15' => ['bg-violet-50','border-violet-200','text-violet-700','Under 15'], 'U18' => ['bg-rose-50','border-rose-200','text-rose-700','Under 18'], 'Open' => ['bg-emerald-50','border-emerald-200','text-emerald-700','Open Class']];
                        [$clsBg, $clsBorder, $clsText, $clsLabel] = $clsCfg[$archer->classification] ?? ['bg-gray-50','border-gray-200','text-gray-700',$archer->classification];
                    @endphp
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2.5">Classification</p>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border text-sm font-bold {{ $clsBg }} {{ $clsBorder }} {{ $clsText }}">
                            {{ $archer->classification }}
                            <span class="font-normal text-xs opacity-75">{{ $clsLabel }}</span>
                        </span>
                    </div>
                @endif
            </div>
            @endif

        </div>

        {{-- Right: Details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Personal info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </span>
                    Personal Information
                </h3>
                <dl class="grid grid-cols-2 gap-4">
                    @php
                        $items = [
                            'Date of Birth'    => $archer->date_of_birth?->format('d-m-Y') ?? '—',
                            'Age'              => $archer->age ? $archer->age . ' years old' : '—',
                            'Gender'           => $archer->gender ? ucfirst($archer->gender) : '—',
                            'Contact Number'   => $archer->phone ?? '—',
                            'Club'             => $archer->club?->name ?? '—',
                            'State / National' => $archer->team ?? '—',
                        ];
                    @endphp
                    @foreach($items as $label => $value)
                        <div class="bg-gray-50 rounded-xl px-4 py-3">
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</dt>
                            <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Location --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </span>
                    Location
                </h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl px-4 py-3 col-span-2">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Address</dt>
                        <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $archer->address_line ?? '—' }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Postcode</dt>
                        <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $archer->postcode ?? '—' }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">State</dt>
                        <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $archer->state ?? '—' }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Country</dt>
                        <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $archer->country ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Equipment --}}
            @if($archer->arrow_type || $archer->arrow_size || $archer->arrow_length || $archer->limb_type || $archer->limb_length || $archer->limb_poundage || $archer->actual_poundage)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-orange-100 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l5.654-4.654m5.896-2.572c.083-.283.27-.576.604-.818L21 8.25l-4.5-4.5-2.053 2.053c-.242.334-.535.52-.818.604m-5.585 5.585L3 21"/>
                        </svg>
                    </span>
                    Equipment
                </h3>
                <dl class="grid grid-cols-2 gap-4">
                    @php
                        $equipment = [
                            'Arrow Type'       => $archer->arrow_type     ?? '—',
                            'Arrow Size'       => $archer->arrow_size     ?? '—',
                            'Arrow Length'     => $archer->arrow_length   ? $archer->arrow_length . '"' : '—',
                            'Limb Type'        => $archer->limb_type      ?? '—',
                            'Limb Length'      => $archer->limb_length    ? $archer->limb_length . '"' : '—',
                            'Limb Poundage'    => $archer->limb_poundage  ? $archer->limb_poundage . ' lbs' : '—',
                            'Actual Poundage'  => $archer->actual_poundage ? $archer->actual_poundage . ' lbs' : '—',
                        ];
                    @endphp
                    @foreach($equipment as $label => $value)
                        <div class="bg-gray-50 rounded-xl px-4 py-3">
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</dt>
                            <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
            @endif

            {{-- Personal Best --}}
            @php
                $hasUnofficialPB = $archer->pb_unofficial_36_score || $archer->pb_unofficial_72_score;
                $hasOfficialPB   = $archer->pb_official_36_score   || $archer->pb_official_72_score;
            @endphp
            @if($hasUnofficialPB || $hasOfficialPB)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2"
                     style="background: linear-gradient(135deg, #f8faff, #f0f4ff);">
                    <span class="h-6 w-6 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                        </svg>
                    </span>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Personal Best</h3>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Unofficial --}}
                    @if($hasUnofficialPB)
                    <div>
                        <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/></svg>
                            Unofficial (Training)
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([['36', $archer->pb_unofficial_36_score, $archer->pb_unofficial_36_date, null], ['72', $archer->pb_unofficial_72_score, $archer->pb_unofficial_72_date, null]] as [$arrows, $score, $date, $tournament])
                                @if($score)
                                <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex h-6 w-6 rounded-full bg-sky-200 text-sky-800 text-xs font-black items-center justify-center">{{ $arrows }}</span>
                                        <span class="text-xs text-sky-500 font-medium">arrows</span>
                                    </div>
                                    <p class="text-3xl font-black text-sky-700 leading-none">{{ $score }}</p>
                                    @if($date)<p class="text-xs text-sky-500 mt-1.5">{{ $date->format('d M Y') }}</p>@endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($hasUnofficialPB && $hasOfficialPB)
                        <div class="border-t border-gray-100"></div>
                    @endif

                    {{-- Official --}}
                    @if($hasOfficialPB)
                    <div>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Official
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([['36', $archer->pb_official_36_score, $archer->pb_official_36_date, $archer->pb_official_36_tournament], ['72', $archer->pb_official_72_score, $archer->pb_official_72_date, $archer->pb_official_72_tournament]] as [$arrows, $score, $date, $tournament])
                                @if($score)
                                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex h-6 w-6 rounded-full bg-emerald-200 text-emerald-800 text-xs font-black items-center justify-center">{{ $arrows }}</span>
                                        <span class="text-xs text-emerald-500 font-medium">arrows</span>
                                    </div>
                                    <p class="text-3xl font-black text-emerald-700 leading-none">{{ $score }}</p>
                                    @if($date)<p class="text-xs text-emerald-500 mt-1.5">{{ $date->format('d M Y') }}</p>@endif
                                    @if($tournament)<p class="text-xs text-emerald-600 font-semibold mt-1 truncate" title="{{ $tournament }}">{{ $tournament }}</p>@endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif

            {{-- Notes --}}
            @if($archer->notes)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Notes</h3>
                <p class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed">{{ $archer->notes }}</p>
            </div>
            @endif

            {{-- Recent Sessions --}}
            @php
                $recentSessions = $archer->sessions()->with(['roundType','score'])->orderByDesc('date')->take(3)->get();
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-100"
                     style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                    <div class="flex items-center gap-2">
                        <span class="h-6 w-6 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                            </svg>
                        </span>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Recent Sessions</h3>
                    </div>
                    <a href="{{ route('sessions.index', $archer) }}"
                       class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
                        View all →
                    </a>
                </div>

                @if($recentSessions->isEmpty())
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-gray-400">No sessions recorded yet.</p>
                        <a href="{{ route('sessions.create', $archer) }}"
                           class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:underline">
                            + Start a session
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($recentSessions as $s)
                            <a href="{{ route('sessions.scorecard', $s) }}"
                               class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                                <div class="flex-shrink-0 w-14 text-center">
                                    @if($s->score?->total_score > 0)
                                        <p class="text-xl font-black text-indigo-700">{{ $s->score->total_score }}</p>
                                    @else
                                        <p class="text-lg font-black text-gray-300">—</p>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $s->roundType->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $s->date->format('d M Y') }}</p>
                                </div>
                                @if($s->is_competition)
                                    <span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-lg flex-shrink-0">
                                        Competition
                                    </span>
                                @endif
                                <svg class="h-4 w-4 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                        <a href="{{ route('sessions.create', $archer) }}"
                           class="flex items-center justify-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-900 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            New Session
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
