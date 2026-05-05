<?php

use Livewire\Volt\Component;
use App\Models\Camera;
use App\Models\Transaction;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function with()
    {
        $totalCameras = Camera::count();
        $availableCameras = Camera::where('status', 'Available')->count();
        $rentedCameras = Camera::where('status', 'Rented')->count();
        $maintenanceCameras = Camera::where('status', 'Maintenance')->count();
        
        $recentTransactions = Transaction::with('customer')
            ->where('status', 'Active')
            ->latest()
            ->take(5)
            ->get();

        return compact(
            'totalCameras',
            'availableCameras',
            'rentedCameras',
            'maintenanceCameras',
            'recentTransactions'
        );
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Welcome back! Here's your rental overview.</p>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-sm text-gray-400 dark:text-slate-500">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                </svg>
                <span>{{ now()->format('l, d F Y') }}</span>
            </div>
        </div>
    </x-slot>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-6">
        {{-- Total Cameras --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700/50 shadow-sm shadow-gray-100/50 dark:shadow-slate-900/50 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                        <circle cx="12" cy="13" r="3"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCameras }}</p>
            <p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Total Cameras</p>
        </div>

        {{-- Available --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700/50 shadow-sm shadow-gray-100/50 dark:shadow-slate-900/50 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-400 dark:text-slate-500">Ready</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $availableCameras }}</p>
            <p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Available</p>
        </div>

        {{-- Rented --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700/50 shadow-sm shadow-gray-100/50 dark:shadow-slate-900/50 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-500/10">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-400 dark:text-slate-500">Active</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rentedCameras }}</p>
            <p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Rented</p>
        </div>

        {{-- Maintenance --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700/50 shadow-sm shadow-gray-100/50 dark:shadow-slate-900/50 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 rounded-xl bg-red-50 dark:bg-red-500/10">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m14.5 9-5 5"/><path d="m9.5 9 5 5"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-400 dark:text-slate-500">Service</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maintenanceCameras }}</p>
            <p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Maintenance</p>
        </div>
    </div>

    {{-- Quick Actions & Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700/50 shadow-sm shadow-gray-100/50 dark:shadow-slate-900/50">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('transactions.create') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-brand-600 to-purple-600 text-white shadow-lg shadow-brand-500/25 hover:shadow-xl hover:shadow-brand-500/30 transition-all duration-300 group">
                    <div class="p-2 rounded-lg bg-white/20">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/><path d="M12 5v14"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">New Transaction</p>
                        <p class="text-xs text-white/70">Create a new rental</p>
                    </div>
                    <svg class="w-4 h-4 ml-auto opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>

                <a href="{{ route('customers.index') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-slate-700/40 hover:bg-gray-100 dark:hover:bg-slate-700/60 transition-colors group">
                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-500/20">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Add Customer</p>
                        <p class="text-xs text-gray-400 dark:text-slate-500">Register new customer</p>
                    </div>
                    <svg class="w-4 h-4 ml-auto text-gray-300 dark:text-slate-600 group-hover:text-gray-400 dark:group-hover:text-slate-500 group-hover:translate-x-1 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>

                <a href="{{ route('inventory.index') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-slate-700/40 hover:bg-gray-100 dark:hover:bg-slate-700/60 transition-colors group">
                    <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-500/20">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Add Camera</p>
                        <p class="text-xs text-gray-400 dark:text-slate-500">Register new inventory</p>
                    </div>
                    <svg class="w-4 h-4 ml-auto text-gray-300 dark:text-slate-600 group-hover:text-gray-400 dark:group-hover:text-slate-500 group-hover:translate-x-1 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-100 dark:border-slate-700/50 shadow-sm shadow-gray-100/50 dark:shadow-slate-900/50">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Active Rentals</h2>
                <a href="{{ route('transactions.index') }}" wire:navigate class="text-sm text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-medium transition-colors">See all</a>
            </div>

            @if($recentTransactions->isEmpty())
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-slate-700/40 mb-4">
                        <svg class="w-8 h-8 text-gray-300 dark:text-slate-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                            <circle cx="12" cy="13" r="3"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400">No active rentals</p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Your recent transactions will appear here</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($recentTransactions as $trx)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-slate-700/30 border border-gray-100 dark:border-slate-700/50">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">#TRX-{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }} &mdash; {{ $trx->customer->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Return Due: <span class="font-medium {{ \Carbon\Carbon::parse($trx->end_date)->isPast() ? 'text-red-500' : 'text-gray-700 dark:text-slate-300' }}">{{ $trx->end_date->format('d M Y') }}</span></p>
                            </div>
                            <a href="{{ route('transactions.show', $trx->id) }}" wire:navigate class="px-3 py-1.5 text-xs font-medium text-brand-700 transition-colors bg-brand-50 rounded-lg hover:bg-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20">
                                View
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
