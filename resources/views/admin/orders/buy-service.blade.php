@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Show Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Validation Error:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Header -->
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2">Buy Service</h2>
                <p class="text-muted mb-0">Admin direct order creation for clients</p>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <span class="badge bg-primary fs-6">Admin Order</span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="helpDropdown" data-bs-toggle="dropdown">
                        <i class="bi bi-question-circle"></i>
                    </button>
                    <div class="dropdown-menu">
                        <p class="dropdown-item-text small">Orders created here are auto-approved.<br>Tasks are auto-generated from work process.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="section-card">
                <div class="progress-steps">
                    <div class="d-flex justify-content-between position-relative">
                        <div class="step-line"></div>
                        
                        @php
                            $steps = [
                                1 => ['icon' => 'person', 'label' => 'Select Client'],
                                2 => ['icon' => 'box-seam', 'label' => 'Choose Service'],
                                3 => ['icon' => 'tag', 'label' => 'Coupon'],
                                4 => ['icon' => 'clipboard-check', 'label' => 'Summary'],
                                5 => ['icon' => 'check-circle', 'label' => 'Done']
                            ];
                        @endphp
                        
                        @foreach($steps as $stepNum => $stepInfo)
                            <div class="step-item {{ $step <= $stepNum ? 'active' : '' }} {{ $step == $stepNum ? 'current' : '' }}">
                                <div class="step-icon">
                                    <i class="bi bi-{{ $stepInfo['icon'] }}"></i>
                                </div>
                                <div class="step-label">{{ $stepInfo['label'] }}</div>
                                <div class="step-number">Step {{ $stepNum }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row">
        <div class="col-12">
            <div class="section-card">
                <form action="{{ route('admin.orders.buy.process') }}" method="POST" id="buyServiceForm">
                    @csrf
                    <input type="hidden" name="step" value="{{ $step }}">
                    
                    <!-- Step Content -->
                    <div class="step-content mt-4">
                        @if($step == 1)
                            @include('admin.orders.partials.step-1-client')
                        @elseif($step == 2)
                            @include('admin.orders.partials.step-2-service')
                        @elseif($step == 3)
                            @include('admin.orders.partials.step-3-coupon')
                        @elseif($step == 4)
                            @include('admin.orders.partials.step-4-summary')
                        @elseif($step == 5)
                            @include('admin.orders.partials.step-5-done')
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Progress Steps Styling */
    .progress-steps {
        padding: 20px 0;
    }
    
    .step-line {
        position: absolute;
        top: 35px;
        left: 10%;
        right: 10%;
        height: 2px;
        background-color: #e2e8f0;
        z-index: 1;
    }
    
    .step-item {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }
    
    .step-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 10px;
        border-radius: 50%;
        background-color: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #64748b;
        border: 4px solid white;
        transition: all 0.3s;
    }
    
    .step-item.active .step-icon {
        background-color: var(--sharpedge-blue);
        color: white;
    }
    
    .step-item.current .step-icon {
        box-shadow: 0 0 0 8px rgba(59, 130, 246, 0.1);
        animation: pulse 2s infinite;
    }
    
    .step-label {
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 5px;
    }
    
    .step-item.active .step-label {
        color: var(--sharpedge-blue);
    }
    
    .step-number {
        font-size: 12px;
        color: #94a3b8;
    }
    
    .step-item.active .step-number {
        color: var(--sharpedge-blue);
        font-weight: 600;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.1); }
        70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    
    /* Form Controls */
    .form-select-lg, .form-control-lg {
        padding: 12px 16px;
        font-size: 16px;
    }
    
    .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }
    
    /* Amount Box */
    .amount-box {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
    }
    
    .amount-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .amount-row:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 18px;
        color: var(--sharpedge-blue);
    }
    
    .discount-badge {
        background-color: #10b981;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    /* Client/Service Info Card */
    .info-card {
        background-color: #f8fafc;
        border-radius: 10px;
        padding: 20px;
        border-left: 4px solid var(--sharpedge-blue);
    }
    
    .info-label {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-value {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
    }
    
    /* Success Animation */
    @keyframes checkmark {
        0% { transform: scale(0); opacity: 0; }
        50% { opacity: 1; }
        100% { transform: scale(1); }
    }
    
    .checkmark-animation {
        animation: checkmark 0.5s ease-in-out;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-select first option in step 1 if only one client exists
    const clientSelect = document.getElementById('client_id');
    if (clientSelect && clientSelect.options.length === 2) { // 1 option + empty option
        clientSelect.selectedIndex = 1;
    }
    
    // Auto-select first option in step 2 if only one service exists
    const serviceSelect = document.getElementById('service_id');
    if (serviceSelect && serviceSelect.options.length === 2) {
        serviceSelect.selectedIndex = 1;
    }
    
    // Coupon code validation
    const couponCodeInput = document.getElementById('coupon_code');
    const couponHelpText = document.getElementById('couponHelp');
    const validateCouponBtn = document.getElementById('validateCouponBtn');
    
    if (couponCodeInput) {
        couponCodeInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                couponHelpText.textContent = 'Enter coupon code (optional)';
                couponHelpText.className = 'form-text text-muted';
                if (validateCouponBtn) validateCouponBtn.style.display = 'none';
            } else {
                couponHelpText.textContent = 'Press Enter or click Validate to check coupon';
                couponHelpText.className = 'form-text text-primary';
                if (validateCouponBtn) validateCouponBtn.style.display = 'inline-block';
            }
        });
    }
    
    // Handle Enter key in coupon field
    if (couponCodeInput) {
        couponCodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('continueBtn').click();
            }
        });
    }
    
    // Form validation
    const form = document.getElementById('buyServiceForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Only validate required fields for steps 1 and 2
            if ({{ $step }} === 1) {
                const clientSelect = document.getElementById('client_id');
                if (!clientSelect || clientSelect.value === '') {
                    e.preventDefault();
                    alert('Please select a client');
                    clientSelect.focus();
                }
            } else if ({{ $step }} === 2) {
                const serviceSelect = document.getElementById('service_id');
                if (!serviceSelect || serviceSelect.value === '') {
                    e.preventDefault();
                    alert('Please select a service');
                    serviceSelect.focus();
                }
            }
        });
    }
});
</script>
@endsection