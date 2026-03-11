<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Archery Stats')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="h-full">
<div class="min-h-full">

    {{-- Navigation --}}
    <nav class="bg-indigo-700 shadow">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-6">
                    <span class="text-white font-bold text-lg tracking-wide">Archery Stats</span>
                    @auth
                        <a href="{{ route('archers.index') }}"
                           class="text-indigo-200 hover:text-white text-sm font-medium">Archers</a>
                    @endauth
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <span class="text-indigo-200 text-sm">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-indigo-200 hover:text-white text-sm">Log out</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Page Header --}}
    @hasSection('header')
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <h1 class="text-xl font-semibold text-gray-900">@yield('header')</h1>
            </div>
        </header>
    @endif

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4"
             x-data="{ show: true }" x-show="show" x-cloak
             x-init="setTimeout(() => show = false, 4000)">
            <div class="rounded-md bg-green-50 border border-green-200 p-4 flex items-center justify-between">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
                <button @click="show = false" class="text-green-500 hover:text-green-700 text-lg leading-none">&times;</button>
            </div>
        </div>
    @endif

    {{-- Main --}}
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>

</div>
@stack('scripts')
</body>
</html>
