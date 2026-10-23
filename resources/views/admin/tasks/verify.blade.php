@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Task Verification</h2>
                <p class="text-muted mb-0">Review and verify task submission</p>
            </div>
            <div>
                <a href="{{ route('admin.tasks.submitted') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Back to List
                </a>
            </div>
        </div>
    </header>

    <div class="row g-4" id="taskContainer" data-task-id="{{ $task->id }}">
        <!-- Left Column - Task Details & Documents -->
        <div class="col-lg-8">
            <!-- A. Task Summary Card -->
            <div class="section-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="section-title mb-0">
                        <i class="bi bi-info-circle me-2"></i> Task Information
                    </h4>
                    <!-- FIXED: Status Badge - Now uses task data, NOT request parameters -->
                    <div id="taskStatusBadge">
                        @if($task->status === 'submitted')
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="bi bi-clock me-1"></i> AWAITING VERIFICATION
                            </span>
                        @elseif($task->status === 'verified')
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle me-1"></i> VERIFIED
                            </span>
                        @elseif($task->rejected_at || $task->status === 'reassigned')
                            {{-- Show REJECTED for any task that was rejected (even if reassigned) --}}
                            <span class="badge bg-danger fs-6">
                                <i class="bi bi-x-circle me-1"></i> REJECTED (Returned)
                                @if($task->reassigned_at)
                                    <span class="badge bg-light text-dark ms-1">Reassigned</span>
                                @endif
                            </span>
                        @else
                            {{-- Fallback for any other status --}}
                            <span class="badge bg-secondary fs-6">
                                {{ strtoupper(str_replace('_', ' ', $task->status)) }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-row">
                            <span class="detail-label">Task Code:</span>
                            <span class="detail-value">
                                <span class="badge bg-primary" id="taskCode">{{ $task->task_code }}</span>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Service:</span>
                            <span class="detail-value">
                                {{ $task->service->service_name ?? 'N/A' }}
                                <small class="text-muted d-block">{{ $task->service->service_code ?? '' }}</small>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Step Name:</span>
                            <span class="detail-value">{{ $task->step_name }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Step Number:</span>
                            <span class="detail-value">{{ $task->step_number }}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-row">
                            <span class="detail-label">Assigned Staff:</span>
                            <span class="detail-value">
                                {{ $task->assignedStaff->name ?? 'N/A' }}
                                <small class="text-muted d-block">{{ $task->assignedStaff->email ?? '' }}</small>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Started At:</span>
                            <span class="detail-value">
                                @if($task->started_at)
                                {{ $task->started_at->format('d M, Y h:i A') }}
                                @else
                                <span class="text-muted">Not started</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Submitted At:</span>
                            <span class="detail-value">
                                @if($task->submitted_at)
                                {{ $task->submitted_at->format('d M, Y h:i A') }}
                                @else
                                <span class="text-muted">Not submitted</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Time Taken:</span>
                            <span class="detail-value">
                                @if($task->started_at && $task->submitted_at)
                                <strong>{{ $task->submitted_at->diffForHumans($task->started_at, true) }}</strong>
                                @else
                                <span class="text-muted">In progress</span>
                                @endif
                            </span>
                        </div>
                        
                        {{-- Show rejection info if available --}}
                        @if($task->rejected_at)
                        <div class="detail-row">
                            <span class="detail-label text-danger">Rejected At:</span>
                            <span class="detail-value text-danger">
                                {{ $task->rejected_at->format('d M, Y h:i A') }}
                            </span>
                        </div>
                        @endif
                        
                        {{-- Show reassign info if available --}}
                        @if($task->reassigned_at)
                        <div class="detail-row">
                            <span class="detail-label text-info">Reassigned At:</span>
                            <span class="detail-value text-info">
                                {{ $task->reassigned_at->format('d M, Y h:i A') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                
                {{-- Show admin notes if any --}}
                @if($task->admin_notes)
                <div class="mt-3 p-3 bg-light rounded">
                    <strong class="d-block mb-2">Admin Notes:</strong>
                    <p class="mb-0">{{ $task->admin_notes }}</p>
                </div>
                @endif
            </div>

            <!-- B. Documents Section -->
            <div class="section-card mb-4">
                <h4 class="section-title mb-4">
                    <i class="bi bi-files me-2"></i> Submitted Documents
                    <span class="badge bg-secondary ms-2">{{ $task->documents->count() }} files</span>
                </h4>
                
                @if($task->documents->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Document Name</th>
                                <th>Status</th>
                                <th>Uploaded By</th>
                                <th>Uploaded Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($task->documents as $document)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                        <div>
                                            <strong>{{ basename($document->file_path) }}</strong>
                                            @if($document->description)
                                            <small class="text-muted d-block">{{ $document->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($document->status == 'submitted')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i> Submitted
                                    </span>
                                    @elseif($document->status == 'verified')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Verified
                                        @if($document->verified_at)
                                        <small class="d-block">{{ $document->verified_at->format('d M') }}</small>
                                        @endif
                                    </span>
                                    @elseif($document->status == 'rejected' || $document->status == 'reassigned')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i> {{ ucfirst($document->status) }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    {{ $document->uploadedBy->name ?? 'System' }}
                                </td>
                                <td>
                                    @if($document->created_at)
                                    <div class="d-flex flex-column">
                                        <small>{{ $document->created_at->format('d M, Y') }}</small>
                                        <small class="text-muted">{{ $document->created_at->format('h:i A') }}</small>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ Storage::url($document->file_path) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-folder-x fs-1 text-muted"></i>
                    <p class="mt-3 mb-0 text-muted">No documents submitted for this task.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Messages & Actions -->
        <div class="col-lg-4">
            <!-- C. Message / Communication Section -->
            <div class="section-card mb-4">
                <h4 class="section-title mb-4">
                    <i class="bi bi-chat-left-text me-2"></i> Communication Log
                    <span class="badge bg-secondary ms-2">{{ $task->messages->count() }}</span>
                </h4>
                
                <div class="communication-log" style="max-height: 300px; overflow-y: auto;" id="messageLog">
                    @if($task->messages->count() > 0)
                        @foreach($task->messages->sortBy('created_at') as $message)
                        <div class="message-item mb-3 p-3 rounded {{ $message->sender_role == 'admin' ? 'bg-light' : 'bg-white border' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="{{ $message->sender_role == 'admin' ? 'text-primary' : 'text-success' }}">
                                        {{ $message->sender->name ?? 'System' }}
                                    </strong>
                                    <small class="text-muted ms-2">{{ $message->sender_role }}</small>
                                </div>
                                <small class="text-muted">
                                    {{ $message->created_at->format('h:i A') }}
                                </small>
                            </div>
                            <p class="mb-0">{{ $message->message }}</p>
                        </div>
                        @endforeach
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-chat-left-text fs-1 text-muted"></i>
                        <p class="mt-3 mb-0 text-muted">No messages yet.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- D. Admin Action Section -->
            @if($task->status == 'submitted')
            <div class="section-card" id="actionSection">
                <h4 class="section-title mb-4">
                    <i class="bi bi-shield-check me-2"></i> Admin Actions
                </h4>
                
                <div class="mb-4">
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" 
                                  rows="3" placeholder="Add notes or remarks for verification..."></textarea>
                        <div class="form-text">These notes will be recorded in the audit trail.</div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-lg" id="verifyTaskBtn">
                            <i class="bi bi-check-circle me-2"></i> Verify Task
                        </button>
                    </div>
                </div>
                
                <div class="border-top pt-4">
                    <h6 class="text-danger mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i> Reject Task
                    </h6>
                    <div class="mb-3">
                        <label for="reject_notes" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_notes" name="reject_notes" 
                                  rows="3" placeholder="Required: Please provide the reason for rejection..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-danger" id="rejectTaskBtn">
                            <i class="bi bi-x-circle me-2"></i> Reject Task
                        </button>
                    </div>
                </div>
            </div>
            @else
            <!-- Show status info if already processed -->
            <div class="section-card">
                <h4 class="section-title mb-4">
                    @if($task->status == 'verified')
                    <i class="bi bi-check-circle text-success me-2"></i> Task Verified
                    @elseif($task->rejected_at)
                    <i class="bi bi-x-circle text-danger me-2"></i> Task Rejected
                    @else
                    <i class="bi bi-info-circle text-info me-2"></i> Task {{ ucfirst($task->status) }}
                    @endif
                </h4>
                
                <div class="alert {{ $task->status == 'verified' ? 'alert-success' : ($task->rejected_at ? 'alert-danger' : 'alert-info') }}">
                    <div class="d-flex align-items-center">
                        <i class="bi {{ $task->status == 'verified' ? 'bi-check-circle' : ($task->rejected_at ? 'bi-x-circle' : 'bi-info-circle') }} me-3 fs-4"></i>
                        <div>
                            <strong class="d-block">This task has been 
                                @if($task->status == 'verified')
                                    verified
                                @elseif($task->rejected_at)
                                    rejected
                                @else
                                    {{ $task->status }}
                                @endif
                            </strong>
                            @if($task->status == 'verified' && $task->verifiedBy)
                            <small>Verified by: {{ $task->verifiedBy->name ?? 'Admin' }}</small>
                            @elseif($task->rejected_at && $task->rejectedBy)
                            <small>Rejected by: {{ $task->rejectedBy->name ?? 'Admin' }}</small>
                            @endif
                            
                            @if($task->reassigned_at)
                            <small class="d-block text-info mt-1">
                                <i class="bi bi-arrow-repeat me-1"></i> Reassigned on {{ $task->reassigned_at->format('d M, Y') }}
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($task->admin_notes)
                <div class="mt-3">
                    <label class="form-label">Admin Notes:</label>
                    <div class="p-3 bg-light rounded">
                        {{ $task->admin_notes }}
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-4">
        <p class="mb-0">© {{ date('Y') }} <strong>SharpEdge</strong>. All rights reserved. | Admin Dashboard v2.4</p>
    </footer>
@endsection

@push('styles')
<style>
    .section-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
    
    .section-title {
        color: #1e293b;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        color: #64748b;
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .detail-value {
        color: #1e293b;
        font-weight: 600;
        text-align: right;
        font-size: 0.9rem;
    }
    
    .communication-log {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }
    
    .communication-log::-webkit-scrollbar {
        width: 6px;
    }
    
    .communication-log::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    
    .communication-log::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 3px;
    }
    
    .message-item {
        border-left: 3px solid #3b82f6;
    }
    
    .message-item.bg-light {
        border-left-color: #10b981;
    }
    
    .form-text {
        font-size: 0.85rem;
    }
    
    @media (max-width: 768px) {
        .detail-row {
            flex-direction: column;
        }
        
        .detail-value {
            text-align: left;
            margin-top: 4px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    const taskId = $('#taskContainer').data('task-id');
    
    // Toastr config
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Verify Task
    $('#verifyTaskBtn').click(function() {
        const adminNotes = $('#admin_notes').val();
        
        Swal.fire({
            title: 'Verify Task?',
            text: "Are you sure you want to verify this task?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, verify it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.tasks.verify.store', ':id') }}".replace(':id', taskId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        admin_notes: adminNotes
                    },
                    beforeSend: function() {
                        $('#verifyTaskBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            
                            // Update UI to show verified state
                            $('#taskStatusBadge').html('<span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i> VERIFIED</span>');
                            $('#actionSection').html(`
                                <h4 class="section-title mb-4">
                                    <i class="bi bi-check-circle text-success me-2"></i> Task Verified
                                </h4>
                                <div class="alert alert-success">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle me-3 fs-4"></i>
                                        <div>
                                            <strong class="d-block">This task has been verified.</strong>
                                        </div>
                                    </div>
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Verification failed';
                        toastr.error(msg);
                        $('#verifyTaskBtn').prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i> Verify Task');
                    }
                });
            }
        });
    });

    // Reject Task
    $('#rejectTaskBtn').click(function() {
        const rejectNotes = $('#reject_notes').val().trim();
        
        if (!rejectNotes) {
            toastr.error('Please provide a reason for rejection');
            return;
        }
        
        Swal.fire({
            title: 'Reject Task?',
            text: "Are you sure you want to reject this task?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reject it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.tasks.reject', ':id') }}".replace(':id', taskId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        admin_notes: rejectNotes
                    },
                    beforeSend: function() {
                        $('#rejectTaskBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            
                            // Update UI to show rejected state
                            $('#taskStatusBadge').html('<span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i> REJECTED</span>');
                            $('#actionSection').html(`
                                <h4 class="section-title mb-4">
                                    <i class="bi bi-x-circle text-danger me-2"></i> Task Rejected
                                </h4>
                                <div class="alert alert-danger">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-x-circle me-3 fs-4"></i>
                                        <div>
                                            <strong class="d-block">This task has been rejected.</strong>
                                            <small>Rejected by: Admin</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Admin Notes:</label>
                                    <div class="p-3 bg-light rounded">${rejectNotes}</div>
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Rejection failed';
                        toastr.error(msg);
                        $('#rejectTaskBtn').prop('disabled', false).html('<i class="bi bi-x-circle me-2"></i> Reject Task');
                    }
                });
            }
        });
    });
});
</script>
@endpush