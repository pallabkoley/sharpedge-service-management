@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">Notice Management</h1>
                <p class="text-muted mb-0">Create and manage notices for backoffice and marketing teams</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
                <i class="bi bi-plus-lg me-2"></i>Create New Notice
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $notices->count() }}</div>
                        <div class="card-label">Total Notices</div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-megaphone-fill text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $notices->where('status', 'active')->count() }}</div>
                        <div class="card-label">Active Notices</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;">
                        <i class="bi bi-check-circle-fill text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $notices->where('target_role', 'backoffice')->count() }}</div>
                        <div class="card-label">Back Office</div>
                    </div>
                    <div class="card-icon" style="background-color: #8b5cf6;">
                        <i class="bi bi-briefcase-fill text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $notices->where('target_role', 'marketing')->count() }}</div>
                        <div class="card-label">Marketing</div>
                    </div>
                    <div class="card-icon" style="background-color: #f59e0b;">
                        <i class="bi bi-bullseye text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="section-card mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0 fw-bold">All Notices</h6>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active" onclick="filterNotices('all')">All</button>
                    <button type="button" class="btn btn-outline-primary" onclick="filterNotices('active')">Active</button>
                    <button type="button" class="btn btn-outline-primary" onclick="filterNotices('inactive')">Inactive</button>
                    <button type="button" class="btn btn-outline-primary" onclick="filterNotices('role', 'all')">All Roles</button>
                    <button type="button" class="btn btn-outline-primary" onclick="filterNotices('role', 'backoffice')">Back Office</button>
                    <button type="button" class="btn btn-outline-primary" onclick="filterNotices('role', 'marketing')">Marketing</button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" class="form-control" placeholder="Search notices..." id="searchNotices">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notices Table -->
    <div class="section-card">
        <div class="table-responsive">
            <table class="table table-hover" id="noticesTable">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="25%">Title</th>
                        <th width="15%">Target Role</th>
                        <th width="10%">Start Date</th>
                        <th width="10%">End Date</th>
                        <th width="10%">Status</th>
                        <th width="15%">Created By</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices as $notice)
                    <tr data-notice-id="{{ $notice->id }}" data-status="{{ $notice->status }}" data-role="{{ $notice->target_role }}">
                        <td><strong>#{{ $notice->id }}</strong></td>
                        <td>
                            <div class="fw-medium">{{ Str::limit($notice->title, 50) }}</div>
                            <small class="text-muted">{{ Str::limit(strip_tags($notice->message), 80) }}</small>
                        </td>
                        <td>
                            @php
                                $roleColors = [
                                    'all' => 'bg-secondary',
                                    'backoffice' => 'bg-primary',
                                    'marketing' => 'bg-warning'
                                ];
                                $roleColor = $roleColors[$notice->target_role] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $roleColor }} rounded-pill">
                                {{ ucfirst($notice->target_role) }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($notice->start_date)->format('M d, Y') }}</td>
                        <td>
                            @if($notice->end_date)
                                {{ \Carbon\Carbon::parse($notice->end_date)->format('M d, Y') }}
                            @else
                                <span class="text-muted">No End Date</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $notice->status == 'active' ? 'bg-success' : 'bg-danger' }} rounded-pill status-badge">
                                {{ ucfirst($notice->status) }}
                            </span>
                        </td>
                        <td>
                            @if($notice->creator)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-placeholder bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 30px; height: 30px; color: white; font-size: 12px;">
                                        {{ strtoupper(substr($notice->creator->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $notice->creator->name }}</span>
                                </div>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm edit-notice" 
                                        onclick="loadEditNoticeModal({{ $notice->id }})"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-outline-{{ $notice->status == 'active' ? 'warning' : 'success' }} btn-sm toggle-status-btn"
                                        onclick="toggleNoticeStatus({{ $notice->id }})"
                                        title="{{ $notice->status == 'active' ? 'Deactivate' : 'Activate' }}">
                                    <i class="bi bi-{{ $notice->status == 'active' ? 'pause' : 'play' }}"></i>
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm delete-notice"
                                        onclick="deleteNotice({{ $notice->id }})"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-megaphone display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">No Notices Found</h5>
                            <p class="text-muted mb-0">Start by creating your first notice</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Notice Modal -->
<div class="modal fade" id="addNoticeModal" tabindex="-1" aria-labelledby="addNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNoticeModalLabel">Create New Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addNoticeForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="addTitle" class="form-label"><span class="text-danger">*</span> Notice Title</label>
                                <input type="text" class="form-control" id="addTitle" name="title" required maxlength="255">
                                <div class="form-text">Enter a clear and descriptive title for the notice</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="addMessage" class="form-label"><span class="text-danger">*</span> Message</label>
                                <textarea class="form-control" id="addMessage" name="message" rows="5" required></textarea>
                                <div class="form-text">Enter the detailed message for the notice</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="addTargetRole" class="form-label"><span class="text-danger">*</span> Target Role</label>
                                    <select class="form-select" id="addTargetRole" name="target_role" required>
                                        <option value="">Select Target Role</option>
                                        <option value="all">All Roles (Backoffice & Marketing)</option>
                                        <option value="backoffice">Back Office Only</option>
                                        <option value="marketing">Marketing Only</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="addStatus" class="form-label"><span class="text-danger">*</span> Status</label>
                                    <select class="form-select" id="addStatus" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="addStartDate" class="form-label"><span class="text-danger">*</span> Start Date</label>
                                    <input type="date" class="form-control" id="addStartDate" name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="addEndDate" class="form-label">End Date (Optional)</label>
                                    <input type="date" class="form-control" id="addEndDate" name="end_date">
                                    <div class="form-text">Leave empty for no expiration</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveNoticeBtn">
                        <span id="saveNoticeBtnText">Create Notice</span>
                        <span class="spinner-border spinner-border-sm d-none" id="saveNoticeSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Notice Modal -->
<div class="modal fade" id="editNoticeModal" tabindex="-1" aria-labelledby="editNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editNoticeModalLabel">Edit Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNoticeForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editNoticeId" name="notice_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="editTitle" class="form-label"><span class="text-danger">*</span> Notice Title</label>
                                <input type="text" class="form-control" id="editTitle" name="title" required maxlength="255">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editMessage" class="form-label"><span class="text-danger">*</span> Message</label>
                                <textarea class="form-control" id="editMessage" name="message" rows="5" required></textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="editTargetRole" class="form-label"><span class="text-danger">*</span> Target Role</label>
                                    <select class="form-select" id="editTargetRole" name="target_role" required>
                                        <option value="">Select Target Role</option>
                                        <option value="all">All Roles (Backoffice & Marketing)</option>
                                        <option value="backoffice">Back Office Only</option>
                                        <option value="marketing">Marketing Only</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="editStatus" class="form-label"><span class="text-danger">*</span> Status</label>
                                    <select class="form-select" id="editStatus" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="editStartDate" class="form-label"><span class="text-danger">*</span> Start Date</label>
                                    <input type="date" class="form-control" id="editStartDate" name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editEndDate" class="form-label">End Date (Optional)</label>
                                    <input type="date" class="form-control" id="editEndDate" name="end_date">
                                    <div class="form-text">Leave empty for no expiration</div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Created By</label>
                                    <input type="text" class="form-control" id="editCreatedBy" readonly style="background-color: #f8f9fa;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Created Date</label>
                                    <input type="text" class="form-control" id="editCreatedAt" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateNoticeBtn">
                        <span id="updateNoticeBtnText">Update Notice</span>
                        <span class="spinner-border spinner-border-sm d-none" id="updateNoticeSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Mandatory field star styling */
.text-danger {
    color: #dc2626 !important;
    font-weight: bold;
}

.avatar-placeholder {
    width: 30px;
    height: 30px;
    background-color: #3b82f6;
    color: white;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

/* Status badges */
.badge.bg-success {
    background-color: #10b981 !important;
}

.badge.bg-danger {
    background-color: #ef4444 !important;
}

.badge.bg-warning {
    background-color: #f59e0b !important;
}

.badge.bg-primary {
    background-color: #3b82f6 !important;
}

.badge.bg-secondary {
    background-color: #64748b !important;
}

/* Modal custom styling */
.modal-lg {
    max-width: 800px;
}

/* Table styling */
.table th {
    background-color: #f8fafc;
    color: #64748b;
    font-weight: 600;
    border-bottom: 1px solid #e2e8f0;
}

.table td {
    vertical-align: middle;
}

/* Notice title styling */
.table .fw-medium {
    color: #1e293b;
    font-weight: 600;
}

/* Form text styling */
.form-text {
    font-size: 0.875rem;
    color: #64748b;
}

/* Button group spacing */
.btn-group .btn {
    margin: 0 2px;
}

/* Filter button active state */
.btn-group .btn.active {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}
</style>
@endpush

@push('scripts')
<script>
// Global variables
let currentNoticeId = null;
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Show toast notification
function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.toast-container').forEach(container => container.remove());
    
    // Create toast container
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
    document.body.appendChild(toastContainer);
    
    const toastId = 'toast-' + Date.now();
    const typeClass = type === 'success' ? 'bg-success' : type === 'danger' ? 'bg-danger' : 'bg-info';
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

// Load Edit Notice Modal
async function loadEditNoticeModal(noticeId) {
    currentNoticeId = noticeId;
    
    try {
        const response = await fetch(`/admin/notices/${noticeId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            
            if (data.notice) {
                populateEditForm(data.notice);
            } else {
                throw new Error('Failed to load notice data');
            }
        } else {
            throw new Error('Server error');
        }
    } catch (error) {
        showToast('Error loading notice data: ' + error.message, 'danger');
        console.error('Error:', error);
    }
}

// Populate edit form with notice data
function populateEditForm(notice) {
    // Set form action
    document.getElementById('editNoticeForm').action = `/admin/notices/${notice.id}`;
    document.getElementById('editNoticeId').value = notice.id;
    
    // Populate all fields
    document.getElementById('editTitle').value = notice.title || '';
    document.getElementById('editMessage').value = notice.message || '';
    document.getElementById('editTargetRole').value = notice.target_role || '';
    document.getElementById('editStatus').value = notice.status || 'active';
    
    // Format dates for date input (YYYY-MM-DD)
    if (notice.start_date) {
        const startDate = new Date(notice.start_date).toISOString().split('T')[0];
        document.getElementById('editStartDate').value = startDate;
    }
    
    if (notice.end_date) {
        const endDate = new Date(notice.end_date).toISOString().split('T')[0];
        document.getElementById('editEndDate').value = endDate;
    } else {
        document.getElementById('editEndDate').value = '';
    }
    
    // Populate readonly fields
    if (notice.creator) {
        document.getElementById('editCreatedBy').value = notice.creator.name || 'Unknown';
    } else {
        document.getElementById('editCreatedBy').value = 'System';
    }
    
    if (notice.created_at) {
        const createdDate = new Date(notice.created_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('editCreatedAt').value = createdDate;
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editNoticeModal'));
    modal.show();
}

// Toggle Notice Status
async function toggleNoticeStatus(noticeId) {
    if (!confirm('Are you sure you want to change the status of this notice?')) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/notices/${noticeId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ _method: 'PATCH' })
        });
        
        if (response.ok) {
            const data = await response.json();
            
            if (data.success) {
                showToast('Notice status updated successfully', 'success');
                
                // Update UI
                const row = document.querySelector(`tr[data-notice-id="${noticeId}"]`);
                if (row) {
                    // Update status badge
                    const statusBadge = row.querySelector('.status-badge');
                    const newStatus = data.status;
                    statusBadge.className = `badge ${newStatus === 'active' ? 'bg-success' : 'bg-danger'} rounded-pill status-badge`;
                    statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    
                    // Update data attribute
                    row.setAttribute('data-status', newStatus);
                    
                    // Update toggle button
                    const toggleBtn = row.querySelector('.toggle-status-btn');
                    const newIcon = newStatus === 'active' ? 'pause' : 'play';
                    const newColor = newStatus === 'active' ? 'warning' : 'success';
                    const newTitle = newStatus === 'active' ? 'Deactivate' : 'Activate';
                    
                    toggleBtn.className = `btn btn-outline-${newColor} btn-sm toggle-status-btn`;
                    toggleBtn.innerHTML = `<i class="bi bi-${newIcon}"></i>`;
                    toggleBtn.title = newTitle;
                    toggleBtn.setAttribute('onclick', `toggleNoticeStatus(${noticeId})`);
                    
                    // Update stats if needed
                    updateStats();
                }
            }
        } else {
            throw new Error('Failed to update status');
        }
    } catch (error) {
        showToast('Error updating notice status: ' + error.message, 'danger');
        console.error('Error:', error);
    }
}

// Delete Notice
async function deleteNotice(noticeId) {
    if (!confirm('Are you sure you want to delete this notice? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/notices/${noticeId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ _method: 'DELETE' })
        });
        
        if (response.ok) {
            const data = await response.json();
            
            if (data.success) {
                showToast('Notice deleted successfully', 'success');
                
                // Remove row from table
                const row = document.querySelector(`tr[data-notice-id="${noticeId}"]`);
                if (row) {
                    row.remove();
                }
                
                // Update stats
                updateStats();
                
                // Show empty message if no rows left
                const tbody = document.querySelector('#noticesTable tbody');
                if (tbody.children.length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = `
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-megaphone display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">No Notices Found</h5>
                            <p class="text-muted mb-0">Start by creating your first notice</p>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }
            }
        } else {
            throw new Error('Failed to delete notice');
        }
    } catch (error) {
        showToast('Error deleting notice: ' + error.message, 'danger');
        console.error('Error:', error);
    }
}

// Update stats cards
function updateStats() {
    const rows = document.querySelectorAll('#noticesTable tbody tr:not([style*="display: none"])');
    const total = rows.length;
    const active = Array.from(rows).filter(row => row.getAttribute('data-status') === 'active').length;
    const backoffice = Array.from(rows).filter(row => row.getAttribute('data-role') === 'backoffice').length;
    const marketing = Array.from(rows).filter(row => row.getAttribute('data-role') === 'marketing').length;
    
    // Update card values if elements exist
    const totalCard = document.querySelector('.card-value:first-child');
    const activeCard = document.querySelectorAll('.card-value')[1];
    const backofficeCard = document.querySelectorAll('.card-value')[2];
    const marketingCard = document.querySelectorAll('.card-value')[3];
    
    if (totalCard) totalCard.textContent = total;
    if (activeCard) activeCard.textContent = active;
    if (backofficeCard) backofficeCard.textContent = backoffice;
    if (marketingCard) marketingCard.textContent = marketing;
}

// Filter notices
function filterNotices(type, value = null) {
    const rows = document.querySelectorAll('#noticesTable tbody tr');
    const filterButtons = document.querySelectorAll('.btn-group .btn');
    
    // Update active button
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    rows.forEach(row => {
        let show = true;
        
        if (type === 'active' || type === 'inactive') {
            show = row.getAttribute('data-status') === type;
        } else if (type === 'role') {
            if (value === 'all') {
                show = true;
            } else {
                show = row.getAttribute('data-role') === value;
            }
        } else if (type === 'all') {
            show = true;
        }
        
        row.style.display = show ? '' : 'none';
    });
    
    // Update stats for visible rows
    updateStats();
}

// Date validation helper
function validateDates(startDateId, endDateId) {
    const startDate = document.getElementById(startDateId).value;
    const endDate = document.getElementById(endDateId).value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (end < start) {
            showToast('End date must be after or equal to start date', 'danger');
            return false;
        }
    }
    
    return true;
}

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Show success message from session
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    
    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('{{ $error }}', 'danger');
        @endforeach
    @endif
    
    // Add Notice Form Submission
    document.getElementById('addNoticeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('saveNoticeBtn');
        const btnText = document.getElementById('saveNoticeBtnText');
        const spinner = document.getElementById('saveNoticeSpinner');
        
        // Basic validation
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            showToast('Please fill all required fields correctly', 'danger');
            return;
        }
        
        // Date validation
        if (!validateDates('addStartDate', 'addEndDate')) {
            return;
        }
        
        try {
            // Show loading state
            submitBtn.disabled = true;
            btnText.textContent = 'Creating...';
            spinner.classList.remove('d-none');
            
            const formData = new FormData(form);
            const response = await fetch("{{ route('admin.notices.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showToast(data.message, 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addNoticeModal'));
                modal.hide();
                
                // Reset form
                form.reset();
                form.classList.remove('was-validated');
                
                // Reload page to show new notice
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
            } else {
                let errorMessage = 'Failed to create notice';
                if (data.errors) {
                    errorMessage = Object.values(data.errors).flat().join(', ');
                } else if (data.message) {
                    errorMessage = data.message;
                }
                throw new Error(errorMessage);
            }
            
        } catch (error) {
            showToast('Error: ' + error.message, 'danger');
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = 'Create Notice';
            spinner.classList.add('d-none');
        }
    });
    
    // Edit Notice Form Submission
    document.getElementById('editNoticeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('updateNoticeBtn');
        const btnText = document.getElementById('updateNoticeBtnText');
        const spinner = document.getElementById('updateNoticeSpinner');
        
        // Basic validation
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            showToast('Please fill all required fields correctly', 'danger');
            return;
        }
        
        // Date validation
        if (!validateDates('editStartDate', 'editEndDate')) {
            return;
        }
        
        try {
            // Show loading state
            submitBtn.disabled = true;
            btnText.textContent = 'Updating...';
            spinner.classList.remove('d-none');
            
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showToast(data.message, 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editNoticeModal'));
                modal.hide();
                
                // Reload page to show updated notice
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
            } else {
                let errorMessage = 'Failed to update notice';
                if (data.errors) {
                    errorMessage = Object.values(data.errors).flat().join(', ');
                } else if (data.message) {
                    errorMessage = data.message;
                }
                throw new Error(errorMessage);
            }
            
        } catch (error) {
            showToast('Error: ' + error.message, 'danger');
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = 'Update Notice';
            spinner.classList.add('d-none');
        }
    });
    
    // Search functionality
    document.getElementById('searchNotices').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#noticesTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
        
        // Update stats for visible rows
        updateStats();
    });
    
    // Set minimum date for start date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('addStartDate').min = today;
    document.getElementById('editStartDate').min = today;
    
    // Set end date min based on start date
    document.getElementById('addStartDate').addEventListener('change', function() {
        document.getElementById('addEndDate').min = this.value;
    });
    
    document.getElementById('editStartDate').addEventListener('change', function() {
        document.getElementById('editEndDate').min = this.value;
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush