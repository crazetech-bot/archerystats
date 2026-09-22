@extends('layouts.app')

@section('title', 'Announcements')
@section('header', 'Announcements')
@section('subheader', 'Club news and notices')

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

    {{-- Compose (admins & coaches) --}}
    @if($composeAudiences)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #fff7ed, #ffedd5);">
            <span class="h-8 w-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Post an Announcement</h2>
                <p class="text-xs text-gray-500">
                    {{ $coachMode ? 'Goes to your assigned archers' : 'Goes to your club members' }}
                    — optionally emailed too
                </p>
            </div>
        </div>
        <form method="POST" action="{{ route('announcements.store') }}" class="p-6 space-y-4"
              x-data="{ emailIt: false }"
              @submit="if (emailIt && !confirm('Post AND email this announcement to the selected audience?')) $event.preventDefault()">
            @csrf
            <div class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-64">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Title</label>
                    <input type="text" name="title" required maxlength="150" value="{{ old('title') }}"
                           placeholder="e.g. Training cancelled this Saturday"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:bg-white outline-none transition
                                  @error('title') border-red-400 bg-red-50 @enderror">
                    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Audience</label>
                    @if($coachMode)
                        <input type="hidden" name="audience" value="my_archers">
                        <div class="rounded-xl border border-gray-200 bg-gray-100 text-sm py-2.5 px-4 text-gray-600 font-semibold">
                            My assigned archers
                        </div>
                    @else
                        <select name="audience"
                                class="rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4 focus:border-amber-500 focus:bg-white outline-none transition">
                            @foreach($composeAudiences as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Message</label>
                <textarea name="body" required maxlength="5000" rows="4"
                          placeholder="Write your announcement…"
                          class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:bg-white outline-none transition
                                 @error('body') border-red-400 bg-red-50 @enderror">{{ old('body') }}</textarea>
                @error('body')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                    <input type="checkbox" name="send_email" value="1" x-model="emailIt"
                           class="rounded border-gray-300 text-amber-600 focus:ring-amber-400">
                    Also email this announcement to the audience
                </label>
                <button type="submit"
                        class="ml-auto px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:opacity-90"
                        style="background: linear-gradient(135deg, #b45309, #f59e0b);">
                    <span x-text="emailIt ? 'Post & Email' : 'Post Announcement'"></span>
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Feed --}}
    <div class="space-y-4">
        @forelse($announcements as $a)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden {{ $a->pinned ? 'border-2 border-amber-300' : 'border border-gray-100' }}">
            <div class="px-6 py-4">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    @php
                        $pill = match($a->audience) {
                            'archers'    => 'background:#fef3c7;color:#b45309;',
                            'coaches'    => 'background:#ccfbf1;color:#0f766e;',
                            'my_archers' => 'background:#e0e7ff;color:#3730a3;',
                            default      => 'background:#ede9fe;color:#6d28d9;',
                        };
                    @endphp
                    @if($a->pinned)
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background:#fef3c7;color:#92400e;">📌 Pinned</span>
                    @endif
                    @if(! $readIds->contains($a->id))
                        <span class="text-xs font-black px-2 py-0.5 rounded-full uppercase tracking-wide" style="background:#4338ca;color:#fff;">New</span>
                    @endif
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="{{ $pill }}">{{ $a->audienceLabel() }}</span>
                    @if(auth()->user()->hasRole(['super_admin']) || (auth()->user()->role === 'club_admin'))
                        <span class="text-xs text-gray-400">{{ $a->club->name }}</span>
                    @endif
                    @if($a->emailed_at)
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600">
                            ✉ Emailed to {{ $a->email_count }}
                        </span>
                    @endif
                    <span class="ml-auto text-xs text-gray-400">{{ $a->created_at->diffForHumans() }}</span>
                </div>
                <h3 class="text-base font-bold text-gray-900">{{ $a->title }}</h3>
                <p class="mt-1.5 text-sm text-gray-600 whitespace-pre-wrap leading-relaxed">{{ $a->body }}</p>
                <div class="mt-3 flex items-center gap-3">
                    <p class="text-xs text-gray-400">
                        Posted by <span class="font-semibold text-gray-500">{{ $a->sender?->name ?? 'Administrator' }}</span>
                        @if($a->coach) (Coach) @endif
                    </p>
                    @php
                        $canDelete = auth()->user()->role === 'super_admin'
                            || (auth()->user()->role === 'club_admin' && (int) auth()->user()->club_id === (int) $a->club_id)
                            || (auth()->user()->role === 'coach' && auth()->user()->coach && $a->coach_id === auth()->user()->coach->id);
                    @endphp
                    @if($canDelete)
                        <span class="ml-auto text-xs font-semibold text-gray-400" title="Members who have seen this announcement">
                            👁 Seen by {{ $a->reads_count }}
                        </span>
                        <form method="POST" action="{{ route('announcements.pin', $a) }}">
                            @csrf
                            <button type="submit"
                                    class="text-xs font-semibold px-2.5 py-1 rounded-lg border transition
                                           {{ $a->pinned ? 'border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100' : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                                {{ $a->pinned ? 'Unpin' : 'Pin' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('announcements.destroy', $a) }}"
                              onsubmit="return confirm('Delete this announcement?') && confirm('Final confirmation — delete it for everyone?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-12 text-center">
            <p class="text-sm text-gray-400">No announcements yet.</p>
        </div>
        @endforelse
    </div>

    @if($announcements->hasPages())
        <div>{{ $announcements->links() }}</div>
    @endif

</div>
@endsection
