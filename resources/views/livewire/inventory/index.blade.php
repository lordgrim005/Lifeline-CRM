<?php
 
use Livewire\Volt\Component;
use App\Models\Camera;
use App\Models\CameraModel;
use Livewire\Attributes\Layout;
 
new #[Layout('layouts.app')] class extends Component {
    public $cameras;
    public $cameraModels;
    public $statusFilter = '';
    
    public $isModalOpen = false;
    public $isEditMode = false;
    
    // Form fields
    public $camera_id;
    public $camera_model_id;
    public $serial_number;
    public $status = 'Available';
 
    public function mount()
    {
        $this->cameraModels = CameraModel::orderBy('brand')->orderBy('name')->get();
        $this->loadCameras();
    }
 
    public function loadCameras()
    {
        $query = Camera::with('cameraModel')->latest();
        
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        $this->cameras = $query->get();
    }
 
    public function updatedStatusFilter()
    {
        $this->loadCameras();
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
        $this->camera_id = null;
        $this->camera_model_id = '';
        $this->serial_number = '';
        $this->status = 'Available';
        $this->resetValidation();
    }
 
    public function edit($id)
    {
        $this->resetValidation();
        $camera = Camera::findOrFail($id);
        $this->camera_id = $camera->id;
        $this->camera_model_id = $camera->camera_model_id;
        $this->serial_number = $camera->serial_number;
        $this->status = $camera->status;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }
 
    public function save()
    {
        $this->validate([
            'camera_model_id' => 'required|exists:camera_models,id',
            'serial_number' => 'required|string|max:255|unique:cameras,serial_number,' . ($this->isEditMode ? $this->camera_id : 'NULL') . ',id,deleted_at,NULL',
            'status' => 'required|in:Available,Rented,Maintenance',
        ]);
 
        if ($this->isEditMode) {
            $camera = Camera::findOrFail($this->camera_id);
            $camera->update([
                'camera_model_id' => $this->camera_model_id,
                'serial_number' => $this->serial_number,
                'status' => $this->status,
            ]);
        } else {
            Camera::create([
                'camera_model_id' => $this->camera_model_id,
                'serial_number' => $this->serial_number,
                'status' => $this->status,
            ]);
        }
 
        $this->loadCameras();
        $this->closeModal();
    }
 
    public function delete($id)
    {
        Camera::findOrFail($id)->delete();
        $this->loadCameras();
    }
}; ?>
 
<div x-on:open-inventory-modal.window="$wire.openModal()">
    <x-slot name="header">
        <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Track physical camera units and their availability.</p>
        </div>
        <div>
            <button x-on:click="$dispatch('open-inventory-modal')" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all duration-300 rounded-xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-lg hover:shadow-brand-500/30">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add New Unit
            </button>
        </div>
        </div>
    </x-slot>
 
    {{-- Actions Bar --}}
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 p-1 bg-white border border-gray-200 rounded-xl dark:bg-slate-800 dark:border-slate-700/50 shadow-sm shadow-gray-200/30 dark:shadow-slate-900/40">
            <button wire:click="$set('statusFilter', '')" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $statusFilter === '' ? 'bg-gray-100 text-gray-900 dark:bg-slate-700 dark:text-white' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-slate-400 dark:hover:bg-slate-700/50 dark:hover:text-white' }}">
                All Units
            </button>
            <button wire:click="$set('statusFilter', 'Available')" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $statusFilter === 'Available' ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/20 dark:text-brand-400' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-slate-400 dark:hover:bg-slate-700/50 dark:hover:text-white' }}">
                Available
            </button>
            <button wire:click="$set('statusFilter', 'Rented')" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $statusFilter === 'Rented' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-slate-400 dark:hover:bg-slate-700/50 dark:hover:text-white' }}">
                Rented
            </button>
            <button wire:click="$set('statusFilter', 'Maintenance')" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $statusFilter === 'Maintenance' ? 'bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-slate-400 dark:hover:bg-slate-700/50 dark:hover:text-white' }}">
                Maintenance
            </button>
        </div>
    </div>
 
    {{-- Main Content: Inventory Table --}}
    <div class="bg-white border shadow-sm dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-slate-700/50 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Model</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Serial Number</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/70 dark:divide-slate-700/50">
                    @forelse($cameras as $camera)
                        <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-slate-700/30">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $camera->cameraModel->brand }} {{ $camera->cameraModel->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 font-mono text-xs dark:text-slate-300">
                                {{ $camera->serial_number }}
                            </td>
                            <td class="px-6 py-4">
                                @if($camera->status === 'Available')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-brand-700 bg-brand-50 rounded-full dark:bg-brand-500/10 dark:text-brand-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> Available
                                    </span>
                                @elseif($camera->status === 'Rented')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-amber-700 bg-amber-50 rounded-full dark:bg-amber-500/10 dark:text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Rented
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-full dark:bg-red-500/10 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Maintenance
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button wire:click="edit({{ $camera->id }})" class="p-2 text-gray-500 transition-colors rounded-lg hover:bg-gray-100 hover:text-gray-900 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                            <path d="m15 5 4 4"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $camera->id }})" wire:confirm="Are you sure you want to delete this camera unit?" class="p-2 text-red-500 transition-colors rounded-lg hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10">
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
                                    <p class="text-base font-medium">No inventory units found</p>
                                    <p class="mt-1 text-sm">Get started by adding a physical camera unit.</p>
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
                                {{ $isEditMode ? 'Edit Camera Unit' : 'Add New Camera Unit' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                {{ $isEditMode ? 'Update existing unit details' : 'Register a new physical unit to inventory' }}
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
                            {{-- Camera Model --}}
                            <div>
                                <label for="camera_model_id" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Camera Model</label>
                                <select id="camera_model_id" wire:model="camera_model_id" 
                                    class="w-full px-5 py-3.5 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-all duration-300" required>
                                    <option value="">Select a Model</option>
                                    @foreach($cameraModels as $m)
                                        <option value="{{ $m->id }}">{{ $m->brand }} {{ $m->name }}</option>
                                    @endforeach
                                </select>
                                @error('camera_model_id') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Serial Number --}}
                            <div>
                                <label for="serial_number" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Serial Number</label>
                                <input type="text" id="serial_number" wire:model="serial_number" 
                                    class="w-full px-5 py-3.5 text-sm font-mono bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-all duration-300" 
                                    placeholder="e.g. SN-12345678" required>
                                @error('serial_number') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Status</label>
                                <select id="status" wire:model="status" 
                                    class="w-full px-5 py-3.5 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 transition-all duration-300" required>
                                    <option value="Available">Available</option>
                                    <option value="Rented">Rented</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                                @error('status') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-4 mt-10">
                            <button type="button" wire:click="closeModal" 
                                class="px-6 py-3 text-sm font-semibold text-gray-600 transition-all bg-gray-100 rounded-2xl hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-8 py-3 text-sm font-bold text-white transition-all rounded-2xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-xl hover:shadow-brand-500/30 active:scale-95">
                                {{ $isEditMode ? 'Update Unit' : 'Save Unit' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
