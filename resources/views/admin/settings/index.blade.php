@extends('layouts.app')

@section('title', 'Settings')
@section('header', 'Settings')
@section('subheader', 'Branding, typography & archer management')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Branding --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #f8faff, #f0f4ff);">
                <span class="h-8 w-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Branding</h2>
                    <p class="text-xs text-gray-500">Upload your organisation logo</p>
                </div>
            </div>
            <div class="p-6"
                 x-data="{ preview: '{{ !empty($settings['logo']) ? asset('storage/' . $settings['logo']) : '' }}', hasLogo: {{ !empty($settings['logo']) ? 'true' : 'false' }} }">
                <div class="flex items-start gap-6">

                    {{-- Preview --}}
                    <div class="flex-shrink-0">
                        <div x-show="hasLogo || preview"
                             class="h-24 w-36 rounded-2xl border-2 border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden shadow-sm">
                            <img :src="preview || '{{ !empty($settings['logo']) ? asset('storage/' . $settings['logo']) : '' }}'"
                                 class="h-full w-full object-contain p-2" alt="Logo preview">
                        </div>
                        <div x-show="!hasLogo && !preview"
                             class="h-24 w-36 rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center text-gray-400">
                            <svg class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                            </svg>
                            <span class="text-xs">No logo</span>
                        </div>
                    </div>

                    {{-- Upload --}}
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Upload Logo</label>
                        <input type="file" name="logo" accept="image/png,image/jpg,image/jpeg,image/webp,image/svg+xml"
                               @change="const f = $event.target.files[0]; if(f){ preview = URL.createObjectURL(f); hasLogo = true; }"
                               class="block w-full text-sm text-gray-500
                                      file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                                      file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100 file:cursor-pointer">
                        @error('logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-2 text-xs text-gray-400">PNG, JPG, WEBP or SVG &mdash; max 2MB. Recommended: transparent background.</p>

                        @if(!empty($settings['logo']))
                            <button type="button" form="remove-logo-form"
                                    class="mt-3 text-xs font-medium text-red-500 hover:text-red-700 hover:underline"
                                    onclick="if(confirm('Remove logo?')) document.getElementById('remove-logo-form').submit()">
                                &times; Remove current logo
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Typography --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #fffbeb, #fef3c7);">
                <span class="h-8 w-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Typography</h2>
                    <p class="text-xs text-gray-500">Google Fonts for body and headings</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 gap-5 sm:grid-cols-3"
                 x-data="{
                     bodyFont: '{{ $settings['body_font'] ?? 'Inter' }}',
                     headingFont: '{{ $settings['heading_font'] ?? 'Inter' }}',
                     headingSize: '{{ $settings['heading_size'] ?? '20' }}'
                 }">

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Body Font</label>
                    <select name="body_font" x-model="bodyFont"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                        @foreach($googleFonts as $font)
                            <option value="{{ $font }}" @selected(($settings['body_font'] ?? 'Inter') === $font)>{{ $font }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-400" :style="`font-family: '${bodyFont}', sans-serif`">
                        The quick brown fox jumps over the lazy dog.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Heading Font</label>
                    <select name="heading_font" x-model="headingFont"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                        @foreach($googleFonts as $font)
                            <option value="{{ $font }}" @selected(($settings['heading_font'] ?? 'Inter') === $font)>{{ $font }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs font-bold text-gray-500" :style="`font-family: '${headingFont}', sans-serif`">
                        Page Heading Preview
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Heading Size</label>
                    <select name="heading_size" x-model="headingSize"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                        @foreach($headingSizes as $px => $label)
                            <option value="{{ $px }}" @selected(($settings['heading_size'] ?? '20') === (string)$px)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 font-bold text-gray-500" :style="`font-size: ${headingSize}px; font-family: '${headingFont}', sans-serif`">
                        Heading
                    </p>
                </div>

                <div class="sm:col-span-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700">
                    <strong>Note:</strong> Font preview in this panel uses live rendering. Changes apply across the whole site after saving.
                </div>
            </div>
        </div>

        {{-- Login Page Typography --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe);">
                <span class="h-8 w-8 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Login Page Typography</h2>
                    <p class="text-xs text-gray-500">Fonts shown on the sign-in screen (independent of main app fonts)</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 gap-5 sm:grid-cols-3"
                 x-data="{
                     loginBodyFont: '{{ $settings['login_body_font'] ?? ($settings['body_font'] ?? 'Inter') }}',
                     loginHeadingFont: '{{ $settings['login_heading_font'] ?? ($settings['heading_font'] ?? 'Inter') }}',
                     loginHeadingSize: '{{ $settings['login_heading_size'] ?? ($settings['heading_size'] ?? '28') }}'
                 }">

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Login Body Font</label>
                    <select name="login_body_font" x-model="loginBodyFont"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                        @foreach($googleFonts as $font)
                            <option value="{{ $font }}" @selected(($settings['login_body_font'] ?? ($settings['body_font'] ?? 'Inter')) === $font)>{{ $font }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-400" :style="`font-family: '${loginBodyFont}', sans-serif`">
                        The quick brown fox jumps over the lazy dog.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Login Heading Font</label>
                    <select name="login_heading_font" x-model="loginHeadingFont"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                        @foreach($googleFonts as $font)
                            <option value="{{ $font }}" @selected(($settings['login_heading_font'] ?? ($settings['heading_font'] ?? 'Inter')) === $font)>{{ $font }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs font-bold text-gray-500" :style="`font-family: '${loginHeadingFont}', sans-serif`">
                        Welcome back
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Login Heading Size</label>
                    <select name="login_heading_size" x-model="loginHeadingSize"
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                        @foreach($headingSizes as $px => $label)
                            <option value="{{ $px }}" @selected(($settings['login_heading_size'] ?? ($settings['heading_size'] ?? '28')) === (string)$px)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 font-bold text-gray-500" :style="`font-size: ${loginHeadingSize}px; font-family: '${loginHeadingFont}', sans-serif`">
                        Welcome
                    </p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                 style="background: linear-gradient(135deg, #faf5ff, #f3e8ff);">
                <span class="h-8 w-8 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Footer</h2>
                    <p class="text-xs text-gray-500">Copyright text shown on the login page and main layout</p>
                </div>
            </div>
            <div class="p-6">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Footer Text</label>
                <input type="text" name="footer_text"
                       value="{{ $settings['footer_text'] ?? '' }}"
                       placeholder="© {{ date('Y') }} Archery Stats Management System"
                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm py-2.5 px-4
                              focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                <p class="mt-2 text-xs text-gray-400">Leave blank to use the default copyright text.</p>
            </div>
        </div>

        {{-- Save --}}
        <div class="flex justify-end pb-2">
            <button type="submit"
                    class="px-8 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-95"
                    style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                Save Settings
            </button>
        </div>
    </form>

    {{-- Standalone remove-logo form (outside main form to avoid nesting) --}}
    @if(!empty($settings['logo']))
        <form id="remove-logo-form" method="POST" action="{{ route('admin.settings.logo.remove') }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif

    {{-- New Archers --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
            <div class="flex items-center gap-3">
                <span class="h-8 w-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Archers</h2>
                    <p class="text-xs text-gray-500">Overview &amp; quick actions</p>
                </div>
            </div>
            <a href="{{ route('archers.create') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow transition-all hover:opacity-90"
               style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                New Archer
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 divide-x divide-gray-100 border-b border-gray-100">
            <div class="px-6 py-4 text-center">
                <p class="text-2xl font-bold text-indigo-600">{{ $totalArchers }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total Archers</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="text-2xl font-bold text-emerald-600">{{ $newThisMonth }}</p>
                <p class="text-xs text-gray-500 mt-0.5">New This Month</p>
            </div>
        </div>

        {{-- Recent list --}}
        <div class="divide-y divide-gray-50">
            @forelse($recentArchers as $ra)
                <div class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50 transition-colors">
                    <img src="{{ $ra->photo_url }}" alt="{{ $ra->full_name }}"
                         class="h-9 w-9 rounded-xl object-cover bg-gray-100 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $ra->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $ra->ref_no }} &middot; {{ $ra->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('archers.show', $ra) }}"
                       class="text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline flex-shrink-0">View</a>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-gray-400">No archers registered yet.</div>
            @endforelse
        </div>

        @if($totalArchers > 6)
            <div class="px-6 py-3 border-t border-gray-100 text-center">
                <a href="{{ route('archers.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                    View all {{ $totalArchers }} archers →
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
