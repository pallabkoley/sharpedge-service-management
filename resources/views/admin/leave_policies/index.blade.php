@extends('layouts.admin')

@section('content')
<div class="container-fluid leave-policy-page">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">Leave Policy Management</h1>
                <p class="text-muted mb-0">Manage all leave types and their policies</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeaveTypeModal">
                <i class="bi bi-plus-lg me-2"></i>Add Leave Type
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $leaveTypes->count() }}</div>
                        <div class="card-label">Total Leave Types</div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-calendar-check text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $leaveTypes->where('status', 1)->count() }}</div>
                        <div class="card-label">Active Types</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;">
                        <i class="bi bi-check-circle text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $leaveTypes->where('status', 0)->count() }}</div>
                        <div class="card-label">Inactive Types</div>
                    </div>
                    <div class="card-icon" style="background-color: #ef4444;">
                        <i class="bi bi-x-circle text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $policies->count() }}</div>
                        <div class="card-label">Policies Configured</div>
                    </div>
                    <div class="card-icon" style="background-color: #8b5cf6;">
                        <i class="bi bi-shield-check text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Policies Form -->
    <div class="section-card mb-4">
        <div class="section-header mb-4">
            <h5 class="mb-0 fw-bold">Leave Policies Configuration</h5>
            <p class="text-muted mb-0">Configure maximum leave days for each leave type</p>
        </div>
        
        <form id="leavePoliciesForm" method="POST" action="{{ route('admin.leave-policies.store') }}">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover" id="policiesTable">
                    <thead>
                        <tr>
                            <th width="30%">Leave Type</th>
                            <th width="25%">Max per Year</th>
                            <th width="25%">Max per Month</th>
                            <th width="20%">Status</th>
                        </tr>
                    </thead>
                    <tbody id="policiesTbody">
                        @foreach($leaveTypes as $index => $leaveType)
                            @php
                                $policy = $policies->where('leave_type_id', $leaveType->id)->first();
                            @endphp
                        <tr data-leave-type-id="{{ $leaveType->id }}">
                            <td>
                                <div class="fw-medium">{{ $leaveType->name }}</div>
                                <small class="text-muted">Code: {{ $leaveType->code }}</small>
                                <input type="hidden" name="policies[{{ $index }}][leave_type_id]" value="{{ $leaveType->id }}">
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           class="form-control" 
                                           name="policies[{{ $index }}][max_per_year]" 
                                           value="{{ $policy->max_per_year ?? 0 }}"
                                           min="0" 
                                           max="365"
                                           required>
                                    <span class="input-group-text">days</span>
                                </div>
                                <div class="form-text">Max 365 days</div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           class="form-control" 
                                           name="policies[{{ $index }}][max_per_month]" 
                                           value="{{ $policy->max_per_month ?? '' }}"
                                           min="0" 
                                           max="31">
                                    <span class="input-group-text">days</span>
                                </div>
                                <div class="form-text">Max 31 days (optional)</div>
                            </td>
                            <td>
                                <span class="badge {{ $leaveType->status ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                    {{ $leaveType->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary" id="savePoliciesBtn">
                    <span id="savePoliciesBtnText">Save All Policies</span>
                    <span class="spinner-border spinner-border-sm d-none" id="savePoliciesSpinner"></span>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="resetFormBtn">
                    Reset Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Leave Type Modal -->
