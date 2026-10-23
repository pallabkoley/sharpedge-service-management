@extends('layouts.client')

@section('title', 'Support Tickets - SharpEdge Client')

@section('page-title', 'Support Tickets')
@section('page-subtitle', 'Track and manage your support requests.')

@section('content')
    {{-- Summary Cards (unchanged) --}}
    <div class="row g-4 mb-4">
        @php
            $totalTickets = $tickets->count();
            $openTickets = $tickets->where('status', 'open')->count();
            $resolvedTickets = $tickets->where('status', 'resolved')->count();
            $overdueTickets = $tickets->filter(function ($t) {
                return $t->isOverdue();
            })->count();
        @endphp

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Total Tickets</h6>
                    <div class="card-icon bg-primary bg-opacity-10">
                        <i class="bi bi-ticket text-primary"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $totalTickets }}</h3>
                <span class="text-muted small">All time</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Open</h6>
                    <div class="card-icon bg-info bg-opacity-10">
                        <i class="bi bi-envelope-open text-info"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $openTickets }}</h3>
                <span class="text-muted small">Awaiting response</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Resolved</h6>
                    <div class="card-icon bg-success bg-opacity-10">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $resolvedTickets }}</h3>
                <span class="text-muted small">Awaiting confirmation</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Overdue</h6>
                    <div class="card-icon bg-danger bg-opacity-10">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $overdueTickets }}</h3>
                <span class="text-muted small">Past SLA</span>
            </div>
        </div>
    </div>

    {{-- Tickets Table (unchanged) --}}
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="section-title mb-0">All Tickets</h4>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="bi bi-plus-lg me-1"></i> New Ticket
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable" id="ticketsTable">
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Order ID</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td class="fw-bold">{{ $ticket->ticket_no }}</td>
                            <td>{{ $ticket->order?->order_id ?? '—' }}</td>
                            <td>{{ $ticket->title }}</td>
                            <td>
                                @php
                                    $priority = $ticket->priority ?? 'medium';
                                    $priorityClasses = [
                                        'low' => 'bg-secondary',
                                        'medium' => 'bg-info',
                                        'high' => 'bg-warning text-dark',
                                    ];
                                    $priorityLabels = [
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                    ];
                                @endphp
                                <span class="badge {{ $priorityClasses[$priority] ?? 'bg-secondary' }} px-3 py-2 rounded-pill">
                                    {{ $priorityLabels[$priority] ?? ucfirst($priority) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $status = $ticket->status ?? 'open';
                                    $statusClasses = [
                                        'open' => 'bg-primary',
                                        'in_progress' => 'bg-info',
                                        'resolved' => 'bg-success',
                                        'reopened' => 'bg-warning text-dark',
                                        'closed' => 'bg-secondary',
                                    ];
                                    $statusLabels = [
                                        'open' => 'Open',
                                        'in_progress' => 'In Progress',
                                        'resolved' => 'Resolved',
                                        'reopened' => 'Reopened',
                                        'closed' => 'Closed',
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$status] ?? 'bg-secondary' }} px-3 py-2 rounded-pill">
                                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                                </span>
                            </td>
                            <td>
                                @if($ticket->due_at)
                                    <span class="{{ $ticket->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                        {{ $ticket->due_at->format('d M Y H:i') }}
                                        @if($ticket->isOverdue())
                                            <i class="bi bi-exclamation-circle ms-1" title="Overdue"></i>
                                        @endif
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-view-ticket" data-id="{{ $ticket->id }}">
                                        <i class="bi bi-eye me-1"></i> View
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Ticket Modal (unchanged) --}}
    <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title" id="createTicketModalLabel">
                        <i class="bi bi-plus-circle me-2"></i> Raise New Ticket
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createTicketForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="orderSelect" class="form-label fw-semibold">
                                Related Order <span class="text-muted">(optional)</span>
                            </label>
                            <select class="form-select" id="orderSelect" name="order_id">
                                <option value="">Loading orders...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="titleInput" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titleInput" name="title" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="messageTextarea" class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="messageTextarea" name="message" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="prioritySelect" class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="prioritySelect" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                            <div class="form-text">SLA: High 24h, Medium 48h, Low 72h.</div>
                        </div>
                        <div id="createTicketError" class="alert alert-danger d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="createTicketSubmit">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                            Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- View Ticket Modal (with messages) --}}
    <div class="modal fade" id="viewTicketModal" tabindex="-1" aria-labelledby="viewTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title" id="viewTicketModalLabel">
                        <i class="bi bi-ticket-detailed me-2"></i> Ticket Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewTicketContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading ticket details...</p>
                    </div>
                </div>
                <div class="modal-footer d-block" id="viewTicketFooter" style="display: none;">
                    <!-- Footer will be dynamically populated -->
                </div>
            </div>
        </div>
    </div>

    {{-- Reopen Reason Modal (unchanged) --}}
    <div class="modal fade" id="reopenReasonModal" tabindex="-1" aria-labelledby="reopenReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title" id="reopenReasonModalLabel">
                        <i class="bi bi-arrow-return-left me-2"></i> Reason for Reopening
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="reopenForm">
                    <div class="modal-body">
                        <p>Please provide a reason why you are reopening this ticket:</p>
                        <textarea class="form-control" id="reopenReason" name="reason" rows="3" required></textarea>
                        <div id="reopenError" class="alert alert-danger d-none mt-3"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="reopenSubmit">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                            Confirm Reopen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Toast Container (unchanged) --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="ticketToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .modal-header.bg-dark {
        background-color: #364966 !important;
    }
    /* Message thread styling */
    .message-item {
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 0;
    }
    .message-item:last-child {
        border-bottom: none;
    }
    .message-sender {
        font-weight: 600;
        color: #1e293b;
    }
    .message-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .message-content {
        margin-top: 0.5rem;
        white-space: pre-wrap;
    }
    .reply-box {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
const ORDERS_LIST_URL = "{{ route('client.orders.list') }}";

$(document).ready(function() {
    // Initialize DataTable (unchanged)
    if ($.fn.DataTable.isDataTable('#ticketsTable')) {
        $('#ticketsTable').DataTable().destroy();
    }
    $('#ticketsTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        responsive: true,
        language: {
            emptyTable: "No tickets found.",
            zeroRecords: "No matching tickets found.",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)"
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var toastEl = document.getElementById('ticketToast');
    var toast = new bootstrap.Toast(toastEl, { delay: 3000 });

    function showToast(message, type = 'success') {
        $('#toastMessage').text(message);
        toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
        toastEl.classList.add(type === 'success' ? 'bg-success' : type === 'danger' ? 'bg-danger' : 'bg-warning');
        toast.show();
    }

    // ==================== CREATE TICKET (unchanged) ====================
    $('#createTicketModal').on('show.bs.modal', function() {
        var orderSelect = $('#orderSelect');
        orderSelect.html('<option value="">Loading orders...</option>').prop('disabled', true);
        $.ajax({
            url: ORDERS_LIST_URL,
            type: 'GET',
            success: function(response) {
                if (response.length) {
                    var options = '<option value="">Select an order (optional)</option>';
                    $.each(response, function(i, order) {
                        options += `<option value="${order.id}">${order.order_no} - ${order.service}</option>`;
                    });
                    orderSelect.html(options).prop('disabled', false);
                } else {
                    orderSelect.html('<option value="">No orders available</option>').prop('disabled', true);
                }
            },
            error: function() {
                orderSelect.html('<option value="">Failed to load orders</option>').prop('disabled', true);
                showToast('Could not load orders. Please refresh.', 'danger');
            }
        });
    });

    $('#createTicketForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#createTicketSubmit');
        var spinner = btn.find('.spinner-border');
        var errorDiv = $('#createTicketError');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        errorDiv.addClass('d-none').html('');

        $.ajax({
            url: '/client/tickets',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                spinner.addClass('d-none');
                btn.prop('disabled', false);
                $('#createTicketModal').modal('hide');
                showToast(response.message || 'Ticket raised successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                spinner.addClass('d-none');
                btn.prop('disabled', false);
                var msg = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                errorDiv.removeClass('d-none').html(msg);
            }
        });
    });

    $('#createTicketModal').on('hidden.bs.modal', function() {
        $('#createTicketForm')[0].reset();
        $('#createTicketError').addClass('d-none').html('');
        $('#orderSelect').html('<option value="">Loading orders...</option>').prop('disabled', true);
    });

    // ==================== VIEW TICKET (updated) ====================
    $(document).on('click', '.btn-view-ticket', function() {
        var ticketId = $(this).data('id');
        var modal = $('#viewTicketModal');
        var contentDiv = $('#viewTicketContent');
        var footerDiv = $('#viewTicketFooter');

        modal.modal('show');
        contentDiv.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading ticket details...</p>
            </div>
        `);
        footerDiv.hide();

        $.ajax({
            url: '/client/tickets/' + ticketId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.ticket) {
                    var ticket = response.ticket;
                    var messages = response.messages || [];
                    var timeLeft = response.time_left || '';
                    var isOverdue = response.is_overdue || false;
                    var html = buildTicketDetailsHTML(ticket, messages, timeLeft, isOverdue);
                    contentDiv.html(html);

                    var footerHtml = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                    if (ticket.status === 'resolved') {
                        footerHtml = `
                            <button type="button" class="btn btn-success btn-confirm-resolved" data-id="${ticket.id}">
                                <i class="bi bi-check-lg me-1"></i> Confirm Resolved
                            </button>
                            <button type="button" class="btn btn-warning btn-reopen" data-id="${ticket.id}">
                                <i class="bi bi-arrow-return-left me-1"></i> Reopen
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        `;
                    }
                    footerDiv.html(footerHtml).show();
                } else {
                    contentDiv.html('<div class="alert alert-danger">Ticket not found.</div>');
                }
            },
            error: function() {
                contentDiv.html('<div class="alert alert-danger">Failed to load ticket details.</div>');
            }
        });
    });

    // 🔁 REVISED FUNCTION: removed description block, shows title + message thread
    function buildTicketDetailsHTML(ticket, messages, timeLeft, isOverdue) {
        var createdDate = ticket.created_at ? new Date(ticket.created_at).toLocaleString() : '—';
        var dueDate = ticket.due_at ? new Date(ticket.due_at).toLocaleString() : '—';
        var priorityBadge = getBadgeHTML(ticket.priority, 'priority');
        var statusBadge = getBadgeHTML(ticket.status, 'status');
        var orderDisplay = ticket.order?.order_id ? `Order #${ticket.order.order_id}` : 'General Support';
        var overdueAlert = isOverdue ? '<div class="alert alert-danger mt-3">⚠️ This ticket is overdue.</div>' : '';

        // Build messages HTML (oldest first)
        var messagesHtml = '';
        if (messages.length) {
            messagesHtml = '<div class="mt-4"><h6 class="fw-bold">Conversation</h6>';
            // Sort messages by created_at just in case (oldest first)
            messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            $.each(messages, function(i, msg) {
                var sender = msg.user_role === 'client' ? 'You' : (msg.user?.name || 'Support');
                var time = msg.created_at ? new Date(msg.created_at).toLocaleString() : '';
                messagesHtml += `
                    <div class="message-item">
                        <div class="d-flex justify-content-between">
                            <span class="message-sender">${sender}</span>
                            <span class="message-time">${time}</span>
                        </div>
                        <div class="message-content">${msg.message}</div>
                    </div>
                `;
            });
            messagesHtml += '</div>';
        } else {
            messagesHtml = '<p class="text-muted mt-3">No messages yet.</p>';
        }

        // Reply form (if ticket is not closed/resolved)
        var replyForm = '';
        if (ticket.status !== 'closed' && ticket.status !== 'resolved') {
            replyForm = `
                <div class="reply-box mt-4">
                    <h6 class="fw-bold">Add a reply</h6>
                    <form class="reply-form" data-ticket-id="${ticket.id}">
                        <div class="mb-2">
                            <textarea class="form-control" name="message" rows="2" required placeholder="Type your reply..."></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-sm">Send Reply</button>
                        </div>
                        <div class="reply-error text-danger small mt-1 d-none"></div>
                    </form>
                </div>
            `;
        }

        // Return complete HTML (note: no separate description block)
        return `
            <div class="ticket-details">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Ticket #:</strong> ${ticket.ticket_no}</p>
                        <p><strong>Order:</strong> ${orderDisplay}</p>
                        <p><strong>Title:</strong> ${ticket.title}</p>
                        <p><strong>Priority:</strong> ${priorityBadge}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> ${statusBadge}</p>
                        <p><strong>Created:</strong> ${createdDate}</p>
                        <p><strong>Due:</strong> ${dueDate}</p>
                        ${timeLeft ? `<p><strong>Time left:</strong> ${timeLeft}</p>` : ''}
                    </div>
                </div>
                ${messagesHtml}
                ${replyForm}
                ${overdueAlert}
            </div>
        `;
    }

    function getBadgeHTML(value, type) {
        var badgeClass = 'bg-secondary';
        var label = value;

        if (type === 'priority') {
            var priorityClasses = { low: 'bg-secondary', medium: 'bg-info', high: 'bg-warning text-dark' };
            var priorityLabels = { low: 'Low', medium: 'Medium', high: 'High' };
            badgeClass = priorityClasses[value] || 'bg-secondary';
            label = priorityLabels[value] || value;
        } else if (type === 'status') {
            var statusClasses = {
                open: 'bg-primary',
                in_progress: 'bg-info',
                resolved: 'bg-success',
                reopened: 'bg-warning text-dark',
                closed: 'bg-secondary'
            };
            var statusLabels = {
                open: 'Open',
                in_progress: 'In Progress',
                resolved: 'Resolved',
                reopened: 'Reopened',
                closed: 'Closed'
            };
            badgeClass = statusClasses[value] || 'bg-secondary';
            label = statusLabels[value] || value.replace(/_/g, ' ');
        }
        return `<span class="badge ${badgeClass} px-3 py-2 rounded-pill">${label}</span>`;
    }

    // ==================== REPLY TO TICKET (unchanged) ====================
    $(document).on('submit', '.reply-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var ticketId = form.data('ticket-id');
        var message = form.find('textarea[name="message"]').val();
        var errorDiv = form.find('.reply-error');
        var btn = form.find('button[type="submit"]');

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Sending...');
        errorDiv.addClass('d-none').html('');

        $.ajax({
            url: '/client/tickets/' + ticketId + '/reply',
            type: 'POST',
            data: { message: message },
            success: function(response) {
                btn.prop('disabled', false).html('Send Reply');
                form[0].reset();
                // Reload ticket details to show new message
                var viewBtn = $('.btn-view-ticket[data-id="' + ticketId + '"]');
                viewBtn.trigger('click');
                showToast(response.message || 'Reply sent', 'success');
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('Send Reply');
                var msg = xhr.responseJSON?.message || 'Could not send reply.';
                errorDiv.removeClass('d-none').html(msg);
            }
        });
    });

    // ==================== CONFIRM RESOLVED (unchanged) ====================
    $(document).on('click', '.btn-confirm-resolved', function() {
        var ticketId = $(this).data('id');
        var btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

        $.ajax({
            url: '/client/tickets/' + ticketId + '/confirm',
            type: 'POST',
            success: function(response) {
                $('#viewTicketModal').modal('hide');
                showToast(response.message || 'Ticket closed successfully.', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Confirm Resolved');
                var msg = 'Error: ' + (xhr.responseJSON?.message || 'Could not confirm.');
                showToast(msg, 'danger');
            }
        });
    });

    // ==================== REOPEN TICKET (unchanged) ====================
    var currentReopenId = null;

    $(document).on('click', '.btn-reopen', function() {
        currentReopenId = $(this).data('id');
        $('#reopenReasonModal').modal('show');
    });

    $('#reopenForm').on('submit', function(e) {
        e.preventDefault();
        if (!currentReopenId) return;

        var btn = $('#reopenSubmit');
        var spinner = btn.find('.spinner-border');
        var errorDiv = $('#reopenError');
        var reason = $('#reopenReason').val();

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        errorDiv.addClass('d-none').html('');

        $.ajax({
            url: '/client/tickets/' + currentReopenId + '/reopen',
            type: 'POST',
            data: { reason: reason },
            success: function(response) {
                $('#reopenReasonModal').modal('hide');
                $('#viewTicketModal').modal('hide');
                showToast(response.message || 'Ticket reopened successfully.', 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                spinner.addClass('d-none');
                btn.prop('disabled', false);
                var msg = xhr.responseJSON?.message || 'Could not reopen ticket.';
                errorDiv.removeClass('d-none').html(msg);
            }
        });
    });

    $('#reopenReasonModal').on('hidden.bs.modal', function() {
        $('#reopenForm')[0].reset();
        $('#reopenError').addClass('d-none').html('');
        $('#reopenSubmit').prop('disabled', false).find('.spinner-border').addClass('d-none');
        currentReopenId = null;
    });
});
</script>
@endpush