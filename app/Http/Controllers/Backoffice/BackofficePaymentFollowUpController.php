<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentFollowUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BackofficePaymentFollowUpController extends Controller
{
    /**
     * =========================================================
     * PAYMENT FOLLOW-UP INDEX
     * =========================================================
     */
    public function index(Request $request)
    {
        $query = Payment::query()
            ->with([
                'client',
                'order',
                'followUps.createdBy',
            ])
            ->approved()
            ->payLaterPending();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->whereHas('client', function ($q) use ($search) {

                $q->where(function ($clientQuery) use ($search) {

                    $clientQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Follow-up Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('follow_up')) {

            switch ($request->follow_up) {

                case 'never':

                    $query->whereDoesntHave('followUps');

                    break;

                case 'due':

                    $query->whereHas('followUps', function ($q) {

                        $q->whereDate(
                            'next_follow_up_date',
                            '<=',
                            Carbon::today()
                        );
                    });

                    break;

                case 'upcoming':

                    $query->whereHas('followUps', function ($q) {

                        $q->whereDate(
                            'next_follow_up_date',
                            '>',
                            Carbon::today()
                        );
                    });

                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Pending Payments
        |--------------------------------------------------------------------------
        */

        $payments = $query
            ->latest('payment_date')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalPending = Payment::query()
            ->approved()
            ->payLaterPending()
            ->count();

        $totalPendingAmount = Payment::query()
            ->approved()
            ->payLaterPending()
            ->sum('amount');

        $todayFollowUps = PaymentFollowUp::query()
            ->whereDate(
                'next_follow_up_date',
                Carbon::today()
            )
            ->count();

        $overdueFollowUps = PaymentFollowUp::query()
            ->whereDate(
                'next_follow_up_date',
                '<',
                Carbon::today()
            )
            ->count();

        $neverFollowedUp = Payment::query()
            ->approved()
            ->payLaterPending()
            ->whereDoesntHave('followUps')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Return Single Index Page
        |--------------------------------------------------------------------------
        */

        return view(
            'Backoffice.payment_follow_ups.index',
            compact(
                'payments',
                'totalPending',
                'totalPendingAmount',
                'todayFollowUps',
                'overdueFollowUps',
                'neverFollowedUp'
            )
        );
    }


    /**
     * =========================================================
     * STORE FOLLOW-UP
     * =========================================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => [
                'required',
                'exists:payments,id',
            ],

            'follow_up_date' => [
                'required',
                'date',
            ],

            'next_follow_up_date' => [
                'nullable',
                'date',
                'after_or_equal:follow_up_date',
            ],

            'note' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Find Payment
        |--------------------------------------------------------------------------
        */

        $payment = Payment::query()
            ->with([
                'client',
                'order',
            ])
            ->findOrFail($validated['payment_id']);

        /*
        |--------------------------------------------------------------------------
        | Only Pay Later Payments
        |--------------------------------------------------------------------------
        */

        if (!$payment->isPayLater()) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Follow-up can only be created for Pay Later payments.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Must Still Be Pending
        |--------------------------------------------------------------------------
        */

        if (!$payment->isPaymentPending()) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'This payment has already been received.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Follow-up
        |--------------------------------------------------------------------------
        */

        PaymentFollowUp::create([
            'payment_id' => $payment->id,

            'order_id' => $payment->order_id,

            'follow_up_date' =>
                $validated['follow_up_date'],

            'next_follow_up_date' =>
                $validated['next_follow_up_date'] ?? null,

            'note' =>
                $validated['note'],

            'created_by' =>
                Auth::id(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | AJAX Request
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' => 'Payment follow-up added successfully.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Normal Request
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('backoffice.payment-follow-ups.index')
            ->with(
                'success',
                'Payment follow-up added successfully.'
            );
    }


    /**
     * =========================================================
     * AJAX PAYMENT DETAILS
     * =========================================================
     *
     * Used by the Payment Details popup.
     */
    public function details($paymentId)
    {
        $payment = Payment::query()
            ->with([
                'client',
                'order',
                'followUps.createdBy',
            ])
            ->findOrFail($paymentId);

        return response()->json([
            'success' => true,

            'payment' => [
                'id' => $payment->id,

                'amount' => $payment->amount,

                'formatted_amount' =>
                    $payment->formatted_amount,

                'payment_method' =>
                    $payment->payment_method,

                'payment_date' =>
                    $payment->payment_date
                        ? $payment->payment_date->format('d M Y')
                        : null,

                'status' =>
                    $payment->payment_tracking_status,

                'transaction_id' =>
                    $payment->transaction_id,

                'remarks' =>
                    $payment->remarks,

                'client' => $payment->client ? [
                    'id' =>
                        $payment->client->id,

                    'name' =>
                        $payment->client->name,

                    'phone' =>
                        $payment->client->phone,

                    'email' =>
                        $payment->client->email,
                ] : null,

                'order' => $payment->order ? [
                    'id' =>
                        $payment->order->id,

                    'order_id' =>
                        $payment->order->order_id
                            ?? $payment->order->id,
                ] : null,
            ],
        ]);
    }


    /**
     * =========================================================
     * AJAX FOLLOW-UP HISTORY
     * =========================================================
     *
     * Used by the Follow-up History popup.
     */
    public function getFollowUps($paymentId)
    {
        $payment = Payment::query()
            ->findOrFail($paymentId);

        $followUps = $payment
            ->followUps()
            ->with('createdBy')
            ->latest('id')
            ->get()
            ->map(function ($followUp) {

                return [
                    'id' =>
                        $followUp->id,

                    'follow_up_date' =>
                        $followUp->formatted_follow_up_date,

                    'next_follow_up_date' =>
                        $followUp->formatted_next_follow_up_date,

                    'note' =>
                        $followUp->note,

                    'created_by' =>
                        optional($followUp->createdBy)->name
                            ?? 'Unknown',

                    'created_at' =>
                        $followUp->created_at
                            ? $followUp->created_at
                                ->format('d M Y h:i A')
                            : null,
                ];
            });

        return response()->json([
            'success' => true,

            'payment_id' =>
                $payment->id,

            'follow_ups' =>
                $followUps,
        ]);
    }
}