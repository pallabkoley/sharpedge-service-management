{{-- resources/views/admin/documents/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Document Requirements')

@section('content')
<div class="container-fluid px-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Service Document Requirements</h1>
                    <p class="text-muted mb-0">Manage required documents for each service</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentsModal">
                    <i class="fas fa-plus me-2"></i>Add Documents
                </button>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="serviceTabs" role="tablist">
                @foreach($services as $index => $service)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index == 0 ? 'active' : '' }}" 
                                id="service-{{ $service->id }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#service-{{ $service->id }}" 
                                type="button"
                                role="tab"
                                aria-controls="service-{{ $service->id }}"
                                aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                            {{ $service->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Tabs Content --}}
    <div class="row">
        <div class="col-12">
            <div class="tab-content" id="serviceTabsContent">
                @foreach($services as $index => $service)
                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" 
                         id="service-{{ $service->id }}" 
                         role="tabpanel"
                         aria-labelledby="service-{{ $service->id }}-tab">
                        
                        <div class="card border-top-0 rounded-top-0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                                <h5 class="mb-0 text-primary">{{ $service->name }} - Required Documents</h5>
                                <button type="button" 
                                        class="btn btn-outline-primary edit-documents-btn"
                                        data-service-id="{{ $service->id }}"
                                        data-service-name="{{ $service->name }}">
                                    <i class="fas fa-edit me-1"></i>Edit Documents
                                </button>
                            </div>
                            <div class="card-body p-4">
                                @if($service->requiredDocuments->count() > 0)
                                    <div class="row g-3">
                                        @foreach($service->requiredDocuments as $document)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="card document-card h-100 shadow-sm">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="card-title mb-0 text-truncate" title="{{ $document->document_name }}">
                                                                {{ $document->document_name }}
                                                            </h6>
                                                            <span class="badge {{ $document->type == 'mandatory' ? 'bg-danger' : 'bg-warning' }} rounded-pill">
                                                                {{ ucfirst($document->type) }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                                            <small class="text-muted">
                                                                <i class="far fa-clock me-1"></i>
                                                                {{ $document->created_at->format('M d, Y') }}
                                                            </small>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger delete-document-btn"
                                                                    data-document-id="{{ $document->id }}"
                                                                    data-document-name="{{ $document->document_name }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="fas fa-folder-open fa-4x text-muted opacity-25"></i>
                                        </div>
                                        <h4 class="text-muted mb-2">No Documents Added</h4>
                                        <p class="text-muted mb-0">This service doesn't have any required documents yet.</p>
                                        <button type="button" 
                                                class="btn btn-primary mt-3 edit-documents-btn"
                                                data-service-id="{{ $service->id }}"
                                                data-service-name="{{ $service->name }}">
                                            <i class="fas fa-plus me-1"></i>Add Documents
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @if($service->requiredDocuments->count() > 0)
                                <div class="card-footer bg-light border-top">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                {{ $service->requiredDocuments->count() }} document(s) required
                                            </small>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            @php
                                                $mandatoryCount = $service->requiredDocuments->where('type', 'mandatory')->count();
                                                $optionalCount = $service->requiredDocuments->where('type', 'optional')->count();
                                            @endphp
                                            <small class="text-muted">
                                                <span class="text-danger">{{ $mandatoryCount }} mandatory</span> | 
                                                <span class="text-warning">{{ $optionalCount }} optional</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Excel Import Section --}}
    <div class="card mt-5">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-file-import me-2"></i>Bulk Import Documents</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="excelFile" class="form-label">Upload Excel/CSV File</label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="excelFile" accept=".xlsx,.xls,.csv">
                            <button class="btn btn-outline-secondary" type="button" id="clearFileBtn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="form-text">Supported formats: .xlsx, .xls, .csv (Max: 10MB)</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Download Template</label>
                        <div>
                            <a href="{{ route('admin.services.documents.template') }}" class="btn btn-outline-success">
                                <i class="fas fa-download me-2"></i>Download Excel Template
                            </a>
                        </div>
                        <div class="form-text">Use the template to ensure correct format</div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-success" id="importExcelBtn">
                <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                <i class="fas fa-file-import me-2"></i>Import Documents
            </button>
        </div>
    </div>
</div>

