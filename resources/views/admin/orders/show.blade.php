@extends('layouts.admin')

@section('title', 'Order Details - SharpEdge Admin')

@section('styles')
<style>
    .page-header {
        background: transparent;
        padding: 1.5rem 0;
        margin-bottom: 1.5rem;
    }
    
    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #eef2f7;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        color: #2d3748;
        font-size: 1.1rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }
    
    .info-item {
        margin-bottom: 0.75rem;
    }
    
    .info-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-size: 1rem;
        color: #2d3748;
        font-weight: 500;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.35rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .status-paid {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .status-cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
    }
    
    .summary-title {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.25rem;
    }
    
    .summary-value {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .payment-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-approved {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .badge-pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .action-btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Order Details</h1>
                <p class="text-muted mb-0">Order #{{ $order->order_id }}</p>
            </div>
            <div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Order Information Card -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-receipt text-primary me-3"></i>
                    <span>Order Information</span>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Order ID</div>
                            <div class="info-value font-monospace">{{ $order->order_id }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Order Date</div>
                            <div class="info-value">{{ $order->created_at->format('d M, Y') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                @php
                                    $status = $order->order_status ?? 'PENDING';
                                    $statusClass = strtolower($status);
                                @endphp
                                <span class="status-badge status-{{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Created By</div>
                            <div class="info-value">{{ $order->created_by ?? 'System' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Information Card -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-user text-primary me-3"></i>
                    <span>Client Information</span>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Name</div>
                            <div class="info-value">{{ $order->client->name ?? 'KANAI CHANDRA DE' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $order->client->email ?? 'kanaichandrae22@gmail.com' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone</div>
                            <div class="info-value">{{ $order->client->phone ?? '9874847343' }}</div>
                        </div>
                       <div class="info-item">
    <div class="info-label">Company</div>
    <div class="info-value">
        {{ $order->client->company_name ?? 'N/A' }}
    </div>
</div>

                    </div>
                </div>
            </div>

            <!-- Service Information Card -->
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-cogs text-primary me-3"></i>
                    <span>Service Information</span>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                       <div class="info-item">
    <div class="info-label">Service Name</div>
    <div class="info-value">
        {{ $order->items->first()?->service?->name ?? 'N/A' }}
    </div>
</div>

                        <div class="info-item">
                            <div class="info-label">Service Code</div>
                            <div class="info-value">{{ $order->service->code ?? 'SV-001' }}</div>
                        </div>
                    </div>
                    @if(isset($order->service->description))
                        <div class="mt-3 pt-3 border-top">
                            <div class="info-label">Description</div>
                            <p class="text-muted mb-0">{{ $order->service->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Payment Details Card -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-file-invoice-dollar text-primary me-3"></i>
                    <span>Payment Details</span>
                </div>

                @php
                    $serviceFee   = $order->base_price;
                    $discount     = $order->discount_amount ?? 0;
                    $taxableAmount = $serviceFee - $discount;

                    $cgstRate = 9;
                    $sgstRate = 9;

                    $cgstAmount = ($taxableAmount * $cgstRate) / 100;
                    $sgstAmount = ($taxableAmount * $sgstRate) / 100;

                    $govtFee = $order->display_govt_fee;
                    $grandTotal = $order->final_amount;
                @endphp

                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Service Fee</span>
                        <span>₹{{ number_format($serviceFee, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount</span>
                        <span class="text-success">
                            -₹{{ number_format($discount, 2) }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Taxable Amount</span>
                        <span>₹{{ number_format($taxableAmount, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>CGST @ {{ $cgstRate }}%</span>
                        <span>₹{{ number_format($cgstAmount, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>SGST @ {{ $sgstRate }}%</span>
                        <span>₹{{ number_format($sgstAmount, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Government Fee (Non-taxable)</span>
                        <span>₹{{ number_format($govtFee, 2) }}</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Grand Total</span>
                        <span>₹{{ number_format($grandTotal, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Amount Summary Card -->
            <!--<div class="summary-card mb-4">-->
            <!--    <div class="d-flex justify-content-between align-items-start">-->
            <!--        <div>-->
            <!--            <div class="summary-title">Total Amount</div>-->
            <!--            <div class="summary-value">-->
            <!--                ₹{{ number_format($order->final_amount, 2) }}-->
            <!--            </div>-->
            <!--        </div>-->
            <!--        <i class="fas fa-receipt fa-2x opacity-50"></i>-->
            <!--    </div>-->

            <!--    <div class="mt-4">-->
            <!--        <div class="d-flex justify-content-between mb-2">-->
            <!--            <span class="opacity-75">Service Fee</span>-->
            <!--            <span>₹{{ number_format($order->base_price, 2) }}</span>-->
            <!--        </div>-->

            <!--        <div class="d-flex justify-content-between mb-2">-->
            <!--            <span class="opacity-75">Discount</span>-->
            <!--            <span class="text-success">-->
            <!--                -₹{{ number_format($order->discount_amount ?? 0, 2) }}-->
            <!--            </span>-->
            <!--        </div>-->

            <!--        <div class="d-flex justify-content-between mb-2">-->
            <!--            <span class="opacity-75">Government Fee (Non-taxable)</span>-->
            <!--            <span>-->
            <!--                ₹{{ number_format($order->display_govt_fee, 2) }}-->
            <!--            </span>-->
            <!--        </div>-->

            <!--        <hr class="my-3 opacity-25">-->

            <!--        <div class="d-flex justify-content-between fw-bold">-->
            <!--            <span>Final Amount</span>-->
            <!--            <span>₹{{ number_format($order->final_amount, 2) }}</span>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->

            <!-- Payment Information Card -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-credit-card text-primary me-3"></i>
                    <span>Payment Information</span>
                </div>

                @php
                    $latestPayment = $order->payments->sortByDesc('created_at')->first();
                @endphp

                <div class="card-body">
                    <div class="info-item mb-3">
                        <div class="info-label">Paid Amount</div>
                        <div class="info-value h5 text-primary">
                            ₹{{ number_format($order->payments->sum('amount'), 2) }}
                        </div>
                    </div>

                    <div class="info-item mb-3">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value">
                            {{ $latestPayment->method ?? 'PAY_LATER' }}
                        </div>
                    </div>

                    <div class="info-item mb-3">
                        <div class="info-label">Last Payment Date</div>
                        <div class="info-value">
                            {{ $latestPayment?->created_at?->format('d M, Y') ?? '—' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Payment Status</div>
                        <div class="info-value">
                            <span class="payment-badge badge-{{ strtolower($order->payment_status) }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <!--<div class="card">-->
            <!--    <div class="card-header d-flex align-items-center">-->
            <!--        <i class="fas fa-bolt text-primary me-3"></i>-->
            <!--        <span>Actions</span>-->
            <!--    </div>-->
            <!--    <div class="card-body">-->
            <!--        <a href="#" class="btn btn-primary action-btn">-->
            <!--            <i class="fas fa-download me-2"></i> Download Invoice-->
            <!--        </a>-->
            <!--        <a href="{{ route('admin.orders.create') }}" class="btn btn-outline-primary action-btn">-->
            <!--            <i class="fas fa-plus me-2"></i> Create New Order-->
            <!--        </a>-->
            <!--    </div>-->
            </div>
        </div>
    </div>
</div>
@endsection