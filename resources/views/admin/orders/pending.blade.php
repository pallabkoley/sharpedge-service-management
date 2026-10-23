@extends('layouts.admin')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Validation Error:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@section('content')
    <!-- Header -->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Payment Management</h2>
                <p class="text-muted mb-0">Review and manage all payment submissions from marketing panel</p>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING APPROVALS</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $pendingCount }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Awaiting your review</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TOTAL AMOUNT</h6>
                    <div class="card-icon bg-info">
                        <i class="bi bi-currency-rupee"></i>
                    </div>
                </div>
                <h3 class="card-value">₹{{ number_format($totalAmount, 2) }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>All submitted payments</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">UNIQUE CLIENTS</h6>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $uniqueClientsCount }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>With submitted payments</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">ALL PAYMENTS</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $allPayments }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Total submissions</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Submissions Table -->
    <div class="mt-4">
        <div class="section-card">
            <!-- Status Filters - Instant JS Filter (No Page Reload) -->
            <div class="d-flex gap-2 mb-4">
                <button type="button" class="btn btn-sm btn-primary filter-btn" data-status="all">All Payments</button>
                <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-status="submitted">Pending</button>
                <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-status="approved">Approved</button>
                <button type="button" class="btn btn-sm btn-outline-danger filter-btn" data-status="rejected">Rejected</button>
                <!-- CHANGE 1: New filter button for Invoice Pending -->
                <button type="button" class="btn btn-sm btn-outline-info filter-btn" data-status="invoice_pending">
                    Invoice Pending
                </button>
            </div>

            <!-- Date Range + Client Dropdown Filter UI -->
            <div class="d-flex gap-2 mb-3 align-items-center flex-wrap">
                <input type="date" id="fromDate" class="form-control form-control-sm" style="width: 160px;">
                <input type="date" id="toDate" class="form-control form-control-sm" style="width: 160px;">
                <select id="clientFilter" class="form-control form-control-sm" style="width: 220px;">
                    <option value="">All Clients</option>
                </select>
                <button class="btn btn-sm btn-primary" id="filterDate">Filter</button>
                <button class="btn btn-sm btn-secondary" id="resetDate">Reset</button>
            </div>

            <!-- Info Alert -->
            @if(request('status') == 'submitted' || !request('status'))
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="bi bi-info-circle me-2 fs-5"></i>
                <div>
                    <strong>{{ $pendingCount }} pending payments</strong> require your attention
                </div>
            </div>
            @endif

            @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover datatable" id="clientsTable">
                    <thead>
                        <tr>
                            <th>ORDER ID</th>
                            <th>CLIENT</th>
                            <th>MARKETING STAFF</th>
                            <th>PAYMENT DETAILS</th>
                            <th>SUBMITTED AT</th>
                            <th>PAYMENT STATUS</th>
                            <th>ORDER STATUS</th>
                            <th>INVOICE STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        @php
                            $latestPayment = $order->latestPayment;
                        @endphp
                        <tr>
                            <td class="fw-bold">
                                <span style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 4px 10px; border-radius: 4px; font-family: 'Courier New', monospace;">
                                    {{ $order->order_id }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $order->client->name ?? 'N/A' }}</strong>
                                    <small class="text-muted">{{ $order->client->email ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $order->marketingStaff->name ?? 'N/A' }}</strong>
                                    <small class="text-muted">{{ $order->marketingStaff->email ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                @if($latestPayment)
                                    <div class="d-flex flex-column">
                                        <strong class="text-primary">₹{{ number_format($order->final_amount, 2) }}</strong>
                                        
                                        <!-- Ledger Calculation -->
                                        <small class="text-muted">
                                            Due: <span class="{{ $order->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                ₹{{ number_format($order->due_amount, 2) }}
                                            </span>
                                        </small>
                                        
                                        <!-- Payment Proof Link/Button -->
                                        @if($latestPayment->payment_proof_path)
                                            @php
                                                $proofUrl = asset('storage/' . $latestPayment->payment_proof_path);
                                            @endphp
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info mt-2 view-proof-btn"
                                                    data-payment-id="{{ $latestPayment->id }}"
                                                    data-payment-amount="{{ $latestPayment->amount }}"
                                                    data-payment-method="{{ $latestPayment->payment_method }}"
                                                    data-payment-date="{{ $latestPayment->payment_date }}"
                                                    data-transaction-id="{{ $latestPayment->transaction_id }}"
                                                    data-proof-url="{{ $proofUrl }}">
                                                <i class="bi bi-file-earmark-image"></i> View Proof
                                            </button>
                                        @else
                                            <span class="text-muted small">No proof uploaded</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">No payment details</span>
                                @endif
                            </td>
                            <!-- SUBMITTED AT column with data-date attribute for filtering -->
                            <td data-date="{{ $latestPayment->created_at ?? '' }}">
                                @if($latestPayment)
                                <div class="d-flex flex-column">
                                    <strong>{{ $latestPayment->created_at->format('d M, Y') }}</strong>
                                    <small class="text-muted">{{ $latestPayment->created_at->format('h:i A') }}</small>
                                </div>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($latestPayment)
                                    @if($latestPayment->status == 'submitted')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i> PENDING
                                    </span>
                                    @elseif($latestPayment->status == 'approved')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> APPROVED
                                    </span>
                                    <div class="text-xs text-muted mt-1">
                                        <small>Approved on: {{ $latestPayment->updated_at->format('d M Y') }}</small>
                                    </div>
                                    @elseif($latestPayment->status == 'rejected')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i> REJECTED
                                    </span>
                                    <div class="text-xs text-muted mt-1">
                                        <small>Rejected on: {{ $latestPayment->updated_at->format('d M Y') }}</small>
                                    </div>
                                    @else
                                    <span class="badge bg-secondary">{{ strtoupper($latestPayment->status) }}</span>
                                    @endif
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($order->order_status == 'PAYMENT_SUBMITTED')
                                <span class="badge bg-warning text-dark">PAYMENT SUBMITTED</span>
                                @elseif($order->order_status == 'PAID')
                                <span class="badge bg-success">PAID</span>
                                @elseif($order->order_status == 'PENDING_PAYMENT')
                                <span class="badge bg-danger">PENDING PAYMENT</span>
                                @else
                                <span class="badge bg-secondary">{{ str_replace('_', ' ', $order->order_status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($latestPayment && $latestPayment->status == 'approved')
                                    @if(!$order->invoice_generated)
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-hourglass"></i> INVOICE PENDING
                                        </span>
                                    @else
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-success">
                                                <i class="bi bi-file-text"></i> INVOICE GENERATED
                                            </span>
                                            <small class="text-muted mt-1">
                                                @if(str_contains($order->invoice_path, '-R'))
                                                    Revised v{{ substr_count($order->invoice_path, '-R') }}
                                                @else
                                                    Original
                                                @endif
                                            </small>
                                            <small class="text-primary">
                                                ₹{{ number_format($order->final_amount, 2) }}
                                            </small>
                                        </div>
                                    @endif
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($latestPayment)
                                <!-- PAYMENT ACTIONS -->
                                <div class="btn-group" role="group">
                                    <!-- Approve Button -->
                                    <form action="{{ route('admin.orders.approve', $order) }}" 
                                          method="POST" 
                                          class="d-inline me-1"
                                          onsubmit="return confirmApprove(event, '{{ $order->order_id }}');">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $latestPayment->status == 'approved' ? 'btn-success disabled' : 'btn-success' }}" 
                                                title="Approve Payment"
                                                @if($latestPayment->status == 'approved') disabled @endif>
                                            <i class="bi bi-check-lg"></i> 
                                            {{ $latestPayment->status == 'approved' ? 'Approved' : 'Approve' }}
                                        </button>
                                    </form>
                                    
                                    <!-- Reject Button -->
                                    <form action="{{ route('admin.orders.reject', $order) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirmReject(event, '{{ $order->order_id }}');">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $latestPayment->status == 'rejected' ? 'btn-danger disabled' : 'btn-danger' }}" 
                                                title="Reject Payment"
                                                @if($latestPayment->status == 'rejected') disabled @endif>
                                            <i class="bi bi-x-lg"></i> 
                                            {{ $latestPayment->status == 'rejected' ? 'Rejected' : 'Reject' }}
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- INVOICE ACTIONS -->
                                @if($latestPayment->status == 'approved')
                                <div class="invoice-actions mt-3">
                                    @if(!$order->invoice_generated)
                                    <!-- Generate Invoice -->
                                    <form method="POST" 
                                          action="{{ route('admin.orders.generate-invoice', $order) }}"
                                          class="invoice-form mb-2"
                                          onsubmit="return validateInvoiceForm(this);">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                   step="0.01"
                                                   name="final_govt_fee"
                                                   class="form-control form-control-sm"
                                                   placeholder="Final Govt Fee"
                                                   value="{{ $order->estimated_govt_fee ?? 0 }}"
                                                   required>
                                            <button type="submit" 
                                                    class="btn btn-success btn-sm">
                                                <i class="bi bi-file-earmark-text"></i> Generate
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-1">Set final govt fee</small>
                                    </form>
                                    @else
                                    <!-- Invoice Generated Actions -->
                                    <div class="d-flex flex-column gap-1">
                                        <!-- View Invoice -->
                                        <a href="{{ route('admin.orders.invoice.view', $order) }}"
                                           target="_blank"
                                           class="btn btn-info btn-sm">
                                            <i class="bi bi-eye"></i> View Invoice
                                        </a>
                                        
                                        <button type="button" 
                                                class="btn btn-primary btn-sm addon-btn"
                                                data-order-id="{{ $order->id }}"
                                                data-order-code="{{ $order->order_id }}"
                                                data-client-name="{{ $order->client->name ?? 'N/A' }}"
                                                data-order-total="{{ $order->final_amount }}">
                                            <i class="bi bi-plus-circle"></i> Add Add-on
                                        </button>
                                        
                                        <button type="button" 
                                            class="btn btn-secondary btn-sm edit-invoice-btn"
                                            data-order-id="{{ $order->id }}"
                                            data-order-code="{{ $order->order_id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editInvoiceModal{{ $order->id }}">
                                            <i class="bi bi-pencil"></i> Edit Govt Fee
                                        </button>
                                    </div>
                                    
                                    <!-- Invoice Details -->
                                    <div class="invoice-info small mt-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Govt Fee:</span>
                                            <strong>₹{{ number_format($order->final_govt_fee, 2) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Total:</span>
                                            <strong class="text-primary">₹{{ number_format($order->final_amount, 2) }}</strong>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif
                                
                                <!-- View Details Button -->
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary mt-2 w-100 view-details-btn"
                                        data-order-id="{{ $order->id }}"
                                        data-order-code="{{ $order->order_id }}">
                                    <i class="bi bi-eye"></i> View Details
                                </button>
                                @else
                                <span class="text-muted">No payment</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- DYNAMIC FILTERED TOTAL CARD -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 bg-light">
                        <div class="card-body">
                            <h6 class="text-muted mb-2"><i class="bi bi-calculator"></i> Filtered Total Amount</h6>
                            <h4 id="filteredTotal" class="mb-0 text-primary">₹0.00</h4>
                            <small class="text-muted">Based on current filters</small>
                        </div>
                    </div>
                </div>
            </div>

            @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-credit-card fs-1 text-muted"></i>
                </div>
                <h4 class="mb-3">No Payment Submissions</h4>
                <p class="text-muted mb-4">No payment submissions found for marketing orders.</p>
                <p class="text-muted small">Payment submissions from marketing panel will appear here.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Revise Invoice Modals (One for each order) -->
    @foreach($orders as $order)
    @if($order->invoice_generated)
    <div class="modal fade" id="reviseInvoiceModal{{ $order->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.orders.revise-invoice', $order) }}" id="reviseInvoiceForm{{ $order->id }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square"></i> Revise Invoice
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Important:</strong> This will generate a new revised invoice. Old invoices will remain archived.
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Current Invoice Details</label>
                            <div class="border rounded p-2 bg-light">
                                <div class="d-flex justify-content-between">
                                    <span>Govt Fee:</span>
                                    <strong>₹{{ number_format($order->final_govt_fee, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Total Amount:</span>
                                    <strong class="text-primary">₹{{ number_format($order->final_amount, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">New Government Fee *</label>
                            <input type="number"
                                   step="0.01"
                                   name="final_govt_fee"
                                   class="form-control"
                                   value="{{ $order->final_govt_fee }}"
                                   required>
                            <small class="text-muted">Enter the updated government fee amount</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Revision Reason *</label>
                            <textarea name="revision_reason"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Explain why this invoice needs revision..."
                                      required></textarea>
                            <small class="text-muted">This reason will be logged in the audit trail</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil-square"></i> Revise Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    @if($order->invoice_generated)
    <div class="modal fade" id="editInvoiceModal{{ $order->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square"></i> Edit Government Fees - Order: {{ $order->order_id }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.orders.edit-invoice', $order) }}" class="edit-invoice-form" id="editInvoiceForm{{ $order->id }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Note:</strong> You can edit the government fee for each service. Other fields are fixed.
                        </div>

                        <!-- Order Summary -->
                        <div class="order-summary bg-light p-3 rounded mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Client:</strong> {{ $order->client->name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Order Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Invoice No:</strong> {{ $order->invoice_number }}</p>
                                    <p class="mb-1"><strong>Current Total:</strong> ₹{{ number_format($order->final_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Services Table with Editable Govt Fees -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Service</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Taxable</th>
                                        <th>Current Govt Fee</th>
                                        <th>New Govt Fee</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->service->service_name ?? 'Service' }}
                                            @if($item->is_addon)
                                                <span class="badge bg-info">Add-on</span>
                                            @endif
                                          </td>
                                        <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">₹{{ number_format($item->taxable_amount ?? $item->total, 2) }}</td>
                                        <td class="text-end">
                                            <span id="current_govt_fee_{{ $item->id }}">
                                                ₹{{ number_format($item->govt_fee ?? 0, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                                            <input type="number" 
                                                   name="govt_fees[{{ $item->id }}]" 
                                                   class="form-control form-control-sm govt-fee-input" 
                                                   value="{{ $item->govt_fee ?? 0 }}"
                                                   step="0.01"
                                                   min="0"
                                                   data-item-id="{{ $item->id }}"
                                                   data-original-fee="{{ $item->govt_fee ?? 0 }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Price Calculation Preview -->
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Updated Price Calculation</h6>
                            </div>
                            <div class="card-body" id="pricePreview{{ $order->id }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Total Service Fee:</td>
                                                <td class="text-end">₹<span id="totalServiceFee{{ $order->id }}">{{ number_format($order->base_price, 2) }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>Total Discount:</td>
                                                <td class="text-end">₹<span id="totalDiscount{{ $order->id }}">{{ number_format($order->discount_amount, 2) }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>Taxable Amount:</td>
                                                <td class="text-end">₹<span id="taxableAmount{{ $order->id }}">{{ number_format($order->base_price - $order->discount_amount, 2) }}</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>CGST ({{ config('app.gst_percentage', 18)/2 }}%):</td>
                                                <td class="text-end">₹<span id="cgstAmount{{ $order->id }}">{{ number_format($order->cgst ?? 0, 2) }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>SGST ({{ config('app.gst_percentage', 18)/2 }}%):</td>
                                                <td class="text-end">₹<span id="sgstAmount{{ $order->id }}">{{ number_format($order->sgst ?? 0, 2) }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>Total Govt Fee:</td>
                                                <td class="text-end">₹<span id="totalGovtFee{{ $order->id }}">{{ number_format($order->final_govt_fee, 2) }}</span></td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td>New Grand Total:</td>
                                                <td class="text-end text-primary">₹<span id="newGrandTotal{{ $order->id }}">{{ number_format($order->final_amount, 2) }}</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Revision Reason</label>
                            <textarea name="revision_reason" 
                                      class="form-control" 
                                      rows="2" 
                                      placeholder="Explain why you're editing the government fees..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">
                        <i class="bi bi-receipt"></i> Order Details: <span id="modalOrderId"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading order details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                    <button type="button" class="btn btn-primary" id="printOrderBtn">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add-on Service Modal -->
    <div class="modal fade" id="addonServiceModal" tabindex="-1" aria-labelledby="addonServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addonServiceModalLabel">
                        <i class="bi bi-plus-circle"></i> Add Add-on Service
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addonServiceForm" action="{{ route('admin.orders.add-addon') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Hidden field for order_id (will be set dynamically) -->
                        <input type="hidden" name="order_id" id="modal_order_id" value="">
                        
                        <!-- Order Summary (Read-only) -->
                        <div class="order-summary bg-light p-3 rounded mb-4">
                            <h6 class="mb-3">Order Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Order ID:</strong> <span id="modal_order_code"></span></p>
                                    <p class="mb-1"><strong>Client:</strong> <span id="modal_client_name"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Current Total:</strong> ₹<span id="modal_order_total"></span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1: Select Service -->
                        <div class="step-container" id="step1">
                            <h6 class="step-title">Step 1: Select Add-on Service</h6>
                            <div class="mb-3">
                                <label for="service_id" class="form-label">Service *</label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">-- Select Service --</option>
                                    @foreach($services ?? [] as $service)
                                        <option value="{{ $service->id }}" 
                                                data-price="{{ $service->our_fee }}"
                                                data-govt-fee="{{ $service->govt_fee }}"
                                                data-taxable="{{ $service->is_taxable }}"
                                                data-tax="{{ $service->tax_percentage }}">
                                            {{ $service->service_name }} - ₹{{ number_format($service->our_fee, 2) }}
                                            @if($service->is_taxable)
                                                (+{{ $service->tax_percentage }}% GST)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Step 2: Configure Add-on -->
                        <div class="step-container mt-4" id="step2" style="display: none;">
                            <h6 class="step-title">Step 2: Configure Add-on</h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="discount_percent" class="form-label">Discount % (Optional)</label>
                                    <input type="number" class="form-control" id="discount_percent" name="discount_percent" value="0" min="0" max="100" step="0.01">
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Price Calculation</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td>Base Price:</td>
                                            <td class="text-end">₹<span id="displayBasePrice">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td>Quantity:</td>
                                            <td class="text-end"><span id="displayQuantity">1</span></td>
                                        </tr>
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td class="text-end">₹<span id="displaySubtotal">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td>Discount:</td>
                                            <td class="text-end">- ₹<span id="displayDiscount">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td>Taxable Amount:</td>
                                            <td class="text-end">₹<span id="displayTaxableAmount">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td>CGST (<span id="cgstRate">9</span>%):</td>
                                            <td class="text-end">+ ₹<span id="displayCGST">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td>SGST (<span id="sgstRate">9</span>%):</td>
                                            <td class="text-end">+ ₹<span id="displaySGST">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td>Government Fee:</td>
                                            <td class="text-end">+ ₹<span id="displayGovtFee">0.00</span></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Add-on Total:</td>
                                            <td class="text-end text-primary">₹<span id="displayAddonTotal">0.00</span></td>
                                        </tr>
                                        <tr class="fw-bold border-top">
                                            <td>New Order Total:</td>
                                            <td class="text-end text-success">₹<span id="displayNewTotal">0.00</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <label for="remarks" class="form-label">Remarks (Optional)</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="prevStepBtn" style="display: none;">Previous</button>
                        <button type="button" class="btn btn-primary" id="nextStepBtn">Next</button>
                        <button type="submit" class="btn btn-success" id="submitAddonBtn" style="display: none;">
                            <i class="bi bi-check-circle"></i> Add Add-on Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add-on Success Modal -->
    <div class="modal fade" id="addonSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle"></i> Add-on Added Successfully
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>The add-on service has been added to the order and the invoice has been updated.</p>
                    <div class="alert alert-info">
                        <strong>Invoice Updated:</strong> A revised invoice has been generated with the add-on service.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="viewUpdatedInvoiceBtn">
                        <i class="bi bi-eye"></i> View Updated Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Proof Modal -->
    <div class="modal fade" id="paymentProofModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark-image"></i> Payment Proof
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="payment-details mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Amount:</strong> ₹<span id="proofAmount"></span></p>
                                <p><strong>Method:</strong> <span id="proofMethod"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Submitted:</strong> <span id="proofDate"></span></p>
                                <p><strong>Transaction ID:</strong> <span id="proofTransactionId"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="proof-image-container text-center">
                        <img id="proofImage" src="" alt="Payment Proof" class="img-fluid" style="max-height: 500px;">
                        <iframe id="proofPdf" src="" style="width:100%; height:500px; display:none;" frameborder="0"></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="downloadProofBtn" class="btn btn-primary" download>
                        <i class="bi bi-download"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="mb-0">© {{ date('Y') }} <strong>SharpEdge</strong>. All rights reserved. | Admin Dashboard v2.4</p>
    </footer>
@endsection

@push('styles')
<style>
    /* Custom styles for payment approval page */
    .stats-card .card-icon.bg-warning {
        background-color: #f59e0b !important;
    }
    
    .stats-card .card-icon.bg-info {
        background-color: #0ea5e9 !important;
    }
    
    .stats-card .card-icon.bg-success {
        background-color: #10b981 !important;
    }
    
    .stats-card .card-change {
        font-size: 14px;
        display: flex;
        align-items: center;
        color: #64748b;
    }
    
    .stats-card .card-change i {
        color: #3b82f6;
    }
    
    .table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 15px;
    }
    
    .table td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .table tr:hover {
        background-color: #f8fafc;
    }
    
    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .badge {
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
    }
    
    /* Custom button disabled states */
    .btn-success.disabled {
        background-color: #4ade80 !important;
        border-color: #4ade80 !important;
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .btn-danger.disabled {
        background-color: #f87171 !important;
        border-color: #f87171 !important;
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Invoice Actions */
    .invoice-actions {
        border-top: 1px dashed #dee2e6;
        padding-top: 10px;
        margin-top: 10px;
    }
    
    .invoice-form .input-group {
        width: 100%;
    }
    
    .invoice-form .form-control-sm {
        border-right: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    .invoice-form .btn {
        border-left: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    .invoice-info {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 8px;
        margin-top: 8px;
        font-size: 12px;
    }
    
    /* Modal custom styles */
    .modal-lg {
        max-width: 800px;
    }
    
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .order-detail-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #3b82f6;
    }
    
    .order-detail-title {
        color: #3b82f6;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        color: #64748b;
        font-weight: 500;
    }
    
    .detail-value {
        color: #1e293b;
        font-weight: 600;
    }
    
    .payment-status-badge {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 20px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .input-group {
            width: 100% !important;
            margin-top: 1rem;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table td, .table th {
            white-space: nowrap;
            min-width: 150px;
        }
        
        .btn-group {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .btn-group form {
            width: 100%;
        }
        
        .btn-group .btn {
            width: 100%;
        }
        
        .invoice-actions {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .invoice-form .input-group {
            flex-direction: column;
        }
        
        .invoice-form .form-control-sm {
            border-right: 1px solid #ced4da;
            border-radius: 0.25rem 0.25rem 0 0;
            margin-bottom: -1px;
        }
        
        .invoice-form .btn {
            border-left: 1px solid #ced4da;
            border-radius: 0 0 0.25rem 0.25rem;
            width: 100%;
        }
        
        .invoice-actions .d-flex.flex-column {
            gap: 5px;
        }
        
        .modal-lg {
            max-width: 95%;
        }
    }

    /* Add-on Service Modal Styles */
    .step-container {
        border-left: 3px solid #3b82f6;
        padding-left: 15px;
        margin-bottom: 20px;
    }
    
    .step-title {
        color: #3b82f6;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .step-title::before {
        content: "•";
        color: #3b82f6;
        font-weight: bold;
        font-size: 1.5rem;
        margin-right: 8px;
        vertical-align: middle;
    }
    
    .calculation-table td {
        padding: 8px 0;
    }
    
    .modal-lg {
        max-width: 800px;
    }
    
    .price-breakdown {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .price-row:last-child {
        border-bottom: none;
    }
    
    .price-label {
        color: #64748b;
        font-weight: 500;
    }
    
    .price-value {
        color: #1e293b;
        font-weight: 600;
    }
    
    .total-row {
        background: #e2e8f0;
        padding: 10px 15px;
        border-radius: 6px;
        margin-top: 10px;
    }
    
    .gst-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        let paymentTable = $('#clientsTable').DataTable();

        // ----- POPULATE CLIENT DROPDOWN FROM TABLE DATA (NO EXTRA QUERY) -----
        let clientSet = new Set();
        paymentTable.column(1).data().each(function(value) {
            let text = $('<div>').html(value).text().trim();
            if (text) clientSet.add(text);
        });
        clientSet.forEach(function(client) {
            $('#clientFilter').append(`<option value="${client.replace(/"/g, '&quot;')}">${client}</option>`);
        });

        // ----- STATUS FILTER (fixed version) -----
        $('.filter-btn').on('click', function() {
            let status = $(this).data('status');

            // Update button styling
            $('.filter-btn').removeClass('btn-primary')
                            .addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary')
                   .addClass('btn-primary');

            // Clear all column searches first
            paymentTable.columns().search('');

            if (status === 'all') {
                paymentTable.draw();
            } 
            else if (status === 'invoice_pending') {
                // Filter on INVOICE STATUS column (index 7)
                paymentTable.column(7).search('INVOICE PENDING').draw();
            } 
            else {
                let searchTerm = '';
                if (status === 'submitted') searchTerm = 'PENDING';
                else if (status === 'approved') searchTerm = 'APPROVED';
                else if (status === 'rejected') searchTerm = 'REJECTED';

                // Filter on PAYMENT STATUS column (index 5)
                paymentTable.column(5).search(searchTerm).draw();
            }
        });

        // ----- DATE RANGE FILTER (custom filter) -----
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            let from = $('#fromDate').val();
            let to = $('#toDate').val();

            let row = paymentTable.row(dataIndex).node();
            let dateStr = $(row).find('td:eq(4)').data('date');

            if (!dateStr) return true;

            let rowDate = new Date(dateStr);

            if (from) {
                let fromDate = new Date(from);
                if (rowDate < fromDate) return false;
            }
            if (to) {
                let toDate = new Date(to);
                toDate.setHours(23, 59, 59, 999);
                if (rowDate > toDate) return false;
            }
            return true;
        });

        $('#filterDate').on('click', function() {
            paymentTable.draw();
        });

        $('#resetDate').on('click', function() {
            $('#fromDate').val('');
            $('#toDate').val('');
            $('#clientFilter').val('');
            paymentTable.search('').columns().search('').draw();
        });

        $('#clientFilter').on('change', function() {
            let value = $(this).val();
            paymentTable.column(1).search(value).draw();
        });

        // ----- DYNAMIC FILTERED TOTAL AMOUNT -----
        function updateFilteredTotal() {
            let total = 0;
            paymentTable.rows({ search: 'applied' }).every(function() {
                let row = this.node();
                let amountHtml = $(row).find('td:eq(3)').html();
                let match = amountHtml.match(/₹([\d,]+\.?\d*)/);
                if (match && match[1]) {
                    let amount = parseFloat(match[1].replace(/,/g, ''));
                    if (!isNaN(amount)) total += amount;
                }
            });
            $('#filteredTotal').text('₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

        paymentTable.on('draw', function() {
            updateFilteredTotal();
        });
        updateFilteredTotal();

        // ----- Rest of your existing JS (alerts, modals, etc.) -----
        $('form').on('submit', function(e) {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            const isDisabled = submitBtn.prop('disabled');
            
            if (!isDisabled) {
                submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Processing...');
            }
            
            setTimeout(function() {
                if (submitBtn.prop('disabled')) {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            }, 3000);
        });

        @if(session('success'))
        showAlert('success', '{{ session('success') }}');
        @endif

        @if(session('error'))
        showAlert('danger', '{{ session('error') }}');
        @endif

        function showAlert(type, message) {
            const alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            $('.header').after(alert);
            setTimeout(() => { $('.alert').alert('close'); }, 5000);
        }

        // View Details Modal
        $(document).on('click', '.view-details-btn', function() {
            const orderId = $(this).data('order-id');
            const orderCode = $(this).data('order-code');
            $('#modalOrderId').text(orderCode);
            const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            modal.show();
            loadOrderDetails(orderId);
        });

        function loadOrderDetails(orderId) {
            $.ajax({
                url: '{{ route("admin.orders.details", ":orderId") }}'.replace(':orderId', orderId),
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#orderDetailsContent').html(`
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading order details...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    if (response.success && response.order) {
                        displayOrderDetails(response.order);
                    } else {
                        $('#orderDetailsContent').html(`<div class="alert alert-danger">Failed to load order details.</div>`);
                    }
                },
                error: function() {
                    $('#orderDetailsContent').html(`<div class="alert alert-danger">Error loading order details.</div>`);
                }
            });
        }

        function displayOrderDetails(order) {
            const latestPayment = order.payments ? order.payments.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0] : null;
            const client = order.client || {};
            const marketingStaff = order.marketing_staff || {};
            let html = `<div class="order-details-container">
                <div class="order-detail-section"><h6 class="order-detail-title"><i class="bi bi-info-circle"></i> Order Information</h6>
                <div class="detail-row"><span class="detail-label">Order ID:</span><span class="detail-value">${order.order_id}</span></div>
                <div class="detail-row"><span class="detail-label">Order Status:</span><span class="detail-value">${getOrderStatusBadge(order.order_status)}</span></div>
                <div class="detail-row"><span class="detail-label">Created:</span><span class="detail-value">${formatDate(order.created_at)}</span></div>
                <div class="detail-row"><span class="detail-label">Base Price:</span><span class="detail-value">₹${formatAmount(order.base_price || 0)}</span></div>
                <div class="detail-row"><span class="detail-label">Discount:</span><span class="detail-value">₹${formatAmount(order.discount_amount || 0)}</span></div></div>
                <div class="order-detail-section"><h6 class="order-detail-title"><i class="bi bi-receipt"></i> Invoice Information</h6>
                <div class="detail-row"><span class="detail-label">Invoice Generated:</span><span class="detail-value">${order.invoice_generated ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}</span></div>`;
            if (order.invoice_generated) {
                html += `<div class="detail-row"><span class="detail-label">Government Fee:</span><span class="detail-value">₹${formatAmount(order.final_govt_fee || 0)}</span></div>
                        <div class="detail-row"><span class="detail-label">Final Amount:</span><span class="detail-value text-primary">₹${formatAmount(order.final_amount || 0)}</span></div>
                        <div class="detail-row"><span class="detail-label">Invoice Version:</span><span class="detail-value">${getInvoiceVersionBadge(order.invoice_path)}</span></div>`;
            } else {
                html += `<div class="detail-row"><span class="detail-label">Estimated Govt Fee:</span><span class="detail-value">₹${formatAmount(order.estimated_govt_fee || 0)}</span></div>`;
            }
            html += `</div>
                <div class="order-detail-section"><h6 class="order-detail-title"><i class="bi bi-person"></i> Client Information</h6>
                <div class="detail-row"><span class="detail-label">Name:</span><span class="detail-value">${client.name || 'N/A'}</span></div>
                <div class="detail-row"><span class="detail-label">Email:</span><span class="detail-value">${client.email || 'N/A'}</span></div>
                <div class="detail-row"><span class="detail-label">Phone:</span><span class="detail-value">${client.phone || 'N/A'}</span></div>
                <div class="detail-row"><span class="detail-label">Client Code:</span><span class="detail-value">${client.client_code || 'N/A'}</span></div></div>
                <div class="order-detail-section"><h6 class="order-detail-title"><i class="bi bi-briefcase"></i> Marketing Staff</h6>
                <div class="detail-row"><span class="detail-label">Name:</span><span class="detail-value">${marketingStaff.name || 'N/A'}</span></div>
                <div class="detail-row"><span class="detail-label">Email:</span><span class="detail-value">${marketingStaff.email || 'N/A'}</span></div>
                <div class="detail-row"><span class="detail-label">Phone:</span><span class="detail-value">${marketingStaff.phone || 'N/A'}</span></div>
                <div class="detail-row"><span class="detail-label">Staff Code:</span><span class="detail-value">${marketingStaff.staff_code || 'N/A'}</span></div></div>`;
            if (latestPayment) {
                html += `<div class="order-detail-section"><h6 class="order-detail-title"><i class="bi bi-credit-card"></i> Payment Details</h6>
                <div class="detail-row"><span class="detail-label">Amount:</span><span class="detail-value">₹${formatAmount(latestPayment.amount)}</span></div>
                <div class="detail-row"><span class="detail-label">Method:</span><span class="detail-value">${latestPayment.payment_method || 'N/A'}</span></div>
                <div class="detail-row"><span class="detail-label">Status:</span><span class="detail-value">${getPaymentStatusBadge(latestPayment.status)}</span></div>
                <div class="detail-row"><span class="detail-label">Submitted:</span><span class="detail-value">${formatDate(latestPayment.created_at)}</span></div>
                ${latestPayment.approved_at ? `<div class="detail-row"><span class="detail-label">Approved:</span><span class="detail-value">${formatDate(latestPayment.approved_at)}</span></div>` : ''}
                ${latestPayment.rejected_at ? `<div class="detail-row"><span class="detail-label">Rejected:</span><span class="detail-value">${formatDate(latestPayment.rejected_at)}</span></div>` : ''}
                ${latestPayment.transaction_id ? `<div class="detail-row"><span class="detail-label">Transaction ID:</span><span class="detail-value">${latestPayment.transaction_id}</span></div>` : ''}</div>`;
            }
            if (order.items && order.items.length > 0) {
                html += `<div class="order-detail-section"><h6 class="order-detail-title"><i class="bi bi-list-check"></i> Order Items</h6>`;
                order.items.forEach((item) => {
                    const service = item.service || {};
                    html += `<div class="detail-row" style="border-bottom:1px dashed #e2e8f0;"><div class="w-100"><div class="d-flex justify-content-between"><strong>${service.service_name || 'Service'}</strong><span class="text-primary">₹${formatAmount(item.price || 0)}</span></div><div class="text-muted small mt-1">${item.description || 'No description'}${service.service_code ? `<br><small>Code: ${service.service_code}</small>` : ''}</div></div></div>`;
                });
                html += `</div>`;
            }
            html += `</div>`;
            $('#orderDetailsContent').html(html);
        }

        function getOrderStatusBadge(status) {
            if (status === 'PAID') return '<span class="badge bg-success">PAID</span>';
            if (status === 'PENDING_PAYMENT') return '<span class="badge bg-danger">PENDING PAYMENT</span>';
            if (status === 'PAYMENT_SUBMITTED') return '<span class="badge bg-warning text-dark">PAYMENT SUBMITTED</span>';
            return `<span class="badge bg-secondary">${status}</span>`;
        }

        function getPaymentStatusBadge(status) {
            if (status === 'approved') return '<span class="badge bg-success">APPROVED</span>';
            if (status === 'rejected') return '<span class="badge bg-danger">REJECTED</span>';
            if (status === 'submitted') return '<span class="badge bg-warning text-dark">PENDING</span>';
            return `<span class="badge bg-secondary">${status}</span>`;
        }

        function getInvoiceVersionBadge(invoicePath) {
            if (!invoicePath) return '<span class="badge bg-secondary">N/A</span>';
            if (invoicePath.includes('-R')) {
                const revision = invoicePath.match(/-R(\d+)/);
                return `<span class="badge bg-warning">Revised v${revision ? revision[1] : '1'}</span>`;
            }
            return '<span class="badge bg-primary">Original</span>';
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }

        function formatAmount(amount) {
            return parseFloat(amount).toFixed(2);
        }

        $('#printOrderBtn').on('click', function() { window.print(); });

        window.validateInvoiceForm = function(form) {
            const govtFee = $(form).find('input[name="final_govt_fee"]').val();
            const btn = $(form).find('button[type="submit"]');
            if (govtFee < 0) {
                Swal.fire({ icon: 'error', title: 'Invalid Govt Fee', text: 'Government fee cannot be negative.' });
                return false;
            }
            const orderId = $(form).closest('tr').find('.view-details-btn').data('order-code');
            Swal.fire({
                title: 'Generate Invoice?',
                html: `Generate invoice for Order: <strong>${orderId}</strong><br><strong>Govt Fee:</strong> ₹${govtFee}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Generate Invoice',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Generating...');
                    form.submit();
                }
            });
            return false;
        };

        $('[id^="reviseInvoiceForm"]').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const govtFee = $(form).find('input[name="final_govt_fee"]').val();
            const reason = $(form).find('textarea[name="revision_reason"]').val();
            const btn = $(form).find('button[type="submit"]');
            Swal.fire({
                title: 'Confirm Invoice Revision',
                html: `<div class="text-start"><p><strong>New Govt Fee:</strong> ₹${govtFee}</p><p><strong>Reason:</strong> ${reason}</p><div class="alert alert-warning small"><i class="bi bi-exclamation-triangle"></i> Old invoice will be archived. This action is permanently logged.</div></div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Revise Invoice',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Revising...');
                    form.submit();
                }
            });
        });

        window.confirmApprove = function(event, orderId) {
            event.preventDefault();
            const form = event.target;
            const button = form.querySelector('button[type="submit"]');
            if (button.disabled) return false;
            Swal.fire({
                title: 'Approve Payment?',
                html: `Are you sure you want to <strong>APPROVE</strong> payment for order:<br><strong class="text-primary">${orderId}</strong>?<br><br><small class="text-muted">Note: You can change the status anytime.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Yes, Approve!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) event.target.submit();
            });
        };

        window.confirmReject = function(event, orderId) {
            event.preventDefault();
            const form = event.target;
            const button = form.querySelector('button[type="submit"]');
            if (button.disabled) return false;
            Swal.fire({
                title: 'Reject Payment?',
                html: `Are you sure you want to <strong>REJECT</strong> payment for order:<br><strong class="text-primary">${orderId}</strong>?<br><br><small class="text-muted">Note: You can change the status anytime.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Yes, Reject!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) event.target.submit();
            });
        };
    });
</script>

<!-- Add-on Service Modal Script (unchanged) -->
<script>
$(document).ready(function() {
    let currentStep = 1;
    let selectedServiceData = null;
    let currentOrderTotal = 0;

    $(document).on('click', '.addon-btn', function() {
        const orderId = $(this).data('order-id');
        const orderCode = $(this).data('order-code');
        const clientName = $(this).data('client-name');
        const orderTotal = $(this).data('order-total');
        
        $('#modal_order_id').val(orderId);
        $('#modal_order_code').text(orderCode);
        $('#modal_client_name').text(clientName);
        $('#modal_order_total').text(parseFloat(orderTotal).toFixed(2));
        currentOrderTotal = parseFloat(orderTotal);
        resetToStep1();
        $('#addonServiceModal').modal('show');
    });

    $('#nextStepBtn').on('click', function() {
        if (currentStep === 1) {
            const serviceId = $('#service_id').val();
            if (!serviceId) {
                Swal.fire({ icon: 'error', title: 'Selection Required', text: 'Please select a service.' });
                return;
            }
            const selectedOption = $('#service_id option:selected');
            selectedServiceData = {
                id: serviceId,
                name: selectedOption.text().split(' - ')[0],
                price: parseFloat(selectedOption.data('price')),
                govtFee: parseFloat(selectedOption.data('govt-fee') || 0),
                isTaxable: selectedOption.data('taxable') === 1,
                taxRate: parseFloat(selectedOption.data('tax') || 18)
            };
            $('#step2').show();
            currentStep = 2;
            $('#prevStepBtn').show();
            $('#nextStepBtn').hide();
            $('#submitAddonBtn').show();
            calculateAddonPrice();
        }
    });

    $('#prevStepBtn').on('click', function() {
        if (currentStep === 2) {
            $('#step2').hide();
            currentStep = 1;
            $('#prevStepBtn').hide();
            $('#nextStepBtn').show();
            $('#submitAddonBtn').hide();
        }
    });

    $('#service_id, #quantity, #discount_percent').on('change keyup', function() {
        if (currentStep === 2) calculateAddonPrice();
    });

    function calculateAddonPrice() {
        if (!selectedServiceData) return;
        const quantity = parseInt($('#quantity').val()) || 1;
        const discountPercent = parseFloat($('#discount_percent').val()) || 0;
        const basePrice = selectedServiceData.price;
        const subtotal = basePrice * quantity;
        const discountAmount = (subtotal * discountPercent) / 100;
        const taxableAmount = subtotal - discountAmount;
        const gstRate = selectedServiceData.isTaxable ? selectedServiceData.taxRate : 0;
        const cgstRate = gstRate / 2;
        const sgstRate = gstRate / 2;
        const cgst = (taxableAmount * cgstRate) / 100;
        const sgst = (taxableAmount * sgstRate) / 100;
        const govtFee = selectedServiceData.govtFee * quantity;
        const addonTotal = taxableAmount + cgst + sgst + govtFee;
        const newTotal = currentOrderTotal + addonTotal;
        $('#displayBasePrice').text(basePrice.toFixed(2));
        $('#displayQuantity').text(quantity);
        $('#displaySubtotal').text(subtotal.toFixed(2));
        $('#displayDiscount').text(discountAmount.toFixed(2));
        $('#displayTaxableAmount').text(taxableAmount.toFixed(2));
        $('#cgstRate').text(cgstRate);
        $('#sgstRate').text(sgstRate);
        $('#displayCGST').text(cgst.toFixed(2));
        $('#displaySGST').text(sgst.toFixed(2));
        $('#displayGovtFee').text(govtFee.toFixed(2));
        $('#displayAddonTotal').text(addonTotal.toFixed(2));
        $('#displayNewTotal').text(newTotal.toFixed(2));
    }

    function resetToStep1() {
        $('#addonServiceForm')[0].reset();
        $('#service_id').val('');
        $('#quantity').val('1');
        $('#discount_percent').val('0');
        $('#remarks').val('');
        $('#step2').hide();
        $('#prevStepBtn, #submitAddonBtn').hide();
        $('#nextStepBtn').show();
        currentStep = 1;
        selectedServiceData = null;
    }

    $('#addonServiceForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            order_id: $('#modal_order_id').val(),
            service_id: $('#service_id').val(),
            quantity: $('#quantity').val(),
            discount_percent: $('#discount_percent').val() || 0,
            remarks: $('#remarks').val(),
            _token: $('input[name="_token"]').val()
        };
        if (!formData.order_id) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Order ID is missing. Please try again.' });
            return;
        }
        const submitBtn = $('#submitAddonBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#addonServiceModal').modal('hide');
                    $('#addonSuccessModal').modal('show');
                    $('#viewUpdatedInvoiceBtn').data('invoice-url', response.invoice_url);
                    setTimeout(function() { location.reload(); }, 3000);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to add add-on service.' });
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Error', text: errorMessage });
            },
            complete: function() { submitBtn.prop('disabled', false).html(originalText); }
        });
    });

    $('#viewUpdatedInvoiceBtn').on('click', function() {
        const invoiceUrl = $(this).data('invoice-url');
        if (invoiceUrl) window.open(invoiceUrl, '_blank');
        $('#addonSuccessModal').modal('hide');
    });

    $('#addonServiceModal').on('hidden.bs.modal', function() { resetToStep1(); });
});
</script>

<!-- Edit Invoice Script (unchanged) -->
<script>
$(document).ready(function() {
    $('.govt-fee-input').on('input', function() {
        const orderId = $(this).closest('.modal').attr('id').replace('editInvoiceModal', '');
        calculateNewTotals(orderId);
    });

    function calculateNewTotals(orderId) {
        let totalGovtFee = 0;
        $(`#editInvoiceModal${orderId} .govt-fee-input`).each(function() {
            totalGovtFee += parseFloat($(this).val()) || 0;
        });
        const serviceFee = parseFloat($(`#totalServiceFee${orderId}`).text().replace(/,/g, '')) || 0;
        const discount = parseFloat($(`#totalDiscount${orderId}`).text().replace(/,/g, '')) || 0;
        const taxableAmount = serviceFee - discount;
        const gstRate = {{ config('app.gst_percentage', 18) }};
        const cgstRate = gstRate / 2;
        const sgstRate = gstRate / 2;
        const cgst = (taxableAmount * cgstRate) / 100;
        const sgst = (taxableAmount * sgstRate) / 100;
        const actualTotal = taxableAmount + cgst + sgst + totalGovtFee;
        const roundedTotal = Math.round(actualTotal);
        $(`#totalGovtFee${orderId}`).text(totalGovtFee.toFixed(2));
        $(`#cgstAmount${orderId}`).text(cgst.toFixed(2));
        $(`#sgstAmount${orderId}`).text(sgst.toFixed(2));
        $(`#newGrandTotal${orderId}`).text(roundedTotal.toFixed(2));
    }

    $('.edit-invoice-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        Swal.fire({
            title: 'Update Invoice?',
            html: 'Are you sure you want to update the government fees? A revised invoice will be generated.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Updating...');
                form[0].submit();
            }
        });
    });
});
</script>

<!-- Payment Proof Modal Script (unchanged) -->
<script>
$(document).ready(function() {
    $(document).on('click', '.view-proof-btn', function() {
        const proofUrl = $(this).data('proof-url');
        const amount = $(this).data('payment-amount');
        const method = $(this).data('payment-method');
        const paymentDate = $(this).data('payment-date');
        const transactionId = $(this).data('transaction-id');
        $('#proofAmount').text(parseFloat(amount).toFixed(2));
        $('#proofMethod').text(method);
        $('#proofDate').text(paymentDate ? new Date(paymentDate).toLocaleDateString() : 'N/A');
        $('#proofTransactionId').text(transactionId || 'N/A');
        const fileExtension = proofUrl.split('.').pop().toLowerCase();
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        if (imageExtensions.includes(fileExtension)) {
            $('#proofImage').attr('src', proofUrl).show();
            $('#proofPdf').hide();
        } else {
            $('#proofPdf').attr('src', proofUrl).show();
            $('#proofImage').hide();
        }
        $('#downloadProofBtn').attr('href', proofUrl);
        $('#paymentProofModal').modal('show');
    });
});
</script>
@endpush