@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Discount Coupons</h2>
                <p class="text-muted mb-0">Manage discount coupons for your services</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" onclick="showAddCouponModal()" 
                        class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    Add Coupon
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
                            <i class="bi bi-tag"></i>
                        </div>
                        <h5 class="card-title text-muted mb-2">Total Coupons</h5>
                        <h3 class="card-value mb-0">{{ $coupons->count() }}</h3>
                    </div>
                    <div class="text-end">
                        <div class="card-change up">
                            <i class="bi bi-arrow-up-right"></i>
                            {{ $coupons->where('status', 'active')->count() }} active
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="card-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                            <i class="bi bi-percent"></i>
                        </div>
                        <h5 class="card-title text-muted mb-2">Percentage Coupons</h5>
                        <h3 class="card-value mb-0">{{ $coupons->where('type', 'percentage')->count() }}</h3>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success">% Off</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="card-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <h5 class="card-title text-muted mb-2">Flat Discount</h5>
                        <h3 class="card-value mb-0">{{ $coupons->where('type', 'flat')->count() }}</h3>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">Flat ₹</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="card-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h5 class="card-title text-muted mb-2">Active Offers</h5>
                        <h3 class="card-value mb-0" id="active-offers">0</h3>
                    </div>
                    <div class="text-end">
                        <div class="card-change down" id="expired-soon">0 expiring soon</div>
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
                            <!-- Status Tabs -->
                            <div class="nav nav-pills" id="status-tabs" role="tablist">
                                <button class="nav-link active" data-status="all" onclick="filterCoupons('all')">
                                    All Coupons
                                </button>
                                <button class="nav-link" data-status="active" onclick="filterCoupons('active')">
                                    <i class="bi bi-check-circle me-1"></i>Active
                                </button>
                                <button class="nav-link" data-status="inactive" onclick="filterCoupons('inactive')">
                                    <i class="bi bi-x-circle me-1"></i>Inactive
                                </button>
                                <button class="nav-link" data-status="expired" onclick="filterCoupons('expired')">
                                    <i class="bi bi-clock me-1"></i>Expired
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
                                <input type="text" id="search-coupons" class="form-control" 
                                       placeholder="Search coupon code or description..." onkeyup="searchCoupons()">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearCouponSearch()">
                                    Clear
                                </button>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-filter"></i> Type
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="filterByType('all')">All Types</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterByType('percentage')">Percentage</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterByType('flat')">Flat Amount</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coupons List -->
    <div class="row" id="coupons-container">
        @forelse($coupons as $coupon)
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4 coupon-card"
                 data-coupon-id="{{ $coupon->id }}"
                 data-status="{{ $coupon->status }}"
                 data-type="{{ $coupon->type }}"
                 data-code="{{ strtolower($coupon->code) }}">
                <div class="coupon-card-inner {{ $coupon->status == 'active' ? 'active' : 'inactive' }}">
                    <!-- Coupon Header -->
                    <div class="coupon-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="coupon-code">{{ $coupon->code }}</span>
                                <div class="coupon-status">
                                    <span class="badge {{ $coupon->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($coupon->status) }}
                                    </span>
                                    @if($coupon->type == 'percentage')
                                        <span class="badge bg-info ms-1">Percentage</span>
                                    @else
                                        <span class="badge bg-primary ms-1">Flat</span>
                                    @endif
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="editCoupon({{ $coupon->id }})">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a>
                                    </li>
                                    @if($coupon->status == 'active')
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="toggleCouponStatus({{ $coupon->id }}, 'inactive')">
                                                <i class="bi bi-pause me-2"></i>Deactivate
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="toggleCouponStatus({{ $coupon->id }}, 'active')">
                                                <i class="bi bi-play me-2"></i>Activate
                                            </a>
                                        </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDeleteCoupon({{ $coupon->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Coupon Body -->
                    <div class="coupon-body">
                        <!-- Discount Value -->
                        <div class="discount-value mb-3">
                            @if($coupon->type == 'percentage')
                                <h2 class="mb-0">{{ $coupon->value }}% OFF</h2>
                                <small class="text-muted">Discount</small>
                            @else
                                <h2 class="mb-0">₹{{ number_format($coupon->value) }}</h2>
                                <small class="text-muted">Flat Discount</small>
                            @endif
                        </div>
                        
                        <!-- Conditions -->
                        <div class="coupon-conditions">
                            @if($coupon->min_amount)
                                <div class="condition-item">
                                    <i class="bi bi-cart-check"></i>
                                    <span>Min. order ₹{{ number_format($coupon->min_amount) }}</span>
                                </div>
                            @endif
                            
                            @if($coupon->max_discount && $coupon->type == 'percentage')
                                <div class="condition-item">
                                    <i class="bi bi-currency-dollar"></i>
                                    <span>Max discount ₹{{ number_format($coupon->max_discount) }}</span>
                                </div>
                            @endif
                            
                            <div class="condition-item">
                                <i class="bi bi-calendar"></i>
                                <span>Valid till {{ \Carbon\Carbon::parse($coupon->end_date)->format('M d, Y') }}</span>
                            </div>
                            
                            @if($coupon->max_usage)
                                <div class="condition-item">
                                    <i class="bi bi-person-check"></i>
                                    <span>{{ $coupon->max_usage }} uses max</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Services -->
                        @if($coupon->services->count() > 0)
                            <div class="coupon-services mt-3">
                                <h6 class="text-muted mb-2">Applicable Services:</h6>
                                <div class="service-tags">
                                    @foreach($coupon->services->take(2) as $service)
                                        <span class="service-tag">{{ $service->name }}</span>
                                    @endforeach
                                    @if($coupon->services->count() > 2)
                                        <span class="service-tag">+{{ $coupon->services->count() - 2 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="coupon-services mt-3">
                                <h6 class="text-muted mb-2">Applicable Services:</h6>
                                <div class="service-tags">
                                    <span class="service-tag">All Services</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Coupon Footer -->
                    <div class="coupon-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Created: {{ \Carbon\Carbon::parse($coupon->created_at)->format('M d, Y') }}
                                </small>
                            </div>
                            <div>
                                @if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($coupon->end_date)))
                                    <span class="badge bg-danger">Expired</span>
                                @elseif(\Carbon\Carbon::now()->addDays(7)->gt(\Carbon\Carbon::parse($coupon->end_date)))
                                    <span class="badge bg-warning">Expiring Soon</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <div class="text-center py-5">
                    <i class="bi bi-tag display-4 text-muted mb-3"></i>
                    <h5>No coupons found</h5>
                    <p class="text-muted">Create your first discount coupon to get started.</p>
                    <button type="button" onclick="showAddCouponModal()" 
                            class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle"></i> Create Your First Coupon
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Coupon Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1" aria-labelledby="addCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCouponModalLabel">Add New Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCouponForm" action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Coupon Code *</label>
                            <input type="text" class="form-control" id="code" name="code" 
                                   placeholder="e.g., WELCOME20" required>
                            <div class="form-text">Unique code customers will enter</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Discount Type *</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="percentage">Percentage (%)</option>
                                <option value="flat">Flat Amount (₹)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="value" class="form-label" id="valueLabel">Discount Value *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="value" name="value" 
                                       min="0" step="0.01" required>
                                <span class="input-group-text" id="valueSuffix">%</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="min_amount" class="form-label">Minimum Order Amount (₹)</label>
                            <input type="number" class="form-control" id="min_amount" name="min_amount" 
                                   min="0" step="0.01" placeholder="Optional">
                            <div class="form-text">Minimum cart value to apply coupon</div>
                        </div>
                    </div>
                    
                    <div class="row" id="maxDiscountSection" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label for="max_discount" class="form-label">Maximum Discount (₹)</label>
                            <input type="number" class="form-control" id="max_discount" name="max_discount" 
                                   min="0" step="0.01" placeholder="Optional">
                            <div class="form-text">Maximum discount amount for percentage coupons</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_usage" class="form-label">Maximum Usage</label>
                            <input type="number" class="form-control" id="max_usage" name="max_usage" 
                                   min="0" placeholder="Unlimited if empty">
                            <div class="form-text">Total number of times coupon can be used</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="max_usage_per_client" class="form-label">Usage Per Client</label>
                            <input type="number" class="form-control" id="max_usage_per_client" name="max_usage_per_client" 
                                   min="0" placeholder="Unlimited if empty">
                            <div class="form-text">Maximum times a single client can use</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Applicable Services</label>
                        <div class="form-text mb-2">Select specific services or leave empty for all services</div>
                        <div class="services-container" style="max-height: 200px; overflow-y: auto;">
                            @foreach($services as $service)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="services[]" value="{{ $service->id }}" 
                                           id="service_{{ $service->id }}">
                                    <label class="form-check-label" for="service_{{ $service->id }}">
                                        {{ $service->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Create Coupon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Coupon Modal (UPDATED to work properly) -->
<div class="modal fade" id="editCouponModal" tabindex="-1" aria-labelledby="editCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCouponModalLabel">Edit Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCouponForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_code" class="form-label">Coupon Code *</label>
                            <input type="text" class="form-control" id="edit_code" name="code" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_type" class="form-label">Discount Type *</label>
                            <select class="form-select" id="edit_type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="percentage">Percentage (%)</option>
                                <option value="flat">Flat Amount (₹)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_value" class="form-label" id="edit_valueLabel">Discount Value *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="edit_value" name="value" 
                                       min="0" step="0.01" required>
                                <span class="input-group-text" id="edit_valueSuffix">%</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_min_amount" class="form-label">Minimum Order Amount (₹)</label>
                            <input type="number" class="form-control" id="edit_min_amount" name="min_amount" 
                                   min="0" step="0.01">
                        </div>
                    </div>
                    
                    <div class="row" id="edit_maxDiscountSection" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label for="edit_max_discount" class="form-label">Maximum Discount (₹)</label>
                            <input type="number" class="form-control" id="edit_max_discount" name="max_discount" 
                                   min="0" step="0.01">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_max_usage" class="form-label">Maximum Usage</label>
                            <input type="number" class="form-control" id="edit_max_usage" name="max_usage" 
                                   min="0" placeholder="Unlimited if empty">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_max_usage_per_client" class="form-label">Usage Per Client</label>
                            <input type="number" class="form-control" id="edit_max_usage_per_client" name="max_usage_per_client" 
                                   min="0" placeholder="Unlimited if empty">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status *</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Applicable Services</label>
                        <div class="services-container" style="max-height: 200px; overflow-y: auto;">
                            @foreach($services as $service)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="services[]" value="{{ $service->id }}" 
                                           id="edit_service_{{ $service->id }}">
                                    <label class="form-check-label" for="edit_service_{{ $service->id }}">
                                        {{ $service->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Update Coupon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCouponModal" tabindex="-1" aria-labelledby="deleteCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCouponModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this coupon? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCoupon">Delete</button>
            </div>
        </div>
    </div>
</div>

<style>
    .coupon-card-inner {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #e5e7eb;
    }
    
    .coupon-card-inner:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #3b82f6;
    }
    
    .coupon-card-inner.active {
        border-left: 4px solid #10b981;
    }
    
    .coupon-card-inner.inactive {
        border-left: 4px solid #6b7280;
        opacity: 0.8;
    }
    
    .coupon-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
    }
    
    .coupon-code {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        font-family: 'Courier New', monospace;
    }
    
    .coupon-body {
        padding: 20px;
    }
    
    .discount-value {
        text-align: center;
        padding: 10px;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .discount-value h2 {
        color: #3b82f6;
        font-weight: 800;
    }
    
    .coupon-conditions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .condition-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .condition-item i {
        color: #3b82f6;
        width: 20px;
        text-align: center;
    }
    
    .coupon-services {
        padding-top: 15px;
        border-top: 1px dashed #e5e7eb;
    }
    
    .service-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .service-tag {
        background: #f3f4f6;
        color: #4b5563;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        border: 1px solid #e5e7eb;
    }
    
    .coupon-footer {
        padding: 15px 20px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }
    
    /* Filter styles */
    .coupon-card.hidden {
        display: none;
    }
    
    /* Modal styles */
    .form-check {
        margin-bottom: 8px;
    }
    
    .services-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
    }
    
    .stats-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        padding: 20px;
        height: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
        border: none;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    
    .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    
    .card-value {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        margin: 10px 0 5px 0;
    }
    
    .card-change {
        font-size: 14px;
        display: flex;
        align-items: center;
    }
    
    .card-change.up {
        color: #10b981;
    }
    
    .card-change.down {
        color: #ef4444;
    }
    
    .section-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        padding: 20px;
        border: none;
    }
</style>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
(function() {
    let deleteCouponId = null;
    let currentFilter = 'all';
    let currentTypeFilter = 'all';
    let currentSearch = '';

    // Initialize when DOM is ready
    $(document).ready(function() {
        updateStats();
        setupFormEvents();
    });

    // Update statistics
    function updateStats() {
        const now = new Date();
        let activeOffers = 0;
        let expiringSoon = 0;
        
        $('.coupon-card').each(function() {
            const endDateText = $(this).find('.condition-item:has(.bi-calendar) span').text().replace('Valid till ', '');
            const endDate = new Date(endDateText);
            const status = $(this).data('status');
            
            if (status === 'active') {
                activeOffers++;
                
                // Check if expiring in next 7 days
                const daysUntilExpiry = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));
                if (daysUntilExpiry <= 7 && daysUntilExpiry > 0) {
                    expiringSoon++;
                }
            }
        });
        
        $('#active-offers').text(activeOffers);
        $('#expired-soon').text(expiringSoon + ' expiring soon');
    }

    // Setup form events
    function setupFormEvents() {
        // Show/hide max discount field based on type for add form
        $('#type').change(function() {
            const type = $(this).val();
            if (type === 'percentage') {
                $('#valueLabel').text('Discount Percentage *');
                $('#valueSuffix').text('%');
                $('#maxDiscountSection').show();
            } else {
                $('#valueLabel').text('Discount Amount (₹) *');
                $('#valueSuffix').text('₹');
                $('#maxDiscountSection').hide();
            }
        });

        // Set minimum end date based on start date for add form
        $('#start_date').change(function() {
            $('#end_date').attr('min', $(this).val());
            if ($('#end_date').val() && $('#end_date').val() < $(this).val()) {
                $('#end_date').val($(this).val());
            }
        });

        // Show/hide max discount field based on type for edit form
        $('#edit_type').change(function() {
            const type = $(this).val();
            if (type === 'percentage') {
                $('#edit_valueLabel').text('Discount Percentage *');
                $('#edit_valueSuffix').text('%');
                $('#edit_maxDiscountSection').show();
            } else {
                $('#edit_valueLabel').text('Discount Amount (₹) *');
                $('#edit_valueSuffix').text('₹');
                $('#edit_maxDiscountSection').hide();
            }
        });

        // Set minimum end date based on start date for edit form
        $('#edit_start_date').change(function() {
            $('#edit_end_date').attr('min', $(this).val());
            if ($('#edit_end_date').val() && $('#edit_end_date').val() < $(this).val()) {
                $('#edit_end_date').val($(this).val());
            }
        });

        // Add form submission (normal)
        $('#addCouponForm').on('submit', function(e) {
            // Normal form submit - page will reload
            return true;
        });

        // Edit form submission (AJAX to avoid page reload)
        $('#editCouponForm').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = $(form).find('button[type="submit"]');
            const spinner = submitBtn.find('.spinner-border');
            spinner.removeClass('d-none');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST', // because we have _method=PUT
                data: $(form).serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    showToast('success', 'Coupon updated successfully');
                    $('#editCouponModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                },
                error: function(xhr) {
                    let msg = 'Update failed. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    showToast('error', msg);
                },
                complete: function() {
                    spinner.addClass('d-none');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    }

    // Show Add Coupon Modal
    function showAddCouponModal() {
        // Reset form
        $('#addCouponForm')[0].reset();
        $('#type').trigger('change');
        $('#start_date').val(new Date().toISOString().split('T')[0]);
        
        // Set end date to 30 days from now
        const endDate = new Date();
        endDate.setDate(endDate.getDate() + 30);
        $('#end_date').val(endDate.toISOString().split('T')[0]);
        
        // Uncheck all services
        $('#addCouponForm input[name="services[]"]').prop('checked', false);
        
        const modal = new bootstrap.Modal(document.getElementById('addCouponModal'));
        modal.show();
    }

    // Edit Coupon - fetch data and populate modal
    function editCoupon(id) {
        $.ajax({
            url: `/admin/coupons/${id}/edit`,
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                // Populate form fields
                $('#edit_code').val(data.coupon.code);
                $('#edit_type').val(data.coupon.type).trigger('change');
                $('#edit_value').val(data.coupon.value);
                $('#edit_min_amount').val(data.coupon.min_amount);
                $('#edit_max_discount').val(data.coupon.max_discount);
                // Format date to YYYY-MM-DD for input type="date"
                if (data.coupon.start_date) {
                    const startDate = new Date(data.coupon.start_date);
                    $('#edit_start_date').val(startDate.toISOString().split('T')[0]);
                }
                if (data.coupon.end_date) {
                    const endDate = new Date(data.coupon.end_date);
                    $('#edit_end_date').val(endDate.toISOString().split('T')[0]);
                }
                $('#edit_max_usage').val(data.coupon.max_usage);
                $('#edit_max_usage_per_client').val(data.coupon.max_usage_per_client);
                $('#edit_status').val(data.coupon.status);

                // Set checkboxes for services
                $('input[name="services[]"]').prop('checked', false);
                if (data.services && data.services.length) {
                    data.services.forEach(serviceId => {
                        $(`#edit_service_${serviceId}`).prop('checked', true);
                    });
                }

                // Set form action
                $('#editCouponForm').attr('action', `/admin/coupons/${id}`);

                // Show modal
                new bootstrap.Modal(document.getElementById('editCouponModal')).show();
            },
            error: function(xhr) {
                showToast('error', 'Failed to load coupon data.');
            }
        });
    }

    // Toggle Coupon Status
    function toggleCouponStatus(id, newStatus) {
        if (!confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this coupon?`)) {
            return;
        }

        $.ajax({
            url: `/admin/coupons/${id}/status`,
            type: 'PATCH',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                showToast('success', `Coupon ${newStatus === 'active' ? 'activated' : 'deactivated'} successfully`);

                // Update UI
                const couponCard = $(`[data-coupon-id="${id}"]`).closest('.coupon-card');
                couponCard.data('status', newStatus);
                couponCard.find('.coupon-status .badge').first()
                    .removeClass('bg-success bg-secondary')
                    .addClass(newStatus === 'active' ? 'bg-success' : 'bg-secondary')
                    .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));

                // Update card appearance
                couponCard.find('.coupon-card-inner')
                    .removeClass('active inactive')
                    .addClass(newStatus);

                updateStats();
            },
            error: function(xhr) {
                showToast('error', 'Failed to update coupon status.');
            }
        });
    }

    // Confirm Delete Coupon
    function confirmDeleteCoupon(id) {
        deleteCouponId = id;
        $('#confirmDeleteCoupon').off('click').on('click', deleteCoupon);
        new bootstrap.Modal(document.getElementById('deleteCouponModal')).show();
    }

    // Delete Coupon
    function deleteCoupon() {
        $('#confirmDeleteCoupon').prop('disabled', true);
        $.ajax({
            url: `/admin/coupons/${deleteCouponId}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                showToast('success', 'Coupon deleted successfully');
                $('#deleteCouponModal').modal('hide');
                $(`[data-coupon-id="${deleteCouponId}"]`).closest('.coupon-card').fadeOut(300, function() {
                    $(this).remove();
                    updateStats();
                    if ($('.coupon-card').length === 0) {
                        location.reload(); // Reload to show empty state
                    }
                });
            },
            error: function(xhr) {
                showToast('error', 'Failed to delete coupon.');
                $('#confirmDeleteCoupon').prop('disabled', false);
            }
        });
    }

    // Filter Coupons
    function filterCoupons(status) {
        currentFilter = status;
        $('.nav-link').removeClass('active');
        $(`[data-status="${status}"]`).addClass('active');
        applyFilters();
    }

    function filterByType(type) {
        currentTypeFilter = type;
        applyFilters();
    }

    function searchCoupons() {
        currentSearch = $('#search-coupons').val().toLowerCase();
        applyFilters();
    }

    function clearCouponSearch() {
        $('#search-coupons').val('');
        currentSearch = '';
        applyFilters();
    }

    function applyFilters() {
        const now = new Date();
        
        $('.coupon-card').each(function() {
            const status = $(this).data('status');
            const type = $(this).data('type');
            const code = $(this).data('code');
            const endDateText = $(this).find('.condition-item:has(.bi-calendar) span').text().replace('Valid till ', '');
            const endDate = new Date(endDateText);
            
            let show = true;
            
            // Status filter
            if (currentFilter !== 'all') {
                if (currentFilter === 'expired') {
                    show = now > endDate;
                } else {
                    show = status === currentFilter && now <= endDate;
                }
            }
            
            // Type filter
            if (show && currentTypeFilter !== 'all') {
                show = type === currentTypeFilter;
            }
            
            // Search filter
            if (show && currentSearch) {
                show = code.includes(currentSearch);
            }
            
            // Show/hide card
            if (show) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
        
        // Show empty state if no results
        const visibleCards = $('.coupon-card:not(.hidden)').length;
        if (visibleCards === 0) {
            if ($('.no-results').length === 0) {
                $('#coupons-container').append(`
                    <div class="col-md-12 no-results">
                        <div class="text-center py-5">
                            <i class="bi bi-search display-4 text-muted mb-3"></i>
                            <h5>No coupons found</h5>
                            <p class="text-muted">Try changing your filters or search criteria.</p>
                            <button type="button" onclick="clearFilters()" 
                                    class="btn btn-outline-primary mt-3">
                                Clear All Filters
                            </button>
                        </div>
                    </div>
                `);
            }
        } else {
            $('.no-results').remove();
        }
        
        updateStats();
    }

    function clearFilters() {
        currentFilter = 'all';
        currentTypeFilter = 'all';
        currentSearch = '';
        
        $('.nav-link').removeClass('active');
        $('[data-status="all"]').addClass('active');
        $('#search-coupons').val('');
        
        applyFilters();
    }

    // Show toast notification
    function showToast(type, message) {
        // Create toast element
        const toastId = 'toast-' + Date.now();
        const icon = type === 'success' ? 'bi-check-circle' : 
                    type === 'error' ? 'bi-x-circle' : 
                    'bi-exclamation-triangle';
        const bgColor = type === 'success' ? 'success' : 
                       type === 'error' ? 'danger' : 
                       'warning';
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${bgColor} border-0 position-fixed top-0 end-0 m-3" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icon} me-2"></i>
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

    // Expose functions to global scope
    window.showAddCouponModal = showAddCouponModal;
    window.editCoupon = editCoupon;
    window.toggleCouponStatus = toggleCouponStatus;
    window.confirmDeleteCoupon = confirmDeleteCoupon;
    window.filterCoupons = filterCoupons;
    window.filterByType = filterByType;
    window.searchCoupons = searchCoupons;
    window.clearCouponSearch = clearCouponSearch;
    window.clearFilters = clearFilters;
})();
</script>
@endpush