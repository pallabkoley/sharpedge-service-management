<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MarketingPaymentController extends Controller
{
    /**
     * Show payment entry form
     */
    public function create($orderId)
    {
        $order = Order::with(['client', 'items.service'])
            ->where('id', $orderId)
            ->where('order_status', 'PENDING_PAYMENT')
            ->firstOrFail();

        return view('Marketing.payments.create', compact('order'));
    }

    /**
     * Store payment submitted by marketing
     */
    public function store(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('order_status', 'PENDING_PAYMENT')
            ->firstOrFail();

        $request->validate([
            'payment_method'     => 'required|in:UPI,Cash,Bank Transfer,Cheque',
            'transaction_id'     => 'nullable|string|max:150',
            'payment_date'       => 'required|date',
            'amount'             => 'required|numeric|min:1',
            'payment_proof'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $proofPath = null;

        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')
                ->store('payment-proofs', 'public');
        }

        Payment::create([
            'order_id'          => $order->id,
            'payment_method'    => $request->payment_method,
            'transaction_id'    => $request->transaction_id,
            'payment_proof_path'=> $proofPath,
            'amount'            => $request->amount,
            'payment_date'      => $request->payment_date,
            'received_by'       => Auth::user()->staff->id,
            'status'            => 'submitted',
        ]);

        // Update order status
        $order->update([
            'order_status' => 'PAID_PENDING_APPROVAL',
        ]);

        // Log status change
        $order->statusLogs()->create([
            'status'     => 'PAID_PENDING_APPROVAL',
            'changed_by'=> Auth::user()->staff->id,
            'remarks'   => 'Payment submitted by marketing',
        ]);

        return redirect()
            ->route('marketing.dashboard')
            ->with('success', 'Payment submitted successfully. Waiting for admin approval.');
    }
}
