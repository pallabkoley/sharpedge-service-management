<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Models\Service;
use App\Models\DocumentRequirement;
use App\Models\ClientDocumentUpload;
use App\Models\DocumentMaster;
use App\Models\Staff;

class DocumentUploadController extends Controller
{
    /**
     * Show document upload page
     */
    public function index(Request $request)
    {
        $clients = Client::where('status', 'active')
            ->orderBy('name')
            ->get();

        $services = Service::where('status', 'active')
            ->orderBy('service_name')
            ->get();

        $client    = null;
        $service   = null;
        $documents = collect();

        if ($request->filled(['client_id', 'service_id'])) {
            $client  = Client::find($request->client_id);
            $service = Service::find($request->service_id);

            if ($client && $service) {
                Log::info("Document upload page loaded", [
                    'client_id'  => $client->id,
                    'service_id' => $service->id
                ]);

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
                    $documents = DocumentMaster::where('status', 'active')
                        ->orderBy('document_name')
                        ->get()
                        ->map(fn ($doc) => $this->mapDocument($doc, $uploads));
                } else {
                    $documents = $requirements->map(function ($req) use ($uploads) {
                        return $this->mapDocument(
                            $req->documentMaster,
                            $uploads,
                            $req->type
                        );
                    });
                }
            }
        }

        return view('Marketing.documents.single-page-upload', compact(
            'clients',
            'services',
            'client',
            'service',
            'documents'
        ));
    }

    /**
     * Upload or replace a document (PDF, Image, Excel supported)
     */
    public function upload(Request $request)
    {
        Log::info('Upload request received', $request->all());

        $validated = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'service_id'  => 'required|exists:services,id',
            'document_id' => 'required|exists:document_masters,id',
            'document'    => 'required|file|mimes:pdf,jpg,jpeg,png,xls,xlsx,csv|max:2048',
        ]);

        $staffId = Staff::where('user_id', auth()->id())->value('id');

        if (!$staffId) {
            Log::error('Staff not found', ['user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Staff profile not found'
            ], 400);
        }

        $file       = $request->file('document');
        $folderPath = "upload_documents/client_{$validated['client_id']}/service_{$validated['service_id']}";
        $fileName   = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        $filePath = $file->storeAs($folderPath, $fileName, 'public');

        $upload = ClientDocumentUpload::updateOrCreate(
            [
                'client_id'   => $validated['client_id'],
                'service_id'  => $validated['service_id'],
                'document_id' => $validated['document_id'],
            ],
            [
                'file_path'          => $filePath,
                'original_file_name' => $file->getClientOriginalName(),
                'file_size'          => $file->getSize(),
                'status'             => 'uploaded',
                'uploaded_by'        => $staffId,
                'uploaded_at'        => now(),
                'rejected_reason'    => null,
            ]
        );

        Log::info('Document uploaded successfully', [
            'upload_id' => $upload->id
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Document uploaded successfully',
            'upload_id' => $upload->id
        ]);
    }

    /**
     * View uploaded document
     */
    public function view($id)
    {
        $document = ClientDocumentUpload::findOrFail($id);

        return response()->file(
            storage_path('app/public/' . $document->file_path)
        );
    }

    /**
     * Delete uploaded document
     */
    public function delete($id)
    {
        $document = ClientDocumentUpload::findOrFail($id);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully');
    }

    /**
     * AJAX: Get services by client
     */
    public function getClientServices($clientId)
    {
        return response()->json([
            'success'  => true,
            'services' => Service::where('status', 'active')
                ->orderBy('service_name')
                ->get()
        ]);
    }

    /**
     * Helper to map document data
     */
    private function mapDocument($docMaster, $uploads, $type = 'mandatory')
    {
        $upload = $uploads->where('document_id', $docMaster->id)->first();

        return (object) [
            'document_id'        => $docMaster->id,
            'document_name'      => $docMaster->document_name,
            'type'               => $type,
            'upload_status'      => $upload->status ?? 'pending',
            'upload_id'          => $upload->id ?? null,
            'file_path'          => $upload->file_path ?? null,
            'original_file_name' => $upload->original_file_name ?? null,
            'rejected_reason'    => $upload->rejected_reason ?? null,
        ];
    }
}
