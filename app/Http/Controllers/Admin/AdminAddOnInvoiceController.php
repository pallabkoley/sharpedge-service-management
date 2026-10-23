<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminAddOnInvoiceController extends Controller
{
    /**
     * Resolve current staff id
     */
    private function getCurrentStaffId()
    {
        $staff = Staff::where('user_id', Auth::id())->first();
        return $staff?->id ?? Staff::first()?->id;
    }

    /**
     * STEP 1: Add ADD-ON service (NO invoice here)
     */
    public function addAddonService(Request $request, Order $order)
    {
        if (!$order->isPaid()) {
            return back()->with('error', 'Add-on can be added only after order is paid.');
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'price'      => 'required|numeric|min:0',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $qty   = $request->quantity ?? 1;
        $price = (float) $request->price;

        OrderItem::create([
            'order_id'       => $order->id,
            'service_id'     => $request->service_id,
            'price'          => $price,
            'quantity'       => $qty,
            'total'          => $price * $qty,
            'item_type'      => 'ADDON',
            'invoice_status' => 'PENDING_INVOICE',
        ]);

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => 'ADDON_ADDED',
            'changed_by' => $this->getCurrentStaffId(),
            'remarks'    => 'Add-on service added',
        ]);

        return back()->with('success', 'Add-on service added successfully.');
    }

    /**
     * STEP 2: Generate ADD-ON Invoice (ORIGINAL invoice untouched)
     */
    public function generateAddonInvoice(Order $order)
    {
        if (!$order->isPaid()) {
            return back()->with('error', 'Order must be paid before generating add-on invoice.');
        }

        $addonItems = $order->items()
            ->addOn()
            ->pendingInvoice()
            ->with('service')
            ->get();

        if ($addonItems->isEmpty()) {
            return back()->with('error', 'No add-on items pending for invoice.');
        }

        try {
            DB::transaction(function () use ($order, $addonItems) {

                $staffId = $this->getCurrentStaffId();

                /* ================= AMOUNTS ================= */

                $subtotal = $addonItems->sum('total');

                $gstRate = (float) config('app.gst_percentage', 18);
                $cgst = round(($subtotal * ($gstRate / 2)) / 100, 2);
                $sgst = round(($subtotal * ($gstRate / 2)) / 100, 2);

                $grandTotal = round($subtotal + $cgst + $sgst, 2);

                /* ================= ADD-ON INVOICE NUMBER ================= */

                $invoiceNumber = 'ADDON-' . now()->format('YmdHis') . '-' . $order->id;
                $fileName = $invoiceNumber . '.pdf';
                $path = 'invoices/' . $fileName;

                /* ================= MARK ITEMS AS INVOICED ================= */

                foreach ($addonItems as $item) {
                    $item->update([
                        'invoice_status' => 'INVOICED',
                    ]);
                }

                /* ================= GENERATE PDF ================= */

                Pdf::loadView('admin.invoices.addon-invoice-pdf', [
                    'order'         => $order,
                    'invoiceNumber' => $invoiceNumber,
                    'items'         => $addonItems,
                    'subtotal'      => $subtotal,
                    'cgst'          => $cgst,
                    'sgst'          => $sgst,
                    'gstRate'       => $gstRate,
                    'grandTotal'    => $grandTotal,
                ])->save(storage_path('app/public/' . $path));

                /* ================= STATUS LOG ================= */

                OrderStatusLog::create([
                    'order_id'   => $order->id,
                    'status'     => 'ADDON_INVOICE_GENERATED',
                    'changed_by' => $staffId,
                    'remarks'    => 'Add-on invoice generated: ' . $invoiceNumber,
                ]);
            });

            return back()->with('success', 'Add-on invoice generated successfully.');

        } catch (\Exception $e) {
            \Log::error('Add-on invoice error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate add-on invoice.');
        }
    }
}
