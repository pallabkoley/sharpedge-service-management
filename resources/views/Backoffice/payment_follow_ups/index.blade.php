@extends('layouts.Backoffice')

@section('page-title', 'Payment Follow-ups')

@section('content')
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TOTAL PENDING</h6>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $totalPending }}</h3>
                <div class="card-change up">
                    <i class="bi bi-arrow-up me-1"></i>
                    <span>Pay Later payments</span>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING AMOUNT</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-cash"></i>
                    </div>
                </div>
                <h3 class="card-value">₹{{ number_format($totalPendingAmount, 2) }}</h3>
                <div class="card-change up">
                    <i class="bi bi-arrow-up me-1"></i>
                    <span>Total due</span>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TODAY'S FOLLOW‑UPS</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $todayFollowUps }}</h3>
                <div class="card-change up">
                    <i class="bi bi-calendar me-1"></i>
                    <span>Due today</span>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">OVERDUE</h6>
                    <div class="card-icon bg-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $overdueFollowUps }}</h3>
                <div class="card-change down">
                    <i class="bi bi-arrow-down me-1"></i>
                    <span>Needs attention</span>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">NEVER FOLLOWED</h6>
                    <div class="card-icon bg-secondary">
                        <i class="bi bi-ban"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $neverFollowedUp }}</h3>
                <div class="card-change up">
                    <i class="bi bi-arrow-up me-1"></i>
                    <span>No follow‑up yet</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Section -->
    <div class="content-section">
        <div class="section-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">Pending Pay Later Payments</h4>
                <div class="d-flex gap-2">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('backoffice.payment-follow-ups.index') }}" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search client..." 
                                   value="{{ request('search') }}" style="min-width: 200px;">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('backoffice.payment-follow-ups.index', ['search' => request('search')]) }}" 
                   class="btn btn-sm {{ !request('follow_up') ? 'btn-primary' : 'btn-outline-primary' }}">
                    All
                </a>
                <a href="{{ route('backoffice.payment-follow-ups.index', ['follow_up' => 'never', 'search' => request('search')]) }}" 
                   class="btn btn-sm {{ request('follow_up') == 'never' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Never
                </a>
                <a href="{{ route('backoffice.payment-follow-ups.index', ['follow_up' => 'due', 'search' => request('search')]) }}" 
                   class="btn btn-sm {{ request('follow_up') == 'due' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Due / Overdue
                </a>
                <a href="{{ route('backoffice.payment-follow-ups.index', ['follow_up' => 'upcoming', 'search' => request('search')]) }}" 
                   class="btn btn-sm {{ request('follow_up') == 'upcoming' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Upcoming
                </a>
            </div>

            @if($payments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 48px;"></i>
                    <h4 class="mt-3">No Pending Payments</h4>
                    <p class="text-muted">All approved Pay Later payments have been received or no records found.</p>
                    @if(request('search') || request('follow_up'))
                        <a href="{{ route('backoffice.payment-follow-ups.index') }}" class="btn btn-sm btn-primary mt-2">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="paymentsTable">
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
                            @foreach($payments as $payment)
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
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-weight: 600; font-size: 14px;">
                                                {{ strtoupper(substr($payment->client->name ?? 'N/A', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $payment->client->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $payment->client->phone ?? '' }} | {{ $payment->client->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">
                                            #{{ $payment->order_id }}
                                        </span>
                                    </td>
                                    <td>₹{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '-' }}</td>
                                    <td>{{ $lastFollowUpDate ? \Carbon\Carbon::parse($lastFollowUpDate)->format('d M Y') : '-' }}</td>
                                    <td>{{ $nextFollowUpDate ? \Carbon\Carbon::parse($nextFollowUpDate)->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($statusClass == 'never')
                                            <span class="badge bg-secondary">Never</span>
                                        @elseif($statusClass == 'due')
                                            <span class="badge bg-danger">Due / Overdue</span>
                                        @else
                                            <span class="badge bg-info">Upcoming</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-primary btn-follow-up" 
                                                    data-payment-id="{{ $payment->id }}"
                                                    data-client-name="{{ $payment->client->name ?? 'N/A' }}"
                                                    data-order-id="{{ $payment->order_id }}"
                                                    data-amount="{{ number_format($payment->amount, 2) }}"
                                                    title="Add Follow-up">
                                                <i class="bi bi-plus-circle"></i> Follow Up
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary btn-history" 
                                                    data-payment-id="{{ $payment->id }}"
                                                    title="View History">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} entries
                        </small>
                    </div>
                    <div>
                        {{ $payments->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
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
                <form id="followUpForm" method="POST" action="{{ route('backoffice.payment-follow-ups.store') }}">
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
                            <textarea class="form-control" name="note" id="followUpNote" rows="3" placeholder="Add remarks..." required></textarea>
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
    <!-- History Modal (AJAX loaded) -->
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
    <!-- jQuery (if not already loaded) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (if not already loaded) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Toastr config
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "4000"
            };

            // Session messages
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

            // DataTables initialization (sort only)
            if ($('#paymentsTable').length && !$.fn.DataTable.isDataTable('#paymentsTable')) {
                $('#paymentsTable').DataTable({
                    order: [[3, 'desc']], // sort by payment date
                    paging: false,
                    searching: false,
                    info: false,
                    columnDefs: [
                        { targets: 2, type: 'num' } // amount column
                    ]
                });
            }

            // Follow-up button
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

                const modal = new bootstrap.Modal(document.getElementById('followUpModal'));
                modal.show();
            });

            // Follow-up form validation (client-side)
            $('#followUpForm').on('submit', function(e) {
                const date = $('#followUpDate').val();
                if (!date) {
                    e.preventDefault();
                    toastr.error('Please select a follow-up date.');
                    return false;
                }
                // Note is already required by HTML5; no extra check needed.
            });

            // History button – AJAX load
            $(document).on('click', '.btn-history', function() {
                const paymentId = $(this).data('payment-id');
                const modal = new bootstrap.Modal(document.getElementById('historyModal'));
                const body = $('#historyModalBody');

                body.html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading history...</p>
                    </div>
                `);

                modal.show();

                $.ajax({
                    url: "{{ url('backoffice/payment-follow-ups') }}/" + paymentId + "/follow-ups",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const followUps = response.follow_ups;
                            let html = '';
                            if (followUps.length === 0) {
                                html = '<div class="alert alert-info">No follow-up records found.</div>';
                            } else {
                                html = '<div class="timeline">';
                                followUps.forEach(function(item) {
                                    html += `
                                        <div class="timeline-item">
                                            <div class="timeline-date"><strong>${item.follow_up_date}</strong></div>
                                            <div>Next: ${item.next_follow_up_date || 'N/A'}</div>
                                            <div>${item.note ? '<p class="mb-1">' + item.note + '</p>' : ''}</div>
                                            <div class="text-muted small">
                                                <i class="bi bi-person"></i> ${item.created_by || 'System'} &bull;
                                                <i class="bi bi-clock"></i> ${item.created_at || ''}
                                            </div>
                                        </div>
                                    `;
                                });
                                html += '</div>';
                            }
                            body.html(html);
                        } else {
                            body.html('<div class="alert alert-danger">Could not load history.</div>');
                        }
                    },
                    error: function() {
                        body.html('<div class="alert alert-danger">An error occurred while loading history.</div>');
                    }
                });
            });

            // Auto-submit search on Enter
            $('input[name="search"]').on('keypress', function(e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
            });
        });
    </script>

    <style>
        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }
        .timeline-item {
            border-left: 2px solid #dee2e6;
            padding-left: 1.5rem;
            padding-bottom: 1.5rem;
            position: relative;
        }
        .timeline-item:last-child {
            border-left: 2px solid transparent;
            padding-bottom: 0;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #0d6efd;
            border: 2px solid white;
        }
        .timeline-date {
            font-weight: 600;
        }
    </style>
@endsection