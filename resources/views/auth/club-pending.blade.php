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
    <title>Club Awaiting Approval — Archery Stats</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background: #f1f5f9; }</style>
</head>
<body class="h-full flex items-center justify-center min-h-screen px-4 py-12">

<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden" style="border:1px solid #e2e8f0;">

        <div class="px-8 pt-10 pb-8 text-center" style="background:#0f172a;">
            <div class="relative inline-flex items-center justify-center mb-5">
                <div class="relative h-18 w-18 rounded-full flex items-center justify-center"
                     style="width:72px;height:72px;background:rgba(245,158,11,0.2);border:2px solid rgba(245,158,11,0.4);">
                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-black text-white mb-1" style="font-family:'Barlow',sans-serif;">EMAIL VERIFIED</h1>
            <p class="text-sm font-medium" style="color:#f59e0b;">Your club is awaiting approval</p>
        </div>

        <div class="px-8 py-8">
            <div class="text-center mb-7">
                <p class="text-slate-600 text-sm leading-relaxed">
                    Thanks for confirming your email@if(!empty($club)), <strong>{{ $club->name }}</strong>@endif.<br>
                    A platform administrator now needs to approve your club before it goes live.
                </p>
                <p class="mt-3 text-xs text-slate-400">
                    You'll receive an email with your club link as soon as it's activated.
                    This usually happens within a day.
                </p>
            </div>

            <a href="{{ route('login') }}"
               class="block w-full text-center rounded-xl px-4 py-3 text-sm font-semibold text-slate-500
                      border border-slate-200 hover:border-slate-300 hover:text-slate-700 transition-all">
                Back to login
            </a>
        </div>

    </div>

    <p class="text-center text-xs text-slate-400 mt-5">{{ $footerText }}</p>
</div>

</body>
</html>
