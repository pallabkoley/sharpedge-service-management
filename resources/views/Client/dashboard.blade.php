@extends('layouts.client')

@section('title', 'Dashboard - SharpEdge Client')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . (Auth::user()->name ?? 'Guest') . '. Here\'s what\'s happening with your orders today.')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TOTAL ORDERS</h6>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-briefcase"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $totalOrders ?? 0 }}</h3>
                <div class="card-change text-muted small">
                    All time orders
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">ACTIVE ORDERS</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $activeOrders ?? 0 }}</h3>
                <div class="card-change text-muted small">
                    Currently in progress
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING ACTIONS</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $pendingActions ?? 0 }}</h3>
                <div class="card-change text-muted small">
                    Tasks waiting for you
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">TOTAL PAID</h6>
                    <div class="card-icon bg-info">
                        <i class="bi bi-currency-rupee"></i>
                    </div>
                </div>
                <h3 class="card-value">₹{{ number_format($totalPaidAmount ?? 0) }}</h3>
                <div class="card-change text-muted small">
                    Lifetime payments approved
                </div>
            </div>
        </div>
    </div>

    @if(!empty($currentOrder))
    <!-- Current Active Order -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="section-card bg-light border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Current Active Order</h5>
                        <p class="mb-0 text-muted">#{{ $currentOrder->id ?? '—' }} - {{ $currentOrder->service->name ?? 'Service' }}</p>
                    </div>
                    <a href="{{ route('client.orders.index') }}" class="btn btn-outline-primary btn-sm">View Details</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Content Section with Chart and Quick Report -->
    <div class="content-section mt-4">
        <div class="row g-4">
            <!-- Order Status Overview Chart -->
            <div class="col-lg-8">
                <div class="section-card">
                    <h4 class="section-title">Order Status Overview</h4>
                    
                    @php
                        // Convert collections to arrays for safe sum calculation
                        $chartLabelsArray = $chartLabels instanceof \Illuminate\Support\Collection ? $chartLabels->toArray() : (array)($chartLabels ?? []);
                        $chartValuesArray = $chartValues instanceof \Illuminate\Support\Collection ? $chartValues->toArray() : (array)($chartValues ?? []);
                        $hasChartData = !empty($chartLabelsArray) && !empty($chartValuesArray) && array_sum($chartValuesArray) > 0;
                    @endphp

                    @if($hasChartData)
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <canvas id="orderStatusChart" style="max-height: 250px; width: 100%;"></canvas>
                            </div>
                            <div class="col-md-5">
                                <div class="chart-legend">
                                    @foreach($chartLabelsArray as $index => $label)
                                        @php
                                            $color = match($label) {
                                                'PAID' => '#28a745',
                                                'PENDING_PAYMENT' => '#fd7e14',
                                                'PAYMENT_SUBMITTED' => '#007bff',
                                                'CREDIT_PENDING' => '#6f42c1',
                                                'CANCELLED' => '#dc3545',
                                                default => '#6c757d'
                                            };
                                            $displayLabel = str_replace('_', ' ', $label);
                                            $value = $chartValuesArray[$index] ?? 0;
                                        @endphp
                                        <div class="legend-item d-flex align-items-center mb-2">
                                            <span class="legend-color me-2" style="width: 16px; height: 16px; background-color: {{ $color }}; border-radius: 4px;"></span>
                                            <span class="legend-label flex-grow-1">{{ $displayLabel }}</span>
                                            <span class="legend-value fw-bold">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="chart-placeholder text-center py-5">
                            <i class="bi bi-pie-chart fs-1 text-muted mb-3"></i>
                            <p class="text-muted">No order data available yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Report -->
            <div class="col-lg-4">
                <div class="section-card h-100">
                    <h4 class="section-title">Quick Report</h4>
                    <div class="quick-report-content">
                        <div class="report-item d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Orders:</span>
                            <span class="fw-bold">{{ $totalOrders ?? 0 }}</span>
                        </div>
                        <div class="report-item d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Active Orders:</span>
                            <span class="fw-bold">{{ $activeOrders ?? 0 }}</span>
                        </div>
                        <div class="report-item d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total Paid:</span>
                            <span class="fw-bold">₹{{ number_format($totalPaidAmount ?? 0) }}</span>
                        </div>
                        
                        @if(!empty($recentOrders) && $recentOrders->count() > 0)
                            <h6 class="text-muted mt-3 mb-2">Most Recent Order</h6>
                            @php $latest = $recentOrders->first(); @endphp
                            <div class="recent-order-card p-3 bg-light rounded">
                                <div class="fw-bold mb-1">#{{ $latest->id }}</div>
                                <div class="small text-muted">{{ $latest->service->name ?? '—' }}</div>
                                <div class="small">{{ $latest->created_at ? $latest->created_at->format('d M Y') : '—' }}</div>
                            </div>
                        @else
                            <div class="text-center text-muted mt-4">
                                <i class="bi bi-inbox fs-2"></i>
                                <p class="small">No recent orders</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="mt-4">
        <div class="section-card">
            <h4 class="section-title">Recent Orders</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Service</th>
                            <th>Order Date</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Work Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($recentOrders ?? []) as $order)
                        <tr>
                            <td class="fw-bold">#{{ $order->id }}</td>
                            <td>{{ $order->service->name ?? '—' }}</td>
                            <td>{{ $order->created_at ? $order->created_at->format('d M Y') : '—' }}</td>
                            <td>₹{{ number_format($order->final_amount ?? 0) }}</td>
                            <td>
                                @php
                                    $paymentStatus = $order->payment_status ?? 'pending';
                                    $paymentClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'submitted' => 'bg-primary text-white',
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                    ];
                                    $paymentLabels = [
                                        'pending' => 'Pending',
                                        'submitted' => 'Submitted',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                    ];
                                @endphp
                                <span class="badge {{ $paymentClasses[$paymentStatus] ?? 'bg-secondary' }} px-3 py-2 rounded-pill">
                                    {{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $workStatus = strtolower($order->calculated_work_status ?? 'unassigned');
                                    $workClasses = [
                                        'unassigned' => 'bg-secondary text-white',
                                        'not_started' => 'bg-secondary text-white',
                                        'assigned' => 'bg-primary text-white',
                                        'in_progress' => 'bg-info text-white',
                                        'waiting_client' => 'bg-warning text-dark',
                                        'submitted' => 'bg-purple text-white',
                                        'verified' => 'bg-success text-white',
                                        'completed' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                    ];
                                    $workLabels = [
                                        'unassigned' => 'Unassigned',
                                        'not_started' => 'Not Started',
                                        'assigned' => 'Assigned',
                                        'in_progress' => 'In Progress',
                                        'waiting_client' => 'Waiting for You',
                                        'submitted' => 'Submitted',
                                        'verified' => 'Verified',
                                        'completed' => 'Completed',
                                        'rejected' => 'Rejected',
                                    ];
                                @endphp
                                <span class="badge {{ $workClasses[$workStatus] ?? 'bg-secondary' }} px-3 py-2 rounded-pill">
                                    {{ $workLabels[$workStatus] ?? ucfirst(str_replace('_', ' ', $workStatus)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No recent orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3">
                <a href="{{ route('client.orders.index') }}" class="btn btn-link">View All Orders <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Reuse the same status badge styles from orders index if needed */
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
    }
    .stats-card .card-change {
        font-size: 0.85rem;
    }
    
    /* Chart legend styling */
    .legend-item {
        font-size: 0.9rem;
    }
    .legend-color {
        display: inline-block;
        border-radius: 4px;
    }
    .chart-placeholder {
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    /* Quick Report styling */
    .quick-report-content {
        padding: 0.5rem 0;
    }
    .report-item {
        font-size: 1rem;
    }
    .section-card.h-100 {
        display: flex;
        flex-direction: column;
    }
    .quick-report-content {
        flex: 1;
    }
    .recent-order-card {
        border-left: 3px solid #0d6efd;
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize chart if data exists
    @if(!empty($chartLabels) && !empty($chartValues) && (($chartValues instanceof \Illuminate\Support\Collection ? $chartValues->sum() : array_sum($chartValues ?? [])) > 0))
        const ctx = document.getElementById('orderStatusChart').getContext('2d');
        
        // Color mapping
        const colorMap = {
            'PAID': '#28a745',
            'PENDING_PAYMENT': '#fd7e14',
            'PAYMENT_SUBMITTED': '#007bff',
            'CREDIT_PENDING': '#6f42c1',
            'CANCELLED': '#dc3545'
        };
        
        const labels = {!! json_encode($chartLabels) !!};
        const values = {!! json_encode($chartValues) !!};
        
        // Generate background colors based on labels
        const backgroundColors = labels.map(label => colorMap[label] || '#6c757d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: backgroundColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false // We'll use custom legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    @endif
});
</script>
@endpush