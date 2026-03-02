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

{{-- Stats bar --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @php
        $total = $archers->total();
        $male  = \App\Models\Archer::whereHas('user')->where('gender','male')->count();
        $female= \App\Models\Archer::whereHas('user')->where('gender','female')->count();
    @endphp
    <div class="bg-white rounded-2xl p-5 shadow-sm" style="border: 1px solid #e2e8f0; border-top: 4px solid #f59e0b;">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total</p>
        <p class="text-4xl font-black text-slate-900 mt-1" style="font-family:'Barlow',sans-serif;">{{ $total }}</p>
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
        <p class="text-4xl font-black text-slate-700 mt-1" style="font-family:'Barlow',sans-serif;">{{ $archers->count() }}</p>
        <p class="text-xs text-slate-500 mt-1">On this page</p>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e2e8f0;">
    <table class="min-w-full">
        <thead>
            <tr style="background: #0f172a;">
                <th class="w-12 py-3.5 pl-5 pr-3 text-left text-xs font-bold text-slate-400 uppercase tracking-widest"></th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Ref No</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Name</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Gender</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Division</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">State Team</th>
                <th class="px-4 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:table-cell">Club</th>
                <th class="px-4 py-3.5 text-right pr-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($archers as $archer)
                <tr class="transition-colors hover:bg-amber-50/40 group">
                    <td class="py-3.5 pl-5 pr-3">
                        <img src="{{ $archer->photo_url }}" alt="{{ $archer->full_name }}"
                             class="h-10 w-10 rounded-full object-cover bg-slate-100 ring-2 ring-white shadow-sm">
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-block text-xs font-mono font-bold px-2.5 py-1 rounded-lg"
                              style="background:#0f172a; color:#f59e0b;">
                            {{ $archer->ref_no ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('archers.show', $archer) }}"
                           class="text-sm font-bold text-slate-900 hover:text-amber-600 transition-colors">
                            {{ $archer->full_name }}
                        </a>
                        <p class="text-xs text-slate-400">{{ $archer->user->email }}</p>
                    </td>
                    <td class="px-4 py-3.5">
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
                    <td class="px-4 py-3.5">
                        @if($archer->division)
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full"
                                  style="background:rgba(245,158,11,0.12); color:#92400e; border: 1px solid rgba(245,158,11,0.3);">
                                {{ $archer->division }}
                            </span>
                        @else
                            <span class="text-slate-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-sm text-slate-600 hidden sm:table-cell">{{ $archer->stateTeam?->name ?? ($archer->state_team ?? '—') }}</td>
                    <td class="px-4 py-3.5 text-sm text-slate-600 hidden sm:table-cell">{{ $archer->club?->name ?? '—' }}</td>
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
                    <td colspan="8" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-600">No archers found</p>
                            @if(auth()->user()->isClubAdmin())
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
