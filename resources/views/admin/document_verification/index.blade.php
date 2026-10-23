@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Document Verification</h2>
                <p class="text-muted mb-0">Review and verify documents uploaded by marketing staff</p>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING ORDERS</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $orders->total() }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Orders with pending documents</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING DOCUMENTS</h6>
                    <div class="card-icon bg-info">
                        <i class="bi bi-files"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $pendingDocumentsCount }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Documents awaiting verification</span>
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
                    <span>With pending documents</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">DOCUMENT TYPES</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $documentTypesCount }}</h3>
                <div class="card-change up">
                    <i class="bi bi-info-circle me-1"></i>
                    <span>Different document types</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders with Document Submissions Table -->
    <div class="mt-4">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">
                    @if($status == 'pending')
                    Pending Document Verification
                    @elseif($status == 'verified')
                    Verified Documents
                    @elseif($status == 'rejected')
                    Rejected Documents
                    @else
                    All Document Submissions
                    @endif
                </h4>
                <div class="d-flex align-items-center gap-3">
                    <!--<span class="text-muted">Showing {{ $orders->count() }} of {{ $orders->total() }} orders</span>-->
                    
                    <!-- Search Bar -->
                    <form method="GET" action="{{ route('admin.document-verification.index') }}" class="d-flex">
                        <!--<input type="hidden" name="status" value="{{ $status }}">-->
                        <!--<div class="input-group" style="width: 300px;">-->
                        <!--    <span class="input-group-text"><i class="bi bi-search"></i></span>-->
                        <!--    <input type="text" -->
                        <!--           name="search" -->
                        <!--           class="form-control" -->
                        <!--           placeholder="Search orders, clients..."-->
                        <!--           value="{{ request('search') }}">-->
                        <!--    @if(request('search'))-->
                        <!--    <button type="button" -->
                        <!--            class="btn btn-outline-secondary" -->
                        <!--            onclick="clearSearch()">-->
                        <!--        <i class="bi bi-x"></i>-->
                        <!--    </button>-->
                        <!--    @endif-->
                        <!--</div>-->
                    </form>
                    
                    <!--<button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">-->
                    <!--    <i class="bi bi-arrow-clockwise"></i> Refresh-->
                    <!--</button>-->
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="d-flex gap-2 mb-4">
                <a href="{{ route('admin.document-verification.index', array_merge(['status' => 'all'], request('search') ? ['search' => request('search')] : [])) }}" 
                   class="btn btn-sm {{ $status == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                    All Orders
                </a>
                <a href="{{ route('admin.document-verification.index', array_merge(['status' => 'pending'], request('search') ? ['search' => request('search')] : [])) }}" 
                   class="btn btn-sm {{ $status == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    Pending
                </a>
                <a href="{{ route('admin.document-verification.index', array_merge(['status' => 'verified'], request('search') ? ['search' => request('search')] : [])) }}" 
                   class="btn btn-sm {{ $status == 'verified' ? 'btn-success' : 'btn-outline-success' }}">
                    Verified
                </a>
                <a href="{{ route('admin.document-verification.index', array_merge(['status' => 'rejected'], request('search') ? ['search' => request('search')] : [])) }}" 
                   class="btn btn-sm {{ $status == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    Rejected
                </a>
            </div>

            <!-- Info Alert -->
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="bi bi-info-circle me-2 fs-5"></i>
                <div>
                    @if($status == 'pending')
                    <strong>Pending Verification:</strong> These orders have documents awaiting your review.
                    <span class="ms-2 text-warning">
                        <i class="bi bi-exclamation-triangle"></i> Rejection requires remarks.
                    </span>
                    @elseif($status == 'verified')
                    <strong>Verified Documents:</strong> These documents have been approved.
                    @elseif($status == 'rejected')
                    <strong>Rejected Documents:</strong> These documents have been rejected with remarks.
                    @else
                    <strong>All Document Submissions:</strong> View all document submissions across all statuses.
                    @endif
                </div>
            </div>

            @if($orders->count() > 0)
            <div class="table-responsive">
               <table class="table table-hover datatable" id="clientsTable">        
                    <thead>
                        <tr>
                            <th>ORDER ID</th>
                            <th>CLIENT</th>
                            <th>SERVICE</th>
                            <th>UPLOADED BY</th>
                            <th>MARKETING STAFF</th>
                            <th>
                                @if($status == 'pending')
                                PENDING DOCUMENTS
                                @elseif($status == 'verified')
                                VERIFIED DOCUMENTS
                                @elseif($status == 'rejected')
                                REJECTED DOCUMENTS
                                @else
                                DOCUMENTS
                                @endif
                            </th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        @php
                            $serviceName = $order->items->first()?->service?->name ?? 'N/A';
                            $serviceCode = $order->items->first()?->service?->service_code ?? '';
                            
                            // Get documents for this order based on current filter
                            $filteredDocuments = $order->documents ?? collect();
                            $documentCount = $filteredDocuments->count();
                            
                            // Get latest uploader for display
                            $latestUploader = null;
                            if($filteredDocuments->isNotEmpty()) {
                                $latestDoc = $filteredDocuments->sortByDesc('uploaded_at')->first();
                                $latestUploader = $latestDoc->uploadedBy ?? null;
                            }
                            
                            // Get pending count for badge
                            $pendingCount = $order->documents->where('status', 'UPLOADED')->count();
                        @endphp
                        <tr data-order-id="{{ $order->id }}" class="order-row">
                            <td class="fw-bold">
                                <span class="order-id-badge">{{ $order->order_id }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $order->client->name ?? 'N/A' }}</strong>
                                    <small class="text-muted">{{ $order->client->client_code ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $serviceName }}</strong>
                                    <small class="text-muted">{{ $serviceCode }}</small>
                                </div>
                            </td>
                            <td>
                                @if($latestUploader)
                                <div class="d-flex flex-column">
                                    <strong>{{ $latestUploader->name }}</strong>
                                    <small class="text-muted">{{ $latestUploader->email }}</small>
                                </div>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $order->marketingStaff->name ?? 'N/A' }}</strong>
                                    <small class="text-muted">{{ $order->marketingStaff->email ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                @if($status == 'pending')
                                <span class="badge bg-warning">
                                    <i class="bi bi-file-earmark me-1"></i> {{ $documentCount }}
                                </span>
                                @elseif($status == 'verified')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> {{ $documentCount }}
                                </span>
                                @elseif($status == 'rejected')
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i> {{ $documentCount }}
                                </span>
                                @else
                                <div class="d-flex flex-column">
                                    <span class="badge bg-warning mb-1">
                                        <i class="bi bi-clock"></i> {{ $pendingCount }} pending
                                    </span>
                                    <small class="text-muted">
                                        Total: {{ $order->documents->count() }}
                                    </small>
                                </div>
                                @endif
                            </td>
                            <td>
                                @if($status == 'verified')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> VERIFIED
                                </span>
                                @elseif($status == 'rejected')
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i> REJECTED
                                </span>
                                @elseif($pendingCount > 0)
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i> PENDING
                                </span>
                                @else
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> VERIFIED
                                </span>
                                @endif
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-primary view-order-btn"
                                        data-order-id="{{ $order->id }}"
                                        data-order-code="{{ $order->order_id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->

            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-4">
                    @if($status == 'pending')
                    <i class="bi bi-clock fs-1 text-muted"></i>
                    @elseif($status == 'verified')
                    <i class="bi bi-check-circle fs-1 text-muted"></i>
                    @elseif($status == 'rejected')
                    <i class="bi bi-x-circle fs-1 text-muted"></i>
                    @else
                    <i class="bi bi-file-check fs-1 text-muted"></i>
                    @endif
                </div>
                <h4 class="mb-3">
                    @if($status == 'pending')
                    No Pending Document Submissions
                    @elseif($status == 'verified')
                    No Verified Documents
                    @elseif($status == 'rejected')
                    No Rejected Documents
                    @else
                    No Document Submissions
                    @endif
                </h4>
                <p class="text-muted mb-4">
                    @if($status == 'pending')
                    No document submissions found for verification.
                    @elseif($status == 'verified')
                    No documents have been verified yet.
                    @elseif($status == 'rejected')
                    No documents have been rejected yet.
                    @else
                    No document submissions found.
                    @endif
                </p>
                <p class="text-muted small">
                    @if($status == 'pending')
                    Document uploads from marketing staff will appear here.
                    @else
                    Switch to a different tab to see documents.
                    @endif
                </p>
                @if($status != 'pending')
                <a href="{{ route('admin.document-verification.index', ['status' => 'pending']) }}" 
                   class="btn btn-primary">
                    <i class="bi bi-clock"></i> View Pending Documents
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Order Documents Verification Modal -->
    <div class="modal fade" id="orderDocumentsModal" tabindex="-1" aria-labelledby="orderDocumentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDocumentsModalLabel">
                        <i class="bi bi-files"></i> Document Verification: 
                        <span id="modalOrderId" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDocumentsContent">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading order documents...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
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
    .order-id-badge {
        background: rgba(59, 130, 246, 0.1); 
        color: #3b82f6; 
        padding: 4px 10px; 
        border-radius: 4px; 
        font-family: 'Courier New', monospace;
        font-size: 14px;
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
    
    .badge {
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .modal-xl {
        max-width: 1000px;
    }
    
    @media (max-width: 768px) {
        .header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .section-card .d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .section-card .input-group {
            width: 100% !important;
            margin-top: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Auto-submit search form when user types (with slight delay)
        let searchTimer;
        $('input[name="search"]').on('keyup', function() {
            clearTimeout(searchTimer);
            const searchForm = $(this).closest('form');
            searchTimer = setTimeout(() => {
                searchForm.submit();
            }, 500);
        });

        // Clear search function
        window.clearSearch = function() {
            const currentStatus = '{{ $status }}';
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            window.location.href = url.toString();
        }

        // View Order Documents Modal
     $(document).on('click', '.view-order-btn', function() {
            const orderId = $(this).data('order-id');
            const orderCode = $(this).data('order-code');
            
            $('#modalOrderId').text(orderCode);
            
            const modal = new bootstrap.Modal(document.getElementById('orderDocumentsModal'));
            modal.show();
            
            loadOrderDocuments(orderId);
        });

        // Load order documents via AJAX
        function loadOrderDocuments(orderId) {
            // Store orderId globally for bulk actions
            window.currentOrderId = orderId;

            $.ajax({
                url: '{{ route("admin.document-verification.load", ":id") }}'.replace(':id', orderId),
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#orderDocumentsContent').html(`
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading order documents...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    if (response.success && response.order) {
                        displayOrderDocuments(response.order, response.documents);
                    } else {
                        $('#orderDocumentsContent').html(`
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> Failed to load order documents.
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#orderDocumentsContent').html(`
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> Error loading order documents.
                        </div>
                    `);
                }
            });
        }

        // Display order documents in modal
        function displayOrderDocuments(order, documents) {
            const pendingDocuments = documents.filter(doc => doc.status === 'UPLOADED');
            const verifiedDocuments = documents.filter(doc => doc.status === 'VERIFIED');
            const rejectedDocuments = documents.filter(doc => doc.status === 'REJECTED');
            
            let html = `
                <!-- Order Information -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="bi bi-info-circle"></i> Order Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Order ID:</strong> ${order.order_id}</p>
                                <p><strong>Client:</strong> ${order.client_name}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Marketing Staff:</strong> ${order.marketing_staff}</p>
                                <p><strong>Service:</strong> ${order.service_name}</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-warning">${pendingDocuments.length} Pending</span>
                            <span class="badge bg-success ms-2">${verifiedDocuments.length} Verified</span>
                            <span class="badge bg-danger ms-2">${rejectedDocuments.length} Rejected</span>
                        </div>
                    </div>
                </div>
            `;

            
            if (pendingDocuments.length > 0) {
                html += `
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="bi bi-files"></i> Bulk Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-chat-left-text"></i> Common Remarks
                                        <span class="text-danger">*</span> <small class="text-muted">(Required for rejection)</small>
                                    </label>
                                    <textarea class="form-control" id="bulkRemarks" rows="2" placeholder="Enter remarks for all documents (optional for approval)"></textarea>
                                </div>
                                <div class="col-md-4 d-flex flex-column justify-content-end">
                                    <button type="button" class="btn btn-success mb-2" id="approveAllBtn">
                                        <i class="bi bi-check-all"></i> Approve All (${pendingDocuments.length})
                                    </button>
                                    <button type="button" class="btn btn-danger" id="rejectAllBtn">
                                        <i class="bi bi-x-octagon"></i> Reject All (${pendingDocuments.length})
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Pending Documents Section
            html += `
                <div class="mb-4">
                    <h5 class="mb-3 text-warning">
                        <i class="bi bi-clock"></i> Pending Verification (${pendingDocuments.length})
                    </h5>
                    
                    ${pendingDocuments.length > 0 ? pendingDocuments.map((document, index) => `
                        <div class="card mb-3 border-warning" data-document-id="${document.id}">
                            <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${document.document_name}</strong>
                                    <small class="text-muted"> - ${document.service_name}</small>
                                </div>
                                <span class="badge bg-warning">
                                    <i class="bi bi-upload"></i> UPLOADED
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    <i class="bi bi-person"></i> Uploaded by: ${document.uploaded_by} 
                                    <span class="ms-3">
                                        <i class="bi bi-calendar"></i> ${document.uploaded_at}
                                    </span>
                                </p>
                                
                                <h6 class="text-muted mb-2">
                                    <i class="bi bi-files"></i> Uploaded Files (${document.files.length})
                                </h6>
                                ${document.files.length > 0 ? document.files.map((file, fileIndex) => `
                                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                        <div>
                                            <i class="bi ${getFileIcon(file.mime_type)} me-2 text-primary"></i>
                                            <strong>${file.file_name}</strong>
                                            <div class="text-muted small">
                                                ${formatFileSize(file.file_size)} • ${file.mime_type}
                                            </div>
                                        </div>
                                        <div>
                                            <a href="${file.url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="${file.url}" download class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                `).join('') : `
                                    <div class="alert alert-warning py-2">
                                        <i class="bi bi-exclamation-triangle"></i> No files uploaded
                                    </div>
                                `}
                                
                                <div class="mt-4 pt-3 border-top">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="form-label small">
                                                <i class="bi bi-chat-left-text"></i> Remarks
                                                <span class="text-danger">*</span> <small class="text-muted">(Required for rejection)</small>
                                            </label>
                                            <textarea class="form-control" 
                                                      id="remarks-${document.id}" 
                                                      rows="2"
                                                      placeholder="Enter remarks for approval/rejection"
                                                      maxlength="1000"></textarea>
                                        </div>
                                        <div class="col-md-4 d-flex flex-column justify-content-end">
                                            <button type="button" 
                                                    class="btn btn-success btn-approve mb-2" 
                                                    data-document-id="${document.id}">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-danger btn-reject" 
                                                    data-document-id="${document.id}">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('') : `
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> No pending documents for verification.
                        </div>
                    `}
                </div>
            `;
            
            if (verifiedDocuments.length > 0) {
                html += `
                    <div class="mb-4">
                        <h5 class="mb-3 text-success">
                            <i class="bi bi-check-circle"></i> Verified Documents (${verifiedDocuments.length})
                        </h5>
                        ${verifiedDocuments.map((document, index) => `
                            <div class="card mb-2 border-success">
                                <div class="card-header bg-success bg-opacity-10 d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${document.document_name}</strong>
                                        <small class="text-muted"> - ${document.service_name}</small>
                                    </div>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> VERIFIED
                                    </span>
                                </div>
                                <div class="card-body py-2">
                                    <p class="text-success small mb-0">
                                        <i class="bi bi-calendar-check"></i> Verified on: ${document.uploaded_at}
                                        ${document.remarks ? `<br><i class="bi bi-chat-left-text"></i> Remarks: ${document.remarks}` : ''}
                                    </p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            if (rejectedDocuments.length > 0) {
                html += `
                    <div class="mb-4">
                        <h5 class="mb-3 text-danger">
                            <i class="bi bi-x-circle"></i> Rejected Documents (${rejectedDocuments.length})
                        </h5>
                        ${rejectedDocuments.map((document, index) => `
                            <div class="card mb-2 border-danger">
                                <div class="card-header bg-danger bg-opacity-10 d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${document.document_name}</strong>
                                        <small class="text-muted"> - ${document.service_name}</small>
                                    </div>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle"></i> REJECTED
                                    </span>
                                </div>
                                <div class="card-body py-2">
                                    <p class="text-danger small mb-0">
                                        <i class="bi bi-calendar-x"></i> Rejected on: ${document.uploaded_at}
                                        ${document.remarks ? `<br><i class="bi bi-chat-left-text"></i> Remarks: ${document.remarks}` : ''}
                                    </p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            $('#orderDocumentsContent').html(html);
            
            // Initialize event handlers
            initDocumentHandlers();
        }

        // Initialize document handlers
        function initDocumentHandlers() {
            // Approve button
            $('.btn-approve').on('click', function() {
                const documentId = $(this).data('document-id');
                const remarks = $(`#remarks-${documentId}`).val();
                
                approveDocument(documentId, remarks);
            });

            // Reject button
            $('.btn-reject').on('click', function() {
                const documentId = $(this).data('document-id');
                const remarks = $(`#remarks-${documentId}`).val();
                
                if (!remarks.trim()) {
                    $(`#remarks-${documentId}`).addClass('is-invalid');
                    $(`#remarks-${documentId}`).focus();
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Remarks Required',
                        text: 'Please provide remarks for rejecting this document.',
                    });
                    return;
                }
                
                rejectDocument(documentId, remarks);
            });

            // 🔥 NEW: Approve All button
            $('#approveAllBtn').on('click', function() {
                const currentOrderId = window.currentOrderId;
                if (!currentOrderId) return;
                
                const remarks = $('#bulkRemarks').val();
                
                Swal.fire({
                    title: 'Approve All Documents?',
                    text: `This will approve all pending documents.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, approve all'
                }).then((result) => {
                    if (result.isConfirmed) {
                        approveAllDocuments(currentOrderId, remarks);
                    }
                });
            });

            // 🔥 NEW: Reject All button
            $('#rejectAllBtn').on('click', function() {
                const currentOrderId = window.currentOrderId;
                if (!currentOrderId) return;
                
                const remarks = $('#bulkRemarks').val();
                
                if (!remarks.trim()) {
                    $('#bulkRemarks').addClass('is-invalid');
                    $('#bulkRemarks').focus();
                    Swal.fire({
                        icon: 'error',
                        title: 'Remarks Required',
                        text: 'Please provide remarks for rejecting all documents.',
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Reject All Documents?',
                    text: `This will reject all pending documents.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, reject all'
                }).then((result) => {
                    if (result.isConfirmed) {
                        rejectAllDocuments(currentOrderId, remarks);
                    }
                });
            });
        }

        // Approve document function
        function approveDocument(documentId, remarks) {
            const approveBtn = $(`.btn-approve[data-document-id="${documentId}"]`);
            const rejectBtn = $(`.btn-reject[data-document-id="${documentId}"]`);
            
            approveBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');
            rejectBtn.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("admin.document-verification.approve", ":id") }}'.replace(':id', documentId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    remarks: remarks
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Close modal and refresh page to update tabs
                            bootstrap.Modal.getInstance(document.getElementById('orderDocumentsModal')).hide();
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                        approveBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Approve');
                        rejectBtn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'An error occurred while approving the document';
                    Swal.fire('Error', error, 'error');
                    approveBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Approve');
                    rejectBtn.prop('disabled', false);
                }
            });
        }

        // Reject document function
        function rejectDocument(documentId, remarks) {
            const approveBtn = $(`.btn-approve[data-document-id="${documentId}"]`);
            const rejectBtn = $(`.btn-reject[data-document-id="${documentId}"]`);
            
            rejectBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');
            approveBtn.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("admin.document-verification.reject", ":id") }}'.replace(':id', documentId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    remarks: remarks
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Close modal and refresh page to update tabs
                            bootstrap.Modal.getInstance(document.getElementById('orderDocumentsModal')).hide();
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                        rejectBtn.prop('disabled', false).html('<i class="bi bi-x-circle"></i> Reject');
                        approveBtn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'An error occurred while rejecting the document';
                    Swal.fire('Error', error, 'error');
                    rejectBtn.prop('disabled', false).html('<i class="bi bi-x-circle"></i> Reject');
                    approveBtn.prop('disabled', false);
                }
            });
        }

        // 🔥 NEW: Approve all documents function
        function approveAllDocuments(orderId, remarks) {
            $.ajax({
                url: '{{ route("admin.document-verification.approve-all", ":orderId") }}'.replace(':orderId', orderId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    remarks: remarks
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#approveAllBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');
                    $('#rejectAllBtn').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            bootstrap.Modal.getInstance(document.getElementById('orderDocumentsModal')).hide();
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                        $('#approveAllBtn').prop('disabled', false).html('<i class="bi bi-check-all"></i> Approve All');
                        $('#rejectAllBtn').prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'An error occurred';
                    Swal.fire('Error', error, 'error');
                    $('#approveAllBtn').prop('disabled', false).html('<i class="bi bi-check-all"></i> Approve All');
                    $('#rejectAllBtn').prop('disabled', false);
                }
            });
        }

        // 🔥 NEW: Reject all documents function
        function rejectAllDocuments(orderId, remarks) {
            $.ajax({
                url: '{{ route("admin.document-verification.reject-all", ":orderId") }}'.replace(':orderId', orderId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    remarks: remarks
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#rejectAllBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');
                    $('#approveAllBtn').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            bootstrap.Modal.getInstance(document.getElementById('orderDocumentsModal')).hide();
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                        $('#rejectAllBtn').prop('disabled', false).html('<i class="bi bi-x-octagon"></i> Reject All');
                        $('#approveAllBtn').prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'An error occurred';
                    Swal.fire('Error', error, 'error');
                    $('#rejectAllBtn').prop('disabled', false).html('<i class="bi bi-x-octagon"></i> Reject All');
                    $('#approveAllBtn').prop('disabled', false);
                }
            });
        }

        // Helper functions
        function getFileIcon(mimeType) {
            if (mimeType.startsWith('image/')) return 'bi-file-image';
            if (mimeType === 'application/pdf') return 'bi-file-pdf';
            if (mimeType.includes('word')) return 'bi-file-word';
            if (mimeType.includes('excel') || mimeType.includes('sheet')) return 'bi-file-excel';
            if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'bi-file-ppt';
            return 'bi-file-earmark';
        }

        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    });
</script>
@endpush