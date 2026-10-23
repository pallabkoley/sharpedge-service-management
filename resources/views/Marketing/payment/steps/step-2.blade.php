<form action="{{ route('marketing.orders.process-step') }}" method="POST">
    @csrf
    <input type="hidden" name="step" value="2">
    
    <div class="step-header mb-4">
        <h3 class="step-title">Select Services</h3>
        <p class="text-muted mb-0">Choose one or more services for <strong>{{ $client->name }}</strong></p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="mb-4">
                <label class="form-label fw-semibold">Select Services (Multiple allowed)</label>
                <select name="service_ids[]" id="service_ids" class="form-select select2-multi" multiple required>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">
                            {{ $service->service_name }} ({{ $service->service_code }}) 
                            - ₹{{ number_format((float)$service->our_fee + (float)$service->govt_fee, 2) }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">You can select multiple services for this order.</small>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('marketing.orders.create', ['step' => 1]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Client
                </a>
                <button type="submit" class="btn btn-primary">Continue to Coupon <i class="fas fa-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</form>

<!-- Include Select2 for multi-select -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#service_ids').select2({
            placeholder: "Search and select services...",
            width: '100%'
        });
    });
</script>