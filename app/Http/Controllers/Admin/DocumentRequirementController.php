<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequirement;
use App\Models\DocumentMaster;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DocumentRequirementController extends Controller
{
    /**
     * Main page
     */
    public function index()
    {
        $services = Service::active()->orderBy('service_name')->get();

        return view('admin.document-requirements.index', compact('services'));
    }

    /**
     * Get document requirements (grouped by service)
     */
    public function getRequirements(Request $request)
    {
        $type = $request->type;

        $query = DocumentRequirement::with(['service', 'documentMaster'])
            ->orderBy('order');

        if ($type) {
            $query->where('type', $type);
        }

        $data = $query->get()->groupBy('service_id');

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * 🔥 SERVICE-BASED DOCUMENT DROPDOWN (MAIN FIX)
     * Returns only documents NOT already added for the service
     */
    public function getDocumentsByService(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $usedDocumentIds = DocumentRequirement::where('service_id', $request->service_id)
            ->pluck('document_master_id');

        $documents = DocumentMaster::active()
            ->whereNotIn('id', $usedDocumentIds)
            ->orderBy('document_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Store new document requirements
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'documents' => 'required|array|min:1',
            'documents.*.document_master_id' => 'nullable|exists:document_masters,id',
            'documents.*.document_name' => 'nullable|string|max:255',
            'documents.*.type' => 'required|in:mandatory,optional',
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->documents as $order => $doc) {

                // Create custom document if needed
                if (empty($doc['document_master_id']) && !empty($doc['document_name'])) {
                    $documentMaster = DocumentMaster::create([
                        'document_name' => $doc['document_name'],
                        'status' => 'active',
                        'type' => 'custom',
                    ]);

                    $documentMasterId = $documentMaster->id;
                } else {
                    $documentMasterId = $doc['document_master_id'];

                    // Prevent duplicate document for same service
                    $exists = DocumentRequirement::where('service_id', $request->service_id)
                        ->where('document_master_id', $documentMasterId)
                        ->exists();

                    if ($exists) {
                        throw new \Exception('Duplicate document for this service');
                    }
                }

                DocumentRequirement::create([
                    'service_id' => $request->service_id,
                    'document_master_id' => $documentMasterId,
                    'type' => $doc['type'],
                    'status' => 'active',
                    'order' => $order,
                ]);
            }
        });

        Cache::forget('document_requirements_cache');

        return response()->json(['success' => true]);
    }

    /**
     * Update document requirements
     */
    public function update(Request $request, $serviceId)
    {
        $request->validate([
            'documents' => 'required|array',
            'documents.*.id' => 'nullable|exists:document_requirements,id',
            'documents.*.document_master_id' => 'nullable|exists:document_masters,id',
            'documents.*.document_name' => 'nullable|string|max:255',
            'documents.*.type' => 'required|in:mandatory,optional',
            'documents.*.status' => 'required|in:active,inactive',
            'deleted_documents' => 'array',
        ]);

        DB::transaction(function () use ($request, $serviceId) {

            if (!empty($request->deleted_documents)) {
                DocumentRequirement::whereIn('id', $request->deleted_documents)->delete();
            }

            foreach ($request->documents as $order => $doc) {

                if (empty($doc['document_master_id']) && !empty($doc['document_name'])) {
                    $documentMaster = DocumentMaster::create([
                        'document_name' => $doc['document_name'],
                        'status' => 'active',
                        'type' => 'custom',
                    ]);

                    $documentMasterId = $documentMaster->id;
                } else {
                    $documentMasterId = $doc['document_master_id'];

                    $duplicate = DocumentRequirement::where('service_id', $serviceId)
                        ->where('document_master_id', $documentMasterId)
                        ->when(isset($doc['id']), fn ($q) => $q->where('id', '!=', $doc['id']))
                        ->exists();

                    if ($duplicate) {
                        throw new \Exception('Duplicate document detected');
                    }
                }

                DocumentRequirement::updateOrCreate(
                    ['id' => $doc['id'] ?? null],
                    [
                        'service_id' => $serviceId,
                        'document_master_id' => $documentMasterId,
                        'type' => $doc['type'],
                        'status' => $doc['status'],
                        'order' => $order,
                    ]
                );
            }
        });

        Cache::forget('document_requirements_cache');

        return response()->json(['success' => true]);
    }

    /**
     * Toggle document status
     */
    public function toggleStatus($id)
    {
        $doc = DocumentRequirement::findOrFail($id);

        $doc->update([
            'status' => $doc->status === 'active' ? 'inactive' : 'active'
        ]);

        Cache::forget('document_requirements_cache');

        return response()->json(['success' => true]);
    }

    /**
     * Add new document to Document Master
     */
    public function addDocument(Request $request)
    {
        $request->validate([
            'document_name' => 'required|string|max:255|unique:document_masters,document_name'
        ]);

        $document = DocumentMaster::create([
            'document_name' => $request->document_name,
            'status' => 'active',
            'type' => 'custom'
        ]);

        return response()->json([
            'success' => true,
            'document' => $document
        ]);
    }
}