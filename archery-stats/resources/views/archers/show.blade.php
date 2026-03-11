@extends('layouts.app')

@section('title', $archer->full_name . ' — Profile')
@section('header', 'Archer Profile')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">

        {{-- Header band --}}
        <div class="bg-indigo-600 h-20"></div>

        <div class="px-6 pb-6">
            {{-- Photo + badges --}}
            <div class="flex items-end justify-between -mt-10 mb-4">
                <img src="{{ $archer->photo_url }}"
                     alt="{{ $archer->full_name }}"
                     class="h-24 w-20 rounded-lg object-cover border-4 border-white shadow-md bg-gray-100">
                <div class="flex items-center gap-2 pb-1">
                    <span class="rounded-full bg-indigo-100 text-indigo-700 text-xs font-mono font-semibold px-3 py-1">
                        {{ $archer->ref_no ?? 'PENDING' }}
                    </span>
                    <span class="rounded-full px-2 py-1 text-xs font-medium
                                 {{ $archer->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $archer->active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <h2 class="text-xl font-bold text-gray-900">{{ $archer->full_name }}</h2>
            <p class="text-sm text-gray-500">{{ $archer->user->email }}</p>

            @if(!empty($archer->divisions))
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach($archer->divisions as $div)
                        <span class="rounded-full bg-yellow-100 text-yellow-800 text-xs px-3 py-0.5 font-medium">
                            {{ $div }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="border-t border-gray-100 px-6 py-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">

                <div>
                    <dt class="font-medium text-gray-500">Date of Birth</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $archer->date_of_birth?->format('d-m-Y') ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-500">Age</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $archer->age ? $archer->age . ' years' : '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-500">Gender</dt>
                    <dd class="text-gray-900 mt-0.5 capitalize">{{ $archer->gender ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-500">Phone</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $archer->phone ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-500">Team</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $archer->team ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-500">Club</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $archer->club?->name ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-500">Classification</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $archer->classification ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-500">State</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $archer->state ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-500">Country</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $archer->country ?? '—' }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="font-medium text-gray-500">Address</dt>
                    <dd class="text-gray-900 mt-0.5">
                        @if($archer->address_line || $archer->postcode || $archer->address_state)
                            {{ $archer->address_line }}<br>
                            {{ implode(', ', array_filter([$archer->postcode, $archer->address_state])) }}
                        @else
                            —
                        @endif
                    </dd>
                </div>

                @if($archer->notes)
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-gray-500">Notes</dt>
                        <dd class="text-gray-900 mt-0.5 whitespace-pre-wrap">{{ $archer->notes }}</dd>
                    </div>
                @endif

            </dl>
        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-4 flex justify-between items-center">
        <a href="{{ route('archers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Back to Archers
        </a>
        <div class="flex gap-3">
            @if(auth()->user()->isClubAdmin())
                <a href="{{ route('archers.edit', $archer) }}"
                   class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-400">
                    Edit
                </a>
            @endif
            @if(auth()->user()->isAdmin())
                <form method="POST" action="{{ route('archers.destroy', $archer) }}"
                      x-data
                      @submit.prevent="if(confirm('Permanently delete {{ $archer->ref_no }}?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection
