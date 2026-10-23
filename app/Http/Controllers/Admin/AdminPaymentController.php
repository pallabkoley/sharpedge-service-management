<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Client;
use App\Models\PaymentFollowUp;
use Carbon\Carbon;

class AdminPaymentController extends Controller
{
    /**
     * =========================================================
     * INDEX – Payment Records with Pay Later Tracking
     * =========================================================
     */
    public function index(Request $request)
{
    $query = Payment::with([
        'client',
        'receivedByStaff',
        'paidBy.role',
        'followUps.createdBy.role',
    ]);

    // Search
    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function ($q) use ($search) {
            $q->whereHas('client', function ($q2) use ($search) {
                $q2->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('remarks', 'like', '%' . $search . '%')
            ->orWhere('transaction_id', 'like', '%' . $search . '%');
        });
    }

    // Filter by client
    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }

    // Filter by payment method
    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    // =========================================================
    // ✅ PAY LATER FILTERS (Pending, Paid, Upcoming)
    // =========================================================
    if ($request->filled('payment_tracking_status')) {
        if ($request->payment_tracking_status === 'pending') {
            $query->whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
                  ->where('payment_received', false);
        } elseif ($request->payment_tracking_status === 'paid') {
            $query->whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
                  ->where('payment_received', true);
        } elseif ($request->payment_tracking_status === 'upcoming') {
            // Only Pay Later, not received, and has a follow-up with next_follow_up_date in the next 7 days
            $query->whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
                  ->where('payment_received', false)
                  ->whereHas('followUps', function ($q) {
                      $q->where('next_follow_up_date', '>=', Carbon::today())
                        ->where('next_follow_up_date', '<=', Carbon::today()->addDays(7));
                  });
        }
    }