{{-- Add Documents Modal --}}
<div class="modal fade" id="addDocumentsModal" tabindex="-1" aria-labelledby="addDocumentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addDocumentsForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocumentsModalLabel">Add Required Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Add one or more required documents for the selected service
                    </div>

                    <div class="mb-3">
                        <label for="service_id" class="form-label">Select Service *</label>
                        <select class="form-select" id="service_id" name="service_id" required>
                            <option value="">-- Choose a Service --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select a service</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Required Documents *</label>
                        <div id="addDocumentFields">
                            <div class="document-field-group input-group mb-2">
                                <input type="text" class="form-control document-input" 
                                       name="document_name[]" 
                                       placeholder="e.g., Aadhaar Card" 
                                       required>
                                <select class="form-select type-select" name="type[]" style="max-width: 150px;">
                                    <option value="mandatory" selected>Mandatory</option>
                                    <option value="optional">Optional</option>
                                </select>
                                <button type="button" class="btn btn-outline-danger remove-document-btn-add" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addDocumentField">
                            <i class="fas fa-plus me-1"></i>Add More Document
                        </button>
                        <div class="form-text">Add at least one required document</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveDocumentsBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                        Save Documents
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Documents Modal --}}
<div class="modal fade" id="editDocumentsModal" tabindex="-1" aria-labelledby="editDocumentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editDocumentsForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_service_id" name="service_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="editDocumentsModalLabel">Edit Required Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Warning: Saving will replace all existing documents for <span id="currentServiceName" class="fw-bold"></span>
                    </div>

                    <div id="editDocumentFields">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading documents...</p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addEditDocumentField">
                            <i class="fas fa-plus me-1"></i>Add More Document
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateDocumentsBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                        Update Documents
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteDocumentModal" tabindex="-1" aria-labelledby="deleteDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDocumentModalLabel">Delete Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="deleteDocumentName" class="fw-bold"></span>?</p>
                <p class="text-danger mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <!-- Toasts will be injected here -->
</div>
@endsection

