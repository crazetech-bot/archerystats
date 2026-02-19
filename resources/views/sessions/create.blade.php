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
<div class="max-w-2xl mx-auto">

    <form method="POST" action="{{ route('sessions.store', $archer) }}"
          x-data="{ isCompetition: {{ old('is_competition') ? 'true' : 'false' }} }">
        @csrf

        <div class="space-y-6">

            {{-- Round Type --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                     style="background: linear-gradient(135deg, #f8faff, #f0f4ff);">
                    <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 6a4 4 0 100 8 4 4 0 000-8zm0 2a2 2 0 110 4 2 2 0 010-4z"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Round Type</h2>
                        <p class="text-xs text-gray-500">Select the format for this session</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    @php
                        $grouped = $roundTypes->groupBy('category');
                    @endphp
                    @foreach($grouped as $category => $rounds)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ ucfirst($category) }}</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($rounds as $rt)
                                    <label class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all
                                                  {{ old('round_type_id') == $rt->id ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                                        <input type="radio" name="round_type_id" value="{{ $rt->id }}"
                                               @checked(old('round_type_id') == $rt->id)
                                               class="mt-0.5 h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-400">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $rt->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $rt->num_ends }} ends × {{ $rt->arrows_per_end }} arrows = {{ $rt->num_ends * $rt->arrows_per_end }} total</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @if(!$loop->last)<div class="border-t border-gray-100"></div>@endif
                    @endforeach
                    @error('round_type_id')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
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
                                   x-model="isCompetition"
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <span class="text-sm font-semibold text-gray-700">This is a competition / official tournament</span>
                        </label>
                    </div>

                    <div class="sm:col-span-2" x-show="isCompetition" x-cloak>
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
