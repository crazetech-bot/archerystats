@php
    $colors = [
        'indigo'  => ['bg' => '#eef2ff', 'border' => '#c7d2fe', 'num' => '#4338ca', 'numBg' => '#e0e7ff', 'title' => '#3730a3'],
        'teal'    => ['bg' => '#f0fdfa', 'border' => '#99f6e4', 'num' => '#0d9488', 'numBg' => '#ccfbf1', 'title' => '#0f766e'],
        'violet'  => ['bg' => '#f5f3ff', 'border' => '#ddd6fe', 'num' => '#7c3aed', 'numBg' => '#ede9fe', 'title' => '#6d28d9'],
        'rose'    => ['bg' => '#fff1f2', 'border' => '#fecdd3', 'num' => '#e11d48', 'numBg' => '#ffe4e6', 'title' => '#be123c'],
    ];
    $c = $colors[$color ?? 'indigo'];
@endphp

<div class="rounded-2xl border mb-4 overflow-hidden"
     style="background: {{ $c['bg'] }}; border-color: {{ $c['border'] }};">
    <div class="flex items-center gap-3 px-5 py-4" style="border-bottom: 1px solid {{ $c['border'] }};">
        <span class="h-7 w-7 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0"
              style="background: {{ $c['numBg'] }}; color: {{ $c['num'] }};">{{ $number }}</span>
        <h3 class="font-bold text-sm" style="color: {{ $c['title'] }};">{{ $title }}</h3>
    </div>
    <ol class="px-5 py-4 space-y-2.5">
        @foreach($steps as $step)
        <li class="flex gap-3 text-sm text-slate-700 leading-relaxed">
            <span class="mt-0.5 h-5 w-5 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                  style="background: {{ $c['numBg'] }}; color: {{ $c['num'] }};">{{ $loop->iteration }}</span>
            <span>{!! $step !!}</span>
        </li>
        @endforeach
    </ol>
</div>
