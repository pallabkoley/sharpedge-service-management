<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentFollowUp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentFollowUpController extends Controller
{
    /**
     * =========================================================
     * MARKETING AUTHORIZATION
     * =========================================================
     */
    private function authorizeMarketing()
    {
        $user = Auth::user();

        abort_unless(
            $user &&
            $user->role &&
            strtolower($user->role->name) === 'marketing',
            403,
            'Unauthorized access.'
        );
    }


    /**
     * =========================================================
     * PAYMENT FOLLOW-UP INDEX
     * =========================================================
     */
    public function index(Request $request)
    {
        $this->authorizeMarketing();

        /*
        |--------------------------------------------------------------------------
        | Pending Pay Later Payments
        |--------------------------------------------------------------------------
        */

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
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');

                });

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Follow-Up Filter
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
        | Payments Pagination
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
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'Marketing.payment_follow_ups.index',
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
        $this->authorizeMarketing();

        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'payment_id'          => 'required|exists:payments,id',
            'follow_up_date'      => 'required|date',
            'next_follow_up_date' => 'nullable|date|after_or_equal:follow_up_date',
            'note'                => 'required|string|max:2000',
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
        | Make Sure Payment Is Pay Later
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
        | Make Sure Payment Is Still Pending
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
        | Create Follow-Up
        |--------------------------------------------------------------------------
        */

        PaymentFollowUp::create([
            'payment_id'          => $payment->id,
            'order_id'            => $payment->order_id,
            'follow_up_date'      => $validated['follow_up_date'],
            'next_follow_up_date' => $validated['next_follow_up_date'] ?? null,
            'note'                => $validated['note'],
            'created_by'          => Auth::id(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('marketing.payment-follow-ups.index')
            ->with(
                'success',
                'Payment follow-up added successfully.'
            );
    }


    /**
     * =========================================================
     * SHOW PAYMENT
     * =========================================================
     */
    public function show($paymentId)
    {
        $this->authorizeMarketing();

        $payment = Payment::query()
            ->with([
                'client',
                'order',
                'followUps.createdBy',
            ])
            ->findOrFail($paymentId);


        return view(
            'marketing.payment_follow_ups.show',
            compact('payment')
        );
    }


    /**
     * =========================================================
     * FOLLOW-UP HISTORY PAGE
     * =========================================================
     */
    public function history($paymentId)
    {
        $this->authorizeMarketing();

        $payment = Payment::query()
            ->with([
                'client',
                'order',
            ])
            ->findOrFail($paymentId);


        $followUps = $payment->followUps()
            ->with('createdBy')
            ->latest('id')
            ->get();


        return view(
            'marketing.payment_follow_ups.history',
            compact(
                'payment',
                'followUps'
            )
        );
    }


    /**
     * =========================================================
     * AJAX FOLLOW-UP HISTORY
     * =========================================================
     */
    public function getFollowUps($paymentId)
    {
        $this->authorizeMarketing();

        $payment = Payment::query()
            ->findOrFail($paymentId);


        $followUps = $payment->followUps()
            ->with('createdBy')
            ->latest('id')
            ->get()
            ->map(function ($followUp) {

                return [

                    'id' => $followUp->id,

                    'follow_up_date' =>
                        $followUp->formatted_follow_up_date,

                    'next_follow_up_date' =>
                        $followUp->formatted_next_follow_up_date,

                    'note' =>
                        $followUp->note,

                    'created_by' =>
                        optional($followUp->createdBy)->name,

                    'created_at' =>
                        $followUp->created_at
                            ? $followUp->created_at->format('d M Y h:i A')
                            : null,

                ];
            });


        return response()->json([

            'success' => true,

            'payment_id' => $payment->id,

            'follow_ups' => $followUps,

        ]);
    }
}