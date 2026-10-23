@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Payment Management</h1>
            <p class="text-muted">View and manage all payment records</p>
        </div>
    </div>

    <!-- Stats Cards (Staff Management Style) -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ number_format($totalPayments) }}</div>
                        <div class="card-label">Total Payments</div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-receipt text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">₹{{ number_format($totalAmount, 2) }}</div>
                        <div class="card-label">Total Amount</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;">
                        <i class="bi bi-currency-rupee text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">₹{{ number_format($todayAmount, 2) }}</div>
                        <div class="card-label">Today's Collection</div>
                    </div>
                    <div class="card-icon" style="background-color: #0ea5e9;">
                        <i class="bi bi-calendar-day text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">₹{{ number_format($monthlyAmount, 2) }}</div>
                        <div class="card-label">Monthly Collection</div>
                    </div>
                    <div class="card-icon" style="background-color: #f59e0b;">
                        <i class="bi bi-calendar-range text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pay Later & Upcoming Follow-ups Stats (Second Row) -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ number_format($payLaterPendingCount ?? 0) }}</div>
                        <div class="card-label">Pay Later Pending</div>
                        <small class="text-muted">₹{{ number_format($payLaterPendingAmount ?? 0, 2) }}</small>
                    </div>
                    <div class="card-icon" style="background-color: #1e293b;">
                        <i class="bi bi-clock text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ number_format($upcomingFollowUpsCount ?? 0) }}</div>
                        <div class="card-label">Upcoming Follow-ups</div>
                        <small class="text-muted">Next 7 days</small>
                    </div>
                    <div class="card-icon" style="background-color: #8b5cf6;">
                        <i class="bi bi-bell text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section (Staff Management Style) -->
    <div class="section-card mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0 fw-bold">Payment Records</h6>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.payments.index', array_merge(request()->except('payment_tracking_status'), ['payment_tracking_status' => null])) }}"
                       class="btn btn-outline-primary {{ !request('payment_tracking_status') ? 'active' : '' }}">
                        All
                    </a>
                    <a href="{{ route('admin.payments.index', array_merge(request()->except('payment_tracking_status'), ['payment_tracking_status' => 'pending'])) }}"
                       class="btn btn-outline-primary {{ request('payment_tracking_status') === 'pending' ? 'active' : '' }}">
                        Pay Later Pending
                    </a>
                    <a href="{{ route('admin.payments.index', array_merge(request()->except('payment_tracking_status'), ['payment_tracking_status' => 'paid'])) }}"
                       class="btn btn-outline-primary {{ request('payment_tracking_status') === 'paid' ? 'active' : '' }}">
                        Pay Later Paid
                    </a>
                    <a href="{{ route('admin.payments.index', array_merge(request()->except('payment_tracking_status'), ['payment_tracking_status' => 'upcoming'])) }}"
                       class="btn btn-outline-primary {{ request('payment_tracking_status') === 'upcoming' ? 'active' : '' }}">
                        Upcoming Follow-ups
                        @if($upcomingFollowUpsCount > 0)
                            <span class="badge bg-danger ms-1">{{ $upcomingFollowUpsCount }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card (search, client, date) -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i> Filter Payments
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Client name, remarks...">
                </div>
                <div class="col-md-3">
                    <label for="client_id" class="form-label">Client</label>
                    <select class="form-select" id="client_id" name="client_id">
                        <option value="">All Clients</option>
                        @foreach($clients as $id => $name)
                            <option value="{{ $id }}" {{ request('client_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table with DataTables -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i> Payment Records
        </div>
        <div class="card-body" style="padding-bottom: 1rem !important; min-height: auto !important;">
            @if($payments->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable" id="paymentsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Method</th>
                                <th>Tracking Status</th>
                                <th>Follow-up Info</th>   <!-- ✅ NEW COLUMN -->
                                <th>Received By</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                            @php
                                $isPayLater = $payment->isPayLater();
                                $isReceived = $payment->isPaymentReceived();

                                // Get upcoming follow-ups (next 7 days)
                                $upcomingFollowUps = $isPayLater && !$isReceived 
                                    ? $payment->followUps->filter(function($fu) {
                                        return $fu->next_follow_up_date && 
                                               \Carbon\Carbon::parse($fu->next_follow_up_date)->between(
                                                   \Carbon\Carbon::today(),
                                                   \Carbon\Carbon::today()->addDays(7)
                                               );
                                    }) 
                                    : collect();
                                $upcomingCount = $upcomingFollowUps->count();
                                $nextFollowUp = $upcomingFollowUps->sortBy('next_follow_up_date')->first();

                                // Get latest follow-up (the one that set the reminder)
                                $latestFollowUp = $isPayLater && !$isReceived 
                                    ? $payment->followUps->whereNotNull('next_follow_up_date')->sortByDesc('created_at')->first()
                                    : null;
                            @endphp
                            <tr data-payment-id="{{ $payment->id }}">
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->client->name ?? 'N/A' }}</td>
                                <td>₹{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : 'N/A' }}</td>
                                <td>{{ $payment->payment_method ?? 'N/A' }}</td>

                                <!-- Tracking Status (only badge) -->
                                <td>
                                    @if($isPayLater)
                                        @if($isReceived)
                                            <span class="badge bg-success">PAID</span>
                                        @else
                                            <span class="badge bg-warning text-dark">PAYMENT PENDING</span>
                                            @if($upcomingCount > 0)
                                                <span class="badge bg-info ms-1" title="{{ $upcomingCount }} upcoming follow-up(s)">
                                                    <i class="bi bi-bell"></i> {{ $upcomingCount }}
                                                </span>
                                            @endif
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <!-- ✅ NEW: Follow-up Info Column (Next Date + Added By) -->
                                <td>
                                    @if($isPayLater && !$isReceived && $upcomingCount > 0 && $nextFollowUp)
                                        <div class="small">
                                            <div>
                                                <i class="bi bi-calendar-event text-primary"></i>
                                                <strong>Next:</strong> {{ \Carbon\Carbon::parse($nextFollowUp->next_follow_up_date)->format('d M Y') }}
                                            </div>
                                            @if($latestFollowUp && $latestFollowUp->createdBy)
                                                <div class="text-muted mt-1">
                                                    <i class="bi bi-person"></i>
                                                    Added by: {{ $latestFollowUp->createdBy->name }}
                                                    @if($latestFollowUp->createdBy->role)
                                                        <span class="badge bg-secondary">{{ $latestFollowUp->createdBy->role->name }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($isPayLater && !$isReceived)
                                        <span class="text-muted">No follow-up</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>{{ $payment->receivedByStaff->name ?? 'N/A' }}</td>
                                <td>{{ $payment->remarks ?: '-' }}</td>

                                <!-- Actions -->
                                <td>
                                    <!-- Receipt Button -->
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#receiptModal" data-payment-id="{{ $payment->id }}" title="View Receipt">
                                        <i class="fas fa-receipt"></i> Receipt
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>

                                    <!-- Pay Later actions -->
                                    @if($isPayLater && !$isReceived)
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#followUpModal" data-payment-id="{{ $payment->id }}">
                                            <i class="fas fa-calendar-plus"></i> Follow Up
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#markPaidModal" data-payment-id="{{ $payment->id }}" data-client="{{ $payment->client->name ?? 'N/A' }}" data-amount="{{ number_format($payment->amount, 2) }}">
                                            <i class="fas fa-check-circle"></i> Mark Paid
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#followUpHistoryModal" data-payment-id="{{ $payment->id }}">
                                            <i class="fas fa-history"></i> History
                                        </button>
                                    @endif

                                    @if($isPayLater && $isReceived)
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#followUpHistoryModal" data-payment-id="{{ $payment->id }}">
                                            <i class="fas fa-history"></i> History
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">No payment records found.</div>
            @endif
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODALS -->
<!-- ========================================================= -->

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="receiptModalLabel">
                    <i class="fas fa-receipt"></i> Payment Receipt
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="receiptModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading receipt...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="downloadReceiptBtn" class="btn btn-success" target="_blank">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Follow-Up Modal -->
<div class="modal fade" id="followUpModal" tabindex="-1" aria-labelledby="followUpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="followUpForm" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="followUpModalLabel">
                        <i class="fas fa-calendar-plus"></i> Add Follow‑up
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="payment_id" id="followUpPaymentId" value="">
                    <div class="mb-3">
                        <label for="follow_up_date" class="form-label">Follow‑up Date *</label>
                        <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="next_follow_up_date" class="form-label">Next Follow‑up Date</label>
                        <input type="date" class="form-control" id="next_follow_up_date" name="next_follow_up_date">
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Follow‑up
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="markPaidModalLabel">
                    <i class="fas fa-check-circle"></i> Mark as Paid
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this Pay Later payment as <strong>PAID</strong>?</p>
                <div class="alert alert-warning">
                    <strong>Client:</strong> <span id="markPaidClient"></span><br>
                    <strong>Amount:</strong> ₹<span id="markPaidAmount"></span>
                </div>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form id="markPaidForm" method="POST">
                    @csrf
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Yes, Mark Paid
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Follow-Up History Modal -->
<div class="modal fade" id="followUpHistoryModal" tabindex="-1" aria-labelledby="followUpHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="followUpHistoryModalLabel">
                    <i class="fas fa-history"></i> Follow‑up History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="followUpHistoryBody">
                <div class="text-center">
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

@push('styles')
<style>
    /* Stats Card Styles (Staff Management Style) */
    .stats-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 20px;
        height: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
        border: none;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    .card-value {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 5px 0;
    }
    .card-label {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }
    .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    /* Section Card */
    .section-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 15px 20px;
        border: none;
    }

    /* ✅ Table Header – Staff Management Style (Light Gray) */
    body #paymentsTable thead th,
    body #paymentsTable thead td,
    body .dataTable thead th,
    body .dataTable thead td {
        background-color: #f8fafc !important;
        color: #64748b !important;
        font-weight: 600 !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    /* Table cells alignment */
    #paymentsTable tbody td,
    .dataTable tbody td {
        vertical-align: middle !important;
    }

    /* General fixes */
    .card-body {
        min-height: auto !important;
        height: auto !important;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {

    // 1. Receipt Modal
    $('#receiptModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var paymentId = button.data('payment-id');
        var modal = $(this);
        var modalBody = modal.find('#receiptModalBody');
        var downloadBtn = modal.find('#downloadReceiptBtn');

        modalBody.html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading receipt...</p>
            </div>
        `);

        var downloadUrl = "{{ route('admin.payments.download-receipt', ':id') }}".replace(':id', paymentId);
        downloadBtn.attr('href', downloadUrl);

        $.ajax({
            url: "{{ route('admin.payments.receipt-data', ':id') }}".replace(':id', paymentId),
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    modalBody.html(response.html);
                } else {
                    modalBody.html('<div class="alert alert-danger">Failed to load receipt.</div>');
                }
            },
            error: function() {
                modalBody.html('<div class="alert alert-danger">Error loading receipt.</div>');
            }
        });
    });

    // 2. Follow-Up Modal (using route name)
    $('#followUpModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var paymentId = button.data('payment-id');
        $('#followUpPaymentId').val(paymentId);
        var form = $('#followUpForm');
        var action = "{{ route('admin.payments.follow-up.store', ['paymentId' => ':id']) }}".replace(':id', paymentId);
        form.attr('action', action);
    });

    // 3. Mark as Paid Modal (using route name)
    $('#markPaidModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var paymentId = button.data('payment-id');
        var client = button.data('client');
        var amount = button.data('amount');
        $('#markPaidClient').text(client);
        $('#markPaidAmount').text(amount);
        var form = $('#markPaidForm');
        var action = "{{ route('admin.payments.mark-paid', ['paymentId' => ':id']) }}".replace(':id', paymentId);
        form.attr('action', action);
    });

    // 4. Follow-Up History Modal (using route name)
    $('#followUpHistoryModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var paymentId = button.data('payment-id');
        var modalBody = $('#followUpHistoryBody');

        modalBody.html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading follow‑up history...</p>
            </div>
        `);

        var url = "{{ route('admin.payments.follow-ups', ['paymentId' => ':id']) }}".replace(':id', paymentId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var html = '';
                    if (response.follow_ups.length === 0) {
                        html = '<div class="alert alert-info">No follow‑ups recorded for this payment.</div>';
                    } else {
                        html += `<div class="mb-3"><strong>Payment:</strong> ${response.payment.client} – ₹${response.payment.amount} (${response.payment.payment_method})</div>`;
                        html += `<div class="list-group">`;
                        response.follow_ups.forEach(function(item) {
                            html += `
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><i class="fas fa-calendar-day"></i> ${item.follow_up_date}</h6>
                                        <small>${item.created_at}</small>
                                    </div>
                                    <p class="mb-1">${item.note || '<em>No note</em>'}</p>
                                    <small>
                                        <strong>Next follow‑up:</strong> ${item.next_follow_up_date || 'None'}
                                        <span class="ms-3"><strong>By:</strong> ${item.created_by}</span>
                                        <span class="ms-2 badge bg-secondary">${item.role}</span>
                                    </small>
                                </div>
                            `;
                        });
                        html += `</div>`;
                    }
                    modalBody.html(html);
                } else {
                    modalBody.html('<div class="alert alert-danger">' + (response.message || 'Failed to load history.') + '</div>');
                }
            },
            error: function() {
                modalBody.html('<div class="alert alert-danger">Error loading follow‑up history.</div>');
            }
        });
    });

});
</script>

@if(session('success'))
<script>
    $(document).ready(function() {
        alert('Success: {{ session('success') }}');
    });
</script>
@endif
@if(session('error'))
<script>
    $(document).ready(function() {
        alert('Error: {{ session('error') }}');
    });
</script>
@endif
@endpush