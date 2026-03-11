@extends('layouts.app')

@section('title', 'New Session — ' . $archer->full_name)
@section('header', 'New Session')
@section('subheader', $archer->ref_no . ' — ' . $archer->full_name)

@section('header-actions')
    <a href="{{ route('sessions.index', $archer) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')
@php
    // Build a JS-safe map of round_type_id to metadata for Alpine overrides
    $rtMeta = [];
    foreach ($roundTypes as $cat => $rounds) {
        foreach ($rounds as $rt) {
            $rtMeta[$rt->id] = [
                'distance' => $rt->distance_meters,
                'face'     => $rt->target_face_cm,
                'scoring'  => $rt->scoring_system ?? 'standard',
                'name'     => $rt->name,
            ];
        }
    }
    $categories = $roundTypes->keys()->toArray();
    $firstCat   = old('round_type_id')
        ? ($roundTypes->first(fn($rounds) => $rounds->contains('id', (int) old('round_type_id')))?->first()?->category ?? ($categories[0] ?? 'indoor'))
        : ($defaultTab ?? ($categories[0] ?? 'indoor'));
    $catColors = [
        'indoor'  => ['bg' => '#e0e7ff', 'text' => '#4338ca', 'active_bg' => '#4338ca', 'active_text' => '#ffffff', 'border' => '#a5b4fc'],
        'outdoor' => ['bg' => '#d1fae5', 'text' => '#065f46', 'active_bg' => '#059669', 'active_text' => '#ffffff', 'border' => '#6ee7b7'],
        'field'   => ['bg' => '#fef3c7', 'text' => '#92400e', 'active_bg' => '#d97706', 'active_text' => '#ffffff', 'border' => '#fcd34d'],
        '3d'      => ['bg' => '#ffedd5', 'text' => '#7c2d12', 'active_bg' => '#ea580c', 'active_text' => '#ffffff', 'border' => '#fdba74'],
        'mssm'    => ['bg' => '#fce7f3', 'text' => '#9d174d', 'active_bg' => '#db2777', 'active_text' => '#ffffff', 'border' => '#f9a8d4'],
        'bakat'   => ['bg' => '#ccfbf1', 'text' => '#134e4a', 'active_bg' => '#0d9488', 'active_text' => '#ffffff', 'border' => '#5eead4'],
        'custom'  => ['bg' => '#f5f3ff', 'text' => '#5b21b6', 'active_bg' => '#7c3aed', 'active_text' => '#ffffff', 'border' => '#c4b5fd'],
    ];
    $disciplineColors = [
        'recurve'  => ['bg' => '#dbeafe', 'text' => '#1d4ed8'],
        'compound' => ['bg' => '#dcfce7', 'text' => '#166534'],
        'barebow'  => ['bg' => '#fef9c3', 'text' => '#854d0e'],
        'field'    => ['bg' => '#fef3c7', 'text' => '#92400e'],
        '3d'       => ['bg' => '#ffedd5', 'text' => '#9a3412'],
        'clout'    => ['bg' => '#f3e8ff', 'text' => '#6b21a8'],
        'longbow'  => ['bg' => '#f1f5f9', 'text' => '#475569'],
    ];
    $scoringLabels = [
        'standard' => 'X · 10–1 · M',
        'compound' => 'X · 10–6 · M',
        'reduced'  => 'X · 10–5 · M',
        'field'    => 'X(6) · 6–1 · M',
        '3d'       => '20 · 17 · 10 · M',
        'clout'    => '5–1 · M',
    ];
@endphp

@include('partials.help-panel', [
    'title' => 'How to record a session',
    'color' => 'indigo',
    'items' => [
        'Select the <strong>session date</strong> at the top.',
        'Choose a <strong>predefined round</strong> from the tabs (Indoor, Outdoor, Field, 3D, etc.), or switch to the <strong>Custom</strong> tab to build your own.',
        'For a <strong>custom round</strong>: pick your <strong>discipline/division</strong> first (Recurve, Compound, etc.), then click <strong>+ Add Segment</strong> for each distance. Set distance (m), target face size (cm), number of ends, and arrows per end. The scoring system is auto-suggested — you can change it manually.',
        'Optionally toggle <strong>Competition</strong> to mark this as a competition round.',
        'Click <strong>Create Session &amp; Start Scoring</strong> to open the scorecard.',
        '<strong>Scoring systems:</strong> Complete (1–10+X) — standard recurve/barebow; Reduced (5–10+X) — WA compound; Field (1–6) — field archery; 3D (20/17/10) — 3D archery.',
    ],
])

