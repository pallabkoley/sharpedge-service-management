@extends('layouts.client')

@section('title', 'My Invoices - SharpEdge Client')

@section('page-title', 'My Invoices')
@section('page-subtitle', 'View and download all your generated invoices.')

@section('content')
    {{-- Summary Cards --}}
    @php
        $totalInvoices = $orders->count();
        $paidInvoices = $orders->where('payment_status', 'approved')->count();
        $pendingInvoices = $orders->where('payment_status', 'pending')->count();
        $totalAmount = $orders->sum('final_amount');
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Total Invoices</h6>
                    <div class="card-icon bg-primary bg-opacity-10" style="background-color: rgba(29,78,216,0.1);">
                        <i class="bi bi-receipt text-primary"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $totalInvoices }}</h3>
                <span class="text-muted small">All time</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Paid</h6>
                    <div class="card-icon bg-success bg-opacity-10" style="background-color: rgba(25,135,84,0.1);">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $paidInvoices }}</h3>
                <span class="text-muted small">Approved payments</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Pending</h6>
                    <div class="card-icon bg-warning bg-opacity-10" style="background-color: rgba(255,193,7,0.1);">
                        <i class="bi bi-hourglass-split text-warning"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $pendingInvoices }}</h3>
                <span class="text-muted small">Awaiting payment</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Total Amount</h6>
                    <div class="card-icon bg-info bg-opacity-10" style="background-color: rgba(13,202,240,0.1);">
                        <i class="bi bi-currency-rupee text-info"></i>
                    </div>
                </div>
                <h3 class="card-value">₹{{ number_format($totalAmount) }}</h3>
                <span class="text-muted small">Sum of all invoices</span>
            </div>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="section-card">
        <h4 class="section-title">All Invoices</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable" id="invoicesTable">
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Order ID</th>
                        <th>Service Name</th>
                        <th>Invoice Date</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="fw-bold">
                                @if($order->invoice_number)
                                    {{ $order->invoice_number }}
                                @else
                                    INV-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                @endif
                            </td>
                            <td>#{{ $order->id }}</td>
                            <td>{{ optional($order->service)->service_name ?? 'Service removed' }}</td>

                            <td>{{ $order->invoice_generated_at ? \Carbon\Carbon::parse($order->invoice_generated_at)->format('d M Y') : $order->updated_at->format('d M Y') }}</td>
                            <td>₹{{ number_format($order->final_amount ?? 0) }}</td>
                            <td>
                                @php
                                    $paymentStatus = $order->payment_status ?? 'pending';
                                    $paymentClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'submitted' => 'bg-primary text-white',
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                    ];
                                    $paymentLabels = [
                                        'pending' => 'Pending',
                                        'submitted' => 'Submitted',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                    ];
                                @endphp
                                <span class="badge {{ $paymentClasses[$paymentStatus] ?? 'bg-secondary' }} px-3 py-2 rounded-pill">
                                    {{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('client.invoices.preview', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Preview Invoice">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('client.invoices.download', $order->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3" title="Download Invoice">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <!--<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-invoice-details" data-id="{{ $order->id }}" title="Invoice Details">-->
                                    <!--    <i class="bi bi-info-circle"></i>-->
                                    <!--</button>-->
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Invoice Details Modal --}}
    <div class="modal fade" id="invoiceDetailsModal" tabindex="-1" aria-labelledby="invoiceDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title" id="invoiceDetailsModalLabel">
                        <i class="bi bi-receipt me-2"></i> Invoice Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="invoiceDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading invoice details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Destroy any existing DataTable instance on this table (prevents double init)
    if ($.fn.DataTable.isDataTable('#invoicesTable')) {
        $('#invoicesTable').DataTable().destroy();
    }
    
    // Initialize DataTable with custom language for empty table
    $('#invoicesTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        responsive: true,
        language: {
            emptyTable: "No invoices found.",
            zeroRecords: "No matching invoices found.",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)"
        }
    });
    
    // Invoice Details Modal AJAX
    $(document).on('click', '.btn-invoice-details', function(e) {
        e.preventDefault();
        
        var orderId = $(this).data('id');
        var modal = $('#invoiceDetailsModal');
        var contentDiv = $('#invoiceDetailsContent');
        
        modal.modal('show');
        contentDiv.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading invoice details...</p>
            </div>
        `);
        
        $.ajax({
            url: '/client/invoices/' + orderId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    var order = response.data;
                    var html = buildInvoiceDetailsHTML(order);
                    contentDiv.html(html);
                } else {
                    contentDiv.html('<div class="alert alert-danger">Invoice not found.</div>');
                }
            },
            error: function() {
                contentDiv.html('<div class="alert alert-danger">Failed to load invoice details. Please try again.</div>');
            }
        });
    });
    
    function buildInvoiceDetailsHTML(order) {
        var invoiceDate = order.invoice_generated_at 
            ? new Date(order.invoice_generated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
            : (order.updated_at ? new Date(order.updated_at).toLocaleDateString() : '—');
        
        var invoiceNumber = order.invoice_number || 'INV-' + String(order.id).padStart(6, '0');
        
        var paymentStatus = order.payment_status || 'pending';
        var paymentBadge = getBadgeHTML(paymentStatus, 'payment');
        
        var downloadUrl = '/client/invoices/download/' + order.id;
        var previewUrl = '/client/invoices/preview/' + order.id;
        
        return `
            <div class="invoice-details">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Invoice Number:</strong> ${invoiceNumber}</p>
                        <p class="mb-1"><strong>Order ID:</strong> #${order.id}</p>
                        <p class="mb-1"><strong>Service:</strong> ${order.service?.name || '—'}</p>
                        <p class="mb-1"><strong>Invoice Date:</strong> ${invoiceDate}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Amount:</strong> ₹${order.final_amount ? order.final_amount.toLocaleString() : '0'}</p>
                        <p class="mb-1"><strong>Payment Status:</strong> ${paymentBadge}</p>
                    </div>
                </div>
                
                <div class="mt-4 d-flex gap-3">
                    <a href="${previewUrl}" target="_blank" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-eye me-1"></i> Preview
                    </a>
                    <a href="${downloadUrl}" class="btn btn-outline-success rounded-pill px-4">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                </div>
            </div>
        `;
    }
    
    function getBadgeHTML(status, type) {
        var badgeClass = '';
        var label = status;
        
        if (type === 'payment') {
            var paymentClasses = {
                'pending': 'bg-warning text-dark',
                'submitted': 'bg-primary text-white',
                'approved': 'bg-success text-white',
                'rejected': 'bg-danger text-white'
            };
            var paymentLabels = {
                'pending': 'Pending',
                'submitted': 'Submitted',
                'approved': 'Approved',
                'rejected': 'Rejected'
            };
            badgeClass = paymentClasses[status] || 'bg-secondary';
            label = paymentLabels[status] || status;
        }
        
        return `<span class="badge ${badgeClass} px-3 py-2 rounded-pill">${label}</span>`;
    }
});
</script>
@endpush