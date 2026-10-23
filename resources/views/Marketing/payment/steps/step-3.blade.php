<div class="step-content">
    <form action="{{ route('marketing.orders.process-step') }}" method="POST" id="step3Form">
        @csrf
        <input type="hidden" name="step" value="3">

        <div class="row">
            <!-- Left Column: Main Controls -->
            <div class="col-lg-8">
                <!-- Page Header -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-1"><i class="fas fa-tags text-primary me-2"></i>Apply Discount</h5>
                    <p class="text-muted small mb-0">Enter discount and select GST type for your order</p>
                </div>

                <!-- ✅ GST Type Selection – WITH BORDER -->
                <div class="card border border-2 border-light shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <label class="form-label fw-semibold mb-3 small text-uppercase text-muted">
                            <i class="fas fa-file-invoice me-1"></i> GST Type
                        </label>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="gst-option {{ (old('gst_type', session('gst_type', 'with_gst')) == 'with_gst') ? 'active' : '' }}"
                                     onclick="document.getElementById('gst_with').click()">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gst_type" id="gst_with" value="with_gst"
                                            {{ old('gst_type', session('gst_type', 'with_gst')) == 'with_gst' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="gst_with">
                                            Service Fees With GST
                                            <small class="d-block text-muted fw-normal">Bill will be <strong class="text-primary">TAX INVOICE</strong></small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="gst-option {{ (old('gst_type', session('gst_type', 'with_gst')) == 'without_gst') ? 'active' : '' }}"
                                     onclick="document.getElementById('gst_without').click()">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gst_type" id="gst_without" value="without_gst"
                                            {{ old('gst_type', session('gst_type', 'with_gst')) == 'without_gst' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="gst_without">
                                            Service Fees Without GST
                                            <small class="d-block text-muted fw-normal">Bill will be <strong class="text-secondary">INVOICE</strong></small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('gst_type')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- ✅ Discount Amount – WITH BORDER -->
                <div class="card border border-2 border-light shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <label for="discount_amount" class="form-label fw-semibold small text-uppercase text-muted">
                            <i class="fas fa-rupee-sign me-1"></i> Discount Amount
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 fw-bold">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control border-0 bg-light"
                                   id="discount_amount" name="discount_amount"
                                   value="{{ old('discount_amount', session('discount_amount', 0)) }}"
                                   placeholder="Enter discount amount">
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Maximum discount allowed: <strong>₹{{ number_format($amounts['total'] ?? 0, 2) }}</strong>
                        </small>
                        @error('discount_amount')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('marketing.orders.create', ['step' => 2]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-arrow-right me-1"></i> Next Step
                    </button>
                </div>
            </div>

            <!-- Right Column: Summary -->
            <div class="col-lg-4">
                <!-- ✅ Client Summary – WITH BORDER -->
                <div class="card border border-2 border-light shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h6 class="text-muted small text-uppercase fw-semibold mb-3">
                            <i class="fas fa-user me-1"></i> Client
                        </h6>
                        <p class="fw-bold mb-1">{{ $client->name ?? 'N/A' }}</p>
                        <small class="text-muted">{{ $client->email ?? '' }}</small>
                    </div>
                </div>

                <!-- ✅ Services Summary – WITH BORDER -->
                <div class="card border border-2 border-light shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h6 class="text-muted small text-uppercase fw-semibold mb-3">
                            <i class="fas fa-cogs me-1"></i> Selected Services
                        </h6>
                        @foreach($services as $service)
                            <p class="mb-1 small">{{ $service->name }}</p>
                        @endforeach
                    </div>
                </div>

                <!-- ✅ Amount Summary – WITH BORDER -->
                <div class="card border border-2 border-light shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h6 class="text-muted small text-uppercase fw-semibold mb-3">
                            <i class="fas fa-calculator me-1"></i> Amount Summary
                        </h6>

                        <div class="amount-summary">
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Subtotal</span>
                                <span class="fw-semibold" id="subtotalDisplay">
                                    ₹{{ number_format($amounts['subtotal'] ?? 0, 2) }}
                                </span>
                            </div>

                            <!-- GST Rows (hidden when without_gst) -->
                            <div id="gstRows">
                                <div class="d-flex justify-content-between py-1" id="cgstRow">
                                    <span class="text-muted small">CGST ({{ $amounts['cgst_rate'] ?? 9 }}%)</span>
                                    <span id="cgstDisplay">₹{{ number_format($amounts['cgst'] ?? 0, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between py-1" id="sgstRow">
                                    <span class="text-muted small">SGST ({{ $amounts['sgst_rate'] ?? 9 }}%)</span>
                                    <span id="sgstDisplay">₹{{ number_format($amounts['sgst'] ?? 0, 2) }}</span>
                                </div>
                            </div>

                            <div class="border-top my-2"></div>

                            <div class="d-flex justify-content-between py-1">
                                <span class="fw-semibold">Total (before discount)</span>
                                <span class="fw-bold" id="totalDisplay">
                                    ₹{{ number_format($amounts['total'] ?? 0, 2) }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between py-1 text-success" id="discountRow" style="{{ ($amounts['discount'] ?? 0) > 0 ? '' : 'display:none;' }}">
                                <span>Discount</span>
                                <span id="discountDisplay">- ₹{{ number_format($amounts['discount'] ?? 0, 2) }}</span>
                            </div>

                            <div class="border-top my-2"></div>

                            <div class="d-flex justify-content-between py-1">
                                <span class="fw-bold fs-6">Final Total</span>
                                <span class="fw-bold fs-6 text-primary" id="finalTotalDisplay">
                                    ₹{{ number_format($amounts['final_total'] ?? 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- ✅ CSS Styles -->
<style>
.gst-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8f9fa;
}

.gst-option:hover {
    border-color: #0d6efd;
    background: #f1f8ff;
}

.gst-option.active {
    border-color: #0d6efd;
    background: #f1f8ff;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
}

.gst-option .form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.gst-option .form-check-label {
    cursor: pointer;
}

.amount-summary .border-top {
    border-color: #e9ecef !important;
}

/* ✅ Card Borders */
.border-light {
    border-color: #dee2e6 !important;
}
</style>

<!-- ✅ JavaScript – Live Update -->
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const gstWith = document.getElementById('gst_with');
    const gstWithout = document.getElementById('gst_without');
    const discountInput = document.getElementById('discount_amount');

    // Base values from backend
    const subtotal = {{ $amounts['subtotal'] ?? 0 }};
    const cgstRate = {{ $amounts['cgst_rate'] ?? 9 }};
    const sgstRate = {{ $amounts['sgst_rate'] ?? 9 }};

    // Display elements
    const cgstDisplay = document.getElementById('cgstDisplay');
    const sgstDisplay = document.getElementById('sgstDisplay');
    const totalDisplay = document.getElementById('totalDisplay');
    const finalTotalDisplay = document.getElementById('finalTotalDisplay');
    const discountDisplay = document.getElementById('discountDisplay');
    const cgstRow = document.getElementById('cgstRow');
    const sgstRow = document.getElementById('sgstRow');
    const discountRow = document.getElementById('discountRow');

    function updateTotals() {
        const gstType = document.querySelector('input[name="gst_type"]:checked')?.value || 'with_gst';
        const discount = parseFloat(discountInput.value) || 0;

        // Calculate GST
        let cgst = 0, sgst = 0;
        if (gstType === 'with_gst') {
            cgst = (subtotal * cgstRate) / 100;
            sgst = (subtotal * sgstRate) / 100;
        }

        const totalBeforeDiscount = subtotal + cgst + sgst;
        const finalTotal = Math.max(0, totalBeforeDiscount - discount);

        // Update display
        cgstDisplay.textContent = '₹' + cgst.toFixed(2);
        sgstDisplay.textContent = '₹' + sgst.toFixed(2);
        totalDisplay.textContent = '₹' + totalBeforeDiscount.toFixed(2);

        if (finalTotalDisplay) {
            finalTotalDisplay.textContent = '₹' + finalTotal.toFixed(2);
        }

        // Show/hide GST rows
        if (cgstRow) cgstRow.style.display = (gstType === 'with_gst') ? '' : 'none';
        if (sgstRow) sgstRow.style.display = (gstType === 'with_gst') ? '' : 'none';

        // Show/hide discount row
        if (discountRow) {
            discountRow.style.display = (discount > 0) ? '' : 'none';
            if (discountDisplay) {
                discountDisplay.textContent = '- ₹' + discount.toFixed(2);
            }
        }

        // Update GST option active state
        document.querySelectorAll('.gst-option').forEach(el => el.classList.remove('active'));
        const activeOption = gstType === 'with_gst' ? gstWith : gstWithout;
        if (activeOption) {
            const parent = activeOption.closest('.gst-option');
            if (parent) parent.classList.add('active');
        }
    }

    // Event listeners
    if (gstWith) gstWith.addEventListener('change', updateTotals);
    if (gstWithout) gstWithout.addEventListener('change', updateTotals);
    if (discountInput) discountInput.addEventListener('input', updateTotals);

    // Initial update
    updateTotals();
});
</script>
@endsection