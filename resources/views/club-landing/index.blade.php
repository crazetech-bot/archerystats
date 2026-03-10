@php
    $bodyFont    = $settings['body_font']    ?? 'Inter';
    $headingFont = $settings['heading_font'] ?? $bodyFont;
    $siteName    = $settings['seo_site_name'] ?? $club->name;
    $gaId        = !empty($settings['seo_ga_id']) ? trim($settings['seo_ga_id']) : null;
    $fontsToLoad = array_unique([$bodyFont, $headingFont, 'Barlow']);
    $fontsParam  = collect($fontsToLoad)
                    ->map(fn($f) => str_replace(' ', '+', $f) . ':wght@400;500;600;700;800;900')
                    ->join('&family=');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $club->name }}</title>
    <meta name="description" content="{{ $club->description ?? $club->tagline ?? 'Welcome to ' . $club->name }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $fontsParam }}&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: '{{ $bodyFont }}', sans-serif; }
        h1, h2, h3, h4 { font-family: '{{ $headingFont }}', sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 70%, #6366f1 100%); }
        .section-card { background: white; border-radius: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06); border: 1px solid #f3f4f6; }
    </style>
    @if($gaId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $gaId }}');</script>
    @endif
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Navbar --}}
    <nav class="hero-gradient shadow-lg sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($clubLogo)
                    <img src="{{ $clubLogo }}" alt="{{ $club->name }}" class="w-9 h-9 rounded-xl object-cover border border-white/30">
                @else
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($club->name, 0, 2)) }}
                    </div>
                @endif
                <span class="text-white font-bold text-lg leading-tight">{{ $club->name }}</span>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/archers') }}"
                       class="text-xs px-4 py-2 rounded-lg bg-white/20 text-white hover:bg-white/30 transition-colors font-medium">
                        Dashboard
                    </a>
                @else
                    @if ($sections['cta'])
                    <a href="{{ route('register') }}"
                       class="text-xs px-4 py-2 rounded-lg bg-white text-indigo-700 hover:bg-indigo-50 transition-colors font-semibold shadow">
                        Join Club
                    </a>
                    @endif
                    <a href="{{ route('login') }}"
                       class="text-xs px-4 py-2 rounded-lg bg-white/20 text-white hover:bg-white/30 transition-colors font-medium">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    @if($sections['hero'])
    @include('club-landing.partials.hero')
    @endif

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 space-y-8">

        {{-- About --}}
        @if($sections['about'] && ($club->description || $club->tagline))
        @include('club-landing.partials.about')
        @endif

        {{-- Contact --}}
        @if($sections['contact'] && ($club->contact_email || $club->contact_phone || $club->address || $club->website))
        @include('club-landing.partials.contact')
        @endif

        {{-- Social --}}
        @if($sections['social'] && ($club->facebook_url || $club->instagram_url || $club->whatsapp_number))
        @include('club-landing.partials.social')
        @endif

        {{-- CTA --}}
        @if($sections['cta'])
        @guest
        @include('club-landing.partials.cta')
        @endguest
        @endif

    </div>

    {{-- Footer --}}
    @include('club-landing.partials.footer')

</body>
</html>
