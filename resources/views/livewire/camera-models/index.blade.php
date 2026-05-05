<?php

use Livewire\Volt\Component;
use App\Models\CameraModel;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $models;
    public $isModalOpen = false;
    public $isEditMode = false;
    
    // Form fields
    public $model_id;
    public $brand;
    public $name;
    public $description;

    public function mount()
    {
        $this->loadModels();
    }

    public function loadModels()
    {
        $this->models = CameraModel::withCount('packages')->latest()->get();
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
        $this->model_id = null;
        $this->brand = '';
        $this->name = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function edit($id)
    {
        $this->resetValidation();
        $model = CameraModel::findOrFail($id);
        $this->model_id = $model->id;
        $this->brand = $model->brand;
        $this->name = $model->name;
        $this->description = $model->description;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'brand' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($this->isEditMode) {
            $model = CameraModel::findOrFail($this->model_id);
            $model->update([
                'brand' => $this->brand,
                'name' => $this->name,
                'description' => $this->description,
            ]);
            // Flash message can be added here
        } else {
            CameraModel::create([
                'brand' => $this->brand,
                'name' => $this->name,
                'description' => $this->description,
            ]);
        }

        $this->loadModels();
        $this->closeModal();
    }

    public function delete($id)
    {
        CameraModel::findOrFail($id)->delete();
        $this->loadModels();
    }
}; ?>

<div x-on:open-model-modal.window="$wire.openModal()">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Camera Models</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Manage your camera inventory brands and models.</p>
            </div>
            <div>
                <button x-on:click="$dispatch('open-model-modal')" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all duration-300 rounded-xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-lg hover:shadow-brand-500/30">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add New Model
                </button>
            </div>
        </div>
    </x-slot>

    {{-- Main Content: Models Table --}}
    <div class="bg-white border shadow-sm dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-slate-700/50 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Brand</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Model Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Packages</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/70 dark:divide-slate-700/50">
                    @forelse($models as $model)
                        <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-slate-700/30">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $model->brand }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-300">
                                {{ $model->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-300">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                                    {{ $model->packages_count }} Packages
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('camera-models.show', $model->id) }}" class="p-2 text-brand-600 transition-colors rounded-lg hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-brand-500/10" title="Manage Packages">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path>
                                        </svg>
                                    </a>
                                    <button wire:click="edit({{ $model->id }})" class="p-2 text-gray-500 transition-colors rounded-lg hover:bg-gray-100 hover:text-gray-900 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                            <path d="m15 5 4 4"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $model->id }})" wire:confirm="Are you sure you want to delete this camera model?" class="p-2 text-red-500 transition-colors rounded-lg hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10">
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
                                        <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"></path>
                                        <circle cx="12" cy="13" r="3"></circle>
                                    </svg>
                                    <p class="text-base font-medium">No camera models found</p>
                                    <p class="mt-1 text-sm">Get started by adding a new camera model.</p>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/60 backdrop-blur-sm transition-all duration-300">
            <div class="relative w-full max-w-lg p-4 animate-in fade-in zoom-in duration-200">
                <div class="relative bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl shadow-brand-500/10 dark:shadow-slate-950/50 border border-white dark:border-slate-700/50 overflow-hidden">
                    {{-- Modal Header --}}
                    <div class="px-8 pt-8 pb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $isEditMode ? 'Edit Camera Model' : 'Add New Camera Model' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                {{ $isEditMode ? 'Update existing model details' : 'Register a new camera brand & model' }}
                            </p>
                        </div>
                        <button wire:click="closeModal" class="p-2 text-gray-400 transition-all rounded-full hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-slate-700 dark:hover:text-white">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit="save" class="px-8 pb-8 pt-4">
                        <div class="space-y-6">
                            {{-- Brand Name --}}
                            <div>
                                <label for="brand" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Brand Name</label>
                                <div class="relative">
                                    <input type="text" id="brand" wire:model="brand" 
                                        class="w-full px-5 py-3.5 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/10 dark:focus:border-brand-400 transition-all duration-300" 
                                        placeholder="e.g. Fujifilm" required>
                                </div>
                                @error('brand') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Model Name --}}
                            <div>
                                <label for="name" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Model Name</label>
                                <div class="relative">
                                    <input type="text" id="name" wire:model="name" 
                                        class="w-full px-5 py-3.5 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/10 dark:focus:border-brand-400 transition-all duration-300" 
                                        placeholder="e.g. Instax Mini 11" required>
                                </div>
                                @error('name') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Description (Optional)</label>
                                <textarea id="description" wire:model="description" rows="4" 
                                    class="w-full px-5 py-3.5 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/10 dark:focus:border-brand-400 transition-all duration-300 resize-none" 
                                    placeholder="Write a short description about this camera model..."></textarea>
                                @error('description') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-4 mt-10">
                            <button type="button" wire:click="closeModal" 
                                class="px-6 py-3 text-sm font-semibold text-gray-600 transition-all bg-gray-100 rounded-2xl hover:bg-gray-200 focus:ring-4 focus:ring-gray-200 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-8 py-3 text-sm font-bold text-white transition-all rounded-2xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-xl hover:shadow-brand-500/30 active:scale-95">
                                {{ $isEditMode ? 'Update Model' : 'Save Model' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
