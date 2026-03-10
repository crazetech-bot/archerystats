<div class="section-card p-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #4338ca, #6366f1)">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Contact Us</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        @if($club->contact_email)
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-400 mb-0.5 font-medium uppercase tracking-wide">Email</div>
                <a href="mailto:{{ $club->contact_email }}" class="text-indigo-700 font-medium hover:underline text-sm">
                    {{ $club->contact_email }}
                </a>
            </div>
        </div>
        @endif

        @if($club->contact_phone)
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-400 mb-0.5 font-medium uppercase tracking-wide">Phone</div>
                <a href="tel:{{ $club->contact_phone }}" class="text-gray-700 font-medium hover:text-indigo-700 text-sm">
                    {{ $club->contact_phone }}
                </a>
            </div>
        </div>
        @endif

        @if($club->address)
        <div class="flex items-start gap-3 sm:col-span-2">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-400 mb-0.5 font-medium uppercase tracking-wide">Address</div>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $club->address }}</p>
            </div>
        </div>
        @endif

        @if($club->website)
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-400 mb-0.5 font-medium uppercase tracking-wide">Website</div>
                <a href="{{ $club->website }}" target="_blank" class="text-indigo-700 font-medium hover:underline text-sm">
                    {{ preg_replace('/^https?:\/\//', '', $club->website) }}
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
