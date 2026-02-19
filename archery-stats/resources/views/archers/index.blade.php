@extends('layouts.app')

@section('title', 'Archers')
@section('header', 'All Archers')

@section('content')
<div class="sm:flex sm:items-center sm:justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $archers->total() }} archer(s) registered</p>
    @if(auth()->user()->isClubAdmin())
        <a href="{{ route('archers.create') }}"
           class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold
                  text-white shadow hover:bg-indigo-500">
            + Add Archer
        </a>
    @endif
</div>

<div class="overflow-hidden rounded-lg shadow ring-1 ring-black ring-opacity-5">
    <table class="min-w-full divide-y divide-gray-200 bg-white">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-12 py-3 pl-4 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ref No</th>
                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Division(s)</th>
                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">State</th>
                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Club</th>
                <th class="relative py-3 pl-3 pr-4"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($archers as $archer)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 pl-4 pr-3">
                        <img src="{{ $archer->photo_url }}"
                             alt="{{ $archer->full_name }}"
                             class="h-10 w-10 rounded-full object-cover bg-gray-100">
                    </td>
                    <td class="px-3 py-3 text-sm font-mono text-gray-700">{{ $archer->ref_no ?? '—' }}</td>
                    <td class="px-3 py-3 text-sm font-medium">
                        <a href="{{ route('archers.show', $archer) }}"
                           class="text-indigo-600 hover:text-indigo-900">{{ $archer->full_name }}</a>
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-600 capitalize">{{ $archer->gender ?? '—' }}</td>
                    <td class="px-3 py-3 text-sm text-gray-600">{{ $archer->divisions_label }}</td>
                    <td class="px-3 py-3 text-sm text-gray-600">{{ $archer->state ?? '—' }}</td>
                    <td class="px-3 py-3 text-sm text-gray-600">{{ $archer->club?->name ?? '—' }}</td>
                    <td class="py-3 pl-3 pr-4 text-right text-sm">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('archers.show', $archer) }}"
                               class="text-indigo-600 hover:underline">View</a>
                            @if(auth()->user()->isClubAdmin())
                                <a href="{{ route('archers.edit', $archer) }}"
                                   class="text-yellow-600 hover:underline">Edit</a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('archers.destroy', $archer) }}"
                                      x-data
                                      @submit.prevent="if(confirm('Delete {{ $archer->ref_no }}? This cannot be undone.')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-400">
                        No archers found.
                        @if(auth()->user()->isClubAdmin())
                            <a href="{{ route('archers.create') }}" class="text-indigo-600 hover:underline">Add one now.</a>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $archers->links() }}</div>
@endsection
