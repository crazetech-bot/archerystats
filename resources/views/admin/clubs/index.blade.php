@extends('layouts.app')

@section('title', 'Club Management')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100">
        <div class="px-6 py-4 flex items-center gap-3" style="background: linear-gradient(135deg, #4338ca, #6366f1)">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">Club Management</h1>
                <p class="text-indigo-200 text-sm">Manage all clubs on the platform</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ([
            ['label' => 'Total Clubs',    'value' => $totalClubs,   'color' => 'indigo'],
            ['label' => 'Active Clubs',   'value' => $activeClubs,  'color' => 'green'],
            ['label' => 'Total Archers',  'value' => $totalArchers, 'color' => 'blue'],
            ['label' => 'Total Coaches',  'value' => $totalCoaches, 'color' => 'purple'],
        ] as $stat)
        <div class="rounded-2xl border border-gray-100 shadow-sm bg-white p-5">
            <div class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Success flash --}}
    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Clubs table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-700">All Clubs</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Club</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Subdomain</th>
                        <th class="text-center px-4 py-3 text-gray-500 font-medium">Archers</th>
                        <th class="text-center px-4 py-3 text-gray-500 font-medium">Coaches</th>
                        <th class="text-center px-4 py-3 text-gray-500 font-medium">Status</th>
                        <th class="text-right px-6 py-3 text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($clubs as $club)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if ($club->logo)
                                    <img src="{{ asset('storage/'.$club->logo) }}" class="w-8 h-8 rounded-lg object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                        {{ strtoupper(substr($club->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-800">{{ $club->name }}</div>
                                    @if ($club->location)
                                        <div class="text-xs text-gray-400">{{ $club->location }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="http://{{ $club->slug }}.sportdns.com" target="_blank"
                               class="text-indigo-600 hover:underline text-xs font-mono">
                                {{ $club->slug }}.sportdns.com
                            </a>
                        </td>
                        <td class="px-4 py-4 text-center text-gray-600">{{ $club->archers_count }}</td>
                        <td class="px-4 py-4 text-center text-gray-600">{{ $club->coaches_count }}</td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $club->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $club->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.clubs.show', $club) }}"
                                   class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                                    View
                                </a>
                                <form method="POST" action="{{ route('admin.clubs.toggle', $club) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg border transition-colors
                                            {{ $club->active
                                                ? 'border-red-200 text-red-600 hover:bg-red-50'
                                                : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                                        {{ $club->active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
