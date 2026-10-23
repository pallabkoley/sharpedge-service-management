<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Client;
use App\Models\Service;
use App\Models\DocumentRequirement;
use App\Models\ClientDocumentUpload;
use App\Models\Staff;

class ClientDocumentController extends Controller
{
    /**
     * Show service-wise document checklist page
     */
    public function index($clientId, $serviceId)
    {
        $client  = Client::findOrFail($clientId);
        $service = Service::findOrFail($serviceId);

        $documents = DocumentRequirement::where('service_id', $serviceId)
            ->active()
            ->with('documentMaster')
            ->orderBy('order')
            ->get()
            ->map(function ($req) use ($clientId, $serviceId) {

                $upload = ClientDocumentUpload::where([
                    'client_id'   => $clientId,
                    'service_id'  => $serviceId,
                    'document_id' => $req->document_master_id,
                ])->first();

                return (object) [
                    'document_id'     => $req->document_master_id,
                    'document_name'   => $req->documentMaster->document_name,
                    'type'            => $req->type,
                    'upload_status'   => $upload->status ?? 'pending',
                    'upload_id'       => $upload->id ?? null,
                    'file_path'       => $upload->file_path ?? null,
                    'rejected_reason' => $upload->rejected_reason ?? null,
                ];
            });

        return view('Marketing.documents.index2', compact(
            'client',
            'service',
            'documents'
        ));
    }

    /**
     * Upload or Replace a client document
     */
    public function upload(Request $request)
    {
        $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'service_id'  => 'required|exists:services,id',
            'document_id' => 'required|exists:document_masters,id',
            'document'    => 'required|file|max:2048',
        ]);

        $staffId = Staff::where('user_id', auth()->id())->value('id');

        if (!$staffId) {
            return back()->withErrors('Staff profile not found');
        }

        $file = $request->file('document');

        $folderPath = 'upload_documents/client_' . $request->client_id
                    . '/service_' . $request->service_id;

        $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        $filePath = $file->storeAs($folderPath, $fileName, 'public');

        ClientDocumentUpload::updateOrCreate(
            [
                'client_id'   => $request->client_id,
                'service_id'  => $request->service_id,
                'document_id' => $request->document_id,
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

        return back()->with('success', 'Document uploaded successfully');
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
     * Delete uploaded document (optional)
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
    
    public function showClientServiceDocuments($clientId, $serviceId)
    {
        $client = Client::findOrFail($clientId);
        $service = Service::findOrFail($serviceId);

        $documents = DocumentRequirement::where('service_id', $serviceId)
            ->active()
            ->with('documentMaster')
            ->orderBy('order')
            ->get()
            ->map(function ($req) use ($clientId, $serviceId) {
                $upload = ClientDocumentUpload::where([
                    'client_id'   => $clientId,
                    'service_id'  => $serviceId,
                    'document_id' => $req->document_master_id,
                ])->first();

                return (object) [
                    'document_id'     => $req->document_master_id,
                    'document_name'   => $req->documentMaster->document_name,
                    'type'            => $req->type,
                    'upload_status'   => $upload->status ?? 'pending',
                    'upload_id'       => $upload->id ?? null,
                    'file_path'       => $upload->file_path ?? null,
                    'rejected_reason' => $upload->rejected_reason ?? null,
                ];
            });

        return view('Marketing.documents.index2', compact(
            'client',
            'service',
            'documents'
        ));
    }
}