<div class="modal fade" id="addLeaveTypeModal" tabindex="-1" aria-labelledby="addLeaveTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeaveTypeModalLabel">Add New Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addLeaveTypeForm" method="POST" action="{{ route('admin.leave-types.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addName" class="form-label"><span class="text-danger">*</span> Leave Type Name</label>
                        <input type="text" class="form-control" id="addName" name="name" required>
                        <div class="invalid-feedback">Please enter leave type name</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="addCode" class="form-label"><span class="text-danger">*</span> Leave Code</label>
                        <input type="text" class="form-control" id="addCode" name="code" required>
                        <div class="invalid-feedback">Please enter leave code</div>
                        <div class="form-text">Unique code (e.g., AL for Annual Leave, SL for Sick Leave)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveLeaveTypeBtn">
                        <span id="saveLeaveTypeBtnText">Add Leave Type</span>
                        <span class="spinner-border spinner-border-sm d-none" id="saveLeaveTypeSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@endsection

@push('styles')
<style>
.leave-policy-page .stats-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.leave-policy-page .stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.leave-policy-page .card-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.leave-policy-page .card-value {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
}

.leave-policy-page .card-label {
    color: #64748b;
    font-size: 14px;
    margin-top: 4px;
}

.leave-policy-page .section-card {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.leave-policy-page .section-header {
    border-bottom: 2px solid #f1f5f9;
    padding-bottom: 16px;
    margin-bottom: 20px;
}

.leave-policy-page .table th {
    background-color: #f8fafc;
    color: #64748b;
    font-weight: 600;
    border-bottom: 2px solid #e2e8f0;
}

.leave-policy-page .table td {
    vertical-align: middle;
    padding: 16px 12px;
}

.leave-policy-page .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.leave-policy-page .form-text {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 4px;
}

.leave-policy-page .badge.bg-success {
    background-color: #10b981 !important;
}

.leave-policy-page .badge.bg-danger {
    background-color: #ef4444 !important;
}

.leave-policy-page .input-group-sm .form-control {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.leave-policy-page .input-group-sm .input-group-text {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.leave-policy-page #policiesTbody tr:hover {
    background-color: #f8fafc;
}

.leave-policy-page #policiesTbody tr:not(:last-child) {
    border-bottom: 1px solid #e2e8f0;
}

.leave-policy-page .modal-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.leave-policy-page .modal-title {
    color: #1e293b;
    font-weight: 600;
}

.leave-policy-page .text-danger {
    color: #dc2626 !important;
}

.leave-policy-page .alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.leave-policy-page .alert-success {
    background-color: #d1fae5;
    color: #065f46;
}

.leave-policy-page .alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

.leave-policy-page .alert .bi {
    font-size: 1.2em;
}
</style>

@endpush

@push('scripts')
<script>
// Global variables
let originalFormData = {};

// Toast Notification
function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.toast').forEach(toast => toast.remove());
    
    const toast = `<div class="toast align-items-center text-white bg-${type} border-0 position-fixed top-3 end-3" role="alert">
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>`;
    
    document.body.insertAdjacentHTML('beforeend', toast);
    const toastEl = document.querySelector('.toast:last-child');
    const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
    bsToast.show();
}

// Store original form data
function storeOriginalFormData() {
    originalFormData = {};
    document.querySelectorAll('#leavePoliciesForm input').forEach(input => {
        if (input.name) {
            originalFormData[input.name] = input.value;
        }
    });
}

// Check if form has changes
function hasFormChanges() {
    let hasChanges = false;
    document.querySelectorAll('#leavePoliciesForm input').forEach(input => {
        if (input.name && originalFormData[input.name] !== input.value) {
            hasChanges = true;
        }
    });
    return hasChanges;
}

// Reset form to original values
function resetForm() {
    Object.keys(originalFormData).forEach(name => {
        const input = document.querySelector(`input[name="${name}"]`);
        if (input) {
            input.value = originalFormData[name];
        }
    });
    showToast('Form reset to original values', 'info');
}

// Add new leave type row
function addLeaveTypeRow(leaveType) {
    const tbody = document.getElementById('policiesTbody');
    const rowCount = tbody.querySelectorAll('tr').length;
    
    const newRow = `
        <tr data-leave-type-id="${leaveType.id}">
            <td>
                <div class="fw-medium">${leaveType.name}</div>
                <small class="text-muted">Code: ${leaveType.code}</small>
                <input type="hidden" name="policies[${rowCount}][leave_type_id]" value="${leaveType.id}">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" 
                           class="form-control" 
                           name="policies[${rowCount}][max_per_year]" 
                           value="0"
                           min="0" 
                           max="365"
                           required>
                    <span class="input-group-text">days</span>
                </div>
                <div class="form-text">Max 365 days</div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" 
                           class="form-control" 
                           name="policies[${rowCount}][max_per_month]" 
                           value=""
                           min="0" 
                           max="31">
                    <span class="input-group-text">days</span>
                </div>
                <div class="form-text">Max 31 days (optional)</div>
            </td>
            <td>
                <span class="badge bg-success rounded-pill">Active</span>
            </td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', newRow);
}

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Store original form data
    storeOriginalFormData();
    
    // Leave Policies Form Submission
    document.getElementById('leavePoliciesForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('savePoliciesBtn');
        const btnText = document.getElementById('savePoliciesBtnText');
        const spinner = document.getElementById('savePoliciesSpinner');
        
        // Validate form
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        submitBtn.disabled = true;
        btnText.textContent = 'Saving...';
        spinner.classList.remove('d-none');
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                let errorMsg = data.message || 'Failed to save policies';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join(', ');
                }
                throw new Error(errorMsg);
            }
            
            if (data.success || response.redirected) {
                // If it's a redirect (non-AJAX), let it happen
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    showToast('Leave policies saved successfully!', 'success');
                    storeOriginalFormData(); // Update original data
                }
            }
        } catch (error) {
            showToast(error.message, 'danger');
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = 'Save All Policies';
            spinner.classList.add('d-none');
        }
    });
    
    // Add Leave Type Form Submission
    document.getElementById('addLeaveTypeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('saveLeaveTypeBtn');
        const btnText = document.getElementById('saveLeaveTypeBtnText');
        const spinner = document.getElementById('saveLeaveTypeSpinner');
        
        // Validate form
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        submitBtn.disabled = true;
        btnText.textContent = 'Adding...';
        spinner.classList.remove('d-none');
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                let errorMsg = data.message || 'Failed to add leave type';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join(', ');
                }
                throw new Error(errorMsg);
            }
            
            if (data.success) {
                showToast(data.message, 'success');
                
                // Add new row to table
                addLeaveTypeRow(data.data);
                
                // Reset form and close modal
                form.reset();
                form.classList.remove('was-validated');
                bootstrap.Modal.getInstance(document.getElementById('addLeaveTypeModal')).hide();
                
                // Update stats count
                updateStatsCount();
            }
        } catch (error) {
            showToast(error.message, 'danger');
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = 'Add Leave Type';
            spinner.classList.add('d-none');
        }
    });
    
    // Reset Form Button
    document.getElementById('resetFormBtn').addEventListener('click', function() {
        if (hasFormChanges()) {
            if (confirm('Are you sure you want to reset all changes?')) {
                resetForm();
            }
        } else {
            showToast('No changes to reset', 'info');
        }
    });
    
    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasFormChanges()) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
    
    // Update stats count (simulate)
    function updateStatsCount() {
        const totalTypes = document.getElementById('policiesTbody').querySelectorAll('tr').length;
        const totalTypesElement = document.querySelector('.stats-card .card-value');
        if (totalTypesElement) {
            totalTypesElement.textContent = totalTypes;
        }
    }
    
    // Form change detection
    document.getElementById('leavePoliciesForm').addEventListener('input', function() {
        const resetBtn = document.getElementById('resetFormBtn');
        if (hasFormChanges()) {
            resetBtn.classList.remove('btn-outline-secondary');
            resetBtn.classList.add('btn-warning');
        } else {
            resetBtn.classList.remove('btn-warning');
            resetBtn.classList.add('btn-outline-secondary');
        }
    });
    
    // Input validation
    document.querySelectorAll('#leavePoliciesForm input[type="number"]').forEach(input => {
        input.addEventListener('change', function() {
            const max = parseInt(this.getAttribute('max'));
            const min = parseInt(this.getAttribute('min'));
            const value = parseInt(this.value) || 0;
            
            if (value > max) {
                this.value = max;
                showToast(`Maximum value is ${max}`, 'warning');
            } else if (value < min) {
                this.value = min;
                showToast(`Minimum value is ${min}`, 'warning');
            }
        });
    });
    
    // Auto-uppercase leave code
    document.getElementById('addCode').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endpush