<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Models
use App\Models\Order;
use App\Models\Staff;
use App\Models\OrderDocument;
use App\Models\DocumentRequirement;
use App\Models\DocumentFile;

class MarketingOrderDocumentController extends Controller
{
    /**
     * Store uploaded docs here:
     * storage/app/public/order_client_documents
     */
    private string $uploadFolder = 'order_client_documents';

    /**
     * =====================================================
     * LOAD DOCUMENT REQUIREMENTS + UPLOADED DOCS (AJAX)
     * =====================================================
     */
    public function loadOrderDocuments($orderId)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $order = Order::where('id', $orderId)
            ->where('marketing_staff_id', $staff->id)
            ->where('order_status', 'PAID')
            ->with(['client', 'items.service'])
            ->firstOrFail();

        $serviceId = optional($order->items->first())->service_id;

        if (!$serviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found for this order.',
            ], 404);
        }

        // Requirements for this service
        $requirements = DocumentRequirement::where('service_id', $serviceId)
            ->where('status', 'active')
            ->with('documentMaster')
            ->orderBy('order')
            ->get();

        // Uploaded docs for this order (with files)
        $uploadedDocs = OrderDocument::where('order_id', $order->id)
            ->with(['files'])
            ->get()
            ->keyBy('document_master_id');

        return response()->json([
            'success' => true,
            'order' => $order,
            'requirements' => $requirements,
            'uploaded_docs' => $uploadedDocs,
        ]);
    }

    /**
     * =====================================================
     * UPLOAD MULTIPLE DOCUMENT FILES (AJAX)
     * =====================================================
     * - Multiple upload enabled
     * - Supports zip / rar / xlsm
     * - Stores files in order_client_documents
     */
    public function uploadOrderDocument(Request $request)
{
$request->validate([
'order_id' => 'required|exists:orders,id',
'document_master_id' => 'required|exists:document_masters,id',


    // Multiple files allowed
    'files' => 'required|array|min:1',

    // Allowed file types
    'files.*' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,zip,rar,json,xlsm',
]);

$staff = Staff::where('user_id', Auth::id())->firstOrFail();

$order = Order::where('id', $request->order_id)
    ->where('marketing_staff_id', $staff->id)
    ->where('order_status', 'PAID')
    ->with('items')
    ->firstOrFail();

$serviceId = optional($order->items->first())->service_id;

if (!$serviceId) {
    return response()->json([
        'success' => false,
        'message' => 'Service not found for this order.'
    ], 404);
}

DB::beginTransaction();

try {

    // Create or get order document
    $orderDoc = OrderDocument::firstOrCreate(
        [
            'order_id' => $order->id,
            'document_master_id' => $request->document_master_id,
        ],
        [
            'service_id' => $serviceId,
            'status' => 'PENDING',
            'uploaded_by' => $staff->id,
        ]
    );

    foreach ($request->file('files') as $file) {

        // Store file in storage/app/public/order_client_documents
        $filePath = $file->store($this->uploadFolder, 'public');

        DocumentFile::create([
            'order_document_id' => $orderDoc->id,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $staff->id,
            'uploaded_at' => now(),
        ]);
    }

    // Update document status after upload
    $orderDoc->update([
        'status' => 'UPLOADED',
        'uploaded_by' => $staff->id,
        'uploaded_at' => now(),
    ]);

    DB::commit();

    return response()->json([
        'success' => true,
        'message' => 'Documents uploaded successfully.',
        'document' => $orderDoc->load('files'),
    ]);

} catch (\Exception $e) {

    DB::rollBack();

    Log::error('Document upload failed', [
        'error' => $e->getMessage(),
        'order_id' => $request->order_id ?? null,
        'document_master_id' => $request->document_master_id ?? null,
    ]);

    return response()->json([
        'success' => false,
        'message' => 'Failed to upload documents.'
    ], 500);
}


}


    /**
     * =====================================================
     * DELETE SINGLE FILE (AJAX) - FIXED VERSION
     * =====================================================
     */
    public function deleteDocumentFile($fileId)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        Log::info('Delete file attempt', [
            'staff_id' => $staff->id,
            'user_id' => Auth::id(),
            'file_id' => $fileId
        ]);

        // Get the file with proper relationships
        $file = DocumentFile::where('id', $fileId)
            ->with(['orderDocument.order' => function($query) use ($staff) {
                // Eager load the order with staff check
                $query->where('marketing_staff_id', $staff->id)
                      ->where('order_status', 'PAID');
            }])
            ->firstOrFail();

        // Check if order was loaded (relationship might be null if staff doesn't own it)
        if (!$file->orderDocument || !$file->orderDocument->order) {
            Log::warning('File or order not found for staff', [
                'staff_id' => $staff->id,
                'file_id' => $fileId,
                'has_order_document' => !is_null($file->orderDocument),
                'has_order' => $file->orderDocument ? !is_null($file->orderDocument->order) : false
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'File not found or you do not have permission to delete it.',
            ], 403);
        }

        $order = $file->orderDocument->order;

        Log::info('Delete file - Authorization details', [
            'staff_id' => $staff->id,
            'order_marketing_staff_id' => $order->marketing_staff_id,
            'order_id' => $order->id,
            'order_status' => $order->order_status,
            'match' => $order->marketing_staff_id == $staff->id
        ]);

        // Alternative security check - more flexible
        $orderBelongsToStaff = Order::where('id', $order->id)
            ->where('marketing_staff_id', $staff->id)
            ->where('order_status', 'PAID')
            ->exists();

        if (!$orderBelongsToStaff) {
            Log::warning('Unauthorized file delete attempt', [
                'staff_id' => $staff->id,
                'order_marketing_staff_id' => $order->marketing_staff_id,
                'order_id' => $order->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Delete file from storage
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            $orderDocument = $file->orderDocument;

            // Delete file record
            $file->delete();

            // If no files left, set status back to PENDING
            $orderDocument->loadCount('files');
            if ($orderDocument->files_count === 0) {
                $orderDocument->update([
                    'status' => 'PENDING',
                    'uploaded_at' => null,
                ]);
            }

            DB::commit();

            Log::info('File deleted successfully', [
                'file_id' => $fileId,
                'staff_id' => $staff->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('File delete failed', [
                'fileId' => $fileId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file. Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * =====================================================
     * DELETE ALL FILES OF A DOCUMENT (AJAX) - FIXED VERSION
     * =====================================================
     */
    public function deleteAllFilesOfDocument($orderDocumentId)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        Log::info('Delete all files attempt', [
            'staff_id' => $staff->id,
            'order_document_id' => $orderDocumentId
        ]);

        // Get the order document with proper relationships
        $orderDoc = OrderDocument::where('id', $orderDocumentId)
            ->with(['order' => function($query) use ($staff) {
                // Eager load the order with staff check
                $query->where('marketing_staff_id', $staff->id)
                      ->where('order_status', 'PAID');
            }, 'files'])
            ->firstOrFail();

        // Check if order was loaded (relationship might be null if staff doesn't own it)
        if (!$orderDoc->order) {
            Log::warning('Order not found for staff', [
                'staff_id' => $staff->id,
                'order_document_id' => $orderDocumentId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Document not found or you do not have permission to delete it.',
            ], 403);
        }

        $order = $orderDoc->order;

        Log::info('Delete all files - Authorization details', [
            'staff_id' => $staff->id,
            'order_marketing_staff_id' => $order->marketing_staff_id,
            'order_id' => $order->id,
            'order_status' => $order->order_status,
            'match' => $order->marketing_staff_id == $staff->id
        ]);

        // Alternative security check - more flexible
        $orderBelongsToStaff = Order::where('id', $order->id)
            ->where('marketing_staff_id', $staff->id)
            ->where('order_status', 'PAID')
            ->exists();

        if (!$orderBelongsToStaff) {
            Log::warning('Unauthorized delete all files attempt', [
                'staff_id' => $staff->id,
                'order_marketing_staff_id' => $order->marketing_staff_id,
                'order_id' => $order->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            foreach ($orderDoc->files as $file) {

                // Delete from storage
                if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }

                // Delete file row
                $file->delete();
            }

            // Update status
            $orderDoc->update([
                'status' => 'PENDING',
                'uploaded_at' => null,
            ]);

            DB::commit();

            Log::info('All files deleted successfully', [
                'order_document_id' => $orderDocumentId,
                'staff_id' => $staff->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All files deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Delete all files failed', [
                'orderDocumentId' => $orderDocumentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete all files.',
            ], 500);
        }
    }

    /**
     * =====================================================
     * CHECK DOCUMENT EXISTS (AJAX)
     * =====================================================
     */
    public function checkDocumentExists($orderId, $documentMasterId)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $exists = OrderDocument::where('order_id', $orderId)
            ->where('document_master_id', $documentMasterId)
            ->whereHas('order', function ($q) use ($staff) {
                $q->where('marketing_staff_id', $staff->id);
            })
            ->whereHas('files')
            ->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }
}