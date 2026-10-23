@extends('layouts.marketing')

@section('title', 'Upload Documents - SharpEdge Marketing')

@section('styles')
<style>
    .selection-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        padding: 2.5rem;
        margin: 2rem auto;
        max-width: 800px;
    }
    
    .card-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    
    .card-header .icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }
    
    .card-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }
    
    .card-header p {
        color: var(--gray-500);
        font-size: 1rem;
        max-width: 500px;
        margin: 0 auto;
    }
    
    /* Form Styles */
    .form-section {
        margin-bottom: 2rem;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border: 2px solid var(--gray-300);
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: all 0.2s ease;
        height: 48px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-control-lg {
        padding: 0.875rem 1rem;
        font-size: 1.125rem;
        height: 56px;
    }
    
    /* Client Card */
    .client-select-card {
        border: 2px solid var(--gray-200);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .client-select-card:hover {
        border-color: var(--primary-color);
        background: rgba(59, 130, 246, 0.02);
    }
    
    .client-select-card.active {
        border-color: var(--primary-color);
        background: rgba(59, 130, 246, 0.05);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }
    
    .client-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .client-avatar {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .client-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .client-details h4 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .client-details p {
        color: var(--gray-500);
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    /* Service Card */
    .service-select-card {
        border: 2px solid var(--gray-200);
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .service-select-card:hover {
        border-color: var(--secondary-color);
        background: rgba(16, 185, 129, 0.02);
    }
    
    .service-select-card.active {
        border-color: var(--secondary-color);
        background: rgba(16, 185, 129, 0.05);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
    }
    
    .service-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--secondary-color), #0da271);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .service-details h5 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .service-details p {
        color: var(--gray-500);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .service-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: var(--gray-400);
    }
    
    /* Step Indicator */
    .step-indicator {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        margin-bottom: 3rem;
        position: relative;
    }
    
    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--gray-200);
        color: var(--gray-500);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .step.active .step-number {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }
    
    .step.completed .step-number {
        background: var(--secondary-color);
        color: white;
    }
    
    .step.completed .step-number i {
        font-size: 0.875rem;
    }
    
    .step-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-500);
        transition: all 0.3s ease;
    }
    
    .step.active .step-label {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .step-line {
        position: absolute;
        top: 20px;
        left: 70px;
        right: 70px;
        height: 2px;
        background: var(--gray-300);
        z-index: 0;
    }
    
    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid var(--gray-200);
    }
    
    .btn-continue {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.875rem 2.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .btn-continue:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }
    
    .btn-continue:disabled {
        background: var(--gray-300);
        color: var(--gray-500);
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }
    
    .btn-back {
        background: transparent;
        color: var(--gray-600);
        border: 1px solid var(--gray-300);
        padding: 0.875rem 2rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-back:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }
    
    /* Loading State */
    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
        margin-right: 0.5rem;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Validation Message */
    .validation-message {
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .selection-card {
            padding: 1.5rem;
            margin: 1rem;
        }
        
        .step-indicator {
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .step-line {
            left: 50px;
            right: 50px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-continue, .btn-back {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Selection Card -->
    <div class="selection-card">
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-line"></div>
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-label">Select Client</div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-label">Select Service</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Upload Documents</div>
            </div>
        </div>
        
        <!-- Form Header -->
        <div class="card-header">
            <div class="icon">
                <i class="fas fa-file-upload"></i>
            </div>
            <h2>Upload Client Documents</h2>
            <p>Select a client and service to view required documents for upload</p>
        </div>
        
        <!-- Selection Form -->
        <form id="documentUploadForm" method="GET" action="{{ route('marketing.documents.upload.show') }}">
            <!-- Step 1: Client Selection -->
            <div class="form-section" id="step1">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Select Client</h4>
                    <span class="text-muted small">{{ $clients->count() }} clients available</span>
                </div>
                
                @if($clients->count() > 0)
                <div class="client-selection">
                    <div class="row">
                        @foreach($clients as $client)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="client-select-card" data-client-id="{{ $client->id }}">
                                <div class="client-info">
                                    @if($client->profile_photo)
                                        @php
                                            $imagePath = str_replace('public/', '', $client->profile_photo);
                                        @endphp
                                        <div class="client-avatar">
                                            <img src="{{ asset('storage/' . $imagePath) }}" 
                                                 alt="{{ $client->name }}"
                                                 onerror="this.onerror=null; this.style.display='none'; this.parentNode.innerHTML='{{ strtoupper(substr($client->name, 0, 1)) }}';">
                                        </div>
                                    @else
                                        <div class="client-avatar">
                                            {{ strtoupper(substr($client->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="client-details">
                                        <h4>{{ $client->name }}</h4>
                                        <p>{{ $client->email }}</p>
                                        <p class="small text-muted mb-0">
                                            {{ $client->company_name ? $client->company_name . ' • ' : '' }}
                                            {{ $client->phone }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No active clients found. Please create a client first.
                </div>
                @endif
                
                <!-- Hidden Input for Client -->
                <input type="hidden" name="client_id" id="selectedClientId" required>
                <div id="clientValidation" class="validation-message" style="display: none;">
                    <i class="fas fa-exclamation-circle"></i>
                    Please select a client to continue
                </div>
            </div>
            
            <!-- Step 2: Service Selection -->
            <div class="form-section" id="step2" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Select Service</h4>
                    <span class="text-muted small" id="serviceCount">{{ $services->count() }} services available</span>
                </div>
                
                <div id="servicesLoading" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading services...</p>
                </div>
                
                <div id="servicesList">
                    @if($services->count() > 0)
                    <div class="row">
                        @foreach($services as $service)
                        <div class="col-md-6 mb-3">
                            <div class="service-select-card" data-service-id="{{ $service->id }}">
                                <div class="service-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="service-details">
                                    <h5>{{ $service->service_name }}</h5>
                                    <p>{{ Str::limit($service->description, 100) }}</p>
                                    <div class="service-meta">
                                        <span><i class="fas fa-clock"></i> {{ $service->processing_time ?? 'N/A' }}</span>
                                        <span><i class="fas fa-tag"></i> {{ $service->category->name ?? 'Uncategorized' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No active services available.
                    </div>
                    @endif
                </div>
                
                <!-- Hidden Input for Service -->
                <input type="hidden" name="service_id" id="selectedServiceId" required>
                <div id="serviceValidation" class="validation-message" style="display: none;">
                    <i class="fas fa-exclamation-circle"></i>
                    Please select a service to continue
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="btn btn-back" id="btnBack" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <button type="submit" class="btn btn-continue" id="btnContinue" disabled>
                    Continue <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let currentStep = 1;
    let selectedClientId = null;
    let selectedServiceId = null;
    
    // Client Selection
    $(document).on('click', '.client-select-card', function() {
        $('.client-select-card').removeClass('active');
        $(this).addClass('active');
        
        selectedClientId = $(this).data('client-id');
        $('#selectedClientId').val(selectedClientId);
        $('#clientValidation').hide();
        
        // Show next step
        showStep(2);
        loadServices();
    });
    
    // Service Selection
    $(document).on('click', '.service-select-card', function() {
        $('.service-select-card').removeClass('active');
        $(this).addClass('active');
        
        selectedServiceId = $(this).data('service-id');
        $('#selectedServiceId').val(selectedServiceId);
        $('#serviceValidation').hide();
        
        // Enable continue button
        $('#btnContinue').prop('disabled', false);
        $('#btnContinue').html('Continue to Upload <i class="fas fa-arrow-right"></i>');
    });
    
    // Back Button
    $('#btnBack').on('click', function() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    });
    
    // Continue Button (Form Submit)
    $('#documentUploadForm').on('submit', function(e) {
        if (currentStep === 1 && !selectedClientId) {
            e.preventDefault();
            $('#clientValidation').show();
            return false;
        }
        
        if (currentStep === 2 && !selectedServiceId) {
            e.preventDefault();
            $('#serviceValidation').show();
            return false;
        }
        
        // Show loading on button
        $('#btnContinue').prop('disabled', true);
        $('#btnContinue').html('<span class="loading-spinner"></span> Processing...');
    });
    
    // Function to show specific step
    function showStep(step) {
        currentStep = step;
        
        // Hide all steps
        $('#step1').hide();
        $('#step2').hide();
        
        // Update step indicator
        $('.step').removeClass('active');
        $(`.step:nth-child(${step + 1})`).addClass('active');
        
        // Show current step
        $(`#step${step}`).show();
        
        // Update buttons
        if (step === 1) {
            $('#btnBack').hide();
            $('#btnContinue').prop('disabled', true);
            $('#btnContinue').html('Continue <i class="fas fa-arrow-right"></i>');
        } else {
            $('#btnBack').show();
            $('#btnContinue').prop('disabled', !selectedServiceId);
            $('#btnContinue').html('Continue to Upload <i class="fas fa-arrow-right"></i>');
        }
    }
    
    // Function to load services (could be filtered by client in future)
    function loadServices() {
        $('#servicesLoading').show();
        $('#servicesList').hide();
        
        // In future, you could filter services by client
        // For now, show all services
        setTimeout(() => {
            $('#servicesLoading').hide();
            $('#servicesList').show();
        }, 500);
    }
    
    // Handle enter key
    $(document).on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            if (currentStep === 1 && selectedClientId) {
                showStep(2);
            } else if (currentStep === 2 && selectedServiceId) {
                $('#documentUploadForm').submit();
            }
        }
    });
    
    // Initialize
    showStep(1);
});
</script>
@endsection