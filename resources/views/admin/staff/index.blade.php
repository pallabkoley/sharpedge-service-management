@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">Staff Management</h1>
                <p class="text-muted mb-0">Manage all staff members and their roles</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="bi bi-plus-lg me-2"></i>Add New Staff
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $staff->count() }}</div>
                        <div class="card-label">Total Staff</div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-people-fill text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $staff->where('role.name', 'admin')->count() }}</div>
                        <div class="card-label">Admins</div>
                    </div>
                    <div class="card-icon" style="background-color: #8b5cf6;">
                        <i class="bi bi-shield-check text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $staff->where('role.name', 'backoffice')->count() }}</div>
                        <div class="card-label">Back Office</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;">
                        <i class="bi bi-briefcase text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $staff->where('role.name', 'marketing')->count() }}</div>
                        <div class="card-label">Marketing</div>
                    </div>
                    <div class="card-icon" style="background-color: #f59e0b;">
                        <i class="bi bi-megaphone text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="section-card mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0 fw-bold">Staff List</h6>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.staff.index') }}" 
                       class="btn btn-outline-primary {{ !request()->role ? 'active' : '' }}">All</a>
                    @foreach($roles as $role)
                    <a href="{{ route('admin.staff.index', ['role' => $role->name]) }}" 
                       class="btn btn-outline-primary {{ request()->role == $role->name ? 'active' : '' }}">
                       {{ ucfirst($role->name) }}
                    </a>
                    @endforeach
                </div>
            </div>
            <!--<div class="d-flex align-items-center gap-2">-->
            <!--    <div class="input-group input-group-sm" style="width: 250px;">-->
            <!--        <input type="text" class="form-control" placeholder="Search staff..." id="searchStaff">-->
            <!--        <button class="btn btn-outline-secondary" type="button">-->
            <!--            <i class="bi bi-search"></i>-->
            <!--        </button>-->
            <!--    </div>-->
            <!--</div>-->
        </div>
    </div>

    <!-- Staff Table -->
    <div class="section-card">
        <div class="table-responsive">
            <table class="table table-hover datatable" id="staffTable">
                <thead>
                    
                        <th>Staff Code</th>
                        <th>Profile</th>
                        <th>Staff Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Date Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $staffMember)
                    <tr data-staff-id="{{ $staffMember->id }}">
                        <td><strong>{{ $staffMember->staff_code }}</strong></td>
                        
                        <!-- Profile Image -->
                        <td>
                            @if($staffMember->profile_image)
                                @php
                                    $imagePath = str_replace('public/', '', $staffMember->profile_image);
                                @endphp
                                <img src="{{ asset('storage/' . $imagePath) }}" 
                                     alt="{{ $staffMember->name }}"
                                     class="rounded-circle"
                                     style="width: 40px; height: 40px; object-fit: cover;"
                                     onerror="this.onerror=null; this.src='{{ asset('assets/img/default-user.png') }}';">
                            @else
                                <div class="avatar-placeholder bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; color: white; font-weight: bold;">
                                    {{ strtoupper(substr($staffMember->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        
                        <td>
                            <div>
                                <div class="fw-medium">{{ $staffMember->name }}</div>
                                @if($staffMember->designation)
                                <small class="text-muted">{{ $staffMember->designation }}</small>
                                @endif
                            </div>
                        </td>
                        <td>{{ $staffMember->email }}</td>
                        <td>
                            @php
                                $roleColors = [
                                    'admin' => 'bg-primary',
                                    'backoffice' => 'bg-success',
                                    'marketing' => 'bg-warning'
                                ];
                                $roleColor = $roleColors[$staffMember->role->name] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $roleColor }} rounded-pill">
                                {{ ucfirst($staffMember->role->name) }}
                            </span>
                        </td>
                        <td>
                            @if($staffMember->department)
                                <span class="fw-medium">{{ $staffMember->department }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $staffMember->phone ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $staffMember->status == 'active' ? 'bg-success' : ($staffMember->status == 'suspended' ? 'bg-warning' : 'bg-danger') }} rounded-pill">
                                {{ ucfirst($staffMember->status) }}
                            </span>
                        </td>
                        <td>{{ $staffMember->date_joined ? \Carbon\Carbon::parse($staffMember->date_joined)->format('M d, Y') : '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- View Button -->
                                <a href="{{ route('admin.staff.show', $staffMember->id) }}" 
                                   class="btn btn-outline-info btn-sm" 
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <button type="button" class="btn btn-outline-secondary btn-sm edit-staff" 
                                        onclick="loadEditStaffModal({{ $staffMember->id }})"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                <!-- Status Toggle Form -->
                                <form action="{{ route('admin.staff.status', $staffMember->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to change status?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $staffMember->status == 'active' ? 'warning' : 'success' }} btn-sm"
                                            title="{{ $staffMember->status == 'active' ? 'Deactivate' : 'Activate' }}">
                                        <i class="bi bi-{{ $staffMember->status == 'active' ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                
                                <!-- Delete Form -->
                                <form action="{{ route('admin.staff.destroy', $staffMember->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this staff member? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="bi bi-people-fill display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">No Staff Members Found</h5>
                            <p class="text-muted mb-0">Start by adding your first staff member</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStaffModalLabel">Add New Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addStaffForm" method="POST" action="{{ route('admin.staff.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column - Basic Info -->
                        <div class="col-md-6">
                            <h6 class="mb-3 border-bottom pb-2">Basic Information</h6>
                            
                            <div class="mb-3 text-center">
                                <div class="profile-photo-container mb-3">
                                    <img id="addProfilePhotoPreview" 
                                         src="https://ui-avatars.com/api/?name=Staff&background=3b82f6&color=fff&size=150"
                                         alt="Profile Photo" 
                                         class="rounded-circle border"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <input type="file" 
                                       class="form-control" 
                                       id="addProfilePhoto" 
                                       name="profile_image"
                                       accept="image/*"
                                       onchange="previewAddImage(this)">
                                <div class="form-text">Upload profile photo (JPG, PNG, max 2MB)</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="addName" class="form-label"><span class="text-danger">*</span> Full Name</label>
                                <input type="text" class="form-control" id="addName" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="addEmail" class="form-label"><span class="text-danger">*</span> Email Address</label>
                                <input type="email" class="form-control" id="addEmail" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="addPhone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="addPhone" name="phone">
                            </div>
                            
                            <div class="mb-3">
                                <label for="addRole" class="form-label"><span class="text-danger">*</span> Role</label>
                                <select class="form-select" id="addRole" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Right Column - Additional Info -->
                        <div class="col-md-6">
                            <h6 class="mb-3 border-bottom pb-2">Additional Information</h6>
                            
                            <div class="mb-3">
                                <label for="addDepartment" class="form-label">Department</label>
                                <input type="text" class="form-control" id="addDepartment" name="department">
                            </div>
                            
                            <div class="mb-3">
                                <label for="addDesignation" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="addDesignation" name="designation">
                            </div>
                            
                            <!-- Password Fields -->
                            <div class="mb-3">
                                <div class="alert alert-warning p-2">
                                    <i class="bi bi-shield-lock me-1"></i>
                                    <small>Set password for staff login (minimum 6 characters)</small>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="addPassword" class="form-label"><span class="text-danger">*</span> Password</label>
                                    <input type="password" class="form-control" id="addPassword" name="password" required minlength="6">
                                    <div class="form-text">Minimum 6 characters</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="addPasswordConfirmation" class="form-label"><span class="text-danger">*</span> Confirm Password</label>
                                    <input type="password" class="form-control" id="addPasswordConfirmation" name="password_confirmation" required minlength="6">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="alert alert-info p-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <small>Staff will be created with Active status by default. You can change it later.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveStaffBtn">
                        <span id="saveStaffBtnText">Create Staff</span>
                        <span class="spinner-border spinner-border-sm d-none" id="saveStaffSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStaffModalLabel">Edit Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStaffForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editStaffId" name="staff_id">
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column - Basic Info -->
                        <div class="col-md-6">
                            <h6 class="mb-3 border-bottom pb-2">Basic Information</h6>
                            
                            <div class="mb-3 text-center">
                                <div class="profile-photo-container mb-3">
                                    <img id="editProfilePhotoPreview" 
                                         src="https://ui-avatars.com/api/?name=Staff&background=3b82f6&color=fff&size=150"
                                         alt="Profile Photo" 
                                         class="rounded-circle border"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <input type="file" 
                                       class="form-control" 
                                       id="editProfilePhoto" 
                                       name="profile_image"
                                       accept="image/*"
                                       onchange="previewEditImage(this)">
                                <div class="form-text">Upload new profile photo (JPG, PNG, max 2MB)</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editName" class="form-label"><span class="text-danger">*</span> Full Name</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email Address</label>
                               <input type="email" class="form-control" id="editEmail" name="email">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editPhone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="editPhone" name="phone">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editRole" class="form-label"><span class="text-danger">*</span> Role</label>
                                <select class="form-select" id="editRole" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Right Column - Additional Info -->
                        <div class="col-md-6">
                            <h6 class="mb-3 border-bottom pb-2">Additional Information</h6>
                            
                            <div class="mb-3">
                                <label for="editDepartment" class="form-label">Department</label>
                                <input type="text" class="form-control" id="editDepartment" name="department">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editDesignation" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="editDesignation" name="designation">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editStatus" class="form-label"><span class="text-danger">*</span> Status</label>
                                <select class="form-select" id="editStatus" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            
                            <!-- Optional Password Change -->
                            <div class="mb-3">
                                <div class="alert alert-info p-2">
                                    <i class="bi bi-key me-1"></i>
                                    <small>To change password, please contact the staff member directly or use the password reset feature.</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Date Joined</label>
                                <input type="text" class="form-control" id="editDateJoined" readonly style="background-color: #f8f9fa;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateStaffBtn">
                        <span id="updateStaffBtnText">Update Staff</span>
                        <span class="spinner-border spinner-border-sm d-none" id="updateStaffSpinner"></span>
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
    width: 40px;
    height: 40px;
    background-color: #3b82f6;
    color: white;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-photo-container {
    position: relative;
    display: inline-block;
}

.profile-photo-container img {
    transition: opacity 0.3s ease;
}

.profile-photo-container:hover img {
    opacity: 0.8;
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

/* Modal custom styling */
.modal-lg {
    max-width: 800px;
}

/* Scrollable modal body */
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

/* Table styling */
/* Payment Table Header – Exact Staff Management Style */
#paymentsTable thead th,
.dataTable thead th {
    background-color: #f8fafc !important;
    color: #64748b !important;
    font-weight: 600 !important;
    border-bottom: 1px solid #e2e8f0 !important;
}

/* Table cells alignment */
#paymentsTable tbody td,
.dataTable tbody td {
    vertical-align: middle !important;
}

/* Alert styling */
.alert-warning {
    background-color: #fffbeb;
    border-color: #fef3c7;
    color: #92400e;
}

.alert-info {
    background-color: #eff6ff;
    border-color: #dbeafe;
    color: #1e40af;
}

/* Button group spacing */
.btn-group .btn {
    margin: 0 2px;
}
</style>
@endpush

@push('scripts')
<script>
// Set global storage URL for JavaScript
const STORAGE_URL = "{{ asset('storage') }}";
const APP_URL = "{{ url('/') }}";

// Global variables
let currentStaffId = null;

// Preview image for add modal
function previewAddImage(input) {
    const preview = document.getElementById('addProfilePhotoPreview');
    if (validateFileSize(input, 2)) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
}

// Preview image for edit modal
function previewEditImage(input) {
    const preview = document.getElementById('editProfilePhotoPreview');
    if (validateFileSize(input, 2)) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
}

// Load Edit Staff Modal
async function loadEditStaffModal(staffId) {
    currentStaffId = staffId;
    
    try {
        // Fetch staff data via AJAX
        const response = await fetch(`/admin/staff/${staffId}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            
            if (data.success) {
                const staff = data.staff;
                populateEditForm(staff);
            } else {
                throw new Error(data.message || 'Failed to load staff data');
            }
        } else {
            // Fallback to table data if API endpoint doesn't exist
            loadFromTableData(staffId);
        }
    } catch (error) {
        // Fallback to table data
        loadFromTableData(staffId);
        console.error('Error:', error);
    }
}

// Populate edit form with staff data
function populateEditForm(staff) {
    // Set form action
    document.getElementById('editStaffForm').action = `/admin/staff/${staff.id}`;
    document.getElementById('editStaffId').value = staff.id;
    
    // Populate all fields
    document.getElementById('editName').value = staff.name || '';
    document.getElementById('editEmail').value = staff.email || '';
    document.getElementById('editPhone').value = staff.phone || '';
    document.getElementById('editRole').value = staff.role_id || '';
    document.getElementById('editDepartment').value = staff.department || '';
    document.getElementById('editDesignation').value = staff.designation || '';
    document.getElementById('editStatus').value = staff.status || 'active';
    document.getElementById('editDateJoined').value = staff.date_joined ? formatDate(staff.date_joined) : '-';
    
    // Set profile photo preview - FIXED URL
    if (staff.profile_image) {
        // Remove 'public/' prefix if it exists
        const imagePath = staff.profile_image.replace('public/', '');
        document.getElementById('editProfilePhotoPreview').src = `${STORAGE_URL}/${imagePath}`;
    } else {
        // Use avatar based on name
        const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(staff.name)}&background=3b82f6&color=fff&size=150`;
        document.getElementById('editProfilePhotoPreview').src = avatarUrl;
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editStaffModal'));
    modal.show();
}

// Load staff data from table row
function loadFromTableData(staffId) {
    try {
        const row = document.querySelector(`tr[data-staff-id="${staffId}"]`);
        if (!row) {
            showToast('Staff data not found', 'danger');
            return;
        }
        
        const cells = row.querySelectorAll('td');
        const name = cells[2].querySelector('.fw-medium').textContent;
        const email = cells[3].textContent;
        const phone = cells[6].textContent !== '-' ? cells[6].textContent : '';
        const department = cells[5].querySelector('span').textContent !== '-' ? cells[5].querySelector('span').textContent : '';
        const designation = cells[2].querySelector('small') ? cells[2].querySelector('small').textContent : '';
        const status = cells[7].querySelector('.badge').textContent.trim().toLowerCase();
        
        // Get role from badge text
        const roleText = cells[4].querySelector('.badge').textContent.trim().toLowerCase();
        const roleSelect = document.getElementById('editRole');
        let roleId = '';
        for (let option of roleSelect.options) {
            if (option.text.toLowerCase() === roleText) {
                roleId = option.value;
                break;
            }
        }
        
        // Set form action
        document.getElementById('editStaffForm').action = `/admin/staff/${staffId}`;
        document.getElementById('editStaffId').value = staffId;
        
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editRole').value = roleId;
        document.getElementById('editDepartment').value = department;
        document.getElementById('editDesignation').value = designation;
        document.getElementById('editStatus').value = status;
        document.getElementById('editDateJoined').value = cells[8].textContent;
        
        // Set profile photo preview - Get from existing image src
        const profileImg = cells[1].querySelector('img');
        if (profileImg && profileImg.src && !profileImg.src.includes('default-user.png')) {
            document.getElementById('editProfilePhotoPreview').src = profileImg.src;
        } else {
            const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=150`;
            document.getElementById('editProfilePhotoPreview').src = avatarUrl;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('editStaffModal'));
        modal.show();
        
    } catch (error) {
        showToast('Error loading staff data', 'danger');
        console.error('Error:', error);
    }
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Validate file size (in MB)
function validateFileSize(input, maxSizeMB) {
    if (input.files && input.files[0]) {
        const fileSize = input.files[0].size / 1024 / 1024; // in MB
        if (fileSize > maxSizeMB) {
            showToast(`File size must be less than ${maxSizeMB}MB`, 'danger');
            input.value = ''; // Clear the file input
            return false;
        }
        return true;
    }
    return true;
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
    
    // Add Staff Form Submission
    document.getElementById('addStaffForm').addEventListener('submit', function(e) {
        const form = this;
        const submitBtn = document.getElementById('saveStaffBtn');
        const btnText = document.getElementById('saveStaffBtnText');
        const spinner = document.getElementById('saveStaffSpinner');
        
        // Basic validation
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
            showToast('Please fill all required fields correctly', 'danger');
            return;
        }
        
        // Password validation
        const password = form.querySelector('input[name="password"]');
        const passwordConfirmation = form.querySelector('input[name="password_confirmation"]');
        
        if (password && passwordConfirmation) {
            // Check minimum length
            if (password.value.length < 6) {
                e.preventDefault();
                showToast('Password must be at least 6 characters', 'danger');
                password.focus();
                return;
            }
            
            // Check if passwords match
            if (password.value !== passwordConfirmation.value) {
                e.preventDefault();
                showToast('Passwords do not match', 'danger');
                passwordConfirmation.focus();
                return;
            }
        }
        
        // File size validation for profile image
        const profileImage = form.querySelector('input[name="profile_image"]');
        if (profileImage && profileImage.files[0]) {
            if (!validateFileSize(profileImage, 2)) {
                e.preventDefault();
                return;
            }
        }
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.textContent = 'Creating...';
        spinner.classList.remove('d-none');
    });
    
    // Edit Staff Form Submission
    document.getElementById('editStaffForm').addEventListener('submit', function(e) {
        const form = this;
        const submitBtn = document.getElementById('updateStaffBtn');
        const btnText = document.getElementById('updateStaffBtnText');
        const spinner = document.getElementById('updateStaffSpinner');
        
        // Basic validation
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
            showToast('Please fill all required fields correctly', 'danger');
            return;
        }
        
        // File size validation for profile image
        const profileImage = form.querySelector('input[name="profile_image"]');
        if (profileImage && profileImage.files[0]) {
            if (!validateFileSize(profileImage, 2)) {
                e.preventDefault();
                return;
            }
        }
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.textContent = 'Updating...';
        spinner.classList.remove('d-none');
    });
    
    // Search functionality
    document.getElementById('searchStaff').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#staffTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Password confirmation real-time validation for add form
    const addPassword = document.getElementById('addPassword');
    const addPasswordConfirmation = document.getElementById('addPasswordConfirmation');
    
    if (addPassword && addPasswordConfirmation) {
        addPasswordConfirmation.addEventListener('input', function() {
            const password = addPassword.value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Passwords must match');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
        
        addPassword.addEventListener('input', function() {
            if (addPasswordConfirmation.value) {
                addPasswordConfirmation.dispatchEvent(new Event('input'));
            }
        });
    }
});
</script>
@endpush