@extends('layouts.Backoffice')

@section('page-title', 'Meeting Management')

@section('content')
    @php
        // Compute stats from the collection passed from controller ($meetings)
        $stats = [
            'total'                  => $meetings->count(),
            'availability_requested' => $meetings->where('status', 'availability_requested')->count(),
            'availability_provided'  => $meetings->where('status', 'availability_provided')->count(),
            'scheduled'              => $meetings->where('status', 'scheduled')->count(),
            'completed'              => $meetings->where('status', 'completed')->count(),
        ];
    @endphp

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TOTAL</h6>
                    <div class="card-icon bg-primary"><i class="bi bi-calendar"></i></div>
                </div>
                <h3 class="card-value" id="stat-total">{{ $stats['total'] }}</h3>
                <span class="text-muted small">All meetings</span>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">AWAITING</h6>
                    <div class="card-icon bg-warning"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <h3 class="card-value" id="stat-awaiting">{{ $stats['availability_requested'] }}</h3>
                <span class="text-muted small">Client availability</span>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PROVIDED</h6>
                    <div class="card-icon bg-info"><i class="bi bi-clock-history"></i></div>
                </div>
                <h3 class="card-value" id="stat-provided">{{ $stats['availability_provided'] }}</h3>
                <span class="text-muted small">Slots submitted</span>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">SCHEDULED</h6>
                    <div class="card-icon bg-success"><i class="bi bi-calendar-check"></i></div>
                </div>
                <h3 class="card-value" id="stat-scheduled">{{ $stats['scheduled'] }}</h3>
                <span class="text-muted small">Confirmed</span>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">COMPLETED</h6>
                    <div class="card-icon bg-secondary"><i class="bi bi-check-circle"></i></div>
                </div>
                <h3 class="card-value" id="stat-completed">{{ $stats['completed'] }}</h3>
                <span class="text-muted small">Finished</span>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="content-section">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">All Meetings</h4>
            </div>

            <!-- Optional Status Filter Tabs -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('backoffice.meetings.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                <a href="{{ route('backoffice.meetings.index', ['status' => 'availability_requested']) }}" class="btn btn-sm {{ request('status') == 'availability_requested' ? 'btn-primary' : 'btn-outline-primary' }}">Awaiting</a>
                <a href="{{ route('backoffice.meetings.index', ['status' => 'availability_provided']) }}" class="btn btn-sm {{ request('status') == 'availability_provided' ? 'btn-primary' : 'btn-outline-primary' }}">Provided</a>
                <a href="{{ route('backoffice.meetings.index', ['status' => 'scheduled']) }}" class="btn btn-sm {{ request('status') == 'scheduled' ? 'btn-primary' : 'btn-outline-primary' }}">Scheduled</a>
                <a href="{{ route('backoffice.meetings.index', ['status' => 'completed']) }}" class="btn btn-sm {{ request('status') == 'completed' ? 'btn-primary' : 'btn-outline-primary' }}">Completed</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="meetingsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Scheduled Date & Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meetings as $meeting)
                            @php
                                $statusClass = [
                                    'availability_requested' => 'bg-warning',
                                    'availability_provided'  => 'bg-info',
                                    'scheduled'              => 'bg-success',
                                    'completed'               => 'bg-secondary',
                                    'cancelled'               => 'bg-danger',
                                ][$meeting->status] ?? 'bg-secondary';
                            @endphp
                            <tr data-meeting-id="{{ $meeting->id }}" data-status="{{ $meeting->status }}">
                                <td>#{{ $meeting->id }}</td>
                                <td>{{ $meeting->client->name ?? 'N/A' }}</td>
                                <td>{{ $meeting->purpose }}</td>
                                <td>
                                    <span class="badge {{ $statusClass }} status-badge">
                                        {{ ucfirst(str_replace('_', ' ', $meeting->status)) }}
                                    </span>
                                </td>
                                <td class="scheduled-datetime">
                                    @if($meeting->final_datetime)
                                        {{ \Carbon\Carbon::parse($meeting->final_datetime)->format('M d, Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="action-buttons">
                                    @if($meeting->status === 'availability_requested')
                                        <button class="btn btn-sm btn-outline-info view-availability-btn" data-id="{{ $meeting->id }}">View Availability</button>
                                    @elseif($meeting->status === 'availability_provided')
                                        <button class="btn btn-sm btn-outline-info view-availability-btn" data-id="{{ $meeting->id }}">View Availability</button>
                                        <button class="btn btn-sm btn-outline-primary schedule-btn" data-id="{{ $meeting->id }}">Schedule Meeting</button>
                                    @elseif($meeting->status === 'scheduled')
                                        <button class="btn btn-sm btn-outline-success complete-btn" data-id="{{ $meeting->id }}">Mark as Completed</button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========== MODALS ========== -->

    <!-- View Availability Modal -->
    <div class="modal fade" id="viewAvailabilityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Client Availability Slots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="availabilityContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Loading availability...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Meeting Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="scheduleForm">
                    @csrf
                    <input type="hidden" id="scheduleMeetingId" name="meeting_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Schedule Meeting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select an availability slot</label>
                            <div id="scheduleSlotsList" class="border rounded p-3 bg-light">
                                <!-- Slots will be injected here -->
                            </div>
                        </div>
                        <!-- Optional staff dropdown – remove if not needed -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Assign to staff (optional)</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Select staff</option>
                                {{-- Populate with staff users if available --}}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="scheduleSubmit">Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Complete Modal -->
    <div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Completion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to mark this meeting as <strong>completed</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmCompleteBtn">Yes, Complete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .stats-card .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
        }
        .action-buttons .btn-sm {
            margin-right: 0.25rem;
        }
        .slot-radio {
            display: block;
            padding: 0.5rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
            background: white;
        }
        .slot-radio:hover {
            background: #f8f9fa;
        }
    </style>
@endsection

@section('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // CSRF token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Toastr options
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            // Initialize DataTable
            const table = $('#meetingsTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                ordering: true,
                searching: true,
                info: true,
                columnDefs: [
                    { orderable: false, targets: [5] } // Actions column not sortable
                ],
                language: {
                    search: "Search meetings:",
                    info: "Showing _START_ to _END_ of _TOTAL_ meetings",
                    infoEmpty: "No meetings to show",
                    zeroRecords: "No matching meetings found"
                }
            });

            // Helper: update stats cards based on current table data
            function updateStats() {
                let total = 0, awaiting = 0, provided = 0, scheduled = 0, completed = 0;
                $('#meetingsTable tbody tr').each(function() {
                    total++;
                    const status = $(this).data('status');
                    if (status === 'availability_requested') awaiting++;
                    else if (status === 'availability_provided') provided++;
                    else if (status === 'scheduled') scheduled++;
                    else if (status === 'completed') completed++;
                });
                $('#stat-total').text(total);
                $('#stat-awaiting').text(awaiting);
                $('#stat-provided').text(provided);
                $('#stat-scheduled').text(scheduled);
                $('#stat-completed').text(completed);
            }

            // View Availability
            $(document).on('click', '.view-availability-btn', function() {
                const meetingId = $(this).data('id');
                $('#viewAvailabilityModal').modal('show');
                $('#availabilityContent').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Loading availability...</p>
                    </div>
                `);

                $.ajax({
                    url: "{{ route('backoffice.meetings.show', ':id') }}".replace(':id', meetingId),
                    method: 'GET',
                    success: function(res) {
                        if (res.status) {
                            const meeting = res.meeting;
                            let slotsHtml = '';
                            if (meeting.availabilities && meeting.availabilities.length) {
                                meeting.availabilities.forEach(slot => {
                                    const date = new Date(slot.available_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                                    slotsHtml += `
                                        <div class="slot-radio">
                                            <strong>${date}</strong> ${slot.start_time} - ${slot.end_time}
                                        </div>
                                    `;
                                });
                            } else {
                                slotsHtml = '<p class="text-muted">No availability slots provided.</p>';
                            }
                            $('#availabilityContent').html(slotsHtml);
                        } else {
                            $('#availabilityContent').html('<p class="text-danger">Failed to load availability.</p>');
                        }
                    },
                    error: function() {
                        $('#availabilityContent').html('<p class="text-danger">Error loading availability.</p>');
                    }
                });
            });

            // Schedule button click – open modal and load slots as radio buttons
            $(document).on('click', '.schedule-btn', function() {
                const meetingId = $(this).data('id');
                $('#scheduleMeetingId').val(meetingId);
                $('#scheduleSlotsList').html('<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading slots...</div>');
                $('#scheduleModal').modal('show');

                $.ajax({
                    url: "{{ route('backoffice.meetings.show', ':id') }}".replace(':id', meetingId),
                    method: 'GET',
                    success: function(res) {
                        if (res.status) {
                            const meeting = res.meeting;
                            let slotsHtml = '';
                            if (meeting.availabilities && meeting.availabilities.length) {
                                meeting.availabilities.forEach(slot => {
                                    const date = new Date(slot.available_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                                    slotsHtml += `
                                        <label class="slot-radio">
                                            <input type="radio" name="availability_id" value="${slot.id}" required>
                                            <span class="ms-2"><strong>${date}</strong> ${slot.start_time} - ${slot.end_time}</span>
                                        </label>
                                    `;
                                });
                            } else {
                                slotsHtml = '<p class="text-muted">No slots available to schedule.</p>';
                            }
                            $('#scheduleSlotsList').html(slotsHtml);
                        } else {
                            $('#scheduleSlotsList').html('<p class="text-danger">Failed to load slots.</p>');
                        }
                    },
                    error: function() {
                        $('#scheduleSlotsList').html('<p class="text-danger">Error loading slots.</p>');
                    }
                });
            });

            // Submit schedule form
            $('#scheduleForm').submit(function(e) {
                e.preventDefault();
                const meetingId = $('#scheduleMeetingId').val();
                const formData = $(this).serialize();

                $('#scheduleSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Scheduling...');

                $.ajax({
                    url: "{{ route('backoffice.meetings.schedule', ':id') }}".replace(':id', meetingId),
                    method: 'POST',
                    data: formData,
                    success: function(res) {
                        if (res.status) {
                            toastr.success(res.message);
                            $('#scheduleModal').modal('hide');

                            // Update the row in DataTable
                            const row = $(`tr[data-meeting-id="${meetingId}"]`);
                            row.data('status', 'scheduled');
                            row.find('.status-badge').removeClass().addClass('badge bg-success status-badge').text('Scheduled');
                            if (res.scheduled_at) {
                                const formatted = new Date(res.scheduled_at).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                                row.find('.scheduled-datetime').text(formatted);
                            }
                            // Replace action buttons
                            row.find('.action-buttons').html(`
                                <button class="btn btn-sm btn-outline-success complete-btn" data-id="${meetingId}">Mark as Completed</button>
                            `);
                            updateStats();
                        } else {
                            toastr.error(res.message || 'Failed to schedule');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Error scheduling';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        toastr.error(msg);
                    },
                    complete: function() {
                        $('#scheduleSubmit').prop('disabled', false).html('Schedule');
                    }
                });
            });

            // Mark as Completed button click
            $(document).on('click', '.complete-btn', function() {
                const meetingId = $(this).data('id');
                $('#completeModal').data('meeting-id', meetingId).modal('show');
            });

            $('#confirmCompleteBtn').click(function() {
                const meetingId = $('#completeModal').data('meeting-id');
                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: "{{ route('backoffice.meetings.complete', ':id') }}".replace(':id', meetingId),
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.status) {
                            toastr.success(res.message);
                            $('#completeModal').modal('hide');

                            const row = $(`tr[data-meeting-id="${meetingId}"]`);
                            row.data('status', 'completed');
                            row.find('.status-badge').removeClass().addClass('badge bg-secondary status-badge').text('Completed');
                            row.find('.action-buttons').html('<span class="text-muted">—</span>');
                            updateStats();
                        } else {
                            toastr.error(res.message || 'Failed to complete');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Error completing meeting';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        toastr.error(msg);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('Yes, Complete');
                    }
                });
            });

            // Reset modals when hidden
            $('#viewAvailabilityModal, #scheduleModal, #completeModal').on('hidden.bs.modal', function() {
                $(this).find('.modal-body').html(''); // clear content
            });
        });
    </script>
@endsection