@extends('layouts.marketing')

@section('title', 'Client Management - SharpEdge Marketing')

@section('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    /* Statistics Cards */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-info h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
        line-height: 1;
    }
    
    .stat-info p {
        color: var(--gray-500);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0;
    }
    
    /* Main Content Card */
    .main-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }
    
    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .header-left h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .header-left p {
        color: var(--gray-500);
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    /* Header Actions - Search and Add Button */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    /* Search box on the right side */
    .header-search-box {
        position: relative;
        width: 250px;
    }
    
    .header-search-box input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-700);
    }
    
    .header-search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }
    
    /* Filters */
    .filters {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .status-filter {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .status-btn {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        background: white;
        color: var(--gray-600);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .status-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .status-btn:hover {
        background: var(--primary-light);
        color: white;
        border-color: var(--primary-color);
    }
    
    /* Table Styles - Simple Black Header */
    .table-container {
        padding: 0;
        overflow-x: auto;
    }
    
    .clients-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    /* Black Table Header - SIMPLE VERSION */
    .clients-table thead {
        background-color: #3b4f6b !important;
    }
    
    .clients-table th {
        padding: 1rem 1rem;
        text-align: left;
        font-weight: 600;
        color: white !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: none;
        white-space: nowrap;
    }
    
    /* Table body styles */
    .clients-table td {
        padding: 1rem 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
        color: var(--gray-700);
        font-size: 0.875rem;
        background: white;
    }
    
    .clients-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .clients-table tbody tr:hover {
        background-color: var(--gray-50);
    }
    
    /* ID Column - Client Code */
    .client-code-box {
        display: inline-block;
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary-color);
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        font-size: 0.875rem;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    /* Profile Column */
    .client-profile-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .client-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        flex-shrink: 0;
        object-fit: cover;
    }
    
    .client-details {
        display: flex;
        flex-direction: column;
    }
    
    .client-name-row {
        font-weight: 600;
        color: var(--dark-color);
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
    }
    
    .client-id-label {
        font-size: 0.75rem;
        color: var(--gray-500);
    }
    
    /* Status Badge */
    .status-with-dot {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .status-dot.active {
        background: var(--secondary-color);
    }
    
    .status-dot.inactive {
        background: var(--gray-500);
    }
    
    .status-dot.pending {
        background: var(--warning-color);
    }
    
    .status-text {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .status-active-text {
        color: var(--secondary-color);
    }
    
    .status-inactive-text {
        color: var(--gray-500);
    }
    
    .status-pending-text {
        color: var(--warning-color);
    }
    
    /* Colorful Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Edit button - Blue */
    .action-btn.edit {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }
    
    .action-btn.edit:hover {
        background: linear-gradient(135deg, var(--primary-dark), #1d4ed8);
    }
    
    /* Delete button - Red */
    .action-btn.delete {
        background: linear-gradient(135deg, var(--danger-color), #dc2626);
    }
    
    .action-btn.delete:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }
    
    /* Add Client Button */
    .btn-add {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    /* Laravel Pagination Styles */
    .pagination-container {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .pagination-info {
        color: var(--gray-600);
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .pagination {
        display: flex;
        gap: 0.5rem;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
    }
    
    .page-item .page-link {
        border: 1px solid var(--gray-300);
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        color: var(--primary-color);
        background: white;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        min-width: 40px;
        text-align: center;
    }
    
    .page-item.active .page-link {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        font-weight: 600;
    }
    
    .page-item.disabled .page-link {
        color: var(--gray-400);
        cursor: not-allowed;
    }
    
    .page-item .page-link:hover:not(.active):not(.disabled) {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }
    
    /* Modal Styles - Updated for two-column layout */
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .form-label.required:after {
        content: " *";
        color: #dc3545;
    }
    
    /* Section headers in modal */
    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--gray-300);
    }
    
    /* Profile photo upload area */
    .profile-upload-area {
        border: 2px dashed var(--gray-300);
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
        background: var(--gray-50);
        margin-bottom: 1.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .profile-upload-area:hover {
        border-color: var(--primary-color);
        background: var(--primary-light-bg);
    }
    
    .profile-upload-area i {
        font-size: 2rem;
        color: var(--gray-400);
        margin-bottom: 0.5rem;
    }
    
    .profile-upload-area p {
        color: var(--gray-600);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .profile-upload-area small {
        color: var(--gray-500);
        font-size: 0.75rem;
    }
    
    /* Password hint */
    .password-hint {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-top: 0.25rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }
        
        .filters {
            flex-direction: column;
            align-items: stretch;
        }
        
        .header-actions {
            flex-direction: column;
            width: 100%;
            gap: 0.75rem;
        }
        
        .header-search-box {
            width: 100%;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .clients-table {
            min-width: 1000px;
        }
        
        .modal-body .row {
            flex-direction: column;
        }
        
        .pagination-container {
            flex-direction: column;
            align-items: stretch;
        }
        
        .pagination-info {
            text-align: center;
            width: 100%;
        }
        
        .pagination {
            justify-content: center;
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header">
        <h1>Client Management</h1>
        <p>Manage all clients and their information</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalClients }}</h3>
                <p>Total Clients</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary-color), #0da271);">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $activeClients }}</h3>
                <p>Active Clients</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--gray-500), var(--gray-600));">
                <i class="fas fa-user-slash"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $inactiveClients }}</h3>
                <p>Inactive Clients</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #e58e0b);">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $pendingClients }}</h3>
                <p>Pending Clients</p>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="main-card">
        <div class="card-header">
            <div class="header-left">
                <h2>Client List</h2>
                <p>All registered clients in the system</p>
            </div>
            
            <!-- Search and Add Button on the right -->
            <div class="header-actions">
                <form method="GET" action="{{ route('marketing.clients.index') }}" class="header-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search clients..." 
                           value="{{ request('search') }}" autocomplete="off">
                </form>
                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addClientModal">
                    <i class="fas fa-plus"></i> Add New Client
                </button>
            </div>
        </div>
        
        <!-- Filters - Status Buttons -->
        <div class="filters">
            <div class="status-filter">
                <a href="{{ route('marketing.clients.index', ['search' => request('search')]) }}" 
                   class="status-btn {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('marketing.clients.index', ['status' => 'active', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'active' ? 'active' : '' }}">
                    Active
                </a>
                <a href="{{ route('marketing.clients.index', ['status' => 'inactive', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'inactive' ? 'active' : '' }}">
                    Inactive
                </a>
                <a href="{{ route('marketing.clients.index', ['status' => 'pending', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'pending' ? 'active' : '' }}">
                    Pending
                </a>
            </div>
        </div>

        <!-- Clients Table -->
        <div class="table-container">
            <table class="clients-table" id="clientsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>PROFILE</th>
                        <th>CLIENT NAME</th>
                        <th>EMAIL</th>
                        <th>PHONE</th>
                        <th>COMPANY</th>
                        <th>CITY</th>
                        <th>STATUS</th>
                        <th>CREATED DATE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr data-client-id="{{ $client->id }}">
                        <!-- ID Column -->
                        <td>
                            <span class="client-code-box">
                                {{ $client->client_code ?? 'CLT-' . str_pad($client->id, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        
                        <!-- PROFILE Column -->
                        <td>
                            <div class="client-profile-cell">
                                @if($client->profile_photo)
                                    @php
                                        // Remove 'public/' prefix if it exists
                                        $imagePath = str_replace('public/', '', $client->profile_photo);
                                    @endphp
                                    <img src="{{ asset('storage/' . $imagePath) }}" 
                                         alt="{{ $client->name }}" 
                                         class="client-avatar"
                                         onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="client-avatar" style="display: none;">
                                        {{ strtoupper(substr($client->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="client-avatar">
                                        {{ strtoupper(substr($client->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="client-details">
                                    <div class="client-name-row">{{ $client->name }}</div>
                                    <div class="client-id-label">ID: {{ $client->id }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- CLIENT NAME Column -->
                        <td>{{ $client->name }}</td>
                        
                        <!-- EMAIL Column -->
                        <td>{{ $client->email }}</td>
                        
                        <!-- PHONE Column -->
                        <td>{{ $client->phone }}</td>
                        
                        <!-- COMPANY Column -->
                        <td>
                            @if($client->company_name)
                                {{ $client->company_name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        
                        <!-- CITY Column -->
                        <td>
                            @if($client->city)
                                {{ $client->city }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        
                        <!-- STATUS Column -->
                        <td>
                            <div class="status-with-dot">
                                @if($client->status == 'active')
                                    <span class="status-dot active"></span>
                                    <span class="status-text status-active-text">ACTIVE</span>
                                @elseif($client->status == 'inactive')
                                    <span class="status-dot inactive"></span>
                                    <span class="status-text status-inactive-text">INACTIVE</span>
                                @else
                                    <span class="status-dot pending"></span>
                                    <span class="status-text status-pending-text">PENDING</span>
                                @endif
                            </div>
                        </td>
                        
                        <!-- CREATED DATE Column -->
                        <td>{{ $client->created_at->format('M d, Y') }}</td>
                        
                        <!-- ACTIONS Column -->
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit edit-client" 
                                        data-client-id="{{ $client->id }}"
                                        data-client-name="{{ $client->name }}"
                                        data-client-email="{{ $client->email }}"
                                        data-client-phone="{{ $client->phone }}"
                                        data-client-status="{{ $client->status }}"
                                        data-client-company="{{ $client->company_name ?? '' }}"
                                        data-client-city="{{ $client->city ?? '' }}"
                                        data-client-dob="{{ $client->dob ?? '' }}"
                                        data-client-gender="{{ $client->gender ?? '' }}"
                                        data-client-address="{{ $client->address ?? '' }}"
                                        data-client-state="{{ $client->state ?? '' }}"
                                        data-client-pincode="{{ $client->pincode ?? '' }}"
                                        data-client-gst="{{ $client->gst_number ?? '' }}"
                                        data-client-preferred-contact="{{ $client->preferred_contact ?? '' }}"
                                        data-client-source="{{ $client->source ?? '' }}"
                                        data-client-notes="{{ $client->notes ?? '' }}"
                                        title="Edit Client">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete delete-client" 
                                        data-client-id="{{ $client->id }}"
                                        data-client-name="{{ $client->name }}"
                                        title="Delete Client">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-users fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No clients found</p>
                            @if(request('search') || request('status'))
                                <a href="{{ route('marketing.clients.index') }}" class="btn btn-sm btn-primary mt-2">
                                    Clear Filters
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Laravel Pagination -->
            @if($clients->hasPages())
            <div class="pagination-container d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }}
                    of {{ $clients->total() }} entries
                </div>

                <div>
                    {{ $clients->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('marketing.clients.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column: Basic & Additional Information -->
                        <div class="col-md-8">
                            <!-- Profile Photo Upload -->
                            <div class="profile-upload-area" onclick="document.getElementById('profile_photo').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Upload profile photo (JPG, PNG, max 2MB)</p>
                                <small>Click to browse or drag and drop</small>
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/*" hidden>
                            </div>
                            
                            <!-- Basic Information Section -->
                            <h6 class="section-title">Basic Information</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required">Full Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required">Email Address</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required">Phone Number</label>
                                    <input type="text" class="form-control" name="phone" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required">Status</label>
                                    <select class="form-select" name="status" required>
                                        <option value="pending" selected>Pending</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" name="dob">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Additional Information Section -->
                            <h6 class="section-title">Additional Information</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="2"></textarea>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="city">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control" name="state">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" class="form-control" name="pincode">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" class="form-control" name="company_name">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">GST Number</label>
                                    <input type="text" class="form-control" name="gst_number">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Preferred Contact</label>
                                    <select class="form-select" name="preferred_contact">
                                        <option value="">Select Preferred Contact</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="sms">SMS</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Source</label>
                                    <select class="form-select" name="source">
                                        <option value="">Select Source</option>
                                        <option value="website">Website</option>
                                        <option value="referral">Referral</option>
                                        <option value="social_media">Social Media</option>
                                        <option value="advertisement">Advertisement</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Login Information -->
                        <div class="col-md-4 border-start">
                            <h6 class="section-title">Login Information</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label required">Password</label>
                                    <input type="password" class="form-control" name="password" required minlength="8">
                                    <div class="password-hint">Required for client login (minimum 8 characters)</div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label required">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" required minlength="8">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editClientForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column: Basic & Additional Information -->
                        <div class="col-md-8">
                            <!-- Profile Photo Upload -->
                            <div class="profile-upload-area" onclick="document.getElementById('edit_profile_photo').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Upload profile photo (JPG, PNG, max 2MB)</p>
                                <small>Click to browse or drag and drop</small>
                                <input type="file" name="profile_photo" id="edit_profile_photo" accept="image/*" hidden>
                            </div>
                            
                            <!-- Basic Information Section -->
                            <h6 class="section-title">Basic Information</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required">Full Name</label>
                                    <input type="text" class="form-control" name="name" id="editName" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required">Email Address</label>
                                    <input type="email" class="form-control" name="email" id="editEmail" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required">Phone Number</label>
                                    <input type="text" class="form-control" name="phone" id="editPhone" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required">Status</label>
                                    <select class="form-select" name="status" id="editStatus" required>
                                        <option value="pending">Pending</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" name="dob" id="editDob">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select" name="gender" id="editGender">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Additional Information Section -->
                            <h6 class="section-title">Additional Information</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" id="editAddress" rows="2"></textarea>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="city" id="editCity">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control" name="state" id="editState">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" class="form-control" name="pincode" id="editPincode">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" class="form-control" name="company_name" id="editCompany">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">GST Number</label>
                                    <input type="text" class="form-control" name="gst_number" id="editGst">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Preferred Contact</label>
                                    <select class="form-select" name="preferred_contact" id="editPreferredContact">
                                        <option value="">Select Preferred Contact</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="sms">SMS</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Source</label>
                                    <select class="form-select" name="source" id="editSource">
                                        <option value="">Select Source</option>
                                        <option value="website">Website</option>
                                        <option value="referral">Referral</option>
                                        <option value="social_media">Social Media</option>
                                        <option value="advertisement">Advertisement</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="notes" id="editNotes" rows="2" placeholder="Any additional notes..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Login Information (Empty for Edit) -->
                        <div class="col-md-4 border-start">
                            <h6 class="section-title">Login Information</h6>
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    Password cannot be changed here. Use the password reset feature if needed.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Client Modal -->
<div class="modal fade" id="deleteClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete client <strong id="deleteClientName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteClientForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Client</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- DataTables CSS & JS (if not loaded in layout) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Show success/error messages from session
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    @endif

    // ✅ DataTables Initialization for Client Table
    $('#clientsTable').DataTable({
        order: [[0, 'desc']], // Sort by ID (first column) descending
        paging: false,        // Custom pagination exists
        searching: false,     // Custom search exists
        info: false,          // Custom info exists
        columnDefs: [
            {
                targets: 0, // ID column (client code)
                type: 'string',
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        // Extract numeric part from CLT-000123 or just numeric
                        const match = data.match(/CLT-(\d+)/);
                        if (match) {
                            return parseInt(match[1], 10);
                        }
                        // If not CLT- format, try to parse numeric
                        const num = parseInt(data, 10);
                        return isNaN(num) ? data : num;
                    }
                    return data;
                }
            }
            // আপনি চাইলে অন্যান্য কলামের জন্য আরও columnDefs যোগ করতে পারেন
        ]
    });

    // Edit Client Button Click
    $(document).on('click', '.edit-client', function() {
        const clientId = $(this).data('client-id');
        const clientName = $(this).data('client-name');
        const clientEmail = $(this).data('client-email');
        const clientPhone = $(this).data('client-phone');
        const clientStatus = $(this).data('client-status');
        const clientCompany = $(this).data('client-company');
        const clientCity = $(this).data('client-city');
        const clientDob = $(this).data('client-dob');
        const clientGender = $(this).data('client-gender');
        const clientAddress = $(this).data('client-address');
        const clientState = $(this).data('client-state');
        const clientPincode = $(this).data('client-pincode');
        const clientGst = $(this).data('client-gst');
        const clientPreferredContact = $(this).data('client-preferred-contact');
        const clientSource = $(this).data('client-source');
        const clientNotes = $(this).data('client-notes');
        
        // Set form action
        $('#editClientForm').attr('action', "{{ route('marketing.clients.update', ':id') }}".replace(':id', clientId));
        
        // Populate form fields
        $('#editName').val(clientName || '');
        $('#editEmail').val(clientEmail || '');
        $('#editPhone').val(clientPhone || '');
        $('#editStatus').val(clientStatus || 'pending');
        $('#editCompany').val(clientCompany || '');
        $('#editCity').val(clientCity || '');
        $('#editDob').val(clientDob || '');
        $('#editGender').val(clientGender || '');
        $('#editAddress').val(clientAddress || '');
        $('#editState').val(clientState || '');
        $('#editPincode').val(clientPincode || '');
        $('#editGst').val(clientGst || '');
        $('#editPreferredContact').val(clientPreferredContact || '');
        $('#editSource').val(clientSource || '');
        $('#editNotes').val(clientNotes || '');
        
        // Show the modal
        const editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
        editModal.show();
    });

    // Delete Client Button Click
    $(document).on('click', '.delete-client', function() {
        const clientId = $(this).data('client-id');
        const clientName = $(this).data('client-name');
        
        $('#deleteClientName').text(clientName);
        $('#deleteClientForm').attr('action', "{{ route('marketing.clients.destroy', ':id') }}".replace(':id', clientId));
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteClientModal'));
        deleteModal.show();
    });

    // Auto-submit search on Enter key
    $('input[name="search"]').on('keypress', function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });

    // Auto-hide toastr messages after 5 seconds
    setTimeout(function() {
        $('.toast').fadeOut('slow');
    }, 5000);

    // Form validation for add client modal
    $('#addClientModal form').on('submit', function(e) {
        const name = $(this).find('input[name="name"]').val();
        const email = $(this).find('input[name="email"]').val();
        const phone = $(this).find('input[name="phone"]').val();
        const password = $(this).find('input[name="password"]').val();
        const passwordConfirmation = $(this).find('input[name="password_confirmation"]').val();
        
        if (!name || !email || !phone || !password || !passwordConfirmation) {
            e.preventDefault();
            toastr.error('Please fill all required fields');
            return false;
        }
        
        if (password !== passwordConfirmation) {
            e.preventDefault();
            toastr.error('Passwords do not match');
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            toastr.error('Password must be at least 8 characters long');
            return false;
        }
    });

    // Form validation for edit client modal
    $('#editClientForm').on('submit', function(e) {
        const name = $('#editName').val();
        const email = $('#editEmail').val();
        const phone = $('#editPhone').val();
        
        if (!name || !email || !phone) {
            e.preventDefault();
            toastr.error('Please fill all required fields');
        }
    });

    // Show loading state on form submission
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
    });

    // Profile photo upload click handler
    $('#profile_photo, #edit_profile_photo').on('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
        const uploadArea = $(this).closest('.profile-upload-area');
        
        if (e.target.files[0]) {
            // Check file size (2MB = 2097152 bytes)
            if (e.target.files[0].size > 2097152) {
                toastr.error('File size must be less than 2MB');
                $(this).val('');
                return;
            }
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(e.target.files[0].type)) {
                toastr.error('Only JPG and PNG files are allowed');
                $(this).val('');
                return;
            }
            
            // Update UI to show file selected
            uploadArea.find('i').removeClass('fa-cloud-upload-alt').addClass('fa-check-circle text-success');
            uploadArea.find('p').html(`<strong>${fileName}</strong>`);
            uploadArea.css({
                'border-color': 'var(--success-color)',
                'background-color': 'rgba(40, 167, 69, 0.1)'
            });
        }
    });
});
</script>
@endsection