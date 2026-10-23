@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="header">
        <h2>Work Processes</h2>
        <p class="text-muted mb-0">Manage work processes and their steps for different services</p>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $processes->count() }}</div>
                        <div class="text-muted">Total Processes</div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $processes->where('status', 'active')->count() }}</div>
                        <div class="text-muted">Active Processes</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @php
                            $totalSteps = $processes->sum(function($process) {
                                return $process->steps->count();
                            });
                        @endphp
                        <div class="card-value">{{ $totalSteps }}</div>
                        <div class="text-muted">Total Steps</div>
                    </div>
                    <div class="card-icon" style="background-color: #8b5cf6;">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $services->count() }}</div>
                        <div class="text-muted">Services</div>
                    </div>
                    <div class="card-icon" style="background-color: #f59e0b;">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Processes Card -->
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title mb-0">Work Processes Management</h3>
            <div>
                <button type="button" class="btn btn-primary" id="addProcessBtn">
                    <i class="bi bi-plus-circle me-1"></i> Add New Process
                </button>
            </div>
        </div>

        <!-- Processes Table -->
        <div class="table-responsive">
            <table class="table table-hover datatable" id="processesTable">
                <thead>
                    <tr>
                        <th>Process Name</th>
                        <th>Service</th>
                        <th>Steps</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($processes as $process)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $process->name }}</div>
                            <small class="text-muted">Created: {{ $process->created_at->format('M d, Y') }}</small>
                        </td>
                        <td>
                            @if($process->service)
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $process->service->name }}</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">No Service</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info bg-opacity-10 text-info me-2">{{ $process->steps->count() }} steps</span>
                                <button class="btn btn-sm btn-outline-info view-steps" 
                                        data-id="{{ $process->id }}"
                                        data-name="{{ $process->name }}"
                                        data-route="{{ route('admin.work-processes.steps', $process->id) }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input status-toggle" 
                                       type="checkbox" 
                                       data-id="{{ $process->id }}"
                                       {{ $process->status == 'active' ? 'checked' : '' }}
                                       role="switch"
                                       id="status_{{ $process->id }}">
                                <label class="form-check-label ms-2" for="status_{{ $process->id }}">
                                    @if($process->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted">
                                {{ $process->creator->name ?? 'System' }}
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-process-btn" 
                                        data-id="{{ $process->id }}"
                                        data-route="{{ route('admin.work-processes.edit', $process->id) }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-process-btn" 
                                        data-id="{{ $process->id }}"
                                        data-name="{{ $process->name }}"
                                        data-route="{{ route('admin.work-processes.destroy', $process->id) }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if($processes->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 48px; color: #cbd5e1;"></i>
                            <p class="text-muted mt-3">No work processes found. Create your first process!</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Process Modal -->
<div class="modal fade" id="addProcessModal" tabindex="-1" aria-labelledby="addProcessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProcessModalLabel">Add New Work Process</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addProcessForm" method="POST" action="{{ route('admin.work-processes.store') }}">
                @csrf
                <div class="modal-body">
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="service_id" class="form-label">Select Service *</label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">Select Service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Process Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g., GST Registration Process" required>
                            </div>
                        </div>
                    </div>

                    <!-- Steps Section -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold">Process Steps *</label>
                            <button type="button" class="btn btn-sm btn-success" id="addStepBtn">
                                <i class="bi bi-plus-circle me-1"></i> Add Step
                            </button>
                        </div>
                        
                        <div id="stepsContainer">
                            <!-- Step template -->
                            <div class="step-item border rounded p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Step Name *</label>
                                            <input type="text" class="form-control step-name" 
                                                   name="steps[0][step_name]" 
                                                   placeholder="e.g., Document Collection" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Estimated Days</label>
                                            <input type="number" class="form-control step-days" 
                                                   name="steps[0][estimated_days]" 
                                                   placeholder="e.g., 3" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Instruction</label>
                                            <textarea class="form-control client-instruction" 
                                                      name="steps[0][client_instruction]" 
                                                      placeholder="Instructions for the client regarding this step..."
                                                      rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-step" disabled>
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Process</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Process Modal -->
<div class="modal fade" id="editProcessModal" tabindex="-1" aria-labelledby="editProcessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProcessModalLabel">Edit Work Process</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProcessForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_process_id" name="id">
                <div class="modal-body">
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_service_id" class="form-label">Select Service *</label>
                                <select class="form-select" id="edit_service_id" name="service_id" required>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Process Name *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status *</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Steps Section -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold">Process Steps *</label>
                            <button type="button" class="btn btn-sm btn-success" id="addEditStepBtn">
                                <i class="bi bi-plus-circle me-1"></i> Add Step
                            </button>
                        </div>
                        
                        <div id="editStepsContainer">
                            <!-- Steps will be dynamically added here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Process</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Steps Modal -->
<div class="modal fade" id="viewStepsModal" tabindex="-1" aria-labelledby="viewStepsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewStepsModalLabel">Process Steps</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="stepsList">
                    <!-- Steps will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProcessModal" tabindex="-1" aria-labelledby="deleteProcessModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteProcessName" class="text-danger"></strong>?</p>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteProcessForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                        Delete Process
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stats-card {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
    }
    
    .card-value {
        font-size: 24px;
        font-weight: bold;
        color: #1e293b;
    }
    
    .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    
    .section-card {
        background-color: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .section-title {
        color: #1e293b;
        font-weight: 600;
    }
    
    .step-item {
        background-color: #f8fafc;
        border-left: 4px solid #3b82f6 !important;
        transition: all 0.3s ease;
    }
    
    .step-item:hover {
        background-color = #f1f5f9;
    }
    
    #processesTable tbody tr {
        transition: background-color 0.2s;
    }
    
    #processesTable tbody tr:hover {
        background-color: #f8fafc;
    }
    
    .form-check.form-switch {
        display: flex;
        align-items: center;
    }
    
    .form-check-input.status-toggle {
        margin-top: 0;
        cursor: pointer;
    }
    
    .form-check-label {
        margin-left: 0.5rem;
    }
    
    .timeline-step {
        padding: 15px;
        border-left: 3px solid #3b82f6;
        margin-bottom: 15px;
        background: #f8fafc;
        border-radius: 0 8px 8px 0;
    }
    
    .timeline-step:last-child {
        margin-bottom: 0;
    }
    
    .client-instruction-box {
        background-color: #f0f9ff;
        border-left: 3px solid #0ea5e9;
        padding: 12px;
        margin-top: 10px;
        border-radius: 0 6px 6px 0;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Toast Notification Function
    function showToast(message, type = 'success') {
        const toast = `<div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex"><div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
        document.body.insertAdjacentHTML('beforeend', toast);
        const toastEl = document.querySelector('.toast:last-child');
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        bsToast.show();
    }
    
    // Add Process Button
    document.getElementById('addProcessBtn').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('addProcessModal'));
        modal.show();
    });
    
    // Step counter for add form
    let stepCounter = 1;
    
    // Add step button functionality
    document.getElementById('addStepBtn').addEventListener('click', function() {
        const container = document.getElementById('stepsContainer');
        const stepTemplate = `
            <div class="step-item border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Step Name *</label>
                            <input type="text" class="form-control step-name" 
                                   name="steps[${stepCounter}][step_name]" 
                                   placeholder="e.g., Document Collection" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Estimated Days</label>
                            <input type="number" class="form-control step-days" 
                                   name="steps[${stepCounter}][estimated_days]" 
                                   placeholder="e.g., 3" min="0">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Instruction</label>
                            <textarea class="form-control client-instruction" 
                                      name="steps[${stepCounter}][client_instruction]" 
                                      placeholder="Instructions for the client regarding this step..."
                                      rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-step">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', stepTemplate);
        stepCounter++;
        
        updateRemoveButtons();
    });
    
    // Remove step functionality for add form
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-step')) {
            const stepItem = e.target.closest('.step-item');
            if (stepItem && document.querySelectorAll('.step-item').length > 1) {
                stepItem.remove();
                updateRemoveButtons();
            }
        }
    });
    
    function updateRemoveButtons() {
        const steps = document.querySelectorAll('.step-item');
        steps.forEach((step, index) => {
            const removeBtn = step.querySelector('.remove-step');
            if (removeBtn) {
                removeBtn.disabled = steps.length <= 1;
            }
        });
    }
    
    // Initialize remove buttons
    updateRemoveButtons();
    
    // Handle add form submission
    document.getElementById('addProcessForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Create Process';
            
            if(data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('addProcessModal')).hide();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Failed to create process', 'danger');
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Create Process';
            showToast('Failed to create process: ' + error.message, 'danger');
        });
    });
    
    // Edit Process Button - USING ROUTE NAMES
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-process-btn')) {
            const button = e.target.closest('.edit-process-btn');
            const processId = button.getAttribute('data-id');
            const routeUrl = button.getAttribute('data-route');
            
            console.log('Loading edit data for process ID:', processId, 'Route:', routeUrl);
            
            // Clear previous steps
            document.getElementById('editStepsContainer').innerHTML = '';
            
            // Load process data via AJAX using route URL
            fetch(routeUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Failed to parse JSON:', text.substring(0, 200));
                        throw new Error('Invalid JSON response');
                    }
                });
            })
            .then(data => {
                console.log('Edit response data:', data);
                
                if (data.success && data.data) {
                    const process = data.data;
                    
                    // Populate basic info
                    document.getElementById('edit_process_id').value = process.id;
                    document.getElementById('edit_name').value = process.name;
                    document.getElementById('edit_service_id').value = process.service_id;
                    document.getElementById('edit_status').value = process.status;
                    
                    // Set form action using route
                    const updateRoute = "{{ route('admin.work-processes.update', ':id') }}".replace(':id', process.id);
                    document.getElementById('editProcessForm').action = updateRoute;
                    
                    // Populate steps
                    if (process.steps && process.steps.length > 0) {
                        process.steps.forEach((step, index) => {
                            addEditStep(index, step.step_name, step.estimated_days, step.client_instruction);
                        });
                    } else {
                        // Add one empty step
                        addEditStep(0);
                    }
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('editProcessModal'));
                    modal.show();
                } else {
                    showToast(data.message || 'Failed to load process data', 'danger');
                }
            })
            .catch(error => {
                console.error('Error loading process:', error);
                showToast('Failed to load process data: ' + error.message, 'danger');
            });
        }
    });
    
    // Add step in edit form
    document.getElementById('addEditStepBtn').addEventListener('click', function() {
        const index = document.querySelectorAll('#editStepsContainer .step-item').length;
        addEditStep(index);
    });
    
    function addEditStep(index, name = '', days = '', clientInstruction = '') {
        const container = document.getElementById('editStepsContainer');
        const stepTemplate = `
            <div class="step-item border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Step Name *</label>
                            <input type="text" class="form-control step-name" 
                                   name="steps[${index}][step_name]" 
                                   value="${name.replace(/"/g, '&quot;')}" 
                                   placeholder="e.g., Document Collection" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Estimated Days</label>
                            <input type="number" class="form-control step-days" 
                                   name="steps[${index}][estimated_days]" 
                                   value="${days}" 
                                   placeholder="e.g., 3" min="0">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Instruction</label>
                            <textarea class="form-control client-instruction" 
                                      name="steps[${index}][client_instruction]" 
                                      placeholder="Instructions for the client regarding this step..."
                                      rows="2">${clientInstruction ? clientInstruction.replace(/</g, '&lt;').replace(/>/g, '&gt;') : ''}</textarea>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-edit-step">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', stepTemplate);
    }
    
    // Remove edit step
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-edit-step')) {
            const stepItem = e.target.closest('.step-item');
            if (stepItem) {
                stepItem.remove();
                // Re-index the remaining steps
                const container = document.getElementById('editStepsContainer');
                const steps = container.querySelectorAll('.step-item');
                steps.forEach((step, index) => {
                    const nameInput = step.querySelector('[name*="[step_name]"]');
                    const daysInput = step.querySelector('[name*="[estimated_days]"]');
                    const instructionInput = step.querySelector('[name*="[client_instruction]"]');
                    
                    if (nameInput) {
                        nameInput.name = `steps[${index}][step_name]`;
                    }
                    if (daysInput) {
                        daysInput.name = `steps[${index}][estimated_days]`;
                    }
                    if (instructionInput) {
                        instructionInput.name = `steps[${index}][client_instruction]`;
                    }
                });
            }
        }
    });
    
    // Handle edit form submission
    document.getElementById('editProcessForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Update Process';
            
            if(data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('editProcessModal')).hide();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Failed to update process', 'danger');
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Update Process';
            showToast('Failed to update process: ' + error.message, 'danger');
        });
    });
    
    // View Steps Button - USING ROUTE NAMES
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-steps')) {
            const button = e.target.closest('.view-steps');
            const processId = button.getAttribute('data-id');
            const processName = button.getAttribute('data-name');
            const routeUrl = button.getAttribute('data-route');
            
            console.log('Fetching steps for process ID:', processId, 'Route:', routeUrl);
            
            // Show loading state
            const stepsList = document.getElementById('stepsList');
            stepsList.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading steps for "${processName}"...</p>
                </div>
            `;
            
            // Show modal immediately
            const modal = new bootstrap.Modal(document.getElementById('viewStepsModal'));
            modal.show();
            
            // Fetch steps using route URL
            fetch(routeUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                return response.text().then(text => {
                    console.log('Response for process', processId, ':', text.substring(0, 500));
                    
                    // Check if it's HTML (starts with <)
                    if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
                        console.error('Server returned HTML instead of JSON. Full response:', text);
                        throw new Error('Server error: Received HTML instead of JSON data. This might be a login page or error page.');
                    }
                    
                    try {
                        return JSON.parse(text);
                    } catch (parseError) {
                        throw new Error('Failed to parse JSON response. Server might be returning an error.');
                    }
                });
            })
            .then(data => {
                console.log('Parsed steps data:', data);
                
                if (data.success) {
                    const steps = data.steps || [];
                    let stepsHTML = `
                        <div class="mb-4">
                            <h5>Process: <strong>${processName}</strong></h5>
                            <p class="text-muted mb-0">Total Steps: ${steps.length}</p>
                        </div>
                    `;
                    
                    if (steps.length > 0) {
                        stepsHTML += '<div class="step-timeline">';
                        steps.forEach((step, index) => {
                            // Escape HTML special characters in instruction except we will add links
                            let instructionText = step.client_instruction || '';
                            // First escape HTML entities to prevent XSS
                            instructionText = instructionText.replace(/[&<>]/g, function(m) {
                                if (m === '&') return '&amp;';
                                if (m === '<') return '&lt;';
                                if (m === '>') return '&gt;';
                                return m;
                            });
                            // Then replace URLs with anchor tags
                            instructionText = instructionText.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
                            
                            stepsHTML += `
                                <div class="timeline-step">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">Step ${index + 1}: ${step.step_name}</h6>
                                            <p class="mb-0 text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                Estimated Time: ${step.estimated_days ? step.estimated_days + ' days' : 'Not specified'}
                                            </p>
                                            <p class="mb-0 text-muted small">
                                                <i class="bi bi-sort-numeric-up me-1"></i>
                                                Order: ${step.step_order}
                                            </p>
                                        </div>
                                        <span class="badge bg-primary">Step ${index + 1}</span>
                                    </div>
                                    ${step.client_instruction ? `
                                    <div class="client-instruction-box mt-2">
                                        <strong><i class="bi bi-chat-left-text me-1"></i>Instruction:</strong>
                                        <p class="mb-0 mt-1">${instructionText}</p>
                                    </div>
                                    ` : ''}
                                </div>
                            `;
                        });
                        stepsHTML += '</div>';
                    } else {
                        stepsHTML += `
                            <div class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 48px; color: #cbd5e1;"></i>
                                <p class="text-muted mt-3">No steps defined for this process</p>
                            </div>
                        `;
                    }
                    
                    stepsList.innerHTML = stepsHTML;
                } else {
                    stepsList.innerHTML = `
                        <div class="alert alert-danger">
                            <h6>Error Loading Steps</h6>
                            <p class="mb-0">${data.message || 'Unknown error'}</p>
                            <p class="mt-2 small text-muted">
                                Process ID: ${processId}<br>
                                Process Name: ${processName}
                            </p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching steps:', error);
                
                stepsList.innerHTML = `
                    <div class="alert alert-danger">
                        <h6>Error Loading Steps</h6>
                        <p class="mb-0">${error.message}</p>
                        <p class="mt-2 small text-muted">
                            Route: ${routeUrl}<br>
                            Process ID: ${processId}<br>
                            Process Name: ${processName}
                        </p>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="window.open('${routeUrl}', '_blank')">
                                Open URL in New Tab
                            </button>
                        </div>
                    </div>
                `;
            });
        }
    });
    
    // Delete Process Button - USING ROUTE NAMES
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-process-btn')) {
            const button = e.target.closest('.delete-process-btn');
            const processId = button.getAttribute('data-id');
            const processName = button.getAttribute('data-name');
            const routeUrl = button.getAttribute('data-route');
            
            document.getElementById('deleteProcessName').textContent = processName;
            document.getElementById('deleteProcessForm').action = routeUrl;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteProcessModal'));
            modal.show();
        }
    });
    
    // Delete Form Submission
    document.getElementById('deleteProcessForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
        
        fetch(this.action, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': csrfToken, 
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'DELETE'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Delete Process';
            
            if(data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('deleteProcessModal')).hide();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Delete failed', 'danger');
            }
        })
        .catch(error => {
            console.error(error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Delete Process';
            showToast('Delete failed: ' + error.message, 'danger');
        });
    });
    
    // Status Toggle - USING ROUTE NAMES
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('status-toggle')) {
            const toggle = e.target;
            const processId = toggle.getAttribute('data-id');
            const newStatus = toggle.checked ? 'active' : 'inactive';
            
            // Update the label immediately for better UX
            const label = toggle.closest('.form-check').querySelector('.form-check-label span');
            if (label) {
                if (newStatus === 'active') {
                    label.textContent = 'Active';
                    label.className = 'badge bg-success';
                } else {
                    label.textContent = 'Inactive';
                    label.className = 'badge bg-secondary';
                }
            }
            
            // Send AJAX request to change status using route
            const changeStatusRoute = "{{ route('admin.work-processes.change-status') }}";
            fetch(changeStatusRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: processId,
                    status: newStatus
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    // Revert the toggle if failed
                    toggle.checked = !toggle.checked;
                    // Revert the label
                    if (label) {
                        if (toggle.checked) {
                            label.textContent = 'Active';
                            label.className = 'badge bg-success';
                        } else {
                            label.textContent = 'Inactive';
                            label.className = 'badge bg-secondary';
                        }
                    }
                    showToast('Failed to update status: ' + (data.message || 'Unknown error'), 'danger');
                } else {
                    showToast('Status updated successfully', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert the toggle
                toggle.checked = !toggle.checked;
                // Revert the label
                if (label) {
                    if (toggle.checked) {
                        label.textContent = 'Active';
                        label.className = 'badge bg-success';
                    } else {
                        label.textContent = 'Inactive';
                        label.className = 'badge bg-secondary';
                    }
                }
                showToast('Failed to update status: ' + error.message, 'danger');
            });
        }
    });
    
    // Reset modals when hidden
    document.getElementById('addProcessModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('addProcessForm').reset();
        document.getElementById('stepsContainer').innerHTML = `
            <div class="step-item border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Step Name *</label>
                            <input type="text" class="form-control step-name" 
                                   name="steps[0][step_name]" 
                                   placeholder="e.g., Document Collection" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Estimated Days</label>
                            <input type="number" class="form-control step-days" 
                                   name="steps[0][estimated_days]" 
                                   placeholder="e.g., 3" min="0">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Instruction</label>
                            <textarea class="form-control client-instruction" 
                                      name="steps[0][client_instruction]" 
                                      placeholder="Instructions for the client regarding this step..."
                                      rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-step" disabled>
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        stepCounter = 1;
        updateRemoveButtons();
    });
    
    document.getElementById('editProcessModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('editStepsContainer').innerHTML = '';
    });
});
</script>
@endpush

@endsection