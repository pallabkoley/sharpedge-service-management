@extends('layouts.Backoffice')

@section('page-title', 'My Leave Requests')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
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
                    <i class="bi bi-calendar-check me-1"></i>
                    <span>All leave requests</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats->pending ?? 0 }}</h3>
                <div class="card-change up">
                    <i class="bi bi-clock-history me-1"></i>
                    <span>Waiting for approval</span>
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
                    <i class="bi bi-check-circle me-1"></i>
                    <span>Leave granted</span>
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
                <div class="card-change down">
                    <i class="bi bi-x-circle me-1"></i>
                    <span>Requests declined</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Balances Section -->
    @if(count($leaveBalances) > 0)
    <div class="content-section mb-4">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">My Leave Balances ({{ Carbon\Carbon::now()->year }})</h4>
            </div>
            
            <div class="row g-3">
                @foreach($leaveBalances as $balance)
                @php
                    $usedPercentage = $balance['max_per_year'] > 0 ? ($balance['approved_used'] / $balance['max_per_year']) * 100 : 0;
                    $progressClass = 'bg-success';
                    if ($usedPercentage >= 80) {
                        $progressClass = 'bg-danger';
                    } elseif ($usedPercentage >= 50) {
                        $progressClass = 'bg-warning';
                    }
                @endphp
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="card-title text-muted mb-0">{{ $balance['leave_type_name'] }}</h6>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ number_format($balance['leave_left'], 1) }} left
                                </span>
                            </div>
                            <h3 class="card-value text-primary">{{ number_format($balance['leave_left'], 1) }}</h3>
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span>Used: {{ number_format($balance['approved_used'], 1) }}</span>
                                <span>Max: {{ $balance['max_per_year'] }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $progressClass }}" 
                                     role="progressbar" 
                                     style="width: {{ min($usedPercentage, 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Section -->
    <div class="content-section">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">My Leave Requests</h4>
                <div class="d-flex align-items-center">
                    <!-- DataTables search will be added automatically -->
                    <!-- Add Leave Button -->
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                        <i class="bi bi-plus-lg me-1"></i> Apply for Leave
                    </button>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('backoffice.leave-requests.index', ['search' => request('search')]) }}" 
                   class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                    All
                </a>
                <a href="{{ route('backoffice.leave-requests.index', ['status' => 'pending', 'search' => request('search')]) }}" 
                   class="btn btn-sm {{ request('status') == 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Pending
                </a>
                <a href="{{ route('backoffice.leave-requests.index', ['status' => 'approved', 'search' => request('search')]) }}" 
                   class="btn btn-sm {{ request('status') == 'approved' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Approved
                </a>
                <a href="{{ route('backoffice.leave-requests.index', ['status' => 'rejected', 'search' => request('search')]) }}" 
                   class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Rejected
                </a>
            </div>

            @if($myLeaves->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 48px;"></i>
                    <h4 class="mt-3">No Leave Requests Found</h4>
                    <p class="text-muted">You haven't applied for any leave yet.</p>
                    @if(request('search') || request('status'))
                        <a href="{{ route('backoffice.leave-requests.index') }}" class="btn btn-sm btn-primary mt-2">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="leaveRequestsTable">
                        <thead>
                            <tr>
                                <th>LEAVE TYPE</th>
                                <th>DATES</th>
                                <th>DAYS</th>
                                <th>STATUS</th>
                                <th>APPLIED ON</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myLeaves as $leave)
                                <tr>
                                    <!-- LEAVE TYPE Column -->
                                    <td>
                                        <div class="fw-bold">{{ $leave->leaveType->name ?? 'N/A' }}</div>
                                        @if($leave->is_half_day)
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $leave->half_day_type == 'first_half' ? 'First Half' : 'Second Half' }}
                                            </small>
                                        @endif
                                    </td>
                                    
                                    <!-- DATES Column -->
                                    <td>
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($leave->from_date)->format('M d, Y') }}</div>
                                        <small class="text-muted">
                                            to {{ \Carbon\Carbon::parse($leave->to_date)->format('M d, Y') }}
                                        </small>
                                    </td>
                                    
                                    <!-- DAYS Column -->
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            {{ $leave->total_days == 0.5 ? '0.5' : number_format($leave->total_days, 1) }} day(s)
                                        </span>
                                    </td>
                                    
                                    <!-- STATUS Column -->
                                    <td>
                                        @if($leave->status == 'pending')
                                            <span class="status-badge status-pending">Pending</span>
                                        @elseif($leave->status == 'approved')
                                            <span class="status-badge status-completed">Approved</span>
                                        @elseif($leave->status == 'rejected')
                                            <span class="status-badge status-cancelled">Rejected</span>
                                        @endif
                                    </td>
                                    
                                    <!-- APPLIED ON Column -->
                                    <td>
                                        <div>{{ $leave->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $leave->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination (Hidden when DataTables is enabled) -->
                @if($myLeaves->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4" id="manualPagination">
                    <div>
                        <small class="text-muted">
                            Showing {{ $myLeaves->firstItem() }} to {{ $myLeaves->lastItem() }} of {{ $myLeaves->total() }} entries
                        </small>
                    </div>
                    <div>
                        {{ $myLeaves->withQueryString()->links() }}
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@section('modals')
    <!-- Apply for Leave Modal -->
    <div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-labelledby="applyLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyLeaveModalLabel">Apply for Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('backoffice.leave-requests.store') }}" method="POST" enctype="multipart/form-data" id="applyLeaveForm">
                    @csrf
                    <div class="modal-body">
                        <!-- Staff ID Display -->
                        @if($staff)
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-badge me-2"></i>
                                <div>
                                    <strong>Staff ID:</strong> {{ $staff->staff_id }}
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row">
                            <!-- Left Column: Leave Details -->
                            <div class="col-md-8">
                                <!-- Leave Type -->
                                <div class="mb-3">
                                    <label class="form-label required">Select Leave Type *</label>
                                    <select class="form-select" name="leave_type_id" id="leave_type_id" required>
                                        <option value="">Select the type of leave</option>
                                        @foreach($leaveTypes as $type)
                                            @php
                                                $balance = $leaveBalances->firstWhere('leave_type_id', $type->id);
                                                $remaining = $balance ? $balance['leave_left'] : 0;
                                            @endphp
                                            <option value="{{ $type->id }}" 
                                                    data-max="{{ $balance ? $balance['max_per_year'] : 0 }}"
                                                    data-used="{{ $balance ? $balance['approved_used'] : 0 }}"
                                                    data-remaining="{{ $remaining }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Select the type of leave you're applying for</div>
                                    
                                    <!-- Leave Balance Information -->
                                    <div class="alert alert-secondary mt-2 py-2" id="leaveBalanceInfo" style="display: none;">
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <small class="text-muted d-block">Yearly Limit</small>
                                                <strong id="maxLimit">0</strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Used</small>
                                                <strong id="usedDays">0</strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Remaining</small>
                                                <strong id="remainingDays" class="text-success">0</strong>
                                            </div>
                                        </div>
                                        <div class="mt-2" id="balanceWarning" style="display: none;">
                                            <small class="text-warning">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                <span id="warningText"></span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Date Range -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">From Date *</label>
                                        <input type="date" class="form-control" name="from_date" id="from_date" 
                                               min="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">To Date *</label>
                                        <input type="date" class="form-control" name="to_date" id="to_date" 
                                               min="{{ date('Y-m-d') }}" required>
                                        <div class="form-text text-warning" id="halfDayWarning" style="display: none;">
                                            <i class="bi bi-exclamation-circle me-1"></i>
                                            Half day must be for a single date only
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Half Day Option -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_half_day" name="is_half_day" value="1">
                                        <label class="form-check-label" for="is_half_day">
                                            This is a half-day leave
                                        </label>
                                    </div>
                                    <div class="form-text">Check if you're taking only half day leave</div>
                                </div>
                                
                                <!-- Half Day Type (Hidden by default) -->
                                <div class="mb-3" id="halfDayOptions" style="display: none;">
                                    <label class="form-label">Half Day Type *</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="half_day_type" value="first_half" id="firstHalf">
                                                <label class="form-check-label" for="firstHalf">
                                                    First Half (Morning)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="half_day_type" value="second_half" id="secondHalf">
                                                <label class="form-check-label" for="secondHalf">
                                                    Second Half (Afternoon)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text">Half day type selection is required for half day leave</div>
                                </div>
                                
                                <!-- Reason -->
                                <div class="mb-3">
                                    <label class="form-label required">Reason for Leave *</label>
                                    <textarea class="form-control" name="reason" rows="3" 
                                              placeholder="Please provide a reason for your leave application" 
                                              required maxlength="1000"></textarea>
                                    <div class="form-text">Maximum 1000 characters</div>
                                </div>
                            </div>
                            
                            <!-- Right Column: Supporting Document -->
                            <div class="col-md-4">
                                <!-- Supporting Document -->
                                <div class="mb-3">
                                    <label class="form-label">Supporting Document (Optional)</label>
                                    <div class="file-upload-area mb-2" onclick="document.getElementById('attachment').click()">
                                        <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                        <p class="small mb-1">Click to upload or drag and drop</p>
                                        <small class="text-muted">PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                        <input type="file" name="attachment" id="attachment" 
                                               accept=".pdf,.jpg,.jpeg,.png" hidden>
                                    </div>
                                    <div id="file-name" class="small text-muted"></div>
                                    <div class="form-text">Optional: Upload medical certificate or other supporting documents</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Leave Modal -->
    <div class="modal fade" id="cancelLeaveModal" tabindex="-1" aria-labelledby="cancelLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelLeaveModalLabel">Cancel Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this leave request for <strong id="cancelLeaveDates"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <form id="cancelLeaveForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Cancel Leave</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .file-upload-area:hover {
        border-color: #0d6efd;
        background: #e7f1ff;
    }
    
    .required:after {
        content: " *";
        color: #dc3545;
    }
    
    .form-text {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .card.border-0 {
        transition: transform 0.2s;
    }
    
    .card.border-0:hover {
        transform: translateY(-2px);
    }
    
    .progress {
        background-color: #e9ecef;
    }
    
    /* DataTables custom styling */
    .dataTables_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 6px 10px;
        font-size: 14px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 2px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        color: #495057;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #0d6efd;
        color: white !important;
        border-color: #0d6efd;
    }
