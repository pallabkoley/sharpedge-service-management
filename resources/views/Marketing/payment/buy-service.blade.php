@extends('layouts.marketing')

@section('title', 'Buy Service - SharpEdge Marketing')

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
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">Buy Service</h1>
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
                    <i class="fas fa-tag me-2"></i>Apply Discount
                @elseif($step == 4)
                    <i class="fas fa-file-invoice me-2"></i>Generate Invoice
                @elseif($step == 5)
                    <i class="fas fa-credit-card me-2"></i>Payment Entry
                @endif
            </h3>
        </div>
        <div class="card-body p-4">
            @include('Marketing.payment.steps.step-' . $step)
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Page Header */
    .page-header {
        padding-top: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    /* Progress Steps Card */
    .card-progress {
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border-radius: 10px;
    }
    
    .steps-container {
        padding: 0.5rem 0;
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
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #adb5bd;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .step-container.active .step-icon-wrapper {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    }
    
    .step-container.completed .step-icon-wrapper {
        background-color: #198754;
        color: white;
        border-color: #198754;
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
        color: #198754;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .step-content {
        padding: 0 5px;
    }
    
    .step-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 4px;
        color: #495057;
    }
    
    .step-container.active .step-title {
        color: #0d6efd;
    }
    
    .step-container.completed .step-title {
        color: #198754;
    }
    
    .step-desc {
        font-size: 0.8rem;
        color: #6c757d;
        line-height: 1.3;
    }
    
    .step-connector {
        position: absolute;
        top: 25px;
        left: calc(50% + 25px);
        right: calc(-50% + 25px);
        height: 2px;
        background-color: #e9ecef;
        z-index: 0;
    }
    
    .step-connector.active {
        background-color: #198754;
    }
    
    /* Step Content Card */
    .card {
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border-radius: 10px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1.25rem 1.5rem;
        border-radius: 10px 10px 0 0 !important;
    }
    
    .card-title {
        font-weight: 600;
        color: #495057;
    }
    
    /* Responsive */
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
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth animations
        const stepContainers = document.querySelectorAll('.step-container');
        stepContainers.forEach(container => {
            container.addEventListener('mouseenter', function() {
                if(this.classList.contains('active') || this.classList.contains('completed')) {
                    const icon = this.querySelector('.step-icon-wrapper');
                    icon.style.transform = 'translateY(-2px)';
                }
            });
            
            container.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.step-icon-wrapper');
                icon.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection