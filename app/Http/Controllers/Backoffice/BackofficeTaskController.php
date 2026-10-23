<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskDocument;
use App\Models\TaskMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackofficeTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /*
    |--------------------------------------------------------------------------
    | List Tasks - Main Page
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $staff = Auth::user()->staff;
        abort_if(!$staff, 403);

        $filter = $request->get('type', 'active');
        
        $query = Task::with(['service', 'order.client'])
            ->where('assigned_to', $staff->id);

        switch ($filter) {
            case 'today':
                $query->whereDate('due_date', now());
                break;
                
            case 'overdue':
                $query->whereDate('due_date', '<', now())
                      ->whereIn('status', ['assigned', 'in_progress', 'waiting_client']);
                break;
                
            case 'completed':
                $query->whereIn('status', ['submitted', 'verified']);
                break;
                
            case 'waiting':
                $query->where('status', 'waiting_client');
                break;
                
            case 'active':
            default:
                $query->whereNotIn('status', ['verified', 'submitted']);
                break;
        }

        $tasks = $query->orderBy('priority', 'desc')
                      ->orderBy('due_date')
                      ->orderBy('created_at')
                      ->get();

        // Stats for the dashboard
        $stats = [
            'total' => Task::where('assigned_to', $staff->id)->count(),
            'today' => Task::where('assigned_to', $staff->id)->whereDate('due_date', now())->count(),
            'overdue' => Task::where('assigned_to', $staff->id)
                ->whereDate('due_date', '<', now())
                ->whereIn('status', ['assigned', 'in_progress', 'waiting_client'])
                ->count(),
            'completed' => Task::where('assigned_to', $staff->id)
                ->whereIn('status', ['submitted', 'verified'])
                ->count(),
            'active' => Task::where('assigned_to', $staff->id)
                ->whereNotIn('status', ['verified', 'submitted'])
                ->count(),
        ];

        return view('Backoffice.tasks.index', compact('tasks', 'stats', 'filter'));
    }

    /*
    |--------------------------------------------------------------------------
    | Get Task Details (AJAX - for modal)
    |--------------------------------------------------------------------------
    */
    public function getTaskDetails($taskId)
{
try {


    $staff = Auth::user()->staff;
    abort_if(!$staff, 403);

    $task = Task::with([
        'service',
        'order.client',

        // verified client documents
        'order.documents' => function ($q) {
            $q->whereIn('status', ['VERIFIED','APPROVED']);
        },

        'order.documents.documentMaster',
        'order.documents.files',
        'order.documents.uploadedBy',

        // work process relations
        'workProcessStep.workProcess',

        // task uploaded documents
        'documents' => function ($query) {
            $query->orderBy('created_at','desc');
        },

        // chat messages
        'messages' => function ($query) {
            $query->with('sender')->orderBy('created_at','asc');
        }

    ])
    ->where('id',$taskId)
    ->where('assigned_to',$staff->id)
    ->first();

    if (!$task) {
        return response()->json([
            'success' => false,
            'message' => 'Task not found or permission denied.'
        ],404);
    }

    return response()->json([
        'success' => true,
        'task' => [

            'id' => $task->id,
            'task_name' => $task->task_name,
            'step_name' => $task->step_name,

            'priority' => $task->priority,
            'status' => $task->status,
            'status_label' => ucfirst(str_replace('_',' ',$task->status)),

            'due_date' => optional($task->due_date)->format('M d, Y'),
            'due_date_raw' => optional($task->due_date)->format('Y-m-d'),

            'created_at' => $task->created_at->format('M d, Y H:i'),
            'started_at' => $task->started_at ? $task->started_at->format('M d, Y H:i') : null,
            'submitted_at' => $task->submitted_at ? $task->submitted_at->format('M d, Y H:i') : null,

            'client_name' => $task->order->client->name ?? 'N/A',
            'client_email' => $task->order->client->email ?? 'N/A',

            'service_name' => $task->service->name ?? 'N/A',

            // WORK PROCESS INFO
            'work_process_name' => optional($task->workProcessStep->workProcess)->name ?? 'N/A',
            'work_process_step' => optional($task->workProcessStep)->step_name ?? 'N/A',
            'client_instruction' => optional($task->workProcessStep)->client_instruction ?? null,

            // RENEWAL
            'renewal_cycle' => $task->renewal_no ?? 0,

            // ADMIN DATA
            'admin_notes' => $task->admin_notes ?? 'No admin notes',
            'remarks' => $task->remarks ?? 'No remarks',

            // CLIENT DOCUMENTS
            'client_documents' => $task->order->documents->map(function ($doc) {

                return [
                    'document_name' =>
                        optional($doc->documentMaster)->document_name ??
                        optional($doc->documentMaster)->name ??
                        'Document',

                    'uploaded_by' =>
                        optional($doc->uploadedBy)->name ?? 'Marketing Staff',

                    'files' => $doc->files->map(function ($file) {

                        return [
                            'file_name' => $file->file_name,
                            'file_size' => $this->formatFileSize($file->file_size),
                            'download_url' => asset('storage/'.$file->file_path)
                        ];

                    })

                ];

            }),

            // TASK DOCUMENTS
            'task_documents' => $task->documents->map(function ($doc) {

                return [
                    'id' => $doc->id,
                    'document_name' => $doc->document_name,
                    'file_size' => $this->formatFileSize($doc->file_size),
                    'uploaded_at' => $doc->created_at->format('M d, Y H:i'),
                    'status' => $doc->status,
                    'download_url' => route('backoffice.tasks.download',$doc->id)
                ];

            }),

            // CHAT MESSAGES
            'messages' => $task->messages->map(function ($msg) {

                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_name' => $msg->sender->name ?? 'Unknown',
                    'sender_role' => $msg->sender_role,
                    'sent_at' => $msg->created_at->format('M d, Y H:i'),
                    'is_me' => $msg->sender_id === Auth::id()
                ];

            })

        ]
    ]);

} catch (\Exception $e) {

    \Log::error('Error fetching task details: '.$e->getMessage());

    return response()->json([
        'success' => false,
        'message' => 'Error loading task details'
    ],500);
}


}

    /*
    |--------------------------------------------------------------------------
    | Start Task (AJAX)
    |--------------------------------------------------------------------------
    */
    public function start($taskId)
    {
        try {
            $staff = Auth::user()->staff;
            
            $task = Task::where('id', $taskId)
                ->where('assigned_to', $staff->id)
                ->whereIn('status', ['assigned', 'waiting_client'])
                ->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found or cannot be started.'
                ], 404);
            }

            $task->update([
                'status' => 'in_progress',
                'started_at' => now(),
                'is_new_for_staff' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task started successfully!',
                'status' => 'in_progress'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error starting task: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Mark as Waiting for Client (AJAX)
    |--------------------------------------------------------------------------
    */
    public function markWaiting($taskId)
    {
        try {
            $staff = Auth::user()->staff;

            $task = Task::where('id', $taskId)
                ->where('assigned_to', $staff->id)
                ->where('status', 'in_progress')
                ->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found or cannot be marked as waiting.'
                ], 404);
            }

            $task->update([
                'status' => 'waiting_client',
                'is_new_for_staff' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task marked as waiting for client!',
                'status' => 'waiting_client'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating task status: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Submit Task (AJAX)
    |--------------------------------------------------------------------------
    */
    public function submit($taskId)
    {
        try {
            $staff = Auth::user()->staff;

            $task = Task::where('id', $taskId)
                ->where('assigned_to', $staff->id)
                ->whereIn('status', [
                    'assigned',
                    'in_progress',
                    'waiting_client',
                    'rejected'
                ])
                ->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found or cannot be submitted.'
                ], 404);
            }

            $task->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'is_new_for_staff' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task submitted successfully!',
                'status' => 'submitted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting task: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Document (AJAX)
    |--------------------------------------------------------------------------
    */
public function uploadDocument(Request $request, $taskId)
{
try {


    $request->validate([
        'documents' => 'required|array|min:1',
        'documents.*' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,xlsm,zip,rar,json',
        'document_name' => 'nullable|string|max:255'
    ]);

    $staff = Auth::user()->staff;

    $task = Task::where('id', $taskId)
        ->where('assigned_to', $staff->id)
        ->first();

    if (!$task) {
        return response()->json([
            'success' => false,
            'message' => 'Task not found.'
        ], 404);
    }

    $uploadedDocs = [];
    $baseName = $request->document_name;

    $files = $request->file('documents');

    foreach ($files as $index => $file) {

        // Decide document name
        if ($baseName && count($files) === 1) {
            $fileName = $baseName . '.' . $file->getClientOriginalExtension();
        } else {
            $fileName = $file->getClientOriginalName();
        }

        $storagePath = 'Backoffice_verified_Documents';

        if (!Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->makeDirectory($storagePath);
        }

        $path = $file->store($storagePath, 'public');

        $document = TaskDocument::create([
            'task_id' => $task->id,
            'uploaded_by' => Auth::id(),
            'document_name' => $fileName,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'uploaded',
        ]);

        $uploadedDocs[] = [
            'id' => $document->id,
            'document_name' => $document->document_name,
            'file_size' => $this->formatFileSize($document->file_size),
            'uploaded_at' => $document->created_at->format('M d, Y H:i'),
            'status' => $document->status,
            'download_url' => route('backoffice.tasks.download', $document->id)
        ];
    }

    return response()->json([
        'success' => true,
        'message' => count($uploadedDocs) . ' document(s) uploaded successfully.',
        'documents' => $uploadedDocs
    ]);

} catch (\Exception $e) {

    return response()->json([
        'success' => false,
        'message' => 'Error uploading documents: ' . $e->getMessage()
    ], 500);
}


}


    /*
    |--------------------------------------------------------------------------
    | Download Document
    |--------------------------------------------------------------------------
    */
    public function downloadDocument($documentId)
    {
        try {
            $staff = Auth::user()->staff;
            
            $document = TaskDocument::findOrFail($documentId);
            
            // Verify the task belongs to the staff member
            $task = Task::where('id', $document->task_id)
                ->where('assigned_to', $staff->id)
                ->first();

            if (!$task) {
                abort(403, 'You do not have permission to download this document.');
            }

            if (!Storage::disk('public')->exists($document->file_path)) {
                abort(404, 'File not found');
            }

            return Storage::disk('public')->download($document->file_path, $document->document_name);
        } catch (\Exception $e) {
            abort(404, 'Document not found: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Send Message (AJAX)
    |--------------------------------------------------------------------------
    */
    public function sendMessage(Request $request, $taskId)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $staff = Auth::user()->staff;
            
            $task = Task::where('id', $taskId)
                ->where('assigned_to', $staff->id)
                ->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.'
                ], 404);
            }

            $message = TaskMessage::create([
                'task_id' => $taskId,
                'sender_id' => Auth::id(),
                'sender_role' => 'backoffice',
                'message' => $request->message,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!',
                'chat_message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_name' => Auth::user()->name,
                    'sender_role' => 'backoffice',
                    'sent_at' => $message->created_at->format('M d, Y H:i'),
                    'is_me' => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Task Remarks (AJAX)
    |--------------------------------------------------------------------------
    */
    public function updateRemarks(Request $request, $taskId)
    {
        try {
            $request->validate([
                'remarks' => 'nullable|string|max:2000',
            ]);

            $staff = Auth::user()->staff;

            $task = Task::where('id', $taskId)
                ->where('assigned_to', $staff->id)
                ->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.'
                ], 404);
            }

            $task->update([
                'remarks' => $request->remarks
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Remarks updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating remarks: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /*
    |--------------------------------------------------------------------------
    | Renew Service (AJAX)
    |--------------------------------------------------------------------------
    */
    public function renewService(Request $request, $taskId)
    {
        try {
            $staff = Auth::user()->staff;
            abort_if(!$staff, 403);

            $baseTask = Task::where('id', $taskId)
                ->where('assigned_to', $staff->id)
                ->first();

            if (!$baseTask) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found or no permission.'
                ], 404);
            }

            // Check if ALL tasks for this service and current renewal cycle are verified
            $unverifiedTasksExist = Task::where('order_id', $baseTask->order_id)
                ->where('service_id', $baseTask->service_id)
                ->where('work_process_id', $baseTask->work_process_id)
                ->where('renewal_no', $baseTask->renewal_no)
                ->where('status', '!=', 'verified')
                ->exists();

            if ($unverifiedTasksExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Renewal allowed only after all service tasks in current cycle are verified.'
                ], 422);
            }

            DB::beginTransaction();

            $maxRenewalNo = Task::where('order_id', $baseTask->order_id)
                ->where('service_id', $baseTask->service_id)
                ->max('renewal_no');

            $nextRenewalNo = ($maxRenewalNo ?? 0) + 1;

            // Base steps from cycle 0
            $baseSteps = Task::where('order_id', $baseTask->order_id)
                ->where('service_id', $baseTask->service_id)
                ->where('work_process_id', $baseTask->work_process_id)
                ->where('renewal_no', 0)
                ->orderBy('work_process_step_id')
                ->get();

            if ($baseSteps->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No base steps found to renew.'
                ], 422);
            }

            foreach ($baseSteps as $stepTask) {
                Task::create([
                    'order_id' => $stepTask->order_id,
                    'service_id' => $stepTask->service_id,
                    'work_process_id' => $stepTask->work_process_id,
                    'work_process_step_id' => $stepTask->work_process_step_id,
                    'task_name' => $stepTask->task_name,
                    'step_name' => $stepTask->step_name,
                    'assigned_to' => $staff->id,
                    'priority' => $stepTask->priority,
                    'due_date' => null,
                    'status' => 'assigned',
                    'is_new_for_staff' => 1,
                    'is_renewal' => 1,
                    'renewal_no' => $nextRenewalNo,
                    'parent_cycle_task_id' => $baseTask->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Renewal created successfully (Renewal #{$nextRenewalNo})",
                'renewal_no' => $nextRenewalNo
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error creating renewal: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Check Renewal Eligibility (AJAX)
    |--------------------------------------------------------------------------
    */
    public function checkRenewEligibility($taskId)
    {
        $staff = Auth::user()->staff;
        abort_if(!$staff, 403);

        $baseTask = Task::where('id', $taskId)
            ->where('assigned_to', $staff->id)
            ->first();

        if (!$baseTask) {
            return response()->json([
                'success' => true,
                'allowed' => false,
                'message' => 'Task not found or no permission.'
            ]);
        }

        // Check if ALL tasks for this service and renewal cycle are verified
        $unverifiedTasksExist = Task::where('order_id', $baseTask->order_id)
            ->where('service_id', $baseTask->service_id)
            ->where('work_process_id', $baseTask->work_process_id)
            ->where('renewal_no', $baseTask->renewal_no)
            ->where('status', '!=', 'verified')
            ->exists();

        return response()->json([
            'success' => true,
            'allowed' => !$unverifiedTasksExist,
            'message' => $unverifiedTasksExist
                ? 'Renewal allowed only after all service tasks in current cycle are verified.'
                : 'Renewal allowed'
        ]);
    }



/*
|--------------------------------------------------------------------------
| Replace Document (AJAX)
|--------------------------------------------------------------------------
*/
public function replaceDocument(Request $request, $documentId)
{
    try {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
            'document_name' => 'nullable|string|max:255'
        ]);

        $staff = Auth::user()->staff;
        
        $document = TaskDocument::findOrFail($documentId);
        
        // Verify the task belongs to the staff member
        $task = Task::where('id', $document->task_id)
            ->where('assigned_to', $staff->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to replace this document.'
            ], 403);
        }

        $file = $request->file('document');
        $fileName = $request->document_name ?: $file->getClientOriginalName();
        
        // Delete the old file
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        // Store the new file (same directory)
        $storagePath = 'Backoffice_verified_Documents';
        $path = $file->store($storagePath, 'public');
        
        // Update document record
        $document->update([
            'document_name' => $fileName,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'uploaded', // or keep previous status? Usually 'replaced' could be added.
            'uploaded_by' => Auth::id(), // track who replaced it
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document replaced successfully!',
            'document' => [
                'id' => $document->id,
                'document_name' => $document->document_name,
                'file_size' => $this->formatFileSize($document->file_size),
                'uploaded_at' => $document->created_at->format('M d, Y H:i'),
                'status' => $document->status,
                'download_url' => route('backoffice.tasks.download', $document->id)
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error replacing document: ' . $e->getMessage()
        ], 500);
    }
}
    /*
    |--------------------------------------------------------------------------
    | Helper function to format file size
    |--------------------------------------------------------------------------
    */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}