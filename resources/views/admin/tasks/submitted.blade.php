@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Task Verification</h2>
                <p class="text-muted mb-0">Review and verify submitted tasks from backoffice</p>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING VERIFICATION</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
                <h3 class="card-value" id="pending-count">{{ $pendingCount }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Awaiting your review</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">VERIFIED TASKS</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <h3 class="card-value" id="verified-count">{{ $verifiedCount }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Successfully verified</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">REJECTED TASKS</h6>
                    <div class="card-icon bg-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
                <h3 class="card-value" id="rejected-count">{{ $rejectedCount }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Rejected submissions</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TOTAL TASKS</h6>
                    <div class="card-icon bg-info">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
                <h3 class="card-value" id="total-count">{{ $totalCount }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>All submissions</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Submissions Table -->
    <div class="mt-4">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">Submitted Tasks</h4>
                <!-- DataTable নিজে search box আনবে, তাই ম্যানুয়াল search সরানো হয়েছে -->
            </div>

            <!-- Status Filters -->
            <div class="d-flex gap-2 mb-4">
                <a href="{{ route('admin.tasks.submitted') }}" 
                   class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                    All Tasks
                </a>
                <a href="{{ route('admin.tasks.submitted', ['status' => 'pending']) }}" 
                   class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    Pending
                </a>
                <a href="{{ route('admin.tasks.submitted', ['status' => 'verified']) }}" 
                   class="btn btn-sm {{ request('status') == 'verified' ? 'btn-success' : 'btn-outline-success' }}">
                    Verified
                </a>
                <a href="{{ route('admin.tasks.submitted', ['status' => 'rejected']) }}" 
                   class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    Rejected
                </a>
            </div>

            <!-- Alert: শুধু pending ট্যাবে -->
            @if(request('status') == 'pending')
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="bi bi-info-circle me-2 fs-5"></i>
                <div>
                    <strong><span id="pending-alert-count">{{ $pendingCount }}</span> pending tasks</strong> require your attention
                </div>
            </div>
            @endif

            @if($tasks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="clientsTable">        
                    <thead>
                        <tr>
                            <th>TASK CODE</th>
                            <th>SERVICE</th>
                            <th>STEP NAME</th>
                            <th>ASSIGNED STAFF</th>
                            <th>STATUS</th>
                            <th>SUBMITTED AT</th>
                            <th>TIME TAKEN</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr data-task-id="{{ $task->id }}" id="task-row-{{ $task->id }}">
                            <td class="fw-bold">
                                <span class="task-code-badge">{{ $task->task_code }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $task->service->service_name ?? 'N/A' }}</strong>
                                    <small class="text-muted">{{ $task->service->service_code ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $task->step_name }}</strong>
                                    <small class="text-muted">Step: {{ $task->step_number }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $task->assignedStaff->name ?? 'N/A' }}</strong>
                                    <small class="text-muted">{{ $task->assignedStaff->email ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                @if($task->status == 'submitted')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock me-1"></i> SUBMITTED
                                </span>
                                @elseif($task->status == 'verified')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> VERIFIED
                                </span>
                                @elseif($task->status == 'rejected')
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i> REJECTED
                                </span>
                                @else
                                <span class="badge bg-secondary">{{ strtoupper($task->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    @if($task->submitted_at)
                                    <strong>{{ $task->submitted_at->format('d M, Y') }}</strong>
                                    <small class="text-muted">{{ $task->submitted_at->format('h:i A') }}</small>
                                    @else
                                    <span class="text-muted">Not submitted</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($task->started_at && $task->submitted_at)
                                <div class="d-flex flex-column">
                                    <strong>{{ $task->submitted_at->diffForHumans($task->started_at, true) }}</strong>
                                    <small class="text-muted">Started: {{ $task->started_at->format('d M') }}</small>
                                </div>
                                @else
                                <span class="text-muted">In progress</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-task-btn" data-task-id="{{ $task->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <div class="mb-4"><i class="bi bi-tasks fs-1 text-muted"></i></div>
                <h4 class="mb-3">No Task Submissions</h4>
                <p class="text-muted mb-4">No task submissions found for verification.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Task Details Modal -->
    <div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-labelledby="taskDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskDetailsModalLabel"><i class="bi bi-clipboard-check"></i> Task Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="taskDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"><span class="visually-hidden">Loading...</span></div>
                        <p class="mt-2">Loading task details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p class="mb-0">© {{ date('Y') }} <strong>SharpEdge</strong>. All rights reserved. | Admin Dashboard v2.4</p>
    </footer>
@endsection

@push('styles')
<style>
    .task-code-badge {
        background: rgba(59, 130, 246, 0.1); 
        color: #3b82f6; 
        padding: 4px 10px; 
        border-radius: 4px; 
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }
    .stats-card .card-icon.bg-warning { background-color: #f59e0b !important; }
    .stats-card .card-icon.bg-success { background-color: #10b981 !important; }
    .stats-card .card-icon.bg-danger { background-color: #ef4444 !important; }
    .stats-card .card-icon.bg-info { background-color: #0ea5e9 !important; }
    .modal-lg { max-width: 800px; }
    .modal-body { max-height: 70vh; overflow-y: auto; }
    .detail-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #3b82f6;
    }
    .detail-title { color: #3b82f6; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
    .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
    .detail-label { color: #64748b; font-weight: 500; }
    .detail-value { color: #1e293b; font-weight: 600; }
    .document-item { padding: 10px; background: white; border-radius: 6px; margin-bottom: 8px; border: 1px solid #e2e8f0; }
    .message-item { padding: 10px; background: white; border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #3b82f6; }
    .message-item.admin { border-left-color: #10b981; }
    .message-item.backoffice { border-left-color: #f59e0b; }
    @media (max-width: 768px) {
        .detail-row { flex-direction: column; }
        .detail-value { text-align: left; margin-top: 4px; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // ========== DATATABLE INIT (with destroy to avoid warning) ==========
    if ($.fn.DataTable.isDataTable('#clientsTable')) {
        $('#clientsTable').DataTable().destroy();
    }
    $('#clientsTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[5, 'desc']],   // 5 = SUBMITTED AT column index
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            emptyTable: "No tasks found",
            zeroRecords: "No matching tasks"
        },
        drawCallback: function() {
            // Re-attach view button events after each table redraw
            $('.view-task-btn').off('click').on('click', function() {
                let taskId = $(this).data('task-id');
                currentTaskId = taskId;
                const modal = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
                modal.show();
                loadTaskDetails(taskId);
            });
        }
    });

    // ========== EXISTING MODAL & VERIFY/REJECT LOGIC ==========
    let currentTaskId = null;

    @if(session('success'))
    showAlert('success', '{{ session('success') }}');
    @endif
    @if(session('error'))
    showAlert('danger', '{{ session('error') }}');
    @endif

    function showAlert(type, message) {
        const alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        $('.header').after(alert);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // initial view button binding (for first load)
    $('.view-task-btn').on('click', function() {
        let taskId = $(this).data('task-id');
        currentTaskId = taskId;
        const modal = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
        modal.show();
        loadTaskDetails(taskId);
    });

    function loadTaskDetails(taskId) {
        $.ajax({
            url: '{{ route("admin.tasks.verify.show", ":taskId") }}'.replace(':taskId', taskId),
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#taskDetailsContent').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"><span class="visually-hidden">Loading...</span></div>
                        <p class="mt-2">Loading task details...</p>
                    </div>
                `);
            },
            success: function(response) {
                if (response.success) {
                    $('#taskDetailsContent').html(response.html);
                    $('#taskDetailsModalLabel').html(`<i class="bi bi-clipboard-check"></i> Task Details: ${response.task.task_code}`);
                    attachActionHandlers(taskId);
                } else {
                    $('#taskDetailsContent').html(`<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Failed to load task details.</div>`);
                }
            },
            error: function() {
                $('#taskDetailsContent').html(`<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Error loading task details.</div>`);
            }
        });
    }

    function attachActionHandlers(taskId) {
        $('#verify-task-form').off('submit').on('submit', function(e) {
            e.preventDefault();
            verifyTask(taskId);
        });
        $('#reject-task-form').off('submit').on('submit', function(e) {
            e.preventDefault();
            rejectTask(taskId);
        });
    }

    function verifyTask(taskId) {
        const adminNotes = $('#verify-notes').val();
        Swal.fire({
            title: 'Verify Task?',
            html: `Are you sure you want to <strong>VERIFY</strong> this task?<br><br><small class="text-muted">Task will be marked as verified.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Yes, Verify!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.tasks.verify.store", ":taskId") }}'.replace(':taskId', taskId),
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', admin_notes: adminNotes },
                    beforeSend: () => $('#verify-task-btn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...'),
                    success: function(response) {
                        if (response.success) {
                            updateTaskRow(taskId, response.task);
                            updateCounters(response.counts);
                            Swal.fire({ icon: 'success', title: 'Verified', text: response.message, timer: 2000, showConfirmButton: false });
                            $('#taskDetailsModal').modal('hide');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                        }
                    },
                    error: (xhr) => Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Something went wrong' }),
                    complete: () => $('#verify-task-btn').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Verify Task')
                });
            }
        });
    }

    function rejectTask(taskId) {
        const adminNotes = $('#reject-notes').val();
        if (!adminNotes.trim()) {
            Swal.fire({ icon: 'warning', title: 'Remarks Required', text: 'Please provide rejection remarks' });
            return;
        }
        Swal.fire({
            title: 'Reject Task?',
            html: `<strong>REJECT</strong> this task?<br><br><small class="text-muted">Staff will be notified.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Reject!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.tasks.reject", ":taskId") }}'.replace(':taskId', taskId),
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', admin_notes: adminNotes },
                    beforeSend: () => $('#reject-task-btn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...'),
                    success: function(response) {
                        if (response.success) {
                            updateTaskRow(taskId, response.task);
                            updateCounters(response.counts);
                            Swal.fire({ icon: 'success', title: 'Rejected', text: response.message, timer: 2000, showConfirmButton: false });
                            $('#taskDetailsModal').modal('hide');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                        }
                    },
                    error: (xhr) => Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Something went wrong' }),
                    complete: () => $('#reject-task-btn').prop('disabled', false).html('<i class="bi bi-x-circle"></i> Reject Task')
                });
            }
        });
    }

    function updateTaskRow(taskId, task) {
        const row = $(`#task-row-${taskId}`);
        let statusBadge = '';
        if (task.status === 'verified') statusBadge = `<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> VERIFIED</span>`;
        else if (task.status === 'rejected') statusBadge = `<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> REJECTED</span>`;
        if (statusBadge) row.find('td:eq(4)').html(statusBadge);
    }

    function updateCounters(counts) {
        $('#pending-count').text(counts.pending);
        $('#verified-count').text(counts.verified);
        $('#rejected-count').text(counts.rejected);
        $('#pending-alert-count').text(counts.pending);
        $('#total-count').text(counts.pending + counts.verified + counts.rejected);
    }

    $('#taskDetailsModal').on('hidden.bs.modal', function () {
        currentTaskId = null;
        $('#taskDetailsContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary"><span class="visually-hidden">Loading...</span></div>
                <p class="mt-2">Loading task details...</p>
            </div>
        `);
    });
});
</script>
@endpush