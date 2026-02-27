@extends('layouts.app')

@section('title', 'Club Archer Results — ' . $coach->full_name)
@section('header', 'Club Archer Results')
@section('subheader', ($coach->club?->name ?? 'No Club') . ' · Read-only')

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
<div class="max-w-6xl mx-auto">

    @if(!$coach->club_id)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl px-6 py-10 text-center">
            <svg class="mx-auto h-10 w-10 text-amber-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
            <p class="text-sm font-semibold text-amber-800">This coach has no club assigned.</p>
            <p class="text-xs text-amber-600 mt-1">Assign a club to the coach to view club archer results.</p>
        </div>
    @elseif($sessions->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-16 text-center">
            <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
            </svg>
            <p class="text-sm font-medium text-gray-500">No scoring sessions found for archers in this club.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Archer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Round</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">10+X</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">X</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($sessions as $session)
                    <tr class="hover:bg-teal-50/30 transition-colors cursor-pointer"
                        onclick="window.location='{{ route('coaches.club-results.show', [$coach, $session]) }}'">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ $session->archer->photo_url }}" alt="{{ $session->archer->full_name }}"
                                     class="h-7 w-7 rounded-lg object-cover flex-shrink-0">
                                <div>
                                    <p class="font-semibold text-gray-900 text-xs">{{ $session->archer->full_name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $session->archer->ref_no }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <p class="font-semibold text-gray-800">{{ $session->date->format('d M Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $session->date->format('l') }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-700">{{ $session->roundType->name }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="text-base font-black text-indigo-700">{{ $session->score?->total_score ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-3 text-center text-gray-600 font-semibold">{{ $session->score?->gold_count ?? '—' }}</td>
                        <td class="px-6 py-3 text-center text-gray-600 font-semibold">{{ $session->score?->x_count ?? '—' }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($session->is_competition)
                                <span class="text-xs font-semibold px-2 py-1 rounded-lg text-amber-700 bg-amber-100">Competition</span>
                            @else
                                <span class="text-xs font-medium px-2 py-1 rounded-lg text-gray-500 bg-gray-100">Training</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($sessions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $sessions->links() }}</div>
            @endif
        </div>
    @endif

</div>
@endsection
