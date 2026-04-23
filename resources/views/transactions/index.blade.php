@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Rental Transactions</h2>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1 font-medium">Capture rental details, tracking, and returns.</p>
        </div>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')" class="px-6 py-3 bg-brand-primary text-white rounded-xl font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-primary/90 transition-all flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create New Rental
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-800/50">
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Customer</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Items</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Time Range</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800">Status</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-800 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($transactions as $trx)
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/20 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-zinc-900 dark:text-white">{{ $trx->customer->full_name }}</div>
                        <div class="text-xs text-zinc-500 font-medium">{{ $trx->customer->whatsapp }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($trx->items as $item)
                                <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-bold rounded uppercase border border-zinc-200 dark:border-zinc-700">
                                    {{ $item->camera->model->model_name }} ({{ $item->camera->serial_number }})
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs font-bold text-zinc-700 dark:text-zinc-300">
                            {{ $trx->start_time->format('d M H:i') }} - {{ $trx->end_time->format('d M H:i') }}
                        </div>
                        <div class="text-[10px] text-zinc-400 font-medium">Duration: {{ $trx->start_time->diffInHours($trx->end_time) }} Hours</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border w-fit {{ $trx->is_returned ? 'bg-green-500/10 text-green-600 border-green-500/20' : 'bg-brand-accent/10 text-brand-accent border-brand-accent/20' }}">
                                {{ $trx->is_returned ? 'RETURNED' : 'ON RENTAL' }}
                            </span>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-tight">Payment: {{ $trx->payment_status }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @if(!$trx->is_returned)
                        <button onclick="openReturnModal({{ $trx->id }})" class="px-3 py-1 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-colors">
                            Return Items
                        </button>
                        @endif
                        <form action="{{ route('transactions.destroy', $trx) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete record?')" class="text-zinc-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400 font-medium italic">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($transactions->hasPages())
        <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/30">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div id="modal-create" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-4xl rounded-3xl shadow-2xl p-8 border border-zinc-100 dark:border-zinc-800 overflow-y-auto max-h-[90vh]">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold">New Rental Transaction</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Customer</label>
                    <select name="customer_id" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Start Time</label>
                        <input type="datetime-local" name="start_time" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">End Time</label>
                        <input type="datetime-local" name="end_time" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                    </div>
                </div>
            </div>

            <div class="border-t border-zinc-100 dark:border-zinc-800 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-bold">Cameras to Rent</h4>
                    <button type="button" onclick="addCameraRow()" class="text-xs font-bold text-brand-primary hover:underline">+ Add Unit</button>
                </div>
                <div id="camera-rows" class="space-y-4">
                    <!-- Dynamic Rows Here -->
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Deposit (Security)</label>
                    <input type="number" name="deposit" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Payment Status</label>
                    <select name="payment_status" required class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white">
                        <option value="Unpaid">Unpaid</option>
                        <option value="Partial/DP">Partial/DP</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-brand-primary text-white rounded-xl font-bold hover:bg-brand-primary/90 transition-all shadow-xl shadow-brand-primary/20">Finalize Transaction</button>
        </form>
    </div>
</div>

<!-- Return Modal -->
<div id="modal-return" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-3xl shadow-2xl p-8 border border-zinc-100 dark:border-zinc-800">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold">Process Return</h3>
            <button onclick="document.getElementById('modal-return').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="return-form" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Late Fee Amount</label>
                <input type="number" name="late_fee_amount" id="late_fee_amount" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 focus:ring-2 focus:ring-brand-primary outline-none transition-all dark:text-white" placeholder="0">
                <p class="text-[10px] text-zinc-500 mt-2 italic">Fee will be added if returned after designated end time.</p>
            </div>
            <button type="submit" class="w-full py-4 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition-all">Mark as Returned</button>
        </form>
    </div>
</div>

<script>
    let cameraIndex = 0;
    const availableCameras = @json($availableCameras);

    function addCameraRow() {
        const container = document.getElementById('camera-rows');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-12 gap-4 items-end bg-zinc-50 dark:bg-zinc-800/30 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800';
        div.id = `camera-row-${cameraIndex}`;
        
        let options = availableCameras.map(c => `<option value="${c.id}">${c.model.model_name} (${c.serial_number})</option>`).join('');

        div.innerHTML = `
            <div class="col-span-12 md:col-span-5">
                <label class="block text-[10px] font-bold uppercase text-zinc-500 mb-1">Camera Unit</label>
                <select name="cameras[${cameraIndex}][id]" required class="w-full px-3 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs">
                    <option value="">Select Unit</option>
                    ${options}
                </select>
            </div>
            <div class="col-span-12 md:col-span-3">
                <label class="block text-[10px] font-bold uppercase text-zinc-500 mb-1">Rate</label>
                <input type="number" name="cameras[${cameraIndex}][rate]" required class="w-full px-3 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs" placeholder="Amount">
            </div>
            <div class="col-span-12 md:col-span-3">
                <label class="block text-[10px] font-bold uppercase text-zinc-500 mb-1">Type</label>
                <select name="cameras[${cameraIndex}][rate_type]" required class="w-full px-3 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs">
                    <option value="Daily">Daily</option>
                    <option value="Hourly">Hourly</option>
                </select>
            </div>
            <div class="col-span-12 md:col-span-1 text-right">
                <button type="button" onclick="removeRow(${cameraIndex})" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        `;
        container.appendChild(div);
        cameraIndex++;
    }

    function removeRow(idx) {
        document.getElementById(`camera-row-${idx}`).remove();
    }

    function openReturnModal(id) {
        const form = document.getElementById('return-form');
        form.action = `/transactions/${id}/return`;
        document.getElementById('modal-return').classList.remove('hidden');
    }

    // Add one row by default
    addCameraRow();

    // Auto-open modal if 'new' parameter is present
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('new')) {
            document.getElementById('modal-create').classList.remove('hidden');
        }
    });
</script>
@endsection
