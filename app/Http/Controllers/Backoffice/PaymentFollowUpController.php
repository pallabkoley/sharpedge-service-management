<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentFollowUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentFollowUpController extends Controller
{
    // ❌ NO authorizeBackoffice() method anymore

    public function index(Request $request)
    {
        
 dd('CONTROLLER REACHED');        // ❌ NO $this->authorizeBackoffice();

        $query = Payment::query()
            ->with(['client', 'order', 'followUps.createdBy'])
            ->approved()
            ->payLaterPending();

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by follow-up status
        if ($request->filled('follow_up')) {
            switch ($request->follow_up) {
                case 'never':
                    $query->whereDoesntHave('followUps');
                    break;
                case 'due':
                    $query->whereHas('followUps', function ($q) {
                        $q->whereDate('next_follow_up_date', '<=', Carbon::today());
                    });
                    break;
                case 'upcoming':
                    $query->whereHas('followUps', function ($q) {
                        $q->whereDate('next_follow_up_date', '>', Carbon::today());
                    });
                    break;
            }
        }

        $payments = $query->latest('payment_date')->paginate(20)->withQueryString();

        $totalPending = Payment::approved()->payLaterPending()->count();
        $totalPendingAmount = Payment::approved()->payLaterPending()->sum('amount');
        $todayFollowUps = PaymentFollowUp::whereDate('next_follow_up_date', Carbon::today())->count();
        $overdueFollowUps = PaymentFollowUp::whereDate('next_follow_up_date', '<', Carbon::today())->count();
        $neverFollowedUp = Payment::approved()->payLaterPending()->whereDoesntHave('followUps')->count();

        return view('backoffice.payment_follow_ups.index', compact(
            'payments',
            'totalPending',
            'totalPendingAmount',
            'todayFollowUps',
            'overdueFollowUps',
            'neverFollowedUp'
        ));
    }

    public function store(Request $request)
    {
        // ❌ NO $this->authorizeBackoffice();

        $validated = $request->validate([
            'payment_id'          => 'required|exists:payments,id',
            'follow_up_date'      => 'required|date',
            'next_follow_up_date' => 'nullable|date|after_or_equal:follow_up_date',
            'note'                => 'required|string|max:2000',
        ]);

        $payment = Payment::with(['client', 'order'])->findOrFail($validated['payment_id']);

        if (!$payment->isPayLater()) {
            return back()->withInput()->with('error', 'Follow-up can only be created for Pay Later payments.');
        }
        if (!$payment->isPaymentPending()) {
            return back()->withInput()->with('error', 'This payment has already been received.');
        }

        PaymentFollowUp::create([
            'payment_id'          => $payment->id,
            'order_id'            => $payment->order_id,
            'follow_up_date'      => $validated['follow_up_date'],
            'next_follow_up_date' => $validated['next_follow_up_date'] ?? null,
            'note'                => $validated['note'],
            'created_by'          => Auth::id(),
        ]);

        return redirect()->route('backoffice.payment-follow-ups.index')
                         ->with('success', 'Payment follow-up added successfully.');
    }

    public function show($paymentId)
    {
        // ❌ NO $this->authorizeBackoffice();

        $payment = Payment::with(['client', 'order', 'followUps.createdBy'])->findOrFail($paymentId);
        return view('backoffice.payment_follow_ups.show', compact('payment'));
    }

    public function history($paymentId)
    {
        // ❌ NO $this->authorizeBackoffice();

        $payment = Payment::with(['client', 'order'])->findOrFail($paymentId);
        $followUps = $payment->followUps()->with('createdBy')->latest('id')->get();
        return view('backoffice.payment_follow_ups.history', compact('payment', 'followUps'));
    }

    public function getFollowUps($paymentId)
    {
        // ❌ NO $this->authorizeBackoffice();

        $payment = Payment::findOrFail($paymentId);

        $followUps = $payment->followUps()
            ->with('createdBy')
            ->latest('id')
            ->get()
            ->map(function ($followUp) {
                return [
                    'id'                   => $followUp->id,
                    'follow_up_date'       => $followUp->formatted_follow_up_date,
                    'next_follow_up_date'  => $followUp->formatted_next_follow_up_date,
                    'note'                 => $followUp->note,
                    'created_by'           => optional($followUp->createdBy)->name,
                    'created_at'           => $followUp->created_at?->format('d M Y h:i A'),
                ];
            });

        return response()->json([
            'success'    => true,
            'payment_id' => $payment->id,
            'follow_ups' => $followUps,
        ]);
    }
}