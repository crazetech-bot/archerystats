<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Archery Stats</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center">

<div class="w-full max-w-sm">
    <div class="bg-white rounded-xl shadow-md px-8 py-10">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-indigo-700">Archery Stats</h1>
            <p class="text-sm text-gray-500 mt-1">Sign in to your account</p>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-3">
                <p class="text-sm text-red-700">{{ $errors->first() }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}" required autofocus
                       class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                              focus:border-indigo-500 focus:ring-indigo-500
                              @error('email') border-red-400 @enderror">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="block w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3
                              focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
            </div>

            <button type="submit"
                    class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold
                           text-white shadow hover:bg-indigo-500 focus:ring-2 focus:ring-indigo-500">
                Sign in
            </button>
        </form>
    </div>
</div>

</body>
</html>
