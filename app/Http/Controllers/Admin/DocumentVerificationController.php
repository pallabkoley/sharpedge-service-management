<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDocument;
use Illuminate\Http\Request;

class DocumentVerificationController extends Controller
{
    /**
     * Main Page (Single Blade)
     * Show ONE ROW per ORDER (where order has at least one UPLOADED document)
     */
public function index(Request $request)
{
    $status = $request->get('status', 'all'); // all|pending|verified|rejected

    $query = Order::with([
            'client',
            'marketingStaff',
            'documents' => function ($q) use ($status) {

                // show docs based on tab
                if ($status === 'pending') {
                    $q->where('status', 'UPLOADED');
                } elseif ($status === 'verified') {
                    $q->where('status', 'VERIFIED');
                } elseif ($status === 'rejected') {
                    $q->where('status', 'REJECTED');
                }

                $q->with(['service', 'documentMaster', 'files', 'uploadedBy']);
            }
        ])
        ->latest();

    // whereHas filter based on tab
    if ($status === 'pending') {
        $query->whereHas('documents', fn($q) => $q->where('status', 'UPLOADED'));
    } elseif ($status === 'verified') {
        $query->whereHas('documents', fn($q) => $q->where('status', 'VERIFIED'));
    } elseif ($status === 'rejected') {
        $query->whereHas('documents', fn($q) => $q->where('status', 'REJECTED'));
    } else {
        // all orders that have at least 1 document (any status)
        $query->whereHas('documents');
    }

    // Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('order_id', 'like', "%{$search}%")
              ->orWhereHas('client', function ($clientQ) use ($search) {
                  $clientQ->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('marketingStaff', function ($staffQ) use ($search) {
                  $staffQ->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    $orders = $query->paginate(15);

    // Stats
    $pendingDocumentsCount = OrderDocument::where('status', 'UPLOADED')->count();
    $uniqueClientsCount = Order::whereHas('documents', fn($q) => $q->where('status', 'UPLOADED'))
        ->distinct('client_id')->count('client_id');

    $documentTypesCount = OrderDocument::where('status', 'UPLOADED')
        ->distinct('document_master_id')
        ->count('document_master_id');

    return view('admin.document_verification.index', compact(
        'orders',
        'pendingDocumentsCount',
        'uniqueClientsCount',
        'documentTypesCount',
        'status'
    ));
}


public function load($orderId)
{
    $order = Order::with([
            'client',
            'marketingStaff',
            'items.service', // ✅ important fix
            'documents.service',
            'documents.documentMaster',
            'documents.files',
            'documents.uploadedBy',
            'documents.verifiedBy',
        ])
        ->findOrFail($orderId);

    return response()->json([
        'success' => true,
        'order' => [
            'id' => $order->id,
            'order_id' => $order->order_id,
            'client_name' => $order->client?->name,
            'marketing_staff' => $order->marketingStaff?->name,
            'service_name' => $order->items->first()?->service?->name ?? 'N/A',
        ],
        'documents' => $order->documents->map(function ($document) {
            return [
                'id' => $document->id,
                'status' => $document->status,
                'remarks' => $document->remarks,
                'uploaded_at' => optional($document->uploaded_at)->format('d M Y, h:i A'),
                'service_name' => $document->service?->name,
                'document_name' => $document->documentMaster?->name,
                'uploaded_by' => $document->uploadedBy?->name,
                'files' => $document->files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'file_name' => $file->file_name,
                        'mime_type' => $file->mime_type,
                        'file_size' => $file->file_size,
                        'url' => asset('storage/' . $file->file_path),
                    ];
                })->values(),
            ];
        })->values(),
    ]);
}


    /**
     * Approve Document (AJAX)
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $document = OrderDocument::findOrFail($id);

        if ($document->status !== 'UPLOADED') {
            return response()->json([
                'success' => false,
                'message' => 'Only uploaded documents can be approved.',
            ], 422);
        }

        $staffId = auth()->user()->staff->id ?? null;

        $document->update([
            'status'      => 'VERIFIED',
            'verified_by' => $staffId,
            'verified_at' => now(),
            'remarks'     => $request->remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document approved successfully.',
        ]);
    }

    /**
     * Reject Document (AJAX)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $document = OrderDocument::findOrFail($id);

        if ($document->status !== 'UPLOADED') {
            return response()->json([
                'success' => false,
                'message' => 'Only uploaded documents can be rejected.',
            ], 422);
        }

        $staffId = auth()->user()->staff->id ?? null;

        $document->update([
            'status'      => 'REJECTED',
            'verified_by' => $staffId,
            'verified_at' => now(),
            'remarks'     => $request->remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document rejected successfully.',
        ]);
    }
    
    public function approveAll(Request $request, $orderId)
{
    $request->validate([
        'remarks' => 'nullable|string|max:1000',
    ]);

    $staffId = auth()->user()->staff->id ?? null;

    OrderDocument::where('order_id', $orderId)
        ->where('status', 'UPLOADED')
        ->update([
            'status'      => 'VERIFIED',
            'verified_by' => $staffId,
            'verified_at' => now(),
            'remarks'     => $request->remarks,
        ]);

    return response()->json([
        'success' => true,
        'message' => 'All documents approved successfully.',
    ]);
}  


public function rejectAll(Request $request, $orderId)
{
    $request->validate([
        'remarks' => 'required|string|max:1000',
    ]);

    $staffId = auth()->user()->staff->id ?? null;

    OrderDocument::where('order_id', $orderId)
        ->where('status', 'UPLOADED')
        ->update([
            'status'      => 'REJECTED',
            'verified_by' => $staffId,
            'verified_at' => now(),
            'remarks'     => $request->remarks,
        ]);

    return response()->json([
        'success' => true,
        'message' => 'All documents rejected successfully.',
    ]);
}
}