<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Success Card -->
            <div class="card border-0 shadow-sm text-center mb-4">
                <div class="card-body p-5">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 p-4 mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 60px;"></i>
                        </div>
                    </div>
                    
                    <!-- Success Message -->
                    <h2 class="mb-3 fw-bold">Payment Submitted Successfully!</h2>
                    <p class="text-muted mb-4">Your payment has been recorded and is waiting for admin approval.</p>
                    
                    <!-- Status Badge -->
                    <div class="mb-4">
                        <span class="badge bg-warning py-2 px-4 fs-6 rounded-pill fw-medium">
                            <i class="fas fa-clock me-2"></i> PENDING APPROVAL
                        </span>
                    </div>
                    
                    <!-- Status Message -->
                    <div class="alert alert-light border mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-info me-3 fs-5"></i>
                            <div class="text-start">
                                <p class="mb-0 small">You will be notified once the payment is approved. You can track the status from your orders dashboard.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4 fw-semibold">
                        <i class="fas fa-receipt me-2 text-primary"></i>Order Summary
                    </h5>
                    
                    <div class="row g-3">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Order ID</small>
                                <strong class="d-block fs-6">{{ $order->order_id }}</strong>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Client</small>
                                <strong class="d-block fs-6">{{ $order->client->name ?? 'N/A' }}</strong>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Service</small>
                                <strong class="d-block fs-6">{{ $order->items->first()->service->service_name ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Order Date</small>
                                <strong class="d-block fs-6">{{ $order->created_at->format('d M, Y') }}</strong>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Order Status</small>
                                <div>
                                    <span class="badge bg-info py-2 px-3 rounded-pill">
                                        {{ str_replace('_', ' ', $order->order_status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Amount</small>
                                <h4 class="text-primary mb-0">₹{{ number_format($order->final_amount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-5">
                <div class="d-grid gap-3 d-md-flex justify-content-center">
                    <a href="{{ route('marketing.orders.show', $order) }}" class="btn btn-primary px-4 py-3">
                        <i class="fas fa-eye me-2"></i> View Order Details
                    </a>
                    
                    <form action="{{ route('marketing.orders.process-step') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="step" value="6">
                        <input type="hidden" name="action" value="new_order">
                        <button type="submit" class="btn btn-success px-4 py-3">
                            <i class="fas fa-plus-circle me-2"></i> Create New Order
                        </button>
                    </form>
                    
                    <a href="{{ route('marketing.orders.index') }}" class="btn btn-outline-secondary px-4 py-3">
                        <i class="fas fa-list me-2"></i> View All Orders
                    </a>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="text-center">
                <p class="text-muted small">
                    <i class="fas fa-envelope me-1"></i> 
                    An email notification will be sent to you once the payment is approved.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .container {
        max-width: 1000px;
    }
    
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .shadow-sm {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
    }
    
    .bg-success.bg-opacity-10 {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    
    .rounded-circle {
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .fw-semibold {
        font-weight: 600;
    }
    
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    .btn {
        font-weight: 500;
        min-width: 160px;
    }
    
    .d-grid.gap-3 > * {
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 2rem !important;
        }
        
        .btn {
            min-width: 100%;
        }
        
        .d-grid.gap-3 > * {
            width: 100%;
        }
    }
    
    @media (max-width: 576px) {
        .col-lg-8.col-xl-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>

<script>
    // Add a subtle animation to the success icon
    document.addEventListener('DOMContentLoaded', function() {
        const successIcon = document.querySelector('.fa-check-circle');
        if (successIcon) {
            successIcon.style.opacity = '0';
            successIcon.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                successIcon.style.transition = 'all 0.5s ease';
                successIcon.style.opacity = '1';
                successIcon.style.transform = 'scale(1)';
            }, 100);
        }
    });
</script>