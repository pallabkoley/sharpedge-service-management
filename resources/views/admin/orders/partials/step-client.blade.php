<div class="text-center mb-4">
    <h4>Select Client</h4>
    <p class="text-muted">Choose a client to purchase service for</p>
</div>

<div class="text-center mb-4">
    <h4>Select Client</h4>
    <p class="text-muted">Choose a client to purchase service for</p>
</div>

<form method="POST" action="{{ route('admin.orders.processStep') }}">
    @csrf
    <input type="hidden" name="step" value="1">
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-group">
                <label for="client_id">Client *</label>
                <select name="client_id" id="client_id" class="form-control select2" required>
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">
                            {{ $client->name }} 
                            @if($client->company_name) - {{ $client->company_name }} @endif
                            ({{ $client->client_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    Next: Select Service <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>
</form>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
});
</script>
@endpush