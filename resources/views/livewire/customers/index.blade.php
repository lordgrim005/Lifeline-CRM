<?php
 
use Livewire\Volt\Component;
use App\Models\Customer;
use Livewire\Attributes\Layout;
 
new #[Layout('layouts.app')] class extends Component {
    public $search = '';
    
    public $isModalOpen = false;
    public $isEditMode = false;
    
    // Form fields
    public $customer_id;
    public $name;
    public $phone;
    public $email;
    public $instagram;
    public $address;
 
    public function with()
    {
        return [
            'customers' => Customer::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('phone', 'like', '%' . $this->search . '%')
                ->orWhere('instagram', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10)
        ];
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
        $this->customer_id = null;
        $this->name = '';
        $this->phone = '';
        $this->email = '';
        $this->instagram = '';
        $this->address = '';
        $this->resetValidation();
    }
 
    public function edit($id)
    {
        $this->resetValidation();
        $customer = Customer::findOrFail($id);
        $this->customer_id = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->email = $customer->email;
        $this->instagram = $customer->instagram;
        $this->address = $customer->address;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }
 
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);
 
        if ($this->isEditMode) {
            $customer = Customer::findOrFail($this->customer_id);
            $customer->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'instagram' => $this->instagram,
                'address' => $this->address,
            ]);
        } else {
            Customer::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'instagram' => $this->instagram,
                'address' => $this->address,
            ]);
        }
 
        $this->closeModal();
    }
 
    public function delete($id)
    {
        Customer::findOrFail($id)->delete();
    }
}; ?>
 
<div x-on:open-customer-modal.window="$wire.openModal()">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Customers</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Manage your rental customer database.</p>
            </div>
            <div>
                <button x-on:click="$dispatch('open-customer-modal')" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all duration-300 rounded-xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-lg hover:shadow-brand-500/30">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Customer
                </button>
            </div>
        </div>
    </x-slot>
 
    {{-- Actions Bar (Search) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 mt-6">
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400 dark:text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full py-2.5 pl-10 pr-4 text-sm text-gray-900 transition-colors border border-gray-200 bg-white rounded-xl focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 dark:bg-slate-800 dark:border-slate-700/50 dark:placeholder-slate-400 dark:text-white dark:focus:ring-brand-400/40 dark:focus:border-brand-400 shadow-sm" placeholder="Search customers by name, phone, or IG...">
        </div>
    </div>    <div class="bg-white border shadow-sm dark:bg-slate-800 rounded-2xl border-gray-200/70 dark:border-slate-700/50 shadow-gray-200/40 dark:shadow-slate-900/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-slate-700/50 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Contact Details</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Social Media</th>
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/70 dark:divide-slate-700/50">
                    @forelse($customers as $customer)
                        <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-slate-700/30">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $customer->name }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-tighter">Customer ID #{{ $customer->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    @if($customer->phone)
                                        <div class="flex items-center gap-2 text-gray-700 dark:text-slate-300">
                                            <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                            </div>
                                            <span class="text-xs font-medium">{{ $customer->phone }}</span>
                                        </div>
                                    @endif
                                    @if($customer->email)
                                        <div class="flex items-center gap-2 text-gray-700 dark:text-slate-300">
                                            <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                                            </div>
                                            <span class="text-xs font-medium">{{ $customer->email }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-300">
                                @if($customer->instagram)
                                    <div class="flex items-center gap-2">
                                        <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-pink-50 dark:bg-pink-500/10 text-pink-600 dark:text-pink-400">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
                                        </div>
                                        <span class="text-xs font-medium">{{ '@' . ltrim($customer->instagram, '@') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $customer->id }})" class="p-2 text-gray-500 transition-colors rounded-xl hover:bg-gray-100 hover:text-gray-900 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white" title="Edit Customer">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                            <path d="m15 5 4 4"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $customer->id }})" wire:confirm="Are you sure you want to delete this customer?" class="p-2 text-red-500 transition-colors rounded-xl hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10" title="Delete Customer">
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
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <p class="text-base font-medium">No customers found</p>
                                    <p class="mt-1 text-sm">Get started by adding a new customer.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/70 dark:border-slate-700/50">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
 
    {{-- Create/Edit Modal --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/60 backdrop-blur-sm transition-all duration-300">
            <div class="relative w-full max-w-2xl p-4 animate-in fade-in zoom-in duration-200">
                <div class="relative bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl shadow-brand-500/10 dark:shadow-slate-950/50 border border-white dark:border-slate-700/50 overflow-hidden">
                    {{-- Modal Header --}}
                    <div class="px-10 pt-10 pb-6 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/20 border-b border-gray-100 dark:border-slate-700/50">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $isEditMode ? 'Edit Customer Profile' : 'Register New Customer' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                {{ $isEditMode ? 'Update existing customer details' : 'Fill in the information to add a new member' }}
                            </p>
                        </div>
                        <button wire:click="closeModal" class="p-2.5 text-gray-400 transition-all rounded-full hover:bg-white hover:text-gray-900 dark:hover:bg-slate-700 dark:hover:text-white shadow-sm border border-transparent hover:border-gray-100 dark:hover:border-slate-600">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit="save" class="px-10 pb-10 pt-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Left Column: Identity --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Full Name</label>
                                    <input type="text" id="name" wire:model="name" 
                                        class="w-full px-5 py-3.5 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/10 dark:focus:border-brand-400 transition-all duration-300" 
                                        placeholder="Enter customer name" required>
                                    @error('name') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="phone" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Phone Number</label>
                                    <input type="text" id="phone" wire:model="phone" 
                                        class="w-full px-5 py-3.5 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/10 dark:focus:border-brand-400 transition-all duration-300" 
                                        placeholder="e.g. 08123456789">
                                    @error('phone') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="instagram" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Instagram Handle</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">@</div>
                                        <input type="text" id="instagram" wire:model="instagram" 
                                            class="w-full px-5 py-3.5 pl-9 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/10 dark:focus:border-brand-400 transition-all duration-300" 
                                            placeholder="username">
                                    </div>
                                    @error('instagram') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Right Column: Contact & Location --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="email" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Email Address</label>
                                    <input type="email" id="email" wire:model="email" 
                                        class="w-full px-5 py-3.5 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/10 dark:focus:border-brand-400 transition-all duration-300" 
                                        placeholder="customer@example.com">
                                    @error('email') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="address" class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Full Address</label>
                                    <textarea id="address" wire:model="address" rows="5" 
                                        class="w-full px-5 py-3.5 text-sm bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 block dark:bg-slate-900/50 dark:border-slate-700 dark:placeholder-slate-500 dark:text-white dark:focus:ring-brand-400/10 dark:focus:border-brand-400 transition-all duration-300 resize-none" 
                                        placeholder="Enter customer's complete address..."></textarea>
                                    @error('address') <span class="text-xs text-red-500 mt-2 block ml-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-5 mt-12 pt-8 border-t border-gray-100 dark:border-slate-700/50">
                            <button type="button" wire:click="closeModal" 
                                class="px-8 py-3.5 text-sm font-semibold text-gray-600 transition-all bg-gray-50 rounded-2xl hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-slate-700/50 dark:text-slate-300 dark:hover:bg-slate-700 border border-gray-100 dark:border-slate-600">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-10 py-3.5 text-sm font-bold text-white transition-all rounded-2xl bg-gradient-to-br from-brand-600 to-purple-600 hover:shadow-xl hover:shadow-brand-500/30 active:scale-95">
                                {{ $isEditMode ? 'Update Profile' : 'Register Customer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
