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

            {{-- Email icon --}}
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6"
                 style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                </svg>
            </div>

            <h1 class="text-3xl font-black text-slate-900 mb-2">Check Your Email</h1>
            <p class="text-slate-500 text-base mb-8">
                We've sent a verification link to the admin email you provided.
                Click it to confirm your account — then a platform administrator will
                review and activate your club.
            </p>

            @if($subdomain)
            <div class="bg-indigo-50 rounded-2xl px-6 py-5 mb-8">
                <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-2">Your club URL (goes live once approved)</p>
                <span class="text-indigo-700 font-black text-xl font-mono break-all">
                    {{ $slug }}.{{ config('app.root_domain', 'sportdns.com') }}
                </span>
                <p class="text-xs text-indigo-400 mt-2">You'll get an email here as soon as your club is activated.</p>
            </div>
            @endif

            <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-left mb-2">
                <p class="text-sm text-amber-800 font-semibold mb-1">Two quick steps remain:</p>
                <ol class="text-sm text-amber-700 list-decimal list-inside space-y-0.5">
                    <li>Open the email and click the verification link.</li>
                    <li>Wait for a platform admin to approve your club (you'll be emailed).</li>
                </ol>
            </div>

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
