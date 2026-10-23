@extends('layouts.marketing')

@section('title', 'Select Service - SharpEdge Marketing')

@section('content')
<div class="header mb-4">
    <h2>Select Service</h2>
    <p class="text-gray-600">Choose service for {{ $client->name }}</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="section-card">
            <h3 class="section-title mb-4">Available Services</h3>
            
            <!-- Services Grid -->
            <div class="row g-3" id="servicesContainer">
                @foreach($services as $service)
                <div class="col-md-6">
                    <div class="card border h-100 service-card" data-service-id="{{ $service->id }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title fw-semibold">{{ $service->name }}</h6>
                                <span class="badge bg-info">{{ $service->category }}</span>
                            </div>
                            <p class="card-text text-gray-600 small mb-3">{{ $service->description }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">₹{{ number_format($service->price, 2) }}</span>
                                <button type="button" class="btn btn-sm btn-outline-primary select-service-btn"
                                        data-service-id="{{ $service->id }}"
                                        data-service-name="{{ $service->name }}"
                                        data-service-price="{{ $service->price }}">
                                    <i class="fas fa-plus me-1"></i> Select
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="section-card sticky-top" style="top: 20px;">
            <h3 class="section-title">Order Summary</h3>
            
            <!-- Selected Service -->
            <div id="selectedService" class="mb-4">
                <p class="text-gray-500 text-center">No service selected yet</p>
            </div>
            
            <!-- Order Form -->
            <form action="{{ route('marketing.orders.store') }}" method="POST" id="orderForm">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="service_id" id="serviceIdInput">
                <input type="hidden" name="base_price" id="basePriceInput" value="0">
                <input type="hidden" name="final_amount" id="finalAmountInput" value="0">
                <input type="hidden" name="discount_percent" value="0">
                <input type="hidden" name="discount_amount" value="0">
                
                <!-- Price Display -->
                <div class="border rounded p-3 bg-light mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Service Price</span>
                        <span id="servicePriceDisplay">₹0.00</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-3">
                        <span class="fw-bold">Total Amount</span>
                        <span class="fw-bold text-primary fs-5" id="totalAmountDisplay">₹0.00</span>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg" id="createOrderBtn" disabled>
                        <i class="fas fa-shopping-cart me-2"></i> Create Order
                    </button>
                    <a href="{{ route('marketing.orders.create') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Client Selection
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedService = null;
        const createOrderBtn = document.getElementById('createOrderBtn');
        const serviceIdInput = document.getElementById('serviceIdInput');
        const basePriceInput = document.getElementById('basePriceInput');
        const finalAmountInput = document.getElementById('finalAmountInput');
        const servicePriceDisplay = document.getElementById('servicePriceDisplay');
        const totalAmountDisplay = document.getElementById('totalAmountDisplay');
        const selectedServiceContainer = document.getElementById('selectedService');
        
        // Service selection
        document.querySelectorAll('.select-service-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                selectedService = {
                    id: this.dataset.serviceId,
                    name: this.dataset.serviceName,
                    price: parseFloat(this.dataset.servicePrice)
                };
                
                updateSelectedServiceDisplay();
                updateOrderForm();
                
                // Enable create order button
                createOrderBtn.disabled = false;
                
                // Highlight selected card
                document.querySelectorAll('.service-card').forEach(card => {
                    card.classList.remove('border-primary', 'bg-light');
                });
                this.closest('.service-card').classList.add('border-primary', 'bg-light');
            });
        });
        
        function updateSelectedServiceDisplay() {
            if (selectedService) {
                selectedServiceContainer.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-semibold mb-0">${selectedService.name}</h6>
                            <small class="text-gray-500">Selected Service</small>
                        </div>
                        <span class="fw-semibold">₹${selectedService.price.toFixed(2)}</span>
                    </div>
                `;
            }
        }
        
        function updateOrderForm() {
            if (selectedService) {
                serviceIdInput.value = selectedService.id;
                basePriceInput.value = selectedService.price;
                finalAmountInput.value = selectedService.price;
                servicePriceDisplay.textContent = `₹${selectedService.price.toFixed(2)}`;
                totalAmountDisplay.textContent = `₹${selectedService.price.toFixed(2)}`;
            }
        }
    });
</script>
@endsection