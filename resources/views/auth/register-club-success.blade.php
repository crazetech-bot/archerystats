@php
    try {
        $s = \App\Models\Setting::getAllCached();
    } catch (\Throwable) {
        $s = [];
    }
    $logoPath   = !empty($s['logo']) ? asset('storage/' . $s['logo']) : null;
    $footerText = $s['footer_text'] ?? ('© ' . date('Y') . ' Archery Stats Management System');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Registered — Archery Stats</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body style="background:#f1f5f9; font-family:'Barlow',sans-serif;" class="min-h-screen flex items-center justify-center p-6">

<div class="w-full max-w-lg">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- Top accent --}}
        <div class="h-2" style="background: linear-gradient(90deg, #f59e0b, #fbbf24, #f59e0b);"></div>

        <div class="px-8 py-10 text-center">

            {{-- Success icon --}}
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6"
                 style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-3xl font-black text-slate-900 mb-2">Club Created!</h1>
            <p class="text-slate-500 text-base mb-8">Your club account is ready. Go to your subdomain to log in and get started.</p>

            @if($subdomain)
            <div class="bg-indigo-50 rounded-2xl px-6 py-5 mb-8">
                <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-2">Your club URL</p>
                <a href="{{ $subdomain }}" target="_blank"
                   class="text-indigo-700 font-black text-xl hover:underline font-mono break-all">
                    {{ $slug }}.{{ config('app.root_domain', 'sportdns.com') }}
                </a>
                <p class="text-xs text-indigo-400 mt-2">Click to open your club's public page</p>
            </div>

            <a href="{{ $subdomain }}/login"
               class="inline-flex items-center gap-2 w-full justify-center px-6 py-3.5 rounded-xl text-white font-black text-base transition-all active:scale-95"
               style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Go to My Club Dashboard
            </a>
            @endif

            <p class="mt-6 text-sm text-slate-400">
                <a href="{{ route('club-register.form') }}" class="hover:text-slate-600">Register another club</a>
                &nbsp;·&nbsp;
                <a href="{{ route('login') }}" class="hover:text-slate-600">Back to login</a>
            </p>

        </div>
    </div>

    <p class="text-xs text-slate-400 text-center mt-6">{{ $footerText }}</p>
</div>

</body>
</html>
