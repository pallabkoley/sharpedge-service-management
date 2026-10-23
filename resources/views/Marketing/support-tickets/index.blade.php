@extends('layouts.marketing')

@section('title', 'Support Tickets - Marketing')

@section('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    /* Statistics Cards – exactly as in client/index */
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
    
    /* Header Actions – optional search */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .header-search-box {
        position: relative;
        width: 250px;
    }
    
    .header-search-box input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-700);
    }
    
    .header-search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }
    
    /* Filters – status buttons */
    .filters {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .status-filter {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .status-btn {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        background: white;
        color: var(--gray-600);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .status-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .status-btn:hover {
        background: var(--primary-light);
        color: white;
        border-color: var(--primary-color);
    }
    
    /* Table Styles – reuse .clients-table for consistency */
    .table-container {
        padding: 0;
        overflow-x: auto;
    }
    
    .clients-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .clients-table thead {
        background-color: #3b4f6b !important;
    }
    
    .clients-table th {
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
    
    .clients-table td {
        padding: 1rem 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
        color: var(--gray-700);
        font-size: 0.875rem;
        background: white;
    }
    
    .clients-table tbody tr:hover {
        background-color: var(--gray-50);
    }
    
    /* Ticket ID badge – similar to client code box */
    .ticket-code-box {
        display: inline-block;
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary-color);
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        font-size: 0.875rem;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    /* Priority badge */
    .priority-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .priority-high { background: #fee2e2; color: #dc2626; }
    .priority-medium { background: #fef3c7; color: #d97706; }
    .priority-low { background: #d1fae5; color: #059669; }
    
    /* Status with dot (like client status) */
    .status-with-dot {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-dot.open { background: #3b82f6; }
    .status-dot.assigned { background: #0dcaf0; }
    .status-dot.in_progress { background: #f59e0b; }
    .status-dot.pending { background: #6b7280; }
    .status-dot.resolved { background: #10b981; }
    .status-dot.closed { background: #1f2937; }
    
    .status-text {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .status-open-text { color: #3b82f6; }
    .status-assigned-text { color: #0dcaf0; }
    .status-in_progress-text { color: #f59e0b; }
    .status-pending-text { color: #6b7280; }
    .status-resolved-text { color: #10b981; }
    .status-closed-text { color: #1f2937; }
    
    /* Action buttons (same as client) */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .action-btn.view {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }
    .action-btn.view:hover {
        background: linear-gradient(135deg, var(--primary-dark), #1d4ed8);
    }
    
    /* Pagination (exact copy) */
    .pagination-container {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .pagination {
        display: flex;
        gap: 0.5rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .page-item .page-link {
        border: 1px solid var(--gray-300);
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        color: var(--primary-color);
        background: white;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .page-item.active .page-link {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .page-item.disabled .page-link {
        color: var(--gray-400);
        cursor: not-allowed;
    }
    
    .page-item .page-link:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }
    
    /* Modal Styles (copied from client) */
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .form-label.required:after {
        content: " *";
        color: #dc3545;
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--gray-300);
    }
    
    /* Message bubbles */
    .message-bubble {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 18px;
        margin-bottom: 10px;
        word-wrap: break-word;
    }
    .message-bubble-client {
        background-color: #e3f2fd;
        color: #0d6efd;
    }
    .message-bubble-marketing {
        background-color: #e9ecef;
        color: #495057;
    }
    .message-meta {
        font-size: 0.7rem;
        margin-top: 4px;
        color: #6c757d;
    }
    
    @media (max-width: 768px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }
        .filters {
            flex-direction: column;
            align-items: stretch;
        }
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
        .header-search-box {
            width: 100%;
        }
        .table-container {
            overflow-x: auto;
        }
        .clients-table {
            min-width: 1000px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header">
        <h1>Support Tickets</h1>
        <p>Manage your assigned tickets</p>
    </div>

    <!-- Statistics Cards -->
    @php
        $stats = [
            'total'       => $tickets->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'pending'     => $tickets->where('status', 'pending')->count(),
            'resolved'    => $tickets->where('status', 'resolved')->count(),
        ];
    @endphp

    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Tickets</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['in_progress'] }}</h3>
                <p>In Progress</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--gray-500), var(--gray-600));">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['pending'] }}</h3>
                <p>Pending</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['resolved'] }}</h3>
                <p>Resolved</p>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="main-card">
        <div class="card-header">
            <div class="header-left">
                <h2>Ticket List</h2>
                <p>All tickets assigned to you</p>
            </div>
            
            <!-- Header Actions with Generate Ticket button -->
            <div class="header-actions">
                <!-- 🔥 NEW: Generate Ticket Button -->
                <button class="btn btn-primary" id="createTicketBtn">
                    <i class="fas fa-plus"></i> Generate Ticket
                </button>

                <form method="GET" action="{{ route('marketing.support-tickets.index') }}" class="header-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search tickets..." 
                           value="{{ request('search') }}" autocomplete="off">
                </form>
            </div>
        </div>
        
        <!-- Filters - Status Buttons -->
        <div class="filters">
            <div class="status-filter">
                <a href="{{ route('marketing.support-tickets.index', ['search' => request('search')]) }}" 
                   class="status-btn {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('marketing.support-tickets.index', ['status' => 'in_progress', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'in_progress' ? 'active' : '' }}">
                    In Progress
                </a>
                <a href="{{ route('marketing.support-tickets.index', ['status' => 'pending', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'pending' ? 'active' : '' }}">
                    Pending
                </a>
                <a href="{{ route('marketing.support-tickets.index', ['status' => 'resolved', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'resolved' ? 'active' : '' }}">
                    Resolved
                </a>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="table-container">
            <table class="clients-table">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Title</th>
                        <th>Client</th>
                        <th>Service / Order</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Time Remaining</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr data-ticket-id="{{ $ticket->id }}">
                        <!-- Ticket ID -->
                        <td>
                            <span class="ticket-code-box">
                                #{{ $ticket->id }}
                            </span>
                        </td>
                        
                        <!-- Title -->
                        <td>
                            <div class="fw-bold">{{ $ticket->title }}</div>
                            <small class="text-muted">{{ Str::limit($ticket->description, 40) }}</small>
                        </td>
                        
                        <!-- Client -->
                        <td>{{ $ticket->client->name ?? 'N/A' }}</td>
                        
                        <!-- Service/Order -->
                        <td>
                            @if($ticket->order)
                                <a href="#" class="text-decoration-none">Order #{{ $ticket->order->id }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        
                        <!-- Priority -->
                        <td>
                            @php
                                $priorityClass = $ticket->priority == 'high' ? 'priority-high' : ($ticket->priority == 'medium' ? 'priority-medium' : 'priority-low');
                            @endphp
                            <span class="priority-badge {{ $priorityClass }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        
                        <!-- Status with dot -->
                        <td>
                            <div class="status-with-dot">
                                <span class="status-dot {{ $ticket->status }}"></span>
                                <span class="status-text status-{{ $ticket->status }}-text">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </div>
                            @if($ticket->status === 'pending' && $ticket->pending_reason)
                                <small class="d-block text-muted" title="{{ $ticket->pending_reason }}">
                                    <i class="fas fa-info-circle"></i> {{ Str::limit($ticket->pending_reason, 20) }}
                                </small>
                            @endif
                        </td>
                        
                        <!-- Time Remaining -->
                        <td>
                            @if($ticket->status === 'pending')
                                <span class="text-muted">Paused</span>
                            @else
                                <span class="text-muted">{{ $ticket->remainingTime() ?? '—' }}</span>
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn view view-ticket-btn" 
                                        data-ticket-id="{{ $ticket->id }}"
                                        title="View Ticket">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No tickets found</p>
                            @if(request('search') || request('status'))
                                <a href="{{ route('marketing.support-tickets.index') }}" class="btn btn-sm btn-primary mt-2">
                                    Clear Filters
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Laravel Pagination -->
            @if($tickets->hasPages())
            <div class="pagination-container">
                <div class="text-muted small">
                    Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }}
                    of {{ $tickets->total() }} entries
                </div>
                <div>
                    {{ $tickets->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Ticket Details Modal (unchanged) -->
<div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ticket Details</h5>
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

<!-- Mark Pending Modal (unchanged) -->
<div class="modal fade" id="markPendingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Ticket as Pending</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Select this option only if you are waiting for client response, documents, or external confirmation.</p>
                <textarea id="pending-reason" class="form-control" rows="3" placeholder="Waiting for client documents / payment / external confirmation"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirm-pending-btn">Mark as Pending</button>
            </div>
        </div>
    </div>
</div>

<!-- ========== NEW: Create Ticket Modal ========== -->
<div class="modal fade" id="createTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="ticket-client" class="form-label required">Client</label>
                    <select id="ticket-client" class="form-select" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="ticket-title" class="form-label required">Title</label>
                    <input type="text" id="ticket-title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="ticket-description" class="form-label required">Description</label>
                    <textarea id="ticket-description" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="ticket-priority" class="form-label required">Priority</label>
                    <select id="ticket-priority" class="form-select" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitTicketBtn">Create Ticket</button>
            </div>
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
    // Toastr config
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Show session messages
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif
    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    @endif

    // Modal instances
    let ticketModal = new bootstrap.Modal(document.getElementById('ticketDetailsModal'));
    let pendingModal = new bootstrap.Modal(document.getElementById('markPendingModal'));

    // ========== NEW: Create Ticket Modal ==========
    let createTicketModal = new bootstrap.Modal(document.getElementById('createTicketModal'));

    // Open create ticket modal
    $('#createTicketBtn').click(function() {
        createTicketModal.show();
    });

    // Submit new ticket via AJAX
    $('#submitTicketBtn').click(function() {
        let clientId = $('#ticket-client').val();
        let title = $('#ticket-title').val().trim();
        let description = $('#ticket-description').val().trim();
        let priority = $('#ticket-priority').val();

        if (!clientId || !title || !description) {
            toastr.error('Please fill in all required fields.');
            return;
        }

        let data = {
            _token: '{{ csrf_token() }}',
            client_id: clientId,
            title: title,
            description: description,
            priority: priority
        };

        $.ajax({
            url: '{{ route("marketing.support-tickets.store") }}',
            type: 'POST',
            data: data,
            success: function(response) {
                toastr.success('Ticket generated successfully.');
                createTicketModal.hide();
                location.reload(); // Refresh to show new ticket in list
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Failed to create ticket.';
                toastr.error(msg);
            }
        });
    });

    // Reset create modal on hide
    $('#createTicketModal').on('hidden.bs.modal', function() {
        $('#ticket-client').val('');
        $('#ticket-title').val('');
        $('#ticket-description').val('');
        $('#ticket-priority').val('medium');
    });

    // ========== Existing ticket view and reply logic ==========
    let currentTicketId = null;

    // View ticket button
    $(document).on('click', '.view-ticket-btn', function() {
        currentTicketId = $(this).data('ticket-id');
        loadTicketDetails(currentTicketId);
    });

    function loadTicketDetails(ticketId) {
        $('#ticket-loading').show();
        $('#ticket-content').hide().html('');
        ticketModal.show();

        $.ajax({
            url: "{{ route('marketing.support-tickets.show', ':id') }}".replace(':id', ticketId),
            type: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                $('#ticket-loading').hide();
                renderTicketContent(response);
            },
            error: function(xhr) {
                $('#ticket-loading').hide();
                let errorMsg = xhr.responseJSON?.message || 'Error loading ticket details.';
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
                                    <p><strong>Status:</strong> <span class="badge bg-${ticket.status}">${ticket.status.replace('_', ' ')}</span></p>
                                </div>
                            </div>
                            <p><strong>Time Remaining:</strong> ${ticket.status === 'pending' ? 'Paused' : (data.time_left || '—')}</p>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header bg-light">Messages</div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            ${renderMessages(messages)}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-light">Reply</div>
                        <div class="card-body">
                            <textarea id="reply-message" class="form-control mb-2" rows="3" placeholder="Write your reply to the client..."></textarea>
                            <button type="button" class="btn btn-primary w-100" id="send-reply-btn">Send Reply</button>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        ${ticket.status !== 'resolved' && ticket.status !== 'closed' && ticket.status !== 'pending' ? `
                            <button type="button" class="btn btn-warning" id="mark-pending-btn">Mark as Pending</button>
                        ` : ''}
                        ${ticket.status === 'pending' ? `
                            <div class="alert alert-secondary">Ticket is pending: ${ticket.pending_reason || 'No reason provided'}</div>
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
            if (msg.user_role === 'marketing') bubbleClass = 'message-bubble-marketing';
            let sender = msg.user_role === 'client' ? msg.user?.name || 'Client' : 'You';
            return `
                <div class="message-bubble ${bubbleClass}">
                    <div class="d-flex justify-content-between">
                        <strong>${sender}</strong>
                        <small class="message-meta">${msg.created_at ? new Date(msg.created_at).toLocaleString() : ''}</small>
                    </div>
                    <div>${msg.message}</div>
                </div>
            `;
        }).join('');
    }

    // Send reply
    $(document).on('click', '#send-reply-btn', function() {
        const message = $('#reply-message').val().trim();
        if (!message) return toastr.error('Please enter a message.');

        $.ajax({
            url: "{{ route('marketing.support-tickets.reply', ':id') }}".replace(':id', currentTicketId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                message: message,
            },
            success: function() {
                $('#reply-message').val('');
                loadTicketDetails(currentTicketId);
                toastr.success('Reply sent successfully');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error sending reply');
            }
        });
    });

    // Mark as Pending
    $(document).on('click', '#mark-pending-btn', function() {
        pendingModal.show();
    });

    $('#confirm-pending-btn').click(function() {
        const reason = $('#pending-reason').val().trim();
        if (!reason) return toastr.error('Please provide a reason.');

        $.ajax({
            url: "{{ route('marketing.support-tickets.pending', ':id') }}".replace(':id', currentTicketId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                pending_reason: reason
            },
            success: function() {
                pendingModal.hide();
                $('#pending-reason').val('');
                ticketModal.hide();
                location.reload();
                toastr.success('Ticket marked as pending');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to mark pending');
            }
        });
    });

    // Reset pending modal on hide
    $('#markPendingModal').on('hidden.bs.modal', function() {
        $('#pending-reason').val('');
    });

    // Reset ticket modal on hide
    $('#ticketDetailsModal').on('hidden.bs.modal', function() {
        $('#ticket-loading').show();
        $('#ticket-content').hide().html('');
    });
});
</script>
@endsection