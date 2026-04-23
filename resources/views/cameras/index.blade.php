@extends('layouts.app')

@section('title', 'Cameras')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Camera Inventory</h2>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1 font-medium">Tracking individual units and their conditions.</p>
        </div>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')" class="px-6 py-3 bg-brand-primary text-white rounded-xl font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-primary/90 transition-all flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Camera Unit
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-800/50">
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Serial Number</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Model</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Status</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($cameras as $camera)
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/20 transition-colors">
                    <td class="px-6 py-4 text-sm font-bold text-zinc-900 dark:text-white">{{ $camera->serial_number }}</td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300 font-medium">{{ $camera->model->model_name }}</td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'Available' => 'bg-green-500/10 text-green-600 border-green-500/20',
                                'Booked' => 'bg-blue-500/10 text-blue-600 border-blue-500/20',
                                'Rented' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
                                'Maintenance' => 'bg-red-500/10 text-red-600 border-red-500/20',
                            ];
                            $colorClass = $statusColors[$camera->status] ?? 'bg-zinc-500/10 text-zinc-600 border-zinc-500/20';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $colorClass }}">
                            {{ $camera->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button onclick="openEditModal({{ $camera->id }}, '{{ $camera->serial_number }}', {{ $camera->model_id }}, '{{ $camera->status }}', '{{ addslashes($camera->condition_notes) }}')" 
                                class="text-zinc-400 hover:text-brand-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <form action="{{ route('cameras.destroy', $camera) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this unit?')" class="text-zinc-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400 font-medium italic">No cameras found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($cameras->hasPages())
        <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/30">
            {{ $cameras->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div id="modal-create" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-3xl shadow-2xl p-8 border border-zinc-100 dark:border-zinc-800">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold">Add Camera Unit</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('cameras.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Model</label>
                    <select name="model_id" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                        @foreach($models as $model)
                            <option value="{{ $model->id }}">{{ $model->model_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Serial Number</label>
                    <input type="text" name="serial_number" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white" placeholder="S/N 123456">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Status</label>
                <select name="status" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                    <option value="Available">Available</option>
                    <option value="Booked">Booked</option>
                    <option value="Rented">Rented</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Condition Notes</label>
                <textarea name="condition_notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white" placeholder="Scratch on lens, ok for use..."></textarea>
            </div>
            <button type="submit" class="w-full py-4 bg-brand-primary text-white rounded-xl font-bold hover:bg-brand-primary/90 transition-all">Save Unit</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="modal-edit" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-3xl shadow-2xl p-8 border border-zinc-100 dark:border-zinc-800">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold">Edit Camera Unit</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Model</label>
                    <select name="model_id" id="edit_model_id" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                        @foreach($models as $model)
                            <option value="{{ $model->id }}">{{ $model->model_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Serial Number</label>
                    <input type="text" name="serial_number" id="edit_serial_number" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Status</label>
                <select name="status" id="edit_status" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                    <option value="Available">Available</option>
                    <option value="Booked">Booked</option>
                    <option value="Rented">Rented</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Condition Notes</label>
                <textarea name="condition_notes" id="edit_condition_notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white"></textarea>
            </div>
            <button type="submit" class="w-full py-4 bg-brand-primary text-white rounded-xl font-bold hover:bg-brand-primary/90 transition-all">Update Unit</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, sn, mid, status, notes) {
        const form = document.getElementById('edit-form');
        form.action = `/cameras/${id}`;
        document.getElementById('edit_serial_number').value = sn;
        document.getElementById('edit_model_id').value = mid;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_condition_notes').value = notes;
        document.getElementById('modal-edit').classList.remove('hidden');
    }
</script>
@endsection
