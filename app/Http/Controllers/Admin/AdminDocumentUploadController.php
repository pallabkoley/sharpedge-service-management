<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\Client;
use App\Models\Service;
use App\Models\DocumentRequirement;
use App\Models\ClientDocumentUpload;
use App\Models\DocumentMaster;

class AdminDocumentUploadController extends Controller
{
    /**
     * Show admin document upload page
     */
    public function index(Request $request)
    {
        $clients  = Client::where('status', 'active')->orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('service_name')->get();

        $client    = null;
        $service   = null;
        $documents = collect();

        if ($request->filled(['client_id', 'service_id'])) {
            $client  = Client::find($request->client_id);
            $service = Service::find($request->service_id);

            if ($client && $service) {

                $requirements = DocumentRequirement::where('service_id', $service->id)
                    ->active()
                    ->with('documentMaster')
                    ->orderBy('order')
                    ->get();

                $uploads = ClientDocumentUpload::where([
                    'client_id'  => $client->id,
                    'service_id' => $service->id,
                ])->get();

                if ($requirements->isEmpty()) {
                    $allDocs = DocumentMaster::where('status', 'active')
                        ->orderBy('document_name')
                        ->get();

                    $documents = $allDocs->map(fn ($doc) =>
                        $this->mapDocument($doc, $uploads)
                    );
                } else {
                    $documents = $requirements->map(fn ($req) =>
                        $this->mapDocument($req->documentMaster, $uploads, $req)
                    );
                }
            }
        }

        return view('admin.documents.index2', compact(
            'clients',
            'services',
            'client',
            'service',
            'documents'
        ));
    }

    /**
     * Upload / replace document (single)
     */
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'service_id'  => 'required|exists:services,id',
            'document_id' => 'required|exists:document_masters,id',
        ]);

        $requirement = DocumentRequirement::where([
            'service_id'         => $validated['service_id'],
            'document_master_id' => $validated['document_id'],
        ])->first();

        $maxSizeMB = $requirement->max_size ?? 2;
        $maxSizeKB = $maxSizeMB * 1024;

        $allowed = $requirement->allowed_extensions
            ?? 'pdf,doc,docx,jpg,jpeg,png,xls,xlsx';

        $allowed = strtolower(str_replace(' ', '', $allowed));

        $validator = Validator::make($request->all(), [
            'document' => "required|file|mimes:$allowed|max:$maxSizeKB",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // 🔥 REPLACE EXISTING FILE (FIXES DUPLICATE KEY ERROR)
        $this->deleteExistingUpload($validated);

        $file   = $request->file('document');
        $folder = "upload_documents/client_{$validated['client_id']}/service_{$validated['service_id']}";
        $name   = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());

        $path = $file->storeAs($folder, $name, 'public');

        $upload = ClientDocumentUpload::create([
            'client_id'          => $validated['client_id'],
            'service_id'         => $validated['service_id'],
            'document_id'        => $validated['document_id'],
            'file_path'          => $path,
            'original_file_name' => $file->getClientOriginalName(),
            'file_size'          => $file->getSize(),
            'file_extension'     => $file->getClientOriginalExtension(),
            'status'             => 'approved',
            'uploaded_by'        => null,
            'uploaded_at'        => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'file'    => [
                'id'   => $upload->id,
                'name' => $upload->original_file_name,
                'size' => round($upload->file_size / 1024, 2) . ' KB',
            ]
        ]);
    }

    /**
     * Upload multiple files (replaces old ones)
     */
    public function uploadMultiple(Request $request)
    {
        $validated = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'service_id'  => 'required|exists:services,id',
            'document_id' => 'required|exists:document_masters,id',
        ]);

        $requirement = DocumentRequirement::where([
            'service_id'         => $validated['service_id'],
            'document_master_id' => $validated['document_id'],
        ])->first();

        $maxSizeMB = $requirement->max_size ?? 2;
        $maxSizeKB = $maxSizeMB * 1024;

        $allowed = $requirement->allowed_extensions
            ?? 'pdf,doc,docx,jpg,jpeg,png,xls,xlsx';

        $allowed = strtolower(str_replace(' ', '', $allowed));

        $validator = Validator::make($request->all(), [
            'documents.*' => "file|mimes:$allowed|max:$maxSizeKB",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // 🔥 CLEAR EXISTING FILES FIRST
        $this->deleteExistingUpload($validated);

        $uploaded = [];

        foreach ($request->file('documents', []) as $file) {
            $folder = "upload_documents/client_{$validated['client_id']}/service_{$validated['service_id']}";
            $name   = time().'_'.rand(100,999).'_'.$file->getClientOriginalName();

            $path = $file->storeAs($folder, $name, 'public');

            $upload = ClientDocumentUpload::create([
                'client_id'          => $validated['client_id'],
                'service_id'         => $validated['service_id'],
                'document_id'        => $validated['document_id'],
                'file_path'          => $path,
                'original_file_name' => $file->getClientOriginalName(),
                'file_size'          => $file->getSize(),
                'file_extension'     => $file->getClientOriginalExtension(),
                'status'             => 'approved',
                'uploaded_by'        => null,
                'uploaded_at'        => now(),
            ]);

            $uploaded[] = [
                'id'   => $upload->id,
                'name' => $upload->original_file_name,
                'size' => round($upload->file_size / 1024, 2) . ' KB',
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' files uploaded successfully',
            'files'   => $uploaded
        ]);
    }

    /**
     * View document
     */
    public function view($id)
    {
        $doc = ClientDocumentUpload::findOrFail($id);
        return response()->file(storage_path('app/public/' . $doc->file_path));
    }

    /**
     * Download document
     */
    public function download($id)
    {
        $doc = ClientDocumentUpload::findOrFail($id);
        return Storage::disk('public')->download($doc->file_path, $doc->original_file_name);
    }

    /**
     * Delete document
     */
    public function delete($id)
    {
        $doc = ClientDocumentUpload::findOrFail($id);

        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    /**
     * Delete existing upload for same client + service + document
     */
    private function deleteExistingUpload(array $validated)
    {
        ClientDocumentUpload::where($validated)->each(function ($old) {
            if ($old->file_path && Storage::disk('public')->exists($old->file_path)) {
                Storage::disk('public')->delete($old->file_path);
            }
            $old->delete();
        });
    }

    /**
     * Map document data for UI
     */
    private function mapDocument($doc, $uploads, $req = null)
    {
        $upload = $uploads->where('document_id', $doc->id)->first();

        return (object) [
            'document_id'        => $doc->id,
            'document_name'      => $doc->document_name,
            'type'               => $req->type ?? 'mandatory',
            'upload_status'      => $upload?->status ?? 'pending',
            'upload_id'          => $upload?->id,
            'file_path'          => $upload?->file_path,
            'original_file_name' => $upload?->original_file_name,
            'file_count'         => $uploads->where('document_id', $doc->id)->count(),
            'max_size'           => $req->max_size ?? $doc->max_size ?? 2,
            'allowed_extensions' => $req->allowed_extensions
                ?? $doc->allowed_extensions
                ?? 'PDF, DOC, DOCX, JPG, PNG, XLS, XLSX',
        ];
    }
}
