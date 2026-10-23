{{-- Step 5 - Payment Entry (MULTI SERVICE VERSION) --}}

@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-3 fs-5"></i>
            <div>
                <strong class="d-block mb-1">Please fix the following errors:</strong>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('marketing.orders.process-step') }}"
      method="POST"
      enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="step" value="5">

    <div class="step-header mb-4">
        <h3 class="step-title mb-2">Payment Entry</h3>
        <p class="text-muted mb-0">
            Submit payment for Order #{{ $order->order_id }}
        </p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Compact Order Summary (UPDATED for multiple services) --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-3 mb-lg-0">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-light rounded-circle p-2 me-3">
                                            <i class="fas fa-receipt text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Order ID</small>
                                            <strong class="d-block">{{ $order->order_id }}</strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-light rounded-circle p-2 me-3">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Client</small>
                                            <strong class="d-block">{{ $order->client->name }}</strong>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- ✅ MULTI SERVICE DISPLAY --}}
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-light rounded-circle p-2 me-3">
                                            <i class="fas fa-cog text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Services ({{ $order->items->count() }})</small>
                                            <strong class="d-block">
                                                @foreach($order->items as $item)
                                                    {{ $item->service->service_name }}@if(!$loop->last), @endif
                                                @endforeach
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-light rounded-circle p-2 me-3">
                                            <i class="fas fa-calendar text-primary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Order Date</small>
                                            <strong class="d-block">{{ $order->created_at->format('d M, Y') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 border-start-lg">
                            <div class="text-center text-lg-end">
                                <small class="text-muted d-block mb-2">Total Amount</small>
                                <h3 class="text-primary mb-3">₹{{ number_format($order->final_amount, 2) }}</h3>
                                <span class="badge bg-warning py-2 px-3 rounded-pill">
                                    <i class="fas fa-clock me-1"></i> PENDING PAYMENT
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Details Form (unchanged from your version) --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Details</h5>
                </div>
                
                <div class="card-body p-4">
                    {{-- Payment Method + Amount --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-2">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method"
                                    id="payment_method"
                                    class="form-select form-select-lg"
                                    required>
                                <option value="">-- Select Payment Method --</option>
                                <option value="UPI" {{ old('payment_method') == 'UPI' ? 'selected' : '' }}>UPI</option>
                                <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>

                                @if(isset($client) && $client->is_credit_allowed)
                                    <option value="PAY_LATER" {{ old('payment_method') == 'PAY_LATER' ? 'selected' : '' }}>
                                        Pay Later
                                    </option>
                                @endif
                            </select>

                            @if(isset($client) && !$client->is_credit_allowed)
                                <div class="alert alert-warning mt-2 py-2 small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Pay Later is not available for this client.
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-2">Amount Paid (₹) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">₹</span>
                                <input type="number"
                                       name="amount"
                                       id="amount"
                                       class="form-control"
                                       value="{{ old('amount', $order->final_amount) }}"
                                       min="1"
                                       max="{{ $order->final_amount }}"
                                       step="0.01"
                                       required>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Maximum: ₹{{ number_format($order->final_amount, 2) }}
                            </small>
                        </div>
                    </div>

                    {{-- UPI Section --}}
                    <div id="upi_section" class="mb-4" style="display:none;">
                        <div class="alert alert-info border-0 mb-3">
                            <div class="d-flex">
                                <i class="fas fa-qrcode me-3 fs-5 mt-1"></i>
                                <div>
                                    <strong>UPI Payment Instructions</strong>
                                    <p class="mb-0 small">Please enter the UPI transaction ID after completing the payment.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-2">UPI Transaction ID <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="transaction_id"
                                       id="transaction_id"
                                       class="form-control form-control-lg"
                                       value="{{ old('transaction_id') }}"
                                       placeholder="Enter UPI transaction ID">
                            </div>

                            <div class="col-md-6">
                                @if($upi && $upi->upi_id && isset($dynamic_qr_code))
                                    <div class="text-center">
                                        <div class="border rounded p-3 mb-3">
                                            {!! $dynamic_qr_code !!}
                                        </div>
                                        <p class="mb-1"><strong>UPI ID:</strong> {{ $upi->upi_id }}</p>
                                        <p class="mb-3">
                                            <strong>Amount:</strong>
                                            ₹{{ number_format($order->final_amount, 2) }}
                                        </p>
                                        <a href="{{ $upi_payment_url }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-external-link-alt me-1"></i> Open in UPI App
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        UPI QR code is not configured
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Payment Date + Proof --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-2">Payment Date <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date"
                                       name="payment_date"
                                       id="payment_date"
                                       class="form-control"
                                       value="{{ old('payment_date', date('Y-m-d')) }}"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-2">Payment Proof (Optional)</label>
                            <div class="input-group input-group-lg">
                                <input type="file"
                                       name="payment_proof"
                                       id="payment_proof"
                                       class="form-control"
                                       accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-file me-1"></i>
                                JPG / PNG / PDF (Max 2MB)
                            </small>
                            <div id="file-selected" class="mt-2 small" style="display:none;">
                                <i class="fas fa-check text-success me-1"></i>
                                <span id="file-name"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Confirmation --}}
                    <div class="mb-4 pt-3 border-top">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="terms"
                                   name="terms"
                                   required
                                   style="width: 1.2em; height: 1.2em;">
                            <label class="form-check-label ms-2" for="terms">
                                <span class="fw-medium">I confirm I have received the payment and details are correct</span>
                            </label>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('marketing.orders.show', $order->id) }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-arrow-left me-2"></i> Back to Invoice
                        </a>
                        <button type="submit"
                                class="btn btn-success px-4"
                                id="submitBtn"
                                disabled>
                            <i class="fas fa-check-circle me-2"></i> Submit Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const method = document.getElementById('payment_method');
    const upiSection = document.getElementById('upi_section');
    const txn = document.getElementById('transaction_id');
    const amount = document.getElementById('amount');
    const proof = document.getElementById('payment_proof');
    const paymentDate = document.getElementById('payment_date');
    const termsCheck = document.getElementById('terms');
    const submitBtn = document.getElementById('submitBtn');
    const fileSelected = document.getElementById('file-selected');
    const fileName = document.getElementById('file-name');
    
    const maxAmount = {{ $order->final_amount }};

    function handlePaymentUI() {
        const selected = method.value;

        if (selected === 'UPI') {
            upiSection.style.display = 'block';
            txn.required = true;
            amount.readOnly = false;
            amount.required = true;
            paymentDate.readOnly = false;
            paymentDate.required = true;
            proof.disabled = false;
        }
        else if (selected === 'PAY_LATER') {
            upiSection.style.display = 'none';
            txn.required = false;
            txn.value = '';
            amount.value = maxAmount;
            amount.readOnly = true;
            amount.required = true;
            paymentDate.readOnly = false;
            paymentDate.required = true;
            proof.value = '';
            proof.disabled = true;
            fileSelected.style.display = 'none';
        }
        else {
            upiSection.style.display = 'none';
            txn.required = false;
            txn.value = '';
            amount.readOnly = false;
            amount.required = true;
            paymentDate.readOnly = false;
            paymentDate.required = true;
            proof.disabled = false;
        }
    }

    amount.addEventListener('input', function () {
        if (parseFloat(this.value) > maxAmount) {
            this.value = maxAmount;
            alert('Amount cannot exceed ₹' + maxAmount.toFixed(2));
        }
    });

    proof.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileName.textContent = this.files[0].name;
            fileSelected.style.display = 'block';
        } else {
            fileSelected.style.display = 'none';
        }
    });

    termsCheck.addEventListener('change', function() {
        submitBtn.disabled = !this.checked;
    });

    handlePaymentUI();
    submitBtn.disabled = true;
    method.addEventListener('change', handlePaymentUI);
});
</script>

<style>
.step-title {
    font-weight: 600;
    color: #2c3e50;
}
.card {
    border-radius: 12px;
}
.form-select-lg, .form-control-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
}
.input-group-lg .form-control,
.input-group-lg .input-group-text {
    padding: 0.75rem 1rem;
    font-size: 1rem;
}
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}
.badge {
    font-weight: 500;
    letter-spacing: 0.3px;
}
.border-start-lg {
    border-left: 1px solid #dee2e6;
}
@media (max-width: 991.98px) {
    .border-start-lg {
        border-left: none;
        border-top: 1px solid #dee2e6;
        padding-top: 1.5rem;
        margin-top: 1rem;
    }
}
.shadow-sm {
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
}
.rounded-circle {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>