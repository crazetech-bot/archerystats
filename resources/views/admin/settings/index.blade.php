@extends('layouts.app')

@section('title', 'Settings')
@section('header', 'Settings')
@section('subheader', 'Branding, typography & archer management')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- ============================================================ --}}
    {{-- Registration Control                                         --}}
    {{-- ============================================================ --}}
    @php
        $regModules = [
            'archer' => [
                'label' => 'Archer',
                'desc'  => 'Self-registration for new archer accounts',
                'icon'  => 'M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z',
            ],
            'coach'  => [
                'label' => 'Coach',
                'desc'  => 'Self-registration for new coach accounts',
                'icon'  => 'M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5',
            ],
            'club'   => [
                'label' => 'Club',
                'desc'  => 'Self-registration for new club admin accounts',
                'icon'  => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z',
            ],
        ];
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #fff7ed, #fef2f2);">
            <span class="h-8 w-8 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Registration Control</h2>
                <p class="text-xs text-gray-500">Suspend or open public self-registration per module</p>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($regModules as $type => $mod)
            @php $isOpen = ($regSettings[$type] ?? '1') === '1'; @endphp
            <div class="flex items-center gap-4 px-6 py-4">
                {{-- Icon + label --}}
                <span class="h-9 w-9 rounded-xl flex items-center justify-center flex-shrink-0"
                      style="{{ $isOpen ? 'background:#f0fdf4;' : 'background:#fef2f2;' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                         style="{{ $isOpen ? 'color:#16a34a;' : 'color:#dc2626;' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $mod['icon'] }}"/>
                    </svg>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">{{ $mod['label'] }} Registration</p>
                    <p class="text-xs text-gray-400">{{ $mod['desc'] }}</p>
                </div>
                {{-- Status badge --}}
                <span class="px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-widest"
                      style="{{ $isOpen
                          ? 'background:#dcfce7; color:#15803d;'
                          : 'background:#fee2e2; color:#b91c1c;' }}">
                    {{ $isOpen ? 'Open' : 'Suspended' }}
                </span>
                {{-- Toggle form --}}
                <form method="POST" action="{{ route('admin.settings.registration') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="value" value="{{ $isOpen ? '0' : '1' }}">
                    <button type="submit"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-all"
                            style="{{ $isOpen
                                ? 'background:#fff1f2; border-color:#fca5a5; color:#dc2626;'
                                : 'background:#f0fdf4; border-color:#86efac; color:#15803d;' }}"
                            onclick="return confirm('{{ $isOpen ? 'Suspend ' . $mod['label'] . ' registration?' : 'Open ' . $mod['label'] . ' registration?' }}')">
                        {{ $isOpen ? 'Suspend' : 'Open Registration' }}
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>

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

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
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

    {{-- Account Management --}}
    @php
        $meId = auth()->id();
        $archerList = $archerUsers->map(fn($u) => [
            'id'     => $u->id,
            'name'   => $u->name,
            'email'  => $u->email,
            'refNo'  => $u->archer?->ref_no ?? '',
            'status' => $u->status ?? 'active',
            'isMe'   => $u->id === $meId,
        ])->values()->toArray();
        $coachList = $coachUsers->map(fn($u) => [
            'id'     => $u->id,
            'name'   => $u->name,
            'email'  => $u->email,
            'refNo'  => $u->coach?->ref_no ?? '',
            'status' => $u->status ?? 'active',
            'isMe'   => $u->id === $meId,
        ])->values()->toArray();
        $clubList = $clubAdminUsers->map(fn($u) => [
            'id'     => $u->id,
            'name'   => $u->name,
            'email'  => $u->email,
            'refNo'  => $u->club?->name ?? '',
            'status' => $u->status ?? 'active',
            'isMe'   => $u->id === $meId,
        ])->values()->toArray();
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
         x-data="{
             tab: 'archer',
             search: '',
             archerList: {{ Js::from($archerList) }},
             coachList:  {{ Js::from($coachList) }},
             clubList:   {{ Js::from($clubList) }},
             get filtered() {
                 const list = this.tab === 'archer' ? this.archerList
                            : this.tab === 'coach'  ? this.coachList
                            : this.clubList;
                 const q = this.search.toLowerCase();
                 return q ? list.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q)) : list;
             }
         }">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #ede9fe, #ddd6fe);">
            <span class="h-8 w-8 rounded-xl flex items-center justify-center flex-shrink-0"
                  style="background:rgba(109,40,217,0.12);">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Account Management</h2>
                <p class="text-xs text-gray-500">Suspend, reactivate or delete individual accounts</p>
            </div>
        </div>

        {{-- Tabs + Search --}}
        <div class="px-6 pt-4 pb-2 flex flex-wrap items-center gap-3 border-b border-gray-100">
            <div class="flex gap-1">
                <button type="button" @click="tab='archer'; search=''"
                        :class="tab==='archer' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'"
                        class="px-3 py-1.5 rounded-lg border text-xs font-bold transition-all">
                    Archers
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs font-bold"
                          :class="tab==='archer' ? 'bg-amber-200 text-amber-900' : 'bg-gray-200 text-gray-600'">
                        {{ count($archerList) }}
                    </span>
                </button>
                <button type="button" @click="tab='coach'; search=''"
                        :class="tab==='coach' ? 'bg-teal-100 text-teal-800 border-teal-300' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'"
                        class="px-3 py-1.5 rounded-lg border text-xs font-bold transition-all">
                    Coaches
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs font-bold"
                          :class="tab==='coach' ? 'bg-teal-200 text-teal-900' : 'bg-gray-200 text-gray-600'">
                        {{ count($coachList) }}
                    </span>
                </button>
                <button type="button" @click="tab='club'; search=''"
                        :class="tab==='club' ? 'bg-indigo-100 text-indigo-800 border-indigo-300' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'"
                        class="px-3 py-1.5 rounded-lg border text-xs font-bold transition-all">
                    Club Admins
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs font-bold"
                          :class="tab==='club' ? 'bg-indigo-200 text-indigo-900' : 'bg-gray-200 text-gray-600'">
                        {{ count($clubList) }}
                    </span>
                </button>
            </div>
            <div class="ml-auto relative">
                <input type="text" x-model="search" placeholder="Search by name or email…"
                       class="pl-8 pr-3 py-1.5 text-xs rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:border-violet-400 focus:ring-1 focus:ring-violet-300 outline-none w-52 transition">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </div>
        </div>

        {{-- User rows --}}
        <div class="divide-y divide-gray-50 min-h-[60px]">
            <template x-for="u in filtered" :key="u.id">
                <div class="px-6 py-3 flex items-center gap-4 hover:bg-gray-50 transition-colors">

                    {{-- Avatar --}}
                    <div class="h-9 w-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold"
                         :style="tab==='archer' ? 'background:#fef3c7;color:#92400e;'
                               : tab==='coach'  ? 'background:#ccfbf1;color:#0f766e;'
                               : 'background:#e0e7ff;color:#3730a3;'"
                         x-text="u.name.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2)">
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate" x-text="u.name"></p>
                        <p class="text-xs text-gray-400 truncate">
                            <span x-text="u.email"></span>
                            <template x-if="u.refNo">
                                <span> · <span x-text="u.refNo"></span></span>
                            </template>
                        </p>
                    </div>

                    {{-- Status badge --}}
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold flex-shrink-0"
                          :class="u.status === 'active'
                              ? 'bg-green-100 text-green-700'
                              : 'bg-red-100 text-red-700'"
                          x-text="u.status === 'active' ? 'Active' : 'Suspended'">
                    </span>

                    {{-- Actions --}}
                    <div x-show="!u.isMe" class="flex items-center gap-2 flex-shrink-0">

                        {{-- Suspend / Reactivate --}}
                        <form :action="`{{ url('admin/accounts') }}/${u.id}/toggle-status`"
                              method="POST" style="display:inline;">
                            @csrf
                            <button type="submit"
                                    :class="u.status === 'active'
                                        ? 'text-amber-700 bg-amber-50 hover:bg-amber-100 border-amber-200'
                                        : 'text-green-700 bg-green-50 hover:bg-green-100 border-green-200'"
                                    :onclick="u.status === 'active'
                                        ? `return confirm('Suspend ${u.name}? They will not be able to log in.')`
                                        : `return confirm('Reactivate ${u.name}?')`"
                                    class="px-2.5 py-1 rounded-lg border text-xs font-bold transition-colors"
                                    x-text="u.status === 'active' ? 'Suspend' : 'Reactivate'">
                            </button>
                        </form>

                        {{-- Delete --}}
                        <form :action="`{{ url('admin/users') }}/${u.id}`"
                              method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    :onclick="`return confirm('Permanently delete ${u.name}? This cannot be undone.')`"
                                    class="px-2.5 py-1 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold transition-colors">
                                Delete
                            </button>
                        </form>

                    </div>
                    <div x-show="u.isMe" class="text-xs text-gray-300 italic flex-shrink-0">you</div>
                </div>
            </template>

            {{-- Empty state --}}
            <div x-show="filtered.length === 0" class="px-6 py-8 text-center text-sm text-gray-400">
                <span x-show="search">No accounts match "<span x-text="search"></span>".</span>
                <span x-show="!search">No accounts in this category.</span>
            </div>
        </div>
    </div>

    {{-- Admin Users --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
            <span class="h-8 w-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Admin Users</h2>
                <p class="text-xs text-gray-500">Manage super admins and club admins</p>
            </div>
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($adminUsers as $admin)
                <div class="px-6 py-4" x-data="{ changingPw: false }">
                    <div class="flex items-center gap-4">
                        @php
                            $badgeBg = match($admin->role) {
                                'super_admin'  => 'background:#fef3c7;color:#b45309;',
                                'state_admin'  => 'background:#d1fae5;color:#065f46;',
                                default        => 'background:#ede9fe;color:#6d28d9;',
                            };
                            $badgeLabel = match($admin->role) {
                                'super_admin'  => 'Super Admin',
                                'state_admin'  => 'State Admin',
                                default        => 'Club Admin',
                            };
                        @endphp
                        <div class="h-9 w-9 rounded-xl flex items-center justify-center flex-shrink-0 font-bold text-sm"
                             style="{{ $badgeBg }}">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $admin->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $admin->email }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0"
                              style="{{ $badgeBg }}">
                            {{ $badgeLabel }}
                        </span>
                        <button type="button" @click="changingPw = !changingPw"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition flex-shrink-0">
                            Change Password
                        </button>
                        @if($admin->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $admin) }}"
                                  onsubmit="return confirm('Remove {{ addslashes($admin->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition flex-shrink-0">
                                    Remove
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Inline change-password form --}}
                    <div x-show="changingPw" x-cloak class="mt-4 pl-13">
                        <form method="POST" action="{{ route('admin.users.password', $admin) }}">
                            @csrf @method('PUT')
                            <div class="flex gap-3 items-end flex-wrap">
                                <div class="flex-1 min-w-40">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">New Password</label>
                                    <input type="password" name="password" required minlength="8"
                                           placeholder="Min. 8 characters"
                                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm px-3 py-2 focus:border-indigo-500 focus:bg-white outline-none transition">
                                </div>
                                <div class="flex-1 min-w-40">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Confirm Password</label>
                                    <input type="password" name="password_confirmation" required
                                           placeholder="Repeat password"
                                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm px-3 py-2 focus:border-indigo-500 focus:bg-white outline-none transition">
                                </div>
                                <button type="submit"
                                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                                        style="background:linear-gradient(135deg,#4338ca,#6366f1);">
                                    Update
                                </button>
                                <button type="button" @click="changingPw = false"
                                        class="px-4 py-2 rounded-xl text-sm font-semibold text-gray-500 border border-gray-200 hover:bg-gray-50 transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            @if($adminUsers->isEmpty())
                <div class="px-6 py-8 text-center text-sm text-gray-400">No admin users found.</div>
            @endif
        </div>
    </div>

    {{-- Add New Admin --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
             style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe);">
            <span class="h-8 w-8 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Add New Admin</h2>
                <p class="text-xs text-gray-500">Create a new super admin or club admin account</p>
            </div>
        </div>

        <div class="p-6">
            @if($errors->hasBag('default') || $errors->any())
                <div class="mb-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4"
                  x-data="newAdminForm"
                  @submit.prevent="if (role === 'club_admin' && clubSource === 'new' && isDuplicate) { return; } $el.submit()">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Admin name"
                               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm px-4 py-2.5
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="admin@example.com"
                               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm px-4 py-2.5
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Role <span class="text-red-500">*</span></label>
                        <select name="role" x-model="role" required
                                class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm px-4 py-2.5
                                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                            <option value="">Select role…</option>
                            <option value="super_admin"  @selected(old('role') === 'super_admin')>Super Admin</option>
                            <option value="club_admin"   @selected(old('role') === 'club_admin')>Club Admin</option>
                            <option value="state_admin"  @selected(old('role') === 'state_admin')>State Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required minlength="8"
                               placeholder="Min. 8 characters"
                               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm px-4 py-2.5
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    </div>
                    <div class="sm:col-start-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required
                               placeholder="Repeat password"
                               class="block w-full rounded-xl border border-gray-300 bg-gray-50 text-sm px-4 py-2.5
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition">
                    </div>
                </div>

                {{-- Club assignment — only shown for Club Admin --}}
                <div x-show="role === 'club_admin'" x-cloak
                     class="rounded-2xl border border-indigo-100 bg-indigo-50/40 p-4 space-y-3">
                    <p class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Club Assignment <span class="text-red-500 normal-case font-normal">*</span></p>

                    {{-- Toggle: existing vs new --}}
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="club_source" value="existing"
                                   x-model="clubSource"
                                   class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-semibold text-gray-700">Select existing club</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="club_source" value="new"
                                   x-model="clubSource"
                                   class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-semibold text-gray-700">Create new club</span>
                        </label>
                    </div>

                    {{-- Existing club dropdown --}}
                    <div x-show="clubSource === 'existing'" x-cloak>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Club</label>
                        <select name="club_id"
                                class="block w-full rounded-xl border border-gray-300 bg-white text-sm px-4 py-2.5
                                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            <option value="">— Select a club —</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" @selected(old('club_id') == $club->id)>
                                    {{ $club->name }}@if($club->state) ({{ $club->state }})@endif
                                </option>
                            @endforeach
                        </select>
                        @error('club_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- New club name input --}}
                    <div x-show="clubSource === 'new'" x-cloak>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">New Club Name</label>
                        <input type="text" name="new_club_name" x-model="newClubName"
                               placeholder="e.g. Kelantan Archery Club"
                               :class="isDuplicate
                                   ? 'border-amber-400 bg-amber-50 focus:border-amber-500 focus:ring-amber-400/20'
                                   : 'border-gray-300 bg-white focus:border-indigo-500 focus:ring-indigo-500/20'"
                               class="block w-full rounded-xl border text-sm px-4 py-2.5 focus:ring-2 outline-none transition">
                        @error('new_club_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror

                        {{-- Duplicate warning --}}
                        <div x-show="isDuplicate" x-cloak
                             class="mt-2 flex items-start gap-2 rounded-xl border border-amber-300 bg-amber-50 px-3 py-2.5">
                            <svg class="h-4 w-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                            <div class="text-xs text-amber-800">
                                <p class="font-bold">Club already exists</p>
                                <p>"<span x-text="matchedClub"></span>" is already in the system. Select it from the <button type="button" @click="clubSource = 'existing'" class="underline font-semibold">existing clubs</button> list instead.</p>
                            </div>
                        </div>

                        <p x-show="!isDuplicate" class="mt-1 text-xs text-gray-400">A new club with this name will be created automatically.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                            :disabled="role === 'club_admin' && clubSource === 'new' && isDuplicate"
                            :class="role === 'club_admin' && clubSource === 'new' && isDuplicate
                                ? 'opacity-40 cursor-not-allowed'
                                : 'hover:opacity-90 active:scale-95'"
                            class="px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow transition"
                            style="background:linear-gradient(135deg,#4338ca,#6366f1);">
                        Create Admin Account
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('newAdminForm', () => ({
        role: '{{ old('role', '') }}',
        clubSource: '{{ old('club_source', 'existing') }}',
        newClubName: '{{ old('new_club_name', '') }}',
        existingClubNames: @json($clubs->pluck('name')),
        get isDuplicate() {
            const v = this.newClubName.trim().toLowerCase();
            return v.length > 0 && this.existingClubNames.some(n => n.toLowerCase() === v);
        },
        get matchedClub() {
            const v = this.newClubName.trim().toLowerCase();
            return this.existingClubNames.find(n => n.toLowerCase() === v) || null;
        }
    }));
});
</script>
@endpush
