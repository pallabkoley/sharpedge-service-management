<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderStatusLog;
use App\Models\Staff;
use App\Models\Task;
use App\Models\Service;
use App\Models\WorkProcess;
use App\Models\WorkProcessStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminOrderApprovalController extends Controller
{
    /**
     * List payment submissions (including due payments)
     */
public function index(Request $request)
{
    // 🔥 MAIN QUERY
    $query = Order::with([
        'client',
        'marketingStaff',
        'payments' => fn ($q) => $q->latest()
    ])->latest();

    // 🔍 FILTERS
    if ($request->filled('status')) {

        // 🟡 Pending (submitted)
        if ($request->status === 'submitted') {
            $query->whereHas('payments', fn ($q) =>
                $q->where('status', 'submitted')
            );
        }

        // 🟢 Approved
        if ($request->status === 'approved') {
            $query->whereHas('payments', fn ($q) =>
                $q->where('status', 'approved')
            );
        }

        // 🔴 Rejected
        if ($request->status === 'rejected') {
            $query->whereHas('payments', fn ($q) =>
                $q->where('status', 'rejected')
            );
        }

        // 🔵 NEW: Approved but Invoice NOT Generated
        if ($request->status === 'approved_invoice_pending') {
            $query->whereHas('payments', function ($q) {
                $q->where('status', 'approved');
            })
            ->where(function ($q) {
                $q->where('invoice_generated', false)
                  ->orWhereNull('invoice_generated');
            });
        }

        // ⚫ No payment
        if ($request->status === 'no_payment') {
            $query->whereDoesntHave('payments');
        }
    }

    // 📦 TABLE DATA
    $orders = $query->get();

    // =========================
    // 📊 DASHBOARD METRICS
    // =========================

    // Pending payments
    $pendingCount = Payment::where('status', 'submitted')->count();

    // Approved revenue
    $totalAmount = Payment::where('status', 'approved')->sum('amount');

    // Unique clients
    $uniqueClientsCount = Order::distinct('client_id')->count('client_id');

    // Total payments
    $allPayments = Payment::count();

    // Total orders
    $totalOrders = Order::count();

    // Active services
    $services = Service::where('status', 'active')
        ->orderBy('service_name')
        ->get();

    return view('admin.orders.pending', compact(
        'orders',
        'pendingCount',
        'totalAmount',
        'uniqueClientsCount',
        'services',
        'allPayments',
        'totalOrders'
    ));
}

    /**
     * Resolve staff id
     */
    private function getCurrentStaffId()
    {
        $staff = Staff::where('user_id', Auth::id())->first();
        if ($staff) return $staff->id;

        if (Auth::user()->email) {
            $staff = Staff::where('email', Auth::user()->email)->first();
            if ($staff) return $staff->id;
        }

        return Staff::first()?->id;
    }

    /**
     * View payment proof/document
     */
    public function viewPaymentProof(Payment $payment)
    {
        if (!$payment->payment_proof_path) {
            return response()->json([
                'success' => false,
                'message' => 'No payment proof found.'
            ], 404);
        }

        $path = storage_path('app/public/' . $payment->payment_proof_path);
        
        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Payment proof file not found.'
            ], 404);
        }

        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    /**
     * Show payment details with proof
     */
    public function showPayment(Payment $payment)
    {
        $payment->load(['order.client', 'order.marketingStaff', 'receivedByStaff']);
        
        return response()->json([
            'success' => true,
            'payment' => $payment,
            'proof_url' => $payment->payment_proof_path ? route('admin.payments.proof', $payment) : null
        ]);
    }

    /**
     * APPROVE = mark payment as approved, update order status based on total paid,
     * create tasks only when fully paid, regenerate invoice if previously rejected.
     */
    public function approve(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                $order = Order::where('id', $order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // get the latest SUBMITTED payment only
                $payment = $order->payments()
                    ->where('status', 'submitted')
                    ->latest()
                    ->first();

                if (!$payment) {
                    throw new \Exception('No submitted payment found.');
                }

                // Idempotency
                if ($payment->status === 'approved') {
                    return;
                }

                $staffId = $this->getCurrentStaffId();

                /* ================= APPROVE PAYMENT ================= */
                $payment->update([
                    'status'           => 'approved',
                    'approved_by'      => $staffId,
                    'approved_at'      => now(),
                    'rejected_at'      => null,
                    'rejection_reason' => null,
                ]);

                /* ================= DYNAMIC STATUS CALCULATION (IMPROVED) ================= */
                $paid = $order->payments()->where('status', 'approved')->sum('amount');
                $total = (float) $order->final_amount;
                $due = max(0, $total - $paid);
                $orderStatus = $due == 0
                    ? 'PAID'
                    : ($paid > 0 ? 'PARTIAL_PAYMENT' : 'PENDING_PAYMENT');

                $order->update([
                    'order_status'   => $orderStatus,
                    'verified_by'    => $staffId,
                    'verified_at'    => now(),
                ]);

                /* ================= TASK CREATION (only on full payment) ================= */
                if ($paid >= $total && !Task::where('order_id', $order->id)->exists()) {
                    foreach ($order->items as $item) {
                        $workProcess = WorkProcess::where('service_id', $item->service_id)
                            ->where('status', 'active')
                            ->first();

                        if (!$workProcess) {
                            throw new \Exception('No active work process found.');
                        }

                        $order->update(['work_process_id' => $workProcess->id]);

                        $steps = WorkProcessStep::where('work_process_id', $workProcess->id)
                            ->orderBy('step_order')
                            ->get();

                        foreach ($steps as $step) {
                            Task::create([
                                'order_id'             => $order->id,
                                'service_id'           => $item->service_id,
                                'work_process_id'      => $workProcess->id,
                                'work_process_step_id' => $step->id,
                                'task_name'            => $item->service->service_name . ' – ' . $step->step_name,
                                'step_name'            => $step->step_name,
                                'status'               => 'unassigned',
                            ]);
                        }
                    }
                }

                /* ================= REGENERATE INVOICE IF ORDER WAS REJECTED ================= */
                $wasRejected = OrderStatusLog::where('order_id', $order->id)
                    ->where('status', 'REJECTED')
                    ->exists();

                if ($wasRejected) {
                    \Log::info('Regenerating invoice for previously rejected order', [
                        'order_id' => $order->id,
                        'old_path' => $order->invoice_path
                    ]);
                    
                    if ($paid >= $total) {
                        $this->generateFinalInvoice($order);
                    } else {
                        $this->regenerateInvoiceAfterApproval($order, $payment);
                    }
                    
                    $order->refresh();
                    
                    \Log::info('Invoice regenerated, new path: ' . $order->invoice_path);
                }

                /* ================= STATUS LOG ================= */
                OrderStatusLog::create([
                    'order_id'   => $order->id,
                    'status'     => $orderStatus,
                    'changed_by' => $staffId,
                    'remarks'    => $paid >= $total
                        ? "Payment of ₹{$payment->amount} approved. Order fully paid."
                        : "Payment of ₹{$payment->amount} approved. Balance: ₹" . ($total - $paid),
                ]);
            });

            return back()->with('success', 'Payment approved successfully.');

        } catch (\Exception $e) {
            \Log::error('Payment approval error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Regenerate invoice after approval (especially after rejection)
     * ✅ Removed final_amount update – invoice now reads order.total
     */
    private function regenerateInvoiceAfterApproval(Order $order, Payment $payment)
    {
        try {
            $order->load(['client', 'items.service']);
            
            // Use order's own total values for consistency
            $serviceFee = (float) $order->base_price;
            $discount = (float) $order->discount_amount;
            $govtFee = (float) $order->final_govt_fee;
            
            $taxableAmount = max(0, $serviceFee - $discount);
            $gstRate = (float) config('app.gst_percentage', 18);
            
            $cgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
            $sgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
            
            $actualTotal = $taxableAmount + $cgst + $sgst + $govtFee;
            $roundedTotal = round($actualTotal);
            $roundOff = round($roundedTotal - $actualTotal, 2);
            
            $baseInvoiceNumber = $order->invoice_number;
            $baseInvoiceNumber = preg_replace('/-REJECTED.*$/', '', $baseInvoiceNumber);
            
            if (!str_contains($baseInvoiceNumber, '-APPROVED')) {
                $invoiceNumber = $baseInvoiceNumber . '-APPROVED';
            } else {
                $invoiceNumber = $baseInvoiceNumber;
            }
            
            $safeInvoiceNumber = str_replace(['/', '\\'], '-', $invoiceNumber);
            $fileName = $safeInvoiceNumber . '.pdf';
            $path = 'invoices/' . $fileName;
            
            $fullPath = storage_path('app/public/' . $path);
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $pdf = Pdf::loadView('admin.invoices.invoice-pdf', [
                'order'           => $order,
                'invoiceNumber'   => $invoiceNumber,
                'serviceFee'      => $serviceFee,
                'govtFee'         => $govtFee,
                'discount'        => $discount,
                'taxableAmount'   => $taxableAmount,
                'cgst'            => $cgst,
                'sgst'            => $sgst,
                'gstRate'         => $gstRate,
                'grandTotal'      => $roundedTotal,
                'actualTotal'     => $actualTotal,
                'roundOff'        => $roundOff,
                'isRevised'       => true,
                'isRejected'      => false, 
                'latestPayment'   => $payment,
            ]);
            
            $pdf->save($fullPath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception('Failed to create invoice file at: ' . $fullPath);
            }
            
            $order->update([
                'invoice_path' => $path,
                'invoice_type' => 'REVISED',
                'round_off' => $roundOff,
                // ✅ final_amount NOT updated – order’s own total remains unchanged
            ]);
            
            \Log::info('Invoice regenerated after approval for order: ' . $order->id, [
                'old_path' => $order->getOriginal('invoice_path'),
                'new_path' => $path,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Invoice regeneration failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Confirm govt fee, generate invoice (only if balance = 0 or CREDIT_PENDING)
     * ✅ Removed final_amount update – initial invoice now reads order.total
     */
    public function confirmGovtFeeAndGenerateInvoice(Request $request, Order $order)
    {
        if ($order->order_status === 'REJECTED') {
            return back()->with('error', 'Invoice cannot be generated. Payment is rejected.');
        }

        if ($order->invoice_generated) {
            return back()->with('error', 'Invoice already generated.');
        }

        // Guard: Invoice only when balance is settled (except CREDIT)
        $paid = $order->payments()->where('status', 'approved')->sum('amount');
        if (($order->final_amount - $paid) > 0 && $order->order_status !== 'CREDIT_PENDING') {
            return back()->with('error', 'Invoice cannot be generated. Balance payment pending.');
        }

        $request->validate([
            'final_govt_fee' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($order, $request) {
                $staffId = $this->getCurrentStaffId();

                $order->load([
                    'client',
                    'items.service',
                    'payments' => fn ($q) => $q->latest()
                ]);

                /* ================= AMOUNT CALCULATION ================= */
                $govtFee = $request->filled('final_govt_fee')
                    ? (float) $request->final_govt_fee
                    : (float) $order->estimated_govt_fee;

                $serviceFee = (float) $order->base_price;
                $discount   = (float) $order->discount_amount;

                $taxableAmount = max(0, $serviceFee - $discount);
                $gstRate = (float) config('app.gst_percentage', 18);

                $cgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
                $sgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);

                $grandTotal = round($taxableAmount + $cgst + $sgst + $govtFee, 2);

                /* ================= FY INVOICE NUMBER ================= */
                $today   = now();
                $fyStart = $today->month >= 4 ? $today->year : $today->year - 1;
                $fyEnd   = $fyStart + 1;
                $fy      = $fyStart . '/' . $fyEnd;

                $lastInvoice = Order::where('invoice_number', 'like', "SRP-$fy/%")
                    ->lockForUpdate()
                    ->orderByDesc('invoice_number')
                    ->first();

                $next = 1;
                if ($lastInvoice && preg_match('/\/(\d+)$/', $lastInvoice->invoice_number, $m)) {
                    $next = ((int) $m[1]) + 1;
                }

                $invoiceNumber = 'SRP-' . $fy . '/' . str_pad($next, 3, '0', STR_PAD_LEFT);
                $fileName = str_replace('/', '-', $invoiceNumber) . '.pdf';
                $path = 'invoices/' . $fileName;

                /* ================= DYNAMIC BALANCE & STATUS ================= */
                $paid = $order->payments()->where('status', 'approved')->sum('amount');
                $balance = max(0, $grandTotal - $paid);
                $orderStatus = $order->order_status === 'REJECTED'
                    ? 'REJECTED'
                    : ($balance > 0 ? 'PARTIAL_PAYMENT' : 'PAID');

                $order->update([
                    'invoice_number'    => $invoiceNumber,
                    'invoice_generated' => true,
                    'invoice_date'      => now(),
                    'final_govt_fee'    => $govtFee,
                    // ✅ final_amount NOT updated – order’s total is already set (from creation / previous steps)
                    'order_status'      => $orderStatus,
                    'invoice_path'      => $path,
                ]);

                /* ================= GENERATE PDF ================= */
                $latestPayment = $order->payments()->latest()->first();
                Pdf::loadView('admin.invoices.invoice-pdf', [
                    'order'         => $order,
                    'invoiceNumber' => $invoiceNumber,
                    'serviceFee'    => $serviceFee,
                    'govtFee'       => $govtFee,
                    'discount'      => $discount,
                    'taxableAmount' => $taxableAmount,
                    'cgst'          => $cgst,
                    'sgst'          => $sgst,
                    'gstRate'       => $gstRate,
                    'grandTotal'    => $grandTotal,
                    'isRevised'     => false,
                    'latestPayment' => $latestPayment,
                ])->save(storage_path('app/public/' . $path));

                /* ================= STATUS LOG ================= */
                OrderStatusLog::create([
                    'order_id'   => $order->id,
                    'status'     => 'INVOICE_GENERATED',
                    'changed_by' => $staffId,
                    'remarks'    => 'Original invoice generated',
                ]);
            });

            return back()->with('success', 'Invoice generated successfully.');

        } catch (\Exception $e) {
            \Log::error('Invoice generation failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Revise invoice (govt fee only) – recalculate balance & status
     * ✅ Removed final_amount update – order total unchanged
     */
    public function reviseInvoice(Request $request, Order $order)
    {
        if ($order->isInvoiceLocked()) {
            return back()->with('error', 'Paid invoice cannot be revised.');
        }

        $request->validate([
            'final_govt_fee'   => 'required|numeric|min:0',
            'revision_reason'  => 'required|string|min:5',
        ]);

        try {
            DB::transaction(function () use ($order, $request) {
                $order = Order::where('id', $order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($order->order_status === 'REJECTED') {
                    throw new \Exception('Rejected order invoice cannot be revised.');
                }

                $staffId = $this->getCurrentStaffId();

                /* ================= CALCULATIONS ================= */
                $serviceFee = (float) $order->base_price;
                $discount   = (float) $order->discount_amount;
                $govtFee    = (float) $request->final_govt_fee;

                $taxableAmount = max(0, $serviceFee - $discount);
                $gstRate = (float) config('app.gst_percentage', 18);

                $cgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
                $sgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);

                $grandTotal = round($taxableAmount + $cgst + $sgst + $govtFee, 2);

                /* ================= DYNAMIC BALANCE & STATUS ================= */
                $paid = $order->payments()->where('status', 'approved')->sum('amount');
                $balance = max(0, $grandTotal - $paid);
                $newOrderStatus = $balance > 0 ? 'PARTIAL_PAYMENT' : 'PAID';

                $order->update([
                    'final_govt_fee' => $govtFee,
                    // ✅ final_amount NOT updated – order total unchanged
                    'order_status'   => $newOrderStatus,
                ]);

                /* ================= REGENERATE PDF ================= */
                $latestPayment = $order->payments()->latest()->first();
                Pdf::loadView('admin.invoices.invoice-pdf', [
                    'order'         => $order,
                    'invoiceNumber' => $order->invoice_number,
                    'serviceFee'    => $serviceFee,
                    'govtFee'       => $govtFee,
                    'discount'      => $discount,
                    'taxableAmount' => $taxableAmount,
                    'cgst'          => $cgst,
                    'sgst'          => $sgst,
                    'gstRate'       => $gstRate,
                    'grandTotal'    => $grandTotal,
                    'isRevised'     => true,
                    'latestPayment' => $latestPayment,
                ])->save(storage_path('app/public/' . $order->invoice_path));

                /* ================= LOG ================= */
                OrderStatusLog::create([
                    'order_id'   => $order->id,
                    'status'     => 'INVOICE_REVISED',
                    'changed_by' => $staffId,
                    'remarks'    => $request->revision_reason,
                ]);
            });

            return back()->with('success', 'Invoice revised successfully.');

        } catch (\Exception $e) {
            \Log::error('Invoice revision error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * REJECT = mark payment rejected, update order status based on remaining approved payments,
     * delete tasks if no approved payments remain.
     * ✅ Fixed: order_status now reflects real payment state, not hard REJECTED
     */
    public function reject(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                $order = Order::where('id', $order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // get the latest SUBMITTED payment only
                $payment = $order->payments()
                    ->where('status', 'submitted')
                    ->latest()
                    ->first();

                if (!$payment) {
                    throw new \Exception('No submitted payment found.');
                }

                if ($payment->status === 'rejected') {
                    return;
                }

                $staffId = $this->getCurrentStaffId();
                $rejectionReason = request('rejection_reason', 'Payment rejected by admin');

                /* ================= REJECT PAYMENT ================= */
                $payment->update([
                    'status'           => 'rejected',
                    'approved_by'      => null,
                    'approved_at'      => null,
                    'rejected_at'      => now(),
                    'rejection_reason' => $rejectionReason,
                ]);

                /* ================= RECALCULATE PAID AFTER REJECTION ================= */
                $paid = $order->payments()->where('status', 'approved')->sum('amount');

                // ✅ New order status based on remaining payments
                $orderStatus = $paid == 0 ? 'PENDING_PAYMENT' : 'PARTIAL_PAYMENT';

                $order->update([
                    'order_status'   => $orderStatus,
                ]);

                /* ================= CLEAN TASKS IF NO PAYMENT LEFT ================= */
                if ($paid <= 0) {
                    Task::where('order_id', $order->id)->delete();
                    $order->update(['work_process_id' => null]);
                }

                /* ================= GENERATE REJECTED INVOICE ================= */
                if ($order->invoice_generated && $order->invoice_path) {
                    $baseInvoiceNumber = $order->invoice_number;
                    if (!str_contains($baseInvoiceNumber, '-REJECTED')) {
                        $rejectedInvoiceNumber = $baseInvoiceNumber . '-REJECTED';
                        
                        $safeInvoiceNumber = str_replace(['/', '\\'], '-', $rejectedInvoiceNumber);
                        $fileName = $safeInvoiceNumber . '.pdf';
                        $rejectedPath = 'invoices/' . $fileName;
                        
                        $order->update(['invoice_path' => $rejectedPath]);
                        
                        Pdf::loadView('admin.invoices.invoice-pdf', [
                            'order'         => $order,
                            'invoiceNumber' => $rejectedInvoiceNumber,
                            'isRejected'    => true,
                            'latestPayment' => $payment,
                        ])->save(storage_path('app/public/' . $rejectedPath));
                    }
                }

                /* ================= STATUS LOG ================= */
                OrderStatusLog::create([
                    'order_id'   => $order->id,
                    'status'     => $orderStatus,
                    'changed_by' => $staffId,
                    'remarks'    => 'Payment rejected: ' . $rejectionReason,
                ]);
            });

            return back()->with('success', 'Payment rejected successfully.');

        } catch (\Exception $e) {
            \Log::error('Reject payment error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function viewInvoice(Order $order)
    {
        if (!$order->invoice_path) {
            \Log::warning('Invoice view attempted but no invoice_path', [
                'order_id' => $order->id,
                'order_status' => $order->order_status
            ]);
            abort(404, 'Invoice not generated.');
        }

       $filePath = public_path('storage/' . $order->invoice_path);

        \Log::info('Attempting to view invoice', [
            'order_id' => $order->id,
            'invoice_path' => $order->invoice_path,
            'full_path' => $filePath,
            'file_exists' => file_exists($filePath),
            'order_status' => $order->order_status
        ]);

        if (!file_exists($filePath)) {
            \Log::error('Invoice file missing', [
                'order_id' => $order->id,
                'invoice_path' => $order->invoice_path,
                'full_path' => $filePath,
                'directory_exists' => file_exists(dirname($filePath)),
                'directory_permissions' => substr(sprintf('%o', fileperms(dirname($filePath))), -4)
            ]);

            abort(404, 'Invoice file not found.');
        }

        return response()->file($filePath, [
            'Content-Type'  => 'application/pdf',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }

    /**
     * Show order
     */
    public function showOrder(Order $order)
    {
        $order->load([
            'client',
            'marketingStaff',
            'payments' => function($q) {
                $q->with(['receivedByStaff'])->latest(); 
            },
            'items.service',
        ]);
    
        return response()->json([
            'success' => true,
            'order'   => $order,
        ]);
    }

    /**
     * Status history
     */
    public function statusHistory(Order $order)
    {
        $logs = OrderStatusLog::with('changedBy')
            ->where('order_id', $order->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $logs,
        ]);
    }
    
    /**
     * Add add-on service to an existing order
     */
    public function addAddonService(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'service_id' => 'required|exists:services,id',
                'quantity' => 'required|integer|min:1',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'remarks' => 'nullable|string|max:500',
            ]);

            $order = null;

            DB::transaction(function () use ($request, &$order) {
                $order = Order::where('id', $request->order_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($order->order_status === 'REJECTED') {
                    throw new \Exception('Cannot add add-on to a rejected order.');
                }

                $service = Service::findOrFail($request->service_id);
                $staffId = $this->getCurrentStaffId();

                $quantity = (int) $request->quantity;
                $discountPercent = (float) ($request->discount_percent ?? 0);

                $basePrice = (float) $service->our_fee;
                $subtotal = $basePrice * $quantity;
                $discountAmount = ($subtotal * $discountPercent) / 100;
                $taxableAmount = $subtotal - $discountAmount;

                $gstRate = $service->is_taxable ? (float) $service->tax_percentage : 0;
                $cgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
                $sgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);

                $govtFee = (float) ($service->govt_fee ?? 0) * $quantity;
                $itemTotal = round($subtotal + $cgst + $sgst + $govtFee, 2);

                $orderItem = \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'price' => $basePrice,
                    'quantity' => $quantity,
                    'total' => $itemTotal,
                    'item_type' => 'ADDON',
                    'invoice_status' => 'PENDING_INVOICE',
                    'is_addon' => true,
                    'parent_item_id' => null,
                    'added_by' => $staffId,
                    'added_at' => now(),
                    'remarks' => $request->remarks,
                ]);

                $newFinalAmount = (float) $order->final_amount + $itemTotal;
                $newBasePrice = (float) $order->base_price + $subtotal;
                $totalGovtFee = (float) $order->final_govt_fee + $govtFee;

                /* ================= DYNAMIC BALANCE & STATUS ================= */
                // 🔥 FIXED: sum only approved payments
                $paid = $order->payments()->where('status', 'approved')->sum('amount');
                $newBalance = max(0, $newFinalAmount - $paid);

                $order->update([
                    'base_price'     => $newBasePrice,
                    'final_amount'   => $newFinalAmount,
                    'final_govt_fee' => $totalGovtFee,
                    'order_status'   => $newBalance > 0 ? 'PARTIAL_PAYMENT' : 'PAID',
                ]);

                $this->generateRevisedInvoiceWithAddon($order, $orderItem, $staffId, [
                    'subtotal' => $subtotal,
                    'taxable_amount' => $taxableAmount,
                    'cgst' => $cgst,
                    'sgst' => $sgst,
                    'govt_fee' => $govtFee,
                    'discount_amount' => $discountAmount,
                    'discount_percent' => $discountPercent,
                ]);

                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => 'ADDON_ADDED',
                    'changed_by' => $staffId,
                    'remarks' => "Add-on service added: {$service->service_name} (Qty: {$quantity}, Amount: ₹{$itemTotal})",
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Add-on service added successfully. Invoice has been updated.',
                'invoice_url' => $order ? route('admin.orders.invoice.view', $order) : null,
            ]);

        } catch (\Exception $e) {
            \Log::error('Add-on service error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate revised invoice with add-on
     * ✅ Removed final_amount update – order total already updated
     */
    private function generateRevisedInvoiceWithAddon($order, $newAddonItem, $staffId, $addonDetails = [])
    {
        $order->load(['client', 'items.service', 'payments' => function($q) {
            $q->latest();
        }]);
        
        $latestPayment = $order->payments->first();
        
        $serviceFee = (float) $order->base_price;
        $discount = (float) $order->discount_amount;
        
        $originalTaxable = max(0, $serviceFee - $discount - ($addonDetails['subtotal'] ?? 0));
        $taxableAmount = $originalTaxable + ($addonDetails['taxable_amount'] ?? 0);
        
        $gstRate = (float) config('app.gst_percentage', 18);
        
        $cgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
        $sgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
        
        $totalGovtFee = (float) $order->final_govt_fee;
        
        $actualTotal = $taxableAmount + $cgst + $sgst + $totalGovtFee;
        $roundedTotal = round($actualTotal);
        $roundOff = round($roundedTotal - $actualTotal, 2);
        
        /* ================= DYNAMIC BALANCE (for PDF only) ================= */
        $paid = $order->payments()->where('status', 'approved')->sum('amount');
        $balance = max(0, $roundedTotal - $paid);
        
        $baseInvoiceNumber = $order->invoice_number;
        if (strpos($baseInvoiceNumber, '-R') !== false) {
            $revision = (int) substr($baseInvoiceNumber, strpos($baseInvoiceNumber, '-R') + 2);
            $revision++;
            $baseInvoiceNumber = substr($baseInvoiceNumber, 0, strpos($baseInvoiceNumber, '-R')) . "-R{$revision}";
        } else {
            $baseInvoiceNumber = $baseInvoiceNumber . '-R1';
        }
        
        $path = 'invoices/' . str_replace('/', '-', $baseInvoiceNumber) . '.pdf';
        
        $order->update([
            'invoice_path' => $path,
            'invoice_type' => 'REVISED',
            'revision_reason' => "Add-on service added: " . ($newAddonItem->service->service_name ?? 'Service'),
            'round_off' => $roundOff,
            // ✅ final_amount NOT updated – order total already updated in addAddonService
        ]);
        
        $pdf = Pdf::loadView('admin.invoices.invoice-pdf', [
            'order' => $order,
            'invoiceNumber' => $baseInvoiceNumber,
            'serviceFee' => $serviceFee,
            'govtFee' => $totalGovtFee,
            'discount' => $discount,
            'taxableAmount' => $taxableAmount,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'gstRate' => $gstRate,
            'actualTotal' => $actualTotal,
            'roundedTotal' => $roundedTotal,
            'roundOff' => $roundOff,
            'isRevised' => true,
            'addonItems' => $order->items->where('is_addon', true),
            'addonDetails' => $addonDetails,
            'latestPayment' => $latestPayment,
            'balance' => $balance,
        ]);
        
        $pdf->save(storage_path('app/public/' . $path));
    }
    
    /**
     * Show edit invoice modal data
     */
    public function editInvoice(Order $order)
    {
        if (!$order->invoice_generated) {
            return back()->with('error', 'Invoice not generated yet.');
        }

        $order->load(['client', 'items.service', 'payments']);
        
        foreach ($order->items as $item) {
            if (!isset($item->cgst)) {
                $gstRate = config('app.gst_percentage', 18) / 2;
                $taxableAmount = $item->taxable_amount ?? ($item->total - ($item->govt_fee ?? 0));
                $item->cgst = round(($taxableAmount * $gstRate) / 100, 2);
                $item->sgst = round(($taxableAmount * $gstRate) / 100, 2);
            }
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'items' => $order->items,
        ]);
    }

    /**
     * Update invoice with new government fees
     * ✅ Removed final_amount update – order total unchanged
     */
    public function updateInvoice(Request $request, Order $order)
    {
        if (!$order->invoice_generated) {
            return back()->with('error', 'Invoice not generated yet.');
        }
    
        $request->validate([
            'item_ids' => 'required|array',
            'govt_fees' => 'required|array',
            'govt_fees.*' => 'numeric|min:0',
            'revision_reason' => 'nullable|string|min:5',
        ]);
    
        try {
            DB::transaction(function () use ($order, $request) {
                $staffId = $this->getCurrentStaffId();
                $totalGovtFee = 0;
                
                foreach ($request->item_ids as $itemId) {
                    if (isset($request->govt_fees[$itemId])) {
                        $item = \App\Models\OrderItem::findOrFail($itemId);
                        $oldGovtFee = $item->govt_fee ?? 0;
                        $newGovtFee = (float) $request->govt_fees[$itemId];
                        
                        $item->govt_fee = $newGovtFee;
                        $item->save();
                        
                        $totalGovtFee += $newGovtFee;
                        
                        if ($oldGovtFee != $newGovtFee) {
                            \App\Models\OrderStatusLog::create([
                                'order_id' => $order->id,
                                'status' => 'ITEM_GOVT_FEE_UPDATED',
                                'changed_by' => $staffId,
                                'remarks' => "Item #{$itemId}: Govt fee changed from ₹{$oldGovtFee} to ₹{$newGovtFee}",
                            ]);
                        }
                    }
                }
                
                $serviceFee = (float) $order->base_price;
                $discount = (float) $order->discount_amount;
                $taxableAmount = max(0, $serviceFee - $discount);
                
                $gstRate = (float) config('app.gst_percentage', 18);
                $cgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
                $sgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
                
                $actualTotal = $taxableAmount + $cgst + $sgst + $totalGovtFee;
                $roundedTotal = round($actualTotal);
                $roundOff = round($roundedTotal - $actualTotal, 2);
                
                /* ================= DYNAMIC BALANCE & STATUS ================= */
                $paid = $order->payments()->where('status', 'approved')->sum('amount');
                $balance = max(0, $roundedTotal - $paid);
                $orderStatus = $balance > 0 ? 'PARTIAL_PAYMENT' : 'PAID';
                
                $baseInvoiceNumber = $order->invoice_number;
                if (strpos($baseInvoiceNumber, '-R') !== false) {
                    $revision = (int) substr($baseInvoiceNumber, strpos($baseInvoiceNumber, '-R') + 2);
                    $revision++;
                    $baseInvoiceNumber = substr($baseInvoiceNumber, 0, strpos($baseInvoiceNumber, '-R')) . "-R{$revision}";
                } else {
                    $baseInvoiceNumber = $baseInvoiceNumber . '-R1';
                }
                
                $path = 'invoices/' . str_replace('/', '-', $baseInvoiceNumber) . '.pdf';
                
                $order->update([
                    'final_govt_fee' => $totalGovtFee,
                    // ✅ final_amount NOT updated – order total unchanged
                    'round_off'      => $roundOff,
                    'order_status'   => $orderStatus,
                    'invoice_path'   => $path,
                    'invoice_type'   => 'REVISED',
                    'revision_reason'=> $request->revision_reason,
                ]);
                
                $latestPayment = $order->payments()->latest()->first();
                
                Pdf::loadView('admin.invoices.invoice-pdf', [
                    'order'         => $order,
                    'invoiceNumber' => $baseInvoiceNumber,
                    'serviceFee'    => $serviceFee,
                    'govtFee'       => $totalGovtFee,
                    'discount'      => $discount,
                    'taxableAmount' => $taxableAmount,
                    'cgst'          => $cgst,
                    'sgst'          => $sgst,
                    'gstRate'       => $gstRate,
                    'grandTotal'    => $roundedTotal,
                    'actualTotal'   => $actualTotal,
                    'roundOff'      => $roundOff,
                    'isRevised'     => true,
                    'latestPayment' => $latestPayment,
                    'editedItems'   => $request->item_ids,
                    'balance'       => $balance,
                ])->save(storage_path('app/public/' . $path));
                
                OrderStatusLog::create([
                    'order_id'   => $order->id,
                    'status'     => 'INVOICE_EDITED',
                    'changed_by' => $staffId,
                    'remarks'    => "Government fees updated. New total: ₹{$roundedTotal}. Reason: {$request->revision_reason}",
                ]);
            });
    
            return back()->with('success', 'Invoice updated successfully. Revised invoice generated.');
    
        } catch (\Exception $e) {
            \Log::error('Invoice edit error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }
    
    /**
     * Update payment details (for admin to modify payment info)
     */
    public function updatePayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'transaction_id' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);
        
        try {
            DB::transaction(function () use ($request, $order) {
                // ensure payment belongs to this order
                $payment = $order->payments()
                    ->where('id', $request->payment_id)
                    ->firstOrFail();
                
                if ($payment->status !== 'submitted') {
                    throw new \Exception('Only pending payments can be updated.');
                }
                
                $oldAmount = $payment->amount;
                
                $payment->update([
                    'amount' => $request->amount ?? $payment->amount,
                    'payment_date' => $request->payment_date ?? $payment->payment_date,
                    'transaction_id' => $request->transaction_id ?? $payment->transaction_id,
                    'remarks' => $request->remarks,
                ]);
                
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => 'PAYMENT_UPDATED',
                    'changed_by' => $this->getCurrentStaffId(),
                    'remarks' => "Payment updated: Amount changed from ₹{$oldAmount} to ₹{$payment->amount}",
                ]);
            });
            
            return back()->with('success', 'Payment details updated successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Payment update error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }
    
    /**
     * Generate final invoice after full payment
     * ✅ Removed final_amount update – order total already correct
     */
    private function generateFinalInvoice(Order $order)
    {
        try {
            if ($order->order_status === 'REJECTED') {
                \Log::error('Final invoice generation blocked for REJECTED order ID: ' . $order->id);
                return false;
            }

            $order->load([
                'client',
                'items.service',
                'payments' => function ($q) {
                    $q->where('status', 'approved')->latest();
                }
            ]);

            $latestPayment = $order->payments->first();

            if (!$latestPayment) {
                \Log::error('Final invoice blocked — no approved payment found for order ID: ' . $order->id);
                return false;
            }

            $serviceFee = (float) $order->base_price;
            $discount   = (float) $order->discount_amount;
            $govtFee    = (float) $order->final_govt_fee;

            $taxableAmount = max(0, $serviceFee - $discount);
            $gstRate = (float) config('app.gst_percentage', 18);

            $cgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);
            $sgst = round(($taxableAmount * ($gstRate / 2)) / 100, 2);

            $actualTotal  = $taxableAmount + $cgst + $sgst + $govtFee;
            $roundedTotal = round($actualTotal);
            $roundOff     = round($roundedTotal - $actualTotal, 2);

            $baseInvoiceNumber = $order->invoice_number;
            $baseInvoiceNumber = preg_replace('/-(R\d+|PAID|FINAL|APPROVED|REJECTED).*$/', '', $baseInvoiceNumber);
            $finalInvoiceNumber = $baseInvoiceNumber . '-FINAL';

            $safeInvoiceNumber = str_replace(['/', '\\'], '-', $finalInvoiceNumber);
            $fileName = $safeInvoiceNumber . '.pdf';
            $relativePath = 'invoices/' . $fileName;

            $fullPath = storage_path('app/public/' . $relativePath);
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $pdf = Pdf::loadView('admin.invoices.invoice-pdf', [
                'order'           => $order,
                'invoiceNumber'   => $finalInvoiceNumber,
                'serviceFee'      => $serviceFee,
                'govtFee'         => $govtFee,
                'discount'        => $discount,
                'taxableAmount'   => $taxableAmount,
                'cgst'            => $cgst,
                'sgst'            => $sgst,
                'gstRate'         => $gstRate,
                'actualTotal'     => $actualTotal,
                'roundedTotal'    => $roundedTotal,
                'grandTotal'      => $roundedTotal,
                'roundOff'        => $roundOff,
                'isFinal'         => true,
                'isPaid'          => true,
                'latestPayment'   => $latestPayment,
                'paymentHistory'  => $order->payments,
            ]);
            
            $pdf->save($fullPath);

            if (!file_exists($fullPath)) {
                throw new \Exception('Failed to create invoice file at: ' . $fullPath);
            }

            $order->update([
                'invoice_path'      => $relativePath,
                'invoice_type'      => 'FINAL',
                // ✅ final_amount NOT updated – order total unchanged
                'round_off'         => $roundOff,
                'order_status'      => 'PAID',
                'is_invoice_locked' => 1,
            ]);

            OrderStatusLog::create([
                'order_id'   => $order->id,
                'status'     => 'FINAL_INVOICE_GENERATED',
                'changed_by' => $this->getCurrentStaffId(),
                'remarks'    => 'Final invoice generated after full payment',
            ]);

            \Log::info('FINAL invoice generated successfully for order ID: ' . $order->id, [
                'path' => $relativePath,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath)
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Final invoice generation failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return false;
        }
    }
}