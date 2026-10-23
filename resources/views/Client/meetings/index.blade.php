{{-- resources/views/Client/meetings/index.blade.php --}}
@extends('layouts.client')

@section('title', 'Meetings - SharpEdge Client')
@section('page-title', 'Meetings')
@section('page-subtitle', 'Request and manage your meetings with us.')

@section('content')
    {{-- ========== SUMMARY CARDS ========== --}}
    <div class="row g-4 mb-4" id="statsCards">
        <!-- Filled via JavaScript -->
    </div>

    {{-- Header with Request Button --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">All Meetings</h4>
        <button class="btn btn-primary rounded-pill px-4" id="requestMeetingBtn">
            <i class="bi bi-plus-circle me-2"></i>Request Meeting
        </button>
    </div>

    {{-- Meetings Table --}}
    <div class="section-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable" id="meetingsTable">
                <thead>
                    <tr>
                        <th>Purpose</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="meetingsTableBody">
                    <!-- Filled via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========== MODALS ========== --}}
    <!-- Modal 1: Request Meeting -->
    <div class="modal fade" id="requestMeetingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Request a Meeting</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="requestMeetingForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="purpose" class="form-label fw-semibold">Purpose <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="purpose" name="purpose" rows="3" required></textarea>
                            <div class="invalid-feedback" id="purposeError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="meeting_type" class="form-label fw-semibold">Meeting Type (optional)</label>
                            <input type="text" class="form-control" id="meeting_type" name="meeting_type" placeholder="e.g., Project Kickoff, Review">
                            <div class="invalid-feedback" id="meetingTypeError"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitRequestBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Request Meeting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: Set Availability -->
    <div class="modal fade" id="availabilityModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Set Your Availability</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="availabilityForm">
                    @csrf
                    <input type="hidden" id="availabilityMeetingId" name="meeting_id">
                    <div class="modal-body">
                        <p class="text-muted mb-3">Add one or more time slots when you are available.</p>
                        <div id="slotsContainer">
                            {{-- Slot rows will be injected here --}}
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addSlotBtn">
                            <i class="bi bi-plus-circle me-1"></i>Add another slot
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitAvailabilityBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Submit Availability
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 3: Edit Meeting -->
    <div class="modal fade" id="editMeetingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Meeting</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMeetingForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editMeetingId" name="meeting_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_purpose" class="form-label fw-semibold">Purpose <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_purpose" name="purpose" rows="3" required></textarea>
                            <div class="invalid-feedback" id="editPurposeError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_meeting_type" class="form-label fw-semibold">Meeting Type (optional)</label>
                            <input type="text" class="form-control" id="edit_meeting_type" name="meeting_type">
                            <div class="invalid-feedback" id="editMeetingTypeError"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitEditBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Update Meeting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 4: Delete Confirmation -->
    <div class="modal fade" id="deleteMeetingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Delete Meeting</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deleteMeetingId">
                    <p>Are you sure you want to delete this meeting? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 5: Request Reschedule Confirmation -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title"><i class="bi bi-arrow-repeat me-2"></i>Request Reschedule</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="rescheduleMeetingId">
                    <p class="mb-0">Are you sure you want to request a reschedule? The current meeting time will be cancelled and you will need to provide new availability.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep it</button>
                    <button type="button" class="btn btn-warning" id="confirmRescheduleBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Yes, Request Reschedule
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 6: Meeting Date & Time Popup (NEW) -->
    <div class="modal fade" id="meetingTimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-event me-2"></i>Meeting Schedule
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2"><strong>Scheduled Date & Time</strong></p>
                    <div class="alert alert-primary fw-semibold" id="meetingTimeText"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="meetingToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

