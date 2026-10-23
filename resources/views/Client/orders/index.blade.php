@extends('layouts.client')

@section('title', 'My Services - SharpEdge Client')

@section('page-title', 'My Services')
@section('page-subtitle', 'Track all your orders and their current status.')

@section('content')
    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        @php
            $totalOrders = $orders->count();
            $inProgress = $orders->where('calculated_work_status', 'IN_PROGRESS')->count();
            $completed = $orders->where('calculated_work_status', 'COMPLETED')->count();
            $pendingPayment = $orders->where('payment_status', 'pending')->count();
        @endphp

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Total Orders</h6>
                    <div class="card-icon bg-primary bg-opacity-10" style="background-color: rgba(29,78,216,0.1);">
                        <i class="bi bi-bag text-primary"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $totalOrders }}</h3>
                <span class="text-muted small">All time</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">In Progress</h6>
                    <div class="card-icon bg-info bg-opacity-10" style="background-color: rgba(13,202,240,0.1);">
                        <i class="bi bi-arrow-repeat text-info"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $inProgress }}</h3>
                <span class="text-muted small">Active orders</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Completed</h6>
                    <div class="card-icon bg-success bg-opacity-10" style="background-color: rgba(25,135,84,0.1);">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $completed }}</h3>
                <span class="text-muted small">Finished orders</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0">Pending Payment</h6>
                    <div class="card-icon bg-warning bg-opacity-10" style="background-color: rgba(255,193,7,0.1);">
                        <i class="bi bi-credit-card text-warning"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $pendingPayment }}</h3>
                <span class="text-muted small">Awaiting payment</span>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="section-card">
        <h4 class="section-title">All Orders</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Service Name</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Work Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="fw-bold">{{ $order->id }}</td>
                            <td>{{ $order->service->name ?? '—' }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
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
                                @php
                                    $workStatus = strtolower($order->calculated_work_status ?? 'unassigned');
                                    $workClasses = [
                                        'unassigned' => 'bg-secondary text-white',
                                        'not_started' => 'bg-secondary text-white',
                                        'assigned' => 'bg-primary text-white',
                                        'in_progress' => 'bg-info text-white',
                                        'waiting_client' => 'bg-warning text-dark',
                                        'submitted' => 'bg-purple text-white',
                                        'verified' => 'bg-success text-white',
                                        'completed' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                    ];
                                    $workLabels = [
                                        'unassigned' => 'Unassigned',
                                        'not_started' => 'Not Started',
                                        'assigned' => 'Assigned',
                                        'in_progress' => 'In Progress',
                                        'waiting_client' => 'Waiting for You',
                                        'submitted' => 'Submitted',
                                        'verified' => 'Verified',
                                        'completed' => 'Completed',
                                        'rejected' => 'Rejected',
                                    ];
                                @endphp
                                <span class="badge {{ $workClasses[$workStatus] ?? 'bg-secondary' }} px-3 py-2 rounded-pill">
                                    {{ $workLabels[$workStatus] ?? ucfirst(str_replace('_', ' ', $workStatus)) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-view-details"
                                            data-id="{{ $order->id }}">
                                        <i class="bi bi-eye me-1"></i> View
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-success rounded-pill px-3 btn-upload-docs"
                                            data-order-id="{{ $order->id }}">
                                        <i class="bi bi-upload me-1"></i> Upload
                                    </button>

                                    @if($order->certificate_path)
                                    <a href="{{ route('client.orders.certificate',$order->id) }}"
                                       class="btn btn-sm btn-dark rounded-pill px-3">
                                        <i class="bi bi-download me-1"></i> Certificate
                                    </a>
                                    @endif

                                    <!-- NEW: Docs button – appears only if support documents exist -->
                                    @if($order->supportDocuments->count() > 0)
                                    <button class="btn btn-sm btn-info rounded-pill px-3 btn-support-docs"
                                            data-order-id="{{ $order->id }}">
                                        <i class="bi bi-folder2-open me-1"></i> Docs
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Order Details Modal (Existing) --}}
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title" id="orderDetailsModalLabel">
                        <i class="bi bi-box-seam me-2"></i> Order Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading order details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== UPLOAD DOCUMENTS MODAL ========== --}}
    <div class="modal fade" id="uploadDocumentsModal" tabindex="-1" aria-labelledby="uploadDocumentsModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                {{-- Header --}}
                <div class="modal-header" style="background-color: #364966; color: white;">
                    <h5 class="modal-title" id="uploadDocumentsModalLabel">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i> Upload Documents
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{-- Body --}}
                <div class="modal-body">
                    {{-- Top Info Row --}}
                    <div id="uploadOrderInfo" class="row mb-4 p-3 bg-light rounded">
                        <div class="col-md-4">
                            <span class="text-muted">Order ID:</span>
                            <span class="fw-bold ms-1" id="uploadOrderId">-</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted">Client Name:</span>
                            <span class="fw-bold ms-1" id="uploadClientName">-</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted">Service Name:</span>
                            <span class="fw-bold ms-1" id="uploadServiceName">-</span>
                        </div>
                    </div>
                    
                    {{-- Upload Progress --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Required Documents</h6>
                        <span class="badge bg-info rounded-pill px-3 py-2" id="uploadProgressBadge">
                            <span id="uploadedCount">0</span> of <span id="totalCount">0</span> uploaded
                        </span>
                    </div>
                    
                    {{-- Document List Container --}}
                    <div id="documentListContainer">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading document requirements...</p>
                        </div>
                    </div>
                </div>
                
                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW: Support Documents Modal -->
    <div class="modal fade" id="supportDocumentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:#364966;color:white;">
                    <h5 class="modal-title">
                        <i class="bi bi-folder2-open me-2"></i>Support Documents
                    </h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="supportDocumentsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2 text-muted">Loading documents...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification Container (Bootstrap Toasts) --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="uploadToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    Document uploaded successfully.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Document Row Styling */
    .document-row {
        border-bottom: 1px solid #e9ecef;
        padding: 1.25rem 0;
    }
    .document-row:last-child {
        border-bottom: none;
    }
    .document-name {
        font-weight: 600;
        color: #1e293b;
    }
    .badge-required {
        background-color: #dc3545;
        color: white;
    }
    .badge-optional {
        background-color: #0d6efd;
        color: white;
    }
    .badge-pending {
        background-color: #ffc107;
        color: #212529;
    }
    .badge-uploaded {
        background-color: #198754;
        color: white;
    }
    .badge-rejected {
        background-color: #dc3545;
        color: white;
    }
    .file-input-label {
        display: inline-block;
        background-color: #f8f9fa;
        border: 1px dashed #ced4da;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .file-input-label:hover {
        background-color: #e9ecef;
    }
    .upload-btn {
        background: linear-gradient(135deg, #1d4ed8, #60a5fa);
        border: none;
        border-radius: 30px;
        padding: 0.4rem 1.2rem;
        color: white;
        font-weight: 500;
        transition: transform 0.2s;
    }
    .upload-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .upload-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .preview-link {
        color: #1d4ed8;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .preview-link:hover {
        text-decoration: underline;
    }
    .replace-link {
        color: #6c757d;
        text-decoration: none;
        font-size: 0.9rem;
        margin-left: 0.5rem;
    }
    .replace-link:hover {
        color: #1d4ed8;
    }
    
    /* Custom Purple Badge for Submitted Status */
    .bg-purple {
        background-color: #6f42c1 !important;
    }

    /* Optional: Make badges slightly smaller and cleaner */
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // --- DataTable Initialization ---
    if ($.fn.DataTable.isDataTable('#ordersTable')) {
        $('#ordersTable').DataTable().destroy();
    }
    $('#ordersTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        responsive: true,
        language: {
            emptyTable: "No orders found.",
            zeroRecords: "No matching orders found.",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)"
        }
    });

    // --- Order Details Modal (Existing) ---
    $(document).on('click', '.btn-view-details', function(e) {
        e.preventDefault();
        var orderId = $(this).data('id');
        var modal = $('#orderDetailsModal');
        var contentDiv = $('#orderDetailsContent');
        modal.modal('show');
        contentDiv.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading order details...</p>
            </div>
        `);
        $.ajax({
            url: '/client/orders/' + orderId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    var order = response.data;
                    var html = buildOrderDetailsHTML(order);
                    contentDiv.html(html);
                } else {
                    contentDiv.html('<div class="alert alert-danger">Order not found.</div>');
                }
            },
            error: function() {
                contentDiv.html('<div class="alert alert-danger">Failed to load order details. Please try again.</div>');
            }
        });
    });

    // --- ========== UPLOAD DOCUMENTS FUNCTIONALITY ========== ---
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Toast initialization
    var toastEl = document.getElementById('uploadToast');
    var toast = new bootstrap.Toast(toastEl, { delay: 3000 });

    function showToast(message, type = 'success') {
        $('#toastMessage').text(message);
        toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
        toastEl.classList.add(type === 'success' ? 'bg-success' : type === 'danger' ? 'bg-danger' : 'bg-warning');
        toast.show();
    }

    // Current order ID (used for replace links)
    var currentOrderId = null;

    // Click handler for Upload Documents button
    $(document).on('click', '.btn-upload-docs', function() {
        currentOrderId = $(this).data('order-id');
        loadDocumentRequirements(currentOrderId);
    });

    // Load document requirements via AJAX
    function loadDocumentRequirements(orderId) {
        var modal = $('#uploadDocumentsModal');
        var docContainer = $('#documentListContainer');
        var orderIdSpan = $('#uploadOrderId');
        var clientNameSpan = $('#uploadClientName');
        var serviceNameSpan = $('#uploadServiceName');
        
        orderIdSpan.text(`#${orderId}`);
        clientNameSpan.text('Loading...');
        serviceNameSpan.text('Loading...');
        docContainer.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading document requirements...</p>
            </div>
        `);
        modal.modal('show');

        $.ajax({
            url: '/client/orders/' + orderId + '/documents',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.order) {
                    var order = response.order;
                    clientNameSpan.text(order.client?.user?.name || '—');
                    serviceNameSpan.text(order.service?.service_name || '—');
                    renderDocumentList(order);
                } else {
                    docContainer.html('<div class="alert alert-danger">Could not load document requirements.</div>');
                }
            },
            error: function() {
                docContainer.html('<div class="alert alert-danger">Server error. Please try again.</div>');
            }
        });
    }

    // Render document list inside modal
    function renderDocumentList(order) {
        var container = $('#documentListContainer');
        var requirements = order.service?.document_requirements || [];
        var uploadedDocs = order.documents || [];
        
        var totalCount = requirements.length;
        var uploadedCount = 0;
        
        var html = '';
        if (totalCount === 0) {
            html = '<div class="alert alert-info text-center py-4">No documents required for this service.</div>';
        } else {
            html = '<div class="document-list">';
            $.each(requirements, function(index, req) {
                var uploadedDoc = uploadedDocs.find(d => d.document_master_id == req.document_master_id);
                var isUploaded = !!uploadedDoc;
                if (isUploaded) uploadedCount++;
                
                var docMaster = req.document_master || (uploadedDoc ? uploadedDoc.document_master : null) || {};
                var docMasterId = req.document_master_id || docMaster.id;
                var docName =
                    docMaster.document_name ||
                    docMaster.display_name ||
                    docMaster.title ||
                    docMaster.name ||
                    'Document';
                
                var isRequired = req.is_required || false;
                var typeBadge = isRequired 
                    ? '<span class="badge badge-required rounded-pill px-3 py-1">Required</span>'
                    : '<span class="badge badge-optional rounded-pill px-3 py-1">Optional</span>';
                
                var statusBadge = '';
                var fileInfo = '';
                var uploadControls = '';
                
                if (isUploaded) {
                    var file = uploadedDoc.files && uploadedDoc.files.length > 0 ? uploadedDoc.files[0] : null;
                    var fileName = file ? file.file_name : 'Uploaded';
                    var fileUrl = file ? '/storage/' + file.file_path : '#';
                    
                    statusBadge = '<span class="badge badge-uploaded rounded-pill px-3 py-1">Uploaded</span>';
                    fileInfo = `
                        <div class="mt-2">
                            <span class="text-muted me-2">${fileName}</span>
                            <a href="${fileUrl}" target="_blank" class="preview-link"><i class="bi bi-eye"></i> Preview</a>
                            <a href="#" class="replace-link" data-master-id="${docMasterId}" data-order-id="${order.id}"><i class="bi bi-arrow-repeat"></i> Replace</a>
                        </div>
                    `;
                    uploadControls = `
                        <div style="display:none;" class="upload-controls-${docMasterId}">
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <label class="file-input-label">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Choose file
                                    <input type="file" class="d-none file-input" data-master-id="${docMasterId}" accept=".pdf,.jpg,.jpeg,.png">
                                </label>
                                <button class="btn upload-btn btn-upload-submit" data-master-id="${docMasterId}" disabled>Upload</button>
                                <span class="validation-error text-danger small" id="error-${docMasterId}"></span>
                            </div>
                        </div>
                    `;
                } else {
                    statusBadge = '<span class="badge badge-pending rounded-pill px-3 py-1">Pending</span>';
                    uploadControls = `
                        <div class="upload-controls-${docMasterId}">
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <label class="file-input-label">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Choose file
                                    <input type="file" class="d-none file-input" data-master-id="${docMasterId}" accept=".pdf,.jpg,.jpeg,.png">
                                </label>
                                <button class="btn upload-btn btn-upload-submit" data-master-id="${docMasterId}" disabled>Upload</button>
                                <span class="validation-error text-danger small" id="error-${docMasterId}"></span>
                            </div>
                        </div>
                    `;
                }
                
                html += `
                    <div class="document-row" data-master-id="${docMasterId}">
                        <div class="row align-items-start">
                            <div class="col-md-5">
                                <div class="document-name mb-1">${docName}</div>
                                <div class="d-flex gap-2 mb-2">
                                    ${typeBadge}
                                    ${statusBadge}
                                </div>
                            </div>
                            <div class="col-md-7">
                                ${fileInfo}
                                ${uploadControls}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        container.html(html);
        $('#uploadedCount').text(uploadedCount);
        $('#totalCount').text(totalCount);
        
        attachFileInputHandlers(order.id);
        attachReplaceHandlers();
    }

    // Attach file input change events
    function attachFileInputHandlers(orderId) {
        $('.file-input').off('change').on('change', function(e) {
            var masterId = $(this).data('master-id');
            var file = this.files[0];
            var uploadBtn = $(`.btn-upload-submit[data-master-id="${masterId}"]`);
            var errorSpan = $(`#error-${masterId}`);
            
            var isValid = true;
            var errorMsg = '';
            
            if (!file) {
                isValid = false;
                errorMsg = 'No file selected.';
            } else {
                if (file.size > 5 * 1024 * 1024) {
                    isValid = false;
                    errorMsg = 'File size must be less than 5MB.';
                }
                var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    isValid = false;
                    errorMsg = 'Only PDF, JPG, PNG files are allowed.';
                }
            }
            
            if (isValid) {
                uploadBtn.prop('disabled', false);
                errorSpan.text('');
            } else {
                uploadBtn.prop('disabled', true);
                errorSpan.text(errorMsg);
            }
        });
        
        // Attach upload button click
        $('.btn-upload-submit').off('click').on('click', function(e) {
            e.preventDefault();
            var masterId = $(this).data('master-id');
            var fileInput = $(`.file-input[data-master-id="${masterId}"]`)[0];
            var file = fileInput.files[0];
            var orderId = $('#uploadOrderId').text().replace('#', '');
            
            if (!file) return;
            
            var btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading...');
            
            var formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('document_master_id', masterId);
            formData.append('file', file);
            
            $.ajax({
                url: '/client/orders/upload-document',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        showToast('Document uploaded successfully!', 'success');
                        updateDocumentRowAfterUpload(masterId, file.name, orderId);
                        var uploadedCount = parseInt($('#uploadedCount').text());
                        $('#uploadedCount').text(uploadedCount + 1);
                    } else {
                        showToast('Upload failed: ' + (response.message || 'Unknown error'), 'danger');
                        btn.prop('disabled', false).html('Upload');
                    }
                },
                error: function(xhr) {
                    var msg = 'Upload failed. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showToast(msg, 'danger');
                    btn.prop('disabled', false).html('Upload');
                }
            });
        });
    }

    // Update document row after successful upload
    function updateDocumentRowAfterUpload(masterId, fileName, orderId) {
        var row = $(`.document-row[data-master-id="${masterId}"]`);
        
        row.find('.badge-pending').removeClass('badge-pending').addClass('badge-uploaded').text('Uploaded');
        
        var uploadControlsDiv = row.find(`.upload-controls-${masterId}`);
        uploadControlsDiv.hide();
        
        var fileInfoHtml = `
            <div class="mt-2">
                <span class="text-muted me-2">${fileName}</span>
                <span class="text-muted">(Uploaded)</span>
                <a href="#" class="replace-link ms-2" data-master-id="${masterId}" data-order-id="${orderId}"><i class="bi bi-arrow-repeat"></i> Replace</a>
            </div>
        `;
        
        var fileInfoDiv = row.find('.col-md-7 > div:first-child');
        if (fileInfoDiv.length) {
            fileInfoDiv.html(fileInfoHtml);
        } else {
            row.find('.col-md-7').prepend(fileInfoHtml);
        }
        
        attachReplaceHandlers();
    }

    // Handle "Replace" link click
    function attachReplaceHandlers() {
        $('.replace-link').off('click').on('click', function(e) {
            e.preventDefault();
            var masterId = $(this).data('master-id');
            var orderId = $(this).data('order-id');
            var row = $(`.document-row[data-master-id="${masterId}"]`);
            
            row.find('.col-md-7 > div:first-child').hide();
            
            var uploadControls = row.find(`.upload-controls-${masterId}`);
            uploadControls.show();
            uploadControls.find('.file-input').val('');
            uploadControls.find('.btn-upload-submit').prop('disabled', true);
            uploadControls.find('.validation-error').text('');
        });
    }

    // --- Helper function for order details modal ---
    function buildOrderDetailsHTML(order) {
        var createdDate = order.created_at ? new Date(order.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '—';
        var paymentStatus = order.payment_status || 'pending';
        var paymentBadge = getBadgeHTML(paymentStatus, 'payment');
        var workStatus = order.calculated_work_status 
                || order.order_status 
                || 'unassigned';
        var workBadge = getBadgeHTML(workStatus, 'work');
        
        var tasksHTML = '';
        if (order.tasks && order.tasks.length > 0) {
            tasksHTML += '<div class="mt-4"><h6 class="fw-bold">Tasks</h6><div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Task</th><th>Status</th><th>Due Date</th></tr></thead><tbody>';
            
            var taskStatusMap = {
                'unassigned': { label: 'Unassigned', class: 'bg-secondary' },
                'not_started': { label: 'Not Started', class: 'bg-secondary' },
                'assigned': { label: 'Assigned', class: 'bg-primary' },
                'in_progress': { label: 'In Progress', class: 'bg-info' },
                'waiting_client': { label: 'Waiting for You', class: 'bg-warning text-dark' },
                'submitted': { label: 'Submitted', class: 'bg-purple' },
                'verified': { label: 'Completed', class: 'bg-success' },
                'completed': { label: 'Completed', class: 'bg-success' },
                'rejected': { label: 'Rejected', class: 'bg-danger' }
            };

            order.tasks.forEach(function(task) {
                var taskStatus = (task.status || 'unassigned').toLowerCase();
                var s = taskStatusMap[taskStatus] || { label: task.status || 'Pending', class: 'bg-secondary' };
                var dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString() : '—';
                
                tasksHTML += `<tr>
                    <td>${task.task_name || task.step_name || '—'}</td>
                    <td>
                        <span class="badge ${s.class} px-3 py-2 rounded-pill">
                            ${s.label}
                        </span>
                    </td>
                    <td>${dueDate}</td>
                </tr>`;
            });
            tasksHTML += '</tbody></table></div></div>';
        } else {
            tasksHTML = '<p class="text-muted mt-3">No tasks available.</p>';
        }
        
        var couponInfo = '';
        if (order.coupon) {
            couponInfo = `
                <div class="mt-3 p-3 bg-light rounded">
                    <p class="mb-1"><strong>Coupon Applied:</strong> ${order.coupon.code || '—'}</p>
                    <p class="mb-1"><strong>Discount:</strong> ${order.coupon.discount_percent ?? 0}%</p>
                    <p class="mb-0"><strong>Final Amount:</strong> ₹${order.final_amount ? order.final_amount.toLocaleString() : '0'}</p>
                </div>
            `;
        }
        
        return `
            <div class="order-details">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Order ID:</strong> #${order.id}</p>
                        <p class="mb-1"><strong>Service:</strong> ${order.service?.service_name || '—'}</p>
                        <p class="mb-1"><strong>Order Date:</strong> ${createdDate}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Total Amount:</strong> ₹${order.final_amount ? order.final_amount.toLocaleString() : '0'}</p>
                        <p class="mb-1"><strong>Payment Status:</strong> ${paymentBadge}</p>
                        <p class="mb-1"><strong>Work Status:</strong> ${workBadge}</p>
                    </div>
                </div>
                ${couponInfo}
                ${tasksHTML}
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
        } else {
            var workStatus = String(status).toLowerCase();
            var workClasses = {
                'unassigned': 'bg-secondary text-white',
                'not_started': 'bg-secondary text-white',
                'assigned': 'bg-primary text-white',
                'in_progress': 'bg-info text-white',
                'waiting_client': 'bg-warning text-dark',
                'submitted': 'bg-purple text-white',
                'verified': 'bg-success text-white',
                'completed': 'bg-success text-white',
                'rejected': 'bg-danger text-white'
            };
            var workLabels = {
                'unassigned': 'Unassigned',
                'not_started': 'Not Started',
                'assigned': 'Assigned',
                'in_progress': 'In Progress',
                'waiting_client': 'Waiting for You',
                'submitted': 'Submitted',
                'verified': 'Completed',
                'completed': 'Completed',
                'rejected': 'Rejected'
            };
            badgeClass = workClasses[workStatus] || 'bg-secondary';
            label = workLabels[workStatus] || status.replace(/_/g, ' ');
        }
        
        return `<span class="badge ${badgeClass} px-3 py-2 rounded-pill">${label}</span>`;
    }

    // NEW: Support Documents click handler
    $(document).on('click', '.btn-support-docs', function() {
        let orderId = $(this).data('order-id');
        $('#supportDocumentsModal').modal('show');
        let html = '';
        @foreach($orders as $order)
            if (orderId == {{ $order->id }}) {
                @foreach($order->supportDocuments as $doc)
                    html += `
                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                            <span>{{ $doc->file_name }}</span>
                            <a href="/storage/{{ $doc->file_path }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    `;
                @endforeach
            }
        @endforeach
        if (html === '') {
            html = '<p class="text-muted">No documents uploaded</p>';
        }
        $('#supportDocumentsContent').html(html);
    });
});
</script>
@endpush