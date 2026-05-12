<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $statusFilter = '';
    public $search = '';

    public function with()
    {
        $query = Transaction::with(['customer'])->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        return [
            'transactions' => $query->paginate(10)
        ];
    }

    public function delete($id)
    {
        Transaction::findOrFail($id)->delete();
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transactions</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Manage camera rentals and returns.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('transactions.create') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all duration-300 rounded-xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-lg hover:shadow-brand-500/30">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    New Rental
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Filter --}}
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 p-1 bg-white border border-gray-200 rounded-xl dark:bg-slate-800 dark:border-slate-700/50 shadow-sm shadow-gray-200/30 dark:shadow-slate-900/40">
            <button wire:click="$set('statusFilter', '')" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $statusFilter === '' ? 'bg-gray-100 text-gray-900 dark:bg-slate-700 dark:text-white' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-slate-400 dark:hover:bg-slate-700/50 dark:hover:text-white' }}">
                All
            </button>
            <button wire:click="$set('statusFilter', 'Active')" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $statusFilter === 'Active' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-slate-400 dark:hover:bg-slate-700/50 dark:hover:text-white' }}">
                Active Rentals
            </button>
            <button wire:click="$set('statusFilter', 'Completed')" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $statusFilter === 'Completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-slate-400 dark:hover:bg-slate-700/50 dark:hover:text-white' }}">
                Completed
            </button>
        </div>

        <div class="relative w-full sm:max-w-xs">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   class="w-full pl-11 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block transition-colors shadow-sm shadow-gray-200/20 dark:bg-slate-700/50 dark:border-slate-700/50 dark:placeholder-slate-400 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400" 
                   placeholder="Search customer name...">
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white border shadow-sm dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-slate-700/50 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Period</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/70 dark:divide-slate-700/50">
                    @forelse($transactions as $trx)
                        <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-slate-700/30">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                #TRX-{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-300">
                                {{ $trx->customer->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-300 text-xs">
                                {{ $trx->start_date->format('d M Y') }} - {{ $trx->end_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 font-semibold dark:text-slate-300">
                                Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($trx->status === 'Active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-amber-700 bg-amber-50 rounded-full dark:bg-amber-500/10 dark:text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-full dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Completed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('transactions.show', $trx->id) }}" wire:navigate class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-brand-700 transition-colors bg-brand-50 rounded-lg hover:bg-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20">
                                        View Details
                                    </a>
                                    @if($trx->status === 'Active')
                                        <a href="{{ route('transactions.show', $trx->id) }}?openModal=1" wire:navigate class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-emerald-700 transition-colors bg-emerald-50 rounded-lg hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20">
                                            Process Return
                                        </a>
                                    @endif
                                    <button wire:click="delete({{ $trx->id }})" wire:confirm="Are you sure you want to delete this transaction? This action cannot be undone." class="p-1.5 text-red-500 transition-colors rounded-lg hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10" title="Delete Transaction">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                            <line x1="10" x2="10" y1="11" y2="17"></line>
                                            <line x1="14" x2="14" y1="11" y2="17"></line>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-4 text-gray-300 dark:text-slate-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <p class="text-base font-medium">No transactions found</p>
                                    <p class="mt-1 text-sm">Get started by creating a new rental transaction.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/70 dark:border-slate-700/50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
