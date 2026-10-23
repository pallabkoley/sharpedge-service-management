<form action="{{ route('admin.orders.processStep') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
    @csrf
    <input type="hidden" name="step" value="5">
    
    <!-- Step Header -->
    <div class="step-header mb-5">
        <h2 class="fw-bold text-gray-800 mb-2">Step 5: Payment Entry</h2>
        <p class="text-gray-600">Enter payment details for the order.</p>
    </div>

    <!-- Order Info -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Order Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>Order ID:</strong>
                        <div class="mt-1">{{ $order->order_id }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>Client:</strong>
                        <div class="mt-1">{{ $client->name }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>Service:</strong>
                        <div class="mt-1">{{ $order->items->first()->service->service_name }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>Total Amount:</strong>
                        <div class="mt-1 h4 text-primary">₹{{ number_format($order->final_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">Payment Details</h5>
            
            <!-- Payment Method -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="payment_method" class="form-label fw-semibold">Payment Method *</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="">-- Select Payment Method --</option>
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                        <option value="PAY_LATER" {{ $client->is_credit_allowed ? '' : 'disabled' }}>
                            Pay Later {{ $client->is_credit_allowed ? '' : '(Not allowed for this client)' }}
                        </option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label fw-semibold">Amount (₹) *</label>
                    <input type="number" 
                           step="0.01" 
                           min="1" 
                           name="amount" 
                           id="amount" 
                           class="form-control" 
                           value="{{ $order->final_amount }}" 
                           required>
                </div>
            </div>

            <!-- Payment Date -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="payment_date" class="form-label fw-semibold">Payment Date *</label>
                    <input type="date" 
                           name="payment_date" 
                           id="payment_date" 
                           class="form-control" 
                           value="{{ date('Y-m-d') }}" 
                           required>
                </div>
                <div class="col-md-6">
                    <label for="transaction_id" class="form-label fw-semibold">Transaction ID</label>
                    <input type="text" 
                           name="transaction_id" 
                           id="transaction_id" 
                           class="form-control" 
                           placeholder="Enter transaction ID">
                </div>
            </div>

            <!-- Payment Proof -->
            <div class="mb-4">
                <label for="payment_proof" class="form-label fw-semibold">Payment Proof (Optional)</label>
                <input type="file" 
                       name="payment_proof" 
                       id="payment_proof" 
                       class="form-control" 
                       accept=".jpg,.jpeg,.png,.pdf">
                <div class="form-text">Upload proof of payment (Max 2MB, JPG, PNG, PDF)</div>
            </div>

            <!-- UPI QR Code (if UPI selected) -->
            <div id="upiSection" class="d-none">
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-qrcode me-3 fs-4"></i>
                        <div>
                            <h6 class="mb-1">UPI Payment Instructions</h6>
                            <p class="mb-0">Scan the QR code below or use the UPI ID to make payment.</p>
                        </div>
                    </div>
                </div>
                
                @if($upi_payment_url && $dynamic_qr_code)
                <div class="text-center mb-3">
                    <div class="mb-3">
                        {!! $dynamic_qr_code !!}
                    </div>
                    <div class="mb-3">
                        <strong>UPI ID:</strong>
                        <div class="text-primary fw-bold">{{ $upi->upi_id ?? 'N/A' }}</div>
                    </div>
                    <a href="{{ $upi_payment_url }}" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-2"></i> Pay via UPI App
                    </a>
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    UPI settings are not configured. Please contact administrator.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-4 pt-4 border-top">
        <a href="{{ route('admin.orders.create', ['step' => 4]) }}" class="btn btn-outline-secondary px-5 py-2">
            <i class="fas fa-arrow-left me-2"></i> Back to Invoice
        </a>
        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
            Submit Payment & Complete
            <i class="fas fa-check-circle ms-2"></i>
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethod = document.getElementById('payment_method');
        const upiSection = document.getElementById('upiSection');
        const transactionId = document.getElementById('transaction_id');
        
        // Show/hide UPI section
        paymentMethod.addEventListener('change', function() {
            if (this.value === 'UPI') {
                upiSection.classList.remove('d-none');
            } else {
                upiSection.classList.add('d-none');
            }
            
            // Make transaction ID required for UPI, Bank Transfer
            if (this.value === 'UPI' || this.value === 'Bank Transfer') {
                transactionId.required = true;
            } else {
                transactionId.required = false;
            }
        });
        
        // Form validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const amount = document.getElementById('amount').value;
            const totalAmount = {{ $order->final_amount }};
            
            if (parseFloat(amount) > totalAmount) {
                e.preventDefault();
                alert('Payment amount cannot exceed order total of ₹' + totalAmount.toFixed(2));
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            submitBtn.disabled = true;
            
            return true;
        });
    });
</script>