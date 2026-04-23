<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - LIFELINEMLG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="h-full bg-brand-bg dark:bg-zinc-950 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl p-8 border border-zinc-100 dark:border-zinc-800 transition-colors">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-brand-primary dark:text-white tracking-tighter">LIFELINE<span class="text-brand-accent italic">MLG</span></h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-2 font-medium">Inventory & Rental System</p>
        </div>

        <form action="{{ url('/login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="username" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Username</label>
                <input type="text" name="username" id="username" required 
                    class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white"
                    placeholder="Enter your username" value="{{ old('username') }}">
                @error('username')
                    <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Password</label>
                <input type="password" name="password" id="password" required 
                    class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white"
                    placeholder="••••••••">
                @error('password')
                    <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-zinc-300 text-brand-primary focus:ring-brand-primary">
                    <span class="ml-2 text-sm text-zinc-600 dark:text-zinc-400">Remember me</span>
                </label>
            </div>

            <button type="submit" 
                class="w-full py-4 bg-brand-primary hover:bg-brand-primary/90 text-white rounded-xl font-bold text-lg shadow-lg shadow-brand-primary/20 transform active:scale-[0.98] transition-all">
                Login to System
            </button>
        </form>

        <p class="text-center mt-8 text-sm text-zinc-400">
            Internal Access Only
        </p>
    </div>
</body>
</html>
