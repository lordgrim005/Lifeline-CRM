<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Customer;
use App\Models\Camera;
use App\Models\CameraPackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $customers;
    public $availableCameras;
    
    // Form fields
    public $customer_id = '';
    public $start_date;
    public $end_date;
    
    // Cart items
    public $cart = []; // Array of ['camera_id', 'camera_package_id', 'price_per_day']
    
    // Selected options for adding to cart
    public $selected_camera_id = '';
    public $available_packages = [];
    public $selected_package_id = '';

    public function mount()
    {
        $this->customers = Customer::orderBy('name')->get();
        $this->start_date = date('Y-m-d');
        $this->end_date = date('Y-m-d', strtotime('+1 day'));
        $this->loadAvailableCameras();
    }

    public function loadAvailableCameras()
    {
        // Only load cameras that are Available and not already in the cart
        $cartCameraIds = array_column($this->cart, 'camera_id');
        
        $this->availableCameras = Camera::with('cameraModel')
            ->where('status', 'Available')
            ->whereNotIn('id', $cartCameraIds)
            ->get();
    }

    public function updatedSelectedCameraId($value)
    {
        if ($value) {
            $camera = Camera::with('cameraModel.packages')->find($value);
            if ($camera && $camera->cameraModel) {
                $this->available_packages = $camera->cameraModel->packages;
            } else {
                $this->available_packages = [];
            }
        } else {
            $this->available_packages = [];
        }
        $this->selected_package_id = '';
    }

    public function addToCart()
    {
        $this->validate([
            'selected_camera_id' => 'required',
            'selected_package_id' => 'required',
        ]);

        $camera = Camera::with('cameraModel')->find($this->selected_camera_id);
        $package = CameraPackage::find($this->selected_package_id);

        if ($camera && $package) {
            $this->cart[] = [
                'camera_id' => $camera->id,
                'camera_model_name' => $camera->cameraModel->brand . ' ' . $camera->cameraModel->name . ' (' . $camera->serial_number . ')',
                'camera_package_id' => $package->id,
                'package_name' => $package->package_name,
                'price_per_day' => $package->daily_price,
            ];

            // Reset selection and reload available cameras
            $this->selected_camera_id = '';
            $this->selected_package_id = '';
            $this->available_packages = [];
            $this->loadAvailableCameras();
        }
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart); // Re-index array
        $this->loadAvailableCameras();
    }

    public function getDaysProperty()
    {
        if (!$this->start_date || !$this->end_date) return 0;
        
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        if ($end->lessThan($start)) return 0;
        
        return $start->diffInDays($end) ?: 1; // Minimum 1 day
    }

    public function getGrandTotalProperty()
    {
        $days = $this->days;
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price_per_day'] * $days;
        }
        return $total;
    }

    public function save()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if (empty($this->cart)) {
            $this->addError('cart', 'Cart is empty. Please add at least one camera.');
            return;
        }

        DB::transaction(function () {
            // 1. Create Transaction
            $transaction = Transaction::create([
                'customer_id' => $this->customer_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'grand_total' => $this->grand_total,
                'status' => 'Active',
                'payment_status' => 'Unpaid',
            ]);

            $days = $this->days;

            // 2. Add Items & Update Camera Status
            foreach ($this->cart as $item) {
                $subtotal = $item['price_per_day'] * $days;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'camera_id' => $item['camera_id'],
                    'camera_package_id' => $item['camera_package_id'],
                    'price_per_day' => $item['price_per_day'],
                    'subtotal' => $subtotal,
                ]);

                // Update Camera Status to Rented
                Camera::where('id', $item['camera_id'])->update(['status' => 'Rented']);
            }
        });

        return $this->redirectRoute('transactions.index', navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.index') }}" wire:navigate class="p-2 text-gray-400 transition-colors bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 dark:bg-slate-800 dark:border-slate-700/50 dark:hover:bg-slate-700 dark:hover:text-white">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Rental</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Create a new camera rental transaction.</p>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left Column: Form & Add to Cart --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Customer & Dates Form --}}
            <div class="p-6 bg-white border shadow-sm dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Rental Details</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="customer_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Customer *</label>
                        <select id="customer_id" wire:model="customer_id" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block dark:bg-slate-700/50 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-colors">
                            <option value="">Select Customer...</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                            @endforeach
                        </select>
                        @error('customer_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Date *</label>
                        <input type="date" id="start_date" wire:model.live="start_date" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block dark:bg-slate-700/50 dark:border-slate-600 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-colors">
                        @error('start_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">End Date *</label>
                        <input type="date" id="end_date" wire:model.live="end_date" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block dark:bg-slate-700/50 dark:border-slate-600 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-colors">
                        @error('end_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Add Item Box --}}
            <div class="p-6 bg-white border shadow-sm dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Add Items to Cart</h2>
                <div class="flex flex-col items-end gap-4 sm:flex-row">
                    <div class="w-full">
                        <label for="selected_camera_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Camera Unit</label>
                        <select id="selected_camera_id" wire:model.live="selected_camera_id" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block dark:bg-slate-700/50 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-colors">
                            <option value="">Select an available unit...</option>
                            @foreach($availableCameras as $cam)
                                <option value="{{ $cam->id }}">{{ $cam->cameraModel->brand }} {{ $cam->cameraModel->name }} - SN: {{ $cam->serial_number }}</option>
                            @endforeach
                        </select>
                        @error('selected_camera_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="w-full">
                        <label for="selected_package_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Package</label>
                        <select id="selected_package_id" wire:model="selected_package_id" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block dark:bg-slate-700/50 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-colors" {{ empty($available_packages) ? 'disabled' : '' }}>
                            <option value="">Select package...</option>
                            @foreach($available_packages as $pkg)
                                <option value="{{ $pkg->id }}">{{ $pkg->package_name }} (Rp {{ number_format($pkg->daily_price, 0, ',', '.') }}/day)</option>
                            @endforeach
                        </select>
                        @error('selected_package_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <button type="button" wire:click="addToCart" class="w-full px-4 py-2.5 text-sm font-semibold text-gray-900 transition-all bg-white border border-gray-200 sm:w-auto rounded-xl hover:bg-gray-50 hover:text-brand-600 focus:ring-4 focus:ring-gray-100 dark:bg-slate-800 dark:text-white dark:border-slate-600 dark:hover:bg-slate-700 dark:focus:ring-slate-700 whitespace-nowrap">
                        Add to Cart
                    </button>
                </div>
                @error('cart') <span class="block mt-4 text-sm font-medium text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Right Column: Cart Summary --}}
        <div class="lg:col-span-1">
            <div class="sticky p-6 bg-white border shadow-sm top-24 dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Rental Cart</h2>
                
                {{-- Cart Items --}}
                <div class="mb-6 space-y-4">
                    @forelse($cart as $index => $item)
                        <div class="p-4 border border-gray-100 bg-gray-50 rounded-xl dark:bg-slate-700/30 dark:border-slate-700/50">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['camera_model_name'] }}</h4>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Package: {{ $item['package_name'] }}</p>
                                    <p class="mt-2 text-sm font-medium text-brand-600 dark:text-brand-400">Rp {{ number_format($item['price_per_day'], 0, ',', '.') }} <span class="text-xs text-gray-400">/day</span></p>
                                </div>
                                <button type="button" wire:click="removeFromCart({{ $index }})" class="p-1.5 text-red-400 transition-colors rounded-lg hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center border-2 border-dashed border-gray-200/70 rounded-xl dark:border-slate-700/50">
                            <p class="text-sm text-gray-500 dark:text-slate-400">Your cart is empty.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Summary --}}
                <div class="pt-4 mt-6 border-t border-gray-100 dark:border-slate-700/50">
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-gray-500 dark:text-slate-400">Duration</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $this->days }} Day(s)</span>
                    </div>
                    <div class="flex justify-between pt-2 mt-4 text-base font-bold border-t border-gray-100 dark:border-slate-700/50">
                        <span class="text-gray-900 dark:text-white">Grand Total</span>
                        <span class="text-brand-600 dark:text-brand-400">Rp {{ number_format($this->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button type="button" wire:click="save" class="w-full py-3 mt-6 text-sm font-semibold text-white transition-all duration-300 rounded-xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-lg hover:shadow-brand-500/30 focus:ring-4 focus:ring-brand-500/20" {{ empty($cart) ? 'disabled' : '' }}>
                    Confirm Rental
                </button>
            </div>
        </div>
    </div>
</div>
