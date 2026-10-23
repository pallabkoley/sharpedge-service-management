@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Service Master</h2>
                <p class="text-muted mb-0">Manage all services and their details</p>
            </div>
            <button type="button" class="btn btn-primary add-service-btn" data-bs-toggle="modal" data-bs-target="#serviceModal">
                <i class="bi bi-plus-lg"></i>
                <span>Add Service</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $totalServices }}</div>
                        <div class="card-label">Total Services</div>
                    </div>
                    <div class="card-icon bg-primary"><i class="bi bi-list-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $activeServices }}</div>
                        <div class="card-label">Active Services</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;"><i class="bi bi-check-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $inactiveServices }}</div>
                        <div class="card-label">Inactive Services</div>
                    </div>
                    <div class="card-icon" style="background-color: #f59e0b;"><i class="bi bi-pause-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $categories->count() }}</div>
                        <div class="card-label">Categories</div>
                    </div>
                    <div class="card-icon" style="background-color: #8b5cf6;"><i class="bi bi-folder"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Table & Controls -->
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <!-- Tabs -->
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-all-tab" data-status="all">All Services</button>
                        <button class="nav-link" id="nav-active-tab" data-status="active">Active</button>
                        <button class="nav-link" id="nav-inactive-tab" data-status="inactive">Inactive</button>
                    </div>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <!-- Category Filter -->
                <select class="form-select form-select-sm" id="categoryFilter" style="width: 180px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <!-- Search -->
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search services...">
                </div>
            </div>
        </div>

        <!-- Table Container (Data loads here via AJAX) -->
        <div id="servicesTableContainer">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading services...</p>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Service Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add New Service</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="serviceForm" method="POST">
                @csrf
                <input type="hidden" id="serviceId" name="id">
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serviceCode" class="form-label required">Service Code</label>
                                <input type="text" class="form-control" id="serviceCode" name="service_code" required placeholder="e.g., SER-001">
                                <div class="form-text">Leave blank to auto-generate</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serviceName" class="form-label required">Service Name</label>
                                <input type="text" class="form-control" id="serviceName" name="service_name" required placeholder="e.g., GST Registration">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serviceCategory" class="form-label required">Category</label>
                                <select class="form-select" id="serviceCategory" name="service_category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Fees -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="govtFee" class="form-label">Government Fee</label>
                                <input type="text" class="form-control" id="govtFee" name="govt_fee" placeholder="e.g., ₹5000 or 'As per ITR'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ourFee" class="form-label">Our Fee (₹)</label>
                                <input type="number" class="form-control" id="ourFee" name="our_fee" placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <!-- Tax & Business Rules -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label required">Taxable?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_taxable" id="isTaxableYes" value="1" required>
                                <label class="form-check-label" for="isTaxableYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_taxable" id="isTaxableNo" value="0" checked>
                                <label class="form-check-label" for="isTaxableNo">No</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="taxPercentage" class="form-label">Tax Percentage (%)</label>
                                <input type="number" class="form-control" id="taxPercentage" name="tax_percentage" placeholder="18" step="0.01" min="0" disabled>
                                <div class="form-text">Required if taxable</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="processingTime" class="form-label">Processing Time</label>
                                <input type="text" class="form-control" id="processingTime" name="processing_time" placeholder="e.g., 5-7 days">
                            </div>
                        </div>
                    </div>
                    <!-- Toggle Settings -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="allow_discount" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="allowDiscount" name="allow_discount" value="1">
                                <label class="form-check-label" for="allowDiscount">Allow Discount</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="visible_to_client" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="visibleToClient" name="visible_to_client" value="1" checked>
                                <label class="form-check-label" for="visibleToClient">Visible to Client</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="serviceStatus" class="form-label required">Status</label>
                                <select class="form-select" id="serviceStatus" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter service description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Service</span>
                        <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteServiceName" class="text-danger"></strong>?</p>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                        <span id="deleteBtnText">Delete Service</span>
                        <span class="spinner-border spinner-border-sm d-none" id="deleteSpinner"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Document Requirements Modal -->
<div class="modal fade" id="documentRequirementsModal" tabindex="-1" aria-labelledby="documentRequirementsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentRequirementsModalLabel">Document Requirements - <span id="docReqServiceName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="documentRequirementsList">
                    <!-- Documents will be loaded here -->
                </div>
                <div id="noDocumentRequirements" class="text-center py-4" style="display: none;">
                    <p class="text-muted">No document requirements added for this service.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Work Process Modal -->
