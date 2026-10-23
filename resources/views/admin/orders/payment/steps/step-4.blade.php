<form action="{{ route('admin.orders.processStep') }}" method="POST">
    @csrf
    <input type="hidden" name="step" value="4">
    
    <!-- Step Header -->
    <div class="step-header mb-5">
        <h2 class="fw-bold text-gray-800 mb-2">Step 4: Generate Invoice</h2>
        <p class="text-gray-600">Review and confirm the order details.</p>
    </div>

    <!-- Order Summary -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">Order Summary</h5>
            
            <!-- Client and Service Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Client Information</h6>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-user-circle me-3 text-primary fs-4"></i>
                        <div>
                            <h5 class="mb-1">{{ $client->name }}</h5>
                            <p class="mb-0 text-muted">{{ $client->email }} • {{ $client->phone }}</p>
                        </div>
                    </div>
                </div>
              <div>
    @foreach($services as $service)
        <h5 class="mb-1">
            {{ $service->service_name }}
        </h5>
        <p class="mb-2 text-muted">
            Code: {{ $service->service_code }}
        </p>
    @endforeach
</div>
            </div>

            <!-- Price Details -->
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount (₹)</th>
                        </tr>
                    </thead>
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
                            <td colspan="2" class="border-bottom"></td>
                        </tr>
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end">₹{{ number_format($amounts['subtotal'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>CGST ({{ $amounts['cgst_rate'] }}%)</td>
                            <td class="text-end">₹{{ number_format($amounts['cgst'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>SGST ({{ $amounts['sgst_rate'] }}%)</td>
                            <td class="text-end">₹{{ number_format($amounts['sgst'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Total GST</td>
                            <td class="text-end">₹{{ number_format($amounts['tax'], 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Total Before Discount</strong></td>
                            <td class="text-end"><strong>₹{{ number_format($amounts['total'], 2) }}</strong></td>
                        </tr>
                        
                        @if($coupon)
                        <tr class="table-success">
                            <td>
                                <strong>Coupon Discount</strong>
                                <br>
                                <small class="text-muted">{{ $coupon->code }} 
                                    @if($coupon->type === 'percentage')
                                        ({{ $coupon->value }}% OFF)
                                    @else
                                        (₹{{ number_format($coupon->value, 2) }} OFF)
                                    @endif
                                </small>
                            </td>
                            <td class="text-end">
                                <strong>- ₹{{ number_format($amounts['discount'] ?? 0, 2) }}</strong>
                            </td>
                        </tr>
                        @endif
                        
                        <tr class="table-primary">
                            <td><strong>Final Total Amount</strong></td>
                            <td class="text-end">
                                <h4 class="text-primary mb-0">₹{{ number_format($amounts['final_total'], 2) }}</h4>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-4 pt-4 border-top">
        <a href="{{ route('admin.orders.create', ['step' => 3]) }}" class="btn btn-outline-secondary px-5 py-2">
            <i class="fas fa-arrow-left me-2"></i> Back to Coupon
        </a>
        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
            Generate Invoice & Continue
            <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
</form>

<style>
    .table-primary {
        background-color: rgba(13, 110, 253, 0.05) !important;
    }
    .table-success {
        background-color: rgba(25, 135, 84, 0.05) !important;
    }
</style>