@php
    try { $s = \App\Models\Setting::getAllCached(); } catch (\Throwable) { $s = []; }
    $footerText = $s['footer_text'] ?? ('© ' . date('Y') . ' Archery Stats Management System');
    $logoUrl    = !empty($s['logo']) ? asset('storage/' . $s['logo']) : null;
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>Create Your Free Archery Stats Account — Archers, Coaches & Clubs | SportDNS</title>
    <meta name="description" content="Create your free Archery Stats account on SportDNS. Register as an Archer to track scores, a Coach to manage athletes, or a Club Admin to run competitions and manage your club roster.">
    <link rel="canonical" href="{{ url('/register') }}">
    <meta property="og:type"         content="website">
    <meta property="og:site_name"    content="Archery Stats | SportDNS">
    <meta property="og:title"        content="Create Your Free Archery Stats Account — Archers, Coaches & Clubs | SportDNS">
    <meta property="og:description"  content="Register as an Archer to track scores, a Coach to manage athletes, or a Club Admin to run competitions and manage your entire club roster.">
    <meta property="og:url"          content="{{ url()->current() }}">
    @if($logoUrl)
    <meta property="og:image"        content="{{ $logoUrl }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image"       content="{{ $logoUrl }}">
    @endif
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="Create Your Free Archery Stats Account — Archers, Coaches & Clubs | SportDNS">
    <meta name="twitter:description" content="Register as an Archer, Coach, or Club Admin on Archery Stats. Track scores, manage athletes, and run club competitions on SportDNS.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/popup.css') }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .branding-panel {
            background: #0f172a;
            background-image:
                radial-gradient(ellipse 70% 50% at 50% 0%, rgba(245,158,11,0.18) 0%, transparent 65%),
                radial-gradient(ellipse 40% 35% at 90% 90%, rgba(245,158,11,0.10) 0%, transparent 55%);
        }
        .dot-grid {
            background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .target-ring { border: 2px solid rgba(245,158,11,0.25); border-radius: 50%; position: absolute; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-1 { animation: fadeUp 0.45s 0.00s ease both; }
        .fade-2 { animation: fadeUp 0.45s 0.08s ease both; }
        .fade-3 { animation: fadeUp 0.45s 0.16s ease both; }
        .fade-4 { animation: fadeUp 0.45s 0.24s ease both; }
        .fade-5 { animation: fadeUp 0.45s 0.32s ease both; }
    </style>
</head>
<body class="h-full" style="background:#f1f5f9;">

<div class="min-h-screen flex">

    {{-- Left branding panel --}}
    <div class="hidden lg:flex lg:w-[40%] flex-col branding-panel relative overflow-hidden">
        <div class="absolute inset-0 dot-grid pointer-events-none"></div>
        <div class="target-ring" style="width:500px;height:500px;top:-120px;left:-180px;"></div>
        <div class="target-ring" style="width:340px;height:340px;top:-40px;left:-100px;"></div>
        <div class="target-ring" style="width:180px;height:180px;top:30px;left:-30px;"></div>
        <div class="target-ring" style="width:500px;height:500px;bottom:-200px;right:-200px;border-color:rgba(245,158,11,0.12);"></div>

        <div class="relative z-10 flex flex-col items-center justify-center flex-1 px-14 text-center">
            <div class="mb-8 h-48 w-48 rounded-3xl flex items-center justify-center overflow-hidden"
                 style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.25);">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Archery Stats logo — SportDNS" class="h-40 w-40 object-contain">
                @else
                    <svg viewBox="0 0 48 48" fill="none" class="h-14 w-14">
                        <circle cx="24" cy="24" r="22" stroke="#f59e0b" stroke-width="2"/>
                        <circle cx="24" cy="24" r="15" stroke="#f59e0b" stroke-width="2" stroke-opacity="0.6"/>
                        <circle cx="24" cy="24" r="8"  stroke="#f59e0b" stroke-width="2" stroke-opacity="0.4"/>
                        <circle cx="24" cy="24" r="3"  fill="#f59e0b"/>
                    </svg>
                @endif
            </div>
            <p class="text-5xl font-black text-white tracking-tight" style="font-family:'Barlow',sans-serif;">ARCHERY<br>STATS</p>
            <p class="mt-4 text-sm font-medium max-w-xs leading-relaxed" style="color:#94a3b8;">
                Create your account as an Archer, Coach or Club and start tracking your archery journey.
            </p>
            <div class="mt-8 space-y-3 text-left w-full max-w-xs">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:rgba(245,158,11,0.15);">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                    <p class="text-sm" style="color:#94a3b8;">Track your scores and progress</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:rgba(245,158,11,0.15);">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                    <p class="text-sm" style="color:#94a3b8;">View personal bests and stats</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:rgba(245,158,11,0.15);">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                    <p class="text-sm" style="color:#94a3b8;">Competition history and rankings</p>
                </div>
            </div>
        </div>
        <div class="relative z-10 px-14 py-5">
            <p class="text-xs text-center" style="color:rgba(255,255,255,0.2);">{{ $footerText }}</p>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="flex-1 flex flex-col min-h-screen bg-white overflow-y-auto">
        <div class="flex-1 flex items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-md" x-data="registerForm">

                {{-- Mobile icon --}}
                <div class="lg:hidden mb-8 text-center fade-1">
                    <div class="mx-auto mb-4 h-28 w-28 rounded-2xl flex items-center justify-center overflow-hidden" style="background:#0f172a;">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Archery Stats logo — SportDNS" class="h-24 w-24 object-contain">
                        @else
                            <svg viewBox="0 0 48 48" fill="none" class="h-9 w-9">
                                <circle cx="24" cy="24" r="22" stroke="#f59e0b" stroke-width="2.5"/>
                                <circle cx="24" cy="24" r="13" stroke="#f59e0b" stroke-width="2" stroke-opacity="0.6"/>
                                <circle cx="24" cy="24" r="4"  fill="#f59e0b"/>
                            </svg>
                        @endif
                    </div>
                    <p class="text-lg font-black text-slate-900" style="font-family:'Barlow',sans-serif;">ARCHERY STATS</p>
                </div>

                {{-- Heading --}}
                <div class="mb-7 fade-1">
                    <h1 class="text-3xl font-black text-slate-900" style="font-family:'Barlow',sans-serif;">Create Your Free Account</h1>
                    <p class="text-sm text-slate-500 mt-2">Join as an Archer, Coach or Club.</p>
                </div>

                {{-- Errors --}}
                @if($errors->any())
                    <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 fade-2">
                        <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            @foreach($errors->all() as $error)
                                <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5"
                      @submit.prevent="
                          const suspMap = { archer: {{ $regOpen['archer'] ? 'true' : 'false' }}, coach: {{ $regOpen['coach'] ? 'true' : 'false' }}, club_admin: {{ $regOpen['club'] ? 'true' : 'false' }} };
                          if (role && !suspMap[role]) return;
                          if (role === 'club_admin' && isDuplicate) return;
                          $el.submit()">
                    @csrf

                    {{-- Role selector --}}
                    <div class="fade-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">I am registering as <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3">

                            {{-- Archer --}}
                            @if($regOpen['archer'])
                            <button type="button" @click="role = 'archer'"
                                    :style="role === 'archer'
                                        ? 'border-color:#f59e0b;background:rgba(245,158,11,0.07);'
                                        : 'border-color:#e2e8f0;background:#f8fafc;'"
                                    class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all cursor-pointer">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                                     :stroke="role === 'archer' ? '#f59e0b' : '#94a3b8'">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-xs font-bold"
                                      :style="role === 'archer' ? 'color:#b45309;' : 'color:#64748b;'">Archer</span>
                                <div x-show="role === 'archer'"
                                     class="absolute top-2 right-2 h-5 w-5 rounded-full flex items-center justify-center"
                                     style="background:#f59e0b;">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                </div>
                            </button>
                            @else
                            <div class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 opacity-50 cursor-not-allowed select-none"
                                 style="border-color:#fca5a5;background:#fef2f2;">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#ef4444">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-xs font-bold" style="color:#64748b;">Archer</span>
                                <span class="text-xs font-bold" style="color:#ef4444;">Suspended</span>
                            </div>
                            @endif

                            {{-- Coach --}}
                            @if($regOpen['coach'])
                            <button type="button" @click="role = 'coach'"
                                    :style="role === 'coach'
                                        ? 'border-color:#f59e0b;background:rgba(245,158,11,0.07);'
                                        : 'border-color:#e2e8f0;background:#f8fafc;'"
                                    class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all cursor-pointer">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                                     :stroke="role === 'coach' ? '#f59e0b' : '#94a3b8'">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                                </svg>
                                <span class="text-xs font-bold"
                                      :style="role === 'coach' ? 'color:#b45309;' : 'color:#64748b;'">Coach</span>
                                <div x-show="role === 'coach'"
                                     class="absolute top-2 right-2 h-5 w-5 rounded-full flex items-center justify-center"
                                     style="background:#f59e0b;">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                </div>
                            </button>
                            @else
                            <div class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 opacity-50 cursor-not-allowed select-none"
                                 style="border-color:#fca5a5;background:#fef2f2;">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#ef4444">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                                </svg>
                                <span class="text-xs font-bold" style="color:#64748b;">Coach</span>
                                <span class="text-xs font-bold" style="color:#ef4444;">Suspended</span>
                            </div>
                            @endif

                            {{-- Club --}}
                            @if($regOpen['club'])
                            <button type="button" @click="role = 'club_admin'"
                                    :style="role === 'club_admin'
                                        ? 'border-color:#6366f1;background:rgba(99,102,241,0.07);'
                                        : 'border-color:#e2e8f0;background:#f8fafc;'"
                                    class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all cursor-pointer">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                                     :stroke="role === 'club_admin' ? '#6366f1' : '#94a3b8'">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                                </svg>
                                <span class="text-xs font-bold"
                                      :style="role === 'club_admin' ? 'color:#4338ca;' : 'color:#64748b;'">Club</span>
                                <div x-show="role === 'club_admin'"
                                     class="absolute top-2 right-2 h-5 w-5 rounded-full flex items-center justify-center"
                                     style="background:#6366f1;">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                </div>
                            </button>
                            @else
                            <div class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 opacity-50 cursor-not-allowed select-none"
                                 style="border-color:#fca5a5;background:#fef2f2;">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#ef4444">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                                </svg>
                                <span class="text-xs font-bold" style="color:#64748b;">Club</span>
                                <span class="text-xs font-bold" style="color:#ef4444;">Suspended</span>
                            </div>
                            @endif

                        </div>
                        <input type="hidden" name="role" :value="role">
                        @error('role')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Club Name (only when Club is selected) --}}
                    <div x-show="role === 'club_admin'" x-cloak class="fade-3">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Club Name <span class="text-red-500">*</span></label>
                        <input type="text" name="club_name" x-model="clubName"
                               placeholder="e.g. Selangor Archery Club"
                               :class="isDuplicate
                                   ? 'border-amber-400 bg-amber-50 focus:border-amber-500 focus:ring-amber-400/20'
                                   : '@error('club_name') border-red-400 bg-red-50 @else border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500/20 @enderror'"
                               class="block w-full rounded-xl border px-4 py-3 text-sm text-slate-900 placeholder-slate-400 transition focus:ring-2 focus:bg-white outline-none">

                        {{-- Duplicate warning --}}
                        <div x-show="isDuplicate" x-cloak
                             class="mt-2 flex items-start gap-2 rounded-xl border border-amber-300 bg-amber-50 px-3 py-2.5">
                            <svg class="h-4 w-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                            <div class="text-xs text-amber-800">
                                <p class="font-bold">Club name already exists</p>
                                <p>"<span x-text="matchedClub"></span>" is already registered in the system. Please use a different name.</p>
                            </div>
                        </div>

                        <p x-show="!isDuplicate" class="mt-1.5 text-xs text-slate-400">This will be your club's name in the system.</p>
                        @error('club_name')
                            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div class="fade-3">
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name') }}" required
                               placeholder="Your full name"
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm
                                      text-slate-900 placeholder-slate-400 transition
                                      focus:border-amber-500 focus:ring-2 focus:bg-white outline-none
                                      @error('name') border-red-400 bg-red-50 @enderror">
                    </div>

                    {{-- Email --}}
                    <div class="fade-3">
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}" required
                               placeholder="you@example.com"
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm
                                      text-slate-900 placeholder-slate-400 transition
                                      focus:border-amber-500 focus:ring-2 focus:bg-white outline-none
                                      @error('email') border-red-400 bg-red-50 @enderror">
                    </div>

                    {{-- Password --}}
                    <div class="fade-4">
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password" name="password" required
                               placeholder="Minimum 8 characters"
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm
                                      text-slate-900 placeholder-slate-400 transition
                                      focus:border-amber-500 focus:ring-2 focus:bg-white outline-none
                                      @error('password') border-red-400 bg-red-50 @enderror">
                    </div>

                    <div class="fade-4">
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="Repeat your password"
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm
                                      text-slate-900 placeholder-slate-400 transition
                                      focus:border-amber-500 focus:ring-2 focus:bg-white outline-none">
                    </div>

                    {{-- Submit --}}
                    <div class="fade-5">
                        <button type="submit"
                                :disabled="role === 'club_admin' && isDuplicate"
                                :class="role === 'club_admin' && isDuplicate
                                    ? 'opacity-40 cursor-not-allowed'
                                    : 'active:scale-95'"
                                class="w-full rounded-xl px-4 py-3 text-sm font-black tracking-wide transition-all duration-150"
                                style="background:#f59e0b;color:#0f172a;font-family:'Barlow',sans-serif;font-size:15px;letter-spacing:0.04em;">
                            CREATE ACCOUNT
                        </button>
                    </div>

                    {{-- Sign in link --}}
                    <div class="text-center fade-5">
                        <p class="text-sm text-slate-500">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-bold" style="color:#f59e0b;">Sign In</a>
                        </p>
                    </div>

                    {{-- User Manual --}}
                    <div class="text-center fade-5">
                        <a href="{{ route('manual') }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold transition-all"
                           style="color:#64748b;background:#f1f5f9;"
                           onmouseover="this.style.background='#e0e7ff';this.style.color='#4338ca'"
                           onmouseout="this.style.background='#f1f5f9';this.style.color='#64748b'">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                            User Manual
                        </a>
                    </div>
                </form>

            </div>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            <p class="text-xs text-slate-400 text-center">{{ $footerText }}</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('registerForm', () => ({
        role: '{{ old('role', '') }}',
        clubName: '{{ old('club_name', '') }}',
        existingClubNames: @json($clubs),
        get isDuplicate() {
            const v = this.clubName.trim().toLowerCase();
            return v.length > 0 && this.existingClubNames.some(n => n.toLowerCase() === v);
        },
        get matchedClub() {
            const v = this.clubName.trim().toLowerCase();
            return this.existingClubNames.find(n => n.toLowerCase() === v) || null;
        }
    }));
});
</script>
<script src="{{ asset('js/popup-engine.js') }}"></script>
@include('partials.popups')
</body>
</html>