</style>
@endsection

@section('scripts')
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTables for leave requests table
    if ($('#leaveRequestsTable').length && !$.fn.DataTable.isDataTable('#leaveRequestsTable')) {
        $('#leaveRequestsTable').DataTable({
            pageLength: 10,
            lengthChange: false,
            ordering: true,
            searching: true,
            info: true,
            responsive: true,
            // columnDefs entry for actions column removed
            language: {
                search: "Search leave requests:",
                info: "Showing _START_ to _END_ of _TOTAL_ requests",
                infoEmpty: "No leave requests to show",
                zeroRecords: "No matching leave requests found"
            }
        });
        
        // Hide manual pagination when DataTables is enabled
        $('#manualPagination').hide();
    }

    // Half Day Toggle
    $('#is_half_day').change(function() {
        const isChecked = $(this).is(':checked');
        const endDate = $('#to_date');
        const halfDayWarning = $('#halfDayWarning');

        if (isChecked) {
            $('#halfDayOptions').show();
            const startDate = $('#from_date').val();
            
            // If dates don't match, show warning and auto-correct
            if (startDate && endDate.val() && startDate !== endDate.val()) {
                halfDayWarning.show();
                endDate.val(startDate);
            }
            
            // Disable to date field for half day
            endDate.prop('disabled', true).addClass('bg-light');
            
            // Validate half day type selection
            if (!$('input[name="half_day_type"]:checked').val()) {
                showToast('warning', 'Please select half day type');
            }
        } else {
            $('#halfDayOptions').hide();
            $('input[name="half_day_type"]').prop('checked', false);
            halfDayWarning.hide();
            endDate.prop('disabled', false).removeClass('bg-light');
        }
    });

    // Show leave balance when leave type is selected
    $('#leave_type_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const maxLimit = selectedOption.data('max');
        const usedDays = selectedOption.data('used');
        const remainingDays = selectedOption.data('remaining');
        const balanceInfo = $('#leaveBalanceInfo');
        const warningDiv = $('#balanceWarning');
        const warningText = $('#warningText');
        
        if (selectedOption.val() && maxLimit > 0) {
            // Update balance info
            $('#maxLimit').text(maxLimit);
            $('#usedDays').text(usedDays.toFixed(1));
            $('#remainingDays').text(remainingDays.toFixed(1));
            
            // Update remaining days color based on amount
            if (remainingDays <= 0) {
                $('#remainingDays').removeClass('text-success').addClass('text-danger');
            } else if (remainingDays < 1) {
                $('#remainingDays').removeClass('text-success').addClass('text-warning');
            } else {
                $('#remainingDays').removeClass('text-danger text-warning').addClass('text-success');
            }
            
            // Show/hide warning based on remaining balance
            if (remainingDays <= 0) {
                warningText.text('No leave balance remaining');
                warningDiv.show();
            } else if (remainingDays < 1) {
                warningText.text('Less than 1 day remaining');
                warningDiv.show();
            } else if (remainingDays < 3) {
                warningText.text('Low leave balance remaining');
                warningDiv.show();
            } else {
                warningDiv.hide();
            }
            
            balanceInfo.show();
        } else {
            balanceInfo.hide();
        }
    });

    // Auto-update to date when from date changes for half day
    $('#from_date').change(function() {
        if ($('#is_half_day').is(':checked')) {
            $('#to_date').val($(this).val());
        }
    });

    // Cancel Leave Button (using event delegation for DataTables)
    $(document).on('click', '.cancel-leave-btn', function() {
        const leaveId = $(this).data('leave-id');
        const leaveDates = $(this).data('leave-dates');

        $('#cancelLeaveDates').text(leaveDates);
        $('#cancelLeaveForm').attr(
            'action',
            "{{ route('backoffice.leave-requests.cancel', ':id') }}".replace(':id', leaveId)
        );

        const cancelModal = new bootstrap.Modal(document.getElementById('cancelLeaveModal'));
        cancelModal.show();
    });

    // File upload display
    $('#attachment').on('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
        const uploadArea = $(this).closest('.file-upload-area');
        
        if (e.target.files[0]) {
            // Check file size (2MB = 2097152 bytes)
            if (e.target.files[0].size > 2097152) {
                showToast('error', 'File size must be less than 2MB');
                $(this).val('');
                $('#file-name').html('');
                return;
            }
            
            // Check file type
            const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(e.target.files[0].type)) {
                showToast('error', 'Only PDF, JPG, and PNG files are allowed');
                $(this).val('');
                $('#file-name').html('');
                return;
            }
            
            $('#file-name').html('<i class="bi bi-file-text me-1"></i>' + fileName);
            uploadArea.css({
                'border-color': '#198754',
                'background-color': 'rgba(25, 135, 84, 0.1)'
            });
        }
    });

    // Form Validation
    $('#applyLeaveForm').on('submit', function(e) {
        const leaveType = $('select[name="leave_type_id"]').val();
        const fromDate = $('input[name="from_date"]').val();
        const toDate = $('input[name="to_date"]').val();
        const reason = $('textarea[name="reason"]').val();
        const isHalfDay = $('#is_half_day').is(':checked');
        const halfDayType = $('input[name="half_day_type"]:checked').val();
        const selectedOption = $('#leave_type_id').find('option:selected');
        const remainingDays = parseFloat(selectedOption.data('remaining') || 0);
        
        // Basic validation
        if (!leaveType || !fromDate || !toDate || !reason) {
            e.preventDefault();
            showToast('error', 'Please fill all required fields');
            return false;
        }
        
        // Half day validation
        if (isHalfDay) {
            if (!halfDayType) {
                e.preventDefault();
                showToast('error', 'Please select half day type');
                return false;
            }
            
            // Check if it's single day only
            if (fromDate !== toDate) {
                e.preventDefault();
                showToast('error', 'Half day leave must be for a single date only');
                return false;
            }
        }
        
        // Calculate days
        const from = new Date(fromDate);
        const to = new Date(toDate);
        const diffTime = Math.abs(to - from);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        const totalDays = isHalfDay ? 0.5 : diffDays;
        
        // Check leave balance
        if (totalDays > remainingDays) {
            e.preventDefault();
            showToast('error', `Leave limit exceeded. Remaining: ${remainingDays.toFixed(1)} day(s) for this leave type.`);
            return false;
        }
        
        // Date validation
        if (to < from) {
            e.preventDefault();
            showToast('error', 'To date must be on or after from date');
            return false;
        }
        
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
    });

    // Auto-update end date min based on start date
    $('input[name="from_date"]').on('change', function() {
        const startDate = $(this).val();
        const endDateField = $('input[name="to_date"]');

        if (startDate) {
            endDateField.attr('min', startDate);

            if (endDateField.val() && new Date(endDateField.val()) < new Date(startDate)) {
                endDateField.val(startDate);
            }
        }
    });

    // Toast messages function
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        document.body.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', function () {
            toast.remove();
        });
    }

    // Show session messages
    @if(session('success'))
        showToast('success', @json(session('success')));
    @endif

    @if(session('error'))
        showToast('danger', @json(session('error')));
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('danger', @json($error));
        @endforeach
    @endif

    // Reset modal when closed
    $('#applyLeaveModal').on('hidden.bs.modal', function() {
        $('#applyLeaveForm')[0].reset();
        $('#halfDayOptions').hide();
        $('input[name="half_day_type"]').prop('checked', false);
        $('#leaveBalanceInfo').hide();
        $('#halfDayWarning').hide();
        $('#file-name').html('');
        $('input[name="to_date"]').prop('disabled', false).removeClass('bg-light');
        $('.file-upload-area').css({
            'border-color': '#dee2e6',
            'background-color': '#f8f9fa'
        });
    });

});
</script>
@endsection