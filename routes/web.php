<?php

use Illuminate\Support\Facades\Route;

use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'pages.dashboard')->name('dashboard');
    
    // Master Data
    Volt::route('camera-models', 'camera-models.index')->name('camera-models.index');
    Volt::route('camera-models/{model}', 'camera-models.show')->name('camera-models.show');
    Volt::route('inventory', 'inventory.index')->name('inventory.index');
    Volt::route('customers', 'customers.index')->name('customers.index');
    Volt::route('transactions', 'transactions.index')->name('transactions.index');
    Volt::route('transactions/create', 'transactions.create')->name('transactions.create');
    Volt::route('transactions/{transaction}', 'transactions.show')->name('transactions.show');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
