<form action="{{ route('admin.orders.processStep') }}" method="POST" id="couponForm">
    @csrf
    <input type="hidden" name="step" value="3">

    <!-- Step Header -->
    <div class="step-header mb-5">
        <h2 class="fw-bold text-gray-800 mb-2">Step 3: Apply Coupon (Optional)</h2>
        <p class="text-gray-600">Apply a discount coupon to the selected services.</p>
    </div>

    <!-- Client & Selected Services Info (MULTIPLE) -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="alert alert-light border">
                <h6 class="mb-2">Client</h6>
                <p class="mb-0 fw-semibold">{{ $client->name }}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-light border">
                <h6 class="mb-2">Selected Services</h6>
                @forelse($services as $service)
                    <p class="mb-1 fw-semibold">
                        {{ $service->service_name }} – ₹{{ number_format($service->our_fee, 2) }}
                    </p>
                @empty
                    <p class="text-danger mb-0">No services selected.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Price Breakdown (Combined) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Price Breakdown</h5>
            <div class="table-responsive">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td>Our Fee</td>
                            <td class="text-end">₹{{ number_format($amounts['our_fee'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Government Fee</td>
                            <td class="text-end">₹{{ number_format($amounts['govt_fee'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Platform Fee</td>
                            <td class="text-end">₹{{ number_format($amounts['platform_fee'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end">₹{{ number_format($amounts['subtotal'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>GST ({{ $amounts['gst_rate'] }}%)</td>
                            <td class="text-end">₹{{ number_format($amounts['tax'], 2) }}</td>
                        </tr>
                        <tr class="fw-bold border-top">
                            <td>Total Amount</td>
                            <td class="text-end text-primary fs-5">₹{{ number_format($amounts['total'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Coupon Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Apply Coupon</h5>

            <!-- Current Coupon (if applied) -->
            @if(session('selected_coupon_id'))
                @php
                    $appliedCoupon = App\Models\Coupon::find(session('selected_coupon_id'));
                @endphp
                <div class="alert alert-success mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Coupon Applied:</strong> {{ $appliedCoupon->code }}
                            <span class="badge bg-info ms-2">
                                @if($appliedCoupon->type === 'percentage')
                                    {{ $appliedCoupon->value }}% OFF
                                @else
                                    ₹{{ number_format($appliedCoupon->value, 2) }} OFF
                                @endif
                            </span>
                        </div>
                        <input type="hidden" name="remove_coupon" value="0" id="remove_coupon_hidden">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCoupon()">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                </div>
            @endif

            <!-- Coupon Input (safe value) -->
            <div class="mb-3">
                <label for="coupon_code" class="form-label">Coupon Code (Optional)</label>
                <input type="text"
                       class="form-control form-control-lg"
                       id="coupon_code"
                       name="coupon_code"
                       placeholder="Enter coupon code"
                       value="{{ $coupons->where('id', session('selected_coupon_id'))->first() ? $coupons->where('id', session('selected_coupon_id'))->first()->code : '' }}">
                <div class="form-text">Leave blank if you don't have a coupon.</div>
            </div>

            <!-- Available Coupons -->
            <div class="mb-4">
                <label class="form-label mb-2">Available Coupons:</label>
                <div class="row g-2">
                    @foreach($coupons as $coupon)
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $coupon->code }}</h6>
                                            <p class="mb-0 small text-muted">
                                                @if($coupon->type === 'percentage')
                                                    {{ $coupon->value }}% OFF
                                                @else
                                                    ₹{{ number_format($coupon->value, 2) }} OFF
                                                @endif
                                                • Valid until: {{ date('d M, Y', strtotime($coupon->end_date)) }}
                                            </p>
                                        </div>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary select-coupon"
                                                data-code="{{ $coupon->code }}">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-4 pt-4 border-top">
        <a href="{{ route('admin.orders.create', ['step' => 2]) }}" class="btn btn-outline-secondary px-5 py-2">
            <i class="fas fa-arrow-left me-2"></i> Back to Services
        </a>
        <div>
            <button type="submit" name="action" value="skip" class="btn btn-outline-secondary px-4 py-2 me-2">
                Skip Coupon
            </button>
            <button type="submit" name="action" value="apply" class="btn btn-primary px-5 py-2 fw-semibold" id="applyButton">
                Continue to Invoice
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fill coupon code when clicking "Apply" on a coupon card
        document.querySelectorAll('.select-coupon').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('coupon_code').value = this.dataset.code;
            });
        });
    });

    // Remove coupon function
    function removeCoupon() {
        document.getElementById('remove_coupon_hidden').value = '1';
        document.getElementById('couponForm').submit();
    }
</script>