{{-- ========== STYLES ========== --}}
@push('styles')
<style>
    .badge-availability-requested { background-color: #ffc107; color: #212529; }
    .badge-availability-provided { background-color: #0dcaf0; color: #212529; }
    .badge-scheduled { background-color: #198754; color: white; }
    .badge-completed { background-color: #6c757d; color: white; }
    .badge-closed { background-color: #343a40; color: white; }
    .badge-reschedule-requested { background-color: #fd7e14; color: white; }
    .action-text { font-size: 0.9rem; color: #6c757d; }
    .btn-sm.rounded-circle {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

{{-- ========== SCRIPTS ========== --}}
@push('scripts')
<script>
$(document).ready(function() {
    // Setup CSRF token and Accept header for all AJAX (FIX 2)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    // Toast helper
    var toastEl = document.getElementById('meetingToast');
    var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    function showToast(message, type = 'success') {
        $('#toastMessage').text(message);
        toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
        toastEl.classList.add(type === 'success' ? 'bg-success' : type === 'danger' ? 'bg-danger' : 'bg-warning');
        toast.show();
    }

    // ------------------------------------------------------------
    // 1. INITIAL RENDER – use data passed from the controller
    // ------------------------------------------------------------
    var initialMeetings = @json($meetings);  // injected by Laravel
    renderMeetings(initialMeetings);

    // ------------------------------------------------------------
    // 2. RENDER FUNCTIONS
    // ------------------------------------------------------------
    function renderMeetings(meetings) {
        updateStats(meetings);
        var tbody = $('#meetingsTableBody');
        tbody.empty();
        meetings.forEach(function(meeting) {
            tbody.append(createMeetingRow(meeting));
        });
    }

    function updateStats(meetings) {
        var total = meetings.length;
        var awaitingAvailability = meetings.filter(m => m.status === 'availability_requested').length;
        var scheduled = meetings.filter(m => m.status === 'scheduled').length;
        var completed = meetings.filter(m => m.status === 'completed').length;

        var statsHtml = `
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title text-muted mb-0">Total Meetings</h6>
                        <div class="card-icon bg-primary bg-opacity-10" style="background-color: rgba(29,78,216,0.1);">
                            <i class="bi bi-calendar-check text-primary"></i>
                        </div>
                    </div>
                    <h3 class="card-value">${total}</h3>
                    <span class="text-muted small">All time</span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title text-muted mb-0">Awaiting Availability</h6>
                        <div class="card-icon bg-warning bg-opacity-10" style="background-color: rgba(255,193,7,0.1);">
                            <i class="bi bi-hourglass-split text-warning"></i>
                        </div>
                    </div>
                    <h3 class="card-value">${awaitingAvailability}</h3>
                    <span class="text-muted small">Need your slots</span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title text-muted mb-0">Scheduled</h6>
                        <div class="card-icon bg-success bg-opacity-10" style="background-color: rgba(25,135,84,0.1);">
                            <i class="bi bi-check2-circle text-success"></i>
                        </div>
                    </div>
                    <h3 class="card-value">${scheduled}</h3>
                    <span class="text-muted small">Confirmed meetings</span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title text-muted mb-0">Completed</h6>
                        <div class="card-icon bg-secondary bg-opacity-10" style="background-color: rgba(108,117,125,0.1);">
                            <i class="bi bi-check-lg text-secondary"></i>
                        </div>
                    </div>
                    <h3 class="card-value">${completed}</h3>
                    <span class="text-muted small">Finished meetings</span>
                </div>
            </div>
        `;
        $('#statsCards').html(statsHtml);
    }

    function createMeetingRow(meeting) {
        var status = meeting.status;
        var statusBadge = getStatusBadge(status);
        var actions = getActionButtons(meeting.id, status, meeting.purpose, meeting.meeting_type);

        // NEW: Date & Time column with clickable link
        var dateTimeCell = '';
        if (meeting.scheduled_at) {
            var formatted = new Date(meeting.scheduled_at).toLocaleString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            dateTimeCell = `<a href="javascript:void(0)" class="text-primary fw-semibold view-meeting-time" data-datetime="${meeting.scheduled_at}">${formatted}</a>`;
        } else {
            dateTimeCell = '—';
        }

        return `
            <tr id="meeting-row-${meeting.id}">
                <td>${escapeHtml(meeting.purpose)}</td>
                <td>${escapeHtml(meeting.meeting_type || '—')}</td>
                <td class="status-badge">${statusBadge}</td>
                <td class="datetime-cell">${dateTimeCell}</td>
                <td class="actions-cell">${actions}</td>
            </tr>
        `;
    }

    // 🔴 CHANGE 1 – STATUS TEXT CLEAR KORA
    function getStatusBadge(status) {
        var badges = {
            'availability_requested': '<span class="badge badge-availability-requested rounded-pill px-3 py-2">Waiting for Your Availability</span>',
            'availability_provided': '<span class="badge badge-availability-provided rounded-pill px-3 py-2">Availability Submitted</span>',
            'scheduled': '<span class="badge badge-scheduled rounded-pill px-3 py-2">Meeting Scheduled</span>',
            'completed': '<span class="badge badge-completed rounded-pill px-3 py-2">Meeting Completed</span>',
            'closed': '<span class="badge badge-closed rounded-pill px-3 py-2">Meeting Closed</span>',
            'reschedule_requested': '<span class="badge badge-reschedule-requested rounded-pill px-3 py-2">Reschedule Requested</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
    }

    function getActionButtons(meetingId, status, purpose = '', type = '') {
        var purposeAttr = escapeHtml(purpose);
        var typeAttr = escapeHtml(type || '');
        var buttons = '<div class="d-flex gap-2 flex-wrap align-items-center">';

        if (status === 'availability_requested' || status === 'reschedule_requested') {
            buttons += `<button class="btn btn-sm btn-outline-primary rounded-pill px-3 set-availability-btn" data-id="${meetingId}"><i class="bi bi-calendar-check me-1"></i>Set Availability</button>`;
        }

        if (status === 'availability_provided') {
            buttons += `<span class="action-text"><i class="bi bi-check-circle me-1"></i>Availability submitted</span>`;
        } else if (status === 'scheduled') {
            buttons += `<button class="btn btn-sm btn-outline-warning rounded-pill px-3 request-reschedule-btn" data-id="${meetingId}"><i class="bi bi-arrow-repeat me-1"></i>Request Reschedule</button>`;
        } else if (status === 'completed') {
            buttons += `<span class="action-text"><i class="bi bi-check2-circle me-1"></i>Meeting completed</span>`;
        } else if (status === 'closed') {
            buttons += `<span class="action-text"><i class="bi bi-lock me-1"></i>Meeting closed</span>`;
        }

        // 🔴 CHANGE 2 – EDIT/DELETE RULE STRICT (already correct)
        if (['availability_requested', 'availability_provided'].includes(status)) {
            buttons += `
                <button class="btn btn-sm btn-outline-secondary rounded-circle edit-meeting-btn" data-id="${meetingId}" data-purpose="${purposeAttr}" data-type="${typeAttr}" title="Edit Meeting">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger rounded-circle delete-meeting-btn" data-id="${meetingId}" title="Delete Meeting">
                    <i class="bi bi-trash"></i>
                </button>
            `;
        }

        buttons += '</div>';
        return buttons;
    }

    function escapeHtml(unsafe) {
        if (!unsafe) return unsafe;
        return unsafe.replace(/[&<>"]/g, function(m) {
            if(m === '&') return '&amp;'; if(m === '<') return '&lt;'; if(m === '>') return '&gt;'; if(m === '"') return '&quot;';
            return m;
        });
    }

    function updateStatsFromAllRows() {
        var meetings = [];
        $('#meetingsTableBody tr').each(function() {
            var id = $(this).attr('id').replace('meeting-row-', '');
            var statusSpan = $(this).find('.status-badge span').text().trim().toLowerCase().replace(' ', '_');
            var statusMap = {
                'availability_requested': 'availability_requested',
                'availability_provided': 'availability_provided',
                'scheduled': 'scheduled',
                'completed': 'completed',
                'closed': 'closed',
                'reschedule_requested': 'reschedule_requested'
            };
            var status = statusMap[statusSpan] || 'unknown';
            meetings.push({ status: status });
        });
        updateStats(meetings);
    }

    // ---------- Request Meeting Modal ----------
    $('#requestMeetingBtn').click(function() {
        $('#requestMeetingForm')[0].reset();
        $('.invalid-feedback').text('').hide();
        $('#requestMeetingModal').modal('show');
    });

    $('#requestMeetingForm').submit(function(e) {
        e.preventDefault();
        var btn = $('#submitRequestBtn');
        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: '{{ route("client.meetings.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#requestMeetingModal').modal('hide');
                showToast('Meeting requested successfully.', 'success');

                var newRow = createMeetingRow(response.meeting);
                $('#meetingsTableBody').prepend(newRow);
                updateStatsFromAllRows();

                // 🔴 CHANGE 4 – AUTO OPEN AVAILABILITY MODAL (already there)
                openAvailabilityModal(response.meeting.id);
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if (errors) {
                    $('#purposeError').text(errors.purpose?.[0] || '').show();
                    $('#meetingTypeError').text(errors.meeting_type?.[0] || '').show();
                } else {
                    showToast('An error occurred. Please try again.', 'danger');
                }
            },
            complete: function() {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
            }
        });
    });

    // ---------- Availability Modal ----------
    function openAvailabilityModal(meetingId) {
        $('#availabilityMeetingId').val(meetingId);
        $('#slotsContainer').empty().append(getSlotRow(0));
        $('#availabilityModal').modal('show');
    }

    function getSlotRow(index) {
        return `
            <div class="row g-2 mb-2 slot-row" data-index="${index}">
                <div class="col-md-4">
                    <input type="date" class="form-control" name="slots[${index}][date]" required>
                </div>
                <div class="col-md-3">
                    <input type="time" class="form-control" name="slots[${index}][start_time]" required>
                </div>
                <div class="col-md-3">
                    <input type="time" class="form-control" name="slots[${index}][end_time]" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-slot" ${index === 0 ? 'disabled' : ''}>
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    $('#addSlotBtn').click(function() {
        var index = $('.slot-row').length;
        $('#slotsContainer').append(getSlotRow(index));
        $('.remove-slot').prop('disabled', function(idx) { return idx === 0; });
    });

    $(document).on('click', '.remove-slot', function() {
        $(this).closest('.slot-row').remove();
        $('#slotsContainer .slot-row').each(function(i) {
            $(this).find('input').each(function() {
                var name = $(this).attr('name').replace(/\[\d+\]/, '[' + i + ']');
                $(this).attr('name', name);
            });
            $(this).attr('data-index', i);
        });
        $('.remove-slot').prop('disabled', function(idx) { return idx === 0; });
    });

    // FIX 1: Availability form submission (use serialize, not JSON)
    $('#availabilityForm').submit(function(e) {
        e.preventDefault();

        var meetingId = $('#availabilityMeetingId').val();
        var btn = $('#submitAvailabilityBtn');

        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: '{{ route("client.meetings.availability", ":id") }}'.replace(':id', meetingId),
            type: 'POST',
            data: $('#availabilityForm').serialize(),   // ✅ FIXED: standard form data
            success: function(response) {
                $('#availabilityModal').modal('hide');
                showToast('Availability submitted.', 'success');
                updateMeetingRow(meetingId, 'availability_provided');
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Failed to submit availability.';
                showToast(msg, 'danger');
            },
            complete: function() {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
            }
        });
    });

    // ---------- Edit Meeting Modal ----------
    $(document).on('click', '.edit-meeting-btn', function() {
        var meetingId = $(this).data('id');
        var purpose = $(this).data('purpose');
        var type = $(this).data('type') || '';
        $('#editMeetingId').val(meetingId);
        $('#edit_purpose').val(purpose);
        $('#edit_meeting_type').val(type);
        $('.invalid-feedback').text('').hide();
        $('#editMeetingModal').modal('show');
    });

    $('#editMeetingForm').submit(function(e) {
        e.preventDefault();
        var meetingId = $('#editMeetingId').val();
        var btn = $('#submitEditBtn');
        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: '/client/meetings/' + meetingId,
            type: 'POST',
            data: $(this).serialize() + '&_method=PUT',
            success: function(response) {
                $('#editMeetingModal').modal('hide');
                showToast('Meeting updated successfully.', 'success');
                var row = $(`#meeting-row-${meetingId}`);
                row.find('td:eq(0)').text($('#edit_purpose').val());
                row.find('td:eq(1)').text($('#edit_meeting_type').val() || '—');
                row.find('.edit-meeting-btn').data('purpose', $('#edit_purpose').val());
                row.find('.edit-meeting-btn').data('type', $('#edit_meeting_type').val());
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if (errors) {
                    $('#editPurposeError').text(errors.purpose?.[0] || '').show();
                    $('#editMeetingTypeError').text(errors.meeting_type?.[0] || '').show();
                } else {
                    showToast('Update failed. Please try again.', 'danger');
                }
            },
            complete: function() {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
            }
        });
    });

    // ---------- Delete Meeting Modal ----------
    $(document).on('click', '.delete-meeting-btn', function() {
        var meetingId = $(this).data('id');
        $('#deleteMeetingId').val(meetingId);
        $('#deleteMeetingModal').modal('show');
    });

    $('#confirmDeleteBtn').click(function() {
        var meetingId = $('#deleteMeetingId').val();
        var btn = $(this);
        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: '/client/meetings/' + meetingId,
            type: 'POST',
            data: { _method: 'DELETE' },
            success: function(response) {
                $('#deleteMeetingModal').modal('hide');
                showToast('Meeting deleted successfully.', 'success');
                $(`#meeting-row-${meetingId}`).remove();
                updateStatsFromAllRows();
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Failed to delete meeting.';
                showToast(msg, 'danger');
            },
            complete: function() {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
            }
        });
    });

    // ---------- Reschedule Modal ----------
    $(document).on('click', '.request-reschedule-btn', function() {
        var meetingId = $(this).data('id');
        $('#rescheduleMeetingId').val(meetingId);
        $('#rescheduleModal').modal('show');
    });

    $('#confirmRescheduleBtn').click(function() {
        var meetingId = $('#rescheduleMeetingId').val();
        var btn = $(this);
        btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: '{{ route("client.meetings.reschedule", ":id") }}'.replace(':id', meetingId),
            type: 'POST',
            success: function(response) {
                $('#rescheduleModal').modal('hide');
                showToast('Reschedule requested.', 'success');
                updateMeetingRow(meetingId, 'reschedule_requested');
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Failed to request reschedule.';
                showToast(msg, 'danger');
            },
            complete: function() {
                btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
            }
        });
    });

    // ---------- Click handler for date/time link (NEW) ----------
    $(document).on('click', '.view-meeting-time', function () {
        var dateTime = $(this).data('datetime');

        var formatted = new Date(dateTime).toLocaleString('en-GB', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        $('#meetingTimeText').text(formatted);
        $('#meetingTimeModal').modal('show');
    });

    function updateMeetingRow(meetingId, newStatus) {
        var row = $(`#meeting-row-${meetingId}`);
        var badgeHtml = getStatusBadge(newStatus);
        row.find('.status-badge').html(badgeHtml);
        var purpose = row.find('td:eq(0)').text();
        var type = row.find('td:eq(1)').text() === '—' ? '' : row.find('td:eq(1)').text();
        var actionsHtml = getActionButtons(meetingId, newStatus, purpose, type);
        row.find('.actions-cell').html(actionsHtml);
        updateStatsFromAllRows();
    }

    $(document).on('click', '.set-availability-btn', function() {
        openAvailabilityModal($(this).data('id'));
    });

    $(document).on('shown.bs.modal', '#availabilityModal', function() {
        $('.remove-slot').prop('disabled', function(idx) { return idx === 0; });
    });
});
</script>
@endpush