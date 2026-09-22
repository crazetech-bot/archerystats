@php
    $val = fn($field, $fallback = '') => old($field, $event ? data_get($event, $field) : $fallback);
    $dt  = fn($field) => $event && data_get($event, $field)
        ? \Illuminate\Support\Carbon::parse(data_get($event, $field))->format('Y-m-d\TH:i')
        : old($field);
@endphp

<div class="flex flex-wrap gap-3">
    <div class="flex-1 min-w-64">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Title</label>
        <input type="text" name="title" required maxlength="150" value="{{ $val('title') }}"
               placeholder="e.g. Club Championship 2026"
               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4 focus:border-indigo-500 focus:bg-white outline-none transition @error('title') border-red-400 bg-red-50 @enderror">
        @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="w-40">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Type</label>
        <select name="event_type" class="w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-3 focus:border-indigo-500 focus:bg-white outline-none transition">
            @foreach(\App\Models\ClubEvent::TYPES as $k => $lbl)
                <option value="{{ $k }}" {{ $val('event_type', 'other') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Starts</label>
        <input type="datetime-local" name="starts_at" required value="{{ $dt('starts_at') }}"
               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4 focus:border-indigo-500 focus:bg-white outline-none transition @error('starts_at') border-red-400 bg-red-50 @enderror">
        @error('starts_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Ends <span class="normal-case font-normal text-gray-400">(optional)</span></label>
        <input type="datetime-local" name="ends_at" value="{{ $dt('ends_at') }}"
               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4 focus:border-indigo-500 focus:bg-white outline-none transition @error('ends_at') border-red-400 bg-red-50 @enderror">
        @error('ends_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    @if(! $coachMode)
    <div class="w-44">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Audience</label>
        <select name="audience" class="w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-3 focus:border-indigo-500 focus:bg-white outline-none transition">
            @foreach($composeAudiences as $k => $lbl)
                <option value="{{ $k }}" {{ $val('audience', 'all') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>
    @else
        <input type="hidden" name="audience" value="my_archers">
    @endif
</div>

<div>
    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Location <span class="normal-case font-normal text-gray-400">(optional)</span></label>
    <input type="text" name="location" maxlength="200" value="{{ $val('location') }}"
           placeholder="e.g. National Archery Range, KL"
           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4 focus:border-indigo-500 focus:bg-white outline-none transition">
</div>

<div>
    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Details <span class="normal-case font-normal text-gray-400">(optional)</span></label>
    <textarea name="description" maxlength="5000" rows="3" placeholder="Anything members should know…"
              class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4 focus:border-indigo-500 focus:bg-white outline-none transition">{{ $val('description') }}</textarea>
</div>
