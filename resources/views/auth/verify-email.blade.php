@php
    try { $s = \App\Models\Setting::getAllCached(); } catch (\Throwable) { $s = []; }
    $footerText = $s['footer_text'] ?? ('© ' . date('Y') . ' Archery Stats Management System');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — Archery Stats</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-1 { animation: fadeUp 0.45s 0.00s ease both; }
        .fade-2 { animation: fadeUp 0.45s 0.10s ease both; }
        .fade-3 { animation: fadeUp 0.45s 0.20s ease both; }
        @keyframes pulse-ring {
            0%   { transform: scale(1);   opacity: 0.6; }
            50%  { transform: scale(1.08); opacity: 0.3; }
            100% { transform: scale(1);   opacity: 0.6; }
        }
        .pulse-ring { animation: pulse-ring 2.4s ease-in-out infinite; }
    </style>
</head>
<body class="h-full flex items-center justify-center min-h-screen px-4 py-12">

<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden" style="border:1px solid #e2e8f0;">

        {{-- Top banner --}}
        <div class="px-8 pt-10 pb-8 text-center" style="background:#0f172a;">
            {{-- Inbox icon with pulse --}}
            <div class="relative inline-flex items-center justify-center mb-5">
                <div class="absolute h-24 w-24 rounded-full pulse-ring" style="background:rgba(245,158,11,0.15);"></div>
                <div class="relative h-18 w-18 rounded-full flex items-center justify-center"
                     style="width:72px;height:72px;background:rgba(245,158,11,0.2);border:2px solid rgba(245,158,11,0.4);">
                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-black text-white mb-1" style="font-family:'Barlow',sans-serif;">CHECK YOUR EMAIL</h1>
            <p class="text-sm font-medium" style="color:#f59e0b;">One more step to activate your account</p>
        </div>

        {{-- Body --}}
        <div class="px-8 py-8">

            {{-- Status flash --}}
            @if(session('status'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 fade-1">
                    <svg class="h-5 w-5 text-emerald-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-emerald-700 font-medium">{{ session('status') }}</p>
                </div>
            @endif

            <div class="fade-2 text-center mb-7">
                <p class="text-slate-600 text-sm leading-relaxed">
                    We've sent a verification link to your email address.<br>
                    Click the link in the email to activate your account and sign in.
                </p>
                <p class="mt-3 text-xs text-slate-400">
                    Didn't receive it? Check your spam folder, or click below to resend.
                </p>
            </div>

            {{-- Resend button --}}
            <div class="fade-3 space-y-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-xl px-4 py-3 text-sm font-black tracking-wide
                                   transition-all duration-150 active:scale-95"
                            style="background:#f59e0b;color:#0f172a;font-family:'Barlow',sans-serif;font-size:14px;letter-spacing:0.04em;">
                        RESEND VERIFICATION EMAIL
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-slate-500
                                   border border-slate-200 hover:border-slate-300 hover:text-slate-700
                                   transition-all duration-150 active:scale-95">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

    </div>

    <p class="text-center text-xs text-slate-400 mt-5">{{ $footerText }}</p>
</div>

</body>
</html>
