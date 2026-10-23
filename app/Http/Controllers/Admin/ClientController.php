<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\Document;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    /**
     * Client listing
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $credit = $request->get('credit');

        $clients = Client::with(['user', 'creator.role'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($credit, function ($query) use ($credit) {
                if ($credit === 'allowed') {
                    $query->where('is_credit_allowed', 1);
                } elseif ($credit === 'not_allowed') {
                    $query->where('is_credit_allowed', 0);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.clients.index', compact('clients', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'phone'             => 'required|unique:clients,phone',
            'password'          => 'required|string|min:8|confirmed',
            'profile_photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_credit_allowed' => 'required|boolean',
        ]);

        DB::transaction(function () use ($request) {

            $clientRole = Role::where('name', 'client')->firstOrFail();

            // Create login user
            $user = User::create([
                'role_id'  => $clientRole->id,
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'phone'    => $request->phone,
                'status'   => 'active',
            ]);

            // Upload profile photo
            $photoPath = null;
            if ($request->hasFile('profile_photo')) {
                $photoPath = $request->file('profile_photo')
                    ->store('Clients', 'public');
            }

            // Create client profile
            Client::create([
                'user_id'           => $user->id,
                'created_by'        => auth()->id(),
                'name'              => $request->name,
                'email'             => $request->email,
                'phone'             => $request->phone,
                'profile_photo'     => $photoPath,
                'status'            => 'active',
                'is_credit_allowed' => $request->is_credit_allowed,
                'dob'               => $request->dob,
                'gender'            => $request->gender,
                'address'           => $request->address,
                'city'              => $request->city,
                'state'             => $request->state,
                'pincode'           => $request->pincode,
                'company_name'      => $request->company_name,
                'gst_number'        => $request->gst_number,
                'preferred_contact' => $request->preferred_contact,
                'source'            => $request->source,
                'notes'             => $request->notes,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Client created successfully',
        ]);
    }

    /**
     * Update client (ADMIN ONLY)
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'required|unique:clients,phone,' . $client->id,
            'password'          => 'nullable|string|min:8|confirmed',
            'profile_photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_credit_allowed' => 'required|boolean',
        ]);

        DB::transaction(function () use ($request, $client) {

            // Replace profile photo if uploaded
            if ($request->hasFile('profile_photo')) {
                if ($client->profile_photo) {
                    Storage::disk('public')->delete($client->profile_photo);
                }

                $client->profile_photo = $request->file('profile_photo')
                    ->store('Clients', 'public');
            }

            // Update client profile
            $client->update([
                'name'              => $request->name,
                'phone'             => $request->phone,
                'status'            => $request->status,
                'is_credit_allowed' => $request->is_credit_allowed,
                'dob'               => $request->dob,
                'gender'            => $request->gender,
                'address'           => $request->address,
                'city'              => $request->city,
                'state'             => $request->state,
                'pincode'           => $request->pincode,
                'company_name'      => $request->company_name,
                'gst_number'        => $request->gst_number,
                'preferred_contact' => $request->preferred_contact,
                'source'            => $request->source,
                'notes'             => $request->notes,
            ]);

            // Prepare user data
            $userData = [
                'name'   => $request->name,
                'phone'  => $request->phone,
                'status' => $request->status,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // Sync login user
            $client->user->update($userData);
        });

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully',
        ]);
    }

    /**
     * Delete client
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        if ($client->profile_photo) {
            Storage::disk('public')->delete($client->profile_photo);
        }

        $client->delete(); // user auto-deletes via FK cascade

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully',
        ]);
    }

    // ============================================================
    //  CREATE FOLDER (nested support)
    // ============================================================
    public function createFolder(Request $request, $clientId)
    {
        $request->validate([
            'folder_name'  => 'required|string|max:255',
            'current_path' => 'nullable|string|max:1000',
        ]);

        $client = Client::findOrFail($clientId);

        $folderName = trim($request->folder_name);
        $folderName = preg_replace('/[\/\\\\:*?"<>|]/', '', $folderName);

        if (empty($folderName)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid folder name.'
            ]);
        }

        $currentPath = trim($request->current_path ?? '', '/');
        $currentPath = str_replace(['../', '..\\'], '', $currentPath);

        $basePath = 'client_internal_documents/' . $client->id;

        if (!empty($currentPath)) {
            $folderPath = $basePath . '/' . $currentPath . '/' . $folderName;
        } else {
            $folderPath = $basePath . '/' . $folderName;
        }

        if (Storage::disk('public')->exists($folderPath)) {
            return response()->json([
                'success' => false,
                'message' => 'A folder with this name already exists.'
            ]);
        }

        try {
            Storage::disk('public')->makeDirectory($folderPath);
            // 🔥 Create .keep file so folder appears in UI
            Storage::disk('public')->put($folderPath . '/.keep', '');
            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating folder.'
            ]);
        }
    }

    // ============================================================
    //  UPLOAD DOCUMENTS (nested support)
    // ============================================================
public function uploadDocuments(Request $request, $clientId)
{
    $request->validate([
        'documents'   => 'required|array',
        'documents.*' => 'file|max:20480', // 20MB max – কোনো MIME ভ্যালিডেশন নেই
        'visibility'  => 'required|in:client_visible,internal',
        'folder_name' => 'nullable|string|max:1000',
    ]);

    $client = Client::findOrFail($clientId);

    if (!$request->hasFile('documents')) {
        return back()->with('error', 'No files selected.');
    }

    $currentPath = trim($request->folder_name ?? '', '/');
    $currentPath = str_replace(['../', '..\\'], '', $currentPath);

    $basePath = 'client_internal_documents/' . $client->id;
    $folder = !empty($currentPath) ? $basePath . '/' . $currentPath : $basePath;

    if (!Storage::disk('public')->exists($folder)) {
        Storage::disk('public')->makeDirectory($folder);
    }

    foreach ($request->file('documents') as $file) {
        // ✅ এক্সটেনশন চেক – এটাই সবচেয়ে নির্ভরযোগ্য পদ্ধতি
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'xls', 'xlsx', 'csv', 'zip', 'rar'];

        if (!in_array($extension, $allowedExtensions)) {
            return back()->with('error', 'File type not allowed: ' . $file->getClientOriginalName());
        }

        $path = $file->store($folder, 'public');

        Document::create([
            'client_id'   => $client->id,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_type'   => $file->getClientMimeType(),
            'file_size'   => $file->getSize(),
            'visibility'  => $request->visibility,
            'uploaded_by' => auth()->id(),
        ]);
    }

    return back()->with('success', 'Documents uploaded successfully.');
}

    // ============================================================
    //  RENAME FOLDER (nested support)
    // ============================================================
    public function renameFolder(Request $request, $clientId)
    {
        $request->validate([
            'old_name'     => 'required|string|max:255',
            'new_name'     => 'required|string|max:255',
            'current_path' => 'nullable|string|max:1000',
        ]);

        $client = Client::findOrFail($clientId);

        $oldName = trim($request->old_name);
        $newName = trim($request->new_name);

        $oldName = preg_replace('/[\/\\\\:*?"<>|]/', '', $oldName);
        $newName = preg_replace('/[\/\\\\:*?"<>|]/', '', $newName);

        if (empty($newName)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid folder name.'
            ]);
        }

        $currentPath = trim($request->current_path ?? '', '/');
        $currentPath = str_replace(['../', '..\\'], '', $currentPath);

        $basePath = 'client_internal_documents/' . $client->id;

        if (!empty($currentPath)) {
            $oldPath = $basePath . '/' . $currentPath . '/' . $oldName;
            $newPath = $basePath . '/' . $currentPath . '/' . $newName;
        } else {
            $oldPath = $basePath . '/' . $oldName;
            $newPath = $basePath . '/' . $newName;
        }

        if (!Storage::disk('public')->exists($oldPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Folder does not exist.'
            ]);
        }

        if (Storage::disk('public')->exists($newPath)) {
            return response()->json([
                'success' => false,
                'message' => 'A folder with this name already exists.'
            ]);
        }

        try {
            DB::transaction(function () use ($oldPath, $newPath, $client) {
                Storage::disk('public')->move($oldPath, $newPath);

                $documents = Document::where('client_id', $client->id)
                    ->where('file_path', 'LIKE', $oldPath . '%')
                    ->get();

                foreach ($documents as $document) {
                    $document->file_path = str_replace($oldPath, $newPath, $document->file_path);
                    $document->save();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Folder renamed successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error renaming folder.'
            ]);
        }
    }

    // ============================================================
    //  DELETE FOLDER (nested support)
    // ============================================================
    public function deleteFolder(Request $request, $clientId)
    {
        $request->validate([
            'folder_name'  => 'required|string|max:1000',
            'current_path' => 'nullable|string|max:1000',
        ]);

        $client = Client::findOrFail($clientId);

        $folderName = trim($request->folder_name);
        $folderName = preg_replace('/[\/\\\\:*?"<>|]/', '', $folderName);

        $currentPath = trim($request->current_path ?? '', '/');
        $currentPath = str_replace(['../', '..\\'], '', $currentPath);

        $basePath = 'client_internal_documents/' . $client->id;

        if (!empty($currentPath)) {
            $folderPath = $basePath . '/' . $currentPath . '/' . $folderName;
        } else {
            $folderPath = $basePath . '/' . $folderName;
        }

        if (!Storage::disk('public')->exists($folderPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Folder does not exist.'
            ]);
        }

        try {
            DB::transaction(function () use ($folderPath, $clientId) {
                Document::where('client_id', $clientId)
                    ->where('file_path', 'LIKE', $folderPath . '%')
                    ->delete();

                Storage::disk('public')->deleteDirectory($folderPath);
            });

            return response()->json([
                'success' => true,
                'message' => 'Folder deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting folder.'
            ]);
        }
    }

    // ============================================================
    //  DELETE DOCUMENT
    // ============================================================
    public function deleteDocument($docId)
    {
        $doc = Document::findOrFail($docId);
        $doc->delete();
        return back()->with('success', 'Document deleted successfully');
    }

    // ============================================================
    //  SHOW CLIENT – builds a merged tree from DB + filesystem
    // ============================================================
    public function show($id)
    {
        $client = Client::with([
            'orders.payments',
            'orders.service',
            'orders.documents',
            'orders.tasks.assignedStaff',
            'payments',
            'documents'
        ])->findOrFail($id);

        // Build a complete folder tree (database + filesystem)
        $folderTree = $this->buildFolderTree($client->id);

        return view('admin.clients.show', compact('client', 'folderTree'));
    }

    /**
     * Recursively build a nested folder tree.
     * Merges folders from database documents and physical directories.
     */
    private function buildFolderTree($clientId)
    {
        $basePath = 'client_internal_documents/' . $clientId;
        $disk = Storage::disk('public');
        $tree = [];

        // 1. Add folders & files from database
        $documents = Document::where('client_id', $clientId)->get();
        foreach ($documents as $doc) {
            $parts = explode('/', $doc->file_path);
            $relParts = array_slice($parts, 2); // remove "client_internal_documents" and clientId
            $current = &$tree;
            foreach ($relParts as $index => $part) {
                if ($index === count($relParts) - 1) {
                    // file
                    if (!isset($current['_files'])) $current['_files'] = [];
                    $current['_files'][] = $doc;
                } else {
                    // folder
                    if (!isset($current[$part])) $current[$part] = [];
                    $current = &$current[$part];
                }
            }
        }

        // 2. Scan filesystem for extra empty folders
        if ($disk->exists($basePath)) {
            $allDirs = $disk->allDirectories($basePath);
            foreach ($allDirs as $dir) {
                $relPath = trim(str_replace($basePath, '', $dir), '/');
                if (empty($relPath)) continue;
                $parts = explode('/', $relPath);
                $current = &$tree;
                $exists = true;
                foreach ($parts as $part) {
                    if (!isset($current[$part])) {
                        $exists = false;
                        break;
                    }
                    $current = &$current[$part];
                }
                if (!$exists) {
                    // add missing folder path
                    $current = &$tree;
                    foreach ($parts as $part) {
                        if (!isset($current[$part])) {
                            $current[$part] = [];
                        }
                        $current = &$current[$part];
                    }
                }
            }
        }

        return $tree;
    }

    // ============================================================
    //  BULK OPERATIONS
    // ============================================================
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'clients' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->clients as $clientData) {
                $client = Client::find($clientData['id']);
                if (!$client) continue;

                $client->update([
                    'status'              => $clientData['status'] ?? $client->status,
                    'is_credit_allowed'   => $clientData['is_credit_allowed'] ?? $client->is_credit_allowed,
                    'city'                => $clientData['city'] ?? $client->city,
                    'state'               => $clientData['state'] ?? $client->state,
                    'preferred_contact'   => $clientData['preferred_contact'] ?? $client->preferred_contact,
                    'notes'               => $clientData['notes'] ?? $client->notes,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Selected clients updated successfully.'
        ]);
    }

    public function bulkEdit(Request $request)
    {
        $request->validate([
            'client_ids'   => 'required|array',
            'client_ids.*' => 'exists:clients,id',
        ]);

        $clients = Client::whereIn('id', $request->client_ids)->get();

        return response()->json([
            'success' => true,
            'clients' => $clients
        ]);
    }
}