<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; }
        .dot-grid {
            background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to   { stroke-dashoffset: 100; }
        }
        .countdown-ring {
            animation: countdown 3s linear forwards;
            stroke-dasharray: 100;
            stroke-dashoffset: 0;
            transform: rotate(-90deg);
            transform-origin: center;
        }
        @keyframes pulse-ring {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50%       { opacity: 0.15; transform: scale(1.08); }
        }
        .pulse { animation: pulse-ring 2s ease-in-out infinite; }
    </style>
</head>
<body class="h-full min-h-screen flex items-center justify-center relative overflow-hidden">

    <div class="absolute inset-0 dot-grid pointer-events-none"></div>

    {{-- Decorative rings --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        <div class="pulse w-[600px] h-[600px] rounded-full" style="border: 1px solid rgba(245,158,11,0.15);"></div>
    </div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none" style="animation-delay:0.4s;">
        <div class="pulse w-[400px] h-[400px] rounded-full" style="border: 1px solid rgba(245,158,11,0.2);"></div>
    </div>

    <div class="relative z-10 text-center px-6 py-12 max-w-md w-full">

        {{-- Bullseye with X --}}
        <div class="mx-auto mb-8 relative inline-flex items-center justify-center">
            <svg viewBox="0 0 120 120" fill="none" class="h-32 w-32">
                <circle cx="60" cy="60" r="56" stroke="#f59e0b" stroke-width="2" opacity="0.3"/>
                <circle cx="60" cy="60" r="38" stroke="#f59e0b" stroke-width="2" opacity="0.5"/>
                <circle cx="60" cy="60" r="20" stroke="#f59e0b" stroke-width="2" opacity="0.7"/>
                <circle cx="60" cy="60" r="8"  fill="#f59e0b" opacity="0.9"/>
                {{-- X mark --}}
                <line x1="42" y1="42" x2="78" y2="78" stroke="#ef4444" stroke-width="4" stroke-linecap="round"/>
                <line x1="78" y1="42" x2="42" y2="78" stroke="#ef4444" stroke-width="4" stroke-linecap="round"/>
            </svg>
        </div>

        {{-- Error code --}}
        <p class="text-8xl font-black mb-2" style="font-family:'Barlow',sans-serif; color:#f59e0b; letter-spacing:-2px;">403</p>

        {{-- Heading --}}
        <h1 class="text-2xl font-black text-white mb-3" style="font-family:'Barlow',sans-serif; letter-spacing:0.04em;">
            ACCESS DENIED
        </h1>
        <p class="text-sm font-medium mb-8" style="color:#94a3b8;">
            {{ $exception->getMessage() ?: "You don't have permission to view this page." }}
        </p>

        {{-- Countdown indicator --}}
        <div class="flex items-center justify-center gap-3 mb-6">
            <div class="relative h-8 w-8">
                <svg viewBox="0 0 36 36" class="h-8 w-8 -rotate-90">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f59e0b" stroke-width="3"
                            stroke-dasharray="100" stroke-linecap="round"
                            class="countdown-ring" id="countdown-circle"/>
                </svg>
                <span class="absolute inset-0 flex items-center justify-center text-xs font-black" style="color:#f59e0b; font-family:'Barlow',sans-serif;" id="countdown-num">3</span>
            </div>
            <p class="text-xs font-medium" style="color:#64748b;">Redirecting back in <span id="countdown-text">3</span> seconds…</p>
        </div>

        {{-- Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="history.back()"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-black tracking-wide transition-all active:scale-95"
                    style="background:#f59e0b; color:#0f172a; font-family:'Barlow',sans-serif;">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                GO BACK
            </button>
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold transition-all"
               style="background:rgba(255,255,255,0.07); color:#94a3b8; border:1px solid rgba(255,255,255,0.1);">
                Home
            </a>
        </div>

    </div>

    <script>
        let count = 3;
        const numEl = document.getElementById('countdown-num');
        const textEl = document.getElementById('countdown-text');

        const interval = setInterval(() => {
            count--;
            if (numEl) numEl.textContent = count;
            if (textEl) textEl.textContent = count;
            if (count <= 0) {
                clearInterval(interval);
                history.back();
            }
        }, 1000);
    </script>

</body>
</html>
