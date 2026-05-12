<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

new #[Layout('layouts.app')] class extends Component {
    public Transaction $transaction;
    
    public $return_date;
    public $calculated_late_fee = 0;
    public $days_late = 0;
    
    #[Url]
    public $openModal = false;

    public function mount(Transaction $transaction)
    {
        $this->transaction = $transaction->load(['customer', 'items.camera.cameraModel', 'items.cameraPackage']);
        $this->return_date = date('Y-m-d');
        $this->calculateLateFee();
    }

    public function updatedReturnDate()
    {
        $this->calculateLateFee();
    }

    public function calculateLateFee()
    {
        if (!$this->transaction || $this->transaction->status === 'Completed') {
            $this->calculated_late_fee = $this->transaction->late_fee ?? 0;
            return;
        }

        if (!$this->return_date) {
            $this->calculated_late_fee = 0;
            $this->days_late = 0;
            return;
        }

        try {
            // Ensure we are only comparing dates by formatting them first
            $endDateStr = $this->transaction->end_date instanceof Carbon 
                ? $this->transaction->end_date->format('Y-m-d') 
                : Carbon::parse($this->transaction->end_date)->format('Y-m-d');
            
            $end = Carbon::parse($endDateStr)->startOfDay();
            $return = Carbon::parse($this->return_date)->startOfDay();

            if ($return->greaterThan($end)) {
                $this->days_late = (int) abs($end->diffInDays($return));
                
                // Late fee: flat Rp 50.000 per day of delay
                $this->calculated_late_fee = 50000 * $this->days_late;
            } else {
                $this->days_late = 0;
                $this->calculated_late_fee = 0;
            }
        } catch (\Exception $e) {
            $this->days_late = 0;
            $this->calculated_late_fee = 0;
        }
    }

    public function toggleModal($state)
    {
        $this->openModal = $state;
    }

    public function processReturn()
    {
        if ($this->transaction->status === 'Completed') return;

        DB::transaction(function () {
            // Calculate final grand total = sum of item subtotals + late fee
            $subtotalItems = $this->transaction->items->sum('subtotal');
            $finalGrandTotal = $subtotalItems + $this->calculated_late_fee;

            // Update Transaction
            $this->transaction->update([
                'status' => 'Completed',
                'payment_status' => 'Paid',
                'late_fee' => $this->calculated_late_fee,
                'grand_total' => $finalGrandTotal,
            ]);

            // Release Cameras
            foreach ($this->transaction->items as $item) {
                if ($item->camera) {
                    $item->camera->update(['status' => 'Available']);
                }
            }
        });

        $this->openModal = false;
        session()->flash('success', 'Return processed successfully! Transaction #TRX-' . str_pad($this->transaction->id, 5, '0', STR_PAD_LEFT) . ' is now completed.');
        return $this->redirectRoute('transactions.index', navigate: true);
    }
}; ?>

