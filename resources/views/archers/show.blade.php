@extends('layouts.app')

@section('title', $archer->full_name . ' — Profile')
@section('og_image', $archer->photo ? asset('storage/' . $archer->photo) : '')
@section('og_description', $archer->full_name . ' · ' . implode(', ', $archer->divisions ?? []) . ($archer->club ? ' · ' . $archer->club->name : '') . ' · Archery Stats')
@section('header', 'Archer Profile')
@section('subheader', $archer->ref_no)

@section('header-actions')
    @if(auth()->user()->role !== 'archer')
    <a href="{{ route('archers.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
    @endif
    <a href="{{ route('sessions.create', $archer) }}"
       class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
       style="background:#10b981; color:#fff; font-family:'Barlow',sans-serif;">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        NEW SESSION
    </a>
    @if(auth()->user()->isClubAdmin() || (auth()->user()->role === 'archer' && auth()->user()->archer?->id === $archer->id))
        <a href="{{ route('archers.edit', $archer) }}"
           class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
           style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
            EDIT
        </a>
    @endif
    @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('archers.destroy', $archer) }}"
              x-data @submit.prevent="if(confirm('Permanently delete {{ $archer->ref_no }}?')) $el.submit()">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
                    style="background:#dc2626; color:#fff; font-family:'Barlow',sans-serif;">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
                DELETE
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Flash messages --}}
    @if(session('info'))
        <div class="mb-4 rounded-xl px-5 py-3 text-sm font-medium text-blue-800 bg-blue-50 border border-blue-200">
            {{ session('info') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-4 rounded-xl px-5 py-3 text-sm font-medium text-amber-800 bg-amber-50 border border-amber-200">
            {{ session('warning') }}
        </div>
    @endif


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Photo + Identity --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Profile card --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="h-28 w-full" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    {{-- Gold target watermark --}}
                    <div class="h-full flex items-center justify-end pr-6 opacity-10">
                        <svg viewBox="0 0 80 80" fill="none" class="h-20 w-20">
                            <circle cx="40" cy="40" r="38" stroke="#f59e0b" stroke-width="3"/>
                            <circle cx="40" cy="40" r="26" stroke="#f59e0b" stroke-width="3"/>
                            <circle cx="40" cy="40" r="14" stroke="#f59e0b" stroke-width="3"/>
                            <circle cx="40" cy="40" r="5"  fill="#f59e0b"/>
                        </svg>
                    </div>
                </div>
                <div class="px-5 pb-5">
                    <div class="-mt-12 mb-4">
                        <img src="{{ $archer->photo_url }}"
                             alt="{{ $archer->full_name }}"
                             class="h-24 w-24 rounded-2xl object-cover bg-slate-100 shadow-lg"
                             style="border: 4px solid #fff;">
                    </div>
                    <h2 class="text-xl font-black text-slate-900 leading-tight" style="font-family:'Barlow',sans-serif;">{{ $archer->full_name }}</h2>
                    <p class="text-sm text-slate-500">{{ $archer->user->email }}</p>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg"
                              style="background:#0f172a; color:#f59e0b;">
                            {{ $archer->ref_no ?? 'PENDING' }}
                        </span>
                        @php
                            $statusCfg = [
                                'active'           => ['text-emerald-700 bg-emerald-50 border-emerald-200', 'Active'],
                                'no_longer_active' => ['text-slate-500 bg-slate-50 border-slate-200', 'No Longer Active'],
                                'injury'           => ['text-red-700 bg-red-50 border-red-200', 'Injury'],
                            ];
                            $st = $archer->status ?? 'active';
                            [$stClass, $stLabel] = $statusCfg[$st] ?? ['text-slate-500 bg-slate-50 border-slate-200', ucfirst($st)];
                        @endphp
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg border {{ $stClass }}">
                            {{ $stLabel }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Division + Classification --}}
            @if($archer->division || !empty($archer->divisions) || $archer->classification)
            <div class="bg-white rounded-2xl shadow-sm p-5 space-y-4" style="border: 1px solid #e2e8f0;">
                @if($archer->division)
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2.5">Division</p>
                    <span class="text-sm font-bold px-3 py-1.5 rounded-xl"
                          style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">
                        {{ $archer->division }}
                    </span>
                </div>
                @elseif(!empty($archer->divisions))
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2.5">Division(s)</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($archer->divisions as $div)
                            <span class="text-sm font-bold px-3 py-1.5 rounded-xl"
                                  style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">
                                {{ $div }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($archer->classification)
                    @if($archer->division || !empty($archer->divisions))<div class="border-t border-slate-100"></div>@endif
                    @php
                        $clsCfg = ['U12' => ['bg-sky-50','border-sky-200','text-sky-700','Under 12'], 'U15' => ['bg-violet-50','border-violet-200','text-violet-700','Under 15'], 'U18' => ['bg-rose-50','border-rose-200','text-rose-700','Under 18'], 'Open' => ['bg-emerald-50','border-emerald-200','text-emerald-700','Open Class']];
                        [$clsBg, $clsBorder, $clsText, $clsLabel] = $clsCfg[$archer->classification] ?? ['bg-slate-50','border-slate-200','text-slate-700',$archer->classification];
                    @endphp
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2.5">Classification</p>
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

            {{-- Personal Information --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #f59e0b;">
                    <svg class="h-5 w-5 flex-shrink-0" style="color:#f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Personal Information</h3>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-2 gap-3">
                        @php
                            $piItems = [
                                'MAREOS ID'   => $archer->mareos_id ?? '—',
                                'WAREOS ID'   => $archer->wareos_id ?? '—',
                                'Division'    => $archer->division ?? '—',
                                'Club'        => $archer->club?->name ?? '—',
                                'State Team'  => $archer->stateTeam?->name ?? ($archer->state_team ?? '—'),
                            ];
                        @endphp
                        @foreach($piItems as $label => $value)
                            <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</dt>
                                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</dd>
                            </div>
                        @endforeach
                        {{-- National Team --}}
                        <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">National Team</dt>
                            @php $nt = $archer->national_team ?? 'No'; @endphp
                            @if($nt && $nt !== 'No')
                                <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(99,102,241,0.12); color:#3730a3; border:1px solid rgba(99,102,241,0.25);">
                                    {{ $nt }}
                                </span>
                            @else
                                <dd class="text-sm font-semibold text-slate-800">{{ $nt }}</dd>
                            @endif
                            @if(in_array(auth()->user()->role, ['super_admin', 'national_team']))
                                <form method="POST" action="{{ route('archers.national-team.update', $archer) }}"
                                      class="mt-2 flex gap-2">
                                    @csrf @method('PATCH')
                                    <select name="national_team"
                                            class="flex-1 rounded-lg border border-gray-300 bg-white text-sm py-1.5 px-3
                                                   focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                        @foreach(\App\Models\Archer::NATIONAL_TEAM_OPTIONS as $opt)
                                            <option value="{{ $opt }}" @selected($archer->national_team === $opt)>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white"
                                            style="background:linear-gradient(135deg,#4338ca,#6366f1)">Save</button>
                                </form>
                            @endif
                        </div>
                        {{-- Para-Archery --}}
                        <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Para-Archery</dt>
                            @if($archer->para_archery)
                                <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">
                                    Yes
                                </span>
                            @else
                                <dd class="text-sm font-semibold text-slate-800">No</dd>
                            @endif
                        </div>
                        {{-- Wheelchair (only when para_archery) --}}
                        @if($archer->para_archery)
                        <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Wheelchair</dt>
                            @if($archer->wheelchair === true)
                                <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-lg"
                                      style="background:rgba(99,102,241,0.12); color:#3730a3; border:1px solid rgba(99,102,241,0.3);">
                                    Yes
                                </span>
                            @elseif($archer->wheelchair === false)
                                <dd class="text-sm font-semibold text-slate-800">No</dd>
                            @else
                                <dd class="text-sm text-slate-400">—</dd>
                            @endif
                        </div>
                        @endif
                    </dl>

                    {{-- Injury Details --}}
                    @if(($archer->status ?? 'active') === 'injury')
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4">
                        <p class="text-xs font-bold text-red-700 uppercase tracking-widest mb-3">Injury Details</p>
                        <dl class="grid grid-cols-3 gap-3">
                            <div class="rounded-xl px-4 py-3 bg-white border border-red-100">
                                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">Date of Injury</dt>
                                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $archer->injury_date?->format('d-m-Y') ?? '—' }}</dd>
                            </div>
                            <div class="rounded-xl px-4 py-3 bg-white border border-red-100">
                                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">Type of Injury</dt>
                                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $archer->injury_type ?? '—' }}</dd>
                            </div>
                            <div class="rounded-xl px-4 py-3 bg-white border border-red-100">
                                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">Expected Return</dt>
                                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $archer->injury_return_date?->format('d-m-Y') ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 1.1 Athlete Profile --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #10b981;">
                    <svg class="h-5 w-5 flex-shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">1.1 &nbsp; Athlete Profile</h3>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-2 gap-3">
                        @php
                            $apTop = [
                                'Date of Birth'   => $archer->date_of_birth?->format('d-m-Y') ?? '—',
                                'Age'             => $archer->age ? $archer->age . ' years old' : '—',
                                'NRIC'            => $archer->nric ?? '—',
                                'Place of Birth'  => $archer->place_of_birth ?? '—',
                                'Gender'          => $archer->gender ? ucfirst($archer->gender) : '—',
                                'Contact'         => $archer->phone ?? '—',
                                'Passport No.'    => $archer->passport_number ?? '—',
                                'Passport Expiry' => $archer->passport_expiry_date?->format('d-m-Y') ?? '—',
                            ];
                            $apBottom = [
                                'State of Residence' => $archer->state ?? '—',
                                'Country'            => $archer->country ?? '—',
                            ];
                        @endphp
                        @foreach($apTop as $label => $value)
                            <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</dt>
                                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</dd>
                            </div>
                        @endforeach
                        <div class="col-span-2 rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">Home Address</dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $archer->address_line ?? '—' }}</dd>
                        </div>
                        @foreach($apBottom as $label => $value)
                            <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</dt>
                                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>

            {{-- 1.2 Next of Kin --}}
            @if($archer->next_of_kin_name)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #f97316;">
                    <svg class="h-5 w-5 flex-shrink-0 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">1.2 &nbsp; Next of Kin</h3>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-2 gap-3">
                        @foreach([
                            'Name'         => $archer->next_of_kin_name ?? '—',
                            'Relationship' => $archer->next_of_kin_relationship ?? '—',
                            'Email'        => $archer->next_of_kin_email ?? '—',
                            'Contact'      => $archer->next_of_kin_phone ?? '—',
                        ] as $label => $value)
                        <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>
            @endif

            {{-- 1.3 Education Background --}}
            @if($archer->school)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #0ea5e9;">
                    <svg class="h-5 w-5 flex-shrink-0 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">1.3 &nbsp; Education Background</h3>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-2 gap-3">
                        <div class="col-span-2 rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">School / Institution</dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $archer->school }}</dd>
                        </div>
                        @if($archer->school_address)
                        <div class="col-span-2 rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">School Address</dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $archer->school_address }}</dd>
                        </div>
                        @endif
                        @foreach(['Postcode' => $archer->school_postcode ?? '—', 'State' => $archer->school_state ?? '—'] as $label => $value)
                        <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>
            @endif

            {{-- 1.4 Equipment --}}
            @if($archer->arrow_type || $archer->arrow_size || $archer->arrow_length || $archer->limb_type || $archer->limb_length || $archer->limb_poundage || $archer->actual_poundage)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #f97316;">
                    <svg class="h-5 w-5 flex-shrink-0 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l5.654-4.654m5.896-2.572c.083-.283.27-.576.604-.818L21 8.25l-4.5-4.5-2.053 2.053c-.242.334-.535.52-.818.604m-5.585 5.585L3 21"/>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">1.4 Equipment</h3>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-2 gap-3">
                        @php
                            $equipment = [
                                'Arrow Type'      => $archer->arrow_type     ?? '—',
                                'Arrow Size'      => $archer->arrow_size     ?? '—',
                                'Arrow Length'    => $archer->arrow_length   ? $archer->arrow_length . '"' : '—',
                                'Limb Type'       => $archer->limb_type      ?? '—',
                                'Limb Length'     => $archer->limb_length    ? $archer->limb_length . '"' : '—',
                                'Limb Poundage'   => $archer->limb_poundage  ? $archer->limb_poundage . ' lbs' : '—',
                                'Actual Poundage' => $archer->actual_poundage ? $archer->actual_poundage . ' lbs' : '—',
                            ];
                        @endphp
                        @foreach($equipment as $label => $value)
                            <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</dt>
                                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
            @endif

            {{-- Personal Best --}}
            @php
                $hasUnofficialPB = $archer->pb_unofficial_36_score || $archer->pb_unofficial_72_score;
                $hasOfficialPB   = $archer->pb_official_36_score   || $archer->pb_official_72_score;
            @endphp
            @if($hasUnofficialPB || $hasOfficialPB)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #f59e0b;">
                    <svg class="h-5 w-5 flex-shrink-0" style="color:#f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Personal Best</h3>
                </div>

                <div class="p-5 space-y-5">

                    @if($hasUnofficialPB)
                    <div>
                        <p class="text-xs font-bold text-sky-600 uppercase tracking-widest mb-3">Unofficial (Training)</p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([['36', $archer->pb_unofficial_36_score, $archer->pb_unofficial_36_date, null], ['72', $archer->pb_unofficial_72_score, $archer->pb_unofficial_72_date, null]] as [$arrows, $score, $date, $tournament])
                                @if($score)
                                <div class="rounded-2xl p-4" style="background:#f0f9ff; border:1px solid #bae6fd;">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex h-6 w-6 rounded-full bg-sky-200 text-sky-800 text-xs font-black items-center justify-center">{{ $arrows }}</span>
                                        <span class="text-xs text-sky-500 font-semibold">arrows</span>
                                    </div>
                                    <p class="text-4xl font-black text-sky-700 leading-none" style="font-family:'Barlow',sans-serif;">{{ $score }}</p>
                                    @if($date)<p class="text-xs text-sky-500 mt-1.5 font-medium">{{ $date->format('d M Y') }}</p>@endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($hasUnofficialPB && $hasOfficialPB)
                        <div class="border-t border-slate-100"></div>
                    @endif

                    @if($hasOfficialPB)
                    <div>
                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mb-3">Official</p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([['36', $archer->pb_official_36_score, $archer->pb_official_36_date, $archer->pb_official_36_tournament], ['72', $archer->pb_official_72_score, $archer->pb_official_72_date, $archer->pb_official_72_tournament]] as [$arrows, $score, $date, $tournament])
                                @if($score)
                                <div class="rounded-2xl p-4" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex h-6 w-6 rounded-full bg-emerald-200 text-emerald-800 text-xs font-black items-center justify-center">{{ $arrows }}</span>
                                        <span class="text-xs text-emerald-500 font-semibold">arrows</span>
                                    </div>
                                    <p class="text-4xl font-black text-emerald-700 leading-none" style="font-family:'Barlow',sans-serif;">{{ $score }}</p>
                                    @if($date)<p class="text-xs text-emerald-500 mt-1.5 font-medium">{{ $date->format('d M Y') }}</p>@endif
                                    @if($tournament)<p class="text-xs text-emerald-600 font-bold mt-1 truncate" title="{{ $tournament }}">{{ $tournament }}</p>@endif
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
            <div class="bg-white rounded-2xl shadow-sm p-6" style="border: 1px solid #e2e8f0;">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3" style="font-family:'Barlow',sans-serif;">Notes</h3>
                <p class="text-sm text-slate-600 whitespace-pre-wrap leading-relaxed">{{ $archer->notes }}</p>
            </div>
            @endif

            {{-- Coach Assignments --}}
            @php
                $pendingAssigned = $archer->sessions()
                    ->where('assigned_by_coach', true)
                    ->whereHas('score', fn($q) => $q->where('total_score', 0))
                    ->with('roundType')
                    ->orderByDesc('date')
                    ->get();
            @endphp
            @if($pendingAssigned->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="flex items-center gap-3 px-5 py-4" style="background:#0f172a; border-bottom:3px solid #6366f1;">
                    <svg class="h-5 w-5 flex-shrink-0" style="color:#818cf8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">
                        Pending Assignments
                    </h3>
                    <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-lg"
                          style="background:rgba(245,158,11,0.2); color:#fbbf24;">
                        {{ $pendingAssigned->count() }} pending
                    </span>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($pendingAssigned as $s)
                    <a href="{{ route('sessions.scorecard', $s) }}"
                       class="flex items-center gap-4 px-5 py-3.5 hover:bg-indigo-50/40 transition-colors">
                        <div class="flex-shrink-0 w-16 text-center">
                            <p class="text-xl font-black text-slate-200">—</p>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $s->roundType->name }}</p>
                            <p class="text-xs text-slate-400">{{ $s->date->format('d M Y') }}</p>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-lg flex-shrink-0"
                              style="background:rgba(99,102,241,0.12); color:#3730a3; border:1px solid rgba(99,102,241,0.25);">
                            Coach Assigned
                        </span>
                        <svg class="h-4 w-4 text-slate-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Training Sessions (Coach-Created) --}}
            @if($trainingSessions->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="flex items-center justify-between gap-3 px-5 py-4" style="background:#0f172a; border-bottom:3px solid #0d9488;">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 flex-shrink-0" style="color:#2dd4bf;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Training Sessions</h3>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-lg" style="background:rgba(13,148,136,0.2); color:#2dd4bf;">
                        {{ $trainingSessions->count() }} session{{ $trainingSessions->count() !== 1 ? 's' : '' }}
                    </span>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($trainingSessions as $ts)
                    <div class="flex items-start gap-4 px-5 py-3.5">
                        {{-- Date --}}
                        <div class="flex-shrink-0 w-14 text-center pt-0.5">
                            <p class="text-lg font-black text-slate-800" style="font-family:'Barlow',sans-serif; line-height:1;">{{ $ts->date->format('d') }}</p>
                            <p class="text-xs font-bold text-slate-400 uppercase">{{ $ts->date->format('M Y') }}</p>
                        </div>
                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-bold text-slate-800">
                                    {{ $ts->coach->full_name ?? '—' }}
                                </p>
                                @if($ts->roundType)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-lg" style="background:#f0fdfa; color:#0f766e; border:1px solid #99f6e4;">
                                        {{ $ts->roundType->name }}
                                    </span>
                                @endif
                                @if($ts->pivot->attended)
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-lg" style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
                                        Attended
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-x-4 mt-1">
                                @if($ts->location)
                                    <p class="text-xs text-slate-400">
                                        <span class="font-semibold text-slate-500">Location:</span> {{ $ts->location }}
                                    </p>
                                @endif
                                @if($ts->focus_area)
                                    <p class="text-xs text-slate-400">
                                        <span class="font-semibold text-slate-500">Focus:</span> {{ $ts->focus_area }}
                                    </p>
                                @endif
                                @if($ts->duration_minutes)
                                    <p class="text-xs text-slate-400">
                                        <span class="font-semibold text-slate-500">Duration:</span> {{ $ts->duration_label }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Sessions --}}
            @php
                $recentSessions = $archer->sessions()->with(['roundType','score'])->orderByDesc('date')->take(3)->get();
            @endphp
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="flex items-center justify-between gap-3 px-5 py-4" style="background:#0f172a; border-bottom:3px solid #10b981;">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                        </svg>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Recent Sessions</h3>
                    </div>
                    <a href="{{ route('sessions.index', $archer) }}"
                       class="text-xs font-bold text-emerald-400 hover:text-emerald-200 transition-colors">
                        View all →
                    </a>
                </div>

                @if($recentSessions->isEmpty())
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-slate-400 font-medium">No sessions recorded yet.</p>
                        <a href="{{ route('sessions.create', $archer) }}"
                           class="mt-3 inline-flex items-center gap-1.5 text-sm font-bold"
                           style="color:#f59e0b;">
                            + Start a session
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($recentSessions as $s)
                            <a href="{{ route('sessions.scorecard', $s) }}"
                               class="flex items-center gap-4 px-5 py-3.5 hover:bg-amber-50/40 transition-colors">
                                <div class="flex-shrink-0 w-16 text-center">
                                    @if($s->score?->total_score > 0)
                                        <p class="text-2xl font-black" style="color:#0f172a; font-family:'Barlow',sans-serif;">{{ $s->score->total_score }}</p>
                                    @else
                                        <p class="text-xl font-black text-slate-200">—</p>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $s->roundType->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $s->date->format('d M Y') }}</p>
                                </div>
                                @if($s->assigned_by_coach)
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-lg flex-shrink-0"
                                          style="background:rgba(99,102,241,0.12); color:#3730a3; border:1px solid rgba(99,102,241,0.25);">
                                        Coach
                                    </span>
                                @elseif($s->is_competition)
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-lg flex-shrink-0"
                                          style="background:rgba(245,158,11,0.12); color:#92400e; border:1px solid rgba(245,158,11,0.3);">
                                        Competition
                                    </span>
                                @endif
                                <svg class="h-4 w-4 text-slate-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-5 py-3" style="border-top:1px solid #f1f5f9; background:#f8fafc;">
                        <a href="{{ route('sessions.create', $archer) }}"
                           class="flex items-center justify-center gap-2 text-sm font-black transition-colors"
                           style="color:#10b981; font-family:'Barlow',sans-serif;">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            NEW SESSION
                        </a>
                    </div>
                @endif
            </div>

        {{-- Personal Achievements --}}
        @php
            $achievements = $archer->achievements()->get();
            $canManageAchievements = auth()->user()->isAdmin()
                || (auth()->user()->role === 'archer' && auth()->user()->archer?->id === $archer->id);
        @endphp
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;"
             x-data="{ adding: false }">
            <div class="flex items-center justify-between gap-3 px-5 py-4" style="background:#0f172a; border-bottom:3px solid #f59e0b;">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 flex-shrink-0" style="color:#f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Personal Achievements</h3>
                </div>
                @if($canManageAchievements)
                <button @click="adding = !adding"
                        class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg transition-all"
                        :style="adding ? 'background:rgba(239,68,68,0.15); color:#fca5a5;' : 'background:rgba(245,158,11,0.15); color:#fbbf24;'">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                         x-show="!adding">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                         x-show="adding">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span x-text="adding ? 'Cancel' : 'Add Achievement'"></span>
                </button>
                @endif
            </div>

            {{-- Add form --}}
            @if($canManageAchievements)
            <div x-show="adding" x-cloak style="border-bottom:1px solid #f1f5f9; background:#fffbeb;">
                <form method="POST" action="{{ route('achievements.store', $archer) }}" class="p-5">
                    @csrf
                    @if(session('achievement_success'))
                        <div class="mb-4 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-2.5">
                            <p class="text-sm text-green-700 font-medium">{{ session('achievement_success') }}</p>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                            @error('date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Achievement <span class="text-red-500">*</span></label>
                            <input type="text" name="achievement" value="{{ old('achievement') }}" required
                                   placeholder="e.g. Gold Medal, 1st Place, National Champion…"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                            @error('achievement')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Team</label>
                            <input type="text" name="team" value="{{ old('team') }}"
                                   placeholder="e.g. Malaysia, Selangor…"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tournament</label>
                            <input type="text" name="tournament" value="{{ old('tournament') }}"
                                   placeholder="e.g. SEA Games 2025, MSN Cup…"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                                class="px-5 py-2 rounded-xl text-sm font-black text-white shadow-sm transition-all active:scale-95"
                                style="background: linear-gradient(135deg,#d97706,#f59e0b); font-family:'Barlow',sans-serif;">
                            SAVE ACHIEVEMENT
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Achievements list --}}
            @if($achievements->isEmpty())
                <div class="px-5 py-8 text-center">
                    <p class="text-sm text-slate-400 font-medium">No achievements recorded yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Date</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Achievement</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Team</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Tournament</th>
                                @if($canManageAchievements)
                                <th class="px-5 py-3"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($achievements as $ach)
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <td class="px-5 py-3.5 text-slate-500 font-medium whitespace-nowrap">
                                    {{ $ach->date->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-slate-800">{{ $ach->achievement }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $ach->team ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $ach->tournament ?: '—' }}</td>
                                @if($canManageAchievements)
                                <td class="px-5 py-3.5 text-right">
                                    <form method="POST"
                                          action="{{ route('achievements.destroy', [$archer, $ach]) }}"
                                          x-data
                                          @submit.prevent="if(confirm('Remove this achievement?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs font-bold text-red-400 hover:text-red-600 transition-colors px-2 py-1 rounded-lg hover:bg-red-50">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        </div>
    </div>
</div>
@endsection
