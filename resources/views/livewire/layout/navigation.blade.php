<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

{{-- ===== SIDEBAR COMPONENT ===== --}}
<div class="h-0">
    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden"
         style="display: none;">
    </div>

    {{-- Sidebar --}}
    <aside id="sidebar"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 w-72 transform transition-transform duration-300 ease-in-out lg:translate-x-0">

        <div class="flex flex-col h-full bg-white dark:bg-slate-800 border-r border-gray-200/70 dark:border-slate-700/50 shadow-sm">

            {{-- Logo Area --}}
            <div class="flex items-center gap-3 px-6 pt-7 pb-2">
                <div class="flex items-center justify-center w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-600 to-purple-600 shadow-lg shadow-brand-500/30">
                    {{-- Camera icon --}}
                    <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                        <circle cx="12" cy="13" r="3"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">LIFELINEMLG</h1>
                    <p class="text-[10px] font-medium text-gray-400 dark:text-slate-500 uppercase tracking-widest">Rental System</p>
                </div>

                {{-- Mobile close button --}}
                <button @click="sidebarOpen = false"
                        class="ml-auto p-1.5 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:text-slate-500 dark:hover:text-slate-300 dark:hover:bg-slate-700 lg:hidden transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Menu Label --}}
            <div class="px-6 pt-6 pb-2">
                <p class="text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Main Menu</p>
            </div>

            {{-- Navigation Links --}}
            <nav class="flex-1 px-4 space-y-1 overflow-y-auto scrollbar-thin">
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" wire:navigate
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   id="nav-dashboard">
                    <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                {{-- Camera Models --}}
                <a href="{{ route('camera-models.index') }}" wire:navigate
                   class="sidebar-link {{ request()->routeIs('camera-models.*') ? 'active' : '' }}"
                   id="nav-camera-models">
                    <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                        <circle cx="12" cy="13" r="3"/>
                    </svg>
                    <span>Camera Models</span>
                </a>

                {{-- Inventory --}}
                <a href="{{ route('inventory.index') }}" wire:navigate
                   class="sidebar-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}"
                   id="nav-inventory">
                    <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                    </svg>
                    <span>Inventory</span>
                </a>

                {{-- Customers --}}
                <a href="{{ route('customers.index') }}" wire:navigate
                   class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                   id="nav-customers">
                    <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Customers</span>
                </a>

                {{-- Transactions --}}
                <a href="{{ route('transactions.index') }}" wire:navigate
                   class="sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                   id="nav-transactions">
                    <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>
                    </svg>
                    <span>Transactions</span>
                </a>
            </nav>

            {{-- Bottom Section --}}
            <div class="px-4 pb-4 mt-auto space-y-3">

                {{-- Divider --}}
                <div class="border-t border-gray-200/70 dark:border-slate-700/50"></div>

                {{-- User Profile & Logout --}}
                <div class="flex items-center gap-3 px-3 py-2">
                    <a href="{{ route('profile') }}" wire:navigate class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-purple-500 text-white text-sm font-bold shadow-md shadow-brand-500/20 hover:shadow-lg hover:shadow-brand-500/30 transition-shadow">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </a>
                    <a href="{{ route('profile') }}" wire:navigate class="flex-1 min-w-0 group">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors"
                           x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                           x-text="name"
                           x-on:profile-updated.window="name = $event.detail.name"></p>
                        <p class="text-xs text-gray-400 dark:text-slate-500 truncate">{{ auth()->user()->email }}</p>
                    </a>

                    {{-- Logout Button --}}
                    <button wire:click="logout"
                            class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 dark:text-slate-500 dark:hover:text-red-400 dark:hover:bg-red-500/10 transition-all duration-200"
                            title="Logout"
                            id="logout-btn">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </aside>
</div>
