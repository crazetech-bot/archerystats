@extends('layouts.app')

@section('title', 'New Training Session — ' . $coach->full_name)
@section('header', 'New Training Session')
@section('subheader', $coach->ref_no . ' · ' . $coach->full_name)

@section('header-actions')
    <a href="{{ route('coaches.training.index', $coach) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')
<form method="POST" action="{{ route('coaches.training.store', $coach) }}">
@csrf

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Session Details --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f0fdfa, #ccfbf1);">
            <span class="h-8 w-8 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Session Details</h2>
                <p class="text-xs text-gray-500">Date, location and focus</p>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

            <div>
                <label for="date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Date <span class="text-red-500 normal-case font-normal">*</span>
                </label>
                <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                              focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition
                              @error('date') border-red-400 bg-red-50 @enderror">
                @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="location" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Location</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}"
                       placeholder="e.g. Club Range, Indoor Hall"
                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                              focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
            </div>

            <div>
                <label for="focus_area" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Focus Area</label>
                <input type="text" id="focus_area" name="focus_area" value="{{ old('focus_area') }}"
                       placeholder="e.g. Stance, Aiming, Release"
                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                              focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
            </div>

            <div>
                <label for="duration_minutes" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Duration (minutes)</label>
                <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes') }}"
                       min="1" max="1440" placeholder="e.g. 90"
                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                              focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition">
            </div>

            <div class="sm:col-span-2">
                <label for="notes" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Session notes, observations..."
                          class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:bg-white outline-none transition resize-none">{{ old('notes') }}</textarea>
            </div>

        </div>
    </div>

    {{-- Shooting Assignment --}}
    @if($roundTypes->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
         x-data="{ roundSelected: '{{ old('round_type_id', '') }}' }">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
            <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Shooting Assignment <span class="text-xs font-normal text-gray-400">(optional)</span></h2>
                <p class="text-xs text-gray-500">Assign a round type — an archery session will be created for each attendee</p>
            </div>
        </div>
        <div class="p-6 space-y-4">

            <div>
                <label for="round_type_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Round Type</label>
                <select id="round_type_id" name="round_type_id" x-model="roundSelected"
                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    <option value="">— No assignment —</option>
                    @foreach($roundTypes as $category => $types)
                    <optgroup label="{{ ucfirst($category) }}">
                        @foreach($types as $rt)
                        <option value="{{ $rt->id }}"
                                {{ old('round_type_id') == $rt->id ? 'selected' : '' }}>
                            {{ $rt->name }}
                            @if($rt->distance_meters) ({{ $rt->distance_meters }}m) @endif
                            @if($rt->target_face_cm) · {{ $rt->target_face_cm }}cm face @endif
                        </option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>

            <div x-show="roundSelected !== ''" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="distance_meters" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Distance Override (m)
                    </label>
                    <input type="number" id="distance_meters" name="distance_meters"
                           value="{{ old('distance_meters') }}"
                           min="1" max="500" placeholder="Leave blank to use round default"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>
                <div>
                    <label for="target_face_cm" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Target Face Override (cm)
                    </label>
                    <input type="number" id="target_face_cm" name="target_face_cm"
                           value="{{ old('target_face_cm') }}"
                           min="10" placeholder="Leave blank to use round default"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                </div>
                <p class="sm:col-span-2 text-xs text-gray-400">
                    Overrides apply to the created archer sessions only. Leave blank to use the round type's defaults.
                </p>
            </div>

        </div>
    </div>
    @endif

    {{-- Elimination Matches --}}
    @php
        $initPairs = json_encode(
            collect(old('em_pairs', []))
                ->map(fn($p) => [
                    'a'     => $p['archer_a_id']   ?? '',
                    'b'     => $p['archer_b_id']   ?? '',
                    'cat'   => $p['category']      ?? 'outdoor',
                    'typeA' => $p['archer_a_type'] ?? 'registered',
                    'typeB' => $p['archer_b_type'] ?? 'registered',
                    'nameA' => $p['archer_a_name'] ?? '',
                    'nameB' => $p['archer_b_name'] ?? '',
                ])
                ->values()->toArray()
        );
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
         x-data="{
             pairs: {!! $initPairs !!},
             addPair() { this.pairs.push({ a: '', b: '', cat: 'outdoor', typeA: 'registered', typeB: 'registered', nameA: '', nameB: '' }); },
             removePair(i) { this.pairs.splice(i, 1); }
         }">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #fdf4ff, #fae8ff);">
            <span class="h-8 w-8 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Elimination Matches <span class="text-xs font-normal text-gray-400">(optional)</span></h2>
                <p class="text-xs text-gray-500">Assign head-to-head match pairs — registered or guest archers</p>
            </div>
        </div>
        <div class="p-6 space-y-3">

            <template x-for="(pair, i) in pairs" :key="i">
                <div class="border border-purple-200 rounded-xl p-4 relative"
                     style="background: rgba(250,232,255,0.35);">
                    <button type="button" @click="removePair(i)"
                            class="absolute top-3 right-3 h-6 w-6 flex items-center justify-center rounded-lg transition-colors"
                            style="color:#a855f7;" onmouseover="this.style.background='#fae8ff'" onmouseout="this.style.background='transparent'">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-3"
                       x-text="'Match ' + (i + 1)"></p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">

                        {{-- Archer A --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Archer A</label>
                            <input type="hidden" :name="`em_pairs[${i}][archer_a_type]`" :value="pair.typeA">
                            <div class="flex gap-1.5 mb-2">
                                <button type="button" @click="pair.typeA = 'registered'"
                                        class="flex-1 py-1 rounded-lg text-xs font-bold border-2 transition-all"
                                        :style="pair.typeA === 'registered'
                                            ? 'background:#eef2ff; color:#4338ca; border-color:#4338ca;'
                                            : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                                    Registered
                                </button>
                                <button type="button" @click="pair.typeA = 'guest'"
                                        class="flex-1 py-1 rounded-lg text-xs font-bold border-2 transition-all"
                                        :style="pair.typeA === 'guest'
                                            ? 'background:#fef3c7; color:#92400e; border-color:#f59e0b;'
                                            : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                                    Guest
                                </button>
                            </div>
                            <div x-show="pair.typeA === 'registered'">
                                <select :name="`em_pairs[${i}][archer_a_id]`" x-model="pair.a"
                                        class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                               focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 outline-none transition">
                                    <option value="">— Select Archer —</option>
                                    @foreach($clubArchers as $archer)
                                    <option value="{{ $archer->id }}">{{ $archer->full_name }} ({{ $archer->ref_no }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="pair.typeA === 'guest'">
                                <input type="text" :name="`em_pairs[${i}][archer_a_name]`" x-model="pair.nameA"
                                       placeholder="Enter archer name"
                                       class="block w-full rounded-xl border border-amber-300 bg-amber-50 text-sm py-2.5 px-4
                                              focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 outline-none transition">
                            </div>
                        </div>

                        {{-- Archer B --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Archer B</label>
                            <input type="hidden" :name="`em_pairs[${i}][archer_b_type]`" :value="pair.typeB">
                            <div class="flex gap-1.5 mb-2">
                                <button type="button" @click="pair.typeB = 'registered'"
                                        class="flex-1 py-1 rounded-lg text-xs font-bold border-2 transition-all"
                                        :style="pair.typeB === 'registered'
                                            ? 'background:#ecfdf5; color:#059669; border-color:#059669;'
                                            : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                                    Registered
                                </button>
                                <button type="button" @click="pair.typeB = 'guest'"
                                        class="flex-1 py-1 rounded-lg text-xs font-bold border-2 transition-all"
                                        :style="pair.typeB === 'guest'
                                            ? 'background:#fef3c7; color:#92400e; border-color:#f59e0b;'
                                            : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                                    Guest
                                </button>
                            </div>
                            <div x-show="pair.typeB === 'registered'">
                                <select :name="`em_pairs[${i}][archer_b_id]`" x-model="pair.b"
                                        class="block w-full rounded-xl border border-gray-300 bg-white text-sm py-2.5 px-4
                                               focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 outline-none transition">
                                    <option value="">— Select Archer —</option>
                                    @foreach($clubArchers as $archer)
                                    <option value="{{ $archer->id }}">{{ $archer->full_name }} ({{ $archer->ref_no }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="pair.typeB === 'guest'">
                                <input type="text" :name="`em_pairs[${i}][archer_b_name]`" x-model="pair.nameB"
                                       placeholder="Enter archer name"
                                       class="block w-full rounded-xl border border-amber-300 bg-amber-50 text-sm py-2.5 px-4
                                              focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 outline-none transition">
                            </div>
                        </div>

                    </div>

                    <input :name="`em_pairs[${i}][category]`" type="hidden" :value="pair.cat">
                    <div class="flex gap-2">
                        <button type="button" @click="pair.cat = 'outdoor'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all"
                                :style="pair.cat === 'outdoor'
                                    ? 'background:#ecfdf5; color:#059669; border-color:#059669;'
                                    : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                            Outdoor
                        </button>
                        <button type="button" @click="pair.cat = 'indoor'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all"
                                :style="pair.cat === 'indoor'
                                    ? 'background:#eef2ff; color:#4338ca; border-color:#4338ca;'
                                    : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                            Indoor
                        </button>
                        <button type="button" @click="pair.cat = 'mssm'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all"
                                :style="pair.cat === 'mssm'
                                    ? 'background:#fdf2f8; color:#db2777; border-color:#db2777;'
                                    : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                            MSSM
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="pairs.length === 0"
                 class="py-8 text-center border-2 border-dashed border-purple-200 rounded-xl">
                <p class="text-sm text-gray-400">No match pairs added yet.</p>
            </div>

            <button type="button" @click="addPair()"
                    class="w-full py-2.5 rounded-xl border-2 border-dashed border-purple-300 text-sm font-semibold text-purple-600 hover:bg-purple-50 transition-colors flex items-center justify-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Add Match Pair
            </button>

        </div>
    </div>

    {{-- Attendance --}}
    @if($clubArchers->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
            <span class="h-8 w-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Attendance</h2>
                <p class="text-xs text-gray-500">Select archers who attended this session</p>
            </div>
        </div>
        <div class="p-6" x-data="{ all: false, selected: [] }">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs text-gray-500"><span x-text="selected.length"></span> selected</p>
                <button type="button" @click="all = !all; all ? selected = {{ json_encode($clubArchers->pluck('id')->values()) }} : selected = []"
                        class="text-xs font-medium text-teal-600 hover:text-teal-800 hover:underline">
                    <span x-show="!all">Select All</span>
                    <span x-show="all" x-cloak>Deselect All</span>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($clubArchers as $archer)
                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer transition-all"
                       :class="selected.includes({{ $archer->id }}) ? 'border-emerald-300 bg-emerald-50' : 'hover:bg-gray-50'">
                    <input type="checkbox" name="archer_ids[]" value="{{ $archer->id }}"
                           x-model="selected"
                           :value="{{ $archer->id }}"
                           class="h-4 w-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                    <img src="{{ $archer->photo_url }}" alt="{{ $archer->full_name }}"
                         class="h-7 w-7 rounded-lg object-cover flex-shrink-0">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $archer->full_name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $archer->ref_no }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Submit --}}
    <div class="flex items-center justify-end gap-3 pb-4">
        <a href="{{ route('coaches.training.index', $coach) }}"
           class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
            Cancel
        </a>
        <button type="submit"
                class="px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-95"
                style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
            Create Session
        </button>
    </div>

</div>
</form>
@endsection
