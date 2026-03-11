{{--
    Contextual help panel — include with:
        @include('partials.help-panel', ['title' => '...', 'items' => [...]])
    or with a slot via the component approach using @slot.

    Params:
        $title  — string, e.g. "How to record a session"
        $items  — array of strings (HTML allowed)
        $color  — optional: 'indigo' (default), 'teal', 'violet'
--}}
@php
    $helpColor  = $color ?? 'indigo';
    $colorMap   = [
        'indigo' => ['bg' => '#eef2ff', 'text' => '#4338ca', 'border' => '#c7d2fe', 'dot' => '#6366f1'],
        'teal'   => ['bg' => '#f0fdfa', 'text' => '#0d9488', 'border' => '#99f6e4', 'dot' => '#14b8a6'],
        'violet' => ['bg' => '#f5f3ff', 'text' => '#7c3aed', 'border' => '#ddd6fe', 'dot' => '#8b5cf6'],
    ];
    $c = $colorMap[$helpColor] ?? $colorMap['indigo'];
@endphp

<div x-data="{ helpOpen: false }" class="mb-5">
    <button type="button"
            @click="helpOpen = !helpOpen"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all w-full sm:w-auto"
            :style="helpOpen
                ? 'background:{{ $c['bg'] }}; color:{{ $c['text'] }}; box-shadow: 0 0 0 1.5px {{ $c['border'] }};'
                : 'background:#f8fafc; color:#64748b;'"
            style="background:#f8fafc; color:#64748b;">
        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
        </svg>
        {{ $title ?? 'How to use this page' }}
        <svg class="h-4 w-4 ml-auto sm:ml-2 transition-transform flex-shrink-0"
             :class="helpOpen ? 'rotate-180' : ''"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
        </svg>
    </button>

    <div x-show="helpOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mt-2 rounded-2xl border p-5"
         style="background:{{ $c['bg'] }}; border-color:{{ $c['border'] }};">
        <ol class="space-y-2.5">
            @foreach($items ?? [] as $item)
            <li class="flex items-start gap-3 text-sm" style="color:#334155;">
                <span class="mt-1.5 h-2 w-2 rounded-full flex-shrink-0" style="background:{{ $c['dot'] }};"></span>
                <span>{!! $item !!}</span>
            </li>
            @endforeach
        </ol>
    </div>
</div>
