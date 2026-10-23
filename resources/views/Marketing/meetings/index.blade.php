@extends('layouts.marketing')

@section('title', 'Meeting Requests - SharpEdge Marketing')

@section('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    /* Statistics Cards */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-info h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
        line-height: 1;
    }
    
    .stat-info p {
        color: var(--gray-500);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0;
    }
    
    /* Main Content Card */
    .main-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }
    
    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .header-left h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .header-left p {
        color: var(--gray-500);
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    /* Request Meeting Button */
    .btn-request {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .btn-request:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    /* Table Styles */
    .table-container {
        padding: 0;
        overflow-x: auto;
    }
    
    .meetings-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .meetings-table thead {
        background-color: #3b4f6b !important;
    }
    
    .meetings-table th {
        padding: 1rem 1rem;
        text-align: left;
        font-weight: 600;
        color: white !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: none;
        white-space: nowrap;
    }
    
    .meetings-table td {
        padding: 1rem 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
        color: var(--gray-700);
        font-size: 0.875rem;
        background: white;
    }
    
    .meetings-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .meetings-table tbody tr:hover {
        background-color: var(--gray-50);
    }
    
    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .status-badge.pending_approval {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-badge.approved {
        background: #d4edda;
        color: #155724;
    }
    
    .status-badge.completed {
        background: #cce5ff;
        color: #004085;
    }
    
    /* Modal Styles */
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .form-label.required:after {
        content: " *";
        color: #dc3545;
    }
    
    /* Manual client toggle */
    .manual-client-field {
        display: none;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }
        
        .card-header {
            flex-direction: column;
            align-items: stretch;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .meetings-table {
            min-width: 1000px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header">
        <h1>Meeting Requests</h1>
        <p>Schedule meetings for admin approval</p>
    </div>

    <!-- Statistics Cards (using correct statuses) -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $meetings->count() }}</h3>
                <p>Total Requests</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #e58e0b);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $meetings->where('status', 'pending_approval')->count() }}</h3>
                <p>Pending Approval</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary-color), #0da271);">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $meetings->where('status', 'approved')->count() }}</h3>
                <p>Approved</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success-color), #0da271);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $meetings->where('status', 'completed')->count() }}</h3>
                <p>Completed</p>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="main-card">
        <div class="card-header">
            <div class="header-left">
                <h2>All Meetings</h2>
                <p>Meetings you have scheduled</p>
            </div>
            
            <!-- Schedule Meeting Button -->
            <button type="button" class="btn btn-request" data-bs-toggle="modal" data-bs-target="#scheduleMeetingModal">
                <i class="fas fa-plus"></i> Schedule Meeting
            </button>
        </div>

        <!-- Meetings Table -->
        <div class="table-container">
            <table class="meetings-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>CLIENT</th>
                        <th>MEETING TYPE</th>
                        <th>PURPOSE</th>
                        <th>DATE</th>
                        <th>TIME</th>
                        <th>STATUS</th>
                        <th>REQUESTED DATE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $meeting)
                    <tr>
                        <td>
                            <span class="client-code-box">MTG-{{ str_pad($meeting->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="client-profile-cell">
                                @if($meeting->client)
                                    @if($meeting->client->profile_photo)
                                        <img src="{{ asset('storage/' . str_replace('public/', '', $meeting->client->profile_photo)) }}" 
                                             alt="{{ $meeting->client->name }}" 
                                             class="client-avatar" style="width:50px; height:50px;">
                                    @else
                                        <div class="client-avatar">
                                            {{ strtoupper(substr($meeting->client->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="client-details">
                                        <div class="client-name-row">{{ $meeting->client->name }}</div>
                                        <div class="client-id-label">{{ $meeting->client->company_name ?? 'No Company' }}</div>
                                    </div>
                                @else
                                    <div class="client-details">
                                        <div class="client-name-row">{{ $meeting->manual_client_name }}</div>
                                        <div class="client-id-label">Manual Entry</div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>{{ $meeting->meeting_type }}</td>
                        <td>{{ Str::limit($meeting->purpose, 40) }}</td>
                        <td>{{ $meeting->meeting_date ? \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y') : '—' }}</td>
                        <td>{{ $meeting->meeting_time ?? '—' }}</td>
                        <td>
                            <span class="status-badge {{ $meeting->status }}">
                                @if($meeting->status == 'pending_approval')
                                    Pending Approval
                                @elseif($meeting->status == 'approved')
                                    Approved
                                @elseif($meeting->status == 'completed')
                                    Completed
                                @else
                                    {{ ucfirst($meeting->status) }}
                                @endif
                            </span>
                        </td>
                        <td>{{ $meeting->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No meetings scheduled</p>
                            <p class="text-muted small">Click the "Schedule Meeting" button to create one.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Schedule Meeting Modal (matches controller fields) -->
<div class="modal fade" id="scheduleMeetingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule a Meeting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scheduleMeetingForm" method="POST" action="{{ route('marketing.meetings.store') }}">
                @csrf
                <div class="modal-body">
                    <!-- Client selection with manual fallback -->
                    <div class="mb-3">
                        <label class="form-label">Select Client</label>
                        <select class="form-select" name="client_id" id="clientSelect">
                            <option value="">-- Select an existing client (optional) --</option>
                            <option value="manual">-- Enter new client manually --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->company_name ?? 'No Company' }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">If your client is not in the list, select "Enter new client manually"</small>
                    </div>
                    
                    <div class="mb-3 manual-client-field" id="manualClientField" style="display: none;">
                        <label class="form-label required">New Client Name</label>
                        <input type="text" class="form-control" name="manual_client_name" id="manualClientName" placeholder="Enter client name">
                        <small class="text-muted">This client will be saved for future meetings</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Meeting Type</label>
                        <input type="text" class="form-control" name="meeting_type" 
                               placeholder="e.g., Discovery Call, Project Review" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Purpose</label>
                        <textarea class="form-control" name="purpose" rows="2" 
                                  placeholder="Brief description of the meeting purpose" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Meeting Date</label>
                            <input type="date" class="form-control" name="meeting_date" id="meetingDate" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Meeting Time</label>
                            <input type="time" class="form-control" name="meeting_time" id="meetingTime" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Schedule & Send for Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Toastr setup
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Function to reset the form completely
    function resetForm() {
        const form = $('#scheduleMeetingForm')[0];
        form.reset(); // Native reset
        
        // Clear all form fields manually to be sure
        $('#clientSelect').val('');
        $('#manualClientName').val('');
        $('input[name="meeting_type"]').val('');
        $('textarea[name="purpose"]').val('');
        $('#meetingDate').val('');
        $('#meetingTime').val('');
        
        // Reset manual client field state
        $('#manualClientField').hide();
        $('#manualClientName').prop('required', false);
        
        // Remove any validation classes
        $('.is-invalid').removeClass('is-invalid');
        $('.form-control').removeClass('is-valid');
    }

    // Toggle manual client field based on dropdown selection
    $('#clientSelect').on('change', function() {
        const value = $(this).val();
        
        if (value === 'manual') {
            $('#manualClientField').show();
            $('#manualClientName').prop('required', true);
        } else if (value === '' || value === null) {
            $('#manualClientField').hide();
            $('#manualClientName').prop('required', false);
        } else {
            // Existing client selected
            $('#manualClientField').hide();
            $('#manualClientName').prop('required', false);
        }
    });

    // Form submission (AJAX)
    $('#scheduleMeetingForm').on('submit', function(e) {
        e.preventDefault();

        // Basic validation
        const clientId = $('#clientSelect').val();
        const manualName = $('#manualClientName').val().trim();
        const meetingType = $('input[name="meeting_type"]').val().trim();
        const purpose = $('textarea[name="purpose"]').val().trim();
        const meetingDate = $('#meetingDate').val();
        const meetingTime = $('#meetingTime').val();

        // Custom validation logic
        if (clientId === 'manual' && !manualName) {
            toastr.error('Please enter the new client name');
            $('#manualClientName').focus();
            return;
        }
        
        if ((!clientId || clientId === '') && !manualName) {
            toastr.error('Please either select a client or enter a manual client name');
            return;
        }
        
        if (!meetingType) {
            toastr.error('Please enter meeting type');
            $('input[name="meeting_type"]').focus();
            return;
        }
        
        if (!purpose) {
            toastr.error('Please enter meeting purpose');
            $('textarea[name="purpose"]').focus();
            return;
        }
        
        if (!meetingDate) {
            toastr.error('Please select meeting date');
            $('#meetingDate').focus();
            return;
        }
        
        if (!meetingTime) {
            toastr.error('Please select meeting time');
            $('#meetingTime').focus();
            return;
        }

        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        $.ajax({
            url: "{{ route('marketing.meetings.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                toastr.success(response.message);
                $('#scheduleMeetingModal').modal('hide');
                
                // Reset form after successful submission
                resetForm();
                
                // Reload page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1200);
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const firstKey = Object.keys(errors)[0];
                    message = errors[firstKey][0];
                    
                    // Highlight the field with error
                    if (firstKey) {
                        $(`[name="${firstKey}"]`).addClass('is-invalid');
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Schedule & Send for Approval');
            }
        });
    });

    // Reset modal on close
    $('#scheduleMeetingModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    // Reset modal when shown
    $('#scheduleMeetingModal').on('show.bs.modal', function() {
        resetForm();
    });

    // Remove validation classes on input
    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endsection