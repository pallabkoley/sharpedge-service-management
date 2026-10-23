@extends('layouts.app')

@section('title', 'Document Upload - Marketing')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Document Upload</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('marketing.dashboard') }}">Marketing</a></li>
                        <li class="breadcrumb-item active">Document Upload</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Total Documents</p>
                            <h4 class="mb-0"><span id="totalDocuments">0</span></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="ri-file-text-line fs-20 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Mandatory</p>
                            <h4 class="mb-0"><span id="mandatoryCount">0</span>/<span id="totalMandatory">0</span></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-danger bg-opacity-10 p-3">
                                <i class="ri-alert-line fs-20 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Uploaded</p>
                            <h4 class="mb-0"><span id="uploadedCount">0</span></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="ri-check-double-line fs-20 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Pending</p>
                            <h4 class="mb-0"><span id="pendingCount">0</span></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="ri-time-line fs-20 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <!-- Selection Card -->
            <div class="card" id="selectionCard">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-user-line me-2"></i>Select Client & Service
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri-error-warning-line me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-check-line me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Client Selection -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Client <span class="text-danger">*</span></label>
                            <select class="form-select" id="clientSelect">
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                            data-email="{{ $client->email }}"
                                            data-phone="{{ $client->phone }}"
                                            data-code="{{ $client->client_code }}">
                                        {{ $client->name }} ({{ $client->client_code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="clientError"></div>
                        </div>

                        <!-- Service Selection -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Service <span class="text-danger">*</span></label>
                            <select class="form-select" id="serviceSelect" disabled>
                                <option value="">-- Select Service --</option>
                                <!-- Services will be loaded via AJAX -->
                            </select>
                            <div class="invalid-feedback" id="serviceError"></div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary px-4" id="loadDocumentsBtn" disabled>
                            <i class="ri-arrow-right-line me-1"></i> Continue to Upload
                        </button>
                    </div>
                </div>
            </div>

            <!-- Document Upload Card (Hidden Initially) -->
            <div class="card d-none" id="uploadCard">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-upload-cloud-2-line me-2"></i>Document Upload
                    </h5>
                    <button type="button" class="btn btn-sm btn-light" id="backBtn">
                        <i class="ri-arrow-left-line me-1"></i> Change Selection
                    </button>
                </div>
                <div class="card-body">
                    <!-- Client & Service Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="text-primary mb-3">
                                    <i class="ri-user-3-line me-2"></i>Client Information
                                </h6>
                                <div class="row">
                                    <div class="col-5">
                                        <p class="mb-1"><strong>Name:</strong></p>
                                        <p class="mb-1"><strong>Email:</strong></p>
                                        <p class="mb-1"><strong>Phone:</strong></p>
                                        <p class="mb-0"><strong>Client Code:</strong></p>
                                    </div>
                                    <div class="col-7">
                                        <p class="mb-1" id="clientName">-</p>
                                        <p class="mb-1" id="clientEmail">-</p>
                                        <p class="mb-1" id="clientPhone">-</p>
                                        <p class="mb-0" id="clientCode">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="text-success mb-3">
                                    <i class="ri-service-line me-2"></i>Service Information
                                </h6>
                                <div class="row">
                                    <div class="col-5">
                                        <p class="mb-1"><strong>Service:</strong></p>
                                        <p class="mb-1"><strong>Service Code:</strong></p>
                                        <p class="mb-0"><strong>Processing Time:</strong></p>
                                    </div>
                                    <div class="col-7">
                                        <p class="mb-1" id="serviceName">-</p>
                                        <p class="mb-1" id="serviceCode">-</p>
                                        <p class="mb-0" id="serviceTime">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Progress -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="ri-information-line fs-20 me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Upload Status</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-3" style="height: 10px;">
                                                <div class="progress-bar bg-success" id="progressBar" 
                                                     role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <span class="fw-bold" id="progressText">0/0 mandatory documents uploaded</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Table -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="ri-file-list-line me-2"></i>Required Documents
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Messages Container -->
                            <div id="messageContainer"></div>
                            
                            <!-- Documents Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-centered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="30%">Document Name</th>
                                            <th width="10%">Type</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">File</th>
                                            <th width="25%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="documentsTable">
                                        <tr id="loadingRow">
                                            <td colspan="6" class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-2 text-muted">Please select client and service to load documents</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Instructions -->
                            <div class="alert alert-light mt-4">
                                <h6 class="mb-2"><i class="ri-information-line me-2"></i>Upload Instructions:</h6>
                                <ul class="mb-0 ps-3">
                                    <li>Allowed file formats: PDF, JPG, JPEG, PNG, DOC, DOCX</li>
                                    <li>Maximum file size: 5MB per document</li>
                                    <li><span class="badge bg-danger">Mandatory</span> documents must be uploaded</li>
                                    <li><span class="badge bg-info">Optional</span> documents are recommended but not required</li>
                                    <li>Click the upload button to select and upload files</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="uploadClientId" name="client_id">
                    <input type="hidden" id="uploadServiceId" name="service_id">
                    <input type="hidden" id="uploadDocumentId" name="document_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Document: <strong id="documentName"></strong></label>
                        <div class="alert alert-info py-2">
                            <small>
                                <i class="ri-information-line me-1"></i>
                                <span id="formatInfo"></span><br>
                                <span id="sizeInfo"></span>
                            </small>
                        </div>
                        <input type="file" class="form-control" name="document_file" id="documentFile" required>
                        <div class="form-text">Please ensure the document is clear and all details are visible</div>
                    </div>
                    
                    <div class="progress mb-3 d-none" id="uploadProgress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitUpload">
                    <i class="ri-upload-cloud-line me-1"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="ri-delete-bin-line me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this document?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="ri-delete-bin-line me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .document-card {
        transition: all 0.3s ease;
        border-left: 4px solid #dee2e6;
    }
    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .document-card.mandatory {
        border-left-color: #dc3545;
    }
    .document-card.optional {
        border-left-color: #0dcaf0;
    }
    .document-card.uploaded {
        border-left-color: #20c997;
    }
    .document-card.approved {
        border-left-color: #198754;
    }
    .document-card.rejected {
        border-left-color: #6c757d;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .file-info {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let currentClientId = null;
    let currentServiceId = null;
    let currentDocuments = [];
    let deleteUploadId = null;
    
    // Initialize modals
    const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Load services when client is selected
    $('#clientSelect').change(function() {
        const clientId = $(this).val();
        const serviceSelect = $('#serviceSelect');
        const loadBtn = $('#loadDocumentsBtn');
        
        if (clientId) {
            // Show loading in service select
            serviceSelect.html('<option value="">Loading services...</option>');
            serviceSelect.prop('disabled', true);
            
            // Make AJAX call to get services for this client
            $.ajax({
                url: '{{ route("marketing.documents.clientServices", "") }}/' + clientId,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.services.length > 0) {
                        serviceSelect.html('<option value="">-- Select Service --</option>');
                        response.services.forEach(function(service) {
                            serviceSelect.append(
                                `<option value="${service.id}" 
                                        data-code="${service.service_code}" 
                                        data-time="${service.processing_time || 'N/A'}">
                                    ${service.service_name} (${service.service_code})
                                </option>`
                            );
                        });
                        serviceSelect.prop('disabled', false);
                        loadBtn.prop('disabled', !serviceSelect.val());
                    } else {
                        serviceSelect.html('<option value="">No services available</option>');
                        loadBtn.prop('disabled', true);
                    }
                    
                    // Update client info preview
                    const selectedOption = $(this).find(':selected');
                    $('#clientName').text(selectedOption.text());
                    $('#clientEmail').text(selectedOption.data('email') || '-');
                    $('#clientPhone').text(selectedOption.data('phone') || '-');
                    $('#clientCode').text(selectedOption.data('code') || '-');
                },
                error: function() {
                    serviceSelect.html('<option value="">Error loading services</option>');
                    showMessage('Failed to load services for this client.', 'danger');
                }
            });
        } else {
            serviceSelect.html('<option value="">-- Select Service --</option>');
            serviceSelect.prop('disabled', true);
            loadBtn.prop('disabled', true);
            resetClientInfo();
        }
    }.bind($('#clientSelect')));
    
    // Enable load button when service is selected
    $('#serviceSelect').change(function() {
        const clientId = $('#clientSelect').val();
        const serviceId = $(this).val();
        $('#loadDocumentsBtn').prop('disabled', !(clientId && serviceId));
        
        // Update service info preview
        const selectedOption = $(this).find(':selected');
        $('#serviceName').text(selectedOption.text());
        $('#serviceCode').text(selectedOption.data('code') || '-');
        $('#serviceTime').text(selectedOption.data('time') || 'N/A');
    });
    
    // Load documents button
    $('#loadDocumentsBtn').click(function() {
        const clientId = $('#clientSelect').val();
        const serviceId = $('#serviceSelect').val();
        
        if (!clientId || !serviceId) {
            showMessage('Please select both client and service.', 'danger');
            return;
        }
        
        currentClientId = clientId;
        currentServiceId = serviceId;
        loadDocuments(clientId, serviceId);
    });
    
    // Back to selection
    $('#backBtn').click(function() {
        $('#selectionCard').removeClass('d-none');
        $('#uploadCard').addClass('d-none');
        resetUploadSection();
    });
    
    // Load documents via AJAX
    function loadDocuments(clientId, serviceId) {
        // Show loading state
        $('#documentsTable').html(`
            <tr id="loadingRow">
                <td colspan="6" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading documents...</p>
                </td>
            </tr>
        `);
        
        // Show upload section
        $('#selectionCard').addClass('d-none');
        $('#uploadCard').removeClass('d-none');
        
        // Make AJAX request
        $.ajax({
            url: '{{ route("marketing.documents.loadDocuments") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                client_id: clientId,
                service_id: serviceId
            },
            success: function(response) {
                if (response.success) {
                    currentDocuments = response.documents;
                    renderDocumentsTable(response.documents);
                    updateProgress(response.stats);
                    updateStatistics(response.stats);
                    showMessage('Documents loaded successfully!', 'success');
                } else {
                    showMessage(response.message || 'Failed to load documents', 'danger');
                    $('#backBtn').click();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Server error occurred';
                showMessage(error, 'danger');
                $('#backBtn').click();
            }
        });
    }
    
    // Update statistics cards
    function updateStatistics(stats) {
        $('#totalDocuments').text(stats.total);
        $('#mandatoryCount').text(stats.mandatory_uploaded);
        $('#totalMandatory').text(stats.mandatory);
        $('#uploadedCount').text(stats.uploaded);
        $('#pendingCount').text(stats.pending);
    }
    
    // Render documents table
    function renderDocumentsTable(documents) {
        const tableBody = $('#documentsTable');
        tableBody.empty();
        
        if (documents.length === 0) {
            tableBody.html(`
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="ri-file-text-line fs-48 text-muted mb-3"></i>
                        <p class="text-muted">No documents required for this service.</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        documents.forEach((doc, index) => {
            const rowClass = getRowClass(doc);
            const statusBadge = getStatusBadge(doc.status || doc.upload_status);
            const actionButtons = getActionButtons(doc, index);
            
            const row = `
                <tr class="${rowClass}" id="row-${doc.document_id || doc.id}">
                    <td>${index + 1}</td>
                    <td>
                        <div>
                            <strong>${doc.document_name || doc.name}</strong>
                            ${doc.description ? `
                                <p class="text-muted mb-0 small mt-1">${doc.description}</p>
                            ` : ''}
                        </div>
                    </td>
                    <td>
                        ${doc.is_mandatory ? 
                            '<span class="badge bg-danger">MANDATORY</span>' : 
                            '<span class="badge bg-info">OPTIONAL</span>'}
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        ${doc.file_name || doc.original_name ? `
                            <div class="file-info">
                                <small class="d-block text-truncate" title="${doc.file_name || doc.original_name}">
                                    <i class="ri-file-text-line me-1"></i>${doc.file_name || doc.original_name}
                                </small>
                                <small class="text-muted">
                                    ${doc.uploaded_at || doc.created_at} • ${doc.file_size ? Math.round(doc.file_size/1024) + ' KB' : ''}
                                </small>
                            </div>
                        ` : `
                            <span class="text-muted small">No file uploaded</span>
                        `}
                    </td>
                    <td>
                        <div class="action-buttons d-flex gap-1">
                            ${actionButtons}
                        </div>
                    </td>
                </tr>
            `;
            
            tableBody.append(row);
        });
        
        // Attach event listeners to buttons
        attachEventListeners();
    }
    
    // Get row CSS class based on document status
    function getRowClass(doc) {
        if (doc.status === 'approved' || doc.is_approved) return 'table-success';
        if (doc.status === 'rejected' || doc.is_rejected) return 'table-danger';
        if (doc.status === 'uploaded' || doc.is_uploaded || doc.file_name) return 'table-info';
        if (doc.is_mandatory) return '';
        return '';
    }
    
    // Get status badge HTML
    function getStatusBadge(status) {
        const badges = {
            'pending': {class: 'bg-warning', text: 'PENDING'},
            'uploaded': {class: 'bg-info', text: 'UPLOADED'},
            'approved': {class: 'bg-success', text: 'APPROVED'},
            'rejected': {class: 'bg-danger', text: 'REJECTED'},
            'not_uploaded': {class: 'bg-secondary', text: 'NOT UPLOADED'}
        };
        
        const badge = badges[status] || {class: 'bg-secondary', text: (status || '').toUpperCase()};
        return `<span class="badge ${badge.class} status-badge">${badge.text}</span>`;
    }
    
    // Get action buttons HTML
    function getActionButtons(doc, index) {
        let buttons = '';
        const hasFile = doc.file_name || doc.original_name;
        
        if (hasFile) {
            // File exists - show view/download/delete/upload
            const uploadId = doc.upload_id || doc.id;
            buttons += `
                <button class="btn btn-sm btn-light view-file" 
                        data-id="${uploadId}"
                        title="View">
                    <i class="ri-eye-line"></i>
                </button>
                <button class="btn btn-sm btn-light download-file" 
                        data-id="${uploadId}"
                        title="Download">
                    <i class="ri-download-line"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-file" 
                        data-id="${uploadId}"
                        title="Delete">
                    <i class="ri-delete-bin-line"></i>
                </button>
            `;
        }
        
        // Always show upload button
        const isApproved = doc.status === 'approved' || doc.is_approved;
        buttons += `
            <button class="btn btn-sm btn-primary upload-file" 
                    data-index="${index}"
                    ${isApproved ? 'disabled' : ''}
                    title="${hasFile ? 'Re-upload' : 'Upload'}">
                <i class="ri-upload-cloud-line"></i>
                ${hasFile ? 'Re-upload' : 'Upload'}
            </button>
        `;
        
        return buttons;
    }
    
    // Attach event listeners to buttons
    function attachEventListeners() {
        // Upload button
        $('.upload-file:not([disabled])').click(function() {
            const index = $(this).data('index');
            const doc = currentDocuments[index];
            
            // Set modal values
            $('#uploadClientId').val(currentClientId);
            $('#uploadServiceId').val(currentServiceId);
            $('#uploadDocumentId').val(doc.document_id || doc.id);
            $('#documentName').text(doc.document_name || doc.name);
            
            // Set format and size info
            const formats = (doc.file_formats || ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']).map(f => f.toUpperCase()).join(', ');
            const maxSizeKB = doc.max_size_kb || 5120;
            const maxSizeMB = doc.max_size_mb || 5;
            $('#formatInfo').text(`Allowed formats: ${formats}`);
            $('#sizeInfo').text(`Maximum size: ${maxSizeKB} KB (${maxSizeMB} MB)`);
            
            // Reset file input
            $('#documentFile').val('');
            $('#uploadProgress').addClass('d-none');
            
            // Show modal
            uploadModal.show();
        });
        
        // View file button
        $('.view-file').click(function() {
            const uploadId = $(this).data('id');
            window.open(`{{ route("marketing.documents.view", "") }}/${uploadId}`, '_blank');
        });
        
        // Download file button
        $('.download-file').click(function() {
            const uploadId = $(this).data('id');
            window.location.href = `{{ route("marketing.documents.download", "") }}/${uploadId}`;
        });
        
        // Delete file button
        $('.delete-file').click(function() {
            deleteUploadId = $(this).data('id');
            deleteModal.show();
        });
    }
    
    // Submit upload
    $('#submitUpload').click(function() {
        const formData = new FormData();
        const fileInput = document.getElementById('documentFile');
        
        if (!fileInput.files.length) {
            showMessage('Please select a file to upload.', 'danger');
            return;
        }
        
        // Validate file size (5MB = 5242880 bytes)
        const file = fileInput.files[0];
        if (file.size > 5242880) {
            showMessage('File size exceeds 5MB limit.', 'danger');
            return;
        }
        
        // Add form data
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('client_id', $('#uploadClientId').val());
        formData.append('service_id', $('#uploadServiceId').val());
        formData.append('document_id', $('#uploadDocumentId').val());
        formData.append('document_file', file);
        
        // Show progress bar
        $('#uploadProgress').removeClass('d-none');
        $('#submitUpload').prop('disabled', true);
        
        // AJAX upload
        $.ajax({
            url: '{{ route("marketing.documents.upload") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        $('#uploadProgress .progress-bar').css('width', percent + '%');
                    }
                });
                return xhr;
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    uploadModal.hide();
                    
                    // Reload documents
                    loadDocuments(currentClientId, currentServiceId);
                } else {
                    showMessage(response.message, 'danger');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Upload failed';
                showMessage(error, 'danger');
            },
            complete: function() {
                $('#submitUpload').prop('disabled', false);
                $('#uploadProgress').addClass('d-none');
                $('#uploadProgress .progress-bar').css('width', '0%');
            }
        });
    });
    
    // Confirm delete
    $('#confirmDelete').click(function() {
        if (!deleteUploadId) return;
        
        $.ajax({
            url: `{{ route('marketing.documents.delete', '') }}/${deleteUploadId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    deleteModal.hide();
                    
                    // Reload documents
                    loadDocuments(currentClientId, currentServiceId);
                } else {
                    showMessage(response.message, 'danger');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Delete failed';
                showMessage(error, 'danger');
            }
        });
    });
    
    // Update progress bar
    function updateProgress(stats) {
        const mandatoryTotal = stats.mandatory;
        const mandatoryUploaded = stats.mandatory_uploaded;
        const percent = mandatoryTotal > 0 ? (mandatoryUploaded / mandatoryTotal) * 100 : 0;
        
        $('#progressBar').css('width', percent + '%');
        $('#progressText').text(`${mandatoryUploaded}/${mandatoryTotal} mandatory documents uploaded`);
        
        // Update text color based on completion
        if (mandatoryUploaded === mandatoryTotal) {
            $('#progressText').addClass('text-success').removeClass('text-danger');
        } else {
            $('#progressText').addClass('text-danger').removeClass('text-success');
        }
    }
    
    // Reset client info
    function resetClientInfo() {
        $('#clientName, #clientEmail, #clientPhone, #clientCode').text('-');
    }
    
    // Reset upload section
    function resetUploadSection() {
        $('#documentsTable').html(`
            <tr id="loadingRow">
                <td colspan="6" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Please select client and service to load documents</p>
                </td>
            </tr>
        `);
        $('#progressBar').css('width', '0%');
        $('#progressText').text('0/0 mandatory documents uploaded');
        currentDocuments = [];
    }
    
    // Show message
    function showMessage(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';
        
        const alert = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('#messageContainer').html(alert);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
    
    // Initialize tooltips
    $('[title]').tooltip();
});
</script>
@endpush