<div class="section-card p-8">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #4338ca, #6366f1)">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800">About Us</h2>
    </div>
    @if($club->tagline)
        <p class="text-lg text-indigo-700 font-medium mb-3">{{ $club->tagline }}</p>
    @endif
    @if($club->description)
        <p class="text-gray-600 leading-relaxed">{{ $club->description }}</p>
    @endif
</div>