    // Date range filter
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('payment_date', [
            $request->from_date,
            $request->to_date
        ]);
    }

    $payments = $query->latest()->get();

    // Summary cards
    $totalPayments = Payment::count();
    $totalAmount   = Payment::sum('amount');
    $todayAmount   = Payment::whereDate('payment_date', Carbon::today())->sum('amount');
    $monthlyAmount = Payment::whereMonth('payment_date', Carbon::now()->month)
                            ->whereYear('payment_date', Carbon::now()->year)
                            ->sum('amount');

    // Pay Later tracking
    $payLaterPendingCount  = Payment::whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
                                    ->where('payment_received', false)
                                    ->count();
    $payLaterPendingAmount = Payment::whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
                                    ->where('payment_received', false)
                                    ->sum('amount');
    $payLaterPaidCount     = Payment::whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
                                    ->where('payment_received', true)
                                    ->count();
    $payLaterPaidAmount    = Payment::whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
                                    ->where('payment_received', true)
                                    ->sum('amount');

    // ✅ NEW: Upcoming Follow-ups count (next 7 days)$payments = Payment::with(['client', 'receivedByStaff'])

    $upcomingFollowUpsCount = Payment::whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
                                     ->where('payment_received', false)
                                     ->whereHas('followUps', function ($q) {
                                         $q->where('next_follow_up_date', '>=', Carbon::today())
                                           ->where('next_follow_up_date', '<=', Carbon::today()->addDays(7));
                                     })
                                     ->count();

    $clients = Client::orderBy('name')->pluck('name', 'id');

    $paymentMethods = Payment::query()
        ->whereNotNull('payment_method')
        ->where('payment_method', '!=', '')
        ->select('payment_method')
        ->distinct()
        ->orderBy('payment_method')
        ->pluck('payment_method');

    return view('admin.payments.index', compact(
        'payments',
        'clients',
        'paymentMethods',
        'totalPayments',
        'totalAmount',
        'todayAmount',
        'monthlyAmount',
        'payLaterPendingCount',
        'payLaterPendingAmount',
        'payLaterPaidCount',
        'payLaterPaidAmount',
        'upcomingFollowUpsCount'   // ← নতুন ভেরিয়েবল
    ));
}

    /**
     * =========================================================
     * STORE PAYMENT
     * =========================================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'amount'         => 'required|numeric|min:0',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|string',
            'remarks'        => 'nullable|string',
        ]);

        Payment::create([
            'client_id'      => $validated['client_id'],
            'amount'         => $validated['amount'],
            'payment_date'   => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'remarks'        => $validated['remarks'] ?? null,
            'received_by'    => Auth::id(),
            'payment_received' => false,
        ]);

        return redirect()->back()->with('success', 'Payment added successfully!');
    }

    /**
     * =========================================================
     * DESTROY PAYMENT
     * =========================================================
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return redirect()->back()->with('success', 'Payment deleted successfully!');
    }

    /**
     * =========================================================
     * RECEIPT DATA (AJAX)
     * =========================================================
     */
    public function getReceiptData($id)
    {
        $payment = Payment::with('client', 'receivedByStaff')->findOrFail($id);
        
        $html = view('admin.payments.receipt', compact('payment'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * =========================================================
     * RECEIPT VIEW
     * =========================================================
     */
    public function receipt($id)
    {
        $payment = Payment::with('client', 'receivedByStaff')->findOrFail($id);
        return view('admin.payments.receipt', compact('payment'));
    }

    /**
     * =========================================================
     * DOWNLOAD RECEIPT PDF
     * =========================================================
     */
    public function downloadReceipt($id)
    {
        // TODO: Implement PDF download logic
        // Example with DomPDF:
        // $payment = Payment::with('client')->findOrFail($id);
        // $pdf = \PDF::loadView('admin.payments.receipt_pdf', compact('payment'));
        // return $pdf->download('receipt-'.$payment->id.'.pdf');
        
        abort(501, 'Download receipt functionality not implemented yet.');
    }

    /**
     * =========================================================
     * STORE FOLLOW-UP (PAY LATER)
     * =========================================================
     */
    public function storeFollowUp(Request $request, $paymentId)
    {
        $validated = $request->validate([
            'follow_up_date'      => 'required|date',
            'next_follow_up_date' => 'nullable|date|after_or_equal:follow_up_date',
            'note'                => 'nullable|string|max:5000',
        ]);

        $payment = Payment::with('order')->findOrFail($paymentId);

        if (!$payment->isPayLater()) {
            return redirect()->back()->with('error', 'Follow-up is only available for Pay Later payments.');
        }
        if ($payment->isPaymentReceived()) {
            return redirect()->back()->with('error', 'This Pay Later payment has already been marked as paid.');
        }

        PaymentFollowUp::create([
            'payment_id'          => $payment->id,
            'order_id'            => $payment->order_id,
            'follow_up_date'      => $validated['follow_up_date'],
            'next_follow_up_date' => $validated['next_follow_up_date'] ?? null,
            'note'                => $validated['note'] ?? null,
            'created_by'          => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Payment follow-up added successfully.');
    }

    /**
     * =========================================================
     * FOLLOW-UP HISTORY (AJAX)
     * =========================================================
     */
    public function followUpHistory($paymentId)
    {
        $payment = Payment::with([
            'client',
            'order',
            'followUps.createdBy.role',
        ])->findOrFail($paymentId);

        if (!$payment->isPayLater()) {
            return response()->json([
                'success' => false,
                'message' => 'Follow-up history is only available for Pay Later payments.',
            ], 422);
        }

        $followUps = $payment->followUps
            ->sortByDesc('follow_up_date')
            ->values()
            ->map(function ($followUp) {
                return [
                    'id'                  => $followUp->id,
                    'follow_up_date'      => $followUp->follow_up_date?->format('d M Y'),
                    'next_follow_up_date' => $followUp->next_follow_up_date?->format('d M Y'),
                    'note'                => $followUp->note,
                    'created_by'          => $followUp->createdBy?->name ?? 'Unknown User',
                    'role'                => ($followUp->createdBy && $followUp->createdBy->role) ? $followUp->createdBy->role->name : 'N/A',
                    'created_at'          => $followUp->created_at?->format('d M Y h:i A'),
                ];
            });

        return response()->json([
            'success'   => true,
            'payment'   => [
                'id'               => $payment->id,
                'client'           => $payment->client?->name ?? 'N/A',
                'amount'           => number_format((float) $payment->amount, 2),
                'payment_method'   => $payment->payment_method,
                'payment_received' => (bool) $payment->payment_received,
                'paid_at'          => $payment->paid_at?->format('d M Y h:i A'),
                'paid_by'          => $payment->paidBy?->name,
                'paid_by_role'     => ($payment->paidBy && $payment->paidBy->role) ? $payment->paidBy->role->name : null,
            ],
            'follow_ups' => $followUps,
        ]);
    }

    /**
     * =========================================================
     * MARK PAY LATER AS PAID
     * =========================================================
     */
    public function markAsPaid($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if (!$payment->isPayLater()) {
            return redirect()->back()->with('error', 'Only Pay Later payments can be marked as paid here.');
        }
        if ($payment->isPaymentReceived()) {
            return redirect()->back()->with('error', 'This payment has already been marked as paid.');
        }

        $payment->update([
            'payment_received' => true,
            'paid_at'          => Carbon::now(),
            'paid_by'          => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pay Later payment marked as paid successfully.');
    }
}