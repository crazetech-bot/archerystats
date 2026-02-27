@extends('layouts.app')

@section('title', 'Elimination Matches')
@section('header', 'Elimination Matches')
@section('subheader', 'World Archery Individual Recurve Set System')

@section('header-actions')
    @if(in_array(auth()->user()->role, ['super_admin', 'club_admin']))
        <a href="{{ route('elimination-matches.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm"
           style="background: linear-gradient(135deg, #4338ca, #6366f1);">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            New Match
        </a>
    @endif
@endsection

@section('content')
<div x-data="{ activeTab: '{{ request('category', 'all') }}' }">

    {{-- Stats bar --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="stat-card rounded-2xl bg-white p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Total Matches</p>
            <p class="text-3xl font-black text-slate-800">{{ $total }}</p>
        </div>
        <div class="stat-card rounded-2xl bg-white p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Completed</p>
            <p class="text-3xl font-black text-emerald-600">{{ $completed }}</p>
        </div>
        <div class="stat-card rounded-2xl bg-white p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">In Progress</p>
            <p class="text-3xl font-black text-amber-500">{{ $inProgress }}</p>
        </div>
    </div>

    {{-- Category filter tabs --}}
    <div class="flex gap-2 mb-5 flex-wrap">
        @foreach(['all' => 'All', 'outdoor' => 'Outdoor', 'indoor' => 'Indoor', 'mssm' => 'MSSM'] as $cat => $label)
            @php
                $colors = [
                    'all'     => ['bg' => '#4338ca', 'text' => '#fff'],
                    'outdoor' => ['bg' => '#059669', 'text' => '#fff'],
                    'indoor'  => ['bg' => '#4338ca', 'text' => '#fff'],
                    'mssm'    => ['bg' => '#db2777', 'text' => '#fff'],
                ];
                $c = $colors[$cat];
            @endphp
            <a href="{{ route('elimination-matches.index', ['category' => $cat]) }}"
               class="px-4 py-1.5 rounded-full text-sm font-bold transition-all border"
               style="{{ request('category', 'all') === $cat
                    ? 'background:' . $c['bg'] . '; color:' . $c['text'] . '; border-color:' . $c['bg'] . ';'
                    : 'background:#fff; color:#64748b; border-color:#e2e8f0;' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Matches table --}}
    <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        @if($matches->isEmpty())
            <div class="py-16 text-center">
                <svg class="h-12 w-12 mx-auto mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-slate-400 font-medium">No matches found.</p>
                @if(in_array(auth()->user()->role, ['super_admin', 'club_admin']))
                    <a href="{{ route('elimination-matches.create') }}"
                       class="mt-3 inline-flex items-center gap-1 text-sm font-bold"
                       style="color:#4338ca;">
                        + Create first match
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-slate-400">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-slate-400">Archer A</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-slate-400">Archer B</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-slate-400">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-slate-400">Result</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($matches as $match)
                            @php
                                $catColors = ['outdoor' => '#059669', 'indoor' => '#4338ca', 'mssm' => '#db2777'];
                                $catColor  = $catColors[$match->category] ?? '#64748b';
                                $catLabel  = $match->category === 'mssm' ? 'MSSM' : ucfirst($match->category);
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-slate-700">
                                    {{ $match->date->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-semibold text-slate-800">
                                        {{ $match->archer_a_id ? $match->archerA->full_name : $match->archer_a_name }}
                                    </span>
                                    @if($match->archer_a_id)
                                        <span class="text-xs text-slate-400 block">{{ $match->archerA->ref_no }}</span>
                                    @else
                                        <span class="text-xs text-amber-500 block">Guest</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-semibold text-slate-800">
                                        {{ $match->archer_b_id ? $match->archerB->full_name : $match->archer_b_name }}
                                    </span>
                                    @if($match->archer_b_id)
                                        <span class="text-xs text-slate-400 block">{{ $match->archerB->ref_no }}</span>
                                    @else
                                        <span class="text-xs text-amber-500 block">Guest</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold text-white"
                                          style="background: {{ $catColor }};">
                                        {{ $catLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($match->status === 'completed')
                                        @php
                                            // Calculate final set points from arrow_values
                                            $av = $match->arrow_values ?? ['a' => [], 'b' => []];
                                            $ptsA = 0; $ptsB = 0;
                                            for ($i = 0; $i < 5; $i++) {
                                                $tA = 0; $tB = 0; $ok = true;
                                                foreach (($av['a'][$i] ?? []) as $v) {
                                                    if ($v === null) { $ok = false; break; }
                                                    $n = strtoupper($v);
                                                    $tA += ($n === 'X' ? 10 : ($n === 'M' ? 0 : (int)$n));
                                                }
                                                if (!$ok) break;
                                                foreach (($av['b'][$i] ?? []) as $v) {
                                                    if ($v === null) { $ok = false; break; }
                                                    $n = strtoupper($v);
                                                    $tB += ($n === 'X' ? 10 : ($n === 'M' ? 0 : (int)$n));
                                                }
                                                if (!$ok) break;
                                                if ($tA > $tB) { $ptsA += 2; } elseif ($tB > $tA) { $ptsB += 2; } else { $ptsA++; $ptsB++; }
                                                if ($ptsA >= 6 || $ptsB >= 6) break;
                                            }
                                            // Determine winner name — winner_id may be null if winner was a guest archer
                                            if ($match->winner_id) {
                                                $winnerName = $match->winner->full_name;
                                                $isA = $match->winner_id === $match->archer_a_id;
                                            } else {
                                                $isA = $ptsA >= $ptsB;
                                                $winnerName = $isA
                                                    ? ($match->archer_a_id ? $match->archerA->full_name : $match->archer_a_name)
                                                    : ($match->archer_b_id ? $match->archerB->full_name : $match->archer_b_name);
                                            }
                                        @endphp
                                        <span class="text-sm font-bold text-emerald-700">
                                            {{ $winnerName }} wins
                                            {{ $isA ? $ptsA . '–' . $ptsB : $ptsB . '–' . $ptsA }}
                                            @if($match->shoot_off)<span class="text-xs font-normal text-slate-400">(S/O)</span>@endif
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($match->status === 'completed')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                              style="background:#dcfce7; color:#15803d;">Completed</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                              style="background:#fef3c7; color:#92400e;">In Progress</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('elimination-matches.scorecard', $match) }}"
                                           class="px-3 py-1 rounded-lg text-xs font-bold text-white"
                                           style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                                            {{ $match->status === 'completed' ? 'View' : 'Score' }}
                                        </a>
                                        @if(in_array(auth()->user()->role, ['super_admin', 'club_admin']))
                                            <form method="POST" action="{{ route('elimination-matches.destroy', $match) }}"
                                                  onsubmit="return confirm('Delete this match?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1 rounded-lg text-xs font-bold"
                                                        style="background:#fee2e2; color:#dc2626;">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($matches->hasPages())
                <div class="px-4 py-4 border-t border-gray-100">
                    {{ $matches->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
