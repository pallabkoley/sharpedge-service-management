<?php

namespace App\Http\Controllers\Admin;

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

class AdminOrderDocumentController extends Controller
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
        $user = Auth::user();

        // ✅ AUTHORIZATION USING ROLE NAME (SOURCE OF TRUTH)
        if (!in_array($user->role->name, ['admin', 'backoffice', 'marketing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $order = Order::with(['client', 'items.service', 'marketingStaff'])
            ->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        // ✅ ORDER STATUS CHECK - FIXED: Check all valid statuses
        if (!in_array($order->order_status, [
            'PENDING_PAYMENT',
            'PAYMENT_SUBMITTED', 
            'PAYMENT_APPROVED',
            'PAID',
            'IN_PROGRESS',
            'COMPLETED'
        ])) {
            return response()->json([
                'success' => false,
                'message' => 'Order not eligible for document access.'
            ], 403);
        }

        $serviceId = optional($order->items->first())->service_id;

        if (!$serviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.'
            ], 404);
        }

        $requirements = DocumentRequirement::where('service_id', $serviceId)
            ->where('status', 'active')
            ->with('documentMaster')
            ->orderBy('order')
            ->get();

        $uploadedDocs = OrderDocument::where('order_id', $order->id)
            ->with([
                'files',
                'uploadedBy',
                'verifiedBy',
                'documentMaster'
            ])
            ->get()
            ->keyBy('document_master_id');

        return response()->json([
            'success' => true,
            'requirements' => $requirements,
            'uploaded_docs' => $uploadedDocs,
        ]);
    }

    /**
     * =====================================================
     * UPLOAD MULTIPLE DOCUMENT FILES (AJAX) - AUTO APPROVED
     * =====================================================
     * - Admin uploads are auto-approved
     * - Status set to 'APPROVED' immediately
     * - Verified by and verified_at set
     * - Stores files in order_client_documents
     */
    public function uploadOrderDocument(Request $request)
    {
        // =========================
        // VALIDATION
        // =========================
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'document_master_id' => 'required|exists:document_masters,id',
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,zip,rar,xlsm',
        ]);

        // =========================
        // AUTHENTICATED ADMIN STAFF
        // =========================
        $adminStaff = Staff::where('user_id', Auth::id())->first();
        
        if (!$adminStaff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff record not found.',
            ], 403);
        }

        // =========================
        // ORDER CHECK (FIXED HERE 🔥)
        // =========================
        $order = Order::where('id', $request->order_id)
            ->whereIn('order_status', [
                'PENDING_PAYMENT',
                'PAYMENT_SUBMITTED',
                'PAYMENT_APPROVED',
                'PAID',
                'IN_PROGRESS',
                'COMPLETED',
            ])
            ->with('items')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not eligible for document upload.',
            ], 403);
        }

        $serviceId = optional($order->items->first())->service_id;

        if (!$serviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found for this order.',
            ], 404);
        }

        // =========================
        // DATABASE TRANSACTION
        // =========================
        DB::beginTransaction();

        try {
            // =========================
            // CREATE / FETCH ORDER DOCUMENT
            // =========================
            $orderDoc = OrderDocument::firstOrCreate(
                [
                    'order_id' => $order->id,
                    'document_master_id' => $request->document_master_id,
                ],
                [
                    'service_id' => $serviceId,
                    'status' => 'PENDING',
                    'uploaded_by' => $adminStaff->id,
                ]
            );

            // =========================
            // FILE UPLOAD LOOP
            // =========================
            foreach ($request->file('files') as $file) {
                $filePath = $file->store($this->uploadFolder, 'public');

                DocumentFile::create([
                    'order_document_id' => $orderDoc->id,
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => $adminStaff->id,
                    'uploaded_at' => now(),
                ]);
            }

            // =========================
            // AUTO-APPROVE (ADMIN UPLOAD)
            // =========================
            $orderDoc->update([
                'status' => 'APPROVED',
                'uploaded_by' => $adminStaff->id,
                'uploaded_at' => now(),
                'verified_by' => $adminStaff->id,
                'verified_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documents uploaded and auto-approved successfully.',
                'document' => $orderDoc->load(['files', 'documentMaster']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin document upload failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id,
                'document_master_id' => $request->document_master_id,
                'admin_staff_id' => $adminStaff->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload documents. Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * =====================================================
     * DELETE SINGLE FILE (AJAX) - ADMIN VERSION
     * =====================================================
     */
    public function deleteDocumentFile($fileId)
    {
        $adminStaff = Staff::where('user_id', Auth::id())->first();
        
        if (!$adminStaff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff record not found.',
            ], 403);
        }

        Log::info('Admin delete file attempt', [
            'admin_staff_id' => $adminStaff->id,
            'user_id' => Auth::id(),
            'file_id' => $fileId
        ]);

        // Get the file with proper relationships
        $file = DocumentFile::where('id', $fileId)
            ->with(['orderDocument.order'])
            ->first();

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }

        // Get order from file relationship
        $order = $file->orderDocument->order ?? null;

        // FIXED: Allow admin to delete from orders in these statuses
        if (!$order || !in_array($order->order_status, [
            'PENDING_PAYMENT',
            'PAYMENT_SUBMITTED',
            'PAYMENT_APPROVED',
            'PAID',
            'IN_PROGRESS',
            'COMPLETED'
        ])) {
            Log::warning('Admin file delete - Order not found or not in valid status', [
                'admin_staff_id' => $adminStaff->id,
                'file_id' => $fileId,
                'order_id' => $order->id ?? null,
                'order_status' => $order->order_status ?? 'none',
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'File not found or order is not in valid status.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Delete file from storage: storage/app/public/order_client_documents
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
                    'verified_by' => null,
                    'verified_at' => null,
                ]);
            }

            DB::commit();

            Log::info('Admin file deleted successfully', [
                'file_id' => $fileId,
                'admin_staff_id' => $adminStaff->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin file delete failed', [
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
     * DELETE ALL FILES OF A DOCUMENT (AJAX) - ADMIN VERSION
     * =====================================================
     */
    public function deleteAllFilesOfDocument($orderDocumentId)
    {
        $adminStaff = Staff::where('user_id', Auth::id())->first();
        
        if (!$adminStaff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff record not found.',
            ], 403);
        }

        Log::info('Admin delete all files attempt', [
            'admin_staff_id' => $adminStaff->id,
            'order_document_id' => $orderDocumentId
        ]);

        // Get the order document with proper relationships
        $orderDoc = OrderDocument::where('id', $orderDocumentId)
            ->with(['order', 'files'])
            ->first();

        if (!$orderDoc) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.',
            ], 404);
        }

        // Get order from document
        $order = $orderDoc->order;

        // FIXED: Allow admin to delete from orders in these statuses
        if (!$order || !in_array($order->order_status, [
            'PENDING_PAYMENT',
            'PAYMENT_SUBMITTED',
            'PAYMENT_APPROVED',
            'PAID',
            'IN_PROGRESS',
            'COMPLETED'
        ])) {
            Log::warning('Admin delete all files - Order not found or not in valid status', [
                'admin_staff_id' => $adminStaff->id,
                'order_document_id' => $orderDocumentId,
                'order_id' => $order->id ?? null,
                'order_status' => $order->order_status ?? 'none',
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Document not found or order is not in valid status.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            foreach ($orderDoc->files as $file) {
                // Delete from storage: storage/app/public/order_client_documents
                if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }

                // Delete file row
                $file->delete();
            }

            // Update status back to PENDING
            $orderDoc->update([
                'status' => 'PENDING',
                'uploaded_at' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);

            DB::commit();

            Log::info('Admin all files deleted successfully', [
                'order_document_id' => $orderDocumentId,
                'admin_staff_id' => $adminStaff->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All files deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin delete all files failed', [
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
     * CHECK DOCUMENT EXISTS (AJAX) - ADMIN VERSION
     * =====================================================
     */
    public function checkDocumentExists($orderId, $documentMasterId)
    {
        $exists = OrderDocument::where('order_id', $orderId)
            ->where('document_master_id', $documentMasterId)
            ->whereHas('order', function ($q) {
                $q->whereIn('order_status', [
                    'PENDING_PAYMENT',
                    'PAYMENT_SUBMITTED',
                    'PAYMENT_APPROVED',
                    'PAID',
                    'IN_PROGRESS',
                    'COMPLETED'
                ]);
            })
            ->whereHas('files')
            ->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    /**
     * =====================================================
     * MANUALLY APPROVE/DISAPPROVE DOCUMENT (ADMIN ONLY)
     * =====================================================
     */
    public function changeDocumentStatus(Request $request, $orderDocumentId)
    {
        $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
            'remarks' => 'nullable|string|max:500',
        ]);

        $adminStaff = Staff::where('user_id', Auth::id())->first();
        
        if (!$adminStaff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff record not found.',
            ], 403);
        }

        $orderDoc = OrderDocument::where('id', $orderDocumentId)
            ->with(['order', 'files'])
            ->first();

        if (!$orderDoc) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.',
            ], 404);
        }

        // Check if order exists and has valid status
        $order = $orderDoc->order;
        if (!$order || !in_array($order->order_status, [
            'PENDING_PAYMENT',
            'PAYMENT_SUBMITTED',
            'PAYMENT_APPROVED',
            'PAID',
            'IN_PROGRESS',
            'COMPLETED'
        ])) {
            return response()->json([
                'success' => false,
                'message' => 'Order is not in valid status.',
            ], 403);
        }

        // Ensure document has files
        if ($orderDoc->files->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Document has no files to approve.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $orderDoc->update([
                'status' => $request->status,
                'verified_by' => $adminStaff->id,
                'verified_at' => now(),
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            Log::info('Admin changed document status', [
                'order_document_id' => $orderDocumentId,
                'status' => $request->status,
                'admin_staff_id' => $adminStaff->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document status updated to ' . $request->status,
                'document' => $orderDoc->load('verifiedBy'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin document status change failed', [
                'orderDocumentId' => $orderDocumentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update document status.',
            ], 500);
        }
    }
}