<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Client;

class PaymentController extends Controller
{
    /**
     * 🔹 Index (Single Page)
     */
    public function index()
    {
        $payments = Payment::with(['client', 'receivedByStaff'])
            ->latest()
            ->paginate(20);

        $clients = Client::pluck('name', 'id');

        return view('Marketing.payment.index2', compact('payments', 'clients'));
    }

    /**
     * 🔹 Store (Add Payment)
     */
    public function store(Request $request)
    {
        // ✅ Validation
        $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'remarks'        => 'nullable|string',
        ]);

        // ✅ Get staff id safely
        $staffId = optional(auth()->user()->staff)->id;

        // ✅ Create payment
        Payment::create([
            'client_id'      => $request->client_id,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date'   => now(),
            'remarks'        => $request->remarks,

            // 🔥 correct mapping
            'received_by'    => $staffId,

            // simple system
            'status'         => 'approved',
        ]);

        return redirect()->back()->with('success', 'Payment added successfully!');
    }

    /**
     * 🔹 Update (Edit Payment)
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'remarks'        => 'nullable|string',
        ]);

        $payment->update([
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'remarks'        => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Payment updated successfully!');
    }

    /**
     * 🔹 Delete (Optional)
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->back()->with('success', 'Payment deleted successfully!');
    }
}