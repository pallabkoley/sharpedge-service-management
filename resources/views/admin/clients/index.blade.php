@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">Client Management</h1>
                <p class="text-muted mb-0">Manage all clients and their information</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addClientModal">
                    <i class="bi bi-plus-lg me-2"></i>Add New Client
                </button>
                <button type="button" class="btn btn-warning" id="bulkEditBtn" disabled>
                    <i class="bi bi-pencil-square me-2"></i>Bulk Edit
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $clients->count() }}</div>
                        <div class="card-label">Total Clients</div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-people text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $clients->where('status', 'active')->count() }}</div>
                        <div class="card-label">Active Clients</div>
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
                        <div class="card-value">{{ $clients->where('status', 'inactive')->count() }}</div>
                        <div class="card-label">Inactive Clients</div>
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
                        <div class="card-value">{{ $clients->where('is_credit_allowed', true)->count() }}</div>
                        <div class="card-label">Credit Allowed</div>
                    </div>
                    <div class="card-icon" style="background-color: #8b5cf6;">
                        <i class="bi bi-credit-card text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="section-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h6 class="mb-0 fw-bold">Client List</h6>

                <!-- Status Filters -->
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.clients.index', array_filter(['status' => request('status'), 'credit' => request('credit')])) }}" 
                       class="btn btn-outline-primary {{ !request('status') && !request('credit') ? 'active' : '' }}">All</a>
                    <a href="{{ route('admin.clients.index', array_merge(['status' => 'active'], array_filter(['credit' => request('credit')]))) }}" 
                       class="btn btn-outline-primary {{ request('status') == 'active' ? 'active' : '' }}">Active</a>
                    <a href="{{ route('admin.clients.index', array_merge(['status' => 'inactive'], array_filter(['credit' => request('credit')]))) }}" 
                       class="btn btn-outline-primary {{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a>
                </div>

                <span class="text-muted">|</span>

                <!-- Credit Limit Filters -->
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.clients.index', array_merge(['credit' => 'allowed'], array_filter(['status' => request('status')]))) }}" 
                       class="btn btn-outline-success {{ request('credit') == 'allowed' ? 'active' : '' }}">
                        <i class="bi bi-check-circle me-1"></i>Credit Allowed
                    </a>
                    <a href="{{ route('admin.clients.index', array_merge(['credit' => 'not_allowed'], array_filter(['status' => request('status')]))) }}" 
                       class="btn btn-outline-danger {{ request('credit') == 'not_allowed' ? 'active' : '' }}">
                        <i class="bi bi-x-circle me-1"></i>Credit Not Allowed
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="section-card">
        <div class="table-responsive">
            <table class="table table-hover" id="clientsTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                        <th>ID</th>
                        <th>Profile</th>
                        <th>Client Name</th>
                        <th>Added By</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Credit Limit</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr data-client-id="{{ $client->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input client-checkbox" data-id="{{ $client->id }}">
                        </td>
                        <td><strong>{{ $client->client_code ?? $client->id }}</strong></td>
                        <td>
                            @if($client->profile_photo)
                                <img src="{{ asset('storage/'.$client->profile_photo) }}" 
                                     alt="{{ $client->name }}"
                                     class="rounded-circle"
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="avatar-placeholder bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; color: white; font-weight: bold;">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div>
                                <div class="fw-medium">{{ $client->name }}</div>
                                <small class="text-muted">ID: {{ $client->user_id ?? 'N/A' }}</small>
                            </div>
                        </td>
                        <td>
                            @if($client->creator)
                                <div>
                                    <div class="fw-medium">{{ $client->creator->name }}</div>
                                    <small class="text-muted" style="font-size: 0.85em;">
                                        {{ ucfirst($client->creator->role->name ?? 'N/A') }}
                                    </small>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->phone }}</td>
                        <td>
                            @if($client->company_name)
                                <span class="fw-medium">{{ $client->company_name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($client->city)
                                <span>{{ $client->city }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $client->status == 'active' ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                {{ ucfirst($client->status) }}
                            </span>
                        </td>
                        <td>
                            @if($client->is_credit_allowed)
                                <span class="badge bg-success">Allowed</span>
                            @else
                                <span class="badge bg-danger">Not Allowed</span>
                            @endif
                        </td>
                        <td>{{ $client->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.clients.show', $client->id) }}" 
                                   class="btn btn-outline-primary btn-sm" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="loadEditClientModal({{ $client->id }})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm delete-client" 
                                        data-client-id="{{ $client->id }}"
                                        data-client-name="{{ $client->name }}"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <!-- NO ROW – DataTables will show 'No data available' via language.emptyTable -->
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Client Modal (same as before) -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addClientForm" method="POST" action="{{ route('admin.clients.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3 border-bottom pb-2">Basic Information</h6>
                            <div class="mb-3 text-center">
                                <div class="profile-photo-container mb-3">
                                    <img id="addProfilePhotoPreview" 
                                         src="https://ui-avatars.com/api/?name=Client&background=3b82f6&color=fff&size=150"
                                         alt="Profile Photo" 
                                         class="rounded-circle border"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <input type="file" class="form-control" id="addProfilePhoto" name="profile_photo" accept="image/*" onchange="previewAddImage(this)">
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
                                <label for="addPhone" class="form-label"><span class="text-danger">*</span> Phone Number</label>
                                <input type="text" class="form-control" id="addPhone" name="phone" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="addDob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="addDob" name="dob">
                                </div>
                                <div class="col-md-6">
                                    <label for="addGender" class="form-label">Gender</label>
                                    <select class="form-select" id="addGender" name="gender">
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3 border-bottom pb-2">Additional Information</h6>
                            <div class="mb-3">
                                <label for="addAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="addAddress" name="address" rows="2"></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="addCity" class="form-label">City</label>
                                    <input type="text" class="form-control" id="addCity" name="city">
                                </div>
                                <div class="col-md-6">
                                    <label for="addState" class="form-label">State</label>
                                    <input type="text" class="form-control" id="addState" name="state">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="addPincode" class="form-label">Pincode</label>
                                <input type="text" class="form-control" id="addPincode" name="pincode">
                            </div>
                            <div class="mb-3">
                                <label for="addCompanyName" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="addCompanyName" name="company_name">
                            </div>
                            <div class="mb-3">
                                <label for="addGstNumber" class="form-label">GST Number</label>
                                <input type="text" class="form-control" id="addGstNumber" name="gst_number">
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="addPreferredContact" class="form-label">Preferred Contact</label>
                                    <select class="form-select" id="addPreferredContact" name="preferred_contact">
                                        <option value="">Select</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="whatsapp">WhatsApp</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="addSource" class="form-label">Source</label>
                                    <select class="form-select" id="addSource" name="source">
                                        <option value="">Select</option>
                                        <option value="referral">Referral</option>
                                        <option value="website">Website</option>
                                        <option value="social_media">Social Media</option>
                                        <option value="walk_in">Walk-in</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="addCreditLimit" class="form-label"><span class="text-danger">*</span> Credit Limit Allowed</label>
                                <select class="form-select" id="addCreditLimit" name="is_credit_allowed" required>
                                    <option value="0">Not Allowed</option>
                                    <option value="1">Allowed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="alert alert-warning p-2">
                                    <i class="bi bi-shield-lock me-1"></i>
                                    <small>Set password for client login (minimum 8 characters)</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="addPassword" class="form-label"><span class="text-danger">*</span> Password</label>
                                    <input type="password" class="form-control" id="addPassword" name="password" required minlength="8">
                                </div>
                                <div class="col-md-6">
                                    <label for="addPasswordConfirmation" class="form-label"><span class="text-danger">*</span> Confirm Password</label>
                                    <input type="password" class="form-control" id="addPasswordConfirmation" name="password_confirmation" required minlength="8">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="addNotes" class="form-label">Notes</label>
                                <textarea class="form-control" id="addNotes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveClientBtn">
                        <span id="saveClientBtnText">Create Client</span>
                        <span class="spinner-border spinner-border-sm d-none" id="saveClientSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editClientForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editClientId" name="client_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3 border-bottom pb-2">Basic Information</h6>
                            <div class="mb-3 text-center">
                                <div class="profile-photo-container mb-3">
                                    <img id="editProfilePhotoPreview" 
                                         src="https://ui-avatars.com/api/?name=Client&background=3b82f6&color=fff&size=150"
                                         alt="Profile Photo" 
                                         class="rounded-circle border"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <input type="file" class="form-control" id="editProfilePhoto" name="profile_photo" accept="image/*" onchange="previewEditImage(this)">
                                <div class="form-text">Upload new profile photo (JPG, PNG, max 2MB)</div>
                            </div>
                            <div class="mb-3">
                                <label for="editName" class="form-label"><span class="text-danger">*</span> Full Name</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label"><span class="text-danger">*</span> Email Address</label>
                                <input type="email" class="form-control" id="editEmail" name="email" required readonly>
                            </div>
                            <div class="mb-3">
                                <label for="editPhone" class="form-label"><span class="text-danger">*</span> Phone Number</label>
                                <input type="text" class="form-control" id="editPhone" name="phone" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="editDob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="editDob" name="dob">
                                </div>
                                <div class="col-md-6">
                                    <label for="editGender" class="form-label">Gender</label>
                                    <select class="form-select" id="editGender" name="gender">
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editStatus" class="form-label"><span class="text-danger">*</span> Status</label>
                                <select class="form-select" id="editStatus" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3 border-bottom pb-2">Additional Information</h6>
                            <div class="mb-3">
                                <label for="editAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="editAddress" name="address" rows="2"></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="editCity" class="form-label">City</label>
                                    <input type="text" class="form-control" id="editCity" name="city">
                                </div>
                                <div class="col-md-6">
                                    <label for="editState" class="form-label">State</label>
                                    <input type="text" class="form-control" id="editState" name="state">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editPincode" class="form-label">Pincode</label>
                                <input type="text" class="form-control" id="editPincode" name="pincode">
                            </div>
                            <div class="mb-3">
                                <label for="editCompanyName" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="editCompanyName" name="company_name">
                            </div>
                            <div class="mb-3">
                                <label for="editGstNumber" class="form-label">GST Number</label>
                                <input type="text" class="form-control" id="editGstNumber" name="gst_number">
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="editPreferredContact" class="form-label">Preferred Contact</label>
                                    <select class="form-select" id="editPreferredContact" name="preferred_contact">
                                        <option value="">Select</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="whatsapp">WhatsApp</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="editSource" class="form-label">Source</label>
                                    <select class="form-select" id="editSource" name="source">
                                        <option value="">Select</option>
                                        <option value="referral">Referral</option>
                                        <option value="website">Website</option>
                                        <option value="social_media">Social Media</option>
                                        <option value="walk_in">Walk-in</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editCreditLimit" class="form-label"><span class="text-danger">*</span> Credit Limit Allowed</label>
                                <select class="form-select" id="editCreditLimit" name="is_credit_allowed" required>
                                    <option value="0">Not Allowed</option>
                                    <option value="1">Allowed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="alert alert-info p-2">
                                    <i class="bi bi-key me-1"></i>
                                    <small>Change password (leave blank to keep current)</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="editPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="editPassword" name="password" minlength="8">
                                </div>
                                <div class="col-md-6">
                                    <label for="editPasswordConfirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="editPasswordConfirmation" name="password_confirmation" minlength="8">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editNotes" class="form-label">Notes</label>
                                <textarea class="form-control" id="editNotes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateClientBtn">
                        <span id="updateClientBtnText">Update Client</span>
                        <span class="spinner-border spinner-border-sm d-none" id="updateClientSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Edit Modal -->
<div class="modal fade" id="bulkEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Edit Clients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="bulkEditTable">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Status</th>
                                <th>Credit Allowed</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Preferred Contact</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="bulkEditTableBody">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveBulkEditBtn">
                    <span id="saveBulkEditBtnText">Save Changes</span>
                    <span class="spinner-border spinner-border-sm d-none" id="saveBulkEditSpinner"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Client Modal -->
<div class="modal fade" id="viewClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Client Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="clientDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-danger { color: #dc2626 !important; font-weight: bold; }
    .avatar-placeholder {
        width: 40px;
        height: 40px;
        background-color: #3b82f6;
        color: white;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
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
    .stats-card .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #3b82f6;
    }
    .stats-card .card-value {
        font-size: 1.75rem;
        font-weight: 700;
    }
    .stats-card .card-label {
        color: #6b7280;
        font-size: 0.875rem;
    }
    .section-card {
        background: #fff;
        border-radius: 8px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    #bulkEditTable td {
        vertical-align: middle;
    }
    #bulkEditTable select,
    #bulkEditTable input,
    #bulkEditTable textarea {
        width: 100%;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.2rem;
        border: 1px solid #ced4da;
    }
    #bulkEditTable textarea {
        resize: vertical;
    }
    .btn-group .btn.active {
        z-index: 3;
        box-shadow: inset 0 3px 5px rgba(0,0,0,0.125);
    }
</style>
@endpush

@push('scripts')
<script>
// ============================================================
// GLOBALS & HELPERS
// ============================================================
let currentClientId = null;

function showToast(message, type = 'success') {
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

function previewAddImage(input) {
    const preview = document.getElementById('addProfilePhotoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
function previewEditImage(input) {
    const preview = document.getElementById('editProfilePhotoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}

// ============================================================
// EDIT CLIENT MODAL (reads from table row, no AJAX 404)
// ============================================================
function loadEditClientModal(clientId) {
    try {
        const row = document.querySelector(`tr[data-client-id="${clientId}"]`);
        if (!row) {
            showToast('Client row not found', 'danger');
            return;
        }
        const cells = row.querySelectorAll('td');
        // indices: 0=checkbox, 1=ID, 2=Profile, 3=Name, 4=AddedBy, 5=Email, 6=Phone, 7=Company, 8=City, 9=Status, 10=Credit, 11=Created, 12=Actions
        const name = cells[3]?.querySelector('.fw-medium')?.textContent?.trim() || '';
        const email = cells[5]?.textContent?.trim() || '';
        const phone = cells[6]?.textContent?.trim() || '';
        const company = cells[7]?.querySelector('span')?.textContent?.trim() || '';
        const city = cells[8]?.querySelector('span')?.textContent?.trim() || '';
        const statusBadge = cells[9]?.querySelector('.badge');
        const status = statusBadge ? statusBadge.textContent.trim().toLowerCase() : 'active';
        const creditBadge = cells[10]?.querySelector('.badge');
        const creditText = creditBadge ? creditBadge.textContent.trim() : 'Not Allowed';
        const creditValue = creditText === 'Allowed' ? '1' : '0';
        const profileImg = cells[2]?.querySelector('img');
        const profileSrc = profileImg ? profileImg.src : '';

        // Populate form
        document.getElementById('editClientForm').action = `/admin/clients/${clientId}`;
        document.getElementById('editClientId').value = clientId;
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editStatus').value = status;
        document.getElementById('editCreditLimit').value = creditValue;
        document.getElementById('editCompanyName').value = company === '-' ? '' : company;
        document.getElementById('editCity').value = city === '-' ? '' : city;
        // Clear others
        ['editDob','editGender','editAddress','editState','editPincode','editGstNumber','editPreferredContact','editSource','editNotes','editPassword','editPasswordConfirmation']
            .forEach(id => document.getElementById(id).value = '');

        // Profile photo
        if (profileSrc) {
            document.getElementById('editProfilePhotoPreview').src = profileSrc;
        } else {
            document.getElementById('editProfilePhotoPreview').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=150`;
        }

        new bootstrap.Modal(document.getElementById('editClientModal')).show();
    } catch (error) {
        showToast('Error loading client data', 'danger');
        console.error(error);
    }
}

// ============================================================
// VIEW CLIENT DETAILS (fallback using table data)
// ============================================================
function loadClientDetails(clientId) {
    try {
        const row = document.querySelector(`tr[data-client-id="${clientId}"]`);
        if (!row) throw new Error('Row not found');
        const cells = row.querySelectorAll('td');
        const name = cells[3]?.querySelector('.fw-medium')?.textContent?.trim() || '';
        const email = cells[5]?.textContent?.trim() || '';
        const phone = cells[6]?.textContent?.trim() || '';
        const company = cells[7]?.querySelector('span')?.textContent?.trim() || '';
        const city = cells[8]?.querySelector('span')?.textContent?.trim() || '';
        const status = cells[9]?.querySelector('.badge')?.textContent?.trim() || '';
        const credit = cells[10]?.querySelector('.badge')?.textContent?.trim() || '';
        const createdDate = cells[11]?.textContent?.trim() || '';
        const addedByContainer = cells[4];
        let creatorInfo = '-';
        if (addedByContainer.querySelector('.fw-medium')) {
            const creatorName = addedByContainer.querySelector('.fw-medium').textContent;
            const creatorRole = addedByContainer.querySelector('.text-muted')?.textContent || '';
            creatorInfo = `<div><div class="fw-medium">${creatorName}</div><small class="text-muted" style="font-size:0.85em;">${creatorRole}</small></div>`;
        }
        let profileHtml = '';
        const img = cells[2]?.querySelector('img');
        if (img && img.src) {
            profileHtml = `<img src="${img.src}" alt="${name}" class="rounded-circle" style="width:100px;height:100px;object-fit:cover;">`;
        } else {
            profileHtml = `<div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width:100px;height:100px;font-size:36px;">${name.charAt(0).toUpperCase()}</div>`;
        }
        const content = `
            <div class="text-center mb-4">
                ${profileHtml}
                <h4 class="mt-3 mb-1">${name}</h4>
                <span class="badge ${status === 'Active' ? 'bg-success' : 'bg-danger'}">${status}</span>
            </div>
            <div class="info-group">
                <div class="info-group-title">Contact Information</div>
                <div class="row">
                    <div class="col-md-6 client-detail-item"><div class="client-detail-label">Email</div><div class="client-detail-value">${email}</div></div>
                    <div class="col-md-6 client-detail-item"><div class="client-detail-label">Phone</div><div class="client-detail-value">${phone}</div></div>
                </div>
                ${company !== '-' ? `<div class="client-detail-item"><div class="client-detail-label">Company</div><div class="client-detail-value">${company}</div></div>` : ''}
                ${city !== '-' ? `<div class="client-detail-item"><div class="client-detail-label">City</div><div class="client-detail-value">${city}</div></div>` : ''}
            </div>
            <div class="info-group">
                <div class="info-group-title">Account Information</div>
                <div class="row">
                    <div class="col-md-6 client-detail-item"><div class="client-detail-label">Client ID</div><div class="client-detail-value">#${clientId}</div></div>
                    <div class="col-md-6 client-detail-item"><div class="client-detail-label">Added By</div><div class="client-detail-value">${creatorInfo}</div></div>
                </div>
                <div class="row">
                    <div class="col-md-6 client-detail-item"><div class="client-detail-label">Credit Limit</div><div class="client-detail-value">${credit === 'Allowed' ? '<span class="badge bg-success">Allowed</span>' : '<span class="badge bg-danger">Not Allowed</span>'}</div></div>
                    <div class="col-md-6 client-detail-item"><div class="client-detail-label">Member Since</div><div class="client-detail-value">${createdDate}</div></div>
                </div>
            </div>
        `;
        document.getElementById('clientDetailsContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('viewClientModal')).show();
    } catch (error) {
        document.getElementById('clientDetailsContent').innerHTML = `<div class="text-center py-5"><i class="bi bi-exclamation-triangle text-danger display-4 mb-3"></i><p class="text-danger">Failed to load client details.</p></div>`;
        new bootstrap.Modal(document.getElementById('viewClientModal')).show();
        console.error(error);
    }
}

// ============================================================
// PASSWORD VALIDATION
// ============================================================
function validatePassword(formId) {
    const form = document.getElementById(formId);
    const password = form.querySelector('input[name="password"]');
    const confirm = form.querySelector('input[name="password_confirmation"]');
    if (formId === 'editClientForm' && (!password.value && !confirm.value)) return true;
    if (password.value.length < 8) {
        showToast('Password must be at least 8 characters', 'danger');
        password.focus();
        return false;
    }
    if (password.value !== confirm.value) {
        showToast('Passwords do not match', 'danger');
        confirm.focus();
        return false;
    }
    return true;
}

// ============================================================
// DOCUMENT READY
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // --- DataTable Initialization (safe: only once) ---
    if (!$.fn.DataTable.isDataTable('#clientsTable')) {
        $('#clientsTable').DataTable({
            "order": [[1, 'desc']],
            "columnDefs": [
                { "orderable": false, "targets": [0, 2, 12] } // checkbox, profile, actions
            ],
            "pageLength": 10,
            "language": {
                "emptyTable": "No clients found. Start by adding your first client."
            },
            "drawCallback": function() {
                setupCheckboxLogic();
            }
        });
    } else {
        // If already initialized, just redraw
        $('#clientsTable').DataTable().draw();
    }

    // --- Checkbox logic (also called after draw) ---
    function setupCheckboxLogic() {
        // Select All
        $(document).off('change', '#selectAll').on('change', '#selectAll', function() {
            var checked = this.checked;
            $('#clientsTable tbody .client-checkbox').prop('checked', checked);
            updateBulkEditButton();
        });

        // Individual checkbox
        $(document).off('change', '#clientsTable tbody .client-checkbox').on('change', '#clientsTable tbody .client-checkbox', function() {
            var allChecked = $('#clientsTable tbody .client-checkbox:checked').length === $('#clientsTable tbody .client-checkbox').length;
            $('#selectAll').prop('checked', allChecked);
            updateBulkEditButton();
        });

        updateBulkEditButton();
    }

    function updateBulkEditButton() {
        var checked = $('#clientsTable tbody .client-checkbox:checked').length;
        $('#bulkEditBtn').prop('disabled', checked === 0);
    }

    // Initial setup
    setupCheckboxLogic();

    // --- Bulk Edit Click ---
    $('#bulkEditBtn').on('click', function() {
        var selected = [];
        $('#clientsTable tbody .client-checkbox:checked').each(function() {
            selected.push($(this).data('id'));
        });
        if (selected.length === 0) {
            showToast('Please select at least one client.', 'warning');
            return;
        }
        openBulkEditModal(selected);
    });

    // --- Open Bulk Edit Modal ---
    async function openBulkEditModal(clientIds) {
        try {
            const response = await fetch('{{ route("admin.clients.bulk-edit") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ client_ids: clientIds })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Failed to load client data.');
            }

            const tbody = document.getElementById('bulkEditTableBody');
            tbody.innerHTML = '';
            data.clients.forEach(client => {
                const row = document.createElement('tr');
                row.dataset.clientId = client.id;

                const nameCell = document.createElement('td');
                nameCell.textContent = client.name;
                row.appendChild(nameCell);

                const statusCell = document.createElement('td');
                const statusSel = document.createElement('select');
                ['active','inactive'].forEach(opt => {
                    const op = document.createElement('option');
                    op.value = opt;
                    op.textContent = opt.charAt(0).toUpperCase() + opt.slice(1);
                    if (client.status === opt) op.selected = true;
                    statusSel.appendChild(op);
                });
                statusCell.appendChild(statusSel);
                row.appendChild(statusCell);

                const creditCell = document.createElement('td');
                const creditSel = document.createElement('select');
                [{value:0,label:'Not Allowed'},{value:1,label:'Allowed'}].forEach(opt => {
                    const op = document.createElement('option');
                    op.value = opt.value;
                    op.textContent = opt.label;
                    if (client.is_credit_allowed == opt.value) op.selected = true;
                    creditSel.appendChild(op);
                });
                creditCell.appendChild(creditSel);
                row.appendChild(creditCell);

                const cityCell = document.createElement('td');
                const cityInput = document.createElement('input');
                cityInput.type = 'text';
                cityInput.value = client.city || '';
                cityInput.classList.add('form-control','form-control-sm');
                cityCell.appendChild(cityInput);
                row.appendChild(cityCell);

                const stateCell = document.createElement('td');
                const stateInput = document.createElement('input');
                stateInput.type = 'text';
                stateInput.value = client.state || '';
                stateInput.classList.add('form-control','form-control-sm');
                stateCell.appendChild(stateInput);
                row.appendChild(stateCell);

                const contactCell = document.createElement('td');
                const contactSel = document.createElement('select');
                ['','email','phone','whatsapp'].forEach(opt => {
                    const op = document.createElement('option');
                    op.value = opt;
                    op.textContent = opt ? opt.charAt(0).toUpperCase()+opt.slice(1) : 'Select';
                    if (client.preferred_contact === opt) op.selected = true;
                    contactSel.appendChild(op);
                });
                contactCell.appendChild(contactSel);
                row.appendChild(contactCell);

                const notesCell = document.createElement('td');
                const notesText = document.createElement('textarea');
                notesText.value = client.notes || '';
                notesText.rows = 2;
                notesText.classList.add('form-control','form-control-sm');
                notesCell.appendChild(notesText);
                row.appendChild(notesCell);

                tbody.appendChild(row);
            });

            new bootstrap.Modal(document.getElementById('bulkEditModal')).show();
        } catch (error) {
            showToast(error.message, 'danger');
            console.error(error);
        }
    }

    // --- Save Bulk Edit ---
    document.getElementById('saveBulkEditBtn').addEventListener('click', async function() {
        const btn = this;
        const btnText = document.getElementById('saveBulkEditBtnText');
        const spinner = document.getElementById('saveBulkEditSpinner');
        btn.disabled = true;
        btnText.textContent = 'Saving...';
        spinner.classList.remove('d-none');

        try {
            const rows = document.querySelectorAll('#bulkEditTableBody tr');
            const clientsData = [];
            rows.forEach(row => {
                const id = row.dataset.clientId;
                clientsData.push({
                    id: id,
                    status: row.querySelector(`select`).value,
                    is_credit_allowed: row.querySelectorAll(`select`)[1].value,
                    city: row.querySelector(`input`).value,
                    state: row.querySelectorAll(`input`)[1].value,
                    preferred_contact: row.querySelectorAll(`select`)[2].value,
                    notes: row.querySelector(`textarea`).value,
                });
            });

            const response = await fetch('{{ route("admin.clients.bulk-update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ clients: clientsData })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Update failed.');
            }

            showToast(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('bulkEditModal')).hide();
            setTimeout(() => window.location.reload(), 1500);
        } catch (error) {
            showToast(error.message, 'danger');
            console.error(error);
        } finally {
            btn.disabled = false;
            btnText.textContent = 'Save Changes';
            spinner.classList.add('d-none');
        }
    });

    // --- Add Client Form ---
    document.getElementById('addClientForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        if (!validatePassword('addClientForm')) return;
        const submitBtn = document.getElementById('saveClientBtn');
        const btnText = document.getElementById('saveClientBtnText');
        const spinner = document.getElementById('saveClientSpinner');
        submitBtn.disabled = true;
        btnText.textContent = 'Creating...';
        spinner.classList.remove('d-none');
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Failed to create client');
            if (data.success) {
                showToast(data.message, 'success');
                form.reset();
                document.getElementById('addProfilePhotoPreview').src = 'https://ui-avatars.com/api/?name=Client&background=3b82f6&color=fff&size=150';
                form.classList.remove('was-validated');
                bootstrap.Modal.getInstance(document.getElementById('addClientModal')).hide();
                setTimeout(() => window.location.reload(), 1500);
            }
        } catch (error) {
            showToast(error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = 'Create Client';
            spinner.classList.add('d-none');
        }
    });

    // --- Edit Client Form ---
    document.getElementById('editClientForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        const pass = form.querySelector('input[name="password"]');
        const confirm = form.querySelector('input[name="password_confirmation"]');
        if (pass.value || confirm.value) {
            if (!validatePassword('editClientForm')) return;
        }
        const submitBtn = document.getElementById('updateClientBtn');
        const btnText = document.getElementById('updateClientBtnText');
        const spinner = document.getElementById('updateClientSpinner');
        submitBtn.disabled = true;
        btnText.textContent = 'Updating...';
        spinner.classList.remove('d-none');
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Failed to update client');
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('editClientModal')).hide();
                setTimeout(() => window.location.reload(), 1500);
            }
        } catch (error) {
            showToast(error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = 'Update Client';
            spinner.classList.add('d-none');
        }
    });

    // --- Delete Client ---
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-client');
        if (!btn) return;
        const id = btn.dataset.clientId;
        const name = btn.dataset.clientName;
        if (confirm(`Delete "${name}"?`)) {
            deleteClient(id);
        }
    });

    async function deleteClient(clientId) {
        try {
            const response = await fetch(`/admin/clients/${clientId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message);
            if (data.success) {
                showToast(data.message, 'success');
                const row = document.querySelector(`tr[data-client-id="${clientId}"]`);
                if (row) row.remove();
            }
        } catch (error) {
            showToast(error.message, 'danger');
        }
    }

    // --- Real-time password confirmation ---
    document.getElementById('addPasswordConfirmation').addEventListener('input', function() {
        const pass = document.getElementById('addPassword');
        this.setCustomValidity(pass.value !== this.value ? 'Passwords must match' : '');
    });
    document.getElementById('editPasswordConfirmation').addEventListener('input', function() {
        const pass = document.getElementById('editPassword');
        if (pass.value || this.value) {
            this.setCustomValidity(pass.value !== this.value ? 'Passwords must match' : '');
        } else {
            this.setCustomValidity('');
        }
    });

    // --- Tooltips ---
    document.querySelectorAll('[title]').forEach(el => new bootstrap.Tooltip(el));
});
</script>
@endpush