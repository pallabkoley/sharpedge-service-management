@extends('layouts.admin')

@section('title', 'Create Order - SharpEdge Admin')

@push('styles')
<style>
    /* Page Header - Override with admin theme colors */
    .page-header {
        padding: 1.5rem 0 1rem 0;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }
    
    .page-header h1.h2 {
        color: #1e293b;
        font-weight: 700;
    }
    
    .page-header p.text-muted {
        color: #64748b;
    }
    
    /* Debug Alert */
    .debug-alert {
        background-color: #fef3c7;
        border-color: #fbbf24;
        color: #92400e;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        padding: 1rem;
    }
    
    .debug-info {
        font-family: monospace;
        font-size: 0.85rem;
        background: rgba(0,0,0,0.05);
        padding: 0.5rem;
        border-radius: 4px;
        margin-top: 0.5rem;
    }
    
    /* Progress Steps Card - Match admin theme */
    .card-progress {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        background-color: white;
        margin-bottom: 1.5rem;
    }
    
    .steps-container {
        padding: 1rem 0;
    }
    
    .steps-wrapper {
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 0 2rem;
    }
    
    .step-container {
        position: relative;
        z-index: 1;
        flex: 1;
        text-align: center;
        max-width: 180px;
    }
    
    .step-indicator {
        position: relative;
        margin-bottom: 12px;
    }
    
    .step-icon-wrapper {
        width: 50px;
        height: 50px;
        margin: 0 auto;
        border-radius: 50%;
        background-color: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #94a3b8;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .step-container.active .step-icon-wrapper {
        background-color: #1d4ed8;
        color: white;
        border-color: #1d4ed8;
        box-shadow: 0 4px 12px rgba(29, 78, 216, 0.25);
    }
    
    .step-container.completed .step-icon-wrapper {
        background-color: #10b981;
        color: white;
        border-color: #10b981;
    }
    
    .step-check {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 22px;
        height: 22px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: #10b981;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .step-content {
        padding: 0 5px;
    }
    
    .step-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 4px;
        color: #475569;
    }
    
    .step-container.active .step-title {
        color: #1d4ed8;
    }
    
    .step-container.completed .step-title {
        color: #10b981;
    }
    
    .step-desc {
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.3;
    }
    
    .step-connector {
        position: absolute;
        top: 25px;
        left: calc(50% + 25px);
        right: calc(-50% + 25px);
        height: 2px;
        background-color: #e2e8f0;
        z-index: 0;
    }
    
    .step-connector.active {
        background-color: #10b981;
    }
    
    /* Step Content Card - Match admin section cards */
    .card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        background-color: white;
    }
    
    .card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
        border-radius: 10px 10px 0 0 !important;
    }
    
    .card-title {
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }
    
    .card-title i {
        color: #1d4ed8;
    }
    
    .card-body.p-4 {
        padding: 1.5rem !important;
    }
    
    /* Button styling to match admin theme */
    .btn-primary {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
        font-weight: 500;
    }
    
    .btn-primary:hover {
        background-color: #1e40af;
        border-color: #1e40af;
    }
    
    .btn-outline-secondary {
        border-color: #e2e8f0;
        color: #64748b;
    }
    
    .btn-outline-secondary:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: #475569;
    }
    
    /* Form styling */
    .form-select-lg {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        padding: 12px 16px;
        height: auto;
        transition: border-color 0.3s ease;
    }
    
    .form-select-lg:focus {
        border-color: #1d4ed8;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        outline: none;
    }
    
    /* Alert styling */
    .alert-info {
        background-color: #dbeafe;
        border-color: #bfdbfe;
        color: #1e40af;
    }
    
    .alert-light {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #475569;
    }
    
    /* Table styling */
    .table {
        color: #475569;
        margin-bottom: 0;
    }
    
    .table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .table tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }
    
    .table-borderless tbody tr:last-child {
        border-bottom: none;
    }
    
    /* Badge styling */
    .badge.bg-primary {
        background-color: #1d4ed8 !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .steps-wrapper {
            padding: 0 1rem;
        }
        
        .step-icon-wrapper {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .step-title {
            font-size: 0.85rem;
        }
        
        .step-desc {
            display: none;
        }
        
        .step-connector {
            top: 20px;
            left: calc(50% + 20px);
            right: calc(-50% + 20px);
        }
        
        .card-body.p-4 {
            padding: 1rem !important;
        }
    }
    
    /* Extra small devices */
    @media (max-width: 576px) {
        .steps-wrapper {
            overflow-x: auto;
            padding-bottom: 10px;
        }
        
        .step-container {
            min-width: 100px;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .card-title {
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    

    <!-- Show Session Errors -->
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

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">Create New Order</h1>
                <p class="text-muted mb-0">Complete the {{ $step == 6 ? '6' : '5' }}-step process to create a new service order</p>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <span class="badge bg-primary fs-6">Step {{ $step }} of {{ $step <= 5 ? '5' : '6' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($step <= 5)
    <!-- Progress Steps -->
    <div class="card card-progress mb-4">
        <div class="card-body">
            <div class="steps-container">
                @php
                    $steps = [
                        1 => ['title' => 'Select Client', 'icon' => 'fas fa-user', 'description' => 'Choose client'],
                        2 => ['title' => 'Select Service', 'icon' => 'fas fa-cogs', 'description' => 'Pick service'],
                        3 => ['title' => 'Apply Coupon', 'icon' => 'fas fa-tag', 'description' => 'Add discount'],
                        4 => ['title' => 'Generate Invoice', 'icon' => 'fas fa-file-invoice', 'description' => 'Create invoice'],
                        5 => ['title' => 'Payment Entry', 'icon' => 'fas fa-credit-card', 'description' => 'Process payment'],
                    ];
                @endphp

                <div class="steps-wrapper">
                    @foreach($steps as $stepNumber => $stepInfo)
                        <div class="step-container {{ $stepNumber <= $step ? 'active' : '' }} {{ $stepNumber < $step ? 'completed' : '' }}">
                            <div class="step-indicator">
                                <div class="step-icon-wrapper">
                                    @if($stepNumber < $step)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="{{ $stepInfo['icon'] }}"></i>
                                    @endif
                                </div>
                                @if($stepNumber < $step)
                                <div class="step-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                @endif
                            </div>
                            <div class="step-content">
                                <div class="step-title">{{ $stepInfo['title'] }}</div>
                                <div class="step-desc">{{ $stepInfo['description'] }}</div>
                            </div>
                            @if($stepNumber < count($steps))
                                <div class="step-connector {{ $stepNumber < $step ? 'active' : '' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Step Content -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                @if($step == 1)
                    <i class="fas fa-user me-2"></i>Select Client
                @elseif($step == 2)
                    <i class="fas fa-cogs me-2"></i>Select Service
                @elseif($step == 3)
                    <i class="fas fa-tag me-2"></i>Apply Coupon
                @elseif($step == 4)
                    <i class="fas fa-file-invoice me-2"></i>Generate Invoice
                @elseif($step == 5)
                    <i class="fas fa-credit-card me-2"></i>Payment Entry
                @endif
            </h3>
        </div>
        <div class="card-body p-4">
            @include('admin.orders.payment.steps.step-' . $step)
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== ORDER CREATION DEBUG ===');
        console.log('Current Step:', {{ $step }});
        console.log('Session Client ID:', '{{ session("selected_client_id") }}');
        console.log('Session Service ID:', '{{ session("selected_service_id") }}');
        console.log('Session Coupon ID:', '{{ session("selected_coupon_id") }}');
        console.log('Route URL for processStep:', '{{ route("admin.orders.processStep") }}');
        console.log('CSRF Token:', '{{ csrf_token() }}');
        
        // Add smooth animations to step indicators
        const stepContainers = document.querySelectorAll('.step-container');
        stepContainers.forEach(container => {
            container.addEventListener('mouseenter', function() {
                if(this.classList.contains('active') || this.classList.contains('completed')) {
                    const icon = this.querySelector('.step-icon-wrapper');
                    icon.style.transform = 'translateY(-2px) scale(1.05)';
                }
            });
            
            container.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.step-icon-wrapper');
                icon.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Debug form submissions
        const forms = document.querySelectorAll('form');
        forms.forEach((form, index) => {
            console.log(`Form ${index} action:`, form.action);
            console.log(`Form ${index} method:`, form.method);
            
            form.addEventListener('submit', function(e) {
                console.log(`Form ${index} submitting...`);
                console.log('Form data:', new FormData(this));
                
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
                    submitBtn.disabled = true;
                    
                    // Re-enable button after 5 seconds in case of error
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                            console.log('Form submission timeout - button re-enabled');
                        }
                    }, 5000);
                }
            });
        });
        
        // Log all click events
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'INPUT' || e.target.tagName === 'A') {
                console.log('Clicked:', e.target.tagName, e.target.className, e.target.innerText);
            }
        });
    });
</script>
@endpush