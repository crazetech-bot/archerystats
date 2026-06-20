@extends('layouts.app')

@section('title', 'Attendance Report')
@section('header', 'Attendance Report')
@section('subheader', 'Per-archer attendance across training sessions')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
            <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                </svg>
            </span>
            <h2 class="text-sm font-bold text-gray-900">Filters</h2>
        </div>
        <form method="GET" action="{{ route('attendance.report') }}" class="p-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($showClubFilter)
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Club</label>
                <select name="club_id" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/20 outline-none">
                    <option value="">All clubs</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" @selected($clubFilter == $club->id)>{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Team</label>
                <select name="team" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/20 outline-none">
                    <option value="">All teams</option>
                    @foreach($teams as $t)
                        <option value="{{ $t }}" @selected($team === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}"
                       class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/20 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}"
                       class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/20 outline-none">
            </div>
            <div class="sm:col-span-2 lg:col-span-4 flex items-center gap-3">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:opacity-90 active:scale-95"
                        style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                    Apply Filters
                </button>
                <a href="{{ route('attendance.report') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Reset</a>
            </div>
        </form>
    </div>

    {{-- Report table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
            <span class="h-8 w-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
            </span>
            <div class="flex-1">
                <h2 class="text-sm font-bold text-gray-900">Attendance by Archer</h2>
                <p class="text-xs text-gray-500">{{ $report->count() }} archer(s)</p>
            </div>
        </div>

        @if($report->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-gray-400">No attendance records for the selected filters.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left">Archer</th>
                        <th class="px-3 py-3 text-center text-emerald-600">Present</th>
                        <th class="px-3 py-3 text-center text-amber-600">Late</th>
                        <th class="px-3 py-3 text-center text-rose-600">Absent</th>
                        <th class="px-3 py-3 text-center text-slate-600">Excused</th>
                        <th class="px-3 py-3 text-center">Total</th>
                        <th class="px-5 py-3 text-left w-48">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($report as $row)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $row['archer']->photo_url }}" alt="{{ $row['archer']->full_name }}"
                                     class="h-9 w-9 rounded-lg object-cover flex-shrink-0">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 truncate">{{ $row['archer']->full_name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $row['archer']->ref_no }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center font-bold text-emerald-700">{{ $row['counts']['present'] }}</td>
                        <td class="px-3 py-3 text-center font-bold text-amber-700">{{ $row['counts']['late'] }}</td>
                        <td class="px-3 py-3 text-center font-bold text-rose-700">{{ $row['counts']['absent'] }}</td>
                        <td class="px-3 py-3 text-center font-bold text-slate-700">{{ $row['counts']['excused'] }}</td>
                        <td class="px-3 py-3 text-center font-bold text-gray-700">{{ $row['total'] }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full rounded-full"
                                         style="width: {{ $row['rate'] }}%; background: {{ $row['rate'] >= 75 ? '#10b981' : ($row['rate'] >= 50 ? '#f59e0b' : '#f43f5e') }};"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-700 w-12 text-right">{{ $row['rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/50 text-xs text-gray-400">
            Rate = (present + late) ÷ total marked. Absent and excused count toward the total but not the rate.
        </div>
        @endif
    </div>

</div>
@endsection
