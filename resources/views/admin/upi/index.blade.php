@extends('layouts.admin')

@section('title', 'UPI & QR Code Settings - SharpEdge Admin')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">UPI & QR Code Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">UPI Settings</li>
                </ol>
            </nav>
        </div>
        @if($setting->upi_id)
            <div class="d-flex gap-2">
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="bi bi-check-circle-fill me-1"></i> Configured
                </span>
            </div>
        @endif
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-4"></i>
            <div>
                <h6 class="mb-0">Success!</h6>
                <p class="mb-0">{{ session('success') }}</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
            <div>
                <h6 class="mb-0">Error!</h6>
                <p class="mb-0">{{ session('error') }}</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Left: UPI Configuration -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-qr-code text-primary me-2"></i>
                        UPI Configuration
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.upi.store') }}" id="upiForm" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="upi_id" class="form-label fw-semibold">UPI ID <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-at"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control rounded-end-2 @error('upi_id') is-invalid @enderror" 
                                           id="upi_id" 
                                           name="upi_id" 
                                           value="{{ old('upi_id', $setting->upi_id) }}"
                                           placeholder="username@okhdfcbank"
                                           required
                                           autocomplete="off">
                                    @error('upi_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle me-1"></i> Enter your personal UPI ID where all payments will be received
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="d-flex align-items-center h-100">
                                    @if($setting->upi_id)
                                        <div class="alert alert-success py-2 px-3 mb-0 w-100">
                                            <i class="bi bi-check-circle-fill me-1"></i> Active & Configured
                                        </div>
                                    @else
                                        <div class="alert alert-warning py-2 px-3 mb-0 w-100">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Not Configured
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-check mt-4 mb-4 p-3 bg-light rounded-3">
                            <input class="form-check-input me-2" type="checkbox" id="confirm_upi" required>
                            <label class="form-check-label fw-medium" for="confirm_upi">
                                <i class="bi bi-shield-check me-1"></i>
                                I confirm that this UPI ID belongs to me and all payments will be directed to this account
                            </label>
                            <div class="invalid-feedback">
                                You must confirm this before saving.
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-2">
                                <i class="bi bi-save me-2"></i>
                                {{ $setting->upi_id ? 'Update UPI ID' : 'Save Configuration' }}
                            </button>
                            
                            @if($setting->upi_id)
                                <a href="{{ route('admin.upi.regenerate') }}" 
                                   class="btn btn-outline-warning px-4 py-2 rounded-2" 
                                   onclick="return confirm('Are you sure you want to regenerate QR code? This will create a new QR code.')">
                                    <i class="bi bi-arrow-clockwise me-2"></i>
                                    Regenerate QR
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-outline-danger px-4 py-2 rounded-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#resetModal">
                                    <i class="bi bi-trash me-2"></i>
                                    Reset Settings
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- How It Works -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        How It Works
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3 border rounded-3 h-100">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                        <i class="bi bi-1-circle text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-2">Set Your UPI ID</h6>
                                    <p class="text-muted mb-0 small">Enter your personal UPI ID to receive payments directly into your account.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3 border rounded-3 h-100">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                        <i class="bi bi-2-circle text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-2">Share QR Code</h6>
                                    <p class="text-muted mb-0 small">Share the generated QR code with clients for instant payments via any UPI app.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3 border rounded-3 h-100">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                        <i class="bi bi-3-circle text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-2">Receive Payments</h6>
                                    <p class="text-muted mb-0 small">All payments come directly to your UPI account instantly with no delays.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3 border rounded-3 h-100">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                        <i class="bi bi-4-circle text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-2">Verify Payments</h6>
                                    <p class="text-muted mb-0 small">Verify payment receipts in the admin panel before delivering services.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: QR Code Display -->
        @if($setting->upi_id)
        <div class="col-lg-4">
            <!-- QR Code Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-qr-code-scan text-primary me-2"></i>
                        Your QR Code
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center">
                        @if($setting->qr_code_url)
                            <!-- QR Code Display -->
                            <div class="mb-4 p-4 bg-white border rounded-3 d-inline-block shadow-sm">
                                <img src="{{ $setting->qr_code_url }}" 
                                     alt="UPI QR Code" 
                                     class="img-fluid"
                                     style="width: 240px; height: 240px;">
                            </div>
                            
                            <!-- UPI ID Display -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">UPI ID</label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control fw-semibold text-center border-2" 
                                           id="upiDisplay" 
                                           value="{{ $setting->upi_id }}" 
                                           readonly
                                           style="font-size: 1.1rem;">
                                    <button class="btn btn-outline-primary" 
                                            type="button" 
                                            onclick="copyUPI()"
                                            data-bs-toggle="tooltip" 
                                            title="Copy UPI ID"
                                            data-bs-placement="top">
                                        <i class="bi bi-copy"></i>
                                    </button>
                                </div>
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-info-circle me-1"></i> Share this UPI ID or QR code for payments
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.upi.download') }}" 
                                   class="btn btn-primary btn-lg rounded-2">
                                    <i class="bi bi-download me-2"></i>
                                    Download QR Code
                                </a>
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.upi.view') }}" 
                                       class="btn btn-outline-primary flex-fill rounded-2"
                                       target="_blank">
                                        <i class="bi bi-arrows-fullscreen me-2"></i>
                                        Full Screen
                                    </a>
                                    
                                    <a href="upi://pay?pa={{ $setting->upi_id }}" 
                                       class="btn btn-outline-success flex-fill rounded-2">
                                        <i class="bi bi-phone me-2"></i>
                                        Test Payment
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                QR Code not generated. Please save your UPI ID first.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        Payment Overview
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 border rounded-3">
                                <div class="fw-bold text-primary fs-3 mb-1">₹ 0</div>
                                <div class="text-muted small">Today</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded-3">
                                <div class="fw-bold text-success fs-3 mb-1">0</div>
                                <div class="text-muted small">Pending</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded-3">
                                <div class="fw-bold text-warning fs-3 mb-1">₹ 0</div>
                                <div class="text-muted small">This Month</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded-3">
                                <div class="fw-bold text-info fs-3 mb-1">0</div>
                                <div class="text-muted small">Total Orders</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Payment Status</span>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Last Updated</span>
                            <span class="fw-medium">{{ now()->format('d M, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-qr-code display-1 text-muted opacity-25"></i>
                    </div>
                    <h5 class="mb-3">No UPI Configuration</h5>
                    <p class="text-muted mb-4">
                        Set up your UPI ID to start receiving payments via QR code.
                    </p>
                    <button type="button" class="btn btn-primary px-4 py-2" onclick="document.getElementById('upi_id').focus()">
                        <i class="bi bi-plus-circle me-2"></i>
                        Configure Now
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Reset Confirmation Modal -->
@if($setting->upi_id)
<div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="resetModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Confirm Reset
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-exclamation display-1 text-warning opacity-50"></i>
                </div>
                <h5 class="text-center mb-3">Reset UPI Configuration?</h5>
                <p class="text-muted text-center mb-4">
                    This action will permanently remove your UPI ID and QR code from the system.
                </p>
                
                <div class="alert alert-warning border-start border-warning border-3">
                    <div class="d-flex">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                        <div>
                            <strong class="d-block mb-1">What will be affected:</strong>
                            <ul class="mb-0 ps-3 small">
                                <li>UPI ID will be removed</li>
                                <li>QR code will be deleted</li>
                                <li>Payment processing will stop</li>
                                <li>You'll need to reconfigure for new payments</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.upi.destroy', $setting->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-trash me-2"></i>
                        Reset Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        // Focus on UPI input
        const upiInput = document.getElementById('upi_id');
        if (upiInput) upiInput.focus();
        
        // Validate UPI format on blur
        if (upiInput) {
            upiInput.addEventListener('blur', function() {
                const upiValue = this.value.trim();
                const upiRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+$/i;
                
                if (upiValue && !upiRegex.test(upiValue)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid UPI ID Format',
                        html: `
                            <div class="text-start">
                                <p class="mb-2">Please enter a valid UPI ID format:</p>
                                <ul class="text-start ps-3 mb-0">
                                    <li>username@bankname</li>
                                    <li>Examples: john@upi, jane@okicici</li>
                                </ul>
                            </div>
                        `,
                        confirmButtonColor: '#4f46e5',
                        confirmButtonText: 'Got it'
                    });
                    this.focus();
                }
            });
        }
        
        // Form validation
        const upiForm = document.getElementById('upiForm');
        if (upiForm) {
            upiForm.addEventListener('submit', function(e) {
                const confirmCheckbox = document.getElementById('confirm_upi');
                if (!confirmCheckbox.checked) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Confirmation Required',
                        text: 'Please confirm that the UPI ID belongs to you before saving.',
                        confirmButtonColor: '#4f46e5'
                    });
                }
                
                // Add Bootstrap validation
                if (!upiForm.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                upiForm.classList.add('was-validated');
            });
        }
        
        // Auto-format UPI ID (add @ if missing)
        if (upiInput) {
            upiInput.addEventListener('input', function(e) {
                let value = this.value;
                if (value.includes('@') && !value.endsWith('@')) {
                    this.value = value.replace(/\s+/g, '').toLowerCase();
                }
            });
        }
    });
    
    // Copy UPI ID to clipboard
    function copyUPI() {
        const upiInput = document.getElementById('upiDisplay');
        if (!upiInput) return;
        
        upiInput.select();
        upiInput.setSelectionRange(0, 99999);
        
        navigator.clipboard.writeText(upiInput.value).then(() => {
            // Show success feedback
            const copyBtn = upiInput.nextElementSibling;
            const originalHTML = copyBtn.innerHTML;
            
            copyBtn.innerHTML = '<i class="bi bi-check2"></i>';
            copyBtn.classList.remove('btn-outline-primary');
            copyBtn.classList.add('btn-success');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                copyBtn.innerHTML = originalHTML;
                copyBtn.classList.remove('btn-success');
                copyBtn.classList.add('btn-outline-primary');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            // Fallback for older browsers
            document.execCommand('copy');
        });
    }
    
    // Test UPI payment
    function testUPIPayment() {
        const upiId = document.getElementById('upiDisplay')?.value;
        if (!upiId) return;
        
        const upiLink = `upi://pay?pa=${upiId}&pn=SharpEdge&mc=0000&tid=TEST${Date.now()}&tn=Test Payment&am=1.00&cu=INR`;
        
        // Try to open UPI app
        window.location.href = upiLink;
        
        // Fallback: Show QR code
        setTimeout(() => {
            Swal.fire({
                title: 'Test Payment',
                html: `
                    <div class="text-center">
                        <p class="mb-3">Scan QR code or copy UPI ID:</p>
                        <div class="mb-3">
                            <img src="{{ $setting->qr_code_url ?? '' }}" 
                                 alt="UPI QR" 
                                 style="width: 200px; height: 200px;" 
                                 class="border rounded p-2">
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" 
                                   class="form-control" 
                                   value="${upiId}" 
                                   readonly>
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    onclick="copyUPI()">
                                <i class="bi bi-copy"></i>
                            </button>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Open UPI App',
                cancelButtonText: 'Close'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = upiLink;
                }
            });
        }, 1000);
    }
</script>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 12px;
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    
    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
    }
    
    .btn-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    
    .btn-primary:hover {
        background-color: #4338ca;
        border-color: #4338ca;
    }
    
    .btn-outline-primary {
        color: #4f46e5;
        border-color: #4f46e5;
    }
    
    .btn-outline-primary:hover {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }
    
    .form-check-input:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    
    .form-check-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
    }
</style>
@endsection