@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">Orders Management</h1>
                <p class="text-muted mb-0">View and manage all service orders</p>
            </div>
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Create New Order
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $orders->total() }}</div>
                        <div class="card-label">Total Orders</div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-cart-check text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $orders->where('order_status', 'PENDING_PAYMENT')->count() }}</div>
                        <div class="card-label">Pending Payment</div>
                    </div>
                    <div class="card-icon" style="background-color: #f59e0b;">
                        <i class="bi bi-clock text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $orders->where('order_status', 'IN_PROGRESS')->count() }}</div>
                        <div class="card-label">In Progress</div>
                    </div>
                    <div class="card-icon" style="background-color: #8b5cf6;">
                        <i class="bi bi-gear text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $orders->where('order_status', 'PAID')->count() }}</div>
                        <div class="card-label">Paid Orders</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;">
                        <i class="bi bi-check-circle text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="section-card mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0 fw-bold">Orders List</h6>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="btn btn-outline-primary {{ !request()->status ? 'active' : '' }}">All</a>
                    <a href="{{ route('admin.orders.index', ['status' => 'PENDING_PAYMENT']) }}" 
                       class="btn btn-outline-primary {{ request()->status == 'PENDING_PAYMENT' ? 'active' : '' }}">
                       Pending Payment
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'IN_PROGRESS']) }}" 
                       class="btn btn-outline-primary {{ request()->status == 'IN_PROGRESS' ? 'active' : '' }}">
                       In Progress
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'PAID']) }}" 
                       class="btn btn-outline-primary {{ request()->status == 'PAID' ? 'active' : '' }}">
                       Paid
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'CANCELLED']) }}" 
                       class="btn btn-outline-primary {{ request()->status == 'CANCELLED' ? 'active' : '' }}">
                       Cancelled
                    </a>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" 
                           class="form-control" 
                           placeholder="Search orders..." 
                           id="searchOrders"
                           value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="section-card">
        <div class="table-responsive">
           <table class="table table-hover datatable" id="clientsTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Created By</th>
                        <th>Invoice</th>
                        <th>Status</th>
                        <th>Documents</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_id }}</strong>
                            @if($order->created_by_admin)
                                <br><small class="text-muted">Admin Order</small>
                            @endif
                        </td>
                        
                        <td>
                            <div>
                                <div class="fw-medium">{{ $order->client->name ?? 'N/A' }}</div>
                                @if($order->client->email ?? false)
                                <small class="text-muted">{{ $order->client->email }}</small>
                                @endif
                            </div>
                        </td>
                        
                        <td>
                            <div class="fw-medium">{{ $order->items->first()->service->service_name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $order->items->first()->service->service_code ?? '' }}</small>
                        </td>
                        
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        
                        <td class="fw-bold">
                            ₹{{ number_format($order->final_amount, 2) }}
                        </td>
                        
                        <td>
                            @if($order->created_by_admin)
                                <span class="badge bg-primary rounded-pill">
                                    <i class="bi bi-shield-check me-1"></i>Admin
                                </span>
                            @elseif($order->marketing_staff_id)
                                <span class="badge bg-success rounded-pill">
                                    <i class="bi bi-person-circle me-1"></i>Marketing
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        
                        <td>
                            @if($order->invoice_generated && $order->invoice_path)
                                <a href="{{ asset('storage/'.$order->invoice_path) }}" 
                                   class="btn btn-sm btn-danger"
                                   target="_blank"
                                   title="Download Invoice">
                                    <i class="bi bi-file-pdf me-1"></i>PDF
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        
                        <td>
                            @php
                                $statusMap = [
                                    'PENDING_PAYMENT' => ['color' => 'bg-warning', 'icon' => 'clock'],
                                    'PAYMENT_SUBMITTED' => ['color' => 'bg-info', 'icon' => 'hourglass-split'],
                                    'PAYMENT_APPROVED' => ['color' => 'bg-primary', 'icon' => 'check-circle'],
                                    'PAYMENT_REJECTED' => ['color' => 'bg-danger', 'icon' => 'x-circle'],
                                    'IN_PROGRESS' => ['color' => 'bg-secondary', 'icon' => 'gear'],
                                    'COMPLETED' => ['color' => 'bg-success', 'icon' => 'check-lg'],
                                    'CANCELLED' => ['color' => 'bg-danger', 'icon' => 'x-lg'],
                                    'PAID' => ['color' => 'bg-success', 'icon' => 'currency-rupee'],
                                ];
                                $statusInfo = $statusMap[$order->order_status] ?? ['color' => 'bg-secondary', 'icon' => 'question-circle'];
                                $statusText = str_replace('_', ' ', $order->order_status);
                            @endphp
                            
                            <span class="badge {{ $statusInfo['color'] }} rounded-pill d-flex align-items-center gap-1" style="width: fit-content;">
                                <i class="bi bi-{{ $statusInfo['icon'] }}"></i>
                                {{ $statusText }}
                            </span>
                        </td>
                        
                        <!-- Documents Column -->
                        <td>
                            @if(in_array($order->order_status, ['PAID', 'PAYMENT_APPROVED', 'COMPLETED']))
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary mt-1 manage-documents-btn"
                                        data-order-id="{{ $order->id }}"
                                        data-order-code="{{ $order->order_id }}"
                                        data-load-url="{{ route('admin.orders.loadDocuments', ['orderId' => $order->id]) }}"
                                        title="Manage Documents">
                                    <i class="bi bi-files"></i> Manage
                                </button>
                            @else
                                <span class="text-muted">Not Available</span>
                                <br>
                                <small class="text-muted">Order must be payment approved</small>
                            @endif
                        </td>
                        
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="btn btn-outline-secondary btn-sm"
                                   title="View Order">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($order->created_by_admin && $order->order_status === 'PENDING_PAYMENT')
                                <form action="{{ route('admin.orders.destroy', $order->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancel Order">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="bi bi-cart-x display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">No Orders Found</h5>
                            <p class="text-muted mb-0">Start by creating your first order</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if($orders->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach($orders->links()->elements[0] as $page => $url)
                            @if($page == $orders->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($orders->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Document Management Modal -->
<div class="modal fade" id="documentManagementModal" tabindex="-1" aria-labelledby="documentManagementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentManagementModalLabel">
                    <i class="bi bi-files me-2"></i>
                    <span id="modalOrderTitle">Manage Documents</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="list-group" id="documentList">
                            <div class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">Loading documents...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div id="documentContentArea">
                            <div class="text-center py-5">
                                <i class="bi bi-file-earmark-text display-4 text-muted mb-3"></i>
                                <h5 class="text-muted">Select a document</h5>
                                <p class="text-muted mb-0">Choose a document from the list to manage</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="deleteModalBody">
                Are you sure you want to delete this file? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
body {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.table th {
    font-size: 0.85rem;
}

.table td {
    color: #1f2933;
    font-weight: 500;
}

.text-muted {
    color: #6b7280 !important;
}

/* Stats card custom colors */
.stats-card .card-icon[style*="background-color: #8b5cf6;"] {
    background-color: #8b5cf6 !important;
}

/* Table styling */
.table th {
    background-color: #3b4f6b !important;
    color: white !important;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: none;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid var(--bs-border-color);
}

.table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Order ID styling */
strong {
    color: var(--bs-dark);
}

/* Status badges */
.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.badge i {
    font-size: 0.7rem;
}

/* Invoice button */
.btn-danger {
    background-color: #ef4444;
    border-color: #ef4444;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-danger:hover {
    background-color: #dc2626;
    border-color: #dc2626;
}

/* Action buttons */
.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.btn-outline-secondary {
    border-color: var(--bs-border-color);
}

.btn-outline-secondary:hover {
    background-color: var(--bs-secondary);
    border-color: var(--bs-secondary);
}

/* Document management styles */
.manage-documents-btn {
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
}

.document-item {
    cursor: pointer;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.document-item:hover {
    background-color: #f8f9fa;
}

.document-item.active {
    background-color: #e3f2fd;
    border-left-color: #2196f3;
}

.uploaded-file {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s;
    background-color: #fff;
}

.uploaded-file:hover {
    background-color: #f8f9fa;
    border-color: #adb5bd;
}

.file-size {
    font-size: 0.75rem;
    color: #6c757d;
}

.file-actions .btn {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
}

/* Progress bar for uploads */
.upload-progress {
    height: 5px;
    margin-top: 5px;
}

/* Document status badges */
.doc-status-badge {
    font-size: 0.7rem;
    padding: 0.15rem 0.4rem;
}

/* File type icons */
.file-icon {
    font-size: 1.5rem;
    width: 2rem;
    text-align: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-group .btn {
        margin: 0 1px;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    
    #documentManagementModal .modal-dialog {
        margin: 0.5rem;
        max-width: 95%;
    }
    
    #documentManagementModal .col-md-3,
    #documentManagementModal .col-md-9 {
        padding: 0.5rem;
        width: 100%;
    }
    
    #documentManagementModal .row {
        flex-direction: column;
    }
    
    .uploaded-file {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .file-actions {
        margin-top: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Global variables
let currentOrderId = null;
let currentDocumentId = null;
let currentFileId = null;
let currentOrderDocumentId = null;
let currentLoadDocumentsUrl = null;

// Define routes using Blade helpers
const APP_ROUTES = {
    uploadDocument: "{{ route('admin.orders.uploadDocument') }}",
    deleteFileTemplate: "{{ route('admin.orders.documentFiles.delete', ['fileId' => '__FILE_ID__']) }}",
    deleteAllFilesTemplate: "{{ route('admin.orders.orderDocuments.deleteAllFiles', ['orderDocumentId' => '__ORDER_DOCUMENT_ID__']) }}",
    changeStatusTemplate: "{{ route('admin.orders.orderDocuments.changeStatus', ['orderDocumentId' => '__ORDER_DOCUMENT_ID__']) }}"
};

// CSRF token for AJAX requests
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Show success message from session
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showToast('{{ session('error') }}', 'danger');
    @endif
    
    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('{{ $error }}', 'danger');
        @endforeach
    @endif
    
    // Search functionality
    const searchInput = document.getElementById('searchOrders');
    const searchButton = document.getElementById('searchButton');
    
    if (searchInput && searchButton) {
        searchButton.addEventListener('click', function() {
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                window.location.href = "{{ route('admin.orders.index') }}?search=" + encodeURIComponent(searchTerm);
            } else {
                window.location.href = "{{ route('admin.orders.index') }}";
            }
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchButton.click();
            }
        });
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Add event listeners for document management buttons
    document.querySelectorAll('.manage-documents-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderCode = this.getAttribute('data-order-code');
            currentLoadDocumentsUrl = this.getAttribute('data-load-url');
            
            console.log('Loading documents URL:', currentLoadDocumentsUrl); // Debug
            
            openDocumentManagementModal(orderId, orderCode);
        });
    });
    
    // Confirm delete button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentFileId) {
            deleteDocumentFile(currentFileId);
        }
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('documentManagementModal');
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            }
        }
    });
});