<div class="modal fade" id="workProcessModal" tabindex="-1" aria-labelledby="workProcessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workProcessModalLabel">Work Process - <span id="workProcessServiceName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="workProcessStepsList">
                    <!-- Steps will be loaded here -->
                </div>
                <div id="noWorkProcess" class="text-center py-4" style="display: none;">
                    <p class="text-muted">No process created for this service.</p>
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
document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = "{{ url('') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let serviceIdToDelete = null;

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

    // Toggle Tax Percentage field
    function toggleTaxPercentage() {
        const taxPercentageField = document.getElementById('taxPercentage');
        const isTaxableYes = document.getElementById('isTaxableYes').checked;
        
        if (isTaxableYes) {
            taxPercentageField.disabled = false;
            taxPercentageField.required = true;
        } else {
            taxPercentageField.disabled = true;
            taxPercentageField.required = false;
            taxPercentageField.value = '';
        }
    }

    // Show Document Requirements Modal
    function showDocumentRequirementsModal(service) {
        const modal = new bootstrap.Modal(document.getElementById('documentRequirementsModal'));
        const title = document.getElementById('docReqServiceName');
        const listContainer = document.getElementById('documentRequirementsList');
        const noDocs = document.getElementById('noDocumentRequirements');
        
        title.textContent = service.service_name;
        
        if (service.document_requirements && service.document_requirements.length > 0) {
            noDocs.style.display = 'none';
            
            let html = `<div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Document Name</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>`;
            
            service.document_requirements.forEach((doc, index) => {
                // FIXED: Changed doc.document_master.name to doc.document_master.document_name
                const documentName = doc.document_master 
                    ? doc.document_master.document_name 
                    : 'N/A';
                const typeBadge = doc.type === 'mandatory' 
                    ? '<span class="badge bg-danger">MANDATORY</span>'
                    : '<span class="badge bg-warning">OPTIONAL</span>';
                const statusBadge = doc.status === 'active'
                    ? '<span class="badge bg-success">ACTIVE</span>'
                    : '<span class="badge bg-secondary">INACTIVE</span>';
                
                html += `<tr>
                    <td>${index + 1}</td>
                    <td>${documentName}</td>
                    <td>${typeBadge}</td>
                    <td>${statusBadge}</td>
                </tr>`;
            });
            
            html += `</tbody></table></div>`;
            listContainer.innerHTML = html;
        } else {
            listContainer.innerHTML = '';
            noDocs.style.display = 'block';
        }
        
        modal.show();
    }

    // Show Work Process Modal
    function showWorkProcessModal(service) {
        const modal = new bootstrap.Modal(document.getElementById('workProcessModal'));
        const title = document.getElementById('workProcessServiceName');
        const listContainer = document.getElementById('workProcessStepsList');
        const noProcess = document.getElementById('noWorkProcess');
        
        title.textContent = service.service_name;
        
        if (service.work_process && service.work_process.steps && service.work_process.steps.length > 0) {
            noProcess.style.display = 'none';
            
            let html = `<div class="list-group">`;
            
            // FIXED: Changed a.order/b.order to a.step_order/b.step_order
            // FIXED: Changed step.name to step.step_name
            service.work_process.steps
                .sort((a, b) => a.step_order - b.step_order)
                .forEach((step, index) => {
                    html += `<div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                                        ${index + 1}
                                    </span>
                                    <h6 class="mb-0">${step.step_name}</h6>
                                </div>
                                ${step.client_instruction ? `<p class="mb-0 mt-1 text-muted small">${step.client_instruction}</p>` : ''}
                            </div>
                            <span class="badge ${step.status === 'active' ? 'bg-success' : 'bg-secondary'}">
                                ${step.status}
                            </span>
                        </div>
                    </div>`;
                });
            
            html += `</div>`;
            listContainer.innerHTML = html;
        } else {
            listContainer.innerHTML = '';
            noProcess.style.display = 'block';
        }
        
        modal.show();
    }

    // Initialize tax percentage toggle
    document.getElementById('isTaxableYes').addEventListener('change', toggleTaxPercentage);
    document.getElementById('isTaxableNo').addEventListener('change', toggleTaxPercentage);

    // Load Services via AJAX
    function loadServices() {
        const status = document.querySelector('.nav-tabs .active')?.dataset.status || 'all';
        const search = document.getElementById('searchInput').value;
        const category_id = document.getElementById('categoryFilter').value;
        
        const params = new URLSearchParams({
            status: status,
            search: search,
            category_id: category_id,
            ajax: 1
        });

        fetch(`${BASE_URL}/admin/services?${params}`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest', 
                'Accept': 'application/json' 
            }
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                let html = `<div class="table-responsive"><table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Govt. Fee</th>
                            <th>Our Fee</th>
                            <th>Processing Time</th>
                            <th>Document Requirements</th>
                            <th>Work Process</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>`;
                
                data.services.forEach(s => {
                    // Count document requirements
                    const docCount = s.document_requirements ? s.document_requirements.length : 0;
                    
                    // Check if work process exists
                    const hasWorkProcess = s.work_process && s.work_process.steps && s.work_process.steps.length > 0;
                    
                    html += `<tr data-id="${s.id}">
                        <td><span class="badge bg-secondary">${s.service_code}</span></td>
                        <td>
                            <strong>${s.service_name}</strong>
                            ${s.description ? `<br><small class="text-muted">${s.description.substring(0,50)}...</small>` : ''}
                        </td>
                        <td><span class="badge bg-light text-dark">${s.category?.name || 'N/A'}</span></td>
                        <td>${s.govt_fee ? `<span class="badge bg-info">${s.govt_fee}</span>` : '-'}</td>
                        <td>${s.our_fee ? `₹${parseFloat(s.our_fee).toFixed(2)}` : '-'}</td>
                        <td>${s.processing_time || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info view-document-requirements" 
                                data-id="${s.id}" 
                                data-name="${s.service_name}"
                                data-service='${JSON.stringify(s).replace(/'/g, "&apos;")}'
                                ${docCount === 0 ? 'disabled' : ''}>
                                View (${docCount})
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info view-work-process" 
                                data-id="${s.id}" 
                                data-name="${s.service_name}"
                                data-service='${JSON.stringify(s).replace(/'/g, "&apos;")}'
                                ${!hasWorkProcess ? 'disabled' : ''}>
                                ${hasWorkProcess ? 'View' : 'No Process'}
                            </button>
                        </td>
                        <td>
                            <span class="badge ${s.status == 'active' ? 'bg-success' : 'bg-warning'}">
                                ${s.status}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-service" data-id="${s.id}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-service" 
                                data-id="${s.id}" data-name="${s.service_name}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                
                if (data.services.length === 0) {
                    html += `<tr><td colspan="10" class="text-center py-4 text-muted">No services found</td></tr>`;
                }
                
                html += `</tbody></table></div>`;
                document.getElementById('servicesTableContainer').innerHTML = html;
                attachRowEvents();
            }
        })
        .catch(err => {
            console.error('Load services error:', err);
            showToast('Failed to load services', 'danger');
        });
    }

    // Attach events to table rows
    function attachRowEvents() {
        // Edit buttons
        document.querySelectorAll('.edit-service').forEach(btn => {
            btn.addEventListener('click', function() {
                const serviceId = this.dataset.id;
                
                fetch(`${BASE_URL}/admin/services/${serviceId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        const s = data.service;
                        
                        // Populate form fields
                        document.getElementById('modalTitle').textContent = 'Edit Service';
                        document.getElementById('submitBtnText').textContent = 'Update Service';
                        document.getElementById('formMethod').value = 'PUT';
                        document.getElementById('serviceId').value = s.id;
                        document.getElementById('serviceCode').value = s.service_code;
                        document.getElementById('serviceName').value = s.service_name;
                        document.getElementById('serviceCategory').value = s.service_category_id;
                        document.getElementById('govtFee').value = s.govt_fee || '';
                        document.getElementById('ourFee').value = s.our_fee || '';
                        document.getElementById('processingTime').value = s.processing_time || '';
                        document.getElementById('description').value = s.description || '';
                        document.getElementById('serviceStatus').value = s.status;
                        document.getElementById('allowDiscount').checked = s.allow_discount == 1;
                        document.getElementById('visibleToClient').checked = s.visible_to_client == 1;
                        
                        // Handle tax fields
                        if (s.is_taxable == 1) {
                            document.getElementById('isTaxableYes').checked = true;
                            document.getElementById('isTaxableNo').checked = false;
                            document.getElementById('taxPercentage').value = s.tax_percentage || '';
                        } else {
                            document.getElementById('isTaxableYes').checked = false;
                            document.getElementById('isTaxableNo').checked = true;
                            document.getElementById('taxPercentage').value = '';
                        }
                        
                        // Update tax percentage field state
                        toggleTaxPercentage();
                        
                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
                        modal.show();
                    } else {
                        showToast(data.message || 'Failed to load service', 'danger');
                    }
                })
                .catch(err => {
                    console.error('Edit fetch error:', err);
                    showToast('Failed to load service data', 'danger');
                });
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-service').forEach(btn => {
            btn.addEventListener('click', function() {
                serviceIdToDelete = this.dataset.id;
                document.getElementById('deleteServiceName').textContent = this.dataset.name;
                document.getElementById('deleteForm').action = `${BASE_URL}/admin/services/${serviceIdToDelete}`;

                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });

        // Document Requirements buttons
        document.querySelectorAll('.view-document-requirements').forEach(btn => {
            btn.addEventListener('click', function() {
                const serviceData = JSON.parse(this.dataset.service.replace(/&apos;/g, "'"));
                showDocumentRequirementsModal(serviceData);
            });
        });

        // Work Process buttons
        document.querySelectorAll('.view-work-process').forEach(btn => {
            btn.addEventListener('click', function() {
                const serviceData = JSON.parse(this.dataset.service.replace(/&apos;/g, "'"));
                showWorkProcessModal(serviceData);
            });
        });
    }

    // Form Submission
    document.getElementById('serviceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitBtn');
        const method = document.getElementById('formMethod').value;
        const serviceId = document.getElementById('serviceId').value;
        
        // Determine URL
        const url = method === 'PUT'
            ? `${BASE_URL}/admin/services/${serviceId}`
            : `${BASE_URL}/admin/services`;

        submitBtn.disabled = true;
        document.getElementById('submitSpinner').classList.remove('d-none');

        // Always use POST and include _method for Laravel method spoofing
        formData.set('_method', method);

        fetch(url, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': csrfToken, 
                'Accept': 'application/json' 
            },
            body: formData
        })
        .then(async r => {
            const data = await r.json();
            if(!r.ok) {
                let errorMsg = 'Validation failed';
                if(data.errors) {
                    errorMsg = Object.values(data.errors).flat().join(', ');
                }
                throw new Error(errorMsg);
            }
            return data;
        })
        .then(data => {
            if(data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('serviceModal')).hide();
                this.reset();
                loadServices();
                
                // Update stats if needed
                if (method === 'POST') {
                    // Increment total services count for new service
                    const totalEl = document.querySelector('.stats-card .card-value');
                    if (totalEl) {
                        totalEl.textContent = parseInt(totalEl.textContent) + 1;
                    }
                }
            }
        })
        .catch(err => {
            showToast(err.message, 'danger');
            console.error('Form Error:', err);
        })
        .finally(() => {
            submitBtn.disabled = false;
            document.getElementById('submitSpinner').classList.add('d-none');
        });
    });

    // Delete Form Submission
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const deleteBtn = document.getElementById('confirmDeleteBtn');
        deleteBtn.disabled = true;
        document.getElementById('deleteSpinner').classList.remove('d-none');

        fetch(this.action, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': csrfToken, 
                'Accept': 'application/json' 
            }
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                loadServices();
                
                // Decrement total services count
                const totalEl = document.querySelector('.stats-card .card-value');
                if (totalEl) {
                    totalEl.textContent = parseInt(totalEl.textContent) - 1;
                }
            } else {
                showToast(data.message || 'Delete failed', 'danger');
            }
        })
        .catch(err => {
            showToast('Delete failed', 'danger');
            console.error(err);
        })
        .finally(() => {
            deleteBtn.disabled = false;
            document.getElementById('deleteSpinner').classList.add('d-none');
        });
    });

    // Event Listeners for filters
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadServices, 500);
    });
    
    document.getElementById('categoryFilter').addEventListener('change', loadServices);
    
    // Tab switching
    document.querySelectorAll('.nav-tabs button').forEach(tab => {
        tab.addEventListener('click', function() { 
            document.querySelectorAll('.nav-tabs button').forEach(t => t.classList.remove('active'));
            this.classList.add('active'); 
            loadServices(); 
        });
    });

    // Reset form when modal is hidden
    document.getElementById('serviceModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('serviceForm').reset();
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('serviceId').value = '';
        document.getElementById('modalTitle').textContent = 'Add New Service';
        document.getElementById('submitBtnText').textContent = 'Save Service';
        document.getElementById('visibleToClient').checked = true;
        document.getElementById('isTaxableNo').checked = true;
        toggleTaxPercentage(); // Reset tax field state
    });

    // Add Service button click
    document.querySelector('.add-service-btn').addEventListener('click', function() {
        // Clear any previous values
        document.getElementById('serviceForm').reset();
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('serviceId').value = '';
        document.getElementById('modalTitle').textContent = 'Add New Service';
        document.getElementById('submitBtnText').textContent = 'Save Service';
        document.getElementById('visibleToClient').checked = true;
        document.getElementById('isTaxableNo').checked = true;
        toggleTaxPercentage(); // Reset tax field state
    });

    // Initial load
    loadServices();
});
</script>
@endpush