@extends('layouts.admin')

@section('title', 'View QR Code - SharpEdge Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">
                    <i class="bi bi-qr-code me-2"></i> QR Code - Full View
                </h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.upi.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="section-card">
                <div class="section-title">
                    <h5 class="mb-0">
                        <i class="bi bi-qr-code-scan me-2"></i> Your UPI QR Code
                    </h5>
                </div>
                
                <div class="text-center p-4">
                    @if($setting->qr_code_url)
                        <!-- QR Code Display -->
                        <div class="mb-4">
                            <img src="{{ $setting->qr_code_url }}" 
                                 alt="UPI QR Code" 
                                 class="img-fluid rounded shadow-lg"
                                 style="max-width: 500px; border: 10px solid white; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        </div>
                        
                        <!-- UPI ID Display -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <span class="badge bg-primary me-2">
                                    <i class="bi bi-wallet2 me-1"></i> UPI ID
                                </span>
                                <h5 class="mb-0">{{ $setting->upi_id }}</h5>
                            </div>
                            <p class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Scan this QR code to make payment directly to SharpEdge
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
                            <a href="{{ route('admin.upi.download') }}" 
                               class="btn btn-primary">
                                <i class="bi bi-download me-1"></i>
                                Download QR Code
                            </a>
                            
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="bi bi-printer me-1"></i>
                                Print QR Code
                            </button>
                            
                            <a href="upi://pay?pa={{ $setting->upi_id }}" 
                               class="btn btn-success">
                                <i class="bi bi-phone me-1"></i>
                                Open in UPI App
                            </a>
                        </div>
                        
                        <!-- Usage Instructions -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="bi bi-lightbulb me-1"></i> How to Use
                            </h6>
                            <ol class="mb-0">
                                <li>Display this QR code to clients for payment</li>
                                <li>Clients scan with any UPI app (PhonePe, Google Pay, Paytm)</li>
                                <li>Payment comes directly to your UPI ID</li>
                                <li>Verify payment in admin panel before service delivery</li>
                            </ol>
                        </div>
                        
                        <!-- Test Section -->
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="fw-bold mb-3">Test Payment Link</h6>
                            <div class="input-group mb-3">
                                <input type="text" 
                                       class="form-control" 
                                       id="paymentLink" 
                                       value="upi://pay?pa={{ $setting->upi_id }}" 
                                       readonly>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="copyPaymentLink()">
                                    <i class="bi bi-copy"></i> Copy Link
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Use this link to test payment functionality
                            </small>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            QR Code not found. Please generate one from UPI settings.
                        </div>
                        <a href="{{ route('admin.upi.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Go to UPI Settings
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
    
    // Copy payment link to clipboard
    function copyPaymentLink() {
        const linkInput = document.getElementById('paymentLink');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999);
        
        try {
            navigator.clipboard.writeText(linkInput.value);
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Payment link copied to clipboard',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            
        } catch (err) {
            // Fallback for older browsers
            document.execCommand('copy');
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Payment link copied to clipboard',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        }
    }
</script>

<style>
    @media print {
        .page-title-box,
        .section-title,
        .alert,
        button,
        a,
        .border-top {
            display: none !important;
        }
        
        .section-card {
            border: none !important;
            box-shadow: none !important;
        }
        
        img {
            max-width: 100% !important;
            border: none !important;
            box-shadow: none !important;
        }
        
        h5 {
            font-size: 24px !important;
            margin-bottom: 20px !important;
        }
    }
</style>
@endsection