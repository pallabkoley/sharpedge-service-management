<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

// Models
use App\Models\Client;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Coupon;
use App\Models\Staff;
use App\Models\CouponUsage;
use App\Models\AdminSetting;
use App\Models\OrderStatusLog;
use App\Models\OrderDocument;
use App\Models\DocumentRequirement;
use App\Models\Task;
use App\Models\WorkProcess;
use App\Models\WorkProcessStep;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminOrderController extends Controller
{
    /* =====================================================
       ORDERS LIST (For Admin)
    ====================================================== */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        
        $query = Order::with(['client', 'items.service', 'payments'])
            ->latest();

        // Filter by search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($status && $status !== 'all') {
            $query->where('order_status', $status);
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders', 'search', 'status'));
    }

    /* =====================================================
       SHOW ORDER DETAILS
    ====================================================== */
    public function show($id)
    {
        $order = Order::with(['client', 'items.service', 'payments'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /* =====================================================
       BUY SERVICE (MULTI STEP) - ADMIN VERSION
       ✅ MODIFIED: Supports multiple services per order
    ====================================================== */
public function create(Request $request)
{
    $step = $request->get('step', 1);

    // 🔥 Always keep step safe
    $data = ['step' => $step];

    switch ($step) {

        // =========================
        // STEP 1: CLIENT
        // =========================
        case 1:
            $data['clients'] = Client::where('status', 'active')
                ->orderBy('name')
                ->get();
            break;


        // =========================
        // STEP 2: SERVICES (MULTI)
        // =========================
        case 2:
            if (!session('selected_client_id')) {
                return redirect()->route('admin.orders.create', ['step' => 1]);
            }

            $data['services'] = Service::active()->get();
            $data['client']   = Client::find(session('selected_client_id'));
            break;


        // =========================
        // STEP 3: COUPON
        // =========================
        case 3:
            if (!session('selected_service_ids')) {
                return redirect()->route('admin.orders.create', ['step' => 2]);
            }

            $services = Service::whereIn('id', session('selected_service_ids'))->get();
            $client   = Client::find(session('selected_client_id'));

            $amounts = $this->calculateCombinedAmountsForServices($services);

            $data = array_merge($data, [
                'services' => $services,
                'client'   => $client,
                'coupons'  => Coupon::where('status', 'active')->get(),
                'amounts'  => $amounts,
            ]);
            break;


        // =========================
        // STEP 4: SUMMARY
        // =========================
        case 4:
            if (!session('selected_service_ids')) {
                return redirect()->route('admin.orders.create', ['step' => 2]);
            }

            $services = Service::whereIn('id', session('selected_service_ids'))->get();
            $client   = Client::find(session('selected_client_id')); // 🔥 IMPORTANT FIX
            $coupon   = Coupon::find(session('selected_coupon_id'));

            $amounts = $this->calculateCombinedAmountsForServices($services);

            if ($coupon) {
                $amounts = $this->applyCouponToCombined($amounts, $coupon);
            }

            $data = array_merge($data, [
                'services' => $services,
                'client'   => $client,
                'coupon'   => $coupon,
                'amounts'  => $amounts,
            ]);
            break;


        // =========================
        // STEP 5: PAYMENT
        // =========================
        case 5:
            if (!session('created_order_id')) {
                return redirect()->route('admin.orders.create', ['step' => 4]);
            }

            $order = Order::find(session('created_order_id'));

            if (!$order) {
                return redirect()->route('admin.orders.create', ['step' => 4]);
            }

            $client = Client::find($order->client_id);
            $upi    = AdminSetting::latest()->first();

            $upiPaymentUrl = null;
            $dynamicQrCode = null;

            if ($upi && $upi->upi_id) {
                $upiPaymentUrl = "upi://pay"
                    . "?pa={$upi->upi_id}"
                    . "&pn=SharpEdge"
                    . "&am={$order->final_amount}"
                    . "&cu=INR";

                $dynamicQrCode = QrCode::size(200)->generate($upiPaymentUrl);
            }

            $data = array_merge($data, [
                'order'            => $order,
                'client'           => $client,
                'upi'              => $upi,
                'upi_payment_url'  => $upiPaymentUrl,
                'dynamic_qr_code'  => $dynamicQrCode,
            ]);
            break;


        // =========================
        // STEP 6: DONE
        // =========================
        case 6:
            if (!session('created_order_id')) {
                return redirect()->route('admin.orders.create', ['step' => 1]);
            }

            $data['order'] = Order::find(session('created_order_id'));
            break;
    }

    return view('admin.orders.payment.buy-service', $data);
}

    /* =====================================================
       PROCESS STEPS - ADMIN VERSION
       ✅ MODIFIED: Stores array of service IDs
    ====================================================== */
    public function processStep(Request $request)
    {
        switch ($request->step) {
            case 1:
                $client = Client::where('id', $request->client_id)
                    ->where('status', 'active')
                    ->firstOrFail();

                session(['selected_client_id' => $client->id]);
                return redirect()->route('admin.orders.create', ['step' => 2]);

            case 2:
                // ✅ Store array of selected service IDs
                $serviceIds = $request->input('service_ids', []);
                if (empty($serviceIds)) {
                    return back()->withErrors('Please select at least one service.');
                }
                session(['selected_service_ids' => $serviceIds]);
                return redirect()->route('admin.orders.create', ['step' => 3]);

            case 3:
                // Handle coupon removal or application
                if ($request->has('remove_coupon')) {
                    session()->forget('selected_coupon_id');
                    return redirect()->route('admin.orders.create', ['step' => 4])
                        ->with('success', 'Coupon removed successfully.');
                }
                
                if ($request->action == 'apply') {
                    $couponCode = trim($request->coupon_code ?? '');
                    
                    if (empty($couponCode)) {
                        session()->forget('selected_coupon_id');
                        return redirect()->route('admin.orders.create', ['step' => 4])
                            ->with('success', 'No coupon applied.');
                    }
                    
                    $coupon = Coupon::where('code', $couponCode)
                        ->where('status', 'active')
                        ->first();
                    
                    if ($coupon) {
                        $today = now()->toDateString();
                        if ($today < $coupon->start_date || $today > $coupon->end_date) {
                            session()->forget('selected_coupon_id');
                            return redirect()->route('admin.orders.create', ['step' => 4])
                                ->with('error', 'This coupon is expired or not yet active.');
                        }
                        
                        session(['selected_coupon_id' => $coupon->id]);
                        return redirect()->route('admin.orders.create', ['step' => 4])
                            ->with('success', 'Coupon applied successfully!');
                    } else {
                        session()->forget('selected_coupon_id');
                        return redirect()->route('admin.orders.create', ['step' => 4])
                            ->with('error', 'Invalid coupon code. Please check and try again.');
                    }
                }
                
                if ($request->action == 'skip') {
                    session()->forget('selected_coupon_id');
                    return redirect()->route('admin.orders.create', ['step' => 4])
                        ->with('success', 'No coupon applied. You can continue.');
                }
                
                return redirect()->route('admin.orders.create', ['step' => 4]);

            case 4:
                return $this->createOrder();

            case 5:
                return $this->processPayment($request);

            case 6:
                $this->clearSession();
                return redirect()->route('admin.orders.create', ['step' => 1]);
        }

        return back();
    }

    /* =====================================================
       CREATE ORDER - ADMIN VERSION (Multiple Services)
       ✅ MODIFIED: Creates one order with multiple order items
    ====================================================== */
    private function createOrder()
    {
        DB::beginTransaction();

        try {
            $client   = Client::findOrFail(session('selected_client_id'));
            $services = Service::whereIn('id', session('selected_service_ids'))->get();
            $coupon   = Coupon::find(session('selected_coupon_id'));

            if ($services->isEmpty()) {
                throw new \Exception('No services selected.');
            }

            // ✅ Coupon validation + usage limit check (if applied)
            if ($coupon) {
                if ($coupon->status !== 'active') {
                    throw new \Exception('Coupon is inactive');
                }

                $today = now()->toDateString();
                if ($today < $coupon->start_date || $today > $coupon->end_date) {
                    throw new \Exception('Coupon is expired or not active yet');
                }

                $totalUsed = CouponUsage::where('coupon_id', $coupon->id)->count();
                if ($coupon->max_usage !== null && $totalUsed >= $coupon->max_usage) {
                    throw new \Exception('Coupon usage limit reached');
                }

                $clientUsed = CouponUsage::where('coupon_id', $coupon->id)
                    ->where('client_id', $client->id)
                    ->count();

                if ($coupon->max_usage_per_client !== null && $clientUsed >= $coupon->max_usage_per_client) {
                    throw new \Exception('This client has already used this coupon maximum times');
                }
            }

            // Calculate combined amounts
            $amounts = $this->calculateCombinedAmountsForServices($services);

            if ($coupon) {
                $amounts = $this->applyCouponToCombined($amounts, $coupon);
            }

            // ✅ Create order
            $order = Order::create([
                'order_id'            => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'client_id'           => $client->id,
                'marketing_staff_id'  => null,
                'base_price'          => $amounts['our_fee'],
                'discount_amount'     => $amounts['discount'] ?? 0,
                'final_amount'        => $amounts['final_total'],
                'coupon_id'           => optional($coupon)->id,
                'order_status'        => 'PENDING_PAYMENT',
                'created_by_admin'    => Auth::id(),
            ]);

            // ✅ Create order items (one per service)
            foreach ($services as $service) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'service_id' => $service->id,
                    'price'      => $service->our_fee,
                    'quantity'   => 1,
                    'total'      => $service->our_fee + $service->govt_fee + $service->platform_fee,
                ]);
            }

            // ✅ Save coupon usage record (only if coupon applied)
            if ($coupon) {
                CouponUsage::create([
                    'coupon_id'       => $coupon->id,
                    'order_id'        => $order->id,
                    'client_id'       => $client->id,
                    'service_id'      => null, // multiple services
                    'discount_amount' => $amounts['discount'] ?? 0,
                    'used_at'         => now(),
                ]);
            }

            DB::commit();

            session(['created_order_id' => $order->id]);

            return redirect()->route('admin.orders.create', ['step' => 5]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
        }
    }

    /* =====================================================
       PAYMENT - ADMIN VERSION (Auto-approved with invoice generation)
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
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $order  = Order::findOrFail(session('created_order_id'));
            $client = Client::findOrFail($order->client_id);

            if ($request->payment_method === 'PAY_LATER' && !$client->is_credit_allowed) {
                DB::rollBack();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
                    'received_by'        => Auth::id(),
                    'status'             => 'approved',
                    'approved_by'        => Auth::id(),
                    'approved_at'        => now(),
                ]);

                $order->update([
                    'order_status'   => 'PAID',
                    'payment_status' => 'Approved',
                    'payment_method' => 'PAY_LATER',
                ]);

                OrderStatusLog::create([
                    'order_id'   => $order->id,
                    'status'     => 'PAID',
                    'changed_by' => Auth::id(),
                    'remarks'    => 'Pay Later selected (Admin created - Auto approved)',
                ]);

                $this->generateInvoiceAndTasks($order, Auth::id());

                DB::commit();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                return redirect()->route('admin.orders.create', ['step' => 6]);
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
                'received_by'        => Auth::id(),
                'status'             => 'approved',
                'approved_by'        => Auth::id(),
                'approved_at'        => now(),
            ]);

            $order->update([
                'order_status'   => 'PAID',
                'payment_status' => 'Approved',
                'payment_method' => $request->payment_method,
            ]);

            OrderStatusLog::create([
                'order_id'   => $order->id,
                'status'     => 'PAID',
                'changed_by' => Auth::id(),
                'remarks'    => 'Payment submitted and auto-approved by admin',
            ]);

            $this->generateInvoiceAndTasks($order, Auth::id());

            DB::commit();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->route('admin.orders.create', ['step' => 6]);

        } catch (\Throwable $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Log::error('Admin payment failed: ' . $e->getMessage());

            return back()->withErrors('Payment failed: ' . $e->getMessage());
        }
    }

    /* =====================================================
       AUTO GENERATE INVOICE AND TASKS (Admin version)
       ✅ Works with multiple order items (tasks per service)
    ====================================================== */
    private function generateInvoiceAndTasks(Order $order, $staffId)
    {
        try {
            // Generate invoice if not yet generated
            if (!$order->invoice_generated) {
                $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . $order->id;

                $order->load('client', 'items.service', 'coupon');

                $subtotal = (float) ($order->base_price ?? 0);
                $discount = (float) ($order->discount_amount ?? 0);

                if ($discount > $subtotal) {
                    $discount = $subtotal;
                }

                $taxableAmount = round($subtotal - $discount, 2);

                $gstRate  = (float) config('app.gst_percentage', 18);
                $cgstRate = $gstRate / 2;
                $sgstRate = $gstRate / 2;

                $cgst = round(($taxableAmount * $cgstRate) / 100, 2);
                $sgst = round(($taxableAmount * $sgstRate) / 100, 2);

                $grandTotal = round($taxableAmount + $cgst + $sgst, 2);

                $latestPayment = Payment::where('order_id', $order->id)->latest()->first();

                $pdf = Pdf::loadView('admin.invoices.invoice-pdf', [
                    'order'         => $order,
                    'invoiceNumber' => $invoiceNumber,
                    'subtotal'      => $subtotal,
                    'discount'      => $discount,
                    'taxableAmount' => $taxableAmount,
                    'gstRate'       => $gstRate,
                    'cgstRate'      => $cgstRate,
                    'sgstRate'      => $sgstRate,
                    'cgst'          => $cgst,
                    'sgst'          => $sgst,
                    'grandTotal'    => $grandTotal,
                    'latestPayment' => $latestPayment,
                ]);

                $relativePath = 'storage/invoices/' . $invoiceNumber . '.pdf';
                $fullPath = public_path($relativePath);

                $dir = dirname($fullPath);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                $pdf->save($fullPath);

                $order->update([
                    'invoice_generated' => 1,
                    'invoice_path'      => $relativePath,
                ]);

                OrderStatusLog::create([
                    'order_id'   => $order->id,
                    'status'     => 'INVOICE_GENERATED',
                    'changed_by' => $staffId,
                    'remarks'    => 'Invoice ' . $invoiceNumber . ' generated',
                ]);
            }

            // Prevent duplicate tasks
            if (Task::where('order_id', $order->id)->exists()) {
                return;
            }

            $orderItems = $order->items;
            if ($orderItems->isEmpty()) {
                throw new \Exception('No order items found');
            }

            foreach ($orderItems as $item) {
                $service = $item->service;

                $workProcess = WorkProcess::where('service_id', $item->service_id)
                    ->where('status', 'active')
                    ->first();

                if (!$workProcess) {
                    throw new \Exception('No active work process found for service: ' . $service->service_name);
                }

                // Associate work process with order (first one, but okay)
                $order->update([
                    'work_process_id' => $workProcess->id,
                ]);

                $steps = WorkProcessStep::where('work_process_id', $workProcess->id)
                    ->orderBy('step_order')
                    ->get();

                foreach ($steps as $step) {
                    Task::create([
                        'order_id'             => $order->id,
                        'service_id'           => $item->service_id,
                        'work_process_id'      => $workProcess->id,
                        'work_process_step_id' => $step->id,
                        'task_name'            => $service->service_name . ' – ' . $step->step_name,
                        'step_name'            => $step->step_name,
                        'status'               => 'unassigned',
                    ]);
                }
            }

            OrderStatusLog::create([
                'order_id'   => $order->id,
                'status'     => 'APPROVED',
                'changed_by' => $staffId,
                'remarks'    => 'Payment auto-approved, invoice generated & tasks created (Admin)',
            ]);

        } catch (\Exception $e) {
            Log::error('Admin invoice/task generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /* =====================================================
       DELETE/CANCEL ORDER - ADMIN VERSION
    ====================================================== */
    public function destroy($id)
    {
        $order = Order::where('id', $id)
            ->where('order_status', 'PENDING_PAYMENT')
            ->firstOrFail();

        if (!$order->created_by_admin) {
            return response()->json([
                'success' => false,
                'message' => 'You can only cancel orders created by admin.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            Payment::where('order_id', $order->id)->delete();
            OrderItem::where('order_id', $order->id)->delete();
            OrderStatusLog::where('order_id', $order->id)->delete();
            Task::where('order_id', $order->id)->delete();
            $order->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin order deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order. Please try again.'
            ], 500);
        }
    }

    /* =====================================================
       INVOICE (Immediate for admin)
    ====================================================== */
    public function invoice(Order $order)
    {
        if (!$order->created_by_admin) {
            abort(403, 'Only admin-created orders can generate invoice directly.');
        }

        $order->load(['client', 'items.service', 'payments']);

        if (!$order->invoice_generated) {
            $this->generateInvoiceAndTasks($order, Auth::id());
            $order->refresh();
        }

        return view('admin.orders.invoice', compact('order'));
    }

    /* =====================================================
       DOWNLOAD INVOICE
    ====================================================== */
    public function downloadInvoice($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->invoice_path && file_exists(public_path($order->invoice_path))) {
            return response()->download(public_path($order->invoice_path), 'invoice_'.$order->id.'.pdf');
        }

        abort(404, 'Invoice not found');
    }

    /* =====================================================
       HELPERS
    ====================================================== */

    /**
     * Calculate combined amounts for multiple services
     */
    private function calculateCombinedAmountsForServices($services)
    {
        $totalOurFee = 0;
        $totalGovtFee = 0;
        $totalPlatformFee = 0;

        foreach ($services as $service) {
            $totalOurFee += (float) ($service->our_fee ?? 0);
            $totalGovtFee += (float) ($service->govt_fee ?? 0);
            $totalPlatformFee += (float) ($service->platform_fee ?? 0);
        }

        $subtotal = $totalOurFee + $totalGovtFee + $totalPlatformFee;
        $gstRate = (float) config('app.gst_percentage', 18);
        $cgst = round(($subtotal * ($gstRate / 2)) / 100, 2);
        $sgst = round(($subtotal * ($gstRate / 2)) / 100, 2);
        $tax = $cgst + $sgst;
        $total = round($subtotal + $tax, 2);

        return [
            'our_fee'       => round($totalOurFee, 2),
            'govt_fee'      => round($totalGovtFee, 2),
            'platform_fee'  => round($totalPlatformFee, 2),
            'subtotal'      => round($subtotal, 2),
            'gst_rate'      => $gstRate,
            'cgst_rate'     => $gstRate / 2,
            'sgst_rate'     => $gstRate / 2,
            'cgst'          => $cgst,
            'sgst'          => $sgst,
            'tax'           => $tax,
            'total'         => $total,
            'final_total'   => $total,
        ];
    }

    /**
     * Apply coupon to combined amounts
     */
    private function applyCouponToCombined($amounts, $coupon)
    {
        $discount = ($coupon->type === 'percentage')
            ? ($amounts['total'] * $coupon->value / 100)
            : $coupon->value;

        if ($discount > $amounts['total']) {
            $discount = $amounts['total'];
        }

        $amounts['discount'] = $discount;
        $amounts['final_total'] = $amounts['total'] - $discount;

        return $amounts;
    }

    /**
     * Calculate amounts for a single service (kept for reference, not used in new flow)
     */
    private function calculateServiceAmounts(Service $service)
    {
        $ourFee      = (float) ($service->our_fee ?? 0);
        $govtFee     = (float) ($service->govt_fee ?? 0);
        $platformFee = (float) ($service->platform_fee ?? 0);

        $subtotal = $ourFee + $govtFee + $platformFee;
        $gstRate  = (float) config('app.gst_percentage', 18);
        $cgst = round(($subtotal * ($gstRate / 2)) / 100, 2);
        $sgst = round(($subtotal * ($gstRate / 2)) / 100, 2);
        $tax   = round($cgst + $sgst, 2);
        $total = round($subtotal + $tax, 2);

        return [
            'our_fee'       => round($ourFee, 2),
            'govt_fee'      => round($govtFee, 2),
            'platform_fee'  => round($platformFee, 2),
            'subtotal'      => round($subtotal, 2),
            'gst_rate'      => $gstRate,
            'cgst_rate'     => $gstRate / 2,
            'sgst_rate'     => $gstRate / 2,
            'cgst'          => $cgst,
            'sgst'          => $sgst,
            'tax'           => $tax,
            'total'         => $total,
            'final_total'   => $total,
        ];
    }

    /**
     * Apply coupon to single service amounts (kept for reference)
     */
    private function applyCoupon($amounts, $coupon)
    {
        $discount = ($coupon->type === 'percentage')
            ? ($amounts['total'] * $coupon->value / 100)
            : $coupon->value;

        if ($discount > $amounts['total']) {
            $discount = $amounts['total'];
        }

        $amounts['discount'] = $discount;
        $amounts['final_total'] = $amounts['total'] - $discount;

        return $amounts;
    }

    /**
     * Clear all session variables used in order creation
     */
    private function clearSession()
    {
        session()->forget([
            'selected_client_id',
            'selected_service_ids',   // ✅ changed from selected_service_id
            'selected_coupon_id',
            'created_order_id',
        ]);
    }
}