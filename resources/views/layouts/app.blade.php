@php
    try {
        $siteSettings  = \App\Models\Setting::getAllCached();
    } catch (\Throwable) {
        $siteSettings  = [];
    }
    $bodyFont     = $siteSettings['body_font']    ?? 'Inter';
    $headingFont  = $siteSettings['heading_font'] ?? $bodyFont;
    $headingSize  = $siteSettings['heading_size'] ?? '20';
    $logoPath     = !empty($siteSettings['logo'])  ? asset('storage/' . $siteSettings['logo']) : null;
    $fontsToLoad  = array_unique([$bodyFont, $headingFont]);
    $fontsParam   = collect($fontsToLoad)
                        ->map(fn($f) => str_replace(' ', '+', $f) . ':wght@400;500;600;700')
                        ->join('&family=');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Archery Stats')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $fontsParam }}&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: '{{ $bodyFont }}', sans-serif; }
        .page-heading { font-family: '{{ $headingFont }}', sans-serif; font-size: {{ $headingSize }}px; }
    </style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="h-full bg-gray-50">
<div class="flex h-full">

    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 z-30"
           style="background: linear-gradient(180deg, #3730a3 0%, #4338ca 50%, #4f46e5 100%);">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            @if($logoPath)
                <div class="h-10 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.15); padding: 4px;">
                    <img src="{{ $logoPath }}" alt="Logo" class="h-full max-w-[120px] object-contain">
                </div>
            @else
                <div class="h-10 w-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.15);">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 6a4 4 0 100 8 4 4 0 000-8zm0 2a2 2 0 110 4 2 2 0 010-4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base leading-tight">Archery Stats</p>
                    <p class="text-indigo-300 text-xs">Management System</p>
                </div>
            @endif
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
            <p class="px-3 mb-2 text-xs font-semibold text-indigo-300 uppercase tracking-widest">Menu</p>
            @auth
                <a href="{{ route('archers.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                          {{ request()->routeIs('archers.*') ? 'bg-white/20 text-white shadow-sm' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    Archers
                </a>

                @if(auth()->user()->role === 'super_admin')
                    <a href="{{ route('admin.settings') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('admin.settings*') ? 'bg-white/20 text-white shadow-sm' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </a>
                @endif
            @endauth
        </nav>

        {{-- User panel --}}
        @auth
        <div class="px-3 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-3 py-3 rounded-xl" style="background: rgba(255,255,255,0.08);">
                <div class="h-9 w-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                     style="background: rgba(255,255,255,0.2);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-indigo-300 text-xs capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                               text-indigo-200 hover:bg-white/10 hover:text-white transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    Sign out
                </button>
            </form>
        </div>
        @endauth
    </aside>

    {{-- Main area --}}
    <div class="flex-1 lg:pl-64 flex flex-col min-h-screen">

        {{-- Top header --}}
        <header class="sticky top-0 z-20 bg-white border-b border-gray-200" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h1 class="font-bold text-gray-900 page-heading">@yield('header', 'Dashboard')</h1>
                    @hasSection('subheader')
                        <p class="text-sm text-gray-500 mt-0.5">@yield('subheader')</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">@yield('header-actions')</div>
            </div>
        </header>

        {{-- Flash message --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 4500)"
                 class="mx-6 mt-5" x-transition>
                <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3">
                    <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-emerald-800 flex-1">{{ session('success') }}</p>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 text-lg leading-none">&times;</button>
                </div>
            </div>
        @endif

        {{-- Content --}}
        <main class="flex-1 px-6 py-6">
            @yield('content')
        </main>

        <footer class="px-6 py-3 border-t border-gray-100">
            <p class="text-xs text-gray-400">{{ $siteSettings['footer_text'] ?? ('© ' . date('Y') . ' Archery Stats Management System') }}</p>
        </footer>
    </div>
</div>
@stack('scripts')
</body>
</html>
