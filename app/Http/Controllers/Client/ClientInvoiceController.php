<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ClientInvoiceController extends Controller
{
    /**
     * Invoice List Page
     * Shows all invoices of logged in client
     */
    public function index()
    {
        $client = auth()->user()->client;

        if (!$client) {
            abort(403, 'Client not found.');
        }

        $orders = Order::with(['service'])
            ->where('client_id', $client->id)
            ->where('invoice_generated', true)
            ->latest()
            ->get();

        return view('Client.invoices.index', compact('orders'));
    }



    /**
     * Download Invoice PDF
     */
    public function downloadInvoice($id)
    {
        $client = auth()->user()->client;

        if (!$client) {
            abort(403, 'Client not found.');
        }

        $order = Order::where('client_id', $client->id)
            ->where('invoice_generated', true)
            ->findOrFail($id);

        $path = storage_path('app/public/' . $order->invoice_path);

        if (!file_exists($path)) {
            abort(404, 'Invoice file not found.');
        }

        return response()->download($path);
    }



    /**
     * View Invoice in Browser (Preview)
     */
public function previewInvoice($id)
{
    $client = auth()->user()->client;

    if (!$client) {
        abort(403, 'Client not found.');
    }

    $order = Order::where('client_id', $client->id)
        ->where('invoice_generated', true)
        ->findOrFail($id);

    $path = storage_path('app/public/' . $order->invoice_path);

    if (!file_exists($path)) {
        abort(404, 'Invoice file not found.');
    }

    return response()->file($path);
}





    /**
     * AJAX Invoice Details Modal
     */
    public function show($id)
    {
        $client = auth()->user()->client;

        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Client not found'
            ], 403);
        }

        $order = Order::with(['service'])
            ->where('client_id', $client->id)
            ->where('invoice_generated', true)
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }
}