// Open document management modal
function openDocumentManagementModal(orderId, orderCode) {
    currentOrderId = orderId;
    
    // Update modal title
    document.getElementById('modalOrderTitle').textContent = `Manage Documents - ${orderCode}`;
    
    // Reset content areas
    document.getElementById('documentList').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2 mb-0">Loading documents...</p>
        </div>
    `;
    
    document.getElementById('documentContentArea').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Loading document information...</p>
        </div>
    `;
    
    // Load documents using the stored URL
    loadOrderDocuments();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('documentManagementModal'));
    modal.show();
}

// Load order documents using the stored URL
function loadOrderDocuments() {
    if (!currentLoadDocumentsUrl) {
        showToast('Document load URL not found', 'danger');
        return;
    }
    
    console.log('Fetching from:', currentLoadDocumentsUrl); // Debug
    
    fetch(currentLoadDocumentsUrl, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Received data:', data); // Debug
        
        if (data.success) {
            renderDocumentList(data.requirements, data.uploaded_docs);
            // Load first document by default if exists
            if (data.requirements.length > 0) {
                const firstReq = data.requirements[0];
                const firstUploaded = data.uploaded_docs[firstReq.document_master_id];
                loadDocumentContent(firstReq, firstUploaded);
            }
        } else {
            showToast(data.message || 'Failed to load documents', 'danger');
        }
    })
    .catch(error => {
        console.error('Error loading documents:', error);
        showToast('Failed to load documents. Please try again.', 'danger');
        
        // Update UI to show error
        document.getElementById('documentList').innerHTML = `
            <div class="text-center py-4">
                <i class="bi bi-exclamation-triangle text-danger display-6 mb-3"></i>
                <p class="text-danger">Failed to load documents</p>
                <button class="btn btn-sm btn-outline-primary" onclick="loadOrderDocuments()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Retry
                </button>
            </div>
        `;
    });
}

