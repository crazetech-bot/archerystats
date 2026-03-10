<section class="hero-gradient text-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-20 text-center">
        @if($clubLogo)
            <img src="{{ $clubLogo }}" alt="{{ $club->name }}"
                 class="w-24 h-24 rounded-2xl object-cover border-4 border-white/30 shadow-xl mx-auto mb-6">
        @else
            <div class="w-24 h-24 rounded-2xl bg-white/20 flex items-center justify-center text-white font-black text-3xl mx-auto mb-6 border-4 border-white/20 shadow-xl">
                {{ strtoupper(substr($club->name, 0, 2)) }}
            </div>
        @endif

        <h1 class="text-4xl sm:text-5xl font-black mb-3 tracking-tight">{{ $club->name }}</h1>

        @if($club->tagline)
            <p class="text-indigo-200 text-xl mb-6 max-w-2xl mx-auto">{{ $club->tagline }}</p>
        @endif

        <div class="flex flex-wrap items-center justify-center gap-4 text-indigo-200 text-sm">
            @if($club->state)
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $club->state }}
                </span>
            @endif
            @if($club->founded_year)
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Est. {{ $club->founded_year }}
                </span>
            @endif
            @if($club->archers_count ?? false)
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    {{ $club->archers_count }} Archers
                </span>
            @endif
        </div>

        @if($sections['cta'])
        @guest
        <div class="mt-10">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-white text-indigo-700 font-bold text-base shadow-lg hover:bg-indigo-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Join {{ $club->name }}
            </a>
        </div>
        @endguest
        @endif
    </div>
</section>
