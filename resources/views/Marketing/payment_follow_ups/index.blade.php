@extends('layouts.marketing')

@section('title', 'Payment Follow-ups - SharpEdge Marketing')

@section('styles')
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- DataTables CSS (optional, for sorting) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        /* Reuse styles from client management page for consistency */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            flex-shrink: 0;
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

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
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

        .filter-btn {
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

        .filter-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .filter-btn:hover {
            background: var(--primary-light);
            color: white;
            border-color: var(--primary-color);
        }

        .table-container {
            padding: 0;
            overflow-x: auto;
        }

        .payments-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .payments-table thead {
            background-color: #3b4f6b !important;
        }

        .payments-table th {
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

        .payments-table td {
            padding: 1rem 1rem;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
            color: var(--gray-700);
            font-size: 0.875rem;
            background: white;
        }

        .payments-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .payments-table tbody tr:hover {
            background-color: var(--gray-50);
        }

        /* Client column */
        .client-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .client-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .client-details {
            display: flex;
            flex-direction: column;
        }

        .client-name {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.875rem;
            margin-bottom: 0.125rem;
        }

        .client-contact {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.due {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-badge.upcoming {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-badge.never {
            background: #f3f4f6;
            color: #4b5563;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
            border: none;
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .action-btn.follow-up {
            background: linear-gradient(135deg, #0ea5e9, #0369a1);
        }

        .action-btn.follow-up:hover {
            background: linear-gradient(135deg, #0369a1, #0c4a6e);
        }

        .action-btn.history {
            background: linear-gradient(135deg, #8b5cf6, #5b21b6);
        }

        .action-btn.history:hover {
            background: linear-gradient(135deg, #5b21b6, #4c1d95);
        }

        /* Modal styles */
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--gray-300);
        }

        .follow-up-timeline {
            position: relative;
            padding-left: 1rem;
        }

        .follow-up-item {
            border-left: 2px solid var(--gray-300);
            padding-left: 1.5rem;
            padding-bottom: 1.5rem;
            position: relative;
        }

        .follow-up-item:last-child {
            border-left: 2px solid transparent;
            padding-bottom: 0;
        }

        .follow-up-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 2px solid white;
        }

        .follow-up-meta {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: 1fr 1fr;
            }
            .header-actions {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }
            .header-search-box {
                width: 100%;
            }
            .filters {
                flex-direction: column;
                align-items: stretch;
            }
            .table-container {
                overflow-x: auto;
            }
            .payments-table {
                min-width: 900px;
            }
        }

        @media (max-width: 480px) {
            .stats-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header">
        <h1>Payment Follow-ups</h1>
        <p>Manage pending Pay Later payments that need follow-up</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalPending }}</h3>
                <p>Total Pending</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalPendingAmount, 2) }}</h3>
                <p>Pending Amount (₹)</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $todayFollowUps }}</h3>
                <p>Today's Follow-ups</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #b91c1c);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $overdueFollowUps }}</h3>
                <p>Overdue Follow-ups</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #6b7280, #374151);">
                <i class="fas fa-ban"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $neverFollowedUp }}</h3>
                <p>Never Followed Up</p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="main-card">
        <div class="card-header">
            <div class="header-left">
                <h2>Pending Pay Later Payments</h2>
                <p>All approved payments where the actual amount is not yet received</p>
            </div>
            <div class="header-actions">
                <form method="GET" action="{{ route('marketing.payment-follow-ups.index') }}" class="header-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search client..." value="{{ request('search') }}" autocomplete="off">
                </form>
                <!-- (optional) a "Refresh" button could be here -->
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="status-filter">
                <a href="{{ route('marketing.payment-follow-ups.index', ['search' => request('search')]) }}" 
                   class="filter-btn {{ !request('follow_up') ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('marketing.payment-follow-ups.index', ['follow_up' => 'never', 'search' => request('search')]) }}" 
                   class="filter-btn {{ request('follow_up') == 'never' ? 'active' : '' }}">
                    Never
                </a>
                <a href="{{ route('marketing.payment-follow-ups.index', ['follow_up' => 'due', 'search' => request('search')]) }}" 
                   class="filter-btn {{ request('follow_up') == 'due' ? 'active' : '' }}">
                    Due / Overdue
                </a>
                <a href="{{ route('marketing.payment-follow-ups.index', ['follow_up' => 'upcoming', 'search' => request('search')]) }}" 
                   class="filter-btn {{ request('follow_up') == 'upcoming' ? 'active' : '' }}">
                    Upcoming
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table class="payments-table" id="paymentsTable">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Order ID</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Last Follow-up</th>
                        <th>Next Follow-up</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php
                            $lastFollowUp = $payment->followUps->last();
                            $nextFollowUpDate = $lastFollowUp ? $lastFollowUp->next_follow_up_date : null;
                            $lastFollowUpDate = $lastFollowUp ? $lastFollowUp->follow_up_date : null;

                            // Determine status
                            $statusClass = 'never';
                            $statusLabel = 'Never';
                            if ($nextFollowUpDate) {
                                if (\Carbon\Carbon::parse($nextFollowUpDate)->isToday() || \Carbon\Carbon::parse($nextFollowUpDate)->isPast()) {
                                    $statusClass = 'due';
                                    $statusLabel = 'Due / Overdue';
                                } else {
                                    $statusClass = 'upcoming';
                                    $statusLabel = 'Upcoming';
                                }
                            }
                        @endphp
                        <tr>
                            <!-- Client -->
                            <td>
                                <div class="client-cell">
                                    <div class="client-avatar">
                                        {{ strtoupper(substr($payment->client->name ?? 'N/A', 0, 1)) }}
                                    </div>
                                    <div class="client-details">
                                        <span class="client-name">{{ $payment->client->name ?? 'N/A' }}</span>
                                        <span class="client-contact">{{ $payment->client->phone ?? '' }} | {{ $payment->client->email ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <!-- Order ID -->
                            <td>
                                <a href="{{ route('marketing.orders.show', $payment->order_id) }}" target="_blank">
                                    #{{ $payment->order_id }}
                                </a>
                            </td>
                            <!-- Amount -->
                            <td>₹{{ number_format($payment->amount, 2) }}</td>
                            <!-- Payment Date -->
                            <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '-' }}</td>
                            <!-- Last Follow-up -->
                            <td>{{ $lastFollowUpDate ? \Carbon\Carbon::parse($lastFollowUpDate)->format('d M Y') : '-' }}</td>
                            <!-- Next Follow-up -->
                            <td>{{ $nextFollowUpDate ? \Carbon\Carbon::parse($nextFollowUpDate)->format('d M Y') : '-' }}</td>
                            <!-- Status -->
                            <td>
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <!-- Actions -->
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn follow-up btn-follow-up" 
                                            data-payment-id="{{ $payment->id }}"
                                            data-client-name="{{ $payment->client->name ?? 'N/A' }}"
                                            data-order-id="{{ $payment->order_id }}"
                                            data-amount="{{ number_format($payment->amount, 2) }}"
                                            title="Add Follow-up">
                                        <i class="fas fa-plus"></i> Follow Up
                                    </button>
                                    <button class="action-btn history btn-history" 
                                            data-payment-id="{{ $payment->id }}"
                                            title="View History">
                                        <i class="fas fa-history"></i> History
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-check-circle fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No pending Pay Later payments found.</p>
                                @if(request('search') || request('follow_up'))
                                    <a href="{{ route('marketing.payment-follow-ups.index') }}" class="btn btn-sm btn-primary mt-2">
                                        Clear Filters
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- Add Follow-up Modal -->
<!-- ============================================================ -->
<div class="modal fade" id="followUpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Follow-up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="followUpForm" method="POST" action="{{ route('marketing.payment-follow-ups.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="payment_id" id="followUpPaymentId">

                    <!-- Client / Order Summary -->
                    <div class="alert alert-light border mb-3">
                        <strong>Client:</strong> <span id="followUpClientName">-</span><br>
                        <strong>Order ID:</strong> #<span id="followUpOrderId">-</span><br>
                        <strong>Amount:</strong> ₹<span id="followUpAmount">-</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Follow-up Date</label>
                        <input type="date" class="form-control" name="follow_up_date" id="followUpDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Next Follow-up Date</label>
                        <input type="date" class="form-control" name="next_follow_up_date" id="nextFollowUpDate">
                    </div>
                  <div class="mb-3">
    <label class="form-label required">Note</label>
    <textarea class="form-control" name="note" id="followUpNote" rows="3" placeholder="Add any remarks..." required></textarea>
</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Follow-up</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- History Modal (loaded via AJAX) -->
<!-- ============================================================ -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Follow-up History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="historyModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading history...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
<!-- DataTables (for sorting only) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Toastr config
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "4000"
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

    // DataTables initialization (sort only, no paging/searching)
    $('#paymentsTable').DataTable({
        order: [[3, 'desc']], // sort by payment date descending
        paging: false,
        searching: false,
        info: false,
        columnDefs: [
            { targets: 2, type: 'num' } // amount as numeric
        ]
    });

    // ------------------------------------------------------------
    // Follow-up Modal
    // ------------------------------------------------------------
    $(document).on('click', '.btn-follow-up', function() {
        const paymentId = $(this).data('payment-id');
        const clientName = $(this).data('client-name');
        const orderId = $(this).data('order-id');
        const amount = $(this).data('amount');

        $('#followUpPaymentId').val(paymentId);
        $('#followUpClientName').text(clientName);
        $('#followUpOrderId').text(orderId);
        $('#followUpAmount').text(amount);

        // Set default follow-up date to today
        const today = new Date().toISOString().split('T')[0];
        $('#followUpDate').val(today);

        // Clear other fields
        $('#nextFollowUpDate').val('');
        $('#followUpNote').val('');

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('followUpModal'));
        modal.show();
    });

    // Form validation for follow-up
    $('#followUpForm').on('submit', function(e) {
        const followUpDate = $('#followUpDate').val();
        if (!followUpDate) {
            e.preventDefault();
            toastr.error('Please select a follow-up date.');
            return false;
        }
        // Optionally, we could add client-side validation for next date >= follow_up_date
    });

    // ------------------------------------------------------------
    // History Modal - AJAX load
    // ------------------------------------------------------------
    $(document).on('click', '.btn-history', function() {
        const paymentId = $(this).data('payment-id');
        const modal = new bootstrap.Modal(document.getElementById('historyModal'));
        const body = $('#historyModalBody');

        // Show loading spinner
        body.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading history...</p>
            </div>
        `);

        modal.show();

        // AJAX request
        $.ajax({
            url: '{{ url("marketing/payment-follow-ups") }}/' + paymentId + '/follow-ups',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const followUps = response.follow_ups;
                    let html = '';
                    if (followUps.length === 0) {
                        html = '<div class="alert alert-info">No follow-up records found for this payment.</div>';
                    } else {
                        html = '<div class="follow-up-timeline">';
                        followUps.forEach(function(item) {
                            html += `
                                <div class="follow-up-item">
                                    <div><strong>${item.follow_up_date}</strong></div>
                                    <div>Next: ${item.next_follow_up_date || 'N/A'}</div>
                                    <div>${item.note ? '<p class="mb-1">' + item.note + '</p>' : ''}</div>
                                    <div class="follow-up-meta">
                                        <i class="fas fa-user"></i> ${item.created_by || 'System'} &bull;
                                        <i class="fas fa-calendar-alt"></i> ${item.created_at || ''}
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    body.html(html);
                } else {
                    body.html('<div class="alert alert-danger">Could not load history. Please try again.</div>');
                }
            },
            error: function() {
                body.html('<div class="alert alert-danger">An error occurred while loading history.</div>');
            }
        });
    });

    // Auto-submit search on Enter key
    $('input[name="search"]').on('keypress', function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });

    // Auto-hide toastr after a while
    setTimeout(function() {
        $('.toast').fadeOut('slow');
    }, 5000);
});
</script>
@endsection