@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Dashboard Overview</h2>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1 font-medium">Real-time inventory status summary.</p>
        </div>
        <div class="text-sm font-bold px-4 py-2 bg-brand-primary/10 text-brand-primary rounded-lg border border-brand-primary/20">
            {{ now()->format('l, d M Y') }}
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Cameras -->
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-2xl text-zinc-600 dark:text-zinc-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ $totalCameras }}</div>
            <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mt-1">Total Cameras</div>
        </div>

        <!-- Available -->
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-500/10 rounded-2xl text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ $availableCameras }}</div>
            <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mt-1 text-green-600">Available</div>
        </div>

        <!-- Booked -->
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-500/10 rounded-2xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ $bookedCameras }}</div>
            <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mt-1 text-blue-600">Booked</div>
        </div>

        <!-- Rented -->
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-brand-accent/10 rounded-2xl text-brand-accent">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ $rentedCameras }}</div>
            <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mt-1 text-brand-accent">Currently Rented</div>
        </div>
    </div>

    <!-- Quick Actions or Placeholder -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 p-8">
            <h3 class="text-xl font-bold mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('transactions.index', ['new' => 1]) }}" class="p-4 bg-brand-primary text-white rounded-2xl flex flex-col items-center justify-center hover:bg-brand-primary/90 transition-all">
                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span class="text-sm font-bold uppercase tracking-tight">New Rental</span>
                </a>
                <a href="{{ route('customers.index', ['new' => 1]) }}" class="p-4 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white rounded-2xl flex flex-col items-center justify-center hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all">
                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    <span class="text-sm font-bold uppercase tracking-tight text-center">New Customer</span>
                </a>
            </div>
        </div>
        
        <div class="bg-zinc-900 dark:bg-brand-primary text-white rounded-3xl p-8 flex flex-col justify-center">
            <h3 class="text-2xl font-black leading-tight italic tracking-tighter">"Efficiency in every capture, <br>Ease in every rental."</h3>
            <p class="mt-4 text-white/70 font-medium">— LIFELINE MLG Team</p>
        </div>
    </div>
</div>
@endsection
