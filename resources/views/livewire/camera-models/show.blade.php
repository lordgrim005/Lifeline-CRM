<?php

use Livewire\Volt\Component;
use App\Models\CameraModel;
use App\Models\CameraPackage;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public CameraModel $model;
    
    public $isModalOpen = false;
    public $isEditMode = false;
    
    // Form fields
    public $package_id;
    public $package_name;
    public $includes;
    public $daily_price;

    public function mount(CameraModel $model)
    {
        $this->model = $model;
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->package_id = null;
        $this->package_name = '';
        $this->includes = '';
        $this->daily_price = '';
        $this->resetValidation();
    }

    public function edit($id)
    {
        $this->resetValidation();
        $package = CameraPackage::findOrFail($id);
        $this->package_id = $package->id;
        $this->package_name = $package->package_name;
        $this->includes = $package->includes;
        $this->daily_price = $package->daily_price;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'package_name' => 'required|string|max:255',
            'includes' => 'nullable|string',
            'daily_price' => 'required|numeric|min:0',
        ]);

        if ($this->isEditMode) {
            $package = CameraPackage::findOrFail($this->package_id);
            $package->update([
                'package_name' => $this->package_name,
                'includes' => $this->includes,
                'daily_price' => $this->daily_price,
            ]);
        } else {
            $this->model->packages()->create([
                'package_name' => $this->package_name,
                'includes' => $this->includes,
                'daily_price' => $this->daily_price,
            ]);
        }

        $this->model->load('packages');
        $this->closeModal();
    }

    public function delete($id)
    {
        CameraPackage::findOrFail($id)->delete();
        $this->model->load('packages');
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('camera-models.index') }}" wire:navigate class="p-2 text-gray-400 transition-colors bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 dark:bg-slate-800 dark:border-slate-700/50 dark:hover:bg-slate-700 dark:hover:text-white">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $model->brand }} {{ $model->name }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Manage rental packages for this camera model.</p>
            </div>
        </div>
    </x-slot>

    {{-- Actions Bar --}}
    <div class="flex justify-end mb-6">
        <button wire:click="openModal" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all duration-300 rounded-xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-lg hover:shadow-brand-500/30">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Package
        </button>
    </div>

    {{-- Packages Table --}}
    <div class="bg-white border shadow-sm dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-slate-700/50 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Package Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Includes</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Daily Price</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/70 dark:divide-slate-700/50">
                    @forelse($model->packages as $package)
                        <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-slate-700/30">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $package->package_name }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 whitespace-normal max-w-xs dark:text-slate-300">
                                {{ $package->includes ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-300">
                                <span class="font-semibold text-brand-600 dark:text-brand-400">Rp {{ number_format($package->daily_price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button wire:click="edit({{ $package->id }})" class="p-2 text-gray-500 transition-colors rounded-lg hover:bg-gray-100 hover:text-gray-900 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                            <path d="m15 5 4 4"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $package->id }})" wire:confirm="Are you sure you want to delete this package?" class="p-2 text-red-500 transition-colors rounded-lg hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10">
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
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-4 text-gray-300 dark:text-slate-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path>
                                    </svg>
                                    <p class="text-base font-medium">No packages yet</p>
                                    <p class="mt-1 text-sm">Add a rental package (e.g. Bronze, Gold) for this camera.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 backdrop-blur-sm">
            <div class="relative w-full max-w-md p-4 max-h-full">
                <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-gray-200/20 dark:shadow-slate-900/50 border border-gray-100 dark:border-slate-700/50">
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-slate-700/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $isEditMode ? 'Edit Package' : 'Add New Package' }}
                        </h3>
                        <button wire:click="closeModal" class="p-1.5 text-gray-400 transition-colors rounded-xl hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-slate-700 dark:hover:text-white">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit="save" class="p-6">
                        <div class="space-y-5">
                            <div>
                                <label for="package_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Package Name</label>
                                <input type="text" id="package_name" wire:model="package_name" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block dark:bg-slate-700/50 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-colors" placeholder="e.g. Bronze, Gold" required>
                                @error('package_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="includes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Includes</label>
                                <textarea id="includes" wire:model="includes" rows="3" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block dark:bg-slate-700/50 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-colors" placeholder="e.g. Camera Only, 10 Papers..."></textarea>
                                @error('includes') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="daily_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Daily Price (Rp)</label>
                                <input type="number" id="daily_price" wire:model="daily_price" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 block dark:bg-slate-700/50 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-colors" placeholder="e.g. 50000" required>
                                @error('daily_price') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-100 dark:border-slate-700/50">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-200 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:focus:ring-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white transition-all rounded-xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-lg hover:shadow-brand-500/30 focus:ring-4 focus:ring-brand-500/20">
                                {{ $isEditMode ? 'Update Package' : 'Save Package' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
