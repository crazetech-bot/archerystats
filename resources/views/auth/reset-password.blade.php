@php
    try { $s = \App\Models\Setting::getAllCached(); } catch (\Throwable) { $s = []; }
    $footerText = $s['footer_text'] ?? ('© ' . date('Y') . ' Archery Stats Management System');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>Reset Password — Archery Stats</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    </style>
</head>
<body class="h-full" style="background:#f1f5f9;">

<div class="min-h-screen flex">

    {{-- Left branding panel --}}
    <div class="hidden lg:flex lg:w-[46%] xl:w-[48%] flex-col branding-panel relative overflow-hidden">
        <div class="absolute inset-0 dot-grid pointer-events-none"></div>
        <div class="target-ring" style="width:500px;height:500px;top:-120px;left:-180px;"></div>
        <div class="target-ring" style="width:340px;height:340px;top:-40px;left:-100px;"></div>
        <div class="target-ring" style="width:180px;height:180px;top:30px;left:-30px;"></div>
        <div class="target-ring" style="width:500px;height:500px;bottom:-200px;right:-200px;border-color:rgba(245,158,11,0.12);"></div>

        <div class="relative z-10 flex flex-col items-center justify-center flex-1 px-14 text-center">
            <div class="mb-8 h-24 w-24 rounded-3xl flex items-center justify-center"
                 style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.25);">
                <svg viewBox="0 0 48 48" fill="none" class="h-14 w-14">
                    <circle cx="24" cy="24" r="22" stroke="#f59e0b" stroke-width="2"/>
                    <circle cx="24" cy="24" r="15" stroke="#f59e0b" stroke-width="2" stroke-opacity="0.6"/>
                    <circle cx="24" cy="24" r="8"  stroke="#f59e0b" stroke-width="2" stroke-opacity="0.4"/>
                    <circle cx="24" cy="24" r="3"  fill="#f59e0b"/>
                </svg>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tight" style="font-family:'Barlow',sans-serif;">ARCHERY<br>STATS</h1>
            <p class="mt-4 text-sm font-medium max-w-xs leading-relaxed" style="color:#94a3b8;">
                Choose a strong password. You'll use it to sign in to your account.
            </p>
        </div>
        <div class="relative z-10 px-14 py-5">
            <p class="text-xs text-center" style="color:rgba(255,255,255,0.2);">{{ $footerText }}</p>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="flex-1 flex flex-col min-h-screen bg-white">
        <div class="flex-1 flex items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-sm">

                {{-- Mobile icon --}}
                <div class="lg:hidden mb-8 text-center fade-1">
                    <div class="mx-auto mb-4 h-14 w-14 rounded-2xl flex items-center justify-center" style="background:#0f172a;">
                        <svg viewBox="0 0 48 48" fill="none" class="h-9 w-9">
                            <circle cx="24" cy="24" r="22" stroke="#f59e0b" stroke-width="2.5"/>
                            <circle cx="24" cy="24" r="13" stroke="#f59e0b" stroke-width="2" stroke-opacity="0.6"/>
                            <circle cx="24" cy="24" r="4"  fill="#f59e0b"/>
                        </svg>
                    </div>
                    <p class="text-lg font-black text-slate-900" style="font-family:'Barlow',sans-serif;">ARCHERY STATS</p>
                </div>

                {{-- Heading --}}
                <div class="mb-8 fade-1">
                    <h2 class="text-3xl font-black text-slate-900" style="font-family:'Barlow',sans-serif;">Reset Password</h2>
                    <p class="text-sm text-slate-500 mt-2">Enter your new password below.</p>
                </div>

                {{-- Errors --}}
                @if($errors->any())
                    <div class="mb-5 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 fade-2">
                        <svg class="h-5 w-5 text-red-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="fade-2">
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Email address</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $email ?? '') }}" required readonly
                               class="block w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm
                                      text-slate-500 outline-none cursor-not-allowed">
                    </div>

                    <div class="fade-3">
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">New Password</label>
                        <input type="password" id="password" name="password" required
                               placeholder="Minimum 8 characters"
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm
                                      text-slate-900 placeholder-slate-400 transition
                                      focus:border-amber-500 focus:ring-2 focus:bg-white outline-none
                                      @error('password') border-red-400 bg-red-50 @enderror">
                    </div>

                    <div class="fade-3">
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-1.5">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="Repeat your new password"
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm
                                      text-slate-900 placeholder-slate-400 transition
                                      focus:border-amber-500 focus:ring-2 focus:bg-white outline-none">
                    </div>

                    <div class="fade-4">
                        <button type="submit"
                                class="w-full rounded-xl px-4 py-3 text-sm font-black tracking-wide
                                       transition-all duration-150 active:scale-95"
                                style="background:#f59e0b;color:#0f172a;font-family:'Barlow',sans-serif;font-size:15px;letter-spacing:0.04em;">
                            SET NEW PASSWORD
                        </button>
                    </div>

                    <div class="text-center fade-4">
                        <a href="{{ route('login') }}" class="text-sm font-semibold" style="color:#f59e0b;">
                            &larr; Back to Sign In
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

</body>
</html>
