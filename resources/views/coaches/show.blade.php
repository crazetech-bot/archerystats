@extends('layouts.app')

@section('title', $coach->full_name . ' — Coach Profile')
@section('og_image', $coach->photo ? asset('storage/' . $coach->photo) : '')
@section('og_description', $coach->full_name . ' · Coach' . ($coach->club ? ' · ' . $coach->club->name : '') . ' · Archery Stats')
@section('header', 'Coach Profile')
@section('subheader', $coach->ref_no)

@section('header-actions')
    @if(auth()->user()->isClubAdmin() || auth()->user()->isAdmin())
    <a href="{{ route('coaches.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
    @endif
    @if(auth()->user()->isClubAdmin() || (auth()->user()->role === 'coach' && auth()->user()->coach?->id === $coach->id))
        <a href="{{ route('coaches.edit', $coach) }}"
           class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
           style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
            EDIT
        </a>
    @endif
    @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('coaches.destroy', $coach) }}"
              x-data @submit.prevent="if(confirm('Permanently delete {{ $coach->ref_no }}?')) $el.submit()">
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Photo + Identity --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Profile card --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="h-28 w-full" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
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
                        <img src="{{ $coach->photo_url }}"
                             alt="{{ $coach->full_name }}"
                             class="h-24 w-24 rounded-2xl object-cover bg-slate-100 shadow-lg"
                             style="border: 4px solid #fff;">
                    </div>
                    <h2 class="text-xl font-black text-slate-900 leading-tight" style="font-family:'Barlow',sans-serif;">{{ $coach->full_name }}</h2>
                    <p class="text-sm text-slate-500">{{ $coach->user->email }}</p>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg"
                              style="background:#0f172a; color:#f59e0b;">
                            {{ $coach->ref_no ?? 'PENDING' }}
                        </span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg border
                                     {{ $coach->active ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-slate-500 bg-slate-50 border-slate-200' }}">
                            {{ $coach->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: Details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Personal info --}}
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
                            $items = [
                                'Date of Birth'    => $coach->date_of_birth?->format('d-m-Y') ?? '—',
                                'Age'              => $coach->age ? $coach->age . ' years old' : '—',
                                'Gender'           => $coach->gender ? ucfirst($coach->gender) : '—',
                                'Contact Number'   => $coach->phone ?? '—',
                                'Club'             => $coach->club?->name ?? '—',
                                'State / National' => $coach->stateTeam?->name ?? ($coach->team ?? '—'),
                                'Coaching Level'        => $coach->coaching_level ?? '—',
                                'Sports Science Course' => $coach->sports_science_course ?? '—',
                            ];
                        @endphp
                        @foreach($items as $label => $value)
                            <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</dt>
                                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>

            {{-- Location --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #10b981;">
                    <svg class="h-5 w-5 flex-shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Location</h3>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-2 gap-3">
                        <div class="col-span-2 rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">Address</dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $coach->address_line ?? '—' }}</dd>
                        </div>
                        @foreach(['Postcode' => $coach->postcode ?? '—', 'State' => $coach->state ?? '—', 'Country' => $coach->country ?? '—'] as $label => $value)
                        <div class="rounded-xl px-4 py-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>

            {{-- Notes --}}
            @if($coach->notes)
            <div class="bg-white rounded-2xl shadow-sm p-6" style="border: 1px solid #e2e8f0;">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3" style="font-family:'Barlow',sans-serif;">Notes</h3>
                <p class="text-sm text-slate-600 whitespace-pre-wrap leading-relaxed">{{ $coach->notes }}</p>
            </div>
            @endif

        </div>
    </div>

    {{-- Sub-modules --}}
    <div class="mt-6">
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3" style="font-family:'Barlow',sans-serif;">Coach Tools</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Assigned Archers --}}
            <a href="{{ route('coaches.archers.index', $coach) }}"
               class="group bg-white rounded-2xl shadow-sm p-5 hover:shadow-md transition-all flex items-center gap-4"
               style="border: 1px solid #e2e8f0; border-left: 4px solid #f59e0b;">
                <div class="h-12 w-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(245,158,11,0.1);">
                    <svg class="h-6 w-6" style="color:#f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.75 3.75 0 11-6.75 0 3.75 3.75 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-slate-900 group-hover:text-amber-600 transition-colors" style="font-family:'Barlow',sans-serif;">ASSIGNED ARCHERS</p>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">{{ $coach->archers()->count() }} assigned</p>
                </div>
                <svg class="h-4 w-4 text-slate-300 group-hover:text-amber-500 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </a>

            {{-- Training Sessions --}}
            <a href="{{ route('coaches.training.index', $coach) }}"
               class="group bg-white rounded-2xl shadow-sm p-5 hover:shadow-md transition-all flex items-center gap-4"
               style="border: 1px solid #e2e8f0; border-left: 4px solid #0f172a;">
                <div class="h-12 w-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(15,23,42,0.08);">
                    <svg class="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-slate-900 group-hover:text-slate-700 transition-colors" style="font-family:'Barlow',sans-serif;">TRAINING SESSIONS</p>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">{{ $coach->trainingSessions()->count() }} sessions logged</p>
                </div>
                <svg class="h-4 w-4 text-slate-300 group-hover:text-slate-500 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </a>

            {{-- Club Archer Results --}}
            <a href="{{ route('coaches.club-results', $coach) }}"
               class="group bg-white rounded-2xl shadow-sm p-5 hover:shadow-md transition-all flex items-center gap-4"
               style="border: 1px solid #e2e8f0; border-left: 4px solid #10b981;">
                <div class="h-12 w-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(16,185,129,0.1);">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-slate-900 group-hover:text-emerald-700 transition-colors" style="font-family:'Barlow',sans-serif;">CLUB ARCHER RESULTS</p>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">View scoring sessions</p>
                </div>
                <svg class="h-4 w-4 text-slate-300 group-hover:text-emerald-500 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </a>

        </div>
    </div>

    {{-- My Archers Performance --}}
    @php
        $assignedArchers = $coach->archers()->with('user')->get();
    @endphp
    @if($assignedArchers->isNotEmpty())
    <div class="mt-6">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
            <div class="px-5 py-4 flex items-center gap-3" style="background:#0f172a; border-bottom:3px solid #0d9488;">
                <svg class="h-5 w-5 flex-shrink-0" style="color:#2dd4bf;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                </svg>
                <h3 class="text-sm font-black text-white uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">My Archers — Performance Overview</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Archer</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Last Session</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Last Score</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Total Sessions</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Best Score</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($assignedArchers as $a)
                        @php
                            $lastSession = $a->sessions()->with('score')->latest('date')->first();
                            $totalSess   = $a->sessions()->count();
                            $bestScore   = $a->sessions()
                                ->join('scores', 'archery_sessions.id', '=', 'scores.archery_session_id')
                                ->max('scores.total_score');
                        @endphp
                        <tr class="hover:bg-teal-50/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('archers.show', $a) }}" class="flex items-center gap-3 group">
                                    <img src="{{ $a->photo_url }}" alt="{{ $a->full_name }}"
                                         class="h-8 w-8 rounded-xl object-cover bg-slate-100 flex-shrink-0">
                                    <div>
                                        <p class="font-bold text-slate-900 group-hover:text-teal-700 transition-colors">{{ $a->full_name }}</p>
                                        <p class="text-xs text-slate-400 font-mono">{{ $a->ref_no }}</p>
                                    </div>
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-medium whitespace-nowrap">
                                {{ $lastSession ? $lastSession->date->format('d M Y') : '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-black text-slate-900" style="font-family:'Barlow',sans-serif;">
                                {{ $lastSession?->score?->total_score ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-slate-600">{{ $totalSess }}</td>
                            <td class="px-5 py-3.5 text-right font-bold" style="color:#d97706;">{{ $bestScore ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('archers.performance', $a) }}"
                                   class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all"
                                   style="background:rgba(13,148,136,0.1); color:#0d9488;">
                                    Analytics →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
