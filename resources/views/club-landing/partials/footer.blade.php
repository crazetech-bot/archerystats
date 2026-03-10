@php
    // Footer always loads platform settings (club_id = NULL)
    $footerSettings = \App\Models\Setting::getAllCached(null);
    $footerText = $footerSettings['footer_text'] ?? null;
@endphp
<footer class="mt-16 border-t border-gray-200 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-gray-500 text-sm">
            @if($clubLogo)
                <img src="{{ $clubLogo }}" alt="" class="w-6 h-6 rounded-md object-cover">
            @endif
            <span class="font-medium text-gray-700">{{ $club->name }}</span>
        </div>
        <div class="text-sm text-gray-400 text-center">
            @if($footerText)
                {!! nl2br(e($footerText)) !!}
            @else
                &copy; {{ date('Y') }} {{ $club->name }}. Powered by Archery Stats.
            @endif
        </div>
    </div>
</footer>
