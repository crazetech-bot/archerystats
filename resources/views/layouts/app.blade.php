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
    $fontsToLoad  = array_unique([$bodyFont, $headingFont, 'Barlow']);
    $fontsParam   = collect($fontsToLoad)
                        ->map(fn($f) => str_replace(' ', '+', $f) . ':wght@400;500;600;700;800;900')
                        ->join('&family=');
    // SEO / Open Graph
    $ogSiteName = $siteSettings['seo_site_name']    ?? 'Archery Stats';
    $ogDefDesc  = $siteSettings['seo_description']  ?? 'Malaysian archery performance tracking — sessions, scores, coaches, clubs and state teams.';
    $ogDefImg   = !empty($siteSettings['seo_og_image']) ? asset('storage/' . $siteSettings['seo_og_image']) : ($logoPath ?? '');
    $gaId       = !empty($siteSettings['seo_ga_id'])    ? trim($siteSettings['seo_ga_id'])    : null;
    $gscToken   = !empty($siteSettings['seo_gsc_token']) ? trim($siteSettings['seo_gsc_token']) : null;
    $ogTitle    = $__env->yieldContent('title', $ogSiteName);
    $ogDesc     = $__env->yieldContent('og_description', $ogDefDesc);
    $ogImg      = $__env->yieldContent('og_image', '') ?: $ogDefImg;
    $ogUrl      = url()->current();
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'Archery Stats')</title>
    <meta name="description" content="{{ $ogDesc }}">
    <link rel="canonical" href="{{ $ogUrl }}">
    {{-- Open Graph --}}
    <meta property="og:type"         content="website">
    <meta property="og:site_name"    content="{{ $ogSiteName }}">
    @if($gscToken)<meta name="google-site-verification" content="{{ $gscToken }}">@endif
    <meta property="og:title"        content="{{ $ogTitle }}">
    <meta property="og:description"  content="{{ $ogDesc }}">
    <meta property="og:url"          content="{{ $ogUrl }}">
    @if($ogImg)
    <meta property="og:image"        content="{{ $ogImg }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    @endif
    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDesc }}">
    @if($ogImg)
    <meta name="twitter:image"       content="{{ $ogImg }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $fontsParam }}&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: '{{ $bodyFont }}', sans-serif; background: #f1f5f9; }
        .page-heading { font-family: '{{ $headingFont }}', sans-serif; font-size: {{ $headingSize }}px; }
        .nav-active {
            background: rgba(245, 158, 11, 0.12);
            border-left: 3px solid #f59e0b;
            color: #fbbf24;
        }
        .nav-active svg { color: #fbbf24; }
        .nav-item {
            border-left: 3px solid transparent;
        }
        .stat-card { border-top: 4px solid #f59e0b; }
        .btn-primary {
            background: #f59e0b;
            color: #0f172a;
            font-weight: 700;
            transition: background 0.15s;
        }
        .btn-primary:hover { background: #fbbf24; }
        .btn-navy {
            background: #0f172a;
            color: #fff;
            font-weight: 600;
            transition: background 0.15s;
        }
        .btn-navy:hover { background: #1e293b; }
        .section-header {
            font-family: 'Barlow', sans-serif;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
    </style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/popup.css') }}">
    @stack('head')
    @if($gaId)
    {{-- Google Analytics 4 --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaId }}');
    </script>
    @endif
</head>
<body class="h-full">
<div class="flex h-full" x-data="{ mobileOpen: false }">

    {{-- Mobile overlay backdrop --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileOpen = false"
         class="fixed inset-0 z-40 bg-black/60 lg:hidden"></div>

    {{-- Mobile slide-in sidebar --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 w-72 flex flex-col lg:hidden"
         style="background: #0f172a; transform: translateX(0);">

        {{-- Mobile sidebar header --}}
        <div class="flex items-center justify-between px-5 py-5" style="border-bottom: 1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center gap-3">
                @if($logoPath)
                    <div class="h-10 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0"
                         style="background: rgba(245,158,11,0.15); padding: 4px;">
                        <img src="{{ $logoPath }}" alt="Logo" class="h-full max-w-[120px] object-contain">
                    </div>
                @else
                    <div class="h-9 w-9 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: rgba(245,158,11,0.2);">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.8">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="12" r="6"/>
                            <circle cx="12" cy="12" r="2" fill="#f59e0b" stroke="none"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-black text-sm leading-tight tracking-wide" style="font-family:'Barlow',sans-serif;">ARCHERY STATS</p>
                        <p class="text-xs font-medium" style="color:#f59e0b;">Management System</p>
                    </div>
                @endif
            </div>
            <button @click="mobileOpen = false"
                    class="h-8 w-8 rounded-lg flex items-center justify-center transition-colors"
                    style="color:#94a3b8;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background=''">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile nav links --}}
        <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
            <p class="px-4 mb-3 text-xs font-bold tracking-widest uppercase" style="color:#475569;">Navigation</p>
            @auth
                @if(auth()->user()->role === 'archer' && auth()->user()->archer)
                    <a href="{{ route('archers.show', auth()->user()->archer) }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('archers.show') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        My Profile
                    </a>
                    <a href="{{ route('sessions.index', auth()->user()->archer) }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('sessions.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        My Sessions
                    </a>
                    <a href="{{ route('archers.performance', auth()->user()->archer) }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('archers.performance') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                        </svg>
                        Performance
                    </a>
                    <a href="{{ route('manual') }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('manual') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        User Manual
                    </a>
                @elseif(auth()->user()->role === 'coach' && auth()->user()->coach)
                    {{-- Coach: full coach module nav --}}
                    <a href="{{ route('coaches.show', auth()->user()->coach) }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('coaches.show') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        My Profile
                    </a>
                    <a href="{{ route('coaches.archers.index', auth()->user()->coach) }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('coaches.archers.*') || request()->routeIs('archers.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        Assigned Archers
                    </a>
                    <a href="{{ route('coaches.training.index', auth()->user()->coach) }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('coaches.training.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                        Training Sessions
                    </a>
                    <a href="{{ route('coaches.club-results', auth()->user()->coach) }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('coaches.club-results*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        Club Results
                    </a>
                    <a href="{{ route('elimination-matches.index') }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('elimination-matches.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                        </svg>
                        Elimination Matches
                    </a>
                    <a href="{{ route('manual') }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('manual') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        User Manual
                    </a>
                @else
                    <a href="{{ route('archers.index') }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('archers.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        Archers
                    </a>

                    @if(in_array(auth()->user()->role, ['super_admin', 'club_admin', 'national_team']))
                        <a href="{{ route('coaches.index') }}" @click="mobileOpen = false"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('coaches.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                            </svg>
                            Coaches
                        </a>
                    @endif

                    <a href="{{ route('elimination-matches.index') }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('elimination-matches.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                        </svg>
                        Elimination Matches
                    </a>

                    @if(auth()->user()->role === 'club_admin' && auth()->user()->club)
                        <a href="{{ route('clubs.dashboard', auth()->user()->club) }}" @click="mobileOpen = false"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('clubs.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                            </svg>
                            My Club
                        </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['super_admin', 'state_admin', 'national_team']))
                        <a href="{{ route('clubs.index') }}" @click="mobileOpen = false"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('clubs.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                            </svg>
                            Clubs
                        </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['super_admin', 'state_admin', 'national_team']))
                        <a href="{{ route('state-teams.index') }}" @click="mobileOpen = false"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('state-teams.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            State Teams
                        </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['super_admin', 'national_team']))
                        <a href="{{ route('national-team.index') }}" @click="mobileOpen = false"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('national-team.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                            </svg>
                            National Team
                        </a>
                    @endif

                    @if(auth()->user()->role === 'super_admin')
                        <a href="{{ route('admin.settings') }}" @click="mobileOpen = false"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('admin.settings*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Settings
                        </a>
                    @endif
                    <a href="{{ route('manual') }}" @click="mobileOpen = false"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('manual') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        User Manual
                    </a>
                @endif
            @endauth
        </nav>

        {{-- Mobile user panel --}}
        @auth
        <div class="px-3 py-4" style="border-top: 1px solid rgba(255,255,255,0.07);">
            @if(in_array(auth()->user()->role, ['super_admin', 'club_admin', 'state_admin', 'national_team']))
            <a href="{{ route('live-scoring.realtime') }}" @click="mobileOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-black mb-2 transition-all"
               style="background: linear-gradient(135deg, #7c3aed, #6d28d9); color: #fff;">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background:#f59e0b;"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2" style="background:#f59e0b;"></span>
                </span>
                LIVE SCORING
            </a>
            @endif
            <div class="flex items-center gap-3 px-3 py-3 rounded-xl" style="background: #1e293b;">
                <div class="h-9 w-9 rounded-full flex items-center justify-center text-slate-900 text-sm font-black flex-shrink-0"
                     style="background: #f59e0b;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs font-medium capitalize" style="color:#94a3b8;">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                               text-slate-400 hover:text-red-400 hover:bg-red-400/5 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    Sign out
                </button>
            </form>
        </div>
        @endauth
    </div>

    {{-- Desktop Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 z-30"
           style="background: #0f172a;">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 py-5" style="border-bottom: 1px solid rgba(255,255,255,0.07);">
            @if($logoPath)
                <div class="h-10 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0"
                     style="background: rgba(245,158,11,0.15); padding: 4px;">
                    <img src="{{ $logoPath }}" alt="Logo" class="h-full max-w-[120px] object-contain">
                </div>
            @else
                <div class="h-10 w-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(245,158,11,0.2);">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.8">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="6"/>
                        <circle cx="12" cy="12" r="2" fill="#f59e0b" stroke="none"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-black text-base leading-tight tracking-wide" style="font-family:'Barlow',sans-serif;">ARCHERY STATS</p>
                    <p class="text-xs font-medium" style="color:#f59e0b;">Management System</p>
                </div>
            @endif
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
            <p class="px-4 mb-3 text-xs font-bold tracking-widest uppercase" style="color:#475569;">Navigation</p>
            @auth
                @if(auth()->user()->role === 'archer' && auth()->user()->archer)
                    {{-- Archer: personal links only --}}
                    <a href="{{ route('archers.show', auth()->user()->archer) }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('archers.show') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        My Profile
                    </a>
                    <a href="{{ route('sessions.index', auth()->user()->archer) }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('sessions.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        My Sessions
                    </a>
                    <a href="{{ route('archers.performance', auth()->user()->archer) }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('archers.performance') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                        </svg>
                        Performance
                    </a>
                    <a href="{{ route('manual') }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('manual') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        User Manual
                    </a>
                @elseif(auth()->user()->role === 'coach' && auth()->user()->coach)
                    {{-- Coach: full coach module nav --}}
                    <a href="{{ route('coaches.show', auth()->user()->coach) }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('coaches.show') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        My Profile
                    </a>
                    <a href="{{ route('coaches.archers.index', auth()->user()->coach) }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('coaches.archers.*') || request()->routeIs('archers.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        Assigned Archers
                    </a>
                    <a href="{{ route('coaches.training.index', auth()->user()->coach) }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('coaches.training.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                        Training Sessions
                    </a>
                    <a href="{{ route('coaches.club-results', auth()->user()->coach) }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('coaches.club-results*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        Club Results
                    </a>
                    <a href="{{ route('elimination-matches.index') }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('elimination-matches.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                        </svg>
                        Elimination Matches
                    </a>
                    <a href="{{ route('manual') }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('manual') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        User Manual
                    </a>
                @else
                    {{-- Admin / club_admin / state_admin: full menu --}}
                    <a href="{{ route('archers.index') }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('archers.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        Archers
                    </a>

                    @if(in_array(auth()->user()->role, ['super_admin', 'club_admin', 'national_team']))
                        <a href="{{ route('coaches.index') }}"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('coaches.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                            </svg>
                            Coaches
                        </a>
                    @endif

                    <a href="{{ route('elimination-matches.index') }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('elimination-matches.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                        </svg>
                        Elimination Matches
                    </a>

                    {{-- Club admin: My Club link --}}
                    @if(auth()->user()->role === 'club_admin' && auth()->user()->club)
                        <a href="{{ route('clubs.dashboard', auth()->user()->club) }}"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('clubs.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                            </svg>
                            My Club
                        </a>
                    @endif

                    {{-- Super admin + state_admin: Clubs list --}}
                    @if(in_array(auth()->user()->role, ['super_admin', 'state_admin', 'national_team']))
                        <a href="{{ route('clubs.index') }}"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('clubs.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                            </svg>
                            Clubs
                        </a>
                    @endif

                    {{-- Super admin + state_admin: State Teams --}}
                    @if(in_array(auth()->user()->role, ['super_admin', 'state_admin', 'national_team']))
                        <a href="{{ route('state-teams.index') }}"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('state-teams.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            State Teams
                        </a>
                    @endif

                    {{-- National Team --}}
                    @if(in_array(auth()->user()->role, ['super_admin', 'national_team']))
                        <a href="{{ route('national-team.index') }}"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('national-team.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                            </svg>
                            National Team
                        </a>
                    @endif

                    @if(auth()->user()->role === 'super_admin')
                        <a href="{{ route('admin.settings') }}"
                           class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                                  {{ request()->routeIs('admin.settings*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Settings
                        </a>
                    @endif
                    <a href="{{ route('manual') }}"
                       class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-r-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('manual') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        User Manual
                    </a>
                @endif
            @endauth
        </nav>

        {{-- User panel --}}
        @auth
        <div class="px-3 py-4" style="border-top: 1px solid rgba(255,255,255,0.07);">
            @if(in_array(auth()->user()->role, ['super_admin', 'club_admin', 'state_admin', 'national_team']))
            <a href="{{ route('live-scoring.realtime') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-black mb-2 transition-all"
               style="background: linear-gradient(135deg, #7c3aed, #6d28d9); color: #fff;">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background:#f59e0b;"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2" style="background:#f59e0b;"></span>
                </span>
                LIVE SCORING
            </a>
            @endif
            <div class="flex items-center gap-3 px-3 py-3 rounded-xl" style="background: #1e293b;">
                <div class="h-9 w-9 rounded-full flex items-center justify-center text-slate-900 text-sm font-black flex-shrink-0"
                     style="background: #f59e0b;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs font-medium capitalize" style="color:#94a3b8;">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                               text-slate-400 hover:text-red-400 hover:bg-red-400/5 transition-all">
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
        <header class="sticky top-0 z-20 bg-white" style="border-bottom: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(15,23,42,0.06);">
            <div class="flex items-center gap-3 px-4 py-4">

                {{-- Hamburger (mobile only) --}}
                <button @click="mobileOpen = true"
                        class="lg:hidden h-9 w-9 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors"
                        style="background:#f1f5f9; color:#0f172a;"
                        aria-label="Open menu">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>

                <div class="flex-1 min-w-0">
                    <h1 class="font-black text-slate-900 page-heading section-header truncate">@yield('header', 'Dashboard')</h1>
                    @hasSection('subheader')
                        <p class="text-sm font-medium text-slate-500 mt-0.5 truncate">@yield('subheader')</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">@yield('header-actions')</div>
            </div>
        </header>

        {{-- Flash message --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 4500)"
                 class="mx-4 mt-5 lg:mx-6" x-transition>
                <div class="flex items-center gap-3 rounded-xl border px-4 py-3"
                     style="background:#f0fdf4; border-color:#86efac;">
                    <div class="h-7 w-7 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background:#dcfce7;">
                        <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-emerald-800 flex-1">{{ session('success') }}</p>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 text-xl leading-none">&times;</button>
                </div>
            </div>
        @endif

        {{-- Content --}}
        <main class="flex-1 px-4 py-5 lg:px-6 lg:py-6">
            @yield('content')
        </main>

        <footer class="px-4 py-3 lg:px-6" style="border-top: 1px solid #e2e8f0;">
            <p class="text-xs text-slate-400">{{ $siteSettings['footer_text'] ?? ('© ' . date('Y') . ' Archery Stats Management System') }}</p>
        </footer>
    </div>
</div>
@stack('scripts')
<script src="{{ asset('js/popup-engine.js') }}"></script>
@include('partials.popups')
</body>
</html>
