@extends('layouts.admin')

@section('title', 'Create Service Request')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Service Request</h1>
        <a href="{{ route('admin.service-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <!-- Create Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">New Service Request Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.service-requests.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Select Client *</label>
                            <select name="client_id" id="client_id" class="form-control" required>
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="service_id" class="form-label">Select Service *</label>
                            <select name="service_id" id="service_id" class="form-control" required>
                                <option value="">-- Select Service --</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}" 
                                        data-gov-fee="{{ $service->government_fee }}"
                                        data-pro-fee="{{ $service->professional_fee }}">
                                    {{ $service->name }} - ₹{{ $service->professional_fee }} + Govt: ₹{{ $service->government_fee }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>Government Fee</h6>
                                <h4 id="gov-fee">₹0.00</h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>Professional Fee</h6>
                                <h4 id="pro-fee">₹0.00</h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>Total Amount</h6>
                                <h4 id="total-fee">₹0.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="client_notes" class="form-label">Client Notes (Optional)</label>
                    <textarea name="client_notes" id="client_notes" class="form-control" rows="3" 
                              placeholder="Any notes from the client..."></textarea>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Calculate fees when service is selected
    document.getElementById('service_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const govFee = parseFloat(selectedOption.getAttribute('data-gov-fee')) || 0;
        const proFee = parseFloat(selectedOption.getAttribute('data-pro-fee')) || 0;
        const totalFee = govFee + proFee;
        
        document.getElementById('gov-fee').textContent = '₹' + govFee.toFixed(2);
        document.getElementById('pro-fee').textContent = '₹' + proFee.toFixed(2);
        document.getElementById('total-fee').textContent = '₹' + totalFee.toFixed(2);
    });
</script>
@endsection
@endsection