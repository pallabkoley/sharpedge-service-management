@extends('layouts.Backoffice')

@section('page-title', 'My Tasks')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TOTAL TASKS</h6>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats['total'] }}</h3>
                <div class="card-change up">
                    <i class="bi bi-list-task me-1"></i>
                    <span>All assigned tasks</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">ACTIVE</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats['active'] }}</h3>
                <div class="card-change up">
                    <i class="bi bi-hourglass-split me-1"></i>
                    <span>In progress / waiting</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">OVERDUE</h6>
                    <div class="card-icon bg-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats['overdue'] }}</h3>
                <div class="card-change down">
                    <i class="bi bi-arrow-down me-1"></i>
                    <span>Needs attention</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">COMPLETED</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats['completed'] }}</h3>
                <div class="card-change up">
                    <i class="bi bi-arrow-up me-1"></i>
                    <span>Submitted & verified</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="content-section">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">My Tasks</h4>
                <div class="d-flex align-items-center">
                    <!-- DataTables controls will be added automatically -->
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('backoffice.tasks.index') }}" 
                   class="btn btn-sm {{ $filter == 'active' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Active
                </a>
                <a href="{{ route('backoffice.tasks.index', ['type' => 'today']) }}" 
                   class="btn btn-sm {{ $filter == 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Today
                </a>
                <a href="{{ route('backoffice.tasks.index', ['type' => 'overdue']) }}" 
                   class="btn btn-sm {{ $filter == 'overdue' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Overdue
                </a>
                <a href="{{ route('backoffice.tasks.index', ['type' => 'waiting']) }}" 
                   class="btn btn-sm {{ $filter == 'waiting' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Waiting
                </a>
                <a href="{{ route('backoffice.tasks.index', ['type' => 'completed']) }}" 
                   class="btn btn-sm {{ $filter == 'completed' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Completed
                </a>
            </div>

            @if($tasks->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 48px;"></i>
                    <h4 class="mt-3">No Tasks Found</h4>
                    <p class="text-muted">You have no tasks in this category.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="tasksTable">
                        <thead>
                            <tr>
                                <th>TASK CODE</th>
                                <th>Task Name</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Renewal</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td class="fw-bold">{{ $task->task_code ?? 'TASK-' . str_pad($task->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $task->task_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $task->step_name ?? '' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $task->order->client->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $task->order->client->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            {{ $task->service->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->renewal_no == 0)
                                            <span class="badge bg-success">New</span>
                                        @else
                                            <span class="badge bg-warning">Renewal #{{ $task->renewal_no }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->priority == 'high')
                                            <span class="priority-badge priority-high">High</span>
                                        @elseif($task->priority == 'medium')
                                            <span class="priority-badge priority-medium">Medium</span>
                                        @else
                                            <span class="priority-badge priority-low">Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->status == 'assigned')
                                            <span class="status-badge status-pending">Assigned</span>
                                        @elseif($task->status == 'in_progress')
                                            <span class="status-badge status-in-progress">In Progress</span>
                                        @elseif($task->status == 'waiting_client')
                                            <span class="status-badge status-waiting">Waiting</span>
                                        @elseif($task->status == 'submitted')
                                            <span class="status-badge status-completed">Submitted</span>
                                        @elseif($task->status == 'verified')
                                            <span class="status-badge status-completed">Verified</span>
                                        @else
                                            <span class="status-badge">{{ ucfirst($task->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}</div>
                                        @if($task->due_date && $task->due_date < now() && !in_array($task->status, ['submitted', 'verified']))
                                            <small class="text-danger">Overdue</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="task-actions">
                                            <button type="button" class="btn-task-action view-task-btn" 
                                                    data-task-id="{{ $task->id }}" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            @if($task->status == 'assigned' || $task->status == 'waiting_client')
                                                <button type="button" class="btn-task-action start-task-btn" 
                                                        data-task-id="{{ $task->id }}" title="Start Task">
                                                    <i class="bi bi-play-fill"></i>
                                                </button>
                                            @endif

                                            @if($task->status == 'in_progress')
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn-task-action dropdown-toggle" type="button" 
                                                            data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item wait-task-btn" href="#" 
                                                               data-task-id="{{ $task->id }}">
                                                                <i class="bi bi-clock me-2"></i> Mark as Waiting
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item submit-task-btn" href="#" 
                                                               data-task-id="{{ $task->id }}">
                                                                <i class="bi bi-check-lg me-2"></i> Submit Task
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Task count display -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <small class="text-muted">
                Showing {{ $tasks->count() }} task(s)
            </small>
        </div>
        <div>
            @if($tasks->count() > 0)
                <small class="text-muted">
                    {{ $stats['overdue'] }} overdue, {{ $stats['today'] }} due today
                </small>
            @endif
        </div>
    </div>

    <!-- Task Details Modal -->
    <div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-labelledby="taskDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskDetailsModalLabel">Task Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be dynamically populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Document Modal (also used for Replace) -->
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadDocumentForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="replace_document_id" name="replace_document_id" value="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="document_name" class="form-label">Document Name (Optional)</label>
                            <input type="text" class="form-control" id="document_name" name="document_name" 
                                   placeholder="Leave empty to use original filename">
                            <small class="text-muted">For multiple files, the same name will be used for all (original filenames will be preserved).</small>
                        </div>
                        <div class="mb-3">
                            <label for="document_file" class="form-label">Select Files</label>
                            <input type="file" class="form-control" id="document_file" name="documents[]" multiple required>
                            <div class="form-text">
                                Allowed file types: PDF, JPG, JPEG, PNG, DOC, DOCX, XLS, XLSX. Max size per file: 10MB.<br>
                                You can select multiple files.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Document(s)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let currentTaskId = null;
    let taskModalInstance = null;
    let uploadModalInstance = null;
    let currentTaskData = null;
    let dataTable = null;

    $(document).ready(function() {
        console.log("Backoffice task JS loaded");

        // Initialize DataTables
        if ($('#tasksTable').length && !$.fn.DataTable.isDataTable('#tasksTable')) {
            dataTable = $('#tasksTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                ordering: true,
                searching: true,
                info: true,
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [8] } // Actions column
                ],
                language: {
                    search: "Search tasks:",
                    info: "Showing _START_ to _END_ of _TOTAL_ tasks",
                    infoEmpty: "No tasks to show",
                    zeroRecords: "No matching tasks found"
                }
            });
        }

        // Initialize modals
        const taskModalEl = document.getElementById('taskDetailsModal');
        if (taskModalEl) {
            taskModalInstance = new bootstrap.Modal(taskModalEl);
        }

        const uploadModalEl = document.getElementById('uploadDocumentModal');
        if (uploadModalEl) {
            uploadModalInstance = new bootstrap.Modal(uploadModalEl);
        }

        // Event listeners
        $(document).on('click', '.view-task-btn', function() {
            currentTaskId = $(this).data('task-id');
            console.log("View clicked for task:", currentTaskId);
            loadTaskDetails(currentTaskId);
        });

        $(document).on('click', '.start-task-btn', function() {
            const taskId = $(this).data('task-id');
            startTask(taskId);
        });

        $(document).on('click', '.wait-task-btn', function(e) {
            e.preventDefault();
            const taskId = $(this).data('task-id');
            markAsWaiting(taskId);
        });

        $(document).on('click', '.submit-task-btn', function(e) {
            e.preventDefault();
            const taskId = $(this).data('task-id');
            submitTask(taskId);
        });

        $(document).on('click', '#update-remarks-btn', function() {
            if (!currentTaskId) return;
            updateRemarks();
        });

        $(document).on('click', '#send-message-btn', function() {
            if (!currentTaskId) return;
            sendMessage();
        });

        $(document).on('keypress', '#chat-message', function(e) {
            if (e.which == 13 && !e.shiftKey) {
                e.preventDefault();
                if (!currentTaskId) return;
                sendMessage();
            }
        });

        $('#uploadDocumentForm').on('submit', function(e) {
            e.preventDefault();
            uploadDocument();
        });

        $('#uploadDocumentModal').on('show.bs.modal', function() {
            if (!currentTaskId) {
                alert('Please select a task first');
                return false;
            }
        });

        // Reset modal state when hidden
        $('#uploadDocumentModal').on('hidden.bs.modal', function () {
            $('#replace_document_id').val('');
            $('#uploadDocumentModalLabel').text('Upload Document');
            $('#document_name').val('');
            // Re-enable multiple selection
            $('#document_file').attr('multiple', 'multiple');
            $('#document_file').val('');
        });

        $(document).on('click', '#renew-service-btn', function() {
            if (!currentTaskId) return;
            renewService();
        });

        // Replace document button handler
        $(document).on('click', '.replace-document-btn', function() {
            const docId = $(this).data('document-id');
            const docName = $(this).data('document-name');
            
            $('#replace_document_id').val(docId);
            $('#document_name').val(docName);
            $('#uploadDocumentModalLabel').text('Replace Document');
            
            // Disable multiple selection for replace (single file only)
            $('#document_file').removeAttr('multiple');
            
            uploadModalInstance.show();
        });
    });

    // ========== Task Details ==========
    function loadTaskDetails(taskId) {
        const url = "{{ route('backoffice.tasks.details', ':id') }}".replace(':id', taskId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#taskDetailsModal .modal-body').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading task details...</p>
                    </div>
                `);
                if (taskModalInstance) {
                    taskModalInstance.show();
                }
            },
            success: function(response) {
                if (response.success) {
                    currentTaskData = response.task;
                    displayTaskDetails(response.task);
                    checkRenewalEligibility(taskId);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error("Error loading task details:", xhr);
                let errorMessage = 'Error loading task details';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            }
        });
    }

    function displayTaskDetails(task) {
        const modalBody = $('#taskDetailsModal .modal-body');

        // Build the modal content
        modalBody.html(`
            <div class="row">
                <!-- Left Column - Task Info & Documents -->
                <div class="col-md-8">
                    <!-- Task Information Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Task Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Task Code:</strong>
                                    <p class="mb-1">${task.task_code || 'TASK-' + task.id}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Task Name:</strong>
                                    <p class="mb-1">${task.task_name}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Step:</strong>
                                    <p class="mb-1">${task.step_name}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Client:</strong>
                                    <p class="mb-1">${task.client_name} (${task.client_email})</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Service:</strong>
                                    <p class="mb-1">${task.service_name}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Priority:</strong>
                                    <span>${getPriorityBadge(task.priority)}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <span>${getStatusBadge(task.status, task.status_label)}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Due Date:</strong>
                                    <p class="mb-1">${task.due_date}</p>
                                </div>
                            </div>
                            <!-- Renewal Information (FIX #1: use renewal_cycle) -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Renewal Cycle:</strong>
                                    <p class="mb-1">${task.renewal_cycle === 0 ? 'Original (Cycle 0)' : `Renewal Cycle #${task.renewal_cycle}`}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Renewal Type:</strong>
                                    <p class="mb-1">${task.is_renewal ? 'Renewal Task' : 'Original Task'}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Work Process Step Details Card (FIX #2: condition changed, passes task) -->
                    ${task.work_process_step ? `
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Work Process Step Details</h6>
                            </div>
                            <div class="card-body">
                                ${renderWorkProcessStep(task)}
                            </div>
                        </div>
                    ` : ''}

                    <!-- Admin Notes Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Admin Notes</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">${task.admin_notes}</p>
                        </div>
                    </div>

                    <!-- Verified Client Documents Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Verified Client Documents</h6>
                        </div>
                        <div class="card-body" id="client-documents-container">
                            ${renderClientDocuments(task.client_documents)}
                        </div>
                    </div>

                    <!-- Task Documents Card -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Task Documents</h6>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                    data-bs-target="#uploadDocumentModal">
                                <i class="bi bi-upload"></i> Upload
                            </button>
                        </div>
                        <div class="card-body" id="task-documents-container">
                            ${renderTaskDocuments(task.task_documents)}
                        </div>
                    </div>
                </div>

                <!-- Right Column - Actions & Chat -->
                <div class="col-md-4">
                    <!-- Task Actions Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Task Actions</h6>
                        </div>
                        <div class="card-body">
                            <div id="task-actions" class="d-grid gap-2">
                                ${getTaskActionsHTML(task)}
                            </div>
                            <!-- Renew Service Section -->
                            <div id="renewal-section" class="mt-3" style="display: none;">
                                <div class="d-grid gap-2">
                                    <button type="button" id="renew-service-btn" class="btn btn-info">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Renew Service
                                    </button>
                                    <small id="renewal-hint" class="text-muted text-center"></small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <textarea id="task-remarks" class="form-control" rows="3" 
                                          placeholder="Add your remarks here...">${task.remarks || ''}</textarea>
                                <button type="button" id="update-remarks-btn" class="btn btn-sm btn-primary mt-2">
                                    Update Remarks
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Card -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Messages</h6>
                        </div>
                        <div class="card-body p-0">
                            <div id="chat-messages" style="height: 300px; overflow-y: auto; padding: 15px;">
                                ${renderMessages(task.messages)}
                            </div>
                            <div class="p-3 border-top">
                                <div class="input-group">
                                    <input type="text" id="chat-message" class="form-control" 
                                           placeholder="Type your message...">
                                    <button class="btn btn-primary" type="button" id="send-message-btn">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);

        $('#taskDetailsModalLabel').text('Task Details: ' + (task.task_code || 'TASK-' + task.id));
    }

    // ========== Helper Rendering Functions ==========
    // FIX #3: renderWorkProcessStep with clickable URLs (HTML escaped + link conversion)
    function renderWorkProcessStep(task) {
        // Escape HTML special characters to prevent XSS
        let instruction = task.client_instruction || 'No instruction provided';
        instruction = instruction.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
        // Convert HTTP/HTTPS URLs to clickable links (opens in new tab)
        instruction = instruction.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
        
        return `
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Work Process:</strong>
                    <p class="mb-1">${task.work_process_name || 'N/A'}</p>
                </div>
                <div class="col-md-6">
                    <strong>Step Name:</strong>
                    <p class="mb-1">${task.work_process_step || 'N/A'}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <strong>Instruction:</strong>
                    <div class="p-3 bg-light rounded mt-2">
                        ${instruction}
                    </div>
                </div>
            </div>
        `;
    }

    function renderClientDocuments(clientDocs) {
        if (!clientDocs || clientDocs.length === 0) {
            return '<p class="text-muted text-center py-3">No verified client documents available.</p>';
        }

        let html = '';

        clientDocs.forEach(doc => {
            html += `
                <div class="mb-3">
                    <strong>${doc.document_name}</strong><br>
                    <small class="text-muted">Uploaded by: ${doc.uploaded_by}</small>
                </div>
            `;

            if (doc.files && doc.files.length > 0) {
                doc.files.forEach(file => {
                    html += `
                        <div class="upload-item ms-3 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="bi ${getFileIcon(file.file_name)} file-icon me-2"></i>
                                <span class="file-name">${file.file_name}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="file-time me-3">${file.file_size}</span>
                                <a href="${file.download_url}"
                                   class="btn btn-sm btn-outline-primary"
                                   download="${file.file_name}"
                                   target="_blank">
                                   <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                    `;
                });
            }
        });

        return html;
    }

    function renderTaskDocuments(taskDocs) {
        if (!taskDocs || taskDocs.length === 0) {
            return '<p class="text-muted text-center py-3">No documents uploaded yet.</p>';
        }

        let html = '';
        taskDocs.forEach(doc => {
            html += `
                <div class="upload-item" data-document-id="${doc.id}">
                    <div class="d-flex align-items-center">
                        <i class="bi ${getFileIcon(doc.document_name)} file-icon me-2"></i>
                        <span class="file-name">${doc.document_name}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="file-time me-3">${doc.uploaded_at}</span>
                        <a href="${doc.download_url}" 
                           class="btn btn-sm btn-outline-primary me-1" 
                           download="${doc.document_name}"
                           target="_blank">
                            <i class="bi bi-download"></i>
                        </a>
                        <button type="button" 
                                class="btn btn-sm btn-outline-warning replace-document-btn" 
                                data-document-id="${doc.id}" 
                                data-document-name="${doc.document_name}"
                                title="Replace this document">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        return html;
    }

    function renderMessages(messages) {
        if (!messages || messages.length === 0) {
            return '<p class="text-muted text-center py-3">No messages yet.</p>';
        }

        let html = '';
        messages.forEach(msg => {
            const msgClass = msg.is_me ? 'message-bubble message-me' : 'message-bubble message-other';
            const senderInfo = msg.is_me ? 'You' : `${msg.sender_name} (${msg.sender_role})`;

            html += `
                <div class="${msgClass} mb-3 p-3 rounded">
                    <div class="small text-muted mb-1">${senderInfo}</div>
                    <div class="mb-1">${msg.message}</div>
                    <div class="small text-muted text-end">${msg.sent_at}</div>
                </div>
            `;
        });

        // Add some CSS for message bubbles
        html += `
            <style>
                .message-bubble {
                    max-width: 80%;
                }
                .message-me {
                    background-color: #e3f2fd;
                    margin-left: auto;
                }
                .message-other {
                    background-color: #f5f5f5;
                    margin-right: auto;
                }
            </style>
        `;

        return html;
    }

    function getPriorityBadge(priority) {
        if (priority === 'high') {
            return '<span class="priority-badge priority-high">High Priority</span>';
        } else if (priority === 'medium') {
            return '<span class="priority-badge priority-medium">Medium Priority</span>';
        } else {
            return '<span class="priority-badge priority-low">Low Priority</span>';
        }
    }

    function getStatusBadge(status, label) {
        const statusClass = {
            'assigned': 'status-pending',
            'in_progress': 'status-in-progress',
            'waiting_client': 'status-waiting',
            'submitted': 'status-completed',
            'verified': 'status-completed'
        }[status] || '';

        return `<span class="status-badge ${statusClass}">${label}</span>`;
    }

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        if (ext === 'pdf') return 'bi-file-earmark-pdf text-danger';
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'bi-file-image text-success';
        if (['doc', 'docx'].includes(ext)) return 'bi-file-earmark-word text-primary';
        if (['xls', 'xlsx'].includes(ext)) return 'bi-file-earmark-excel text-success';
        return 'bi-file-earmark';
    }

    function getTaskActionsHTML(task) {
        if (['assigned', 'waiting_client', 'rejected'].includes(task.status)) {
            return `
                <button type="button" class="btn btn-success" onclick="startTask(${task.id})">
                    <i class="bi bi-play-circle me-1"></i> ${task.status === 'rejected' ? 'Restart Task' : 'Start Task'}
                </button>
                <button type="button" class="btn btn-primary" onclick="submitTask(${task.id})">
                    <i class="bi bi-check-circle me-1"></i> Resubmit Task
                </button>
            `;
        } else if (task.status === 'in_progress') {
            return `
                <button type="button" class="btn btn-warning" onclick="markAsWaiting(${task.id})">
                    <i class="bi bi-clock me-1"></i> Mark as Waiting
                </button>
                <button type="button" class="btn btn-success" onclick="submitTask(${task.id})">
                    <i class="bi bi-check-circle me-1"></i> Submit Task
                </button>
            `;
        } else if (['submitted', 'verified'].includes(task.status)) {
            return `
                <button type="button" class="btn btn-secondary" disabled>
                    <i class="bi bi-check-circle me-1"></i> Task Completed
                </button>
            `;
        }
        return '';
    }

    // ========== Task Actions ==========
    function checkRenewalEligibility(taskId) {
        const url = "{{ route('backoffice.tasks.renew.check', ':id') }}".replace(':id', taskId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#renewal-section').show();
                if (response.success && response.allowed) {
                    $('#renew-service-btn').prop('disabled', false);
                    $('#renewal-hint').html('<i class="bi bi-check-circle me-1"></i> Click to create a new renewal cycle for this service');
                } else {
                    $('#renew-service-btn').prop('disabled', true);
                    $('#renewal-hint').html('<i class="bi bi-info-circle me-1"></i> ' + (response.message || 'Renewal not allowed at this time'));
                }
            },
            error: function(xhr) {
                $('#renewal-section').show();
                $('#renew-service-btn').prop('disabled', true);
                $('#renewal-hint').html('<i class="bi bi-exclamation-triangle me-1"></i> Unable to check renewal status');
            }
        });
    }

    function renewService() {
        if (!currentTaskId) return;

        if (!confirm('Are you sure you want to renew this service?\n\nThis will create new tasks for all steps of this service.\nAll existing completed tasks will remain in history.')) {
            return;
        }

        const url = "{{ route('backoffice.tasks.renew', ':id') }}".replace(':id', currentTaskId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#renew-service-btn').prop('disabled', true).html('<i class="bi bi-arrow-clockwise me-1"></i> Creating Renewal...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.message);
                    if (taskModalInstance) {
                        taskModalInstance.hide();
                    }
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                    $('#renew-service-btn').prop('disabled', false).html('<i class="bi bi-arrow-clockwise me-1"></i> Renew Service');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error renewing service';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                $('#renew-service-btn').prop('disabled', false).html('<i class="bi bi-arrow-clockwise me-1"></i> Renew Service');
            }
        });
    }

    function startTask(taskId) {
        if (!confirm('Are you sure you want to start this task?')) return;

        const url = "{{ route('backoffice.tasks.start', ':id') }}".replace(':id', taskId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error starting task: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    function markAsWaiting(taskId) {
        if (!confirm('Mark this task as waiting for client?')) return;

        const url = "{{ route('backoffice.tasks.wait', ':id') }}".replace(':id', taskId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error updating task status: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    function submitTask(taskId) {
        if (!confirm('Are you sure you want to submit this task?')) return;

        const url = "{{ route('backoffice.tasks.submit', ':id') }}".replace(':id', taskId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error submitting task: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    function updateRemarks() {
        const remarks = $('#task-remarks').val();
        const url = "{{ route('backoffice.tasks.remarks', ':id') }}".replace(':id', currentTaskId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                remarks: remarks
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error updating remarks: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    function sendMessage() {
        const message = $('#chat-message').val().trim();
        if (!message) return;

        const url = "{{ route('backoffice.tasks.messages.send', ':id') }}".replace(':id', currentTaskId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                message: message
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const msg = response.chat_message;
                    const messageHtml = `
                        <div class="message-bubble message-me mb-3 p-3 rounded">
                            <div class="small text-muted mb-1">You</div>
                            <div class="mb-1">${msg.message}</div>
                            <div class="small text-muted text-end">${msg.sent_at}</div>
                        </div>
                    `;
                    $('#chat-messages').append(messageHtml);
                    $('#chat-message').val('');
                    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error sending message: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    function uploadDocument() {
        const formData = new FormData($('#uploadDocumentForm')[0]);
        const replaceDocId = $('#replace_document_id').val();
        
        let url;
        if (replaceDocId) {
            // Replacement mode – ensure only one file is selected
            const fileInput = $('#document_file')[0];
            if (fileInput.files.length > 1) {
                alert('Please select only one file for replacement.');
                return;
            }
            // Re‑build FormData with single file (name 'document')
            formData.delete('documents[]');
            formData.append('document', fileInput.files[0]);
            url = "{{ route('backoffice.tasks.documents.replace', ':id') }}".replace(':id', replaceDocId);
        } else {
            // Upload mode – multiple files
            url = "{{ route('backoffice.tasks.documents.upload', ':id') }}".replace(':id', currentTaskId);
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('#uploadDocumentForm button[type="submit"]').prop('disabled', true).html('Uploading...');
            },
            success: function(response) {
                if (response.success) {
                    let message = response.message;
                    if (response.documents && response.documents.length > 1) {
                        message = response.documents.length + ' documents uploaded successfully.';
                    }
                    alert(message);
                    uploadModalInstance.hide();
                    $('#uploadDocumentForm')[0].reset();
                    $('#replace_document_id').val('');
                    $('#uploadDocumentModalLabel').text('Upload Document');
                    $('#document_file').attr('multiple', 'multiple');
                    loadTaskDetails(currentTaskId); // refresh task details
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Unknown error'));
            },
            complete: function() {
                $('#uploadDocumentForm button[type="submit"]').prop('disabled', false).html('Upload Document(s)');
            }
        });
    }
</script>
@endsection