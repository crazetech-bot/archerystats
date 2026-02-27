@extends('layouts.app')

@section('title', 'New Elimination Match')
@section('header', 'New Elimination Match')
@section('subheader', 'World Archery Elimination Match')

@section('content')
<div x-data="{
    activeTab: 'outdoor',
    category: 'outdoor',
    format: '{{ old('format', 'set_point') }}',
    typeA: '{{ old('archer_a_type', 'registered') }}',
    typeB: '{{ old('archer_b_type', 'registered') }}',
    setTab(tab) {
        this.activeTab = tab;
        this.category = tab;
        if (tab !== 'outdoor') this.format = 'set_point';
    },
    get isCompound() {
        return this.category === 'outdoor' && this.format === 'cumulative';
    }
}" class="max-w-2xl mx-auto">

    <form method="POST" action="{{ route('elimination-matches.store') }}">
        @csrf
        <input type="hidden" name="category" x-model="category">
        <input type="hidden" name="format" x-model="format">
        <input type="hidden" name="distance_m" :value="isCompound ? '50' : ''">

        {{-- Category Tabs --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 mb-5 overflow-hidden">
            <div class="px-5 py-4" style="background: linear-gradient(135deg, #1e293b, #0f172a); border-bottom: 1px solid rgba(255,255,255,0.07);">
                <h2 class="text-white font-bold text-sm section-header">Match Category</h2>
                <p class="text-slate-400 text-xs mt-0.5">Select the venue type for this match</p>
            </div>
            <div class="p-5">
                <div class="flex gap-3 flex-wrap">
                    @php
                        $tabs = [
                            'outdoor' => ['label' => 'Outdoor', 'color' => '#059669', 'bg' => '#ecfdf5', 'desc' => 'Outdoor range, standard scoring'],
                            'indoor'  => ['label' => 'Indoor',  'color' => '#4338ca', 'bg' => '#eef2ff', 'desc' => 'Indoor range, standard scoring'],
                            'mssm'    => ['label' => 'MSSM',    'color' => '#db2777', 'bg' => '#fdf2f8', 'desc' => 'Malaysian School Sports match'],
                        ];
                    @endphp

                    @foreach($tabs as $key => $tab)
                        <button type="button"
                                @click="setTab('{{ $key }}')"
                                class="flex-1 min-w-[120px] px-4 py-3 rounded-xl border-2 text-left transition-all"
                                :style="activeTab === '{{ $key }}'
                                    ? 'background: {{ $tab['bg'] }}; border-color: {{ $tab['color'] }}; color: {{ $tab['color'] }};'
                                    : 'background: #f8fafc; border-color: #e2e8f0; color: #64748b;'">
                            <span class="block font-bold text-sm">{{ $tab['label'] }}</span>
                            <span class="block text-xs mt-0.5 opacity-70">{{ $tab['desc'] }}</span>
                        </button>
                    @endforeach
                </div>

                @error('category')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Format Selector (outdoor only) --}}
        <div x-show="activeTab === 'outdoor'"
             class="rounded-2xl bg-white shadow-sm border border-gray-100 mb-5 overflow-hidden">
            <div class="px-5 py-4" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); border-bottom: 1px solid rgba(255,255,255,0.07);">
                <h2 class="text-white font-bold text-sm section-header">Match Format</h2>
                <p class="text-purple-200 text-xs mt-0.5">Select the scoring format for this outdoor match</p>
            </div>
            <div class="p-5">
                <div class="flex gap-3">
                    <button type="button"
                            @click="format = 'set_point'"
                            class="flex-1 px-4 py-3 rounded-xl border-2 text-left transition-all"
                            :style="format === 'set_point'
                                ? 'background:#eff6ff; border-color:#4338ca; color:#4338ca;'
                                : 'background:#f8fafc; border-color:#e2e8f0; color:#64748b;'">
                        <span class="block font-bold text-sm">Set-Point (Recurve)</span>
                        <span class="block text-xs mt-0.5 opacity-70">First to 6 set points · Valid: X, 10–1, M</span>
                    </button>
                    <button type="button"
                            @click="format = 'cumulative'"
                            class="flex-1 px-4 py-3 rounded-xl border-2 text-left transition-all"
                            :style="format === 'cumulative'
                                ? 'background:#fffbeb; border-color:#d97706; color:#92400e;'
                                : 'background:#f8fafc; border-color:#e2e8f0; color:#64748b;'">
                        <span class="block font-bold text-sm">Compound Cumulative</span>
                        <span class="block text-xs mt-0.5 opacity-70">Highest total after 15 arrows · Valid: X, 10–5, M</span>
                    </button>
                </div>

                <div x-show="isCompound"
                     class="mt-3 rounded-xl px-4 py-3 text-xs"
                     style="background:#fffbeb; border: 1px solid #fcd34d;">
                    <p class="font-bold text-amber-700">80 cm 6-ring face &nbsp;·&nbsp; Distance: <strong>50 m</strong> (auto-set)</p>
                    <p class="text-amber-600 mt-0.5">Valid: X · 10 · 9 · 8 · 7 · 6 · 5 · M &nbsp;|&nbsp; Scores 1–4 are <strong>not valid</strong> on this face</p>
                </div>
            </div>
        </div>

        {{-- Archer Selection --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 mb-5 overflow-hidden">
            <div class="px-5 py-4" style="background: linear-gradient(135deg, #f59e0b, #d97706); border-bottom: 1px solid rgba(0,0,0,0.05);">
                <h2 class="text-white font-bold text-sm section-header">Archers</h2>
                <p class="text-amber-100 text-xs mt-0.5">Select a registered archer or enter a guest name manually</p>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- Archer A --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Archer A</label>

                    {{-- Type toggle --}}
                    <div class="flex gap-1.5 mb-3">
                        <input type="hidden" name="archer_a_type" x-model="typeA">
                        <button type="button" @click="typeA = 'registered'"
                                class="flex-1 py-1.5 rounded-lg text-xs font-bold transition-all border-2"
                                :style="typeA === 'registered'
                                    ? 'background:#eef2ff; color:#4338ca; border-color:#4338ca;'
                                    : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                            Registered
                        </button>
                        <button type="button" @click="typeA = 'guest'"
                                class="flex-1 py-1.5 rounded-lg text-xs font-bold transition-all border-2"
                                :style="typeA === 'guest'
                                    ? 'background:#fef3c7; color:#92400e; border-color:#f59e0b;'
                                    : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                            Guest
                        </button>
                    </div>

                    <div x-show="typeA === 'registered'">
                        <select name="archer_a_id"
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-slate-800
                                       focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                            <option value="">— Select Archer A —</option>
                            @foreach($archers as $archer)
                                <option value="{{ $archer->id }}"
                                    {{ old('archer_a_id') == $archer->id ? 'selected' : '' }}>
                                    {{ $archer->name }} ({{ $archer->ref_no }})
                                </option>
                            @endforeach
                        </select>
                        @error('archer_a_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="typeA === 'guest'">
                        <input type="text" name="archer_a_name"
                               value="{{ old('archer_a_name') }}"
                               placeholder="Enter archer name"
                               class="w-full rounded-xl border border-amber-300 bg-amber-50 px-3 py-2.5 text-sm text-slate-800
                                      focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent">
                        @error('archer_a_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Archer B --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Archer B</label>

                    {{-- Type toggle --}}
                    <div class="flex gap-1.5 mb-3">
                        <input type="hidden" name="archer_b_type" x-model="typeB">
                        <button type="button" @click="typeB = 'registered'"
                                class="flex-1 py-1.5 rounded-lg text-xs font-bold transition-all border-2"
                                :style="typeB === 'registered'
                                    ? 'background:#ecfdf5; color:#059669; border-color:#059669;'
                                    : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                            Registered
                        </button>
                        <button type="button" @click="typeB = 'guest'"
                                class="flex-1 py-1.5 rounded-lg text-xs font-bold transition-all border-2"
                                :style="typeB === 'guest'
                                    ? 'background:#fef3c7; color:#92400e; border-color:#f59e0b;'
                                    : 'background:#f8fafc; color:#94a3b8; border-color:#e2e8f0;'">
                            Guest
                        </button>
                    </div>

                    <div x-show="typeB === 'registered'">
                        <select name="archer_b_id"
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-slate-800
                                       focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                            <option value="">— Select Archer B —</option>
                            @foreach($archers as $archer)
                                <option value="{{ $archer->id }}"
                                    {{ old('archer_b_id') == $archer->id ? 'selected' : '' }}>
                                    {{ $archer->name }} ({{ $archer->ref_no }})
                                </option>
                            @endforeach
                        </select>
                        @error('archer_b_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="typeB === 'guest'">
                        <input type="text" name="archer_b_name"
                               value="{{ old('archer_b_name') }}"
                               placeholder="Enter archer name"
                               class="w-full rounded-xl border border-amber-300 bg-amber-50 px-3 py-2.5 text-sm text-slate-800
                                      focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent">
                        @error('archer_b_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- Match Details --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 mb-5 overflow-hidden">
            <div class="px-5 py-4" style="background: linear-gradient(135deg, #1e293b, #0f172a); border-bottom: 1px solid rgba(255,255,255,0.07);">
                <h2 class="text-white font-bold text-sm section-header">Match Details</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" required
                           value="{{ old('date', date('Y-m-d')) }}"
                           class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-slate-800
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    @error('date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Location</label>
                    <input type="text" name="location"
                           value="{{ old('location') }}"
                           placeholder="e.g. PBSM Range, KL"
                           class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-slate-800
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Competition Name</label>
                    <input type="text" name="competition_name"
                           value="{{ old('competition_name') }}"
                           placeholder="e.g. MSSM State Finals 2026"
                           class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-slate-800
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                </div>
            </div>
        </div>

        {{-- Rules Reminder (format-aware) --}}
        <div x-show="!isCompound"
             class="rounded-xl border px-4 py-3 mb-5"
             style="background: #eff6ff; border-color: #bfdbfe;">
            <p class="text-xs font-bold text-indigo-700 mb-1">Set System Rules</p>
            <ul class="text-xs text-indigo-600 space-y-0.5">
                <li>· Max 5 sets &nbsp;·&nbsp; 3 arrows per archer per set</li>
                <li>· Set winner: 2 pts &nbsp;·&nbsp; Tie: 1 pt each</li>
                <li>· First to <strong>6 set points</strong> wins the match</li>
                <li>· If 5–5 after set 5 → Shoot-off (1 arrow each, closest to center wins)</li>
            </ul>
        </div>

        <div x-show="isCompound"
             class="rounded-xl border px-4 py-3 mb-5"
             style="background: #fffbeb; border-color: #fcd34d;">
            <p class="text-xs font-bold text-amber-700 mb-1">Compound Cumulative Rules</p>
            <ul class="text-xs text-amber-700 space-y-0.5">
                <li>· 5 ends &nbsp;·&nbsp; 3 arrows per archer per end &nbsp;·&nbsp; 15 arrows total (max 150 pts)</li>
                <li>· Highest cumulative score wins</li>
                <li>· Valid: X(=10) · 10 · 9 · 8 · 7 · 6 · 5 · M(=0) &nbsp;·&nbsp; Scores 1–4 are <strong>invalid</strong></li>
                <li>· If tied after 5 ends → Shoot-off (1 arrow, nearest to center if equal)</li>
            </ul>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 py-3 rounded-xl text-sm font-bold text-white shadow-sm"
                    style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                Create Match &amp; Open Scorecard
            </button>
            <a href="{{ route('elimination-matches.index') }}"
               class="px-5 py-3 rounded-xl text-sm font-bold text-slate-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
