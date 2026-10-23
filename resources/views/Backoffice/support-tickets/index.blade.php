@extends('layouts.Backoffice')

@section('page-title', 'My Assigned Support Tickets')

@section('content')
    <!-- Stats Cards (computed from $tickets) -->
    @php
        $stats = [
            'total'       => $tickets->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'pending'     => $tickets->where('status', 'pending')->count(),
            'resolved'    => $tickets->where('status', 'resolved')->count(),
        ];
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TOTAL TICKETS</h6>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-ticket"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats['total'] }}</h3>
                <div class="card-change up">
                    <i class="bi bi-ticket me-1"></i>
                    <span>All assigned tickets</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">IN PROGRESS</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats['in_progress'] }}</h3>
                <div class="card-change up">
                    <i class="bi bi-hourglass-split me-1"></i>
                    <span>Being worked on</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING</h6>
                    <div class="card-icon bg-secondary">
                        <i class="bi bi-pause-circle"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats['pending'] }}</h3>
                <div class="card-change down">
                    <i class="bi bi-pause-circle me-1"></i>
                    <span>Waiting on client / external</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">RESOLVED</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $stats['resolved'] }}</h3>
                <div class="card-change up">
                    <i class="bi bi-check-circle me-1"></i>
                    <span>Awaiting client confirmation</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="content-section">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">My Assigned Tickets</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                    <i class="bi bi-plus-lg me-1"></i> New Ticket
                </button>
            </div>

            <!-- Filter Tabs (based on status) -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('backoffice.support-tickets.index') }}" 
                   class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                    All
                </a>
                <a href="{{ route('backoffice.support-tickets.index', ['status' => 'in_progress']) }}" 
                   class="btn btn-sm {{ request('status') == 'in_progress' ? 'btn-primary' : 'btn-outline-primary' }}">
                    In Progress
                </a>
                <a href="{{ route('backoffice.support-tickets.index', ['status' => 'pending']) }}" 
                   class="btn btn-sm {{ request('status') == 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Pending
                </a>
                <a href="{{ route('backoffice.support-tickets.index', ['status' => 'resolved']) }}" 
                   class="btn btn-sm {{ request('status') == 'resolved' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Resolved
                </a>
            </div>

            @if($tickets->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-ticket text-muted" style="font-size: 48px;"></i>
                    <h4 class="mt-3">No Tickets Found</h4>
                    <p class="text-muted">You don't have any assigned tickets in this view.</p>
                    @if(request('status'))
                        <a href="{{ route('backoffice.support-tickets.index') }}" class="btn btn-sm btn-primary mt-2">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="ticketsTable">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Title</th>
                                <th>Client</th>
                                <th>Service / Order</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Time Remaining / SLA</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td class="fw-bold">#{{ $ticket->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $ticket->title }}</div>
                                        <small class="text-muted">{{ Str::limit($ticket->description, 50) }}</small>
                                    </td>
                                    <td>
                                        {{ $ticket->client->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if($ticket->order)
                                            <a href="#" class="text-decoration-none">Order #{{ $ticket->order->id }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $priorityClass = [
                                                'low'    => 'bg-success',
                                                'medium' => 'bg-warning',
                                                'high'   => 'bg-danger',
                                            ][$ticket->priority] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $priorityClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $priorityClass) }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $ticket->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                        @if($ticket->status === 'pending' && $ticket->pending_reason)
                                            <small class="d-block text-muted" title="{{ $ticket->pending_reason }}">
                                                <i class="bi bi-info-circle"></i> {{ Str::limit($ticket->pending_reason, 20) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->status === 'pending')
                                            <span class="text-muted">Paused</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="task-actions">
                                            <button type="button" 
                                                    class="btn-task-action view-ticket-btn" 
                                                    data-ticket-id="{{ $ticket->id }}"
                                                    title="View Ticket">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Optional Pagination (if using pagination) -->
                @if($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator && $tickets->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4" id="manualPagination">
                    <div>
                        <small class="text-muted">
                            Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} entries
                        </small>
                    </div>
                    <div>
                        {{ $tickets->withQueryString()->links() }}
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@section('modals')
    <!-- Create Ticket Modal -->
    <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createTicketForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTicketModalLabel">Create New Support Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            @if(isset($clients) && $clients->isNotEmpty())
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                                    @endforeach
                                </select>
                            @else
                                <p class="text-muted small">No clients available. Please ensure the controller passes `$clients`.</p>
                                <input type="text" class="form-control" id="client_id" name="client_id" placeholder="Enter client ID" required>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitTicketBtn">Create Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ticket Details Modal -->
    <div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-labelledby="ticketDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketDetailsModalLabel">Ticket Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-5" id="ticket-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading ticket details...</p>
                    </div>
                    <div id="ticket-content" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Pending Modal (reason) -->
    <div class="modal fade" id="markPendingModal" tabindex="-1" aria-labelledby="markPendingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="markPendingModalLabel">Mark Ticket as Pending</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Select this option only if work is blocked due to client response, documents, or external dependency. SLA will be paused.</p>
                    <textarea id="pending-reason" class="form-control" rows="3" placeholder="Waiting for client documents / payment / external confirmation"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirm-pending-btn">Mark as Pending</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Resolve Modal -->
    <div class="modal fade" id="confirmResolveModal" tabindex="-1" aria-labelledby="confirmResolveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmResolveModalLabel">Mark as Resolved</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Have you fully resolved the issue?</p>
                    <p class="text-muted small">The client will be notified and asked to confirm before the ticket is closed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirm-resolve-btn">Yes, mark as resolved</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    /* Reuse status badge styling from tasks/leave requests */
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-open { background: #e3f2fd; color: #0d6efd; }
    .status-assigned { background: #e7f5ff; color: #0dcaf0; }
    .status-in_progress { background: #fff3cd; color: #ffc107; }
    .status-pending { background: #e2e3e5; color: #6c757d; }
    .status-resolved { background: #d1e7dd; color: #198754; }
    .status-closed { background: #d3d3d4; color: #1e1e2f; }

    .btn-task-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f8f9fa;
        color: #6c757d;
        transition: all 0.2s;
    }
    .btn-task-action:hover {
        background: #e9ecef;
        color: #0d6efd;
    }
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
    }

    /* Message bubbles */
    .message-bubble {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 18px;
        margin-bottom: 10px;
        word-wrap: break-word;
    }
    .message-bubble-internal {
        background-color: #fff3cd;
        color: #856404;
    }
    .message-bubble-client {
        background-color: #e3f2fd;
        color: #0d6efd;
    }
    .message-bubble-backoffice {
        background-color: #e9ecef;
        color: #495057;
    }
    .message-meta {
        font-size: 0.7rem;
        margin-top: 4px;
        color: #6c757d;
    }
    .internal-note-label {
        font-size: 0.8rem;
        color: #dc3545;
    }
    .status-hint {
        background: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 10px;
        font-size: 0.85rem;
    }
</style>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endsection

@section('scripts')
<!-- jQuery (already in layout, but included for safety) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTables for tickets table
    if ($('#ticketsTable').length && $.fn.DataTable && !$.fn.DataTable.isDataTable('#ticketsTable')) {
        $('#ticketsTable').DataTable({
            pageLength: 10,
            lengthChange: false,
            ordering: true,
            searching: true,
            info: true,
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [7] } // Actions column (8th column)
            ],
            language: {
                search: "Search tickets:",
                info: "Showing _START_ to _END_ of _TOTAL_ tickets",
                infoEmpty: "No tickets to show",
                zeroRecords: "No matching tickets found"
            }
        });
        
        // Hide manual pagination if it exists (when using DataTables)
        $('#manualPagination').hide();
    }

    // Modal instances
    let ticketModal = new bootstrap.Modal(document.getElementById('ticketDetailsModal'));
    let pendingModal = new bootstrap.Modal(document.getElementById('markPendingModal'));
    let resolveModal = new bootstrap.Modal(document.getElementById('confirmResolveModal'));

    let currentTicketId = null;

    // View ticket button click
    $(document).on('click', '.view-ticket-btn', function() {
        currentTicketId = $(this).data('ticket-id');
        loadTicketDetails(currentTicketId);
    });

    // ---------- Create Ticket ----------
    $('#createTicketForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#submitTicketBtn');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Creating...');

        $.ajax({
            url: "{{ route('backoffice.support-tickets.store') }}",
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Close modal and refresh page
                    bootstrap.Modal.getInstance(document.getElementById('createTicketModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error creating ticket.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                alert(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Create Ticket');
            }
        });
    });

    // Reset form when modal is hidden
    $('#createTicketModal').on('hidden.bs.modal', function() {
        $('#createTicketForm')[0].reset();
    });

    // ---------- Load Ticket Details ----------
    function loadTicketDetails(ticketId) {
        $('#ticket-loading').show();
        $('#ticket-content').hide().html('');
        ticketModal.show();

        $.ajax({
            url: "{{ route('backoffice.support-tickets.show', ':id') }}".replace(':id', ticketId),
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#ticket-loading').hide();
                renderTicketContent(response);
            },
            error: function(xhr) {
                $('#ticket-loading').hide();
                let errorMsg = 'Error loading ticket details.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#ticket-content').html(`
                    <div class="alert alert-danger">${errorMsg}</div>
                `).show();
            }
        });
    }

    function renderTicketContent(data) {
        const ticket = data.ticket || data;
        const messages = data.messages || [];

        let html = `
            <div class="row">
                <!-- Left column: Ticket info and messages -->
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong>Ticket #${ticket.id}</strong> - ${ticket.title}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Client:</strong> ${ticket.client?.name || 'N/A'}</p>
                                    <p><strong>Service/Order:</strong> ${ticket.order ? 'Order #' + ticket.order.id : '—'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Priority:</strong> <span class="badge bg-${ticket.priority === 'high' ? 'danger' : (ticket.priority === 'medium' ? 'warning' : 'success')}">${ticket.priority}</span></p>
                                    <p><strong>Status:</strong> <span class="status-badge status-${ticket.status}">${ticket.status.replace('_', ' ')}</span></p>
                                </div>
                            </div>
                            <p><strong>Time Remaining / SLA:</strong> ${ticket.status === 'pending' ? 'Paused' : (data.time_left || '—')}</p>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">Messages</div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;" id="messages-container">
                            ${renderMessages(messages)}
                        </div>
                    </div>
                </div>

                <!-- Right column: Actions and reply -->
                <div class="col-md-4">
                    <!-- Status Guidance Hint -->
                    <div class="status-hint mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Status guidance:</strong><br>
                        • Pending pauses SLA<br>
                        • Resolved requires client confirmation<br>
                        • Closed can only be done after client approval
                    </div>

                    <!-- Reply Box -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">Reply</div>
                        <div class="card-body">
                            <textarea id="reply-message" class="form-control mb-2" rows="3" placeholder="Write your reply..."></textarea>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="internal-note">
                                <label class="form-check-label" for="internal-note">
                                    Internal note – visible only to admin and staff
                                </label>
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="send-reply-btn">Send Reply</button>
                        </div>
                    </div>

                    <!-- Action Buttons (only allowed based on status) -->
                    <div class="d-grid gap-2">
                        ${ticket.status !== 'resolved' && ticket.status !== 'closed' ? `
                            <button type="button" class="btn btn-warning" id="mark-pending-btn">Mark as Pending</button>
                        ` : ''}
                        ${ticket.status === 'in_progress' || ticket.status === 'pending' ? `
                            <button type="button" class="btn btn-success" id="mark-resolved-btn">Mark as Resolved</button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;

        $('#ticket-content').html(html).show();
    }

    function renderMessages(messages) {
        if (!messages.length) return '<p class="text-muted text-center">No messages yet.</p>';

        return messages.map(msg => {
            let bubbleClass = 'message-bubble-client';
            if (msg.is_internal) bubbleClass = 'message-bubble-internal';
            else if (msg.user_role === 'backoffice') bubbleClass = 'message-bubble-backoffice';

            let senderLabel = msg.user_role === 'client' ? msg.user?.name || 'Client' : (msg.is_internal ? 'Staff (internal)' : 'Staff');
            return `
                <div class="message-bubble ${bubbleClass}">
                    <div class="d-flex justify-content-between">
                        <strong>${senderLabel}</strong>
                        <small class="message-meta">${msg.created_at ? new Date(msg.created_at).toLocaleString() : ''}</small>
                    </div>
                    <div>${msg.message}</div>
                    ${msg.is_internal ? '<div class="internal-note-label mt-1"><i class="bi bi-incognito"></i> Internal note</div>' : ''}
                </div>
            `;
        }).join('');
    }

    // Send reply
    $(document).on('click', '#send-reply-btn', function() {
        const message = $('#reply-message').val().trim();
        if (!message) return alert('Please enter a message.');

        $.ajax({
            url: "{{ route('backoffice.support-tickets.reply', ':id') }}".replace(':id', currentTicketId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                message: message,
                is_internal: $('#internal-note').is(':checked') ? 1 : 0
            },
            success: function(response) {
                $('#reply-message').val('');
                $('#internal-note').prop('checked', false);
                loadTicketDetails(currentTicketId);
            },
            error: function(xhr) {
                alert('Error sending reply: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Mark as Pending - open reason modal
    $(document).on('click', '#mark-pending-btn', function() {
        pendingModal.show();
    });

    $('#confirm-pending-btn').click(function() {
        const reason = $('#pending-reason').val().trim();
        if (!reason) {
            alert('Please provide a reason.');
            return;
        }

        $.ajax({
            url: "{{ route('backoffice.support-tickets.pending', ':id') }}".replace(':id', currentTicketId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                pending_reason: reason
            },
            success: function(response) {
                pendingModal.hide();
                $('#pending-reason').val('');
                ticketModal.hide();
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to mark pending'));
            }
        });
    });

    // Mark as Resolved - confirmation
    $(document).on('click', '#mark-resolved-btn', function() {
        resolveModal.show();
    });

    $('#confirm-resolve-btn').click(function() {
        $.ajax({
            url: "{{ route('backoffice.support-tickets.resolved', ':id') }}".replace(':id', currentTicketId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                resolveModal.hide();
                ticketModal.hide();
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to mark resolved'));
            }
        });
    });

    // Reset pending modal when hidden
    $('#markPendingModal').on('hidden.bs.modal', function() {
        $('#pending-reason').val('');
    });

    // Clean up when ticket modal closes
    $('#ticketDetailsModal').on('hidden.bs.modal', function() {
        $('#ticket-loading').show();
        $('#ticket-content').hide().html('');
    });
});
</script>
@endsection