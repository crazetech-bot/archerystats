@extends('layouts.app')

@section('title', $club->name . ' — Club Detail')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.clubs.index') }}" class="hover:text-indigo-600">Clubs</a>
        <span>/</span>
        <span class="text-gray-700 font-medium">{{ $club->name }}</span>
    </div>

    {{-- Header --}}
    <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100">
        <div class="px-6 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, #4338ca, #6366f1)">
            <div class="flex items-center gap-4">
                @if ($club->logo)
                    <img src="{{ asset('storage/'.$club->logo) }}" class="w-12 h-12 rounded-xl object-cover border-2 border-white/30">
                @else
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($club->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $club->name }}</h1>
                    <a href="http://{{ $club->slug }}.sportdns.com" target="_blank"
                       class="text-indigo-200 text-sm hover:text-white font-mono">
                        {{ $club->slug }}.sportdns.com ↗
                    </a>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    {{ $club->active ? 'bg-green-400/20 text-green-100' : 'bg-red-400/20 text-red-100' }}">
                    {{ $club->active ? 'Active' : 'Inactive' }}
                </span>
                <form method="POST" action="{{ route('admin.clubs.toggle', $club) }}">
                    @csrf
                    <button type="submit"
                        class="text-xs px-4 py-2 rounded-lg bg-white/20 text-white hover:bg-white/30 transition-colors">
                        {{ $club->active ? 'Deactivate Club' : 'Activate Club' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @foreach ([
            ['label' => 'Archers',  'value' => $archerCount],
            ['label' => 'Coaches',  'value' => $coachCount],
            ['label' => 'Admin Users', 'value' => $adminUsers->count()],
        ] as $stat)
        <div class="rounded-2xl border border-gray-100 shadow-sm bg-white p-5">
            <div class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Club Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-700">Club Information</h2>
            </div>
            <div class="p-6 space-y-3 text-sm">
                @foreach ([
                    ['Contact Email',    $club->contact_email],
                    ['Contact Phone',    $club->contact_phone],
                    ['Address',         $club->address],
                    ['State',           $club->state],
                    ['Website',         $club->website],
                    ['Founded',         $club->founded_year],
                    ['Reg. No.',        $club->registration_number],
                    ['Facebook',        $club->facebook_url],
                    ['Instagram',       $club->instagram_url],
                    ['WhatsApp',        $club->whatsapp_number],
                ] as [$label, $value])
                    @if ($value)
                    <div class="flex gap-3">
                        <span class="text-gray-400 w-32 shrink-0">{{ $label }}</span>
                        <span class="text-gray-700">{{ $value }}</span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Admin Users --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-700">Admin Users</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse ($adminUsers as $user)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <div class="font-medium text-gray-700 text-sm">{{ $user->name }}</div>
                        <div class="text-xs text-gray-400">{{ $user->email }}</div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 font-medium">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                </div>
                @empty
                <div class="px-6 py-4 text-sm text-gray-400">No admin users assigned.</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Recent Archers --}}
    @if ($club->archers->count())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700">Archers ({{ $archerCount }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Name</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Ref No</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Division</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($club->archers->take(10) as $archer)
                    <tr>
                        <td class="px-6 py-3 text-gray-700">{{ $archer->full_name }}</td>
                        <td class="px-6 py-3 text-gray-500 font-mono text-xs">{{ $archer->ref_no }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ implode(', ', $archer->divisions ?? []) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($archerCount > 10)
            <div class="px-6 py-3 text-xs text-gray-400 border-t border-gray-50">
                Showing 10 of {{ $archerCount }} archers.
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
