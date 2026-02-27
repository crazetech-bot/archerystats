@extends('layouts.app')

@section('title', $club->name . ' — Members')
@section('header', $club->name)
@section('subheader', 'Membership Management')

@section('header-actions')
    <a href="{{ route('clubs.dashboard', $club) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Dashboard
    </a>
@endsection

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ tab: 'archers' }">

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $type)
    @if(session($type))
        @php $colors = ['success'=>'green','error'=>'red','info'=>'blue']; $c = $colors[$type]; @endphp
        <div class="mb-4 flex items-center gap-3 rounded-xl border border-{{ $c }}-200 bg-{{ $c }}-50 px-4 py-3">
            <p class="text-sm text-{{ $c }}-700 font-medium">{{ session($type) }}</p>
        </div>
    @endif
    @endforeach

    {{-- Tab switcher --}}
    <div class="flex gap-2 mb-6">
        <button @click="tab='archers'"
                :class="tab==='archers' ? 'text-white shadow-md' : 'text-slate-600 bg-slate-100 hover:bg-slate-200'"
                :style="tab==='archers' ? 'background:linear-gradient(135deg,#4338ca,#6366f1);' : ''"
                class="px-5 py-2.5 rounded-xl text-sm font-black transition-all" style="font-family:'Barlow',sans-serif;">
            Archers ({{ $club->archers->count() }})
        </button>
        <button @click="tab='coaches'"
                :class="tab==='coaches' ? 'text-white shadow-md' : 'text-slate-600 bg-slate-100 hover:bg-slate-200'"
                :style="tab==='coaches' ? 'background:linear-gradient(135deg,#0d9488,#14b8a6);' : ''"
                class="px-5 py-2.5 rounded-xl text-sm font-black transition-all" style="font-family:'Barlow',sans-serif;">
            Coaches ({{ $club->coaches->count() }})
        </button>
    </div>

    {{-- ── ARCHERS TAB ── --}}
    <div x-show="tab==='archers'" class="space-y-5">

        {{-- Current archer members --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
            <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Current Archers</h3>
            </div>
            @if($club->archers->isNotEmpty())
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Archer</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Ref No.</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Division</th>
                        <th class="text-right px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($club->archers as $archer)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                                     style="background:linear-gradient(135deg,#4338ca,#6366f1);">
                                    {{ strtoupper(substr($archer->full_name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-900">{{ $archer->full_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $archer->ref_no }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs">{{ $archer->divisions_label }}</td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('clubs.archers.remove', [$club, $archer]) }}"
                                  x-data @submit.prevent="if(confirm('Remove {{ $archer->full_name }} from this club?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                        style="background:#fee2e2; color:#991b1b;">
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="px-6 py-10 text-center text-slate-400 text-sm">No archers in this club yet.</div>
            @endif
        </div>

        {{-- Pending archer invitations --}}
        @php $archerPending = collect($pendingInvitations)->filter(fn($p) => $p['invitation']->invitable_type === 'archer'); @endphp
        @if($archerPending->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
            <div class="px-6 py-4" style="background:#78350f; border-bottom:3px solid #f59e0b;">
                <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Pending Invitations</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    @foreach($archerPending as $p)
                    <tr class="hover:bg-amber-50">
                        <td class="px-6 py-3 font-medium text-slate-800">{{ $p['invitable']?->full_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-400">Invited {{ $p['invitation']->invited_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-xs text-slate-400">Expires {{ $p['invitation']->expires_at->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('club-invitations.cancel', $p['invitation']) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:#fef3c7; color:#92400e;">
                                    Cancel
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Invite archer --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
            <div class="px-6 py-4" style="background:#312e81; border-bottom:3px solid #6366f1;">
                <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Invite Archer</h3>
            </div>
            <div class="p-6" x-data="{ selected: '', currentClub: '' }">
                <form method="POST" id="inviteArcherForm" x-ref="form">
                    @csrf
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Select Archer</label>
                            <select name="archer_id" x-model="selected"
                                    @change="currentClub = $el.options[$el.selectedIndex].dataset.club"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">— Select an archer to invite —</option>
                                @foreach($availableArchers as $archer)
                                    <option value="{{ $archer->id }}"
                                            data-club="{{ $archer->club?->name ?? '' }}">
                                        {{ $archer->full_name }} ({{ $archer->ref_no }})
                                        @if($archer->club) · {{ $archer->club->name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" :disabled="!selected"
                                :style="selected ? 'background:linear-gradient(135deg,#4338ca,#6366f1); opacity:1;' : 'background:#e2e8f0; opacity:0.5; cursor:not-allowed;'"
                                class="px-5 py-2.5 rounded-xl text-sm font-black text-white transition-all"
                                style="font-family:'Barlow',sans-serif;"
                                @click.prevent="
                                    if (!selected) return;
                                    let msg = 'Send membership invitation to this archer?';
                                    if (currentClub) msg = 'This archer is currently in ' + currentClub + '. Send an invitation to transfer them to your club?';
                                    if (confirm(msg)) {
                                        $refs.form.action = '{{ url('clubs/' . $club->id . '/archers') }}/' + selected;
                                        $refs.form.submit();
                                    }
                                ">
                            Send Invitation
                        </button>
                    </div>
                </form>
                <p class="text-xs text-slate-400 mt-3">The archer will receive an email with Accept / Decline links. This invitation expires in 7 days.</p>
            </div>
        </div>
    </div>

    {{-- ── COACHES TAB ── --}}
    <div x-show="tab==='coaches'" class="space-y-5">

        {{-- Current coaches --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
            <div class="px-6 py-4" style="background:#0d4040; border-bottom:3px solid #0d9488;">
                <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Current Coaches</h3>
            </div>
            @if($club->coaches->isNotEmpty())
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Coach</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Ref No.</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Level</th>
                        <th class="text-right px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($club->coaches as $coach)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                                     style="background:linear-gradient(135deg,#0d9488,#14b8a6);">
                                    {{ strtoupper(substr($coach->full_name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-900">{{ $coach->full_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $coach->ref_no }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs">{{ $coach->coaching_level ?? '—' }}</td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('clubs.coaches.remove', [$club, $coach]) }}"
                                  x-data @submit.prevent="if(confirm('Remove {{ $coach->full_name }} from this club?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg"
                                        style="background:#fee2e2; color:#991b1b;">
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="px-6 py-10 text-center text-slate-400 text-sm">No coaches in this club yet.</div>
            @endif
        </div>

        {{-- Pending coach invitations --}}
        @php $coachPending = collect($pendingInvitations)->filter(fn($p) => $p['invitation']->invitable_type === 'coach'); @endphp
        @if($coachPending->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
            <div class="px-6 py-4" style="background:#78350f; border-bottom:3px solid #f59e0b;">
                <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Pending Invitations</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    @foreach($coachPending as $p)
                    <tr class="hover:bg-amber-50">
                        <td class="px-6 py-3 font-medium text-slate-800">{{ $p['invitable']?->full_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-400">Invited {{ $p['invitation']->invited_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-xs text-slate-400">Expires {{ $p['invitation']->expires_at->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('club-invitations.cancel', $p['invitation']) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:#fef3c7; color:#92400e;">
                                    Cancel
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Invite coach --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
            <div class="px-6 py-4" style="background:#0d4040; border-bottom:3px solid #0d9488;">
                <h3 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">Invite Coach</h3>
            </div>
            <div class="p-6" x-data="{ selected: '', currentClub: '' }">
                <form method="POST" id="inviteCoachForm" x-ref="form">
                    @csrf
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Select Coach</label>
                            <select name="coach_id" x-model="selected"
                                    @change="currentClub = $el.options[$el.selectedIndex].dataset.club"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-teal-400">
                                <option value="">— Select a coach to invite —</option>
                                @foreach($availableCoaches as $coach)
                                    <option value="{{ $coach->id }}"
                                            data-club="{{ $coach->club?->name ?? '' }}">
                                        {{ $coach->full_name }} ({{ $coach->ref_no }})
                                        @if($coach->club) · {{ $coach->club->name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" :disabled="!selected"
                                :style="selected ? 'background:linear-gradient(135deg,#0d9488,#14b8a6); opacity:1;' : 'background:#e2e8f0; opacity:0.5; cursor:not-allowed;'"
                                class="px-5 py-2.5 rounded-xl text-sm font-black text-white transition-all"
                                style="font-family:'Barlow',sans-serif;"
                                @click.prevent="
                                    if (!selected) return;
                                    let msg = 'Send membership invitation to this coach?';
                                    if (currentClub) msg = 'This coach is currently in ' + currentClub + '. Send an invitation to transfer them to your club?';
                                    if (confirm(msg)) {
                                        $refs.form.action = '{{ url('clubs/' . $club->id . '/coaches') }}/' + selected;
                                        $refs.form.submit();
                                    }
                                ">
                            Send Invitation
                        </button>
                    </div>
                </form>
                <p class="text-xs text-slate-400 mt-3">The coach will receive an email with Accept / Decline links. This invitation expires in 7 days.</p>
            </div>
        </div>
    </div>

</div>
@endsection
