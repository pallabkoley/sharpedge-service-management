<div class="task-details-container">
    <!-- 1️⃣ Task Summary Section -->
    <div class="detail-section mb-4">
        <h6 class="detail-title">
            <i class="bi bi-info-circle"></i> Task Summary
        </h6>
        <div class="row">
            <div class="col-md-6">
                <div class="detail-row">
                    <span class="detail-label">Task Code:</span>
                    <span class="detail-value">
                        <span class="badge bg-primary">{{ $task->task_code }}</span>
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
            </div>
        </div>
        
        <div class="detail-row mt-3">
            <span class="detail-label">Current Status:</span>
            <span class="detail-value">
                @if($task->status == 'submitted')
                <span class="badge bg-warning text-dark">
                    <i class="bi bi-clock me-1"></i> SUBMITTED
                </span>
                @elseif($task->status == 'verified')
                <span class="badge bg-success">
                    <i class="bi bi-check-circle me-1"></i> VERIFIED
                </span>
                @if($task->verified_at)
                <small class="text-muted d-block mt-1">
                    Verified on: {{ $task->verified_at->format('d M, Y h:i A') }}
                    @if($task->verifiedBy)
                    by {{ $task->verifiedBy->name }}
                    @endif
                </small>
                @endif
                @elseif($task->status == 'rejected')
                <span class="badge bg-danger">
                    <i class="bi bi-x-circle me-1"></i> REJECTED
                </span>
                @if($task->updated_at)
                <small class="text-muted d-block mt-1">
                    Rejected on: {{ $task->updated_at->format('d M, Y h:i A') }}
                </small>
                @endif
                @endif
            </span>
        </div>
    </div>

    <!-- 2️⃣ Documents Section -->
    <div class="detail-section mb-4">
        <h6 class="detail-title">
            <i class="bi bi-files"></i> Submitted Documents
            <span class="badge bg-secondary ms-2">{{ $task->documents->count() }} files</span>
        </h6>
        
        @if($task->documents->count() > 0)
            @foreach($task->documents as $document)
            <div class="document-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                            <strong>{{ basename($document->file_path) }}</strong>
                            <span class="badge ms-2 
                                @if($document->status == 'submitted') bg-warning text-dark
                                @elseif($document->status == 'verified') bg-success
                                @elseif($document->status == 'rejected') bg-danger
                                @else bg-secondary @endif">
                                {{ ucfirst($document->status) }}
                            </span>
                        </div>
                        @if($document->description)
                        <small class="text-muted d-block">{{ $document->description }}</small>
                        @endif
                        <small class="text-muted">
                            Uploaded: {{ $document->created_at->format('d M, Y h:i A') }}
                        </small>
                    </div>
                    <div>
                        <a href="{{ Storage::url($document->file_path) }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download"></i> View
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-3">
                <i class="bi bi-folder-x text-muted fs-4"></i>
                <p class="mt-2 mb-0 text-muted">No documents submitted</p>
            </div>
        @endif
    </div>

    <!-- 3️⃣ Messages / Communication Section -->
    <div class="detail-section mb-4">
        <h6 class="detail-title">
            <i class="bi bi-chat-left-text"></i> Communication Log
            <span class="badge bg-secondary ms-2">{{ $task->messages->count() }} messages</span>
        </h6>
        
        <div style="max-height: 200px; overflow-y: auto;" class="p-2">
            @if($task->messages->count() > 0)
                @foreach($task->messages->sortBy('created_at') as $message)
                <div class="message-item {{ $message->sender_role == 'admin' ? 'admin' : 'backoffice' }} mb-2">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <strong class="{{ $message->sender_role == 'admin' ? 'text-success' : 'text-warning' }}">
                                {{ $message->sender->name ?? 'System' }}
                            </strong>
                            <small class="text-muted ms-2">({{ ucfirst($message->sender_role) }})</small>
                        </div>
                        <small class="text-muted">
                            {{ $message->created_at->format('h:i A') }}
                        </small>
                    </div>
                    <p class="mb-0">{{ $message->message }}</p>
                    <small class="text-muted">
                        {{ $message->created_at->format('d M, Y') }}
                    </small>
                </div>
                @endforeach
            @else
                <div class="text-center py-3">
                    <i class="bi bi-chat-left-text text-muted fs-4"></i>
                    <p class="mt-2 mb-0 text-muted">No messages yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- 4️⃣ Admin Action Section (only if status = submitted) -->
    @if($task->status == 'submitted')
    <div class="detail-section">
        <h6 class="detail-title">
            <i class="bi bi-shield-check"></i> Admin Actions
        </h6>
        
        <div class="row">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded mb-3">
                    <h6 class="text-success mb-3">
                        <i class="bi bi-check-circle"></i> Verify Task
                    </h6>
                    <form id="verify-task-form">
                        <div class="mb-3">
                            <label for="verify-notes" class="form-label">Remarks (Optional)</label>
                            <textarea class="form-control" id="verify-notes" name="admin_notes" 
                                      rows="2" placeholder="Add verification remarks..."></textarea>
                        </div>
                        <button type="submit" id="verify-task-btn" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Verify Task
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="p-3 bg-light rounded">
                    <h6 class="text-danger mb-3">
                        <i class="bi bi-x-circle"></i> Reject Task
                    </h6>
                    <form id="reject-task-form">
                        <div class="mb-3">
                            <label for="reject-notes" class="form-label">Remarks <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reject-notes" name="admin_notes" 
                                      rows="2" placeholder="Required: Enter rejection reason..." required></textarea>
                        </div>
                        <button type="submit" id="reject-task-btn" class="btn btn-danger w-100">
                            <i class="bi bi-x-circle"></i> Reject Task
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="alert alert-warning mt-3">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Note:</strong> Once verified or rejected, the task status cannot be changed from this panel.
        </div>
    </div>
    @endif
</div>