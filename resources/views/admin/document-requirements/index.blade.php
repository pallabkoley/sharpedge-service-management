@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Document Requirements</h2>
                <p class="text-muted mb-0">Manage document requirements for each service</p>
            </div>
            <button type="button" class="btn btn-primary" id="addDocumentBtn" data-bs-toggle="modal" data-bs-target="#documentModal">
                <i class="bi bi-plus-lg"></i>
                <span>Add Documents</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value" id="servicesWithDocsCount">0</div>
                        <div class="card-label">Services with Docs</div>
                    </div>
                    <div class="card-icon bg-primary"><i class="bi bi-files"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value" id="mandatoryDocsCount">0</div>
                        <div class="card-label">Mandatory Docs</div>
                    </div>
                    <div class="card-icon" style="background-color: #ef4444;"><i class="bi bi-exclamation-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value" id="optionalDocsCount">0</div>
                        <div class="card-label">Optional Docs</div>
                    </div>
                    <div class="card-icon" style="background-color: #f59e0b;"><i class="bi bi-info-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value" id="totalDocsCount">0</div>
                        <div class="card-label">Total Documents</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;"><i class="bi bi-file-text"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="section-card mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0 fw-bold">All Documents</h6>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                    <button type="button" class="btn btn-outline-primary" data-filter="mandatory">Mandatory</button>
                    <button type="button" class="btn btn-outline-primary" data-filter="optional">Optional</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔍 Global Search Bar -->
    <div class="section-card mb-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-search text-muted me-2"></i>
            <input type="text" id="searchRequirements" class="form-control" placeholder="Search by service or document name...">
        </div>
    </div>

    <!-- Document Requirements List -->
    <div id="documentRequirementsList">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading document requirements...</p>
        </div>
    </div>
</div>

