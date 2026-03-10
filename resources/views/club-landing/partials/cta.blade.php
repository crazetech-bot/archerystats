<div class="rounded-2xl overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 100%)">
    <div class="px-8 py-12 text-center text-white">
        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
        </div>
        <h2 class="text-2xl sm:text-3xl font-black mb-3">Join {{ $club->name }}</h2>
        <p class="text-indigo-200 text-base mb-8 max-w-xl mx-auto">
            Track your scores, monitor your progress, and connect with your coaches — all in one place.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-white text-indigo-700 font-bold text-base shadow-lg hover:bg-indigo-50 transition-colors w-full sm:w-auto justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Register Now
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-white/20 text-white font-semibold text-base hover:bg-white/30 transition-colors w-full sm:w-auto justify-center">
                Already a member? Login
            </a>
        </div>
    </div>
</div>
