@extends('layouts.app')

@section('title', $stateTeam->name . ' — State Team Profile')
@section('og_image', $stateTeam->logo ? asset('storage/' . $stateTeam->logo) : '')
@section('og_description', $stateTeam->name . ' · State Archery Team · Archery Stats')
@section('header', 'State Team Profile')
@section('subheader', $stateTeam->name)

@section('header-actions')
    <a href="{{ route('state-teams.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
    <a href="{{ route('state-teams.edit', $stateTeam) }}"
       class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
       style="background:#e2e8f0; color:#0f172a; font-family:'Barlow',sans-serif;">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
        </svg>
        EDIT
    </a>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Identity --}}
        <div class="lg:col-span-1 space-y-5">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
                <div class="h-28 w-full flex items-center justify-center" style="background: linear-gradient(135deg,#064e3b 0%,#059669 100%);">
                    @if($stateTeam->logo_url)
                        <img src="{{ $stateTeam->logo_url }}" alt="{{ $stateTeam->name }}" class="h-20 w-20 object-contain rounded-xl bg-white/10 p-2">
                    @else
                        <div class="h-20 w-20 rounded-xl flex items-center justify-center text-white text-3xl font-black"
                             style="background:rgba(255,255,255,0.15); font-family:'Barlow',sans-serif;">
                            {{ strtoupper(substr($stateTeam->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="p-5">
                    <h2 class="text-lg font-black text-slate-900" style="font-family:'Barlow',sans-serif;">{{ $stateTeam->name }}</h2>
                    @if($stateTeam->registration_number)
                        <p class="text-sm text-slate-500 mt-0.5">{{ $stateTeam->registration_number }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                              style="{{ $stateTeam->active ? 'background:#d1fae5; color:#065f46;' : 'background:#fee2e2; color:#991b1b;' }}">
                            {{ $stateTeam->active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($stateTeam->founded_year)
                            <span class="text-xs text-slate-400">Est. {{ $stateTeam->founded_year }}</span>
                        @endif
                    </div>
                    @if($stateTeam->description)
                        <p class="text-sm text-slate-600 mt-3 leading-relaxed">{{ $stateTeam->description }}</p>
                    @endif

                    {{-- Team Admin badge --}}
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">Team Admin</p>
                        @if($stateTeam->admin)
                            <div class="flex items-center gap-2">
                                <div class="h-7 w-7 rounded-lg flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                                     style="background:linear-gradient(135deg,#0d9488,#14b8a6);">
                                    {{ strtoupper(substr($stateTeam->admin->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $stateTeam->admin->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $stateTeam->admin->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-slate-400 italic">No admin appointed</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-2xl p-4 text-center shadow-sm" style="border:1px solid #e2e8f0; border-top:3px solid #059669;">
                    <p class="text-3xl font-black text-slate-900" style="font-family:'Barlow',sans-serif;">{{ $stateTeam->archers_count }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Archers</p>
                </div>
                <div class="bg-white rounded-2xl p-4 text-center shadow-sm" style="border:1px solid #e2e8f0; border-top:3px solid #0d9488;">
                    <p class="text-3xl font-black text-slate-900" style="font-family:'Barlow',sans-serif;">{{ $stateTeam->coaches_count }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Coaches</p>
                </div>
            </div>
        </div>

        {{-- Right: Details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Contact --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
                <div class="px-6 py-4" style="background:#064e3b; border-bottom:3px solid #059669;">
                    <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Contact Information</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Email</p>
                        <p class="text-sm text-slate-700 mt-1">
                            @if($stateTeam->contact_email)
                                <a href="mailto:{{ $stateTeam->contact_email }}" style="color:#059669;">{{ $stateTeam->contact_email }}</a>
                            @else —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Phone</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $stateTeam->contact_phone ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">State</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $stateTeam->state ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Website</p>
                        <p class="text-sm mt-1">
                            @if($stateTeam->website)
                                <a href="{{ $stateTeam->website }}" target="_blank" style="color:#059669;">{{ $stateTeam->website }}</a>
                            @else —
                            @endif
                        </p>
                    </div>
                    @if($stateTeam->address)
                    <div class="sm:col-span-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Address</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $stateTeam->address }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Coaches in this state team --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="flex items-center justify-between px-6 py-4" style="background:#0d9488; border-bottom:3px solid #14b8a6;">
            <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Assigned Coaches</h3>
            <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:rgba(255,255,255,0.2); color:#fff;">
                {{ $stateTeam->coaches_count }} coach{{ $stateTeam->coaches_count !== 1 ? 'es' : '' }}
            </span>
        </div>
        @if($stateTeam->coaches->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-slate-400">No coaches assigned to this state team yet.</p>
                <p class="text-xs text-slate-400 mt-1">Assign coaches via their profile (State / National Team field).</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Coach</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Ref No</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Coaching Level</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Club</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($stateTeam->coaches as $coach)
                        <tr class="hover:bg-teal-50/30 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    @if($coach->photo)
                                        <img src="{{ asset('storage/' . $coach->photo) }}" alt="{{ $coach->user->name }}"
                                             class="h-9 w-9 rounded-xl object-cover flex-shrink-0">
                                    @else
                                        <div class="h-9 w-9 rounded-xl flex items-center justify-center text-white text-sm font-black flex-shrink-0"
                                             style="background: linear-gradient(135deg,#0d9488,#14b8a6);">
                                            {{ strtoupper(substr($coach->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $coach->user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $coach->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs text-slate-500">{{ $coach->ref_no ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $coach->coaching_level ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $coach->club?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('coaches.show', $coach) }}"
                                   class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                                   style="color:#0d9488; background:#f0fdfa; border:1px solid #99f6e4;">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Appoint Admin — visible to super_admin / state_admin only --}}
    @if(auth()->user()->isAdmin() || auth()->user()->role === 'state_admin')
    <div class="mt-6 bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;"
         x-data="{ open: false }">
        <div class="flex items-center justify-between px-6 py-4 cursor-pointer"
             style="background:linear-gradient(135deg,#1e1b4b,#3730a3); border-bottom:3px solid #6366f1;"
             @click="open = !open">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 text-indigo-200 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
                <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Appoint Team Admin</h3>
            </div>
            <svg class="h-4 w-4 text-indigo-300 transition-transform" :class="open ? 'rotate-180' : ''"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </div>

        <div x-show="open" x-cloak class="px-6 py-5">
            @if($stateTeam->admin)
            <div class="flex items-center gap-3 mb-4 p-3 rounded-xl" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                <svg class="h-4 w-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-emerald-700">
                    Current admin: <strong>{{ $stateTeam->admin->name }}</strong>
                    — appointing a new one will replace them.
                </p>
            </div>
            @endif

            <form method="POST" action="{{ route('state-teams.appoint-admin', $stateTeam) }}">
                @csrf
                <div class="flex items-end gap-3 flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 block">Select Coach</label>
                        <select name="user_id" required
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 outline-none">
                            <option value="">— select a coach —</option>
                            @foreach($coachUsers as $cu)
                                <option value="{{ $cu->id }}" @selected($stateTeam->admin_user_id === $cu->id)>
                                    {{ $cu->name }}
                                    @if($cu->coach?->ref_no) ({{ $cu->coach->ref_no }}) @endif
                                    @if($cu->role !== 'coach') · {{ str_replace('_', ' ', $cu->role) }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            onclick="return confirm('Appoint this coach as state team admin? They will be granted State Admin privileges.')"
                            style="background:linear-gradient(135deg,#4338ca,#6366f1);"
                            class="px-5 py-2 rounded-xl text-white text-sm font-bold shadow-sm transition-opacity hover:opacity-90">
                        Appoint Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Archers in this state team --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="flex items-center justify-between px-6 py-4" style="background:#064e3b; border-bottom:3px solid #059669;">
            <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Assigned Archers</h3>
            <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:rgba(255,255,255,0.2); color:#fff;">
                {{ $stateTeam->archers_count }} archer{{ $stateTeam->archers_count !== 1 ? 's' : '' }}
            </span>
        </div>
        @if($stateTeam->archers->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-slate-400">No archers assigned to this state team yet.</p>
                <p class="text-xs text-slate-400 mt-1">Assign archers via their profile (Personal Information → State Team).</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Archer</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Ref No</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Division</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Classification</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Club</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($stateTeam->archers as $archer)
                        <tr class="hover:bg-emerald-50/30 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    @if($archer->photo)
                                        <img src="{{ asset('storage/' . $archer->photo) }}" alt="{{ $archer->user->name }}"
                                             class="h-9 w-9 rounded-xl object-cover flex-shrink-0">
                                    @else
                                        <div class="h-9 w-9 rounded-xl flex items-center justify-center text-white text-sm font-black flex-shrink-0"
                                             style="background: linear-gradient(135deg,#065f46,#059669);">
                                            {{ strtoupper(substr($archer->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="font-semibold text-slate-800">{{ $archer->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs text-slate-500">{{ $archer->ref_no ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $archer->division ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $archer->classification ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $archer->club->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $stCfg = [
                                        'active'           => ['#d1fae5','#065f46','Active'],
                                        'no_longer_active' => ['#f1f5f9','#475569','No Longer Active'],
                                        'injury'           => ['#fee2e2','#991b1b','Injury'],
                                    ];
                                    [$stBg, $stColor, $stLabel] = $stCfg[$archer->status ?? 'active'] ?? ['#f1f5f9','#475569', ucfirst($archer->status ?? '')];
                                @endphp
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-lg"
                                      style="background:{{ $stBg }}; color:{{ $stColor }};">
                                    {{ $stLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('archers.show', $archer) }}"
                                   class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                                   style="color:#059669; background:#ecfdf5; border:1px solid #6ee7b7;">
                                    View
                                </a>
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