<!-- Add/Edit Document Requirements Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add Document Requirements</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="documentForm" method="POST">
                @csrf
                <input type="hidden" id="serviceId" name="service_id">
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <!-- Service Selection - FIXED: changed form-control to form-select -->
                    <div class="mb-4">
                        <label for="serviceSelect" class="form-label required fw-bold">Select Service</label>
                        <select class="form-select select-service" id="serviceSelect" name="service_id" required>
                            <option value="">Choose a service...</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">
                                {{ $service->service_code }} - {{ $service->service_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Document Requirements -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold">Document Requirements</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="addDocumentRow">
                                <i class="bi bi-plus-circle me-1"></i> Add Document
                            </button>
                        </div>
                        
                        <div id="documentRowsContainer">
                            <!-- Document rows will be added here -->
                        </div>
                        
                        <div id="emptyDocumentState" class="text-center py-4 border rounded bg-light">
                            <i class="bi bi-file-text display-6 text-muted mb-3"></i>
                            <p class="text-muted mb-0">No documents added yet. Click "Add Document" to start.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Requirements</span>
                        <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add New Document Modal -->
<div class="modal fade" id="addNewDocumentModal" tabindex="-1" aria-labelledby="addNewDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addNewDocumentModalLabel">Add New Document</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addNewDocumentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newDocumentName" class="form-label required fw-bold">Document Name</label>
                        <input type="text" class="form-control" id="newDocumentName" name="document_name" 
                               placeholder="Enter document name (e.g., GST Certificate, MSME Certificate)" 
                               required>
                        <div class="form-text">Enter a unique document name that doesn't already exist.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveNewDocumentBtn">
                        <span>Add Document</span>
                        <span class="spinner-border spinner-border-sm d-none" id="saveNewDocumentSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Row Template (Hidden) -->
<template id="documentRowTemplate">
    <div class="document-row card mb-2">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-5 mb-2 mb-md-0">
                    <label class="form-label small text-muted mb-1">Document Name</label>
                    <div class="input-group">
                        <!-- This already uses form-select, good -->
                        <select class="form-select document-select" required>
                            <option value="">Select Document</option>
                            <!-- Documents will be loaded dynamically -->
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-add-new-doc" 
                                data-bs-toggle="modal" data-bs-target="#addNewDocumentModal">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <input type="hidden" class="document-id-input">
                    <input type="hidden" class="document-master-id-input">
                    <input type="hidden" class="document-name-input">
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="form-label small text-muted mb-1">Type</label>
                    <select class="form-select document-type" required>
                        <option value="mandatory">Mandatory</option>
                        <option value="optional">Optional</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select class="form-select document-status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-document mt-3">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('styles')
<!-- Select2 CSS + Bootstrap 5 Theme CSS (FIX) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
.service-card {
    background: #fff;
    border-radius: 10px;
    padding: 22px;
    margin-bottom: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    border: 1px solid #e5e7eb;
    position: relative;
    transition: all 0.25s ease;
}

/* Blue left accent like your screenshot */
.service-card::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    background: #2563eb; /* blue accent */
    border-radius: 10px 0 0 10px;
}

/* Hover effect */
.service-card:hover {
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.service-header {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 12px;
    margin-bottom: 14px;
}

.service-title {
    font-size: 17px;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.document-count {
    font-size: 14px;
    color: #6b7280;
    margin-top: 5px;
}
.required-docs {
    font-size: 15px;
    color: #374151;
    margin-bottom: 10px;
}
.mandatory-section {
    margin-bottom: 15px;
}
.mandatory-title {
    font-size: 14px;
    font-weight: 600;
    color: #dc2626;
    margin-bottom: 8px;
}
.optional-title {
    font-size: 14px;
    font-weight: 600;
    color: #d97706;
    margin-bottom: 8px;
}
.document-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.document-list li {
    padding: 6px 0;
    padding-left: 20px;
    position: relative;
    color: #4b5563;
    font-size: 14px;
}
.document-list li:before {
    content: "•";
    position: absolute;
    left: 0;
    color: #9ca3af;
}
.edit-btn-container {
    position: absolute;
    bottom: 20px;
    right: 20px;
}
.btn-outline-primary {
    border: 1px solid #3b82f6;
    color: #3b82f6;
}
.btn-outline-primary:hover {
    background-color: #3b82f6;
    color: white;
}
.btn-group-sm > .btn {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}
.form-control, .form-select {
    border: 1px solid #d1d5db;
    border-radius: 6px;
}
.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
@endpush

@push('scripts')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const documentRowTemplate = document.getElementById('documentRowTemplate').content;
    let currentServiceId = null;
    let isEditMode = false;
    let documentRequirements = {};
    let deletedDocumentIds = [];
    
    // Store documents for the selected service
    let serviceDocuments = [];

    // Define routes from Laravel
    const routes = {
        store: '{{ route("admin.document-requirements.store") }}',
        update: '{{ route("admin.document-requirements.update", ["serviceId" => ":serviceId"]) }}',
        list: '{{ route("admin.document-requirements.list") }}',
        documentsByService: '{{ route("admin.document-requirements.documents.by-service") }}',
        addDocument: '{{ route("admin.document-requirements.add-document") }}',
        toggleStatus: '{{ route("admin.document-requirements.toggle-status", ["id" => ":id"]) }}'
    };

    console.log('Routes loaded:', routes);

    // ========== SELECT2 INITIALIZATION with Bootstrap 5 theme ==========
    $('#documentModal').on('shown.bs.modal', function () {
        $('#serviceSelect').select2({
            theme: 'bootstrap-5',           // <-- FIX: added theme
            placeholder: 'Search service...',
            allowClear: true,
            dropdownParent: $('#documentModal'),
            width: '100%'
        });
    });

    // Clean up Select2 when modal is hidden
    $('#documentModal').on('hidden.bs.modal', function () {
        if ($('#serviceSelect').data('select2')) {
            $('#serviceSelect').select2('destroy');
        }
        // Also destroy any document selects to avoid duplicates next time
        $('#documentRowsContainer .document-select').each(function() {
            if ($(this).data('select2')) {
                $(this).select2('destroy');
            }
        });
    });

    // ========== Load documents for selected service ==========
    async function loadDocumentsByService(serviceId) {
        try {
            const response = await fetch(
                `${routes.documentsByService}?service_id=${serviceId}`,
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                }
            );

            const data = await response.json();
            serviceDocuments = data.success ? data.data : [];
        } catch (e) {
            serviceDocuments = [];
            showToast('Failed to load documents for selected service', 'danger');
        }
    }

    // ========== Populate dropdown based on serviceDocuments ==========
    function populateDocumentDropdown(selectElement) {
        selectElement.innerHTML = '<option value="">Select Document</option>';

        if (!serviceDocuments.length) {
            selectElement.disabled = true;
            return;
        }

        serviceDocuments.forEach(doc => {
            const option = document.createElement('option');
            option.value = doc.id;
            option.textContent = doc.document_name;
            selectElement.appendChild(option);
        });

        selectElement.disabled = false;
    }

    // Toast Notification
    function showToast(message, type = 'success') {
        const toast = `<div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex"><div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
        document.body.insertAdjacentHTML('beforeend', toast);
        const toastEl = document.querySelector('.toast:last-child');
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        bsToast.show();
    }

    // Load Document Requirements
    function loadDocumentRequirements() {
        const filter = document.querySelector('.btn-group .btn.active')?.dataset.filter || 'all';
        const type = filter === 'all' ? '' : filter;
        
        const url = `${routes.list}?type=${type}`;
        console.log('Loading requirements from:', url);
        
        fetch(url, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest', 
                'Accept': 'application/json' 
            }
        })
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })
        .then(data => {
            if(data.success) {
                documentRequirements = data.data;
                updateStats();
                renderDocumentRequirements();
            }
        })
        .catch(err => {
            console.error('Error loading requirements:', err);
            showToast('Failed to load document requirements', 'danger');
        });
    }

    // Update Statistics
    function updateStats() {
        let servicesCount = 0;
        let mandatoryCount = 0;
        let optionalCount = 0;
        let totalDocs = 0;

        Object.values(documentRequirements).forEach(serviceDocs => {
            servicesCount++;
            serviceDocs.forEach(doc => {
                totalDocs++;
                if (doc.type === 'mandatory') mandatoryCount++;
                if (doc.type === 'optional') optionalCount++;
            });
        });

        document.getElementById('servicesWithDocsCount').textContent = servicesCount;
        document.getElementById('mandatoryDocsCount').textContent = mandatoryCount;
        document.getElementById('optionalDocsCount').textContent = optionalCount;
        document.getElementById('totalDocsCount').textContent = totalDocs;
    }

    // Render Document Requirements
    function renderDocumentRequirements() {
        const container = document.getElementById('documentRequirementsList');
        
        if (Object.keys(documentRequirements).length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-file-text display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">No Document Requirements</h5>
                    <p class="text-muted mb-4">Add document requirements for your services</p>
                    <button class="btn btn-primary" id="addDocumentBtnEmpty" data-bs-toggle="modal" data-bs-target="#documentModal">
                        <i class="bi bi-plus-lg"></i> Add Document Requirements
                    </button>
                </div>
            `;
            document.getElementById('addDocumentBtnEmpty').addEventListener('click', () => {
                document.getElementById('serviceSelect').value = '';
                document.getElementById('serviceSelect').disabled = false;
                document.getElementById('documentRowsContainer').innerHTML = '';
                document.getElementById('emptyDocumentState').classList.remove('d-none');
            });
            return;
        }

        let html = '';
        
        Object.entries(documentRequirements).forEach(([serviceId, docs]) => {
            const service = docs[0]?.service;
            if (!service) return;

            // Get only active documents
            const activeDocs = docs.filter(doc => doc.status === 'active');
            
            // Group by type
            const mandatoryDocs = activeDocs.filter(doc => doc.type === 'mandatory');
            const optionalDocs = activeDocs.filter(doc => doc.type === 'optional');

            html += `
            <div class="service-card">
                <div class="service-header">
                    <h5 class="service-title">${service.service_name}</h5>
                    <div class="document-count">${docs.length} document(s)</div>
                </div>
                
                <div class="required-docs">Required Documents:</div>
                
                ${mandatoryDocs.length > 0 ? `
                <div class="mandatory-section">
                    <div class="mandatory-title">Mandatory Documents:</div>
                    <ul class="document-list">
                        ${mandatoryDocs.map(doc => {
                            const docMaster = doc.document_master;
                            return docMaster ? `
                            <li>${docMaster.document_name}</li>
                            ` : '';
                        }).join('')}
                    </ul>
                </div>
                ` : ''}
                
                ${optionalDocs.length > 0 ? `
                <div class="optional-section">
                    <div class="optional-title">Optional Documents:</div>
                    <ul class="document-list">
                        ${optionalDocs.map(doc => {
                            const docMaster = doc.document_master;
                            return docMaster ? `
                            <li>${docMaster.document_name}</li>
                            ` : '';
                        }).join('')}
                    </ul>
                </div>
                ` : ''}
                
                <div class="edit-btn-container">
                    <button class="btn btn-outline-primary btn-sm edit-service-docs" 
                            data-service-id="${serviceId}" 
                            data-service-name="${service.service_name}">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </button>
                </div>
            </div>
            `;
        });
        
        container.innerHTML = html;
        attachEditEvents();
    }

    // ========== Add Document Row with Select2 (Bootstrap 5 theme) ==========
    function addDocumentRow(documentData = null) {
        const container = document.getElementById('documentRowsContainer');
        const emptyState = document.getElementById('emptyDocumentState');
        
        const row = documentRowTemplate.cloneNode(true);
        const rowElement = row.querySelector('.document-row');
        
        // Populate data if provided (for edit mode)
        if (documentData) {
            const select = rowElement.querySelector('.document-select');
            const masterIdInput = rowElement.querySelector('.document-master-id-input');
            const nameInput = rowElement.querySelector('.document-name-input');
            const typeSelect = rowElement.querySelector('.document-type');
            const statusSelect = rowElement.querySelector('.document-status');
            const idInput = rowElement.querySelector('.document-id-input');
            
            if (documentData.id) idInput.value = documentData.id;
            masterIdInput.value = documentData.document_master_id || '';
            nameInput.value = documentData.document_master?.document_name || '';
            typeSelect.value = documentData.type || 'mandatory';
            statusSelect.value = documentData.status || 'active';
        }
        
        container.appendChild(rowElement);
        emptyState.classList.add('d-none');
        
        // Populate dropdown with serviceDocuments
        const selectElement = rowElement.querySelector('.document-select');
        populateDocumentDropdown(selectElement);
        
        // 🔍 Initialize Select2 with Bootstrap 5 theme
        $(selectElement).select2({
            theme: 'bootstrap-5',            // <-- FIX: added theme
            placeholder: 'Search document...',
            allowClear: true,
            dropdownParent: $('#documentModal'),
            width: '100%'
        });
        
        // If we have documentData with a document_master_id, set the select after options are loaded
        if (documentData && documentData.document_master_id) {
            $(selectElement).val(documentData.document_master_id).trigger('change');
            const masterIdInput = rowElement.querySelector('.document-master-id-input');
            const nameInput = rowElement.querySelector('.document-name-input');
            masterIdInput.value = documentData.document_master_id;
            nameInput.value = documentData.document_master?.document_name || '';
        }
        
        // Update hidden inputs when select changes (for Select2)
        $(selectElement).on('change', function() {
            const row = this.closest('.document-row');
            const masterIdInput = row.querySelector('.document-master-id-input');
            const nameInput = row.querySelector('.document-name-input');
            
            if (this.value) {
                masterIdInput.value = this.value;
                const selectedDoc = serviceDocuments.find(doc => doc.id == this.value);
                nameInput.value = selectedDoc ? selectedDoc.document_name : '';
            } else {
                masterIdInput.value = '';
                nameInput.value = '';
            }
        });
        
        // Add new document button event
        rowElement.querySelector('.btn-add-new-doc').addEventListener('click', function() {
            window.currentNewDocumentRow = rowElement;
        });
    }

    // Load service documents into modal (for edit)
    async function loadServiceDocuments(serviceId) {
        document.getElementById('documentRowsContainer').innerHTML = '';
        deletedDocumentIds = [];
        
        // Load fresh documents for this service
        await loadDocumentsByService(serviceId);
        
        const serviceDocs = documentRequirements[serviceId] || [];
        
        if (serviceDocs.length > 0) {
            isEditMode = true;
            currentServiceId = serviceId;
            document.getElementById('modalTitle').textContent = 'Edit Document Requirements';
            document.getElementById('submitBtnText').textContent = 'Update Requirements';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('serviceId').value = serviceId;
            document.getElementById('serviceSelect').value = serviceId;
            document.getElementById('serviceSelect').disabled = true;
            
            serviceDocs.forEach(doc => {
                addDocumentRow({
                    id: doc.id,
                    document_master_id: doc.document_master_id,
                    document_master: doc.document_master,
                    type: doc.type,
                    status: doc.status
                });
            });
        } else {
            isEditMode = false;
            currentServiceId = serviceId;
            document.getElementById('modalTitle').textContent = 'Add Document Requirements';
            document.getElementById('submitBtnText').textContent = 'Save Requirements';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('serviceId').value = serviceId;
            document.getElementById('serviceSelect').value = serviceId;
            document.getElementById('serviceSelect').disabled = true;
            
            // Add first empty row
            addDocumentRow();
        }
        
        const emptyState = document.getElementById('emptyDocumentState');
        const container = document.getElementById('documentRowsContainer');
        if (container.children.length === 0) {
            emptyState.classList.remove('d-none');
        } else {
            emptyState.classList.add('d-none');
        }
    }

    // Attach edit events to service cards
    function attachEditEvents() {
        document.querySelectorAll('.edit-service-docs').forEach(btn => {
            btn.addEventListener('click', function() {
                const serviceId = this.dataset.serviceId;
                loadServiceDocuments(serviceId);
                new bootstrap.Modal(document.getElementById('documentModal')).show();
            });
        });
    }

    // Filter button events
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadDocumentRequirements();
        });
    });

    // ========== Service change listener ==========
    $('#serviceSelect').on('change', async function () {
        const serviceId = this.value;

        document.getElementById('documentRowsContainer').innerHTML = '';
        document.getElementById('emptyDocumentState').classList.remove('d-none');

        if (!serviceId) {
            serviceDocuments = [];
            return;
        }

        await loadDocumentsByService(serviceId);
        addDocumentRow();
    });

    // ========== Add Document button click ==========
    document.getElementById('addDocumentBtn').addEventListener('click', function() {
        isEditMode = false;
        currentServiceId = null;
        deletedDocumentIds = [];
        document.getElementById('modalTitle').textContent = 'Add Document Requirements';
        document.getElementById('submitBtnText').textContent = 'Save Requirements';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('serviceId').value = '';
        document.getElementById('serviceSelect').value = '';
        document.getElementById('serviceSelect').disabled = false;
        
        // Reset Select2 value when opening modal for add
        $('#serviceSelect').val(null).trigger('change');
        
        document.getElementById('documentRowsContainer').innerHTML = '';
        document.getElementById('emptyDocumentState').classList.remove('d-none');
        serviceDocuments = [];
    });

    // ========== Add Document Row button ==========
    document.getElementById('addDocumentRow').addEventListener('click', function() {
        if (!document.getElementById('serviceSelect').value) {
            showToast('Please select a service first', 'warning');
            return;
        }
        addDocumentRow();
    });

    // Remove document row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-document')) {
            const row = e.target.closest('.document-row');
            const docIdInput = row.querySelector('.document-id-input');
            
            if (isEditMode && currentServiceId && docIdInput && docIdInput.value) {
                deletedDocumentIds.push(docIdInput.value);
            }
            
            // Destroy Select2 before removing the row
            const select = row.querySelector('.document-select');
            if ($(select).data('select2')) {
                $(select).select2('destroy');
            }
            
            row.remove();
            
            const container = document.getElementById('documentRowsContainer');
            if (container.children.length === 0) {
                document.getElementById('emptyDocumentState').classList.remove('d-none');
            }
        }
    });

    // Add New Document Form Submission
    document.getElementById('addNewDocumentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const documentName = document.getElementById('newDocumentName').value.trim();
        if (!documentName) {
            showToast('Please enter a document name', 'danger');
            return;
        }
        
        const submitBtn = document.getElementById('saveNewDocumentBtn');
        const spinner = document.getElementById('saveNewDocumentSpinner');
        
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        
        try {
            console.log('Adding new document via:', routes.addDocument);
            
            const response = await fetch(routes.addDocument, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ document_name: documentName })
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Add document response:', data);
            
            if (data.success) {
                // Add to serviceDocuments
                serviceDocuments.push(data.document);
                
                // Update all document selects (including Select2 instances)
                document.querySelectorAll('.document-select').forEach(select => {
                    const newOption = new Option(data.document.document_name, data.document.id, false, false);
                    $(select).append(newOption).trigger('change');
                    
                    // If this is the row where we clicked "Add New", select it
                    if (window.currentNewDocumentRow && window.currentNewDocumentRow.querySelector('.document-select') === select) {
                        $(select).val(data.document.id).trigger('change');
                    }
                });
                
                bootstrap.Modal.getInstance(document.getElementById('addNewDocumentModal')).hide();
                this.reset();
                showToast('Document added successfully', 'success');
            }
        } catch (error) {
            console.error('Add Document Error:', error);
            showToast(error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            window.currentNewDocumentRow = null;
        }
    });

    // Document Requirements Form Submission
    document.getElementById('documentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const serviceId = document.getElementById('serviceSelect').value;
        if (!serviceId) {
            showToast('Please select a service', 'danger');
            return;
        }
        
        const documents = [];
        const rows = document.querySelectorAll('.document-row');
        
        if (rows.length === 0) {
            showToast('Please add at least one document', 'danger');
            return;
        }
        
        rows.forEach((row, index) => {
            const docId = row.querySelector('.document-id-input').value;
            const documentMasterId = row.querySelector('.document-master-id-input').value;
            const documentName = row.querySelector('.document-name-input').value;
            const type = row.querySelector('.document-type').value;
            const status = row.querySelector('.document-status').value;
            
            if (documentMasterId || documentName) {
                const docData = {
                    document_master_id: documentMasterId ? parseInt(documentMasterId) : null,
                    document_name: documentMasterId ? null : documentName,
                    type: type,
                    status: status
                };
                
                if (docId) {
                    docData.id = parseInt(docId);
                }
                
                documents.push(docData);
            }
        });
        
        if (documents.length === 0) {
            showToast('Please select or enter valid documents', 'danger');
            return;
        }
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        document.getElementById('submitSpinner').classList.remove('d-none');
        
        try {
            let response, data;
            let url, method;
            
            if (isEditMode && currentServiceId) {
                // Update mode
                url = routes.update.replace(':serviceId', currentServiceId);
                method = 'PUT';
                const payload = {
                    documents: documents,
                    deleted_documents: deletedDocumentIds.map(id => parseInt(id))
                };
                console.log('Update URL:', url, 'Payload:', payload);
                response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            } else {
                // Create mode
                url = routes.store;
                method = 'POST';
                const payload = {
                    service_id: parseInt(serviceId),
                    documents: documents
                };
                console.log('Store URL:', url, 'Payload:', payload);
                response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            }
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                
                // Try to parse as JSON if possible
                try {
                    data = JSON.parse(errorText);
                    let errorMsg = data.message || 'Operation failed';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join(', ');
                    }
                    throw new Error(errorMsg);
                } catch (e) {
                    throw new Error(`Server error: ${response.status}`);
                }
            }
            
            data = await response.json();
            console.log('Form response:', data);
            
            if (data.success) {
                showToast(isEditMode ? 'Document requirements updated successfully' : 'Document requirements added successfully', 'success');
                bootstrap.Modal.getInstance(document.getElementById('documentModal')).hide();
                loadDocumentRequirements();
                this.reset();
            } else {
                throw new Error(data.message || 'Operation failed');
            }
        } catch (err) {
            console.error('Form Error:', err);
            showToast(err.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            document.getElementById('submitSpinner').classList.add('d-none');
        }
    });

    // ========== GLOBAL SEARCH ==========
    document.getElementById('searchRequirements').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.service-card').forEach(card => {
            const serviceName = card.querySelector('.service-title')?.textContent.toLowerCase() || '';
            const docItems = Array.from(card.querySelectorAll('.document-list li')).map(li => li.textContent.toLowerCase());
            const matches = serviceName.includes(searchTerm) || docItems.some(text => text.includes(searchTerm));
            card.style.display = matches ? 'block' : 'none';
        });
    });

    // Initial load
    loadDocumentRequirements();
});
</script>
@endpush