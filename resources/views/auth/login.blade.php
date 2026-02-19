<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Archery Stats</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full" style="background: linear-gradient(135deg, #3730a3 0%, #4f46e5 50%, #6366f1 100%);">

<div class="min-h-full flex items-center justify-center p-4">
    <div class="w-full max-w-md">

        {{-- Logo / Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl mb-4"
                 style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 6a4 4 0 100 8 4 4 0 000-8zm0 2a2 2 0 110 4 2 2 0 010-4z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Archery Stats</h1>
            <p class="text-indigo-200 mt-1 text-sm">Management System</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-8">
            <h2 class="text-xl font-bold text-gray-900 mb-1">Welcome back</h2>
            <p class="text-sm text-gray-500 mb-6">Sign in to your account to continue</p>

            @if($errors->any())
                <div class="mb-5 flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
                    <svg class="h-5 w-5 text-red-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}" required autofocus
                           placeholder="you@example.com"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm
                                  text-gray-900 placeholder-gray-400 transition
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none
                                  @error('email') border-red-400 bg-red-50 @enderror">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••"
                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm
                                  text-gray-900 placeholder-gray-400 transition
                                  focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-lg
                               transition-all duration-150 hover:opacity-90 active:scale-95"
                        style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                    Sign in
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-indigo-300 mt-6">&copy; {{ date('Y') }} Archery Stats Management System</p>
    </div>
</div>

</body>
</html>
