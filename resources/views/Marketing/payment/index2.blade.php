@extends('layouts.marketing')

@section('title', 'Payment Management - SharpEdge Marketing')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
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
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 1.25rem;
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
        margin-bottom: 0.25rem;
    }
    .stat-info p {
        color: var(--gray-500);
        margin-bottom: 0;
    }
    .main-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
    .filters {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .table-container {
        overflow-x: auto;
    }
    .payments-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .payments-table thead {
        background-color: #3b4f6b;
    }
    .payments-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: white;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }
    .payments-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
        font-size: 0.875rem;
    }
    .payments-table tbody tr:hover {
        background-color: var(--gray-50);
    }
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        cursor: pointer;
    }
    .action-btn.edit {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }
    .action-btn.delete {
        background: linear-gradient(135deg, var(--danger-color), #dc2626);
    }
    .btn-add {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .pagination-container {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    @media (max-width: 768px) {
        .stats-cards { grid-template-columns: 1fr; }
        .card-header { flex-direction: column; align-items: stretch; }
        .filters { flex-direction: column; }
        .payments-table { min-width: 800px; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="header">
        <h1>Payment Management</h1>
        <p>Record and manage all client payments</p>
    </div>

    <!-- Statistics Cards -->
    @php
        $totalAmount = $payments->sum('amount');
        $avgAmount = $payments->count() ? $totalAmount / $payments->count() : 0;
    @endphp
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $payments->total() }}</h3>
                <p>Total Payments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary-color), #0da271);">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-info">
                <h3>₹{{ number_format($totalAmount, 2) }}</h3>
                <p>Total Amount</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #e58e0b);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>₹{{ number_format($avgAmount, 2) }}</h3>
                <p>Average Payment</p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="main-card">
        <div class="card-header">
            <div>
                <h2>Payment List</h2>
                <p class="text-muted">All recorded transactions</p>
            </div>
            <div>
                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="fas fa-plus"></i> Add New Payment
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="{{ route('marketing.payments.index') }}" class="d-flex gap-2 flex-wrap w-100">
                <div class="flex-grow-1" style="min-width: 200px;">
                    <input type="text" name="search" class="form-control" placeholder="Search by client, remarks..." value="{{ request('search') }}">
                </div>
                <div style="min-width: 150px;">
                    <select name="client_id" class="form-select">
                        <option value="">All Clients</option>
                        @foreach($clients as $id => $name)
                            <option value="{{ $id }}" {{ request('client_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                @if(request('search') || request('client_id'))
                    <a href="{{ route('marketing.payments.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>
        </div>

        <!-- Payments Table -->
        <div class="table-container">
            <table class="payments-table" id="paymentsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Payment Date</th>
                        <th>Remarks</th>
                        <th>Received By</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->client->name ?? 'N/A' }}</td>
                            <td>₹{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                            <td>{{ $payment->remarks ?? '-' }}</td>
                            <td>{{ $payment->receivedBy->name ?? 'System' }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->status == 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit edit-payment"
                                            data-id="{{ $payment->id }}"
                                            data-amount="{{ $payment->amount }}"
                                            data-method="{{ $payment->payment_method }}"
                                            data-remarks="{{ $payment->remarks }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete delete-payment"
                                            data-id="{{ $payment->id }}"
                                            data-client="{{ $payment->client->name ?? '' }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-receipt fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No payments found.</p>
                                @if(request('search') || request('client_id'))
                                    <a href="{{ route('marketing.payments.index') }}" class="btn btn-sm btn-primary">Clear Filters</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="pagination-container">
                <div class="text-muted small">
                    Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} entries
                </div>
                <div>
                    {{ $payments->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('marketing.payments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Client</label>
                        <select name="client_id" class="form-select" required>
                            <option value="">Select Client</option>
                            @foreach($clients as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">Select</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPaymentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" id="editAmount" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Payment Method</label>
                        <select name="payment_method" id="editMethod" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" id="editRemarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Payment Modal -->
<div class="modal fade" id="deletePaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this payment for <strong id="deleteClientName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deletePaymentForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- DataTables CSS & JS (if not loaded in layout) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    toastr.options = { closeButton: true, progressBar: true, timeOut: 3000 };

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

    // ✅ DataTables Initialization for Payment Table
    $('#paymentsTable').DataTable({
        order: [[0, 'desc']], // Sort by ID descending
        paging: false,        // Custom pagination exists
        searching: false,     // Custom search exists
        info: false,          // Custom info exists
        columnDefs: [
            {
                targets: 0, // ID column
                type: 'numeric'
            },
            {
                targets: 2, // Amount column
                type: 'numeric',
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        // Extract numeric value from "₹1,234.56"
                        const num = parseFloat(data.replace(/[₹,]/g, ''));
                        return isNaN(num) ? 0 : num;
                    }
                    return data;
                }
            },
            {
                targets: 4, // Payment Date column - for date sorting
                type: 'date'
            }
        ]
    });

    // Edit payment
    $(document).on('click', '.edit-payment', function() {
        let id = $(this).data('id');
        let amount = $(this).data('amount');
        let method = $(this).data('method');
        let remarks = $(this).data('remarks');

        $('#editAmount').val(amount);
        $('#editMethod').val(method);
        $('#editRemarks').val(remarks);
        $('#editPaymentForm').attr('action', "{{ route('marketing.payments.update', ':id') }}".replace(':id', id));

        new bootstrap.Modal(document.getElementById('editPaymentModal')).show();
    });

    // Delete payment
    $(document).on('click', '.delete-payment', function() {
        let id = $(this).data('id');
        let clientName = $(this).data('client');
        $('#deleteClientName').text(clientName);
        $('#deletePaymentForm').attr('action', "{{ route('marketing.payments.destroy', ':id') }}".replace(':id', id));
        new bootstrap.Modal(document.getElementById('deletePaymentModal')).show();
    });

    // Validate amount on add form
    $('#addPaymentModal form').on('submit', function(e) {
        let amount = parseFloat($('input[name="amount"]').val());
        if (isNaN(amount) || amount <= 0) {
            e.preventDefault();
            toastr.error('Amount must be a positive number');
        }
    });

    // Validate amount on edit form
    $('#editPaymentForm').on('submit', function(e) {
        let amount = parseFloat($('#editAmount').val());
        if (isNaN(amount) || amount <= 0) {
            e.preventDefault();
            toastr.error('Amount must be a positive number');
        }
    });

    // Disable submit button on form submit to prevent double submission
    $('form').on('submit', function() {
        let btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm"></span> Processing...');
    });
});
</script>
@endsection