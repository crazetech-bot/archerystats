@extends('layouts.app')

@section('title', 'State Teams')
@section('header', 'State Teams')
@section('subheader', 'All registered state teams')

@section('header-actions')
    <a href="{{ route('state-teams.create') }}"
       class="inline-flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl shadow-md transition-all active:scale-95"
       style="background: linear-gradient(135deg,#065f46,#059669); color:#fff; font-family:'Barlow',sans-serif;">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        NEW STATE TEAM
    </a>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Stats bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #059669;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Teams</p>
            <p class="text-4xl font-black text-slate-900 mt-1" style="font-family:'Barlow',sans-serif;">{{ $totalTeams }}</p>
            <p class="text-xs text-slate-500 mt-1">Registered state teams</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #10b981;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active</p>
            <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#10b981;">{{ $activeTeams }}</p>
            <p class="text-xs text-slate-500 mt-1">Active teams</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm" style="border:1px solid #e2e8f0; border-top:4px solid #f59e0b;">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Archers</p>
            <p class="text-4xl font-black mt-1" style="font-family:'Barlow',sans-serif; color:#f59e0b;">{{ $totalArchers }}</p>
            <p class="text-xs text-slate-500 mt-1">Assigned archers</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="px-6 py-4" style="background:#064e3b; border-bottom:3px solid #059669;">
            <h2 class="text-sm font-black tracking-widest uppercase text-white" style="font-family:'Barlow',sans-serif;">All State Teams</h2>
        </div>

        @if($stateTeams->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-slate-400 text-sm">No state teams found. Create your first state team.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Team</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">State</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Archers</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Coaches</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="text-right px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($stateTeams as $team)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($team->logo_url)
                                        <img src="{{ $team->logo_url }}" alt="" class="h-9 w-9 rounded-xl object-cover flex-shrink-0">
                                    @else
                                        <div class="h-9 w-9 rounded-xl flex items-center justify-center text-white text-sm font-black flex-shrink-0"
                                             style="background: linear-gradient(135deg,#065f46,#059669);">
                                            {{ strtoupper(substr($team->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $team->name }}</p>
                                        @if($team->registration_number)
                                            <p class="text-xs text-slate-400">{{ $team->registration_number }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-slate-500">{{ $team->state ?: '—' }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">{{ $team->archers_count }}</td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">{{ $team->coaches_count }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="{{ $team->active ? 'background:#d1fae5; color:#065f46;' : 'background:#fee2e2; color:#991b1b;' }}">
                                    {{ $team->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('state-teams.show', $team) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                       style="background:#ecfdf5; color:#065f46;">
                                        View
                                    </a>
                                    <a href="{{ route('state-teams.edit', $team) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                       style="background:#fef3c7; color:#92400e;">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('state-teams.destroy', $team) }}"
                                          x-data @submit.prevent="if(confirm('Delete {{ $team->name }}?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                                style="background:#fee2e2; color:#991b1b;">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($stateTeams->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $stateTeams->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
