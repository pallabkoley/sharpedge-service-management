@extends('layouts.marketing')

@section('title', 'Order Details - SharpEdge Marketing')

@section('content')
<div class="header mb-4">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2>Order Details</h2>
            <p class="text-gray-600">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div>
            @php
                $statusClass = [
                    'PENDING_PAYMENT' => 'warning',
                    'PAYMENT_PENDING' => 'info',
                    'PAYMENT_SUBMITTED' => 'primary',
                    'APPROVED' => 'success',
                    'REJECTED' => 'danger',
                    'CANCELLED' => 'secondary'
                ][$order->order_status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $statusClass }} fs-6">
                {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
            </span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Order Information -->
    <div class="col-lg-8">
        <div class="section-card mb-4">
            <h3 class="section-title">Order Information</h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-gray-500">Client</label>
                    <p class="fw-semibold">{{ $order->client->name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-gray-500">Created By</label>
                    <p class="fw-semibold">{{ $order->marketingStaff->name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-gray-500">Order Date</label>
                    <p class="fw-semibold">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-gray-500">Last Updated</label>
                    <p class="fw-semibold">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Service Details -->
        <div class="section-card mb-4">
            <h3 class="section-title">Service Details</h3>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Description</th>
                            <th class="text-end">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->service->name }}</strong>
                                <p class="text-gray-600 small mb-0">{{ $item->service->category }}</p>
                            </td>
                            <td>
                                <p class="text-gray-600 small mb-0">{{ $item->service->description }}</p>
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($item->price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Payment History -->
        @if($order->payments->count() > 0)
        <div class="section-card">
            <h3 class="section-title">Payment History</h3>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Transaction ID</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $payment->payment_method }}</span>
                            </td>
                            <td>
                                <code>{{ $payment->transaction_id ?? 'N/A' }}</code>
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                @php
                                    $paymentStatusClass = [
                                        'PENDING_APPROVAL' => 'warning',
                                        'APPROVED' => 'success',
                                        'REJECTED' => 'danger'
                                    ][$payment->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $paymentStatusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Order Summary -->
    <div class="col-lg-4">
        <div class="section-card sticky-top" style="top: 20px;">
            <h3 class="section-title">Order Summary</h3>
            
            <div class="border rounded p-3 bg-light mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span>Base Price</span>
                    <span>₹{{ number_format($order->base_price, 2) }}</span>
                </div>
                
                @if($order->discount_percent > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount ({{ $order->discount_percent }}%)</span>
                    <span class="text-success">-₹{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                @endif
                
                <div class="d-flex justify-content-between border-top pt-3 mb-3">
                    <span class="fw-bold">Total Amount</span>
                    <span class="fw-bold text-primary fs-5">₹{{ number_format($order->final_amount, 2) }}</span>
                </div>
                
                @if($order->payments->where('status', 'APPROVED')->sum('amount') > 0)
                <div class="d-flex justify-content-between small text-gray-500">
                    <span>Amount Paid</span>
                    <span>₹{{ number_format($order->payments->where('status', 'APPROVED')->sum('amount'), 2) }}</span>
                </div>
                
                <div class="d-flex justify-content-between small text-gray-500">
                    <span>Balance</span>
                    <span class="fw-semibold {{ $order->final_amount - $order->payments->where('status', 'APPROVED')->sum('amount') > 0 ? 'text-danger' : 'text-success' }}">
                        ₹{{ number_format($order->final_amount - $order->payments->where('status', 'APPROVED')->sum('amount'), 2) }}
                    </span>
                </div>
                @endif
            </div>
            
            <!-- Actions -->
            <div class="d-grid gap-2">
                @if($order->order_status == 'PENDING_PAYMENT')
                <a href="{{ route('marketing.payments.create', $order) }}" class="btn btn-primary">
                    <i class="fas fa-credit-card me-2"></i> Make Payment
                </a>
                @endif
                
                @if($order->payments->where('status', 'APPROVED')->sum('amount') >= $order->final_amount)
                <div class="alert alert-success small mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Fully Paid
                </div>
                @endif
                
                <a href="{{ route('marketing.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Orders
                </a>
                
                @if($order->order_status == 'PENDING_PAYMENT')
                <form action="{{ route('marketing.orders.destroy', $order) }}" 
                      method="POST"
                      onsubmit="return confirm('Are you sure you want to cancel this order?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-times me-2"></i> Cancel Order
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection