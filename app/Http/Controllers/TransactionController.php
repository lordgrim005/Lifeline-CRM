<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['customer', 'items.camera.model'])
            ->latest()
            ->paginate(10);
        
        $customers = Customer::all();
        $availableCameras = Camera::with('model')->where('status', 'Available')->get();

        return view('transactions.index', compact('transactions', 'customers', 'availableCameras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'cameras' => 'required|array',
            'cameras.*.id' => 'required|exists:cameras,id',
            'cameras.*.rate' => 'required|numeric',
            'cameras.*.rate_type' => 'required|string',
            'deposit' => 'nullable|numeric',
            'payment_status' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $transaction = Transaction::create([
                'customer_id' => $request->customer_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'payment_status' => $request->payment_status,
                'deposit' => $request->deposit ?? 0,
                'is_returned' => false,
            ]);

            foreach ($request->cameras as $cameraData) {
                // Calculate line total based on rate and rate type
                // Simple version for now: line_total = rate (fixed for the duration)
                // In production, you might multiply rate by hours/days
                $transaction->items()->create([
                    'camera_id' => $cameraData['id'],
                    'rate' => $cameraData['rate'],
                    'rate_type' => $cameraData['rate_type'],
                    'line_total' => $cameraData['rate'], // Assumption: rate is for the total rental
                ]);

                // Update Camera status
                Camera::find($cameraData['id'])->update(['status' => 'Rented']);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Rental transaction created successfully.');
    }

    public function return(Request $request, Transaction $transaction)
    {
        $request->validate([
            'late_fee_amount' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, $transaction) {
            $transaction->update([
                'is_returned' => true,
                'late_fee_amount' => $request->late_fee_amount ?? 0,
                'payment_status' => 'Paid',
            ]);

            foreach ($transaction->items as $item) {
                $item->camera->update(['status' => 'Available']);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Items marked as returned.');
    }

    public function destroy(Transaction $transaction)
    {
        // Only allow delete if not returned or handle status rollback
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted.');
    }
}
