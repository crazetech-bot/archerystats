@php
    try {
        $s = \App\Models\Setting::getAllCached();
    } catch (\Throwable) {
        $s = [];
    }
    $loginBodyFont    = $s['login_body_font']    ?? $s['body_font']    ?? 'Inter';
    $loginHeadingFont = $s['login_heading_font'] ?? $s['heading_font'] ?? $loginBodyFont;
    $loginHeadingSize = (int)($s['login_heading_size'] ?? $s['heading_size'] ?? '28');
    $logoPath         = !empty($s['logo']) ? asset('storage/' . $s['logo']) : null;
    $footerText       = $s['footer_text'] ?? ('© ' . date('Y') . ' Archery Stats Management System');
    $fontsToLoad      = array_unique([$loginBodyFont, $loginHeadingFont]);
    $fontsParam       = collect($fontsToLoad)
                            ->map(fn($f) => str_replace(' ', '+', $f) . ':wght@400;500;600;700')
                            ->join('&family=');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Archery Stats</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $fontsParam }}&display=swap" rel="stylesheet">
    <style>
        body { font-family: '{{ $loginBodyFont }}', sans-serif; }
        .login-heading { font-family: '{{ $loginHeadingFont }}', sans-serif; font-size: {{ $loginHeadingSize }}px; line-height: 1.2; }

        .branding-panel {
            background: #0c0a1e;
            background-image:
                radial-gradient(ellipse 80% 55% at 50% -10%, rgba(99,102,241,0.45) 0%, transparent 65%),
                radial-gradient(ellipse 50% 40% at 85% 85%,  rgba(139,92,246,0.2)  0%, transparent 55%);
        }
        .dot-grid {
            background-image: radial-gradient(rgba(255,255,255,0.055) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-1 { animation: fadeUp 0.45s 0.00s ease both; }
        .fade-2 { animation: fadeUp 0.45s 0.08s ease both; }
        .fade-3 { animation: fadeUp 0.45s 0.16s ease both; }
        .fade-4 { animation: fadeUp 0.45s 0.24s ease both; }
    </style>
</head>
<body class="h-full bg-white">

<div class="min-h-screen flex">

    {{-- ── Left: Branding panel ── --}}
    <div class="hidden lg:flex lg:w-[46%] xl:w-[48%] flex-col branding-panel relative overflow-hidden">
        <div class="absolute inset-0 dot-grid pointer-events-none"></div>

        {{-- Centre content --}}
        <div class="relative z-10 flex flex-col items-center justify-center flex-1 px-14 text-center">

            {{-- Logo --}}
            @if($logoPath)
                <div class="mb-8 p-5 rounded-3xl"
                     style="background: rgba(255,255,255,0.07); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1);">
                    <img src="{{ $logoPath }}" alt="Logo" class="h-20 max-w-[200px] object-contain">
                </div>
            @else
                <div class="mb-8 h-20 w-20 rounded-3xl flex items-center justify-center"
                     style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                    <svg class="h-10 w-10 text-white opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 6a4 4 0 100 8 4 4 0 000-8zm0 2a2 2 0 110 4 2 2 0 010-4z"/>
                    </svg>
                </div>
            @endif

            <h1 class="text-4xl font-bold text-white tracking-tight">Archery Stats</h1>
            <p class="mt-3 text-indigo-300 text-sm max-w-xs leading-relaxed">
                Precision tracking for every archer, every shot, every competition.
            </p>

            {{-- Decorative pills --}}
            <div class="mt-10 flex flex-wrap justify-center gap-2.5">
                @foreach(['Archer Management', 'Score Tracking', 'Performance Analytics'] as $pill)
                    <span class="px-4 py-1.5 rounded-full text-xs font-medium text-white/60"
                          style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);">
                        {{ $pill }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Footer on branding side --}}
        <div class="relative z-10 px-14 py-5">
            <p class="text-xs text-white/25 text-center">{{ $footerText }}</p>
        </div>
    </div>

    {{-- ── Right: Form panel ── --}}
    <div class="flex-1 flex flex-col min-h-screen bg-white">

        {{-- Vertical centering wrapper --}}
        <div class="flex-1 flex items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-sm">

                {{-- Mobile logo (hidden on desktop) --}}
                <div class="lg:hidden mb-8 text-center fade-1">
                    @if($logoPath)
                        <img src="{{ $logoPath }}" alt="Logo" class="h-14 object-contain mx-auto mb-4">
                    @endif
                    <p class="text-lg font-bold text-gray-900">Archery Stats</p>
                </div>

                {{-- Heading --}}
                <div class="mb-8 fade-1">
                    <h2 class="login-heading font-bold text-gray-900">Welcome back</h2>
                    <p class="text-sm text-gray-500 mt-2">Sign in to your account to continue</p>
                </div>

                {{-- Error --}}
                @if($errors->any())
                    <div class="mb-5 flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 fade-2">
                        <svg class="h-5 w-5 text-red-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div class="fade-2">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}" required autofocus
                               placeholder="you@example.com"
                               class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm
                                      text-gray-900 placeholder-gray-400 transition
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none
                                      @error('email') border-red-400 bg-red-50 @enderror">
                    </div>

                    <div class="fade-3">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <input type="password" id="password" name="password" required
                               placeholder="••••••••"
                               class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm
                                      text-gray-900 placeholder-gray-400 transition
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none">
                    </div>

                    <div class="flex items-center fade-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>

                    <div class="fade-4">
                        <button type="submit"
                                class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-md
                                       transition-all duration-150 hover:opacity-90 active:scale-95"
                                style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                            Sign in
                        </button>
                    </div>
                </form>

            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 text-center">{{ $footerText }}</p>
        </div>

    </div>
</div>

</body>
</html>
