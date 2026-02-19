@extends('layouts.app')

@section('title', 'Archers')
@section('header', 'Archers')
@section('subheader', 'Manage registered archers')

@section('header-actions')
    @if(auth()->user()->isClubAdmin())
        <a href="{{ route('archers.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-md
                  transition-all hover:opacity-90 active:scale-95"
           style="background: linear-gradient(135deg, #4338ca, #6366f1);">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Add Archer
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
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $total }}</p>
        <p class="text-xs text-gray-500 mt-0.5">Registered archers</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Male</p>
        <p class="text-3xl font-bold text-blue-500 mt-1">{{ $male }}</p>
        <p class="text-xs text-gray-500 mt-0.5">Male archers</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Female</p>
        <p class="text-3xl font-bold text-pink-500 mt-1">{{ $female }}</p>
        <p class="text-xs text-gray-500 mt-0.5">Female archers</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Page</p>
        <p class="text-3xl font-bold text-gray-700 mt-1">{{ $archers->count() }}</p>
        <p class="text-xs text-gray-500 mt-0.5">Showing this page</p>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full">
        <thead>
            <tr style="background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                <th class="w-12 py-3.5 pl-5 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ref No</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Division(s)</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">State</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Club</th>
                <th class="px-4 py-3.5 text-right pr-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($archers as $archer)
                <tr class="hover:bg-indigo-50/30 transition-colors group">
                    <td class="py-3.5 pl-5 pr-3">
                        <img src="{{ $archer->photo_url }}" alt="{{ $archer->full_name }}"
                             class="h-10 w-10 rounded-full object-cover bg-gray-100 ring-2 ring-white shadow-sm">
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-block text-xs font-mono font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">
                            {{ $archer->ref_no ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('archers.show', $archer) }}"
                           class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                            {{ $archer->full_name }}
                        </a>
                        <p class="text-xs text-gray-400">{{ $archer->user->email }}</p>
                    </td>
                    <td class="px-4 py-3.5">
                        @if($archer->gender === 'male')
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">
                                ♂ Male
                            </span>
                        @elseif($archer->gender === 'female')
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-pink-700 bg-pink-50 px-2.5 py-1 rounded-full">
                                ♀ Female
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex flex-wrap gap-1">
                            @forelse($archer->divisions ?? [] as $div)
                                <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                                    {{ $div }}
                                </span>
                            @empty
                                <span class="text-gray-400 text-sm">—</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600">{{ $archer->state ?? '—' }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-600">{{ $archer->club?->name ?? '—' }}</td>
                    <td class="px-4 py-3.5 pr-5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('archers.show', $archer) }}"
                               class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg transition-colors">
                                View
                            </a>
                            @if(auth()->user()->isClubAdmin())
                                <a href="{{ route('archers.edit', $archer) }}"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 px-2.5 py-1.5 rounded-lg transition-colors">
                                    Edit
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('archers.destroy', $archer) }}"
                                      x-data
                                      @submit.prevent="if(confirm('Delete {{ $archer->ref_no }}?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg transition-colors">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">No archers found</p>
                            @if(auth()->user()->isClubAdmin())
                                <a href="{{ route('archers.create') }}" class="text-sm text-indigo-600 hover:underline font-medium">Add your first archer</a>
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