<div class="max-w-2xl mx-auto"
     x-data="{
         activeTab:        '{{ $firstCat }}',
         selectedId:       {{ old('round_type_id') ? (int) old('round_type_id') : ($defaultRoundTypeId ?? 'null') }},
         isComp:           {{ old('is_competition') ? 'true' : 'false' }},
         roundMeta:        {{ Js::from($rtMeta) }},
         archerDiscipline: '{{ $archerDiscipline }}',

         customSegments: [
             { distance: '', face: '', scoring: 'standard', num_ends: 6, arrows_per_end: 6 }
         ],

         get isCustom() { return this.activeTab === 'custom'; },

         get selected() { return this.selectedId ? this.roundMeta[this.selectedId] : null; },
         get distPlaceholder() {
             if (!this.selected) return 'Distance (m)';
             return this.selected.distance ? this.selected.distance + 'm (default)' : 'Not specified';
         },
         get facePlaceholder() {
             if (!this.selected) return 'Target face (cm)';
             return this.selected.face ? this.selected.face + 'cm (default)' : 'Not specified';
         },

         get customTotalEnds() {
             return this.customSegments.reduce((s, seg) => s + (parseInt(seg.num_ends) || 0), 0);
         },
         get customArrowsPerEnd() {
             return parseInt(this.customSegments[0]?.arrows_per_end) || 6;
         },
         get customTotalArrows() {
             return this.customSegments.reduce((s, seg) => {
                 return s + (parseInt(seg.num_ends) || 0) * (parseInt(seg.arrows_per_end) || 6);
             }, 0);
         },

         scoringForFace(face, discipline) {
             if (discipline === 'field') return 'field';
             if (discipline === '3d') return '3d';
             face = parseInt(face);
             if (!face) return 'standard';
             if (discipline === 'compound') return 'reduced';
             if (face === 80) return 'reduced';
             return 'standard';
         },

         onFaceChange(i) {
             const f = parseInt(this.customSegments[i].face);
             if (f) {
                 this.customSegments[i].scoring = this.scoringForFace(f, this.archerDiscipline);
             }
         },

         addSegment() {
             const last = this.customSegments[this.customSegments.length - 1];
             this.customSegments.push({
                 distance:       last ? last.distance : '',
                 face:           last ? last.face     : '',
                 scoring:        last ? last.scoring  : 'standard',
                 num_ends:       6,
                 arrows_per_end: last ? last.arrows_per_end : 6,
             });
         },
         removeSegment(i) {
             if (this.customSegments.length > 1) this.customSegments.splice(i, 1);
         },
     }">

    <form method="POST" action="{{ route('sessions.store', $archer) }}">
        @csrf

        {{-- Hidden custom-round inputs, submitted only when isCustom --}}
        <template x-if="isCustom">
            <div>
                <input type="hidden" name="is_custom" value="1">
                <input type="hidden" name="custom_discipline" :value="archerDiscipline">
                <template x-for="(seg, i) in customSegments" :key="i">
                    <div>
                        <input type="hidden" :name="`custom_segments[${i}][distance]`"        :value="seg.distance">
                        <input type="hidden" :name="`custom_segments[${i}][face]`"             :value="seg.face">
                        <input type="hidden" :name="`custom_segments[${i}][scoring]`"          :value="seg.scoring">
                        <input type="hidden" :name="`custom_segments[${i}][num_ends]`"         :value="seg.num_ends">
                        <input type="hidden" :name="`custom_segments[${i}][arrows_per_end]`"   :value="seg.arrows_per_end">
                    </div>
                </template>
            </div>
        </template>

        <div class="space-y-6">

            {{-- Round Type --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                     style="background: linear-gradient(135deg, #f8faff, #f0f4ff);">
                    <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Round Type</h2>
                        <p class="text-xs text-gray-500">Select a predefined format or build a custom round</p>
                    </div>
                </div>

                {{-- Category Tabs --}}
                <div class="flex border-b border-gray-100 overflow-x-auto">
                    @foreach($categories as $cat)
                    @php $c = $catColors[$cat] ?? $catColors['indoor']; @endphp
                    <button type="button"
                            @click="activeTab = '{{ $cat }}'"
                            :class="activeTab === '{{ $cat }}'
                                ? 'border-b-2 font-semibold text-xs'
                                : 'text-gray-500 hover:text-gray-700 text-xs font-medium'"
                            :style="activeTab === '{{ $cat }}'
                                ? 'border-color: {{ $c['active_bg'] }}; color: {{ $c['active_bg'] }}; background: {{ $c['bg'] }};'
                                : ''"
                            class="flex-shrink-0 px-4 py-3 transition-colors capitalize">
                        {{ $cat === '3d' ? '3D' : ($cat === 'mssm' ? 'MSSM' : ($cat === 'bakat' ? 'Bakat Kebangsaan' : ucfirst($cat))) }}
                    </button>
                    @endforeach
                    {{-- Custom Tab (always shown) --}}
                    @php $cc = $catColors['custom']; @endphp
                    <button type="button"
                            @click="activeTab = 'custom'"
                            :class="activeTab === 'custom'
                                ? 'border-b-2 font-semibold text-xs'
                                : 'text-gray-500 hover:text-gray-700 text-xs font-medium'"
                            :style="activeTab === 'custom'
                                ? 'border-color: {{ $cc['active_bg'] }}; color: {{ $cc['active_bg'] }}; background: {{ $cc['bg'] }};'
                                : ''"
                            class="flex-shrink-0 px-4 py-3 transition-colors">
                        Custom
                    </button>
                </div>

                <div class="p-5">
                    {{-- Predefined Round Cards --}}
                    @foreach($roundTypes as $category => $rounds)
                    @php $c = $catColors[$category] ?? $catColors['indoor']; @endphp
                    <div x-show="activeTab === '{{ $category }}'"
                         style="{{ $category !== $firstCat ? 'display:none' : '' }}">
                        <div class="grid grid-cols-1 gap-2.5">
                            @foreach($rounds as $rt)
                            @php
                                $disc = $rt->discipline ?? null;
                                $dc = $disc ? ($disciplineColors[$disc] ?? ['bg'=>'#f1f5f9','text'=>'#475569']) : null;
                                $scoring = $rt->scoring_system ?? 'standard';
                                $scoringLabel = $scoringLabels[$scoring] ?? $scoring;
                                $segments  = $rt->distance_segments;
                                $distLabel = ($segments && count($segments) > 1)
                                    ? collect($segments)->pluck('distance')->map(fn($d) => $d . 'm')->join(' / ')
                                    : ($rt->distance_meters ? $rt->distance_meters . 'm' : '—');
                                $faceLabel = $rt->target_face_cm ? $rt->target_face_cm . 'cm' : '—';
                            @endphp
                            <label @click="selectedId = {{ $rt->id }}"
                                   :class="selectedId === {{ $rt->id }}
                                       ? 'ring-2'
                                       : 'border-gray-200 bg-gray-50 hover:border-gray-300'"
                                   :style="selectedId === {{ $rt->id }}
                                       ? 'border-color: {{ $c['border'] }}; background: {{ $c['bg'] }}; --tw-ring-color: {{ $c['border'] }};'
                                       : ''"
                                   class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all">
                                <input type="radio" name="round_type_id" value="{{ $rt->id }}"
                                       @checked(old('round_type_id') ? old('round_type_id') == $rt->id : ($defaultRoundTypeId && $defaultRoundTypeId == $rt->id))
                                       class="mt-0.5 h-4 w-4 flex-shrink-0 border-gray-300 focus:ring-2"
                                       :style="'accent-color: {{ $c['active_bg'] }}'">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-semibold text-gray-900">{{ $rt->name }}</p>
                                        @if($disc && $dc)
                                        <span class="text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-md"
                                              style="background: {{ $dc['bg'] }}; color: {{ $dc['text'] }};">
                                            {{ $disc }}
                                        </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 mt-1 flex-wrap">
                                        <span class="text-xs text-gray-500">
                                            {{ $rt->num_ends }} ends × {{ $rt->arrows_per_end }} arrow{{ $rt->arrows_per_end > 1 ? 's' : '' }}
                                            <span class="text-gray-400 mx-1">·</span>
                                            {{ $rt->num_ends * $rt->arrows_per_end }} total
                                        </span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-xs text-gray-500">
                                            <span class="font-medium">{{ $distLabel }}</span>
                                            <span class="text-gray-400 mx-1">·</span>
                                            <span class="font-medium">{{ $faceLabel }}</span>
                                        </span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-[11px] font-mono text-gray-400">{{ $scoringLabel }}</span>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    {{-- Custom Round Builder --}}
                    <div x-show="isCustom" style="display:none">

                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Round Name <span class="normal-case font-normal text-gray-400">(optional)</span>
                            </label>
                            <input type="text" name="custom_name"
                                   value="{{ old('custom_name') }}"
                                   placeholder="e.g. Training Round — Mixed Distance"
                                   class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                          focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:bg-white outline-none transition">
                        </div>

                        {{-- Discipline picker --}}
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Discipline
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="disc in ['recurve','compound','barebow','field','3d']" :key="disc">
                                    <button type="button"
                                            @click="archerDiscipline = disc; customSegments.forEach((s,i) => { if (s.face) onFaceChange(i); })"
                                            :class="archerDiscipline === disc
                                                ? 'ring-2 ring-violet-400 bg-violet-50 text-violet-700 font-semibold'
                                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                            class="px-3 py-1.5 rounded-lg text-xs capitalize transition-all"
                                            x-text="disc === '3d' ? '3D' : (disc.charAt(0).toUpperCase() + disc.slice(1))">
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Column headings --}}
                        <div class="grid gap-2 px-1 mb-1" style="grid-template-columns: 1fr 1fr 1.4fr 70px 70px 32px;">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Distance (m)</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Face (cm)</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Scoring System</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Ends</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Arr/End</p>
                            <p></p>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(seg, i) in customSegments" :key="i">
                                <div class="grid gap-2 items-center" style="grid-template-columns: 1fr 1fr 1.4fr 70px 70px 32px;">
                                    <select x-model="seg.distance"
                                            class="w-full rounded-lg border border-gray-300 bg-gray-50 text-sm py-2 px-2
                                                   focus:border-violet-400 focus:ring-2 focus:ring-violet-400/20 focus:bg-white outline-none transition">
                                        <option value="">— choose distance —</option>
                                        <option value="90">90m</option>
                                        <option value="70">70m</option>
                                        <option value="60">60m</option>
                                        <option value="50">50m</option>
                                        <option value="40">40m</option>
                                        <option value="30">30m</option>
                                        <option value="25">25m</option>
                                        <option value="20">20m</option>
                                        <option value="18">18m</option>
                                        <option value="15">15m</option>
                                        <option value="10">10m</option>
                                        <option value="5">5m</option>
                                    </select>
                                    <input type="text" inputmode="numeric"
                                           x-model="seg.face"
                                           @input="onFaceChange(i)"
                                           placeholder="e.g. 122"
                                           class="w-full rounded-lg border border-gray-300 bg-gray-50 text-sm py-2 px-3
                                                  focus:border-violet-400 focus:ring-2 focus:ring-violet-400/20 focus:bg-white outline-none transition">
                                    <select x-model="seg.scoring"
                                            class="w-full rounded-lg border border-gray-300 bg-gray-50 text-sm py-2 px-2
                                                   focus:border-violet-400 focus:ring-2 focus:ring-violet-400/20 focus:bg-white outline-none transition">
                                        <option value="standard">Complete (1 – 10 +X)</option>
                                        <option value="reduced">Reduced (5 – 10 +X)</option>
                                        <option value="six_ring">Reduced (6 – 10 +X)</option>
                                        <option value="field">Field (1 – 6)</option>
                                        <option value="standard_x11">Complete (1 – 10 =X=11)</option>
                                        <option value="six_ring_x11">Reduced (6 – 10 +X=11)</option>
                                    </select>
                                    <input type="number" min="1" max="24"
                                           x-model="seg.num_ends"
                                           class="w-full rounded-lg border border-gray-300 bg-gray-50 text-sm py-2 px-2
                                                  focus:border-violet-400 focus:ring-2 focus:ring-violet-400/20 focus:bg-white outline-none transition text-center">
                                    <input type="number" min="1" max="12"
                                           x-model="seg.arrows_per_end"
                                           class="w-full rounded-lg border border-gray-300 bg-gray-50 text-sm py-2 px-2
                                                  focus:border-violet-400 focus:ring-2 focus:ring-violet-400/20 focus:bg-white outline-none transition text-center">
                                    <button type="button" @click="removeSegment(i)"
                                            :disabled="customSegments.length <= 1"
                                            :class="customSegments.length <= 1 ? 'opacity-30 cursor-not-allowed' : 'hover:text-red-600'"
                                            class="h-8 w-8 flex items-center justify-center rounded-lg text-gray-400 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="addSegment()"
                                class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-violet-700 hover:text-violet-900 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Add Ends
                        </button>

                        {{-- Summary --}}
                        <div class="mt-4 flex items-center gap-2 text-xs text-violet-700 bg-violet-50 border border-violet-100 rounded-xl px-4 py-2.5">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                            </svg>
                            <span>
                                Total: <strong x-text="customTotalEnds"></strong> ends
                                &times; <strong x-text="customArrowsPerEnd"></strong> arrows
                                = <strong x-text="customTotalArrows"></strong> arrows
                            </span>
                        </div>

                        <p class="mt-2 text-[11px] text-gray-400">
                            Scoring is auto-suggested from face size + discipline. Override each dropdown as needed.
                        </p>
                    </div>
                </div>

                @error('round_type_id')
                <p class="px-5 pb-4 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @error('custom_segments')
                <p class="px-5 pb-4 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Distance / Target Face Overrides (predefined rounds only) --}}
            <div x-show="selectedId !== null && !isCustom" x-cloak
                 class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                     style="background: linear-gradient(135deg, #fafaf9, #f5f5f4);">
                    <span class="h-8 w-8 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Distance &amp; Target Override</h2>
                        <p class="text-xs text-gray-500">Leave blank to use round type defaults</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <label for="distance_meters" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Distance (m)
                        </label>
                        <input type="number" id="distance_meters" name="distance_meters"
                               value="{{ old('distance_meters') }}"
                               min="1" max="300"
                               :placeholder="distPlaceholder"
                               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition
                                      @error('distance_meters') border-red-400 bg-red-50 @enderror">
                        @error('distance_meters')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="target_face_cm" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Target Face (cm)
                        </label>
                        <input type="number" id="target_face_cm" name="target_face_cm"
                               value="{{ old('target_face_cm') }}"
                               min="1"
                               :placeholder="facePlaceholder"
                               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition
                                      @error('target_face_cm') border-red-400 bg-red-50 @enderror">
                        @error('target_face_cm')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Session Details --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                     style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                    <span class="h-8 w-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Session Details</h2>
                        <p class="text-xs text-gray-500">Date, location and conditions</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>
                        <label for="date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Date <span class="text-red-500 normal-case font-normal">*</span>
                        </label>
                        <input type="date" id="date" name="date"
                               value="{{ old('date', date('Y-m-d')) }}"
                               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition
                                      @error('date') border-red-400 bg-red-50 @enderror">
                        @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="weather" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Weather / Condition</label>
                        <select id="weather" name="weather"
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                            <option value="">— Select —</option>
                            <option value="indoor"  @selected(old('weather') === 'indoor')>Indoor</option>
                            <option value="sunny"   @selected(old('weather') === 'sunny')>Sunny</option>
                            <option value="cloudy"  @selected(old('weather') === 'cloudy')>Cloudy</option>
                            <option value="windy"   @selected(old('weather') === 'windy')>Windy</option>
                            <option value="rain"    @selected(old('weather') === 'rain')>Rain</option>
                            <option value="other"   @selected(old('weather') === 'other')>Other</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="location" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Location</label>
                        <input type="text" id="location" name="location"
                               value="{{ old('location') }}"
                               placeholder="e.g. Pusat Memanah Bukit Jalil"
                               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_competition" value="1"
                                   @checked(old('is_competition'))
                                   x-model="isComp"
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <span class="text-sm font-semibold text-gray-700">This is a competition / official tournament</span>
                        </label>
                    </div>

                    <div class="sm:col-span-2" x-show="isComp" x-cloak>
                        <label for="competition_name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Competition Name</label>
                        <input type="text" id="competition_name" name="competition_name"
                               value="{{ old('competition_name') }}"
                               placeholder="e.g. National Open 2025"
                               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Notes</label>
                        <textarea id="notes" name="notes" rows="2"
                                  placeholder="Any notes about this session..."
                                  class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                         focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition resize-none">{{ old('notes') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 pb-4">
                <a href="{{ route('sessions.index', $archer) }}"
                   class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700
                          hover:bg-gray-50 transition-colors shadow-sm">
                    Cancel
                </a>
                <button type="submit"
                        class="px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-95"
                        style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                    Start Session →
                </button>
            </div>

        </div>
    </form>
</div>
@endsection
