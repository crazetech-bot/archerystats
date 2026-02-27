@extends('layouts.app')

@section('title', $club->name . ' — Club Profile')
@section('header', 'Club Profile')
@section('subheader', $club->name)

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('clubs.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
    @endif
    <a href="{{ route('clubs.dashboard', $club) }}"
       class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
       style="background: linear-gradient(135deg,#4338ca,#6366f1); color:#fff; font-family:'Barlow',sans-serif;">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
        DASHBOARD
    </a>
    <a href="{{ route('clubs.members', $club) }}"
       class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
       style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
        </svg>
        MEMBERS
    </a>
    <a href="{{ route('clubs.edit', $club) }}"
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
                <div class="h-28 w-full flex items-center justify-center" style="background: linear-gradient(135deg,#312e81 0%,#4338ca 100%);">
                    @if($club->logo_url)
                        <img src="{{ $club->logo_url }}" alt="{{ $club->name }}" class="h-20 w-20 object-contain rounded-xl bg-white/10 p-2">
                    @else
                        <div class="h-20 w-20 rounded-xl flex items-center justify-center text-white text-3xl font-black"
                             style="background:rgba(255,255,255,0.15); font-family:'Barlow',sans-serif;">
                            {{ strtoupper(substr($club->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="p-5">
                    <h2 class="text-lg font-black text-slate-900" style="font-family:'Barlow',sans-serif;">{{ $club->name }}</h2>
                    @if($club->registration_number)
                        <p class="text-sm text-slate-500 mt-0.5">{{ $club->registration_number }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                              style="{{ $club->active ? 'background:#d1fae5; color:#065f46;' : 'background:#fee2e2; color:#991b1b;' }}">
                            {{ $club->active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($club->founded_year)
                            <span class="text-xs text-slate-400">Est. {{ $club->founded_year }}</span>
                        @endif
                    </div>
                    @if($club->description)
                        <p class="text-sm text-slate-600 mt-3 leading-relaxed">{{ $club->description }}</p>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-2xl p-4 text-center shadow-sm" style="border:1px solid #e2e8f0; border-top:3px solid #6366f1;">
                    <p class="text-2xl font-black text-slate-900" style="font-family:'Barlow',sans-serif;">{{ $club->archers_count }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Archers</p>
                </div>
                <div class="bg-white rounded-2xl p-4 text-center shadow-sm" style="border:1px solid #e2e8f0; border-top:3px solid #0d9488;">
                    <p class="text-2xl font-black text-slate-900" style="font-family:'Barlow',sans-serif;">{{ $club->coaches_count }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Coaches</p>
                </div>
                <div class="bg-white rounded-2xl p-4 text-center shadow-sm" style="border:1px solid #e2e8f0; border-top:3px solid #f59e0b;">
                    <p class="text-2xl font-black text-slate-900" style="font-family:'Barlow',sans-serif;">{{ $sessionsThisMonth }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Sessions</p>
                </div>
            </div>
        </div>

        {{-- Right: Details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Contact --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
                <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                    <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Contact Information</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Email</p>
                        <p class="text-sm text-slate-700 mt-1">
                            @if($club->contact_email)
                                <a href="mailto:{{ $club->contact_email }}" style="color:#4338ca;">{{ $club->contact_email }}</a>
                            @else —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Phone</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $club->contact_phone ?: '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Website</p>
                        <p class="text-sm mt-1">
                            @if($club->website)
                                <a href="{{ $club->website }}" target="_blank" style="color:#4338ca;">{{ $club->website }}</a>
                            @else —
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Location --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
                <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                    <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Location</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Location / City</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $club->location ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">State</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $club->state ?: '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Address</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $club->address ?: '—' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- List of Coaches --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="flex items-center justify-between px-6 py-4" style="background:#0d9488; border-bottom:3px solid #14b8a6;">
            <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">List of Coaches</h3>
            <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:rgba(255,255,255,0.2); color:#fff;">
                {{ $club->coaches_count }} coach{{ $club->coaches_count !== 1 ? 'es' : '' }}
            </span>
        </div>
        @if($club->coaches->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-slate-400">No coaches registered to this club yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Coach</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Ref No</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Coaching Level</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Email</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($club->coaches as $coach)
                        <tr class="hover:bg-teal-50/40 transition-colors">
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
                                    <span class="font-semibold text-slate-800">{{ $coach->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs text-slate-500">{{ $coach->ref_no ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $coach->coaching_level ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $coach->user->email }}</td>
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

    {{-- List of Archers --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="flex items-center justify-between px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
            <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">List of Archers</h3>
            <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:rgba(255,255,255,0.2); color:#fff;">
                {{ $club->archers_count }} archer{{ $club->archers_count !== 1 ? 's' : '' }}
            </span>
        </div>
        @if($club->archers->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-slate-400">No archers registered to this club yet.</p>
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
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($club->archers as $archer)
                        <tr class="hover:bg-indigo-50/40 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    @if($archer->photo)
                                        <img src="{{ asset('storage/' . $archer->photo) }}" alt="{{ $archer->user->name }}"
                                             class="h-9 w-9 rounded-xl object-cover flex-shrink-0">
                                    @else
                                        <div class="h-9 w-9 rounded-xl flex items-center justify-center text-white text-sm font-black flex-shrink-0"
                                             style="background: linear-gradient(135deg,#4338ca,#6366f1);">
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
                                   style="color:#4338ca; background:#eef2ff; border:1px solid #c7d2fe;">
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
