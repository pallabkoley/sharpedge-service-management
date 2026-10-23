@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header (unchanged) -->
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Support Tickets</h2>
                <p class="text-muted mb-0">Manage and track customer support requests</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                    <i class="bi bi-plus-circle me-1"></i> Create New Ticket
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $tickets->count() }}</div>
                        <div class="text-muted">Total Tickets</div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-ticket-detailed-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $tickets->where('status', 'open')->count() }}</div>
                        <div class="text-muted">Open Tickets</div>
                    </div>
                    <div class="card-icon" style="background-color: #ef4444;">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $tickets->where('status', 'pending')->count() }}</div>
                        <div class="text-muted">Pending Tickets</div>
                    </div>
                    <div class="card-icon" style="background-color: #f59e0b;">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $tickets->where('status', 'resolved')->count() }}</div>
                        <div class="text-muted">Resolved Tickets</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="section-card mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="pending">Pending</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="priorityFilter">
                    <option value="">All Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="client_issue">Client Issue</option>
                    <option value="internal_issue">Internal Issue</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-outline-secondary w-100" id="resetFilters">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="section-card">
        <div class="table-responsive">
            <table class="table table-hover" id="ticketsTable">
                <thead>
                    <tr>
                        <th>Ticket No.</th>
                        <th>Title</th>
                        <th>Client</th>
                        <th>Assigned To</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr data-status="{{ $ticket->status }}" 
                        data-priority="{{ $ticket->priority }}"
                        data-type="{{ $ticket->ticket_type }}"
                        data-id="{{ $ticket->id }}">
                        <td>
                            <span class="fw-bold text-primary">#{{ $ticket->ticket_no }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $ticket->title }}</div>
                            @if($ticket->description)
                            <small class="text-muted text-truncate d-block" style="max-width: 200px;">
                                {{ Str::limit($ticket->description, 50) }}
                            </small>
                            @endif
                        </td>
                        <td>
                            @if($ticket->client)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2" style="width: 32px; height: 32px; background-color: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                                        {{ strtoupper(substr($ticket->client->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="small">{{ $ticket->client->name }}</div>
                                        <small class="text-muted">{{ $ticket->client->email }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">No Client</span>
                            @endif
                        </td>
                        <td>
                            @if($ticket->assignedUser)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2" style="width: 32px; height: 32px; background-color: #8b5cf6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                                        {{ strtoupper(substr($ticket->assignedUser->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="small">{{ $ticket->assignedUser->name }}</div>
                                        <small class="text-muted">{{ $ticket->assignedUser->email }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            @if($ticket->priority == 'high')
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-chevron-double-up me-1"></i> High
                                </span>
                            @elseif($ticket->priority == 'medium')
                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-chevron-up me-1"></i> Medium
                                </span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-chevron-down me-1"></i> Low
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($ticket->status == 'open')
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-circle-fill me-1"></i> Open
                                </span>
                            @elseif($ticket->status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-clock me-1"></i> Pending
                                </span>
                            @elseif($ticket->status == 'resolved')
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle me-1"></i> Resolved
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($ticket->ticket_type == 'client_issue')
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-person me-1"></i> Client Issue
                                </span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-building me-1"></i> Internal Issue
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="text-muted small">
                                {{ $ticket->created_at->format('M d, Y') }}
                            </div>
                            <div class="text-muted smaller">
                                {{ $ticket->created_at->format('h:i A') }}
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary view-ticket-btn"
                                        data-id="{{ $ticket->id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewTicketModal">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning quick-edit"
                                        data-id="{{ $ticket->id }}"
                                        data-status="{{ $ticket->status }}"
                                        data-priority="{{ $ticket->priority }}"
                                        data-assigned-to="{{ $ticket->assigned_to }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#quickEditModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('admin.support-tickets.destroy', $ticket->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this ticket?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Ticket Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTicketModalLabel">Create New Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createTicketForm" method="POST" action="{{ route('admin.support-tickets.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ticket_type" class="form-label">Ticket Type *</label>
                                <select class="form-control" id="ticket_type" name="ticket_type" required>
                                    <option value="client_issue">Client Issue</option>
                                    <option value="internal_issue">Internal Issue</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority *</label>
                                <select class="form-control" id="priority" name="priority" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_id" class="form-label">Client (Optional)</label>
                                <select class="form-control" id="client_id" name="client_id">
                                    <option value="">Select Client</option>
                                    @php
                                        $clients = \App\Models\Client::all();
                                    @endphp
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Assign To (Optional)</label>
                                <select class="form-control" id="assigned_to" name="assigned_to">
                                    <option value="">Select Staff Member</option>
                                    @foreach($staff as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the issue..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== VIEW TICKET MODAL with messages & reply ========== -->
<div class="modal fade" id="viewTicketModal" tabindex="-1" aria-labelledby="viewTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTicketModalLabel">
                    <i class="bi bi-ticket-detailed me-2"></i> Ticket Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ticketDetailsContent">
                <!-- Loader -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading ticket details...</p>
                </div>
            </div>
            <div class="modal-footer d-block" id="viewTicketFooter" style="display: none;">
                <!-- Footer will be populated dynamically (close button etc.) -->
            </div>
        </div>
    </div>
</div>

<!-- Quick Edit Modal -->
<div class="modal fade" id="quickEditModal" tabindex="-1" aria-labelledby="quickEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickEditModalLabel">Update Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickEditForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="open">Open</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_priority" class="form-label">Priority</label>
                        <select class="form-control" id="edit_priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_assigned_to" class="form-label">Assign To</label>
                        <select class="form-control" id="edit_assigned_to" name="assigned_to">
                            <option value="">Unassigned</option>
                            @foreach($staff as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="pendingReasonGroup" style="display: none;">
                        <label for="edit_pending_reason" class="form-label">Pending Reason</label>
                        <input type="text" class="form-control" id="edit_pending_reason" name="pending_reason">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stats-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .card-value {
        font-size: 1.75rem;
        font-weight: bold;
        color: #1f2937;
    }
    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .section-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    .avatar-circle {
        width: 32px;
        height: 32px;
        background-color: #3b82f6;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
    }

    /* Ticket details styling */
    .ticket-info-row {
        padding: 0.5rem 0;
        border-bottom: 1px dashed #eee;
    }
    .ticket-label {
        font-weight: 600;
        color: #495057;
        min-width: 120px;
    }
    .ticket-value {
        color: #212529;
    }

    /* Message thread */
    .message-thread {
        max-height: 400px;
        overflow-y: auto;
        padding: 1rem;
        background: #f9fafc;
        border-radius: 0.5rem;
        margin: 1rem 0;
    }
    .message-item {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .message-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .message-sender {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .role-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        background-color: #e9ecef;
        color: #495057;
    }
    .role-badge.client {
        background-color: #cfe2ff;
        color: #084298;
    }
    .role-badge.admin {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    .role-badge.backoffice {
        background-color: #fff3cd;
        color: #856404;
    }
    .role-badge.marketing {
        background-color: #d3d3f0;
        color: #4a3f8b;
    }
    .internal-note {
        background-color: #f8f9fa;
        border-left: 3px solid #6c757d;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 0.25rem;
    }
    .internal-badge {
        background-color: #6c757d;
        color: white;
        font-size: 0.7rem;
        padding: 0.15rem 0.5rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-left: 0.5rem;
    }
    .message-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .message-content {
        white-space: pre-wrap;
        background: white;
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
    }

    /* Reply box */
    .reply-box {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    .internal-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .internal-checkbox input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== CSRF FIX for all jQuery AJAX calls on this page =====
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        // Reusable toast (optional – you may implement a toast system)
        function showToast(message, type = 'success') {
            alert(message); // fallback; replace with your preferred notification
        }

        // Filter tickets
        const statusFilter = document.getElementById('statusFilter');
        const priorityFilter = document.getElementById('priorityFilter');
        const typeFilter = document.getElementById('typeFilter');
        const resetBtn = document.getElementById('resetFilters');
        if (statusFilter && priorityFilter && typeFilter && resetBtn) {
            const ticketRows = document.querySelectorAll('#ticketsTable tbody tr');
            function filterTickets() {
                const status = statusFilter.value;
                const priority = priorityFilter.value;
                const type = typeFilter.value;
                ticketRows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    const rowPriority = row.getAttribute('data-priority');
                    const rowType = row.getAttribute('data-type');
                    let show = true;
                    if (status && rowStatus !== status) show = false;
                    if (show && priority && rowPriority !== priority) show = false;
                    if (show && type && rowType !== type) show = false;
                    row.style.display = show ? '' : 'none';
                });
            }
            statusFilter.addEventListener('change', filterTickets);
            priorityFilter.addEventListener('change', filterTickets);
            typeFilter.addEventListener('change', filterTickets);
            resetBtn.addEventListener('click', function() {
                statusFilter.value = '';
                priorityFilter.value = '';
                typeFilter.value = '';
                filterTickets();
            });
        }

        // ==================== VIEW TICKET with messages ====================
        const viewTicketModal = document.getElementById('viewTicketModal');
        const ticketDetailsContent = document.getElementById('ticketDetailsContent');
        const viewTicketFooter = document.getElementById('viewTicketFooter');
        let currentTicketId = null;

        // Open modal and load data
        document.querySelectorAll('.view-ticket-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                currentTicketId = this.getAttribute('data-id');
                ticketDetailsContent.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading ticket details...</p>
                    </div>
                `;
                viewTicketFooter.style.display = 'none';
                loadTicketDetails(currentTicketId);
            });
        });

        function loadTicketDetails(ticketId) {
            const url = `/admin/support-tickets/${ticketId}`;
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                if (data.success && data.ticket) {
                    displayTicketDetails(data.ticket, data.messages || []);
                } else {
                    ticketDetailsContent.innerHTML = '<div class="alert alert-danger">Failed to load ticket details.</div>';
                }
            })
            .catch(error => {
                console.error(error);
                ticketDetailsContent.innerHTML = '<div class="alert alert-danger">Error loading ticket details.</div>';
            });
        }

        function displayTicketDetails(ticket, messages) {
            // Format metadata
            const statusBadge = getBadge(ticket.status, 'status');
            const priorityBadge = getBadge(ticket.priority, 'priority');
            const typeBadge = getBadge(ticket.ticket_type, 'type');

            const clientHtml = ticket.client
                ? `<div class="d-flex align-items-center">
                       <div class="avatar-circle me-2" style="background-color: #3b82f6;">${ticket.client.name?.charAt(0).toUpperCase() || '?'}</div>
                       <div><div class="small">${ticket.client.name || 'N/A'}</div><small class="text-muted">${ticket.client.email || ''}</small></div>
                   </div>`
                : '<span class="badge bg-secondary">No Client</span>';

            const assignedHtml = ticket.assigned_user
                ? `<div class="d-flex align-items-center">
                       <div class="avatar-circle me-2" style="background-color: #8b5cf6;">${ticket.assigned_user.name?.charAt(0).toUpperCase() || '?'}</div>
                       <div><div class="small">${ticket.assigned_user.name || 'N/A'}</div><small class="text-muted">${ticket.assigned_user.email || ''}</small></div>
                   </div>`
                : '<span class="badge bg-warning">Unassigned</span>';

            const createdDate = new Date(ticket.created_at).toLocaleString();
            const dueDate = ticket.due_at ? new Date(ticket.due_at).toLocaleString() : '—';

            // Build message thread
            let messagesHtml = '';
            if (messages.length) {
                messagesHtml = '<div class="message-thread">';
                // Sort oldest first
                messages.sort((a,b) => new Date(a.created_at) - new Date(b.created_at));
                messages.forEach(msg => {
                    const sender = msg.user?.name || (msg.user_role === 'client' ? ticket.client?.name : 'System');
                    const role = msg.user_role || 'staff';
                    const roleClass = role === 'client' ? 'client' : (role === 'admin' ? 'admin' : (role === 'backoffice' ? 'backoffice' : 'marketing'));
                    const time = new Date(msg.created_at).toLocaleString();
                    const isInternal = msg.is_internal ? true : false;
                    const internalTag = isInternal ? '<span class="internal-badge"><i class="bi bi-key"></i> Internal</span>' : '';

                    messagesHtml += `
                        <div class="message-item ${isInternal ? 'internal-note' : ''}">
                            <div class="message-header">
                                <span class="message-sender">
                                    ${sender}
                                    <span class="role-badge ${roleClass}">${role}</span>
                                    ${internalTag}
                                </span>
                                <span class="message-time">${time}</span>
                            </div>
                            <div class="message-content">${msg.message.replace(/\n/g, '<br>')}</div>
                        </div>
                    `;
                });
                messagesHtml += '</div>';
            } else {
                messagesHtml = '<p class="text-muted text-center py-3">No messages yet.</p>';
            }

            // Reply form (only if ticket is not closed)
            let replyForm = '';
            if (ticket.status !== 'closed') {
                replyForm = `
                    <div class="reply-box">
                        <h6 class="fw-bold mb-3">Add a reply</h6>
                        <form class="reply-form" data-ticket-id="${ticket.id}">
                            <div class="mb-2">
                                <textarea class="form-control" name="message" rows="3" required placeholder="Type your message..."></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="internal-checkbox">
                                    <input type="checkbox" name="is_internal" id="internalCheck" value="1">
                                    <label for="internalCheck" class="text-muted small">Mark as internal note (not visible to client)</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Send Reply</button>
                            </div>
                            <div class="reply-error text-danger small mt-2 d-none"></div>
                        </form>
                    </div>
                `;
            }

            // Assemble full HTML
            const html = `
                <div class="ticket-details">
                    <div class="mb-3">
                        <h5>${ticket.title}</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            ${statusBadge} ${priorityBadge} ${typeBadge}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="ticket-info-row d-flex"><span class="ticket-label">Ticket No:</span><span class="ticket-value fw-bold">#${ticket.ticket_no}</span></div>
                            <div class="ticket-info-row d-flex"><span class="ticket-label">Client:</span><span class="ticket-value">${clientHtml}</span></div>
                            <div class="ticket-info-row d-flex"><span class="ticket-label">Assigned To:</span><span class="ticket-value">${assignedHtml}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="ticket-info-row d-flex"><span class="ticket-label">Created:</span><span class="ticket-value">${createdDate}</span></div>
                            <div class="ticket-info-row d-flex"><span class="ticket-label">Due:</span><span class="ticket-value">${dueDate}</span></div>
                            <div class="ticket-info-row d-flex"><span class="ticket-label">SLA Hours:</span><span class="ticket-value">${ticket.sla_hours || '—'}</span></div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-2">Conversation</h6>
                    ${messagesHtml}
                    ${replyForm}
                </div>
            `;

            ticketDetailsContent.innerHTML = html;
            viewTicketFooter.innerHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
            viewTicketFooter.style.display = 'block';
        }

        function getBadge(value, type) {
            const classes = {
                status: {
                    open: 'bg-danger',
                    assigned: 'bg-info',
                    in_progress: 'bg-primary',
                    pending: 'bg-warning',
                    resolved: 'bg-success',
                    closed: 'bg-secondary'
                },
                priority: {
                    high: 'bg-danger',
                    medium: 'bg-warning',
                    low: 'bg-success'
                },
                type: {
                    client_issue: 'bg-primary',
                    internal_issue: 'bg-info'
                }
            };
            const labels = {
                status: {
                    open: 'Open',
                    assigned: 'Assigned',
                    in_progress: 'In Progress',
                    pending: 'Pending',
                    resolved: 'Resolved',
                    closed: 'Closed'
                },
                priority: {
                    high: 'High',
                    medium: 'Medium',
                    low: 'Low'
                },
                type: {
                    client_issue: 'Client Issue',
                    internal_issue: 'Internal Issue'
                }
            };
            const cls = classes[type]?.[value] || 'bg-secondary';
            const label = labels[type]?.[value] || value;
            return `<span class="badge ${cls} bg-opacity-10 text-${cls.replace('bg-','')} px-3 py-2 rounded-pill">${label}</span>`;
        }

        // ==================== REPLY SUBMIT ====================
        $(document).on('submit', '.reply-form', function(e) {
            e.preventDefault();
            const form = $(this);
            const ticketId = form.data('ticket-id');
            const message = form.find('textarea[name="message"]').val();
            const isInternal = form.find('input[name="is_internal"]').is(':checked');
            const errorDiv = form.find('.reply-error');
            const btn = form.find('button[type="submit"]');

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Sending...');
            errorDiv.addClass('d-none').html('');

            $.ajax({
                url: '/admin/support-tickets/' + ticketId + '/reply',
                type: 'POST',
                data: { message: message, is_internal: isInternal ? 1 : 0 },
                success: function(response) {
                    btn.prop('disabled', false).html('Send Reply');
                    form[0].reset();
                    // Reload ticket to show new message
                    loadTicketDetails(ticketId);
                    showToast(response.message || 'Reply sent', 'success');
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('Send Reply');
                    const msg = xhr.responseJSON?.message || 'Could not send reply.';
                    errorDiv.removeClass('d-none').html(msg);
                }
            });
        });

        // ==================== QUICK EDIT ====================
        document.querySelectorAll('.quick-edit').forEach(button => {
            button.addEventListener('click', function() {
                const ticketId = this.getAttribute('data-id');
                const status = this.getAttribute('data-status');
                const priority = this.getAttribute('data-priority');
                const assignedTo = this.getAttribute('data-assigned-to');

                document.getElementById('edit_status').value = status;
                document.getElementById('edit_priority').value = priority;
                document.getElementById('edit_assigned_to').value = assignedTo || '';

                // Show pending reason if status is pending
                const pendingGroup = document.getElementById('pendingReasonGroup');
                if (status === 'pending') {
                    pendingGroup.style.display = 'block';
                } else {
                    pendingGroup.style.display = 'none';
                }

                document.getElementById('quickEditForm').action = `/admin/support-tickets/${ticketId}`;
            });
        });

        // Show pending reason when status changes to pending in quick edit
        document.getElementById('edit_status')?.addEventListener('change', function() {
            const pendingGroup = document.getElementById('pendingReasonGroup');
            pendingGroup.style.display = this.value === 'pending' ? 'block' : 'none';
        });

        // Quick Edit Form submit
        const quickEditForm = document.getElementById('quickEditForm');
        if (quickEditForm) {
            quickEditForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Updating...';

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PATCH'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Ticket updated successfully');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to update ticket');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update ticket');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }
    });
</script>
@endpush

@endsection