@push('styles')
<style>
    .document-card {
        border-left: 4px solid #4e73df;
        transition: all 0.2s ease-in-out;
        height: 100%;
    }
    
    .document-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }
    
    .nav-tabs .nav-link:hover {
        color: #4e73df;
        border-bottom: 3px solid rgba(78, 115, 223, 0.3);
    }
    
    .nav-tabs .nav-link.active {
        color: #4e73df;
        background-color: transparent;
        border: none;
        border-bottom: 3px solid #4e73df;
        font-weight: 600;
    }
    
    .document-field-group {
        transition: all 0.3s ease;
    }
    
    .document-field-group:focus-within {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .type-select {
        flex: 0 0 150px;
    }
    
    .badge.bg-danger {
        background-color: #e74a3b !important;
    }
    
    .badge.bg-warning {
        background-color: #f6c23e !important;
        color: #000;
    }
    
    #toastContainer {
        z-index: 9999 !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Variables
    let currentServiceId = null;
    let currentServiceName = null;
    let documentToDelete = null;
    
    // Initialize event listeners
    initEventListeners();
    
    function initEventListeners() {
        // Add document field (Add modal)
        $('#addDocumentField').on('click', function() {
            addDocumentField('#addDocumentFields', 'add');
        });
        
        // Add document field (Edit modal)
        $('#addEditDocumentField').on('click', function() {
            addDocumentField('#editDocumentFields', 'edit');
        });
        
        // Remove document field (Add modal)
        $(document).on('click', '.remove-document-btn-add', function() {
            removeDocumentField($(this), '#addDocumentFields');
        });
        
        // Remove document field (Edit modal)
        $(document).on('click', '.remove-document-btn-edit', function() {
            removeDocumentField($(this), '#editDocumentFields');
        });
        
        // Add Documents Form
        $('#addDocumentsForm').on('submit', function(e) {
            e.preventDefault();
            saveDocuments('add');
        });
        
        // Edit Documents Form
        $('#editDocumentsForm').on('submit', function(e) {
            e.preventDefault();
            saveDocuments('edit');
        });
        
        // Edit button click
        $(document).on('click', '.edit-documents-btn', function() {
            openEditModal($(this).data('service-id'), $(this).data('service-name'));
        });
        
        // Delete button click
        $(document).on('click', '.delete-document-btn', function() {
            openDeleteModal($(this).data('document-id'), $(this).data('document-name'));
        });
        
        // Confirm delete
        $('#confirmDeleteBtn').on('click', function() {
            deleteDocument();
        });
        
        // Import Excel
        $('#importExcelBtn').on('click', function() {
            importExcel();
        });
        
        // Clear file input
        $('#clearFileBtn').on('click', function() {
            $('#excelFile').val('');
        });
        
        // Modal hidden events
        $('#addDocumentsModal, #editDocumentsModal, #deleteDocumentModal').on('hidden.bs.modal', function() {
            resetForms();
        });
    }
    
    function addDocumentField(containerSelector, type) {
        const container = $(containerSelector);
        const fieldCount = container.find('.document-input').length;
        const btnClass = type === 'edit' ? 'remove-document-btn-edit' : 'remove-document-btn-add';
        
        const field = `
            <div class="document-field-group input-group mb-2">
                <input type="text" class="form-control document-input" 
                       name="document_name[]" 
                       placeholder="e.g., Aadhaar Card" 
                       required>
                <select class="form-select type-select" name="type[]" style="max-width: 150px;">
                    <option value="mandatory" selected>Mandatory</option>
                    <option value="optional">Optional</option>
                </select>
                <button type="button" class="btn btn-outline-danger ${btnClass}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.append(field);
        
        // Enable remove buttons if more than one field
        const removeBtns = container.find(`.${btnClass}`);
        if (removeBtns.length > 1) {
            removeBtns.prop('disabled', false);
        }
    }
    
    function removeDocumentField(button, containerSelector) {
        const container = $(containerSelector);
        button.closest('.document-field-group').remove();
        
        // Disable remove button if only one field left
        const removeBtns = container.find('.btn-outline-danger');
        if (removeBtns.length === 1) {
            removeBtns.prop('disabled', true);
        }
    }
    
    function saveDocuments(type) {
        const form = type === 'add' ? '#addDocumentsForm' : '#editDocumentsForm';
        const modal = type === 'add' ? '#addDocumentsModal' : '#editDocumentsModal';
        
        let url, method;
        if (type === 'add') {
            const serviceId = $('#service_id').val();
            if (!serviceId) {
                showToast('Error', 'Please select a service', 'danger');
                $('#service_id').addClass('is-invalid');
                return;
            }
            url = '{{ route("admin.services.documents.store", ["service" => ":serviceId"]) }}'.replace(':serviceId', serviceId);
            method = 'POST';
        } else {
            if (!currentServiceId) return;
            url = '{{ route("admin.services.documents.update", ["service" => ":serviceId"]) }}'.replace(':serviceId', currentServiceId);
            method = 'PUT';
        }
        
        const formData = $(form).serialize();
        
        // Validate form
        let isValid = true;
        $(`${form} [required]`).each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });
        
        if (!isValid) {
            showToast('Error', 'Please fill all required fields', 'danger');
            return;
        }
        
        // Show loading
        const submitBtn = $(`${form} button[type="submit"]`);
        submitBtn.prop('disabled', true);
        submitBtn.find('.spinner-border').removeClass('d-none');
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    // Close modal
                    $(modal).modal('hide');
                    
                    // Show success message
                    showToast('Success', response.message, 'success');
                    
                    // Reload page after 1 second
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showToast('Error', response.message || 'Operation failed', 'danger');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showToast('Error', xhr.responseJSON?.message || 'An error occurred. Please try again.', 'danger');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.find('.spinner-border').addClass('d-none');
            }
        });
    }
    
    function openEditModal(serviceId, serviceName) {
        currentServiceId = serviceId;
        currentServiceName = serviceName;
        
        // Set modal title
        $('#editDocumentsModalLabel').text('Edit Documents for ' + serviceName);
        $('#currentServiceName').text(serviceName);
        $('#edit_service_id').val(serviceId);
        
        // Clear any existing fields
        $('#editDocumentFields').empty();
        
        // Get existing documents via AJAX
        $.ajax({
            url: '{{ route("admin.services.documents.show", ["service" => ":serviceId"]) }}'.replace(':serviceId', serviceId),
            type: 'GET',
            beforeSend: function() {
                $('#editDocumentFields').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading documents...</p>
                    </div>
                `);
            },
            success: function(response) {
                const documents = response.data;
                const container = $('#editDocumentFields');
                container.empty();
                
                if (documents && documents.length > 0) {
                    documents.forEach((doc, index) => {
                        const field = `
                            <div class="document-field-group input-group mb-2">
                                <input type="text" class="form-control document-input" 
                                       name="document_name[]" 
                                       value="${doc.document_name}" 
                                       required>
                                <select class="form-select type-select" name="type[]" style="max-width: 150px;">
                                    <option value="mandatory" ${doc.type === 'mandatory' ? 'selected' : ''}>Mandatory</option>
                                    <option value="optional" ${doc.type === 'optional' ? 'selected' : ''}>Optional</option>
                                </select>
                                <button type="button" class="btn btn-outline-danger remove-document-btn-edit" ${index === 0 ? 'disabled' : ''}>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        container.append(field);
                    });
                } else {
                    // Add one empty field if no documents exist
                    addDocumentField('#editDocumentFields', 'edit');
                }
            },
            error: function(xhr) {
                $('#editDocumentFields').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error loading documents. ${xhr.responseJSON?.message || 'Please try again.'}
                    </div>
                `);
            }
        });
        
        // Show modal
        $('#editDocumentsModal').modal('show');
    }
    
    function openDeleteModal(documentId, documentName) {
        documentToDelete = documentId;
        $('#deleteDocumentName').text(documentName);
        $('#deleteDocumentModal').modal('show');
    }
    
    function deleteDocument() {
        if (!documentToDelete || !currentServiceId) return;
        
        const deleteBtn = $('#confirmDeleteBtn');
        deleteBtn.prop('disabled', true);
        deleteBtn.find('.spinner-border').removeClass('d-none');
        
        $.ajax({
            url: '{{ route("admin.services.documents.destroy", ["service" => ":serviceId", "document" => ":documentId"]) }}'
                .replace(':serviceId', currentServiceId)
                .replace(':documentId', documentToDelete),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    $('#deleteDocumentModal').modal('hide');
                    showToast('Success', response.message, 'success');
                    
                    // Reload page after 1 second
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showToast('Error', response.message || 'Delete failed', 'danger');
                }
            },
            error: function(xhr) {
                showToast('Error', xhr.responseJSON?.message || 'Error deleting document', 'danger');
            },
            complete: function() {
                deleteBtn.prop('disabled', false);
                deleteBtn.find('.spinner-border').addClass('d-none');
                documentToDelete = null;
            }
        });
    }
    
    function importExcel() {
        const fileInput = $('#excelFile')[0];
        
        if (!fileInput.files.length) {
            showToast('Error', 'Please select an Excel file to import', 'danger');
            return;
        }
        
        const formData = new FormData();
        formData.append('excel_file', fileInput.files[0]);
        
        const importBtn = $('#importExcelBtn');
        importBtn.prop('disabled', true);
        importBtn.find('.spinner-border').removeClass('d-none');
        
        $.ajax({
            url: '{{ route("admin.services.documents.import") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    showToast('Success', response.message, 'success');
                    
                    // Clear file input
                    $('#excelFile').val('');
                    
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showToast('Error', response.message || 'Import failed', 'danger');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showToast('Error', xhr.responseJSON?.message || 'Import failed', 'danger');
                }
            },
            complete: function() {
                importBtn.prop('disabled', false);
                importBtn.find('.spinner-border').addClass('d-none');
            }
        });
    }
    
    function resetForms() {
        // Reset add form
        $('#addDocumentsForm')[0].reset();
        $('#addDocumentFields').html(`
            <div class="document-field-group input-group mb-2">
                <input type="text" class="form-control document-input" 
                       name="document_name[]" 
                       placeholder="e.g., Aadhaar Card" 
                       required>
                <select class="form-select type-select" name="type[]" style="max-width: 150px;">
                    <option value="mandatory" selected>Mandatory</option>
                    <option value="optional">Optional</option>
                </select>
                <button type="button" class="btn btn-outline-danger remove-document-btn-add" disabled>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `);
        
        // Clear validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }
    
    function showValidationErrors(errors) {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Show new errors
        $.each(errors, function(field, messages) {
            const input = $(`[name="${field}"]`);
            if (input.length) {
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback">${messages[0]}</div>`);
            } else if (field.includes('document_name.')) {
                // Handle array field errors
                const index = field.match(/document_name\.(\d+)/)[1];
                $(`[name="document_name[]"]`).eq(index).addClass('is-invalid')
                    .after(`<div class="invalid-feedback">${messages[0]}</div>`);
            } else if (field.includes('type.')) {
                const index = field.match(/type\.(\d+)/)[1];
                $(`[name="type[]"]`).eq(index).addClass('is-invalid')
                    .after(`<div class="invalid-feedback">${messages[0]}</div>`);
            }
        });
    }
    
    function showToast(title, message, type) {
        const toastId = 'toast-' + Date.now();
        const icon = type === 'success' ? 'check-circle' : 
                    type === 'danger' ? 'exclamation-circle' : 
                    type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        
        const toast = $(`
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-${icon} me-2 fs-5"></i>
                        <div>
                            <strong>${title}:</strong> ${message}
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `);
        
        $('#toastContainer').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            bsToast.hide();
        }, 5000);
        
        // Remove from DOM after hide
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
});
</script>
@endpush