// Render document list
function renderDocumentList(requirements, uploadedDocs) {
    const documentList = document.getElementById('documentList');
    
    if (!requirements || requirements.length === 0) {
        documentList.innerHTML = `
            <div class="text-center py-4">
                <i class="bi bi-file-earmark-x display-6 text-muted mb-3"></i>
                <p class="text-muted">No document requirements found</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    requirements.forEach((req, index) => {
        const uploadedDoc = uploadedDocs && uploadedDocs[req.document_master_id] ? uploadedDocs[req.document_master_id] : null;
        const status = uploadedDoc ? uploadedDoc.status : 'NOT_UPLOADED';
        const fileCount = uploadedDoc && uploadedDoc.files ? uploadedDoc.files.length : 0;
        
        const statusColors = {
            'APPROVED': 'success',
            'PENDING': 'warning',
            'UPLOADED': 'info',
            'REJECTED': 'danger',
            'NOT_UPLOADED': 'secondary'
        };
        
        const statusIcons = {
            'APPROVED': 'check-circle',
            'PENDING': 'clock',
            'UPLOADED': 'cloud-upload',
            'REJECTED': 'x-circle',
            'NOT_UPLOADED': 'file-earmark'
        };
        
        const statusColor = statusColors[status] || 'secondary';
        const statusIcon = statusIcons[status] || 'file-earmark';
        
        html += `
            <a href="#" class="list-group-item list-group-item-action document-item ${index === 0 ? 'active' : ''}" 
               data-document-master-id="${req.document_master_id}"
               data-document-name="${req.document_master.document_name}"
               data-type="${req.type}"
               onclick="selectDocument(this, ${JSON.stringify(req).replace(/"/g, '&quot;')}, ${uploadedDoc ? JSON.stringify(uploadedDoc).replace(/"/g, '&quot;') : 'null'}); return false;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-${statusIcon} text-${statusColor} me-2"></i>
                        <div>
                            <div class="fw-medium">${req.document_master.document_name}</div>
                            <small class="text-muted">${req.type === 'mandatory' ? 'Required' : 'Optional'}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-${statusColor} doc-status-badge">${status}</span>
                        ${fileCount > 0 ? `<span class="badge bg-info doc-status-badge ms-1">${fileCount}</span>` : ''}
                    </div>
                </div>
            </a>
        `;
    });
    
    documentList.innerHTML = html;
}

// Select document
function selectDocument(element, requirement, uploadedDoc) {
    // Remove active class from all items
    document.querySelectorAll('.document-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to selected item
    element.classList.add('active');
    
    // Load document content
    loadDocumentContent(requirement, uploadedDoc);
}

// Load document content
function loadDocumentContent(requirement, uploadedDoc) {
    currentDocumentId = requirement.document_master_id;
    
    const contentArea = document.getElementById('documentContentArea');
    
    // Show loading
    contentArea.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Loading document...</p>
        </div>
    `;
    
    // Render after short delay for better UX
    setTimeout(() => {
        renderDocumentContent(requirement, uploadedDoc);
    }, 300);
}

// Render document content
function renderDocumentContent(requirement, uploadedDoc) {
    const hasUploaded = uploadedDoc && uploadedDoc.files && uploadedDoc.files.length > 0;
    const isApproved = uploadedDoc && uploadedDoc.status === 'APPROVED';
    const isRejected = uploadedDoc && uploadedDoc.status === 'REJECTED';
    const isPending = uploadedDoc && uploadedDoc.status === 'PENDING';
    const isUploaded = uploadedDoc && uploadedDoc.status === 'UPLOADED';
    
    let html = `
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold">${requirement.document_master.document_name}</h6>
                        <small class="text-muted">${requirement.type === 'mandatory' ? 'Required Document' : 'Optional Document'}</small>
                    </div>
                    <div>
                        ${hasUploaded ? `
                            <span class="badge bg-${isApproved ? 'success' : isRejected ? 'danger' : isPending ? 'warning' : isUploaded ? 'info' : 'secondary'}">
                                ${uploadedDoc.status}
                            </span>
                        ` : `
                            <span class="badge bg-secondary">NOT UPLOADED</span>
                        `}
                    </div>
                </div>
            </div>
            <div class="card-body">
    `;
    
    if (hasUploaded) {
        currentOrderDocumentId = uploadedDoc.id;
        
        // Show uploaded files
        html += `
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Uploaded Files (${uploadedDoc.files.length})</h6>
                <div class="uploaded-files-list">
        `;
        
        uploadedDoc.files.forEach(file => {
            const fileSize = formatFileSize(file.file_size);
            const fileIcon = getFileIcon(file.mime_type || file.file_name);
            const fileUrl = `/storage/${file.file_path}`;
            
            html += `
                <div class="uploaded-file" id="file-${file.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi ${fileIcon} me-3 text-primary file-icon"></i>
                            <div>
                                <div class="fw-medium">${file.file_name}</div>
                                <div class="file-size">${fileSize} • ${file.mime_type || 'Unknown type'}</div>
                                <small class="text-muted">Uploaded: ${new Date(file.uploaded_at).toLocaleDateString()}</small>
                            </div>
                        </div>
                        <div class="btn-group btn-group-sm file-actions">
                            <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="${fileUrl}" download="${file.file_name}" class="btn btn-outline-success" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger" onclick="showDeleteConfirm(${file.id}, '${file.file_name.replace(/'/g, "\\'")}')" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                </div>
                
                <div class="mt-3">
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteAllFiles(${uploadedDoc.id}, '${requirement.document_master.document_name.replace(/'/g, "\\'")}')">
                        <i class="bi bi-trash me-1"></i>Delete All Files
                    </button>
                </div>
            </div>
        `;
        
        // Status change for admin (only if not already approved or rejected)
        if (!isApproved && !isRejected) {
            html += `
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Change Status</h6>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" onclick="changeDocumentStatus(${uploadedDoc.id}, 'APPROVED')">
                            <i class="bi bi-check-circle me-1"></i>Approve
                        </button>
                        <button type="button" class="btn btn-danger" onclick="changeDocumentStatus(${uploadedDoc.id}, 'REJECTED')">
                            <i class="bi bi-x-circle me-1"></i>Reject
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Show remarks if exists
        if (uploadedDoc.remarks) {
            html += `
                <div class="alert alert-info mb-4">
                    <small><strong>Remarks:</strong> ${uploadedDoc.remarks}</small>
                </div>
            `;
        }
    } else {
        currentOrderDocumentId = null;
    }
    
    // Upload form
    html += `
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Upload New Files</h6>
                    <form id="uploadDocumentForm" enctype="multipart/form-data">
                        <input type="hidden" name="order_id" value="${currentOrderId}">
                        <input type="hidden" name="document_master_id" value="${requirement.document_master_id}">
                        
                        <div class="mb-3">
                            <label class="form-label">Select Files (PDF, JPG, PNG, ZIP, RAR, XLSM)</label>
                            <input type="file" 
                                   class="form-control" 
                                   name="files[]" 
                                   id="documentFiles"
                                   multiple
                                   accept=".pdf,.jpg,.jpeg,.png,.zip,.rar,.xlsm"
                                   required>
                            <div class="form-text">Maximum file size: 10MB per file</div>
                        </div>
                        
                        <div class="progress mb-3 d-none" id="uploadProgress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="bi bi-cloud-upload me-1"></i>Upload & Auto-Approve
                            </button>
                            ${hasUploaded ? `
                            <button type="button" class="btn btn-outline-secondary" onclick="refreshDocumentContent()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                            </button>
                            ` : ''}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('documentContentArea').innerHTML = html;
    
    // Add form submit event
    const form = document.getElementById('uploadDocumentForm');
    if (form) {
        form.removeEventListener('submit', uploadDocument); // Remove existing listener
        form.addEventListener('submit', uploadDocument);
    }
}

// Upload document
function uploadDocument(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const uploadBtn = document.getElementById('uploadBtn');
    const progressBar = document.getElementById('uploadProgress');
    const progressBarInner = progressBar.querySelector('.progress-bar');
    
    // Validate files
    const files = document.getElementById('documentFiles').files;
    if (files.length === 0) {
        showToast('Please select at least one file', 'warning');
        return;
    }
    
    // Check file sizes
    let totalSize = 0;
    for (let i = 0; i < files.length; i++) {
        totalSize += files[i].size;
        if (files[i].size > 10 * 1024 * 1024) { // 10MB
            showToast(`File "${files[i].name}" exceeds 10MB limit`, 'danger');
            return;
        }
    }
    
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="bi bi-cloud-upload me-1"></i>Uploading...';
    
    progressBar.classList.remove('d-none');
    progressBarInner.style.width = '0%';
    
    // Simulate progress
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 5;
        if (progress > 90) {
            clearInterval(progressInterval);
        }
        progressBarInner.style.width = `${progress}%`;
    }, 100);
    
    fetch(APP_ROUTES.uploadDocument, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(response => {
        clearInterval(progressInterval);
        progressBarInner.style.width = '100%';
        
        if (!response.ok) {
            throw new Error(`Upload failed: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        setTimeout(() => {
            progressBar.classList.add('d-none');
            progressBarInner.style.width = '0%';
        }, 500);
        
        if (data.success) {
            showToast(data.message || 'Documents uploaded and auto-approved successfully!', 'success');
            // Reset form
            form.reset();
            // Refresh the document content
            setTimeout(() => {
                loadOrderDocuments();
            }, 1000);
        } else {
            showToast(data.message || 'Upload failed', 'danger');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        clearInterval(progressInterval);
        progressBar.classList.add('d-none');
        showToast('Upload failed. Please try again.', 'danger');
    })
    .finally(() => {
        setTimeout(() => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="bi bi-cloud-upload me-1"></i>Upload & Auto-Approve';
        }, 500);
    });
}

// Show delete confirmation
function showDeleteConfirm(fileId, fileName) {
    currentFileId = fileId;
    
    const modalBody = document.getElementById('deleteModalBody');
    modalBody.innerHTML = `Are you sure you want to delete <strong>"${fileName}"</strong>? This action cannot be undone.`;
    
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();
}

// Delete document file
function deleteDocumentFile(fileId) {
    const deleteUrl = APP_ROUTES.deleteFileTemplate.replace('__FILE_ID__', fileId);
    
    fetch(deleteUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Delete failed: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'File deleted successfully', 'success');
            // Remove file from UI
            const fileElement = document.getElementById(`file-${fileId}`);
            if (fileElement) {
                fileElement.remove();
            }
            // Close delete modal
            bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();
            // Refresh document list if no files left
            setTimeout(() => {
                loadOrderDocuments();
            }, 500);
        } else {
            showToast(data.message || 'Failed to delete file', 'danger');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showToast('Failed to delete file. Please try again.', 'danger');
    })
    .finally(() => {
        currentFileId = null;
    });
}

// Delete all files
function deleteAllFiles(orderDocumentId, documentName) {
    if (confirm(`Are you sure you want to delete ALL files for "${documentName}"? This action cannot be undone.`)) {
        const deleteUrl = APP_ROUTES.deleteAllFilesTemplate.replace('__ORDER_DOCUMENT_ID__', orderDocumentId);
        
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Delete all failed: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'All files deleted successfully', 'success');
            // Refresh document list
            loadOrderDocuments();
        } else {
            showToast(data.message || 'Failed to delete files', 'danger');
        }
    })
    .catch(error => {
        console.error('Delete all error:', error);
        showToast('Failed to delete files. Please try again.', 'danger');
    });
    }
}

// Change document status
function changeDocumentStatus(orderDocumentId, status) {
    const changeStatusUrl = APP_ROUTES.changeStatusTemplate.replace('__ORDER_DOCUMENT_ID__', orderDocumentId);
    
    let remarks = '';
    if (status === 'REJECTED') {
        remarks = prompt('Please enter rejection reason:');
        if (remarks === null) return; // User cancelled
    }
    
    fetch(changeStatusUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: status,
            remarks: remarks
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Status change failed: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || `Document status updated to ${status}`, 'success');
            // Refresh document list
            loadOrderDocuments();
        } else {
            showToast(data.message || 'Failed to change status', 'danger');
        }
    })
    .catch(error => {
        console.error('Status change error:', error);
        showToast('Failed to change status. Please try again.', 'danger');
    });
}

// Refresh document content
function refreshDocumentContent() {
    loadOrderDocuments();
}

// Helper functions
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(fileInfo) {
    if (typeof fileInfo === 'string') {
        if (fileInfo.includes('pdf')) return 'bi-file-pdf';
        if (fileInfo.includes('image')) return 'bi-file-image';
        if (fileInfo.includes('zip') || fileInfo.includes('rar')) return 'bi-file-zip';
        if (fileInfo.includes('excel') || fileInfo.includes('spreadsheet') || fileInfo.includes('xls')) return 'bi-file-excel';
        if (fileInfo.includes('word') || fileInfo.includes('doc')) return 'bi-file-word';
        if (fileInfo.includes('text')) return 'bi-file-text';
    }
    
    // Check by extension
    const fileName = typeof fileInfo === 'string' ? fileInfo : (fileInfo.file_name || '');
    if (fileName.endsWith('.pdf')) return 'bi-file-pdf';
    if (fileName.match(/\.(jpg|jpeg|png|gif|bmp|webp)$/i)) return 'bi-file-image';
    if (fileName.match(/\.(zip|rar|7z|tar|gz)$/i)) return 'bi-file-zip';
    if (fileName.match(/\.(xls|xlsx|xlsm)$/i)) return 'bi-file-excel';
    if (fileName.match(/\.(doc|docx)$/i)) return 'bi-file-word';
    
    return 'bi-file-earmark';
}

// Show toast notification
function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.toast-container').forEach(container => container.remove());
    
    // Create toast container
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
    document.body.appendChild(toastContainer);
    
    const toastId = 'toast-' + Date.now();
    const typeClass = type === 'success' ? 'bg-success' : 
                     type === 'danger' ? 'bg-danger' : 
                     type === 'warning' ? 'bg-warning' : 'bg-info';
    
    const toast = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${typeClass} text-white">
                <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toast);
    const toastEl = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastEl, { 
        delay: 3000,
        autohide: true 
    });
    bsToast.show();
    
    // Remove toast from DOM after hiding
    toastEl.addEventListener('hidden.bs.toast', function () {
        this.remove();
        if (toastContainer.children.length === 0) {
            toastContainer.remove();
        }
    });
}
</script>
@endpush