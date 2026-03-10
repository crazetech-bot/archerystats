@extends('layouts.app')

@section('title', 'Club Page Settings')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100">
        <div class="px-6 py-4 flex items-center gap-3" style="background: linear-gradient(135deg, #4338ca, #6366f1)">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">Club Public Page</h1>
                <p class="text-indigo-200 text-sm">
                    Customise what visitors see at
                    <a href="{{ url('/') }}" target="_blank" class="underline text-indigo-100">
                        {{ $club->slug }}.sportdns.com
                    </a>
                </p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.club-page.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Club Info --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Basic Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-700">Club Information</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Displayed publicly on your club page</p>
                    </div>
                    <div class="p-6 space-y-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Club Logo</label>
                            @if($clubLogo)
                            <div class="flex items-center gap-4 mb-3">
                                <img src="{{ $clubLogo }}" alt="Current logo" class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                                <div class="text-sm text-gray-500">Current logo</div>
                            </div>
                            @endif
                            <input type="file" name="logo" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP. Max 2MB. Recommended: square, at least 200×200px.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tagline</label>
                            <input type="text" name="tagline" value="{{ old('tagline', $club->tagline) }}"
                                   placeholder="e.g. Precision. Discipline. Excellence."
                                   class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">About / Description</label>
                            <textarea name="description" rows="4" placeholder="Tell visitors about your club..."
                                      class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('description', $club->description) }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- Contact Details --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-700">Contact Details</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $club->contact_email) }}"
                                   placeholder="info@yourclub.com"
                                   class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $club->contact_phone) }}"
                                   placeholder="+60 12-345 6789"
                                   class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                            <textarea name="address" rows="2" placeholder="Full club address"
                                      class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('address', $club->address) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">State</label>
                            <select name="state" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">— Select State —</option>
                                @foreach (\App\Models\Archer::MALAYSIAN_STATES as $st)
                                    <option value="{{ $st }}" {{ old('state', $club->state) === $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Website</label>
                            <input type="url" name="website" value="{{ old('website', $club->website) }}"
                                   placeholder="https://yourclub.com"
                                   class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                    </div>
                </div>

                {{-- Social Links --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-700">Social Media</h2>
                    </div>
                    <div class="p-6 space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Facebook URL</label>
                            <input type="url" name="facebook_url" value="{{ old('facebook_url', $club->facebook_url) }}"
                                   placeholder="https://facebook.com/yourclub"
                                   class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Instagram URL</label>
                            <input type="url" name="instagram_url" value="{{ old('instagram_url', $club->instagram_url) }}"
                                   placeholder="https://instagram.com/yourclub"
                                   class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $club->whatsapp_number) }}"
                                   placeholder="+60123456789"
                                   class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <p class="text-xs text-gray-400 mt-1">Include country code, digits only. E.g. 60123456789</p>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Right: Section Toggles --}}
            <div class="space-y-6">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-4">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-700">Page Sections</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Choose which sections appear on your public page</p>
                    </div>
                    <div class="divide-y divide-gray-50">

                        @foreach ([
                            ['key' => 'page_hero_enabled',    'label' => 'Hero Banner',   'desc' => 'Large header with club name and logo'],
                            ['key' => 'page_about_enabled',   'label' => 'About Section', 'desc' => 'Club description and tagline'],
                            ['key' => 'page_contact_enabled', 'label' => 'Contact Info',  'desc' => 'Phone, email, address'],
                            ['key' => 'page_social_enabled',  'label' => 'Social Links',  'desc' => 'Facebook, Instagram, WhatsApp'],
                            ['key' => 'page_cta_enabled',     'label' => 'Join CTA',      'desc' => '"Join Club" registration button'],
                        ] as $toggle)
                        <div class="px-6 py-4 flex items-center justify-between gap-3" x-data>
                            <div>
                                <div class="text-sm font-medium text-gray-700">{{ $toggle['label'] }}</div>
                                <div class="text-xs text-gray-400">{{ $toggle['desc'] }}</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="hidden" name="{{ $toggle['key'] }}" value="0">
                                <input type="checkbox" name="{{ $toggle['key'] }}" value="1"
                                       class="sr-only peer"
                                       {{ ($settings[$toggle['key']] ?? '1') === '1' ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        @endforeach

                    </div>

                    <div class="px-6 py-4 border-t border-gray-100">
                        <button type="submit"
                                class="w-full py-2.5 rounded-xl text-white text-sm font-semibold transition-opacity hover:opacity-90"
                                style="background: linear-gradient(135deg, #4338ca, #6366f1)">
                            Save Changes
                        </button>
                        <a href="{{ url('/') }}" target="_blank"
                           class="block text-center mt-2 text-xs text-indigo-600 hover:underline">
                            Preview public page ↗
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>
@endsection
