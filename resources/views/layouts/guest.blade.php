<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="themeManager()"
      x-init="init()"
      :class="{ 'dark': isDark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="LIFELINEMLG — Sign in to your Inventory & Rental System.">

        <title>{{ config('app.name', 'LIFELINEMLG') }} — Sign In</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8
                    bg-gradient-to-br from-gray-50 via-brand-50/30 to-purple-50/20
                    dark:from-slate-900 dark:via-slate-900 dark:to-brand-950/20
                    transition-colors duration-300">

            {{-- Logo --}}
            <div class="mb-8 text-center">
                <a href="/" class="inline-flex items-center gap-3 group">
                    <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-600 to-purple-600 shadow-xl shadow-brand-500/30 group-hover:shadow-brand-500/50 transition-shadow">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                            <circle cx="12" cy="13" r="3"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">LIFELINEMLG</h1>
                        <p class="text-[10px] font-medium text-gray-400 dark:text-slate-500 uppercase tracking-widest">Rental System</p>
                    </div>
                </a>
            </div>

            {{-- Card --}}
            <div class="w-full sm:max-w-md">
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-slate-900/50
                            border border-gray-100 dark:border-slate-700/50
                            px-8 py-8 sm:px-10 sm:py-10">
                    {{ $slot }}
                </div>

                {{-- Theme toggle --}}
                <div class="flex justify-center mt-6">
                    <button @click="toggleTheme()"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-800/60 transition-colors"
                            id="guest-theme-toggle">
                        <template x-if="isDark">
                            <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                            </svg>
                        </template>
                        <template x-if="!isDark">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                            </svg>
                        </template>
                        <span x-text="isDark ? 'Light Mode' : 'Dark Mode'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Theme Manager --}}
        <script>
            function themeManager() {
                return {
                    isDark: false,
                    init() {
                        const stored = localStorage.getItem('theme');
                        if (stored === 'dark') {
                            this.isDark = true;
                        } else if (stored === 'light') {
                            this.isDark = false;
                        } else {
                            this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        }
                    },
                    toggleTheme() {
                        this.isDark = !this.isDark;
                        localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    }
                }
            }
        </script>
    </body>
</html>
