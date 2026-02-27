@extends('layouts.app')

@section('title', 'Assigned Archers — ' . $coach->full_name)
@section('header', 'Assigned Archers')
@section('subheader', $coach->ref_no . ' · ' . $coach->full_name)

@section('header-actions')
    <a href="{{ route('coaches.show', $coach) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Coach
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="rounded-xl px-5 py-3 text-sm font-medium text-emerald-800 bg-emerald-50 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl px-5 py-3 text-sm font-medium text-red-800 bg-red-50 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Assign New Archer --}}
    @if($available->isNotEmpty() && (auth()->user()->isClubAdmin() || auth()->user()->role === 'coach'))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1 flex items-center gap-2">
            <span class="h-6 w-6 rounded-lg bg-teal-100 flex items-center justify-center">
                <svg class="h-3.5 w-3.5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
            </span>
            Assign Archer
        </h3>
        <p class="text-xs text-gray-400 mb-4 ml-8">
            Same club → assigned immediately. Different club → a confirmation email is sent to the archer (valid 72 hours).
        </p>
        <form method="POST" action="{{ route('coaches.archers.store', $coach) }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <label for="archer_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Select Archer</label>
                <select id="archer_id" name="archer_id"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                               focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition
                               @error('archer_id') border-red-400 bg-red-50 @enderror">
                    <option value="">— Choose archer —</option>
                    @foreach($available as $archer)
                        <option value="{{ $archer->id }}">
                            {{ $archer->ref_no }} — {{ $archer->full_name }}
                            {{ $archer->club ? '(' . $archer->club->name . ')' : '(No Club)' }}
                            {{ ($archer->club_id === null || $archer->club_id === $coach->club_id) ? '✓' : '✉' }}
                        </option>
                    @endforeach
                </select>
                @error('archer_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1.5 text-xs text-gray-400">✓ = same club (direct assign) &nbsp;|&nbsp; ✉ = different club (invitation sent)</p>
            </div>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:opacity-90 flex-shrink-0"
                    style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                Assign
            </button>
        </form>
    </div>
    @endif

    {{-- Pending Cross-Club Invitations --}}
    @if($pendingInvitations->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #fef3c7;">
        <div class="px-5 py-4 flex items-center gap-3" style="background:#fffbeb; border-bottom:2px solid #f59e0b;">
            <svg class="h-5 w-5 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
            <div>
                <h3 class="text-sm font-black text-amber-900 uppercase tracking-widest" style="font-family:'Barlow',sans-serif;">Pending Invitations</h3>
                <p class="text-xs text-amber-700">Awaiting archer confirmation — expires in 72 hours</p>
            </div>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#fafafa; border-bottom:1px solid #e2e8f0;">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Archer</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Club</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Expires</th>
                    @if(auth()->user()->isClubAdmin())
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($pendingInvitations as $inv)
                <tr class="hover:bg-amber-50/40">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-gray-900">{{ $inv->archer->full_name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $inv->archer->ref_no }}</p>
                    </td>
                    <td class="px-5 py-3 text-gray-600 text-sm">{{ $inv->archer->club?->name ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 px-2 py-1 rounded-lg">
                            {{ $inv->expires_at->format('d M Y, h:i A') }}
                        </span>
                    </td>
                    @if(auth()->user()->isClubAdmin())
                    <td class="px-5 py-3 text-right">
                        <form method="POST" action="{{ route('coach-archer-invitations.cancel', $inv) }}"
                              x-data @submit.prevent="if(confirm('Cancel this invitation?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-medium text-red-600 hover:text-red-800 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 transition-colors">
                                Cancel
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

    {{-- Assigned Archers List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f0fdfa, #ccfbf1);">
            <div class="flex items-center gap-3">
                <span class="h-8 w-8 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.75 3.75 0 11-6.75 0 3.75 3.75 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Assigned Archers</h2>
                    <p class="text-xs text-gray-500">{{ $coach->archers->count() }} archer(s) under this coach</p>
                </div>
            </div>
        </div>

        @if($coach->archers->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.75 3.75 0 11-6.75 0 3.75 3.75 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                <p class="text-sm font-medium text-gray-500">No archers assigned yet</p>
                <p class="text-xs text-gray-400 mt-1">Use the form above to assign archers to this coach.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Archer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ref No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Club</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($coach->archers as $archer)
                    <tr class="hover:bg-teal-50/30 transition-colors">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $archer->photo_url }}" alt="{{ $archer->full_name }}"
                                     class="h-8 w-8 rounded-lg object-cover bg-gray-100 flex-shrink-0">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $archer->full_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $archer->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="font-mono text-xs font-semibold text-teal-600 bg-teal-50 px-2 py-1 rounded-lg">{{ $archer->ref_no }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-600 capitalize">{{ $archer->gender ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $archer->club?->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('archers.show', $archer) }}"
                                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                    Profile
                                </a>
                                <a href="{{ route('archers.performance', $archer) }}"
                                   class="text-xs font-medium text-teal-600 hover:text-teal-800 px-3 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 transition-colors">
                                    Analytics
                                </a>
                                <a href="{{ route('sessions.index', $archer) }}"
                                   class="text-xs font-medium text-amber-600 hover:text-amber-800 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 transition-colors">
                                    Sessions
                                </a>
                                @if(auth()->user()->isClubAdmin())
                                <form method="POST" action="{{ route('coaches.archers.destroy', [$coach, $archer]) }}"
                                      x-data @submit.prevent="if(confirm('Remove {{ $archer->ref_no }} from roster?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-medium text-red-600 hover:text-red-800 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 transition-colors">
                                        Remove
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
