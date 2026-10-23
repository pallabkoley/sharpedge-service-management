@extends('layouts.marketing')

@section('title', 'Order Details - Marketing Panel')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">Order Details</h2>
                        <p class="text-muted mb-0">Order #{{ $order->order_id }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('marketing.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Orders
                        </a>
                        @if($order->order_status === 'PENDING_PAYMENT')
                            <form action="{{ route('marketing.orders.destroy', $order) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to cancel this order?')">
                                    <i class="fas fa-trash me-2"></i> Cancel Order
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Order ID</label>
                            <p class="fw-semibold">#{{ $order->order_id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Order Date</label>
                            <p class="fw-semibold">{{ $order->created_at->format('d M, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Order Status</label>
                            <p>
                                @php
                                    $statusColors = [
                                        'PENDING_PAYMENT' => 'warning',
                                        'PAYMENT_PENDING' => 'info',
                                        'PAYMENT_SUBMITTED' => 'primary',
                                        'PAYMENT_APPROVED' => 'success',
                                        'COMPLETED' => 'success',
                                        'CANCELLED' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$order->order_status] ?? 'secondary' }}">
                                    {{ str_replace('_', ' ', $order->order_status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Created By</label>
                            <p class="fw-semibold">{{ $order->marketingStaff->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Client Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Client Name</label>
                            <p class="fw-semibold">{{ $order->client->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="fw-semibold">{{ $order->client->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Phone</label>
                            <p class="fw-semibold">{{ $order->client->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Company</label>
                            <p class="fw-semibold">{{ $order->client->company_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Service Information</h5>
                    
                    @php
                        $orderItem = $order->items->first();
                        $service = $orderItem->service ?? null;
                    @endphp
                    
                    @if($service)
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Service Name</label>
                            <p class="fw-semibold">{{ $service->service_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Service Code</label>
                            <p class="fw-semibold">{{ $service->service_code }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Category</label>
                            <p class="fw-semibold">{{ $service->category->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Description</label>
                            <p class="fw-semibold">{{ $service->description ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Payment & Amounts -->
        <div class="col-lg-4">
            <!-- Amount Summary -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Amount Summary</h5>
                    
                    <div class="order-summary">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Base Price</span>
                            <span>₹{{ number_format($order->base_price, 2) }}</span>
                        </div>
                        
                        @if($order->govt_fee > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Government Fee</span>
                            <span>₹{{ number_format($order->govt_fee, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($order->tax_amount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax Amount</span>
                            <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($order->platform_fee > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Platform Fee</span>
                            <span>₹{{ number_format($order->platform_fee, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span class="text-muted">Discount</span>
                            <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mt-3 pt-3 border-top fw-bold fs-5">
                            <span>Total Amount</span>
                            <span class="text-primary">₹{{ number_format($order->final_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Payment Information</h5>
                    
                    @if($order->payments->isNotEmpty())
                        @foreach($order->payments as $payment)
                            <div class="payment-item mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Amount:</span>
                                    <span class="fw-semibold">₹{{ number_format($payment->amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Method:</span>
                                    <span>{{ $payment->payment_method }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Date:</span>
                                    <span>{{ $payment->payment_date->format('d M, Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Status:</span>
                                    <span class="badge bg-{{ $payment->status === 'PENDING_APPROVAL' ? 'warning' : 'success' }}">
                                        {{ str_replace('_', ' ', $payment->status) }}
                                    </span>
                                </div>
                                @if($payment->transaction_id)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Transaction ID:</span>
                                    <span class="text-truncate" style="max-width: 150px;">
                                        {{ $payment->transaction_id }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            @if(!$loop->last)<hr>@endif
                        @endforeach
                    @else
                        <p class="text-muted text-center">No payment records found</p>
                    @endif
                </div>
            </div>

            <!-- Coupon Information -->
            @if($order->coupon)
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Coupon Applied</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Coupon Code:</span>
                        <span class="fw-semibold">{{ $order->coupon->code }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Discount:</span>
                        <span class="text-success">
                            @if($order->coupon->type == 'percentage')
                                {{ $order->coupon->value }}%
                            @else
                                ₹{{ number_format($order->coupon->value, 2) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection