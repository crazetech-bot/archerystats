@extends('layouts.app')

@section('title', $coach->full_name . ' — Coach Profile')
@section('header', 'Coach Profile')
@section('subheader', $coach->ref_no)

@section('header-actions')
    <a href="{{ route('coaches.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
    @if(auth()->user()->isClubAdmin())
        <a href="{{ route('coaches.edit', $coach) }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow-md transition-all hover:opacity-90"
           style="background: linear-gradient(135deg, #d97706, #f59e0b);">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
            Edit
        </a>
    @endif
    @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('coaches.destroy', $coach) }}"
              x-data @submit.prevent="if(confirm('Permanently delete {{ $coach->ref_no }}?')) $el.submit()">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow-md transition-all hover:opacity-90"
                    style="background: linear-gradient(135deg, #dc2626, #ef4444);">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
                Delete
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Photo + Identity --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Profile card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-24 w-full" style="background: linear-gradient(135deg, #0d9488, #14b8a6);"></div>
                <div class="px-5 pb-5">
                    <div class="-mt-12 mb-4">
                        <img src="{{ $coach->photo_url }}"
                             alt="{{ $coach->full_name }}"
                             class="h-24 w-24 rounded-2xl object-cover bg-gray-100 border-4 border-white shadow-lg">
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ $coach->full_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $coach->user->email }}</p>

                    <div class="mt-3 flex flex-wrap gap-1.5">
                        <span class="text-xs font-mono font-semibold text-teal-600 bg-teal-50 border border-teal-200 px-2.5 py-1 rounded-lg">
                            {{ $coach->ref_no ?? 'PENDING' }}
                        </span>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-lg
                                     {{ $coach->active ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-gray-500 bg-gray-50 border border-gray-200' }}">
                            {{ $coach->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: Details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Personal info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-teal-100 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </span>
                    Personal Information
                </h3>
                <dl class="grid grid-cols-2 gap-4">
                    @php
                        $items = [
                            'Date of Birth'    => $coach->date_of_birth?->format('d-m-Y') ?? '—',
                            'Age'              => $coach->age ? $coach->age . ' years old' : '—',
                            'Gender'           => $coach->gender ? ucfirst($coach->gender) : '—',
                            'Contact Number'   => $coach->phone ?? '—',
                            'Club'             => $coach->club?->name ?? '—',
                            'State / National' => $coach->team ?? '—',
                            'Coaching Level'   => $coach->coaching_level ?? '—',
                        ];
                    @endphp
                    @foreach($items as $label => $value)
                        <div class="bg-gray-50 rounded-xl px-4 py-3">
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</dt>
                            <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Location --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="h-6 w-6 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </span>
                    Location
                </h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl px-4 py-3 col-span-2">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Address</dt>
                        <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $coach->address_line ?? '—' }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Postcode</dt>
                        <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $coach->postcode ?? '—' }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">State</dt>
                        <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $coach->state ?? '—' }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Country</dt>
                        <dd class="text-sm font-semibold text-gray-800 mt-1">{{ $coach->country ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Notes --}}
            @if($coach->notes)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Notes</h3>
                <p class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed">{{ $coach->notes }}</p>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
