@extends('layouts.admin')

@section('title', 'Buy Service for Client - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Progress Steps -->
            <div class="card">
                <div class="card-body">
                    <div class="steps">
                        <div class="step {{ $step >= 1 ? 'active' : '' }}">
                            <div class="step-icon">1</div>
                            <div class="step-label">Select Client</div>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step {{ $step >= 2 ? 'active' : '' }}">
                            <div class="step-icon">2</div>
                            <div class="step-label">Select Service</div>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step {{ $step >= 3 ? 'active' : '' }}">
                            <div class="step-icon">3</div>
                            <div class="step-label">Confirm & Create</div>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step {{ $step >= 4 ? 'active' : '' }}">
                            <div class="step-icon">✓</div>
                            <div class="step-label">Done</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step Content -->
            <div class="card mt-3">
                <div class="card-body">
                    @if($step == 1)
                        @include('admin.orders.partials.step-client')
                    @elseif($step == 2)
                        @include('admin.orders.partials.step-service')
                    @elseif($step == 3)
                        @include('admin.orders.partials.step-summary')
                    @elseif($step == 4)
                        @include('admin.orders.partials.step-done')
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.steps {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 0;
}
.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}
.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 5px;
    border: 2px solid #dee2e6;
}
.step.active .step-icon {
    background: #007bff;
    color: white;
    border-color: #007bff;
}
.step-label {
    font-size: 12px;
    color: #6c757d;
}
.step.active .step-label {
    color: #007bff;
    font-weight: 500;
}
.step-divider {
    flex: 1;
    height: 2px;
    background: #dee2e6;
    margin: 0 10px;
}
</style>
@endsection