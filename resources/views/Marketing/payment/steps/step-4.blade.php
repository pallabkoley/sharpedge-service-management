<form action="{{ route('marketing.orders.process-step') }}" method="POST">
    @csrf
    <input type="hidden" name="step" value="4">

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <h5 class="fw-bold mb-1">
                    <i class="fas fa-file-invoice text-primary me-2"></i>
                    Confirm Order
                </h5>
                <p class="text-muted small mb-0">
                    Review your selected services and final amount
                </p>
            </div>

            <!-- ✅ Main Card with Border -->
            <div class="card border border-2 border-light shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <!-- Invoice Type Badge -->
                    <div class="mb-3">
                        @if(($gst_type ?? 'with_gst') === 'with_gst')
                            <span class="badge bg-primary fs-6 px-3 py-2">
                                <i class="fas fa-receipt me-1"></i> TAX INVOICE
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6 px-3 py-2">
                                <i class="fas fa-receipt me-1"></i> INVOICE
                            </span>
                        @endif
                    </div>

                    <!-- Client & Order Info -->
                    <div class="row mb-4 pb-3 border-bottom">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small mb-2">Client</h6>
                            <p class="mb-1 fw-medium">{{ $client->name }}</p>
                            <p class="mb-1 small text-muted">{{ $client->email }}</p>
                            <p class="mb-0 small text-muted">{{ $client->phone }}</p>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <h6 class="text-muted text-uppercase small mb-2">Services ({{ count($services) }})</h6>
                            @foreach($services as $srv)
                                <p class="mb-1">{{ $srv->service_name }} <span class="text-muted">({{ $srv->service_code }})</span></p>
                            @endforeach
                        </div>
                    </div>

                    <!-- Payment Breakdown -->
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small mb-3">Payment Breakdown</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    {{-- Services --}}
                                    @foreach($services as $srv)
                                        <tr>
                                            <td class="ps-0">{{ $srv->service_name }} (Our Fee)</td>
                                            <td class="text-end pe-0">₹{{ number_format($srv->our_fee, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 ps-3 text-muted small">+ Govt. Fee</td>
                                            <td class="text-end pe-0">₹{{ number_format($srv->govt_fee, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    {{-- Subtotal --}}
                                    <tr class="border-top">
                                        <td class="ps-0 pt-2"><strong>Subtotal</strong></td>
                                        <td class="text-end pe-0 pt-2"><strong>₹{{ number_format($amounts['subtotal'], 2) }}</strong></td>
                                    </tr>

                                    {{-- ✅ CGST – শুধুমাত্র with_gst হলে --}}
                                    @if(($gst_type ?? 'with_gst') === 'with_gst')
                                    <tr>
                                        <td class="ps-0">CGST ({{ $amounts['cgst_rate'] }}%)</td>
                                        <td class="text-end pe-0">₹{{ number_format($amounts['cgst'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0">SGST ({{ $amounts['sgst_rate'] }}%)</td>
                                        <td class="text-end pe-0">₹{{ number_format($amounts['sgst'], 2) }}</td>
                                    </tr>
                                    @endif

                                    {{-- Direct Discount --}}
                                    @if(($amounts['discount'] ?? 0) > 0)
                                    <tr class="border-top">
                                        <td class="ps-0 pt-2 text-success"><strong>Direct Discount</strong></td>
                                        <td class="text-end pe-0 pt-2 text-success"><strong>-₹{{ number_format($amounts['discount'], 2) }}</strong></td>
                                    </tr>
                                    @endif

                                    {{-- Round Off --}}
                                    <tr>
                                        <td class="ps-0">Round Off</td>
                                        <td class="text-end pe-0">₹{{ number_format($amounts['round_off'], 2) }}</td>
                                    </tr>

                                    {{-- Final Amount --}}
                                    <tr class="border-top border-2">
                                        <td class="ps-0 pt-3"><h5 class="mb-0">Final Amount</h5></td>
                                        <td class="text-end pe-0 pt-3"><h4 class="text-primary">₹{{ number_format($amounts['final_total'], 2) }}</h4></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="mb-4 pt-3 border-top">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                            <label class="form-check-label" for="termsCheck">
                                I confirm that all details are correct and I agree to proceed.
                            </label>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('marketing.orders.create', ['step' => 3]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Discount
                        </a>
                        <button type="submit" class="btn btn-success px-4" id="confirmBtn" disabled>
                            <i class="fas fa-check-circle"></i> Confirm & Create Order
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Summary -->
        <div class="col-lg-4">
            <!-- Client Summary -->
            <div class="card border border-2 border-light shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h6 class="text-muted text-uppercase small mb-3">
                        <i class="fas fa-user me-1"></i> Order Summary
                    </h6>

                    <div class="mb-3">
                        <small class="text-muted">Client</small>
                        <strong class="d-block">{{ $client->name }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Services</small>
                        <ul class="list-unstyled mb-0">
                            @foreach($services as $srv)
                                <li class="small">{{ $srv->service_name }}</li>
                            @endforeach
                        </ul>
                    </div>

                    @if(($amounts['discount'] ?? 0) > 0)
                    <div class="mb-3">
                        <small class="text-muted">Direct Discount</small>
                        <strong class="d-block text-success">-₹{{ number_format($amounts['discount'], 2) }}</strong>
                    </div>
                    @endif

                    <div class="pt-3 border-top mt-3">
                        <div class="bg-light rounded p-3 text-center">
                            <small class="text-muted d-block mb-1">Final Payable Amount</small>
                            <h3 class="text-primary mb-0">₹{{ number_format($amounts['final_total'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const termsCheck = document.getElementById('termsCheck');
    const confirmBtn = document.getElementById('confirmBtn');

    termsCheck.addEventListener('change', function () {
        confirmBtn.disabled = !this.checked;
    });
});
</script>