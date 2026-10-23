@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Document Requirements Master</h2>
                <p class="text-muted mb-0">Manage required documents for all services</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" onclick="showImportModal()" 
                        class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="bi bi-upload"></i>
                    Import
                </button>
                <a href="{{ route('admin.document-requirements.export-template') }}" 
                   class="btn btn-outline-success d-flex align-items-center gap-2">
                    <i class="bi bi-download"></i>
                    Export Template
                </a>
                <button type="button" onclick="showAddModal()" 
                        class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    Add Document
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="card-icon bg-primary mb-3">
                            <i class="bi bi-files"></i>
                        </div>
                        <h5 class="card-title text-muted mb-2">Total Documents</h5>
                        <h3 class="card-value mb-0" id="total-docs">0</h3>
                    </div>
                    <div class="text-end">
                        <div class="card-change up">
                            <i class="bi bi-arrow-up-right"></i>
                            <span id="active-docs">0</span> active
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="card-icon" style="background: linear-gradient(135deg, #ef4444, #f87171);">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                        <h5 class="card-title text-muted mb-2">Mandatory</h5>
                        <h3 class="card-value mb-0" id="mandatory-docs">0</h3>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger">Required</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="card-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h5 class="card-title text-muted mb-2">Optional</h5>
                        <h3 class="card-value mb-0" id="optional-docs">0</h3>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-info">Optional</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="card-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                            <i class="bi bi-layers"></i>
                        </div>
                        <h5 class="card-title text-muted mb-2">Services Covered</h5>
                        <h3 class="card-value mb-0" id="services-covered">0</h3>
                    </div>
                    <div class="text-end">
                        <div class="card-change down">
                            <span id="inactive-docs">0</span> inactive
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="section-card">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <!-- Type Tabs -->
                            <div class="nav nav-pills" id="type-tabs" role="tablist">
                                <button class="nav-link active" data-type="all" onclick="filterByType('all')">
                                    All Documents
                                </button>
                                <button class="nav-link" data-type="mandatory" onclick="filterByType('mandatory')">
                                    <i class="bi bi-exclamation-circle me-1"></i>Mandatory
                                </button>
                                <button class="nav-link" data-type="optional" onclick="filterByType('optional')">
                                    <i class="bi bi-check-circle me-1"></i>Optional
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="search-input" class="form-control" 
                                       placeholder="Search documents or services..." onkeyup="searchDocuments()">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                    Clear
                                </button>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="filterByStatus('all')">All Status</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterByStatus('active')">Active Only</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterByStatus('inactive')">Inactive Only</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="row mb-3" id="bulk-actions-section" style="display: none;">
        <div class="col-md-12">
            <div class="section-card bulk-actions">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span id="selected-count">0</span> documents selected
                    </div>
                    <div class="d-flex gap-2">
                        <select id="bulk-action-select" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Bulk Actions</option>
                            <option value="activate">Activate</option>
                            <option value="deactivate">Deactivate</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button class="btn btn-sm btn-primary" onclick="applyBulkAction()">Apply</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">Clear Selection</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Requirements List -->
    <div class="row">
        <div class="col-md-12">
            <div id="documents-container">
                <!-- Documents will be loaded here via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading document requirements...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Document Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentModalLabel">Add Document Requirements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addDocumentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="service_id" class="form-label">Service *</label>
                        <select id="service_id" name="service_id" class="form-select" required>
                            <option value="">Select Service</option>
                            @foreach($services ?? [] as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Documents</label>
                        <div id="documents-container-add">
                            <div class="document-item mb-2">
                                <div class="input-group">
                                    <input type="text" name="documents[0][document_name]" 
                                           class="form-control" placeholder="Document name" required>
                                    <select name="documents[0][type]" class="form-select" style="max-width: 150px;">
                                        <option value="mandatory">Mandatory</option>
                                        <option value="optional">Optional</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeDocumentField(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addDocumentField()">
                            <i class="bi bi-plus"></i> Add Another Document
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Save Documents
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Document Modal -->
<div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentModalLabel">
                    Edit Document Requirements - <span id="service-name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDocumentForm">
                <input type="hidden" id="edit_service_id" name="service_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Drag and drop to reorder documents. Changes are saved automatically when you click "Save Changes".
                    </div>
                    
                    <div id="edit-documents-container" class="sortable-list">
                        <!-- Documents will be loaded here -->
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addNewDocumentField()">
                        <i class="bi bi-plus"></i> Add New Document
                    </button>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show-deleted">
                            <label class="form-check-label" for="show-deleted">
                                Show deleted documents
                            </label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Document Requirements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Download the template first to ensure correct format. Only .xls and .xlsx files are supported.
                    </div>
                    
                    <div class="import-dropzone p-5 text-center" id="dropzone">
                        <i class="bi bi-cloud-upload display-4 text-muted mb-3"></i>
                        <p class="mb-2">Drag & drop your Excel file here</p>
                        <p class="text-muted small mb-3">or</p>
                        <input type="file" id="importFile" name="file" class="form-control" accept=".xls,.xlsx">
                        <div class="form-text">Maximum file size: 5MB</div>
                    </div>
                    
                    <div id="import-results" style="display: none;">
                        <!-- Results will be shown here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.document-requirements.export-template') }}" 
                       class="btn btn-outline-success me-auto">
                        <i class="bi bi-download"></i> Download Template
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this document? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>
</div>

<style>
    .document-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    .document-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .document-card.mandatory {
        border-left: 4px solid #ef4444;
    }
    
    .document-card.optional {
        border-left: 4px solid #3b82f6;
    }
    
    .service-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .service-header {
        background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .service-body {
        padding: 20px;
    }
    
    .sortable-list .sortable-item {
        cursor: move;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin-bottom: 10px;
        padding: 15px;
    }
    
    .sortable-item.sortable-ghost {
        opacity: 0.4;
        background: #f8f9fa;
    }
    
    .sortable-item.sortable-drag {
        transform: rotate(5deg);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .sortable-handle {
        cursor: move;
        color: #6c757d;
        padding: 0 10px;
    }
    
    .badge-mandatory {
        background-color: #fee2e2;
        color: #dc2626;
        font-weight: 500;
    }
    
    .badge-optional {
        background-color: #dbeafe;
        color: #1d4ed8;
        font-weight: 500;
    }
    
    .bulk-actions {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px dashed #38bdf8;
        border-radius: 10px;
    }
    
    .import-dropzone {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        transition: all 0.3s;
    }
    
    .import-dropzone.dragover {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.05);
    }
</style>

@endsection

@push('styles')
<!-- jQuery and additional CSS if needed -->
@endpush

@push('scripts')
<!-- Load jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
// Document Requirements Module - Self-contained to prevent conflicts
(function() {
    // Variables
    let currentType = 'all';
    let currentStatus = 'all';
    let currentSearch = '';
    let selectedDocuments = new Set();
    let documentCounter = 0;
    let editSortable = null;

    // Initialize when DOM is ready
    $(document).ready(function() {
        loadDocuments();
        loadStats();
        
        // Form submissions
        $('#addDocumentForm').on('submit', function(e) {
            e.preventDefault();
            submitAddForm();
        });
        
        $('#editDocumentForm').on('submit', function(e) {
            e.preventDefault();
            submitEditForm();
        });
        
        $('#importForm').on('submit', function(e) {
            e.preventDefault();
            submitImport();
        });
        
        // Drag and drop for import
        setupDropzone();
    });

    // Load documents via AJAX
    function loadDocuments() {
        $.ajax({
            url: '{{ route("admin.document-requirements.list") }}',
            type: 'GET',
            data: {
                type: currentType,
                search: currentSearch
            },
            success: function(response) {
                if (response.success) {
                    renderDocuments(response.data);
                }
            },
            error: function() {
                $('#documents-container').html(`
                    <div class="alert alert-danger">
                        Error loading documents. Please try again.
                    </div>
                `);
            }
        });
    }

    // Load statistics
    function loadStats() {
        $.ajax({
            url: '{{ route("admin.document-requirements.stats") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    updateStats(response.data);
                }
            }
        });
    }

    // Update stats display
    function updateStats(stats) {
        $('#total-docs').text(stats.total);
        $('#mandatory-docs').text(stats.mandatory);
        $('#optional-docs').text(stats.optional);
        $('#services-covered').text(stats.services_count);
        $('#active-docs').text(stats.active);
        $('#inactive-docs').text(stats.inactive);
    }

    // Render documents grouped by service
    function renderDocuments(groupedData) {
        let html = '';
        
        if (Object.keys(groupedData).length === 0) {
            html = `
                <div class="text-center py-5">
                    <i class="bi bi-folder-x display-4 text-muted mb-3"></i>
                    <h5>No documents found</h5>
                    <p class="text-muted">Try adding some document requirements or change your filters.</p>
                </div>
            `;
        } else {
            for (const [serviceId, documents] of Object.entries(groupedData)) {
                const service = documents[0].service;
                html += renderServiceCard(service, documents);
            }
        }
        
        $('#documents-container').html(html);
        attachDocumentListeners();
    }

    // Render service card with its documents
    function renderServiceCard(service, documents) {
        let mandatoryCount = documents.filter(d => d.type === 'mandatory').length;
        let optionalCount = documents.filter(d => d.type === 'optional').length;
        let activeCount = documents.filter(d => d.status === 'active').length;
        
        return `
            <div class="service-card">
                <div class="service-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">${service.name}</h5>
                        <div class="d-flex gap-3 text-muted small">
                            <span><i class="bi bi-files me-1"></i> ${documents.length} documents</span>
                            <span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i> ${mandatoryCount} mandatory</span>
                            <span class="text-primary"><i class="bi bi-check-circle me-1"></i> ${optionalCount} optional</span>
                            <span class="text-success"><i class="bi bi-check-circle me-1"></i> ${activeCount} active</span>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" onclick="documentRequirements.editServiceDocuments(${service.id})">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </div>
                </div>
                <div class="service-body">
                    <div class="row">
                        ${documents.map(doc => renderDocumentCard(doc)).join('')}
                    </div>
                </div>
            </div>
        `;
    }

    // Render individual document card
    function renderDocumentCard(document) {
        const typeClass = document.type === 'mandatory' ? 'mandatory' : 'optional';
        const statusClass = document.status === 'active' ? 'status-active' : 'status-inactive';
        const statusText = document.status === 'active' ? 'Active' : 'Inactive';
        
        return `
            <div class="col-md-6 col-lg-4">
                <div class="document-card ${typeClass} p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge badge-${document.type} mb-2">
                                ${document.type === 'mandatory' ? 'Mandatory' : 'Optional'}
                            </span>
                            <span class="badge ${statusClass}">${statusText}</span>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input document-checkbox" type="checkbox" 
                                   value="${document.id}" onchange="documentRequirements.toggleDocumentSelection(${document.id})">
                        </div>
                    </div>
                    <h6 class="mb-1">${document.document_name}</h6>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Order: ${document.sort_order}</small>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary btn-sm" 
                                    onclick="documentRequirements.toggleDocumentStatus(${document.id})">
                                <i class="bi bi-toggle-${document.status === 'active' ? 'on' : 'off'}"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" 
                                    onclick="documentRequirements.confirmDelete(${document.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Filter functions
    function filterByType(type) {
        currentType = type;
        $('.nav-link').removeClass('active');
        $(`[data-type="${type}"]`).addClass('active');
        loadDocuments();
    }

    function filterByStatus(status) {
        currentStatus = status;
        loadDocuments();
    }

    function searchDocuments() {
        currentSearch = $('#search-input').val();
        loadDocuments();
    }

    function clearSearch() {
        $('#search-input').val('');
        currentSearch = '';
        loadDocuments();
    }

    // Show Add Modal
    function showAddModal() {
        documentCounter = 1;
        $('#addDocumentForm')[0].reset();
        $('#documents-container-add').html(`
            <div class="document-item mb-2">
                <div class="input-group">
                    <input type="text" name="documents[0][document_name]" 
                           class="form-control" placeholder="Document name" required>
                    <select name="documents[0][type]" class="form-select" style="max-width: 150px;">
                        <option value="mandatory">Mandatory</option>
                        <option value="optional">Optional</option>
                    </select>
                    <button type="button" class="btn btn-outline-danger" onclick="documentRequirements.removeDocumentField(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `);
        new bootstrap.Modal(document.getElementById('addDocumentModal')).show();
    }

    // Add document field
    function addDocumentField() {
        const html = `
            <div class="document-item mb-2">
                <div class="input-group">
                    <input type="text" name="documents[${documentCounter}][document_name]" 
                           class="form-control" placeholder="Document name" required>
                    <select name="documents[${documentCounter}][type]" class="form-select" style="max-width: 150px;">
                        <option value="mandatory">Mandatory</option>
                        <option value="optional">Optional</option>
                    </select>
                    <button type="button" class="btn btn-outline-danger" onclick="documentRequirements.removeDocumentField(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#documents-container-add').append(html);
        documentCounter++;
    }

    // Remove document field
    function removeDocumentField(button) {
        if ($('#documents-container-add .document-item').length > 1) {
            $(button).closest('.document-item').remove();
        }
    }

    // Submit Add Form
    function submitAddForm() {
        const form = $('#addDocumentForm');
        const submitBtn = form.find('button[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');
        
        spinner.removeClass('d-none');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.document-requirements.store") }}',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addDocumentModal')).hide();
                    showToast('success', response.message);
                    loadDocuments();
                    loadStats();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Error adding documents';
                showToast('error', error);
            },
            complete: function() {
                spinner.addClass('d-none');
                submitBtn.prop('disabled', false);
            }
        });
    }

    // Edit Service Documents
    function editServiceDocuments(serviceId) {
        $.ajax({
            url: '{{ route("admin.document-requirements.get-by-service", ":id") }}'.replace(':id', serviceId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderEditModal(response.service, response.documents);
                }
            }
        });
    }

    // Render Edit Modal
    function renderEditModal(service, documents) {
        $('#edit_service_id').val(service.id);
        $('#service-name').text(service.name);
        
        let html = '';
        documents.forEach((doc, index) => {
            html += renderEditDocumentField(doc, index);
        });
        
        $('#edit-documents-container').html(html);
        
        // Initialize sortable
        if (editSortable) {
            editSortable.destroy();
        }
        
        editSortable = Sortable.create(document.getElementById('edit-documents-container'), {
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            handle: '.sortable-handle',
            onEnd: function() {
                updateDocumentOrders();
            }
        });
        
        new bootstrap.Modal(document.getElementById('editDocumentModal')).show();
    }

    // Render edit document field
    function renderEditDocumentField(document, index) {
        return `
            <div class="sortable-item" data-id="${document.id}">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="sortable-handle">
                            <i class="bi bi-grip-vertical"></i>
                        </div>
                    </div>
                    <div class="col">
                        <input type="hidden" name="documents[${index}][id]" value="${document.id}">
                        <input type="text" name="documents[${index}][document_name]" 
                               class="form-control mb-2" value="${document.document_name}" required>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <select name="documents[${index}][type]" class="form-select">
                                    <option value="mandatory" ${document.type === 'mandatory' ? 'selected' : ''}>Mandatory</option>
                                    <option value="optional" ${document.type === 'optional' ? 'selected' : ''}>Optional</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="documents[${index}][status]" class="form-select">
                                    <option value="active" ${document.status === 'active' ? 'selected' : ''}>Active</option>
                                    <option value="inactive" ${document.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="documents[${index}][order]" 
                                       class="form-control" value="${document.sort_order}" placeholder="Order">
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="documentRequirements.markForDeletion(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Add new document field in edit modal
    function addNewDocumentField() {
        const index = $('#edit-documents-container .sortable-item').length;
        const html = `
            <div class="sortable-item">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="sortable-handle">
                            <i class="bi bi-grip-vertical"></i>
                        </div>
                    </div>
                    <div class="col">
                        <input type="text" name="documents[${index}][document_name]" 
                               class="form-control mb-2" placeholder="New document name" required>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <select name="documents[${index}][type]" class="form-select">
                                    <option value="mandatory">Mandatory</option>
                                    <option value="optional">Optional</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="documents[${index}][status]" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="documents[${index}][order]" 
                                       class="form-control" value="${index}" placeholder="Order">
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="documentRequirements.removeEditField(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#edit-documents-container').append(html);
        editSortable.option('disabled', false);
    }

    // Mark document for deletion
    function markForDeletion(button) {
        const item = $(button).closest('.sortable-item');
        const id = item.data('id');
        
        if (id) {
            if (!$('#deleted-documents-container').length) {
                $('#editDocumentForm').append('<input type="hidden" id="deleted-documents-container" name="deleted_documents[]">');
            }
            $('#deleted-documents-container').append(`<input type="hidden" name="deleted_documents[]" value="${id}">`);
        }
        
        item.fadeOut(300, function() {
            $(this).remove();
            updateDocumentOrders();
        });
    }

    // Remove edit field
    function removeEditField(button) {
        $(button).closest('.sortable-item').remove();
        updateDocumentOrders();
    }

    // Update document orders
    function updateDocumentOrders() {
        $('#edit-documents-container .sortable-item').each(function(index) {
            $(this).find('input[name$="[order]"]').val(index);
        });
    }

    // Submit Edit Form
    function submitEditForm() {
        const form = $('#editDocumentForm');
        const submitBtn = form.find('button[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');
        
        spinner.removeClass('d-none');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.document-requirements.update", ":id") }}'.replace(':id', $('#edit_service_id').val()),
            type: 'PUT',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editDocumentModal')).hide();
                    showToast('success', response.message);
                    loadDocuments();
                    loadStats();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Error updating documents';
                showToast('error', error);
            },
            complete: function() {
                spinner.addClass('d-none');
                submitBtn.prop('disabled', false);
            }
        });
    }

    // Show Import Modal
    function showImportModal() {
        $('#importFile').val('');
        $('#import-results').hide().html('');
        new bootstrap.Modal(document.getElementById('importModal')).show();
    }

    // Setup dropzone for import
    function setupDropzone() {
        const dropzone = $('#dropzone');
        const fileInput = $('#importFile');
        
        dropzone.on('dragover', function(e) {
            e.preventDefault();
            dropzone.addClass('dragover');
        });
        
        dropzone.on('dragleave', function(e) {
            e.preventDefault();
            dropzone.removeClass('dragover');
        });
        
        dropzone.on('drop', function(e) {
            e.preventDefault();
            dropzone.removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                fileInput[0].files = files;
            }
        });
        
        fileInput.on('change', function() {
            const fileName = this.files[0]?.name || 'No file chosen';
            dropzone.find('p').first().text(fileName);
        });
    }

    // Submit Import
    function submitImport() {
        const form = $('#importForm')[0];
        const formData = new FormData(form);
        const submitBtn = $('#importForm button[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');
        
        spinner.removeClass('d-none');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.document-requirements.import") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showImportResults(response.results);
                    loadDocuments();
                    loadStats();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Import failed';
                showToast('error', error);
            },
            complete: function() {
                spinner.addClass('d-none');
                submitBtn.prop('disabled', false);
            }
        });
    }

    // Show import results
    function showImportResults(results) {
        let html = `
            <div class="alert alert-success mt-3">
                <h6><i class="bi bi-check-circle me-2"></i>Import Complete</h6>
                <p class="mb-0">${results.imported} documents imported successfully.</p>
            </div>
        `;
        
        if (results.errors && results.errors.length > 0) {
            html += `
                <div class="alert alert-warning mt-2">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>${results.errors.length} errors found:</h6>
                    <ul class="mb-0 small">
                        ${results.errors.map(error => `<li>${error}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        if (results.skipped && results.skipped.length > 0) {
            html += `
                <div class="alert alert-info mt-2">
                    <h6><i class="bi bi-info-circle me-2"></i>${results.skipped.length} documents skipped:</h6>
                    <ul class="mb-0 small">
                        ${results.skipped.map(skip => `<li>${skip}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        $('#import-results').html(html).show();
    }

    // Toggle document selection for bulk actions
    function toggleDocumentSelection(id) {
        if (selectedDocuments.has(id)) {
            selectedDocuments.delete(id);
        } else {
            selectedDocuments.add(id);
        }
        
        updateBulkActionsUI();
    }

    // Update bulk actions UI
    function updateBulkActionsUI() {
        const count = selectedDocuments.size;
        $('#selected-count').text(count);
        
        if (count > 0) {
            $('#bulk-actions-section').show();
        } else {
            $('#bulk-actions-section').hide();
        }
    }

    // Clear selection
    function clearSelection() {
        selectedDocuments.clear();
        $('.document-checkbox').prop('checked', false);
        updateBulkActionsUI();
    }

    // Apply bulk action
    function applyBulkAction() {
        const action = $('#bulk-action-select').val();
        if (!action) {
            showToast('warning', 'Please select an action');
            return;
        }
        
        if (selectedDocuments.size === 0) {
            showToast('warning', 'No documents selected');
            return;
        }
        
        if (action === 'delete') {
            if (!confirm(`Are you sure you want to delete ${selectedDocuments.size} document(s)?`)) {
                return;
            }
        }
        
        $.ajax({
            url: '{{ route("admin.document-requirements.bulk-actions") }}',
            type: 'POST',
            data: {
                action: action,
                ids: Array.from(selectedDocuments)
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    loadDocuments();
                    loadStats();
                    clearSelection();
                }
            },
            error: function() {
                showToast('error', 'Error performing bulk action');
            }
        });
    }

    // Toggle individual document status
    function toggleDocumentStatus(id) {
        $.ajax({
            url: '{{ route("admin.document-requirements.toggle-status", ":id") }}'.replace(':id', id),
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    loadDocuments();
                    loadStats();
                }
            },
            error: function() {
                showToast('error', 'Error updating status');
            }
        });
    }

    // Confirm delete
    function confirmDelete(id) {
        $('#confirm-delete').off('click').on('click', function() {
            deleteDocument(id);
        });
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Delete document
    function deleteDocument(id) {
        // This should be handled by the bulk actions or update method
        // For individual delete, we can use the bulk action endpoint with single ID
        if (confirm('Are you sure you want to delete this document?')) {
            $.ajax({
                url: '{{ route("admin.document-requirements.bulk-actions") }}',
                type: 'POST',
                data: {
                    action: 'delete',
                    ids: [id]
                },
                success: function(response) {
                    if (response.success) {
                        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                        showToast('success', response.message);
                        loadDocuments();
                        loadStats();
                    }
                },
                error: function() {
                    showToast('error', 'Error deleting document');
                }
            });
        }
    }

    // Show toast notification
    function showToast(type, message) {
        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} border-0 position-fixed top-0 end-0 m-3" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-x-circle' : 'bi-exclamation-triangle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        $('body').append(toastHtml);
        const toast = new bootstrap.Toast(document.getElementById(toastId));
        toast.show();
        
        // Remove after hide
        document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    // Attach event listeners to newly loaded documents
    function attachDocumentListeners() {
        // Attach any additional listeners if needed
    }

    // Expose functions to global scope for inline onclick handlers
    window.documentRequirements = {
        filterByType: filterByType,
        filterByStatus: filterByStatus,
        searchDocuments: searchDocuments,
        clearSearch: clearSearch,
        showAddModal: showAddModal,
        showImportModal: showImportModal,
        addDocumentField: addDocumentField,
        removeDocumentField: removeDocumentField,
        editServiceDocuments: editServiceDocuments,
        addNewDocumentField: addNewDocumentField,
        markForDeletion: markForDeletion,
        removeEditField: removeEditField,
        toggleDocumentSelection: toggleDocumentSelection,
        toggleDocumentStatus: toggleDocumentStatus,
        confirmDelete: confirmDelete,
        clearSelection: clearSelection,
        applyBulkAction: applyBulkAction
    };

    // Initialize global functions
    window.filterByType = filterByType;
    window.filterByStatus = filterByStatus;
    window.searchDocuments = searchDocuments;
    window.clearSearch = clearSearch;
    window.showAddModal = showAddModal;
    window.showImportModal = showImportModal;
})();
</script>
@endpush