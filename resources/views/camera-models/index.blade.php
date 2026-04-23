@extends('layouts.app')

@section('title', 'Camera Models')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Camera Models</h2>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1 font-medium">Manage types of cameras in your inventory.</p>
        </div>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')" class="px-6 py-3 bg-brand-primary text-white rounded-xl font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-primary/90 transition-all flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Model
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-800/50">
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">#</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Model Name</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($models as $model)
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/20 transition-colors">
                    <td class="px-6 py-4 text-sm font-medium text-zinc-400">{{ $loop->iteration + ($models->currentPage() - 1) * $models->perPage() }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-zinc-900 dark:text-white">{{ $model->model_name }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button onclick="openEditModal({{ $model->id }}, '{{ $model->model_name }}')" class="text-zinc-400 hover:text-brand-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <form action="{{ route('camera-models.destroy', $model) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this model? Items linked will be affected.')" class="text-zinc-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400 font-medium italic">No models found. Add some to get started!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($models->hasPages())
        <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/30">
            {{ $models->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div id="modal-create" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-3xl shadow-2xl p-8 border border-zinc-100 dark:border-zinc-800">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold">Add New Model</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('camera-models.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="model_name" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Model Name</label>
                <input type="text" name="model_name" id="model_name" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white" placeholder="e.g. Instax Mini 11">
            </div>
            <button type="submit" class="w-full py-4 bg-brand-primary text-white rounded-xl font-bold hover:bg-brand-primary/90 transition-all">Save Model</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="modal-edit" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-3xl shadow-2xl p-8 border border-zinc-100 dark:border-zinc-800">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold">Edit Model</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div>
                <label for="edit_model_name" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Model Name</label>
                <input type="text" name="model_name" id="edit_model_name" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
            </div>
            <button type="submit" class="w-full py-4 bg-brand-primary text-white rounded-xl font-bold hover:bg-brand-primary/90 transition-all">Update Model</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name) {
        const form = document.getElementById('edit-form');
        form.action = `/camera-models/${id}`;
        document.getElementById('edit_model_name').value = name;
        document.getElementById('modal-edit').classList.remove('hidden');
    }
</script>
@endsection
