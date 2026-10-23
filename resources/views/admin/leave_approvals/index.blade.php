@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Leave Request Management</h2>
                <p class="text-muted mb-0">Review and approve/reject leave requests from staff</p>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TOTAL REQUESTS</h6>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats->total ?? 0 }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>All leave requests</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats->pending ?? 0 }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Awaiting review</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">APPROVED</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats->approved ?? 0 }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Approved leaves</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">REJECTED</h6>
                    <div class="card-icon bg-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats->rejected ?? 0 }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Rejected leaves</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="mt-4">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">Leave Requests</h4>
                <div class="d-flex gap-3">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.leave-approvals.index') }}" class="d-flex">
                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search staff, reason..." value="{{ request('search') }}">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Status Filters -->
            <div class="d-flex gap-2 mb-4">
                <a href="{{ route('admin.leave-approvals.index') }}" 
                   class="btn btn-sm {{ !request('status') || request('status') == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                    All Requests
                </a>
                <a href="{{ route('admin.leave-approvals.index', ['status' => 'pending']) }}" 
                   class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    Pending
                </a>
                <a href="{{ route('admin.leave-approvals.index', ['status' => 'approved']) }}" 
                   class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                    Approved
                </a>
                <a href="{{ route('admin.leave-approvals.index', ['status' => 'rejected']) }}" 
                   class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    Rejected
                </a>
            </div>

            <!-- Info Alert -->
            @if((request('status') == 'pending' || !request('status')) && ($stats->pending ?? 0) > 0)
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="bi bi-info-circle me-2 fs-5"></i>
                <div>
                    <strong>{{ $stats->pending ?? 0 }} pending requests</strong> require your attention
                </div>
            </div>
            @endif

            @if($leaveRequests->count() > 0)
            <div class="table-responsive">
           <table class="table table-hover datatable" id="clientsTable">        
                    <thead>
                        <tr>
                            <th>REQUEST ID</th>
                            <th>STAFF DETAILS</th>
                            <th>LEAVE TYPE</th>
                            <th>LEAVE DATES</th>
                            <th>DURATION</th>
                            <th>REASON</th>
                            <th>APPLIED ON</th>
                            <th>DOCUMENT/ATTACHMENT</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveRequests as $leave)
                        <tr>
                            <td class="fw-bold">
                                <span style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 4px 10px; border-radius: 4px; font-family: 'Courier New', monospace;">
                                    #{{ str_pad($leave->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $leave->staff->name ?? 'N/A' }}</strong>
                                    <small class="text-muted">{{ $leave->staff->staff_code ?? '' }}</small>
                                    <small class="text-muted">{{ $leave->staff->email ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($leave->leaveType->icon ?? false)
                                    <i class="bi bi-{{ $leave->leaveType->icon }} me-2 text-primary"></i>
                                    @endif
                                    <span>{{ $leave->leaveType->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M, Y') }}</strong>
                                    <small class="text-muted">to</small>
                                    <strong>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M, Y') }}</strong>
                                </div>
                            </td>
                         <td>
    @php $days = (int) $leave->total_days; @endphp
    <span class="badge bg-info">
        {{ $days }} {{ $days == 1 ? 'Day' : 'Days' }}
    </span>
</td>

                            <td>
                                <div class="text-truncate" style="max-width: 200px;" 
                                     title="{{ $leave->reason }}">
                                    {{ Str::limit($leave->reason, 50) }}
                                </div>
                                @if($leave->rejection_reason)
                                <div class="text-danger small mt-1">
                                    <strong>Rejection Reason:</strong> {{ Str::limit($leave->rejection_reason, 30) }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $leave->created_at->format('d M, Y') }}</strong>
                                    <small class="text-muted">{{ $leave->created_at->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                @if($leave->attachment)
                                    @php
                                        $fileExtension = pathinfo($leave->attachment, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                                        $isPdf = strtolower($fileExtension) === 'pdf';
                                        $fileUrl = Storage::url($leave->attachment);

                                    @endphp
                                    
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        @if($isImage)
                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                                <i class="bi bi-image"></i> View Image
                                            </a>
                                        @elseif($isPdf)
                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="bi bi-file-pdf"></i> View PDF
                                            </a>
                                        @else
                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100">
                                                <i class="bi bi-file-earmark"></i> View File
                                            </a>
                                        @endif
                                        
                                        <a href="{{ $fileUrl }}" download class="btn btn-sm btn-outline-success w-100">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                @else
                                    <span class="text-muted">No File</span>
                                @endif
                            </td>
                            <td>
                                @if($leave->status == 'pending')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock me-1"></i> PENDING
                                </span>
                                @elseif($leave->status == 'approved')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> APPROVED
                                </span>
                                @if($leave->approved_at)
                                <div class="text-xs text-muted mt-1">
                                    <small>Approved by: {{ $leave->approvedBy->name ?? 'Admin' }}</small>
                                    <br>
                                    <small>On: {{ $leave->approved_at->format('d M Y') }}</small>
                                </div>
                                @endif
                                @elseif($leave->status == 'rejected')
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i> REJECTED
                                </span>
                                @if($leave->approved_at)
                                <div class="text-xs text-muted mt-1">
                                    <small>Rejected by: {{ $leave->approvedBy->name ?? 'Admin' }}</small>
                                    <br>
                                    <small>On: {{ $leave->approved_at->format('d M Y') }}</small>
                                </div>
                                @endif
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    @if($leave->status == 'pending')
                                    <div class="action-wrap">
                                        <div class="action-top">
                                            <form action="{{ route('admin.leave-approvals.approve', $leave) }}"
                                                  method="POST"
                                                  class="action-form"
                                                  onsubmit="return confirmApprove(event, '{{ $leave->staff->name ?? 'Staff' }}', '{{ \Carbon\Carbon::parse($leave->start_date)->format('d M, Y') }}');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success w-100" title="Approve Leave">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                            </form>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger w-100 reject-btn"
                                                    data-leave-id="{{ $leave->id }}"
                                                    data-staff-name="{{ $leave->staff->name ?? 'Staff' }}">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        </div>
                                    </div>
                                    @else
                                    <div class="text-center">
                                        <small class="text-muted">Already processed</small>
                                        <div class="mt-1">
                                            @if($leave->status == 'approved')
                                            <i class="bi bi-check-circle text-success fs-5"></i>
                                            @else
                                            <i class="bi bi-x-circle text-danger fs-5"></i>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($leaveRequests->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $leaveRequests->appends(request()->query())->links() }}
            </div>
            @endif
            
            @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-calendar-x fs-1 text-muted"></i>
                </div>
                <h4 class="mb-3">No Leave Requests Found</h4>
                <p class="text-muted mb-4">
                    @if(request('search') || request('status'))
                    Try changing your search or filter criteria.
                    @else
                    No leave requests have been submitted yet.
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">
                            <i class="bi bi-x-circle text-danger me-2"></i> Reject Leave Request
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <p class="mb-2">
                                You are about to reject the leave request for:
                                <strong id="staffName"></strong>
                            </p>
                            <p class="text-danger small mb-3">
                                <i class="bi bi-exclamation-triangle"></i> Once rejected, this action cannot be undone.
                            </p>
                        </div>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label required">
                                Rejection Reason
                            </label>
                            <textarea class="form-control" 
                                      id="rejection_reason" 
                                      name="rejection_reason" 
                                      rows="3" 
                                      placeholder="Please provide a reason for rejection..."
                                      required></textarea>
                            <div class="form-text">
                                This reason will be visible to the staff member.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check-lg"></i> Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p class="mb-0">© {{ date('Y') }} <strong>SharpEdge</strong>. All rights reserved. | Admin Dashboard v2.4</p>
    </footer>
@endsection

@push('styles')
<style>
    .stats-card .card-icon.bg-warning {
        background-color: #f59e0b !important;
    }
    
    .stats-card .card-icon.bg-success {
        background-color: #10b981 !important;
    }
    
    .stats-card .card-icon.bg-danger {
        background-color: #ef4444 !important;
    }
    
    .stats-card .card-change {
        font-size: 14px;
        display: flex;
        align-items: center;
        color: #64748b;
    }
    
    .table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 15px;
    }
    
    .table td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .badge {
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .form-label.required:after {
        content: " *";
        color: #ef4444;
    }
    
    @media (max-width: 768px) {
        .header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .section-card .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem !important;
        }
        
        .input-group {
            width: 100% !important;
        }
    }

    .action-wrap{
        width: 100%;
    }

    .action-top{
        display: flex;
        gap: 6px;
    }

    .action-top .btn,
    .action-top form{
        flex: 1;
        min-width: 0;
    }

    .action-form{
        width: 100%;
        display: flex;
    }

    .action-top .btn{
        height: 32px;
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .action-top form button,
    .action-top .reject-btn {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .attachment-buttons {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 100px;
    }

    .attachment-buttons .btn {
        padding: 4px 8px;
        font-size: 12px;
        white-space: nowrap;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.reject-btn').on('click', function() {
            const leaveId = $(this).data('leave-id');
            const staffName = $(this).data('staff-name');
            
            const rejectUrl = "{{ route('admin.leave-approvals.reject', ':id') }}".replace(':id', leaveId);
            $('#rejectForm').attr('action', rejectUrl);
            
            $('#staffName').text(staffName);
            $('#rejection_reason').val('');
            
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        });

        window.confirmApprove = function(event, staffName, startDate) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Approve Leave Request?',
                html: `Are you sure you want to <strong>APPROVE</strong> leave request for:<br>
                      <strong class="text-primary">${staffName}</strong><br>
                      Starting on <strong>${startDate}</strong>?<br><br>
                      <small class="text-muted">Note: This action cannot be undone.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Yes, Approve!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const button = event.target.querySelector('button[type="submit"]');
                    const originalText = button.innerHTML;
                    button.disabled = true;
                    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Processing...';
                    
                    event.target.submit();
                }
            });
        }

        $('#rejectForm').on('submit', function(e) {
            const reason = $('#rejection_reason').val().trim();
            if (!reason) {
                e.preventDefault();
                Swal.fire({
                    title: 'Reason Required',
                    text: 'Please provide a reason for rejection.',
                    icon: 'warning',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }
            
            const button = $(this).find('button[type="submit"]');
            const originalText = button.html();
            button.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Processing...');
        });

        @if(session('success'))
        showAlert('success', '{{ session('success') }}');
        @endif

        @if(session('error'))
        showAlert('danger', '{{ session('error') }}');
        @endif

        function showAlert(type, message) {
            const alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            
            $('.header').after(alert);
            
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    });
</script>
@endpush