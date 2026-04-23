<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('camera-models', \App\Http\Controllers\CameraModelController::class);
    Route::resource('cameras', \App\Http\Controllers\CameraController::class);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('transactions', \App\Http\Controllers\TransactionController::class);
    Route::post('transactions/{transaction}/return', [\App\Http\Controllers\TransactionController::class, 'return'])->name('transactions.return');
});
