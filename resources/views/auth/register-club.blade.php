@php
    try {
        $s = \App\Models\Setting::getAllCached();
    } catch (\Throwable) {
        $s = [];
    }
    $loginBodyFont    = $s['login_body_font']    ?? $s['body_font']    ?? 'Inter';
    $loginHeadingFont = $s['login_heading_font'] ?? $s['heading_font'] ?? $loginBodyFont;
    $logoPath         = !empty($s['logo']) ? asset('storage/' . $s['logo']) : null;
    $footerText       = $s['footer_text'] ?? ('© ' . date('Y') . ' Archery Stats Management System');
    $fontsToLoad      = array_unique([$loginBodyFont, $loginHeadingFont, 'Barlow']);
    $fontsParam       = collect($fontsToLoad)
                            ->map(fn($f) => str_replace(' ', '+', $f) . ':wght@400;500;600;700;800;900')
                            ->join('&family=');
    $rootDomain = config('app.root_domain', 'sportdns.com');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Register Your Club — Archery Stats | SportDNS</title>
    <meta name="description" content="Create a free club account on Archery Stats. Manage your archers, track scores, and build your club's digital presence.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $fontsParam }}&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: '{{ $loginBodyFont }}', sans-serif; }
        .branding-panel {
            background: #0f172a;
            background-image:
                radial-gradient(ellipse 70% 50% at 50% 0%,   rgba(245,158,11,0.18) 0%, transparent 65%),
                radial-gradient(ellipse 40% 35% at 90% 90%,  rgba(245,158,11,0.10) 0%, transparent 55%);
        }
        .dot-grid {
            background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .target-ring {
            border: 2px solid rgba(245,158,11,0.25);
            border-radius: 50%;
            position: absolute;
        }
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

    {{-- Left: Branding panel --}}
    <div class="hidden lg:flex lg:w-[40%] xl:w-[42%] flex-col branding-panel relative overflow-hidden">
        <div class="absolute inset-0 dot-grid pointer-events-none"></div>
        <div class="target-ring" style="width:500px;height:500px;top:-120px;left:-180px;"></div>
        <div class="target-ring" style="width:340px;height:340px;top:-40px;left:-100px;"></div>
        <div class="target-ring" style="width:180px;height:180px;top:30px;left:-30px;"></div>

        <div class="relative z-10 flex flex-col items-center justify-center flex-1 px-14 text-center">
            @if($logoPath)
                <div class="mb-8 p-5 rounded-3xl" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
                    <img src="{{ $logoPath }}" alt="Archery Stats" class="h-16 max-w-[160px] object-contain">
                </div>
            @else
                <div class="mb-8 h-24 w-24 rounded-3xl flex items-center justify-center"
                     style="background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.25);">
                    <svg viewBox="0 0 48 48" fill="none" class="h-14 w-14">
                        <circle cx="24" cy="24" r="22" stroke="#f59e0b" stroke-width="2"/>
                        <circle cx="24" cy="24" r="15" stroke="#f59e0b" stroke-width="2" stroke-opacity="0.6"/>
                        <circle cx="24" cy="24" r="8"  stroke="#f59e0b" stroke-width="2" stroke-opacity="0.4"/>
                        <circle cx="24" cy="24" r="3"  fill="#f59e0b"/>
                    </svg>
                </div>
            @endif

            <p class="text-4xl font-black text-white tracking-tight" style="font-family:'Barlow',sans-serif;">YOUR CLUB.<br>YOUR PLATFORM.</p>
            <p class="mt-4 text-sm font-medium max-w-xs leading-relaxed" style="color:#94a3b8;">
                Get your own archery club management platform on a custom subdomain — free.
            </p>

            <div class="mt-10 space-y-3 w-full max-w-xs">
                @foreach([
                    ['icon' => '🎯', 'text' => 'Your own subdomain (yourclub.sportdns.com)'],
                    ['icon' => '👥', 'text' => 'Manage archers & coaches'],
                    ['icon' => '📊', 'text' => 'Track scores & performance'],
                    ['icon' => '🏠', 'text' => 'Public landing page for your club'],
                ] as $f)
                <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);">
                    <span class="text-lg">{{ $f['icon'] }}</span>
                    <span class="text-sm font-medium" style="color:#cbd5e1;">{{ $f['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="relative z-10 px-14 py-5">
            <p class="text-xs text-center" style="color:rgba(255,255,255,0.2);">{{ $footerText }}</p>
        </div>
    </div>

    {{-- Right: Form --}}
    <div class="flex-1 flex flex-col min-h-screen bg-white overflow-y-auto">
        <div class="flex-1 flex items-start justify-center px-6 py-10 sm:px-10">
            <div class="w-full max-w-md">

                {{-- Mobile logo --}}
                <div class="lg:hidden mb-6 text-center fade-1">
                    @if($logoPath)
                        <img src="{{ $logoPath }}" alt="Archery Stats" class="h-12 object-contain mx-auto mb-3">
                    @endif
                    <p class="text-lg font-black text-slate-900" style="font-family:'Barlow',sans-serif;">ARCHERY STATS</p>
                </div>

                {{-- Heading --}}
                <div class="mb-8 fade-1">
                    <h1 class="text-3xl font-black text-slate-900" style="font-family:'Barlow',sans-serif;">Register Your Club</h1>
                    <p class="text-sm text-slate-500 mt-2">Create your free club account and subdomain</p>
                </div>

                {{-- Errors --}}
                @if($errors->any())
                    <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 fade-2">
                        <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                        </svg>
                        <ul class="text-sm text-red-700 font-medium space-y-1">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('club-register') }}"
                      x-data="{
                          clubName: '{{ old('club_name') }}',
                          slug: '{{ old('slug') }}',
                          slugAvailable: null,
                          slugChecking: false,
                          generateSlug(name) {
                              return name.toLowerCase()
                                  .replace(/[^a-z0-9\s-]/g, '')
                                  .trim()
                                  .replace(/\s+/g, '-')
                                  .replace(/-+/g, '-')
                                  .slice(0, 60);
                          },
                          onClubNameInput() {
                              if (!this.slug || this.slug === this.generateSlug(this.clubName.slice(0,-1))) {
                                  this.slug = this.generateSlug(this.clubName);
                              }
                              this.checkSlug();
                          },
                          async checkSlug() {
                              if (!this.slug) { this.slugAvailable = null; return; }
                              this.slugChecking = true;
                              const r = await fetch('{{ route('club-register.check-slug') }}?slug=' + encodeURIComponent(this.slug));
                              const d = await r.json();
                              this.slugAvailable = d.available;
                              this.slugChecking = false;
                          }
                      }"
                      class="space-y-5">
                    @csrf

                    {{-- Club section --}}
                    <div class="fade-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Club Details</p>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Club Name <span class="text-red-500">*</span></label>
                                <input type="text" name="club_name" required
                                       x-model="clubName"
                                       @input="onClubNameInput()"
                                       placeholder="e.g. Selangor Archery Club"
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-amber-500 focus:ring-2 focus:bg-white transition @error('club_name') border-red-400 bg-red-50 @enderror">
                                @error('club_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">
                                    Subdomain <span class="text-red-500">*</span>
                                    <span class="font-normal text-slate-400 ml-1">— your club's URL</span>
                                </label>
                                <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 overflow-hidden focus-within:border-amber-500 focus-within:ring-2 focus-within:bg-white transition @error('slug') border-red-400 bg-red-50 @enderror">
                                    <input type="text" name="slug" required
                                           x-model="slug"
                                           @input="checkSlug()"
                                           placeholder="your-club"
                                           pattern="[a-zA-Z0-9\-]+"
                                           class="flex-1 bg-transparent px-4 py-3 text-sm text-slate-900 placeholder-slate-400 outline-none font-mono">
                                    <span class="pr-4 text-sm text-slate-400 font-mono shrink-0">.{{ $rootDomain }}</span>
                                </div>
                                <div class="mt-1.5 min-h-[18px]">
                                    <p x-show="slugChecking" class="text-xs text-slate-400">Checking availability...</p>
                                    <p x-show="!slugChecking && slugAvailable === true" class="text-xs text-green-600 font-medium">
                                        ✓ Available — <span class="font-mono" x-text="slug + '.{{ $rootDomain }}'"></span>
                                    </p>
                                    <p x-show="!slugChecking && slugAvailable === false" class="text-xs text-red-600 font-medium">
                                        ✗ Already taken — try a different subdomain
                                    </p>
                                </div>
                                @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">State</label>
                                <select name="state" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-amber-500 focus:ring-2 transition">
                                    <option value="">— Select state (optional) —</option>
                                    @foreach (\App\Models\Archer::MALAYSIAN_STATES as $st)
                                        <option value="{{ $st }}" {{ old('state') === $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 my-2"></div>

                    {{-- Admin section --}}
                    <div class="fade-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Club Admin Account</p>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Your Name <span class="text-red-500">*</span></label>
                                <input type="text" name="admin_name" required value="{{ old('admin_name') }}"
                                       placeholder="Your full name"
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-amber-500 focus:ring-2 focus:bg-white transition @error('admin_name') border-red-400 bg-red-50 @enderror">
                                @error('admin_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" name="admin_email" required value="{{ old('admin_email') }}"
                                       placeholder="admin@yourclub.com"
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-amber-500 focus:ring-2 focus:bg-white transition @error('admin_email') border-red-400 bg-red-50 @enderror">
                                @error('admin_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password" required
                                       placeholder="Minimum 8 characters"
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-amber-500 focus:ring-2 focus:bg-white transition">
                                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" required
                                       placeholder="Re-enter your password"
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-amber-500 focus:ring-2 focus:bg-white transition">
                            </div>
                        </div>
                    </div>

                    <div class="fade-4 pt-2">
                        <button type="submit"
                                class="w-full rounded-xl px-4 py-3.5 text-sm font-black tracking-wide transition-all duration-150 active:scale-95"
                                style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif; font-size:15px; letter-spacing:0.04em;">
                            CREATE CLUB ACCOUNT
                        </button>
                        <p class="mt-4 text-center text-sm text-slate-500 fade-5">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-semibold" style="color:#f59e0b;">Sign in</a>
                        </p>
                    </div>

                </form>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-100">
            <p class="text-xs text-slate-400 text-center">{{ $footerText }}</p>
        </div>
    </div>

</div>
</body>
</html>
