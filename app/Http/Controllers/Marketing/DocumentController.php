<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\DocumentMaster;
use App\Models\DocumentRequirement;
use App\Models\ClientDocumentUpload;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display document requirements (Read-only for marketing)
     */
    public function index()
    {
        $servicesWithDocs = Service::whereHas('documentRequirements')
            ->with(['documentRequirements' => function($query) {
                $query->where('status', 'active')
                      ->with('documentMaster')
                      ->orderBy('type')
                      ->orderBy('order');
            }])
            ->active()
            ->orderBy('service_name')
            ->get();

        $totalDocuments = DocumentMaster::where('status', 'active')->count();

        $mandatoryCount = DocumentRequirement::where('type', 'mandatory')
            ->where('status', 'active')
            ->count();

        $optionalCount = DocumentRequirement::where('type', 'optional')
            ->where('status', 'active')
            ->count();

        return view('Marketing.documents.index', [
            'servicesWithDocs' => $servicesWithDocs,
            'totalDocuments' => $totalDocuments,
            'mandatoryCount' => $mandatoryCount,
            'optionalCount' => $optionalCount,
            'servicesCount' => $servicesWithDocs->count(),
        ]);
    }

    /**
     * Show document details for a specific service (for modal)
     */
    public function showServiceDocuments($id)
    {
        $service = Service::with(['documentRequirements' => function($query) {
            $query->where('status', 'active')
                  ->with('documentMaster')
                  ->orderBy('type')
                  ->orderBy('order');
        }])
        ->active()
        ->findOrFail($id);

        $mandatoryDocuments = $service->documentRequirements
            ->where('type', 'mandatory')
            ->pluck('documentMaster');

        $optionalDocuments = $service->documentRequirements
            ->where('type', 'optional')
            ->pluck('documentMaster');

        return response()->json([
            'success' => true,
            'service' => $service,
            'mandatoryDocuments' => $mandatoryDocuments,
            'optionalDocuments' => $optionalDocuments,
            'mandatoryCount' => $mandatoryDocuments->count(),
            'optionalCount' => $optionalDocuments->count(),
        ]);
    }

    /**
     * Get document requirements in the format needed for the table layout
     */
    public function getDocumentRequirementsTable()
    {
        $services = Service::whereHas('documentRequirements')
            ->with(['documentRequirements' => function($query) {
                $query->where('status', 'active')
                      ->with('documentMaster')
                      ->select(['service_id', 'document_master_id', 'type'])
                      ->orderBy('type');
            }])
            ->active()
            ->orderBy('service_name')
            ->get()
            ->map(function($service) {
                return [
                    'id' => $service->id,
                    'service_name' => $service->service_name,
                    'service_code' => $service->service_code,
                    'description' => $service->description,
                    'document_count' => $service->documentRequirements->count(),
                    'documents' => $service->documentRequirements->map(function($req) {
                        return [
                            'id' => $req->documentMaster->id,
                            'name' => $req->documentMaster->document_name,
                            'type' => $req->type,
                            'file_type' => $req->documentMaster->file_type,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }

    /* =========================================================
     | PHASE-1: CLIENT DOCUMENT UPLOAD (NO APPROVAL)
     |========================================================= */

    /**
     * Upload or replace client document
     */
    public function upload(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'document_requirement_id' => 'required|exists:document_requirements,id',
            'document_master_id' => 'required|exists:document_masters,id',
            'file' => 'required|file|max:5120', // 5MB
        ]);

        $clientId = Auth::id();
        $file = $request->file('file');

        /**
         * FILE STORAGE LOCATION
         * ----------------------------------
         * storage/app/public/client-documents/{client_id}/{service_id}/
         */
        $path = "client-documents/{$clientId}/{$request->service_id}";
        $storedFilePath = $file->store($path, 'public');

        $upload = ClientDocumentUpload::updateOrCreate(
            [
                'client_id' => $clientId,
                'service_id' => $request->service_id,
                'document_requirement_id' => $request->document_requirement_id,
            ],
            [
                'document_master_id' => $request->document_master_id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $storedFilePath,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'upload_status' => 'uploaded',
                'uploaded_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'data' => [
                'file_name' => $upload->file_name,
                'file_url' => Storage::disk('public')->url($upload->file_path),
                'uploaded_at' => $upload->uploaded_at->format('d M Y, h:i A'),
            ]
        ]);
    }

    /**
     * Get upload progress for a service (helper)
     */
    public function serviceUploadProgress($serviceId)
    {
        $clientId = Auth::id();

        $total = DocumentRequirement::where('service_id', $serviceId)
            ->where('status', 'active')
            ->count();

        $uploaded = ClientDocumentUpload::where('client_id', $clientId)
            ->where('service_id', $serviceId)
            ->count();

        return response()->json([
            'success' => true,
            'uploaded' => $uploaded,
            'total' => $total,
            'percentage' => $total > 0 ? round(($uploaded / $total) * 100) : 0,
        ]);
    }
    /**
 * Get uploaded documents for a specific service
 */
public function getUploadedDocuments($serviceId)
{
    $clientId = Auth::id();
    
    $uploadedDocs = ClientDocumentUpload::where('client_id', $clientId)
        ->where('service_id', $serviceId)
        ->with('documentMaster')
        ->get()
        ->map(function($upload) {
            return [
                'document_requirement_id' => $upload->document_requirement_id,
                'document_master_id' => $upload->document_master_id,
                'file_name' => $upload->file_name,
                'file_path' => $upload->file_path,
                'file_url' => Storage::disk('public')->url($upload->file_path),
                'uploaded_at' => $upload->uploaded_at->format('d M Y, h:i A'),
                'document_name' => $upload->documentMaster->document_name ?? 'Unknown',
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $uploadedDocs
    ]);
}
}
