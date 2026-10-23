<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientDocsController extends Controller
{
    /**
     * ===============================
     * 📄 CLIENT-WISE DOCUMENT VIEW
     * ===============================
     */
    public function index($clientId)
    {
        $client = Client::with(['documents.uploader'])
            ->findOrFail($clientId);

        return view('Backoffice.clients.documents', compact('client'));
    }

    /**
     * ===============================
     * 📤 CLIENT-WISE UPLOAD
     * ===============================
     */
    public function store(Request $request, $clientId)
    {
        $request->validate([
            'documents'   => 'required|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'visibility'  => 'required|in:client_visible,internal'
        ]);

        if (!$request->hasFile('documents')) {
            return back()->with('error', 'No files selected');
        }

        $this->saveDocuments(
            $request->file('documents'),
            $clientId,
            $request->visibility
        );

        return back()->with('success', 'Documents uploaded successfully');
    }

    /**
     * ===============================
     * 🌍 GLOBAL DOCUMENT DASHBOARD
     * ===============================
     */
    public function allDocuments()
    {
        $clients = Client::all();

        $documents = Document::with(['client', 'uploader'])
            ->latest()
            ->get();

        return view('Backoffice.documents.index', compact('clients', 'documents'));
    }

    /**
     * ===============================
     * 🌍 GLOBAL UPLOAD (SELECT CLIENT)
     * ===============================
     */
    public function storeGlobal(Request $request)
    {
        $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'documents'   => 'required|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'visibility'  => 'required|in:client_visible,internal'
        ]);

        if (!$request->hasFile('documents')) {
            return back()->with('error', 'No files selected');
        }

        $this->saveDocuments(
            $request->file('documents'),
            $request->client_id,
            $request->visibility
        );

        return back()->with('success', 'Documents uploaded successfully');
    }

    /**
     * ===============================
     * 🗑 DELETE DOCUMENT (CLIENT-WISE)
     * ===============================
     */
    public function destroy($clientId, $id)
    {
        $doc = Document::where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $this->deleteFile($doc);

        return back()->with('success', 'Document deleted successfully');
    }

    /**
     * ===============================
     * 🗑 GLOBAL DELETE
     * ===============================
     */
    public function destroyGlobal($id)
    {
        $doc = Document::findOrFail($id);

        $this->deleteFile($doc);

        return back()->with('success', 'Document deleted successfully');
    }

    /**
     * ===============================
     * 👁 VIEW / DOWNLOAD (CLIENT-WISE)
     * ===============================
     */
    public function show($clientId, $id)
    {
        $doc = Document::where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        return $this->serveFile($doc);
    }

    /**
     * ===============================
     * 👁 GLOBAL VIEW / DOWNLOAD
     * ===============================
     */
    public function showGlobal($id)
    {
        $doc = Document::findOrFail($id);

        return $this->serveFile($doc);
    }

    /**
     * ===============================
     * 🔁 COMMON: SAVE DOCUMENTS
     * ===============================
     */
    private function saveDocuments($files, $clientId, $visibility)
    {
        foreach ($files as $file) {
            if (!$file->isValid()) continue;

            $path = $file->store('client_internal_documents', 'public');

            Document::create([
                'client_id'   => $clientId,
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => $path,
                'file_type'   => $file->getClientMimeType(),
                'file_size'   => $file->getSize(),
                'visibility'  => $visibility,
                'uploaded_by' => auth()->id(),
            ]);
        }
    }

    /**
     * ===============================
     * 🔁 COMMON: DELETE FILE
     * ===============================
     */
    private function deleteFile($doc)
    {
        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();
    }

    /**
     * ===============================
     * 🔁 COMMON: SERVE FILE
     * ===============================
     */
    private function serveFile($doc)
    {
        $filePath = storage_path('app/public/' . $doc->file_path);

        if (!$doc->file_path || !file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->file($filePath);
    }
}