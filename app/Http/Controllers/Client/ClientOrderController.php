<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDocument;
use App\Models\DocumentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ClientOrderController extends Controller
{
    /**
     * My Orders Page (Blade)
     */
public function index()
{
    $client = auth()->user()->client;

 

    $orders = Order::with([
        'tasks',
        'coupon',
        'service',
        'supportDocuments' // admin uploaded support documents
    ])
    ->where('client_id', $client->id)
    ->latest()
    ->get();

    return view('Client.orders.index', compact('orders'));
}

    /**
     * Order Details (AJAX)
     */
public function show($id)
{
    $client = Auth::user()->client;

    if (!$client) {
        return response()->json([
            'status' => false,
            'message' => 'Client not found'
        ], 403);
    }

    $order = Order::with([
        'tasks',
        'service' => function ($q) {
            $q->withTrashed()->select('id','service_name');
        },
        'documents.documentMaster',
        'documents.files',
        'coupon'
    ])
    ->where('id', $id)
    ->where('client_id', $client->id)
    ->first();

    if (!$order) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized'
        ], 403);
    }

    return response()->json([
        'status' => true,
        'data' => $order
    ]);
}

    /**
     * Load Required Documents for Order (AJAX)
     */
    public function documents(Order $order)
    {
        $client = auth()->user()->client;

     

        $order->load([
            'client.user',
            'service',
            'service.documentRequirements.documentMaster',
            'documents.documentMaster',
            'documents.files'
        ]);

        return response()->json([
            'status' => true,
            'order'  => $order
        ]);
    }

    /**
     * Upload Document File (Client Panel)
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'order_id'           => 'required|exists:orders,id',
            'document_master_id' => 'required|exists:document_masters,id',
            'file'               => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $client = auth()->user()->client;

        $order = Order::where('id', $request->order_id)
            ->where('client_id', $client->id)
            ->firstOrFail();

        // Create order document if not exists
        $orderDocument = OrderDocument::firstOrCreate(
            [
                'order_id'           => $order->id,
                'service_id'         => $order->service_id,
                'document_master_id' => $request->document_master_id,
            ],
            [
                'status'      => 'pending',
                'uploaded_at' => now(),
            ]
        );

        // Store file
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();

        $path = $file->storeAs(
            'order_client_documents/' . $order->id,
            $fileName,
            'public'
        );

        // Save file record
        DocumentFile::create([
            'order_document_id' => $orderDocument->id,
            'file_path'         => $path,
            'file_name'         => $fileName,
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
            'uploaded_at'       => now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Document uploaded successfully',
        ]);
    }



public function downloadCertificate($id)
{
    $client = auth()->user()->client;

    if (!$client) {
        abort(403);
    }

    $order = Order::where('id', $id)
        ->where('client_id', $client->id)
        ->firstOrFail();

    if (!$order->certificate_path) {
        return back()->with('error', 'Certificate not available');
    }

    $path = storage_path('app/public/' . $order->certificate_path);

    if (!file_exists($path)) {
        return back()->with('error', 'Certificate file missing');
    }

    return response()->download($path);
}
    /**
     * Orders List for Support Tickets Dropdown (AJAX)
     */
    public function listForTickets()
{
    $client = auth()->user()->client;

    if (!$client) {
        return response()->json([], 403);
    }

    return Order::with('service')
        ->where('client_id', $client->id)
        ->latest()
        ->get()
        ->map(function ($order) {
            return [
                'id'       => $order->id,
                'order_no' => $order->order_id, // your human-readable order number
                'service'  => $order->service?->name ?? 'N/A',
            ];
        });
}
}