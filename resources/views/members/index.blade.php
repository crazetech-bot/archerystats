@extends('layouts.app')

@section('title', 'Members — ' . $club->name)
@section('header', 'Members')
@section('subheader', $club->name)

@section('content')
<div class="space-y-6">

    {{-- Flash messages --}}
    @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="rounded-xl bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 text-sm">
            {{ session('info') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ([
            ['label' => 'Archers',  'value' => $archers->count()],
            ['label' => 'Coaches',  'value' => $coaches->count()],
            ['label' => 'Pending Invites', 'value' => $invitations->filter(fn($i) => $i->isPending())->count()],
            ['label' => 'Transfer Requests', 'value' => $transfers->count()],
        ] as $stat)
        <div class="rounded-2xl border border-gray-100 shadow-sm bg-white p-5">
            <div class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Invite a member --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #ecfdf5, #d1fae5);">
            <span class="h-8 w-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v10.5z"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Invite a Member</h2>
                <p class="text-xs text-gray-500">Works for existing users and for people without an account yet</p>
            </div>
        </div>
        <form method="POST" action="{{ route('members.invite') }}" class="p-6 flex flex-wrap items-end gap-3">
            @csrf
            <div class="flex-1 min-w-56">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Email Address</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="member@example.com"
                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                              focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:bg-white outline-none transition
                              @error('email') border-red-400 bg-red-50 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Invite As</label>
                <select name="role"
                        class="rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4 focus:border-emerald-500 focus:bg-white outline-none transition">
                    <option value="archer">Archer</option>
                    <option value="coach">Coach</option>
                </select>
            </div>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:opacity-90 flex-shrink-0"
                    style="background: linear-gradient(135deg, #047857, #10b981);">
                Send Invitation
            </button>
        </form>
    </div>

    {{-- Incoming transfer requests --}}
    @if($transfers->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
            <span class="h-8 w-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Incoming Transfer Requests</h2>
                <p class="text-xs text-gray-500">Archers asking to make {{ $club->name }} their primary club</p>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($transfers as $transfer)
            <div class="px-6 py-4 flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $transfer->archer?->user?->name ?? 'Unknown archer' }}
                        <span class="text-xs text-gray-400 font-normal">{{ $transfer->archer?->ref_no }}</span>
                    </p>
                    <p class="text-xs text-gray-400">
                        From {{ $transfer->fromClub?->name ?? 'Unaffiliated' }}
                        · requested {{ $transfer->created_at->diffForHumans() }}
                        · expires {{ $transfer->expires_at->format('d M Y') }}
                    </p>
                </div>
                <form method="POST" action="{{ route('members.transfers.approve', $transfer) }}"
                      onsubmit="return confirm('Approve this transfer? {{ addslashes($transfer->archer?->user?->name ?? 'The archer') }} will make this their primary club.')">
                    @csrf
                    <button type="submit"
                            class="text-xs font-bold px-3 py-1.5 rounded-lg text-white transition hover:opacity-90"
                            style="background: linear-gradient(135deg, #047857, #10b981);">
                        Approve
                    </button>
                </form>
                <form method="POST" action="{{ route('members.transfers.decline', $transfer) }}"
                      onsubmit="return confirm('Decline this transfer request?')">
                    @csrf
                    <button type="submit"
                            class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">
                        Decline
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Pending / recent invitations --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #fffbeb, #fef3c7);">
            <span class="h-8 w-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Invitations</h2>
                <p class="text-xs text-gray-500">Recent invitations and their status</p>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($invitations as $inv)
            <div class="px-6 py-3.5 flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">
                        {{ $inv->displayName() }}
                        <span class="text-xs font-normal text-gray-400">· {{ ucfirst($inv->invitable_type) }}</span>
                        @if(! $inv->invitable_id)
                            <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-violet-50 text-violet-600">new user</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 truncate">{{ $inv->email }} · sent {{ $inv->invited_at?->format('d M Y') }}</p>
                </div>
                @php
                    $badge = $inv->isPending() ? ['Pending', 'background:#fef3c7;color:#b45309;']
                        : ($inv->status === 'accepted' ? ['Accepted', 'background:#dcfce7;color:#15803d;']
                        : ($inv->isExpired() ? ['Expired', 'background:#f1f5f9;color:#64748b;']
                        : ['Declined', 'background:#fee2e2;color:#b91c1c;']));
                @endphp
                <span class="text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0" style="{{ $badge[1] }}">{{ $badge[0] }}</span>
                @if($inv->status !== 'accepted')
                    <form method="POST" action="{{ route('members.invitations.resend', $inv) }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition">
                            Resend
                        </button>
                    </form>
                @endif
                @if($inv->isPending())
                    <form method="POST" action="{{ route('members.invitations.cancel', $inv) }}"
                          onsubmit="return confirm('Cancel this invitation?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">
                            Cancel
                        </button>
                    </form>
                @endif
            </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-gray-400">No invitations sent yet.</div>
            @endforelse
        </div>
    </div>

    {{-- Roster --}}
    @foreach ([['Archers', $archers, 'archer'], ['Coaches', $coaches, 'coach']] as [$label, $rows, $type])
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #ede9fe, #ddd6fe);">
            <span class="h-8 w-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(109,40,217,0.12);">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
            </span>
            <h2 class="text-sm font-bold text-gray-900">{{ $label }} ({{ $rows->count() }})</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($rows as $member)
            @php $pivot = $member->pivot; @endphp
            <div class="px-6 py-3.5 flex flex-wrap items-center gap-3">
                <div class="h-9 w-9 rounded-xl flex items-center justify-center flex-shrink-0 font-bold text-sm"
                     style="background:#ede9fe;color:#6d28d9;">
                    {{ strtoupper(substr($member->user?->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">
                        {{ $member->user?->name ?? '—' }}
                        <span class="text-xs font-normal text-gray-400">{{ $member->ref_no }}</span>
                    </p>
                    <p class="text-xs text-gray-400 truncate">
                        {{ $member->user?->email }}
                        @if($pivot->joined_at) · joined {{ \Illuminate\Support\Carbon::parse($pivot->joined_at)->format('d M Y') }} @endif
                    </p>
                </div>
                @if($pivot->primary_club)
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0" style="background:#fef3c7;color:#b45309;">Primary</span>
                @else
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0 bg-gray-100 text-gray-500">Secondary</span>
                @endif
                <a href="{{ route($type === 'archer' ? 'archers.show' : 'coaches.show', $member) }}"
                   class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition flex-shrink-0">
                    View
                </a>
                <form method="POST" action="{{ route('members.remove', [$type, $member->id]) }}" class="flex-shrink-0"
                      onsubmit="return confirm('Remove {{ addslashes($member->user?->name ?? 'this member') }} from {{ addslashes($club->name) }}?') && confirm('Final confirmation — remove this member from the club? Their account is kept.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">
                        Remove
                    </button>
                </form>
            </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-gray-400">No {{ strtolower($label) }} yet.</div>
            @endforelse
        </div>
    </div>
    @endforeach

</div>
@endsection
