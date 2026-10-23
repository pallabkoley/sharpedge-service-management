<form action="{{ route('admin.orders.processStep') }}" method="POST">
    @csrf
    <input type="hidden" name="step" value="2">
    
    <!-- Step Header -->
    <div class="step-header mb-5">
        <h2 class="fw-bold text-gray-800 mb-2">Step 2: Select Services</h2>
        <p class="text-gray-600">Choose one or more services for {{ $client->name }}.</p>
    </div>

    <!-- Client Info -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-circle me-3 fs-4"></i>
            <div>
                <h6 class="mb-1">Client Information</h6>
                <p class="mb-0">{{ $client->name }} • {{ $client->email }} • {{ $client->phone }}</p>
            </div>
        </div>
    </div>

    <!-- Service Selection (MULTIPLE) -->
    <div class="mb-4">
        <h5 class="fw-semibold text-gray-700 mb-3">Select Services</h5>
        <select name="service_ids[]" id="service_ids" class="form-select" multiple required 
                style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; height: auto; min-height: 150px;">
            @foreach($services as $service)
                <option value="{{ $service->id }}">
                    {{ $service->service_name }} - ₹{{ number_format($service->our_fee, 2) }}
                </option>
            @endforeach
        </select>
        <small class="text-muted mt-2 d-block">
            <i class="fas fa-info-circle"></i> Hold <kbd>Ctrl</kbd> (Windows) or <kbd>Cmd</kbd> (Mac) to select multiple services.
        </small>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-5 pt-4 border-top">
        <a href="{{ route('admin.orders.create', ['step' => 1]) }}" class="btn btn-outline-secondary px-5 py-2">
            <i class="fas fa-arrow-left me-2"></i> Back to Clients
        </a>
        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="continueBtn" disabled>
            Continue to Coupon
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const serviceSelect = document.getElementById('service_ids');
        const continueBtn = document.getElementById('continueBtn');
        
        function toggleButton() {
            // Enable if at least one option is selected
            continueBtn.disabled = serviceSelect.selectedOptions.length === 0;
        }
        
        serviceSelect.addEventListener('change', toggleButton);
        // Initial check (in case of pre-selected values)
        toggleButton();
    });
</script>