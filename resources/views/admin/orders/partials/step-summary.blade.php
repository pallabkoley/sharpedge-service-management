<div class="text-center mb-4">
    <h4>Order Summary</h4>
    <p class="text-muted">
        Confirm details for: <strong>{{ $client->name }}</strong>
    </p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Service Details -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Service Details</h5>
            </div>
            <div class="card-body">
                <h5>{{ $service->service_name }}</h5>
                <p class="text-muted">{{ $service->description }}</p>
            </div>
        </div>

        <!-- Pricing Breakdown -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Pricing Breakdown</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td>Our Fee</td>
                        <td class="text-right">₹{{ number_format($amounts['our_fee'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Government Fee</td>
                        <td class="text-right">₹{{ number_format($amounts['govt_fee'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Platform Fee</td>
                        <td class="text-right">₹{{ number_format($amounts['platform_fee'], 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Subtotal</strong></td>
                        <td class="text-right"><strong>₹{{ number_format($amounts['subtotal'], 2) }}</strong></td>
                    </tr>
                    
                    @if($coupon)
                    <tr class="table-success">
                        <td>
                            Discount ({{ $coupon->code }})
                            @if($coupon->type == 'percentage')
                                ({{ $coupon->value }}%)
                            @endif
                        </td>
                        <td class="text-right">-₹{{ number_format($amounts['discount'], 2) }}</td>
                    </tr>
                    @endif
                    
                    <tr>
                        <td>GST ({{ $amounts['gst_rate'] }}%)</td>
                        <td class="text-right">₹{{ number_format($amounts['tax'], 2) }}</td>
                    </tr>
                    <tr class="table-primary">
                        <td><h5 class="mb-0">Total Amount</h5></td>
                        <td class="text-right">
                            <h5 class="mb-0">₹{{ number_format($amounts['final_total'], 2) }}</h5>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Coupon Section -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Apply Coupon (Optional)</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.processStep') }}" class="form-inline">
                    @csrf
                    <input type="hidden" name="step" value="3">
                    
                    <div class="form-group mr-2 mb-2 flex-grow-1">
                        <input type="text" name="coupon_code" class="form-control w-100" 
                               placeholder="Enter coupon code" 
                               value="{{ $coupon ? $coupon->code : '' }}">
                    </div>
                    <button type="submit" class="btn btn-outline-primary mb-2">
                        {{ $coupon ? 'Update Coupon' : 'Apply Coupon' }}
                    </button>
                </form>
                
                @if($coupon)
                <div class="alert alert-success mt-3 mb-0">
                    <strong>Coupon Applied:</strong> {{ $coupon->code }} 
                    ({{ $coupon->type == 'percentage' ? $coupon->value . '%' : '₹' . $coupon->value }})
                    <br>
                    <small>Discount: ₹{{ number_format($amounts['discount'], 2) }}</small>
                </div>
                @endif
            </div>
        </div>

        <!-- Admin Notes (Optional) -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Admin Notes (Optional)</h5>
            </div>
            <div class="card-body">
                <textarea class="form-control" rows="3" placeholder="Add any internal notes..."></textarea>
            </div>
        </div>

        <!-- Confirmation -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Admin Order Note:</strong> This order will be auto-approved and tasks will be 
            automatically generated from the work process.
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-6 text-left">
                <a href="{{ route('admin.orders.create', ['step' => 2]) }}" 
                   class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
            <div class="col-6 text-right">
                <form method="POST" action="{{ route('admin.orders.processStep') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="step" value="3">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-check-circle mr-2"></i> Create Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>