<div x-data="{ modalOpen: @entangle('openModal') }" 
     @open-return-modal.window="modalOpen = true">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.index') }}" wire:navigate class="p-2 text-gray-400 transition-colors bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 dark:bg-slate-800 dark:border-slate-700/50 dark:hover:bg-slate-700 dark:hover:text-white">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transaction #TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">View details and process returns.</p>
            </div>
            @if($transaction->status === 'Active')
                <button @click="$dispatch('open-return-modal')" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all duration-300 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 hover:shadow-lg hover:shadow-emerald-500/30">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Process Return
                </button>
            @endif
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left: Details & Items --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Info Card --}}
            <div class="p-6 bg-white border shadow-sm dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Information</h2>
                    @if($transaction->status === 'Active')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-amber-700 bg-amber-50 rounded-full dark:bg-amber-500/10 dark:text-amber-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-full dark:bg-emerald-500/10 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Completed
                        </span>
                    @endif
                </div>
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase dark:text-slate-500">Name</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $transaction->customer->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase dark:text-slate-500">Contact</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $transaction->customer->phone ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase dark:text-slate-500">Rental Period</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ $transaction->start_date->format('d M Y') }} &rarr; {{ $transaction->end_date->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase dark:text-slate-500">Payment Status</p>
                        <p class="mt-1 font-medium {{ $transaction->payment_status === 'Paid' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                            {{ $transaction->payment_status }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="bg-white border shadow-sm dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-slate-700/50">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Rented Items</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-slate-700/50 dark:text-slate-400">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Item</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Package</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Price/Day</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/70 dark:divide-slate-700/50">
                            @foreach($transaction->items as $item)
                                <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-slate-700/30">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $item->camera->cameraModel->brand ?? 'Unknown' }} {{ $item->camera->cameraModel->name ?? 'Model' }}
                                        </div>
                                        <div class="text-xs font-mono text-gray-500 dark:text-slate-400">
                                            SN: {{ $item->camera->serial_number ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700 dark:text-slate-300">
                                        {{ $item->cameraPackage->package_name ?? 'Custom' }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-right text-gray-700 dark:text-slate-300">
                                        Rp {{ number_format($item->price_per_day, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-right text-gray-900 dark:text-white">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Payment Summary --}}
        <div class="lg:col-span-1">
            <div class="sticky p-6 bg-white border shadow-sm top-24 dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Payment Summary</h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-slate-400">Subtotal Items</span>
                        <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($transaction->items->sum('subtotal'), 0, ',', '.') }}</span>
                    </div>
                    
                    @if($transaction->late_fee > 0 || ($transaction->status === 'Active' && $calculated_late_fee > 0))
                        <div class="flex justify-between">
                            <span class="text-red-500 dark:text-red-400">Late Fee</span>
                            <span class="font-medium text-red-600 dark:text-red-400">Rp {{ number_format($transaction->status === 'Completed' ? $transaction->late_fee : $calculated_late_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="pt-3 mt-3 border-t border-gray-100 dark:border-slate-700/50">
                        <div class="flex justify-between text-base font-bold">
                            <span class="text-gray-900 dark:text-white">Grand Total</span>
                            <span class="text-brand-600 dark:text-brand-400">
                                @php
                                    $subtotalItems = $transaction->items->sum('subtotal');
                                    $activeFee = $transaction->status === 'Completed' ? $transaction->late_fee : $calculated_late_fee;
                                    $displayGrandTotal = $subtotalItems + $activeFee;
                                @endphp
                                Rp {{ number_format($displayGrandTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Return Modal --}}
    <div x-show="modalOpen && '{{ $transaction->status }}' === 'Active'" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 backdrop-blur-sm">
            <div class="relative w-full max-w-md p-4 max-h-full" @click.away="modalOpen = false">
                <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-gray-200/20 dark:shadow-slate-900/50 border border-gray-100 dark:border-slate-700/50">
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-slate-700/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Process Return</h3>
                        <button @click="modalOpen = false" class="p-1.5 text-gray-400 transition-colors rounded-xl hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-slate-700 dark:hover:text-white">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-5">
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700/50">
                            <p class="text-sm font-medium text-gray-700 dark:text-slate-300">Expected Return: <span class="font-bold">{{ $transaction->end_date->format('d M Y') }}</span></p>
                        </div>

                        <div>
                            <label for="return_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Actual Return Date</label>
                            <input type="date" id="return_date" wire:model.live="return_date" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block dark:bg-slate-700/50 dark:border-slate-600 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-colors" required>
                        </div>

                        @if($calculated_late_fee > 0)
                            <div class="p-4 bg-red-50 rounded-xl dark:bg-red-500/10 border border-red-100 dark:border-red-500/20">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-red-700 dark:text-red-400">Late Duration:</span>
                                    <span class="text-sm font-bold text-red-700 dark:text-red-400">{{ $days_late }} Day(s)</span>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-red-200/50 dark:border-red-500/20">
                                    <span class="text-sm font-medium text-red-700 dark:text-red-400">Late Fee (100%/day):</span>
                                    <span class="text-base font-bold text-red-700 dark:text-red-400">Rp {{ number_format($calculated_late_fee, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20">
                                <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Status:</span>
                                <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400">On Time (No Fee)</span>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-gray-100 dark:border-slate-700/50">
                            <p class="mb-4 text-sm text-gray-500 dark:text-slate-400">Processing this return will mark the transaction as completed and release the cameras back to Available inventory.</p>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-200 rounded-xl hover:bg-gray-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                                    Cancel
                                </button>
                                <button type="button" wire:click="processReturn" class="px-4 py-2 text-sm font-semibold text-white transition-all rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 hover:shadow-lg hover:shadow-emerald-500/30">
                                    Confirm Return
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
