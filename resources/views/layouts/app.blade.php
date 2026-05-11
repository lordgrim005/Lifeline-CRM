<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="LIFELINEMLG Inventory & Rental System — Manage your camera rental business with ease.">

        <title>{{ config('app.name', 'LIFELINEMLG') }} — Inventory & Rental System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        @livewireStyles
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        {{-- ===== MAIN DASHBOARD SHELL ===== --}}
        <div class="min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-300"
             x-data="{ sidebarOpen: false }">

            {{-- ===== SIDEBAR (Livewire Component) ===== --}}
            <livewire:layout.navigation />

            {{-- ===== MAIN CONTENT AREA ===== --}}
            <div class="lg:ml-72 min-h-screen flex flex-col">

                {{-- ===== TOPBAR ===== --}}
                <header id="topbar"
                        class="sticky top-0 z-20 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl
                               border-b border-gray-200/70 dark:border-slate-700/50 shadow-sm shadow-gray-200/40 dark:shadow-slate-900/50">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                        {{-- Left: Hamburger + Search --}}
                        <div class="flex items-center gap-4 flex-1">
                            {{-- Mobile hamburger --}}
                            <button @click="sidebarOpen = true"
                                    class="p-2 -ml-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-700 lg:hidden transition-colors"
                                    id="mobile-menu-btn">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                                </svg>
                            </button>

                        </div>

                        {{-- Right: Greeting + Actions --}}
                        <div class="flex items-center gap-3">
                            {{-- Greeting --}}
                            <div class="hidden md:block text-right">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Hallo, Welcome! 👋
                                </p>
                                <p class="text-xs text-gray-400 dark:text-slate-500"
                                   x-data="{{ json_encode(['name' => auth()->user()->name ?? 'Admin']) }}"
                                   x-text="name"
                                   x-on:profile-updated.window="name = $event.detail.name"></p>
                            </div>


                            {{-- User Avatar (mobile only) --}}
                            <a href="{{ route('profile') }}" wire:navigate
                               class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-purple-500 text-white text-sm font-bold shadow-md shadow-brand-500/20 lg:hidden"
                               id="mobile-profile-btn">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </a>
                        </div>
                    </div>
                </header>

                {{-- ===== PAGE CONTENT ===== --}}
                <main class="flex-1 p-4 sm:p-6 lg:p-6 lg:pr-6">
                    {{-- Page Header --}}
                    @if (isset($header))
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endif

                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div x-data="{ show: true }" 
                             x-show="show" 
                             x-init="setTimeout(() => show = false, 5000)"
                             class="mb-6 flex items-center justify-between p-4 text-emerald-700 bg-emerald-50 rounded-2xl border border-emerald-100 shadow-sm shadow-emerald-200/40">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-500 text-white shadow-lg shadow-emerald-500/20">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    {{-- Page Body --}}
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
