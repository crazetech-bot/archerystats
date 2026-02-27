@extends('layouts.app')

@section('title', 'Clubs')
@section('header', 'Clubs')
@section('subheader', 'All registered clubs')

@section('header-actions')
    <a href="{{ route('clubs.import') }}"
       class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
       style="background: linear-gradient(135deg,#0f172a,#334155); color:#fff; font-family:'Barlow',sans-serif;">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        IMPORT CSV
    </a>
    <a href="{{ route('clubs.create') }}"
       class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
       style="background: linear-gradient(135deg,#4338ca,#6366f1); color:#fff; font-family:'Barlow',sans-serif;">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        NEW CLUB
    </a>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Stats bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #6366f1;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Clubs</p>
            <p class="text-4xl font-black text-slate-900 mt-1" style="font-family:'Barlow',sans-serif;">{{ $totalClubs }}</p>
            <p class="text-xs text-slate-500 mt-1">Registered clubs</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #10b981;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active</p>
            <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#10b981;">{{ $activeClubs }}</p>
            <p class="text-xs text-slate-500 mt-1">Active clubs</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #f59e0b;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Archers</p>
            <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#f59e0b;">{{ $totalArchers }}</p>
            <p class="text-xs text-slate-500 mt-1">Total archers</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #0d9488;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Coaches</p>
            <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#0d9488;">{{ $totalCoaches }}</p>
            <p class="text-xs text-slate-500 mt-1">Total coaches</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('import_result'))
        @php $result = session('import_result'); @endphp
        <div class="mb-4 rounded-xl overflow-hidden" style="border:1px solid #e2e8f0;">
            <div class="flex items-center gap-4 px-5 py-4" style="background:#f0fdf4; border-bottom:1px solid #bbf7d0;">
                <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex gap-6">
                    <span class="text-sm font-bold text-emerald-700">
                        {{ $result['imported'] }} club{{ $result['imported'] !== 1 ? 's' : '' }} imported
                    </span>
                    @if($result['skipped'] > 0)
                        <span class="text-sm font-semibold text-amber-600">
                            {{ $result['skipped'] }} skipped
                        </span>
                    @endif
                </div>
            </div>
            @if(!empty($result['errors']))
                <div class="px-5 py-3 bg-white space-y-1">
                    @foreach($result['errors'] as $err)
                        <p class="text-xs text-slate-500">{{ $err }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Clubs table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
            <h2 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">All Clubs</h2>
        </div>

        @if($clubs->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-slate-400 text-sm">No clubs found. Create your first club.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Club</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Location</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Archers</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Coaches</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="text-right px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($clubs as $club)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($club->logo_url)
                                        <img src="{{ $club->logo_url }}" alt="" class="h-9 w-9 rounded-xl object-cover flex-shrink-0">
                                    @else
                                        <div class="h-9 w-9 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-sm font-black"
                                             style="background: linear-gradient(135deg,#4338ca,#6366f1);">
                                            {{ strtoupper(substr($club->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $club->name }}</p>
                                        @if($club->registration_number)
                                            <p class="text-xs text-slate-400">{{ $club->registration_number }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-slate-500">{{ $club->location ?: '—' }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">{{ $club->archers_count }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">{{ $club->coaches_count }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="{{ $club->active ? 'background:#d1fae5; color:#065f46;' : 'background:#fee2e2; color:#991b1b;' }}">
                                    {{ $club->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('clubs.dashboard', $club) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                       style="background:#eef2ff; color:#4338ca;">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('clubs.edit', $club) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                       style="background:#fef3c7; color:#92400e;">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('clubs.destroy', $club) }}"
                                          x-data @submit.prevent="if(confirm('Delete {{ $club->name }}?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                                style="background:#fee2e2; color:#991b1b;">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($clubs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $clubs->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
