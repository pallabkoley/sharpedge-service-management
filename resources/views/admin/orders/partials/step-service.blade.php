<div class="text-center mb-4">
    <h4>Select Service</h4>
    <p class="text-muted">
        Service for: <strong>{{ $client->name }}</strong>
        @if($client->company_name)
            <br><small class="text-muted">{{ $client->company_name }}</small>
        @endif
    </p>
</div>

<form method="POST" action="{{ route('admin.orders.processStep') }}">
    @csrf
    <input type="hidden" name="step" value="2">
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-group">
                <label for="service_id">Service *</label>
                <select name="service_id" id="service_id" class="form-control select2" required>
                    <option value="">-- Select Service --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">
                            {{ $service->service_name }} 
                            - ₹{{ number_format($service->our_fee, 2) }}
                            @if($service->govt_fee > 0)
                                + ₹{{ number_format($service->govt_fee, 2) }} Govt. Fee
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="row mt-4">
                <div class="col-6 text-left">
                    <a href="{{ route('admin.orders.create', ['step' => 1]) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
                <div class="col-6 text-right">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Next: Summary <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
});
</script>
@endpush