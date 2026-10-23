@extends('layouts.Backoffice')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Form Column (Full width, no right sidebar on create) -->
        <div class="col-lg-12">
            {{-- Stepper (matches your existing design) --}}
            @include('Backoffice.computation.partials.stepper', ['current' => 'client'])

            <div class="card shadow mt-3 border-0 rounded-4">
                <div class="card-header bg-white border-bottom rounded-top-4">
                    <h5 class="mb-0 fw-bold">Client & Financial Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('backoffice.computations.store') }}" method="POST">
                        @csrf

                        {{-- Client Selection Row --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                                <select name="client_id" class="form-select shadow-sm" id="client_select" required>
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                        data-pan="{{ $client->pan }}"
                                        data-email="{{ $client->email }}"
                                        data-phone="{{ $client->phone }}"
                                        data-type="{{ $client->client_type_id ?? '' }}">
                                        {{ $client->name }} ({{ $client->pan ?? 'PAN not available' }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Client Type <span class="text-danger">*</span></label>
                                <select name="client_type_id" class="form-select shadow-sm" id="client_type_select" required>
                                    <option value="">Select Type</option>
                                    @foreach($clientTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Personal Details Section --}}
                        <h6 class="fw-bold text-primary mt-4">📋 Personal Details</h6>
                        <hr class="mt-2 mb-3">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">PAN</label>
                                <input type="text" name="pan" id="pan" maxlength="10" class="form-control shadow-sm">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Aadhaar</label>
                                <input type="text" name="aadhaar" id="aadhaar" maxlength="12" class="form-control shadow-sm">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DOB</label>
                                <input type="date" name="dob" class="form-control shadow-sm">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Residential Status</label>
                                <select name="residential_status" class="form-select shadow-sm">
                                    <option value="resident">Resident</option>
                                    <option value="non_resident">Non Resident</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tax Regime</label>
                                <select name="tax_regime" class="form-select shadow-sm">
                                    <option value="old">Old</option>
                                    <option value="new">New</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="mobile" id="mobile" class="form-control shadow-sm">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control shadow-sm">
                            </div>
                        </div>

                        {{-- Financial Details Section --}}
                        <h6 class="fw-bold text-primary mt-4">💰 Financial Details</h6>
                        <hr class="mt-2 mb-3">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Financial Year</label>
                                <input type="text" name="financial_year" class="form-control shadow-sm" value="{{ date('Y')-1 }}-{{ date('Y') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Assessment Year</label>
                                <input type="text" name="assessment_year" class="form-control shadow-sm" value="{{ date('Y') }}" required>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('backoffice.computations.index') }}" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Next: Income Details →</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Auto-fill client details + PAN & Aadhaar validation --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientSelect = document.getElementById('client_select');
        const panInput = document.getElementById('pan');
        const emailInput = document.getElementById('email');
        const mobileInput = document.getElementById('mobile');
        const clientTypeSelect = document.getElementById('client_type_select');
        const aadhaarInput = document.getElementById('aadhaar');

        // Auto-fill from client selection
        if (clientSelect) {
            clientSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    panInput.value = selectedOption.dataset.pan || '';
                    emailInput.value = selectedOption.dataset.email || '';
                    mobileInput.value = selectedOption.dataset.phone || '';

                    const clientTypeId = selectedOption.dataset.type;
                    if (clientTypeId && clientTypeSelect) {
                        clientTypeSelect.value = clientTypeId;
                    }
                } else {
                    panInput.value = '';
                    emailInput.value = '';
                    mobileInput.value = '';
                    if (clientTypeSelect) clientTypeSelect.value = '';
                }
            });
        }

        // ✅ PAN Validation (India format)
        panInput.addEventListener('input', function () {
            let value = this.value.toUpperCase();
            this.value = value;

            const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

            if (value.length === 10) {
                if (!panRegex.test(value)) {
                    this.setCustomValidity('Invalid PAN format (Example: ABCDE1234F)');
                } else {
                    this.setCustomValidity('');
                }
            } else {
                this.setCustomValidity('');
            }
        });

        // ✅ Aadhaar Validation (12 digits only)
        aadhaarInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            this.value = value;

            if (value.length === 12) {
                this.setCustomValidity('');
            } else {
                this.setCustomValidity('Aadhaar must be 12 digits');
            }
        });
    });
</script>
@endsection