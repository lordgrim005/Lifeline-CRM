@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Customer Directory</h2>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1 font-medium">Manage your client base and contact details.</p>
        </div>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')" class="px-6 py-3 bg-brand-primary text-white rounded-xl font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-primary/90 transition-all flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Register Customer
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-800/50">
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Name</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">WhatsApp</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Instagram</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($customers as $customer)
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/20 transition-colors">
                    <td class="px-6 py-4 text-sm font-bold text-zinc-900 dark:text-white">{{ $customer->full_name }}</td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300 font-medium">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->whatsapp) }}" target="_blank" class="flex items-center text-green-600 hover:underline">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.912-.001-3.793-.457-5.47-1.32l-6.537 1.528zm6.537-4.22c1.474.873 3.193 1.334 4.954 1.335 5.4 0 9.793-4.393 9.795-9.793 0-2.617-1.02-5.074-2.871-6.928-1.851-1.854-4.307-2.873-6.924-2.873-5.4 0-9.794 4.393-9.796 9.795-.001 1.73.454 3.42 1.316 4.908l-.936 3.419 3.5 3.5-3.5 3.4z"></path></svg>
                            {{ $customer->whatsapp }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300 font-medium italic">
                        @if($customer->instagram)
                            @ {{ $customer->instagram }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button onclick="openEditModal({{ $customer->id }}, '{{ $customer->full_name }}', '{{ $customer->whatsapp }}', '{{ $customer->instagram }}', '{{ addslashes($customer->address) }}')" 
                                class="text-zinc-400 hover:text-brand-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this customer?')" class="text-zinc-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400 font-medium italic">No customers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($customers->hasPages())
        <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/30">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div id="modal-create" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-3xl shadow-2xl p-8 border border-zinc-100 dark:border-zinc-800">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold">Register Customer</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Full Name</label>
                <input type="text" name="full_name" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white" placeholder="John Doe">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">WhatsApp</label>
                    <input type="text" name="whatsapp" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white" placeholder="08123456789">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Instagram</label>
                    <input type="text" name="instagram" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white" placeholder="@username">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Address</label>
                <textarea name="address" rows="3" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white" placeholder="Client address details..."></textarea>
            </div>
            <button type="submit" class="w-full py-4 bg-brand-primary text-white rounded-xl font-bold hover:bg-brand-primary/90 transition-all">Save Customer</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="modal-edit" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-3xl shadow-2xl p-8 border border-zinc-100 dark:border-zinc-800">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold">Edit Customer</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Full Name</label>
                <input type="text" name="full_name" id="edit_full_name" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">WhatsApp</label>
                    <input type="text" name="whatsapp" id="edit_whatsapp" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Instagram</label>
                    <input type="text" name="instagram" id="edit_instagram" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Address</label>
                <textarea name="address" id="edit_address" rows="3" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white"></textarea>
            </div>
            <button type="submit" class="w-full py-4 bg-brand-primary text-white rounded-xl font-bold hover:bg-brand-primary/90 transition-all">Update Customer</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, wa, ig, addr) {
        const form = document.getElementById('edit-form');
        form.action = `/customers/${id}`;
        document.getElementById('edit_full_name').value = name;
        document.getElementById('edit_whatsapp').value = wa;
        document.getElementById('edit_instagram').value = ig;
        document.getElementById('edit_address').value = addr;
        document.getElementById('modal-edit').classList.remove('hidden');
    }

    // Auto-open modal if 'new' parameter is present
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('new')) {
            document.getElementById('modal-create').classList.remove('hidden');
        }
    });
</script>
@endsection
