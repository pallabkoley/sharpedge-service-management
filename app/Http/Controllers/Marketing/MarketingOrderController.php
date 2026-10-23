<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Models
use App\Models\Client;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Staff;
// use App\Models\Coupon;          // ❌ সরানো হয়েছে
// use App\Models\CouponUsage;    // ❌ সরানো হয়েছে
use App\Models\AdminSetting;
use App\Models\OrderStatusLog;
use App\Models\OrderDocument;
use App\Models\DocumentRequirement;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MarketingOrderController extends Controller
{
    /* =====================================================
       ORDERS LIST
    ====================================================== */
    public function index()
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $orders = Order::where('marketing_staff_id', $staff->id)
            ->with(['client', 'items.service', 'payments'])
            ->latest()
            ->paginate(20);

        return view('Marketing.orders.index', compact('orders'));
    }

    /* =====================================================
       SHOW ORDER DETAILS
    ====================================================== */
    public function show($id)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();
        
        $order = Order::where('marketing_staff_id', $staff->id)
            ->where('id', $id)
            ->with(['client', 'items.service', 'payments'])
            ->firstOrFail();

        return view('Marketing.orders.show', compact('order'));
    }

    /* =====================================================
       DELETE/CANCEL ORDER
    ====================================================== */
    public function destroy($id)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $order = Order::where('marketing_staff_id', $staff->id)
            ->where('id', $id)
            ->where('order_status', 'PENDING_PAYMENT')
            ->firstOrFail();

        DB::beginTransaction();

        try {
            $order->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /* =====================================================
       BUY SERVICE (MULTI STEP – MULTI SERVICE)
    ====================================================== */
    public function create(Request $request)
    {
        $step  = $request->get('step', 1);
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $data = [
            'step'  => $step,
            'staff' => $staff,
        ];

        switch ($step) {

            // ==========================================
            // STEP 1 - SELECT CLIENT
            // ==========================================
            case 1:

                $data['clients'] = Client::where(
                        'created_by',
                        $staff->user_id
                    )
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get();

                break;


            // ==========================================
            // STEP 2 - SELECT SERVICE
            // ==========================================
            case 2:

                if (!session('selected_client_id')) {
                    return redirect()->route(
                        'marketing.orders.create',
                        ['step' => 1]
                    );
                }

                $data['services'] = Service::active()->get();

                $data['client'] = Client::find(
                    session('selected_client_id')
                );

                break;


            // ==========================================
            // STEP 3 - GST TYPE & DIRECT DISCOUNT
            // ==========================================
            case 3:

                if (!session('selected_service_ids')) {
                    return redirect()->route(
                        'marketing.orders.create',
                        ['step' => 2]
                    );
                }

                $services = Service::whereIn(
                    'id',
                    session('selected_service_ids')
                )->get();

                $data['services'] = $services;

                $data['client'] = Client::find(
                    session('selected_client_id')
                );

                /*
                 * Get previously entered GST type & discount from session
                 */
                $gstType  = session('gst_type', 'with_gst');
                $discount = (float) session('discount_amount', 0);

                /*
                 * Calculate original amount (uses session gst_type)
                 */
                $amounts = $this->calculateMultipleServiceAmounts($services);

                /*
                 * Never allow discount above total
                 */
                $discount = min(
                    max($discount, 0),
                    $amounts['total']
                );

                /*
                 * Apply discount & finalise amounts
                 */
                $amounts['discount'] = round($discount, 2);
                $netTotal = $amounts['total'] - $discount;
                $amounts['final_total'] = round(max(0, $netTotal));
                $amounts['round_off'] = round($amounts['final_total'] - $netTotal, 2);

                $data['amounts']        = $amounts;
                $data['discount_amount'] = $discount;
                $data['gst_type']       = $gstType;   // Blade-এ দেখানোর জন্য

                break;


            // ==========================================
            // STEP 4 - GENERATE INVOICE
            // ==========================================
            case 4:

                if (!session('selected_service_ids')) {
                    return redirect()->route(
                        'marketing.orders.create',
                        ['step' => 2]
                    );
                }

                $services = Service::whereIn(
                    'id',
                    session('selected_service_ids')
                )->get();

                /*
                 * Get GST type & discount from session
                 */
                $gstType  = session('gst_type', 'with_gst');
                $discount = (float) session('discount_amount', 0);

                /*
                 * Calculate amounts (uses session gst_type)
                 */
                $amounts = $this->calculateMultipleServiceAmounts($services);

                /*
                 * Safety validation
                 */
                $discount = min(
                    max($discount, 0),
                    $amounts['total']
                );

                /*
                 * Apply discount
                 */
                $amounts['discount'] = round($discount, 2);
                $netTotal = $amounts['total'] - $discount;
                $amounts['final_total'] = round(max(0, $netTotal));
                $amounts['round_off'] = round($amounts['final_total'] - $netTotal, 2);

                $data['services']       = $services;
                $data['client']         = Client::find(session('selected_client_id'));
                $data['amounts']        = $amounts;
                $data['discount_amount'] = $discount;
                $data['gst_type']       = $gstType;

                break;


            // ==========================================
            // STEP 5 - PAYMENT ENTRY
            // ==========================================
            case 5:

                $order = Order::find(
                    session('created_order_id')
                );

                if (!$order) {
                    return redirect()->route(
                        'marketing.orders.create',
                        ['step' => 4]
                    );
                }

                $data['order'] = $order;
                $data['client'] = Client::find($order->client_id);

                $data['upi'] = AdminSetting::latest()->first();
                $data['upi_payment_url'] = null;
                $data['dynamic_qr_code'] = null;

                if ($data['upi'] && $data['upi']->upi_id) {
                    $upiPaymentUrl = 'upi://pay'
                        . '?pa=' . $data['upi']->upi_id
                        . '&pn=SharpEdge'
                        . '&am=' . $order->final_amount
                        . '&cu=INR';

                    $data['upi_payment_url'] = $upiPaymentUrl;
                    $data['dynamic_qr_code'] = QrCode::size(200)->generate($upiPaymentUrl);
                }

                break;


            // ==========================================
            // STEP 6 - COMPLETE
            // ==========================================
            case 6:

                $data['order'] = Order::find(
                    session('created_order_id')
                );

                break;
        }

        return view(
            'Marketing.payment.buy-service',
            $data
        );
    }

    /* =====================================================
       PROCESS STEPS
    ====================================================== */
    public function processStep(Request $request)
    {
        switch ($request->step) {

            // ==========================================
            // STEP 1 - SELECT CLIENT
            // ==========================================
            case 1:

                $client = Client::where('id', $request->client_id)
                    ->where('status', 'active')
                    ->firstOrFail();

                session(['selected_client_id' => $client->id]);

                return redirect()->route(
                    'marketing.orders.create',
                    ['step' => 2]
                );


            // ==========================================
            // STEP 2 - SELECT SERVICE
            // ==========================================
            case 2:

                $request->validate([
                    'service_ids' => 'required|array|min:1',
                    'service_ids.*' => 'exists:services,id',
                ]);

                session(['selected_service_ids' => $request->service_ids]);

                return redirect()->route(
                    'marketing.orders.create',
                    ['step' => 3]
                );


            // ==========================================
            // STEP 3 - GST TYPE & DIRECT DISCOUNT
            // ==========================================
            case 3:

                $request->validate([
                    'gst_type' => 'required|in:with_gst,without_gst',
                    'discount_amount' => 'nullable|numeric|min:0',
                ]);

                $gstType = $request->gst_type;
                $discount = (float) ($request->discount_amount ?? 0);

                // Save GST type in session FIRST so calculation uses it
                session(['gst_type' => $gstType]);

                $serviceIds = session('selected_service_ids', []);
                if (empty($serviceIds)) {
                    return redirect()->route('marketing.orders.create', ['step' => 2]);
                }

                $services = Service::whereIn('id', $serviceIds)->get();
                $amounts = $this->calculateMultipleServiceAmounts($services);

                $discount = min(max($discount, 0), $amounts['total']);

                if ($discount > $amounts['total']) {
                    return back()
                        ->withErrors([
                            'discount_amount' => 'Discount cannot exceed ₹' . number_format($amounts['total'], 2)
                        ])
                        ->withInput();
                }

                session(['discount_amount' => round($discount, 2)]);

                return redirect()->route(
                    'marketing.orders.create',
                    ['step' => 4]
                );


            // ==========================================
            // STEP 4 - CREATE ORDER
            // ==========================================
            case 4:

                return $this->createOrder();


            // ==========================================
            // STEP 5 - PAYMENT
            // ==========================================
            case 5:

                return $this->processPayment($request);


            // ==========================================
            // STEP 6 - CLEAR SESSION
            // ==========================================
            case 6:

                $this->clearSession();

                return redirect()->route(
                    'marketing.orders.create',
                    ['step' => 1]
                );
        }

        return back();
    }

    /* =====================================================
       CREATE ORDER (MULTI SERVICE)
    ====================================================== */
    private function createOrder()
    {
        DB::beginTransaction();

        try {
            $staff = Staff::where('user_id', Auth::id())->firstOrFail();
            $client = Client::findOrFail(session('selected_client_id'));

            $serviceIds = session('selected_service_ids');
            if (empty($serviceIds)) {
                throw new \Exception('No services selected.');
            }

            $services = Service::whereIn('id', $serviceIds)->get();
            if ($services->isEmpty()) {
                throw new \Exception('Selected services not found.');
            }

            // Get GST type from session
            $gstType = session('gst_type', 'with_gst');

            // Calculate amounts (uses session gst_type)
            $amounts = $this->calculateMultipleServiceAmounts($services);

            // Direct discount
            $discount = (float) session('discount_amount', 0);
            if ($discount < 0) $discount = 0;
            if ($discount > $amounts['total']) {
                throw new \Exception('Discount cannot exceed total amount.');
            }

            $discount = round($discount, 2);
            $amounts['discount'] = $discount;

            $netTotal = $amounts['total'] - $discount;
            $amounts['final_total'] = round(max(0, $netTotal));
            $amounts['round_off'] = round($amounts['final_total'] - $netTotal, 2);

            // Create order
            $order = Order::create([
                'order_id' => 'ORD-'
                    . now()->format('Ymd')
                    . '-'
                    . strtoupper(uniqid()),

                'client_id' => $client->id,
                'service_id' => null, // multiple services
                'marketing_staff_id' => $staff->id,

                'base_price' => $amounts['our_fee'],
                'estimated_govt_fee' => $amounts['govt_fee'],

                'discount_amount' => $amounts['discount'] ?? 0,
                'gst_type' => $gstType,          // ✅ saved
                'final_amount' => $amounts['final_total'],

                'coupon_id' => null,              // no coupon
                'order_status' => 'PENDING_PAYMENT',
                'payment_status' => 'Pending',
            ]);

            // Create order items
            foreach ($services as $service) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'price' => $service->our_fee,
                    'quantity' => 1,
                    'total' => $service->our_fee,
                ]);
            }

            DB::commit();

            session(['created_order_id' => $order->id]);

            return redirect()->route('marketing.orders.create', ['step' => 5]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Marketing order creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'selected_client_id' => session('selected_client_id'),
                'selected_service_ids' => session('selected_service_ids'),
            ]);

            return back()->withErrors($e->getMessage());
        }
    }

    /* =====================================================
       PAYMENT
    ====================================================== */
    private function processPayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:Cash,UPI,Bank Transfer,Cheque,PAY_LATER',
            'amount'         => 'required|numeric|min:1',
            'payment_date'   => 'required|date',
            'transaction_id' => 'nullable|string|max:255',
            'payment_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $order  = Order::findOrFail(session('created_order_id'));
            $staff  = Staff::where('user_id', Auth::id())->firstOrFail();
            $client = Client::findOrFail($order->client_id);

            if ($request->payment_method === 'PAY_LATER' && !$client->is_credit_allowed) {
                DB::rollBack();
                return back()->withErrors('This client is not allowed for Pay Later payment.');
            }

            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
            }

            // PAY LATER FLOW
            if ($request->payment_method === 'PAY_LATER') {
                Payment::create([
                    'order_id'           => $order->id,
                    'client_id'          => $order->client_id,
                    'payment_method'     => 'PAY_LATER',
                    'transaction_id'     => null,
                    'payment_proof_path' => null,
                    'amount'             => $order->final_amount,
                    'payment_date'       => $request->payment_date,
                    'received_by'        => $staff->id,
                    'status'             => 'submitted',
                ]);

                $order->update([
                    'order_status'   => 'CREDIT_PENDING',
                    'payment_status' => 'Pending',
                    'payment_method' => 'PAY_LATER',
                ]);

                OrderStatusLog::create([
                    'order_id'   => $order->id,
                    'status'     => 'CREDIT_PENDING',
                    'changed_by' => $staff->id,
                    'remarks'    => 'Pay Later selected (credit allowed)',
                ]);

                DB::commit();
                return redirect()->route('marketing.orders.create', ['step' => 6]);
            }

            // NORMAL PAYMENT FLOW
            Payment::create([
                'order_id'           => $order->id,
                'client_id'          => $order->client_id,
                'payment_method'     => $request->payment_method,
                'transaction_id'     => $request->transaction_id,
                'payment_proof_path' => $proofPath,
                'amount'             => $request->amount,
                'payment_date'       => $request->payment_date,
                'received_by'        => $staff->id,
                'status'             => 'submitted',
            ]);

            $order->update([
                'order_status'   => 'PARTIAL_PAYMENT',
                'payment_status' => 'Pending',
                'payment_method' => $request->payment_method,
            ]);

            OrderStatusLog::create([
                'order_id'   => $order->id,
                'status'     => 'PAYMENT_SUBMITTED',
                'changed_by' => $staff->id,
                'remarks'    => 'Payment submitted by marketing',
            ]);

            DB::commit();
            return redirect()->route('marketing.orders.create', ['step' => 6]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Payment failed: ' . $e->getMessage());
            return back()->withErrors('Payment failed: ' . $e->getMessage());
        }
    }

    /* =====================================================
       INVOICE (AFTER ADMIN APPROVAL)
    ====================================================== */
    public function invoice(Order $order)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        if ($order->marketing_staff_id !== $staff->id) {
            abort(403);
        }

        if ($order->order_status !== 'PAID') {
            return back()->withErrors('Invoice available only after approval');
        }

        $order->load(['client', 'items.service', 'payments']);

        if (!$order->invoice_generated) {
            $order->update([
                'invoice_generated' => 1,
                'invoice_path'      => 'INV-' . $order->order_id,
            ]);
        }

        return view('Marketing.orders.invoice', compact('order'));
    }

    /* =====================================================
       HELPERS – MULTI SERVICE CALCULATION
    ====================================================== */
    private function calculateMultipleServiceAmounts($services)
    {
        $totalOurFee   = floatval($services->sum('our_fee') ?? 0);
        $totalGovtFee  = floatval($services->sum('govt_fee') ?? 0);
        $totalPlatform = floatval($services->sum('platform_fee') ?? 0);

        $subtotal = $totalOurFee + $totalGovtFee + $totalPlatform;

        $gstRate  = floatval(config('app.gst_percentage', 18));
        $cgstRate = $gstRate / 2;
        $sgstRate = $gstRate / 2;

        // ✅ GST type check – session থেকে read
        $gstType = session('gst_type', 'with_gst');

        if ($gstType === 'with_gst') {
            $cgst = round(($subtotal * $cgstRate) / 100, 2);
            $sgst = round(($subtotal * $sgstRate) / 100, 2);
        } else {
            $cgst = 0;
            $sgst = 0;
        }

        $tax = $cgst + $sgst;
        $total = $subtotal + $tax;

        return [
            'our_fee'      => round($totalOurFee, 2),
            'govt_fee'     => round($totalGovtFee, 2),
            'platform_fee' => round($totalPlatform, 2),
            'subtotal'     => round($subtotal, 2),
            'gst_rate'     => $gstRate,
            'cgst_rate'    => $cgstRate,
            'sgst_rate'    => $sgstRate,
            'cgst'         => $cgst,
            'sgst'         => $sgst,
            'tax'          => round($tax, 2),
            'discount'     => 0,
            'total'        => round($total, 2),
            'round_off'    => 0,
            'final_total'  => round($total),
        ];
    }

    // ❌ applyCoupon() সম্পূর্ণ সরানো হয়েছে

    private function clearSession()
    {
        session()->forget([
            'selected_client_id',
            'selected_service_ids',
            'discount_amount',
            'gst_type',            // ✅ নতুন যোগ
            'created_order_id',
        ]);
    }

    /* =====================================================
       DOCUMENTS (RETAINED FROM ORIGINAL)
    ====================================================== */
    public function loadOrderDocuments($orderId)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $order = Order::where('id', $orderId)
            ->where('marketing_staff_id', $staff->id)
            ->where('order_status', 'PAID')
            ->with('items.service')
            ->firstOrFail();

        $serviceId = $order->items->first()->service_id;

        $requirements = DocumentRequirement::where('service_id', $serviceId)
            ->where('status', 'active')
            ->with('documentMaster')
            ->orderBy('order')
            ->get();

        $uploadedDocs = OrderDocument::where('order_id', $order->id)
            ->get()
            ->keyBy('document_master_id');

        return response()->json([
            'order' => $order,
            'requirements' => $requirements,
            'uploaded_docs' => $uploadedDocs,
        ]);
    }

    public function uploadOrderDocument(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'document_master_id' => 'required|exists:document_masters,id',
            'file' => 'required|file|max:5120',
        ]);

        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $order = Order::where('id', $request->order_id)
            ->where('marketing_staff_id', $staff->id)
            ->where('order_status', 'PAID')
            ->with('items')
            ->firstOrFail();

        $serviceId = $order->items->first()->service_id;

        $existingDocument = OrderDocument::where('order_id', $order->id)
            ->where('document_master_id', $request->document_master_id)
            ->first();

        $file = $request->file('file');
        $filePath = $file->store('order_client_documents', 'public');

        if ($existingDocument) {
            if ($existingDocument->file_path && Storage::disk('public')->exists($existingDocument->file_path)) {
                Storage::disk('public')->delete($existingDocument->file_path);
            }
            $existingDocument->update([
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'status' => 'UPLOADED',
                'uploaded_by' => $staff->id,
                'uploaded_at' => now(),
            ]);
            $document = $existingDocument;
        } else {
            $document = OrderDocument::create([
                'order_id' => $order->id,
                'service_id' => $serviceId,
                'document_master_id' => $request->document_master_id,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'status' => 'UPLOADED',
                'uploaded_by' => $staff->id,
                'uploaded_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'document' => $document
        ]);
    }

    public function deleteOrderDocument($id)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $document = OrderDocument::where('id', $id)
            ->with('order')
            ->firstOrFail();

        if ($document->order->marketing_staff_id !== $staff->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        if (in_array($document->status, ['VERIFIED', 'APPROVED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Verified or approved documents cannot be deleted'
            ], 403);
        }

        DB::beginTransaction();
        try {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document'
            ], 500);
        }
    }

    public function checkDocumentExists($orderId, $documentMasterId)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $exists = OrderDocument::where('order_id', $orderId)
            ->where('document_master_id', $documentMasterId)
            ->whereHas('order', function($q) use ($staff) {
                $q->where('marketing_staff_id', $staff->id);
            })
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    /* =====================================================
       DUE PAYMENT (RETAINED)
    ====================================================== */
    public function getPaymentStatus($orderId)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();
        
        $order = Order::where('id', $orderId)
            ->where('marketing_staff_id', $staff->id)
            ->with(['payments' => function($q) {
                $q->latest();
            }])
            ->firstOrFail();
        
        $latestPayment = $order->payments->first();
        $dueAmount = $order->balance_amount ?? 0;
        
        return response()->json([
            'success' => true,
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status,
            'due_amount' => $dueAmount,
            'paid_amount' => $order->paid_amount ?? 0,
            'total_amount' => $order->final_amount,
            'has_pending_payment' => $latestPayment && $latestPayment->status === 'submitted',
            'latest_payment_status' => $latestPayment ? $latestPayment->status : null
        ]);
    }

    public function payDue(Request $request, Order $order)
    {
        $request->validate([
            'payment_method' => 'required|in:Cash,UPI,Bank Transfer,Cheque',
            'amount'         => 'required|numeric|min:1',
            'payment_date'   => 'required|date',
            'transaction_id' => 'nullable|string|max:255',
            'payment_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $staff = Staff::where('user_id', Auth::id())->first();
            if (!$staff) {
                throw new \Exception('Staff not found');
            }

            $paid = $order->paid_amount;
            $due  = $order->due_amount;

            if ($due <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order is already fully paid'
                ], 400);
            }

            if ($request->amount > $due) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amount exceeds due ₹' . number_format($due, 2)
                ], 400);
            }

            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
            }

            Payment::create([
                'order_id'           => $order->id,
                'client_id'          => $order->client_id,
                'payment_method'     => $request->payment_method,
                'transaction_id'     => $request->transaction_id,
                'payment_proof_path' => $proofPath,
                'amount'             => $request->amount,
                'payment_date'       => $request->payment_date,
                'received_by'        => $staff->id,
                'status'             => 'approved',
                'approved_by'        => $staff->id,
                'approved_at'        => now(),
            ]);

            $order->load('payments');
            $paid = $order->paid_amount;
            $due  = $order->due_amount;

            if ($order->isPaid()) {
                $order->update(['order_status' => 'PAID']);
            } else {
                $order->update(['order_status' => 'PARTIAL_PAYMENT']);
            }

            OrderStatusLog::create([
                'order_id'   => $order->id,
                'status'     => 'PAYMENT_ADDED',
                'changed_by' => $staff->id,
                'remarks'    => "Manual payment ₹{$request->amount} via {$request->payment_method}",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully',
                'paid'    => $paid,
                'due'     => $due,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCurrentStaffId()
    {
        $staff = Staff::where('user_id', Auth::id())->first();
        if ($staff) {
            return $staff->id;
        }

        if (Auth::user()->email) {
            $staff = Staff::where('email', Auth::user()->email)->first();
            if ($staff) {
                return $staff->id;
            }
        }

        throw new \Exception('Staff record not found for current user');
    }
}