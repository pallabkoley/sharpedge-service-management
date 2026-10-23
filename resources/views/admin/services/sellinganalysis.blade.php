@extends('layouts.admin')

@push('styles')
<style>
    /* Clean Stats Card Styles */
    .stats-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: all 0.25s ease;
        border: 1px solid #f1f5f9;
        height: 100%;
    }
    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .card-title {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        letter-spacing: 0.5px;
        margin: 0;
    }
    .card-value {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 5px 0;
    }
    .card-subtext {
        font-size: 13px;
        color: #94a3b8;
        margin: 0;
    }
    .card-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }

    .bg-primary { background: #3b82f6; }
    .bg-success { background: #10b981; }
    .bg-info { background: #06b6d4; }
    .bg-warning { background: #f59e0b; }

    /* Table Header – Light Gray (Staff Management Style) */
    #serviceStatsTable thead th,
    #serviceStatsTable thead td,
    .dataTable thead th,
    .dataTable thead td {
        background-color: #f8fafc !important;
        color: #64748b !important;
        font-weight: 600 !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Service Selling Analysis</h2>
            <p class="text-muted">Track sales performance of your services</p>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.service.selling.analysis') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <a href="{{ route('admin.service.selling.analysis') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title">TOTAL ORDERS</h6>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-cart"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ number_format($totalOrders) }}</h3>
                <p class="card-subtext">All service orders</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title">TOTAL REVENUE</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-currency-rupee"></i>
                    </div>
                </div>
                <h3 class="card-value">₹{{ number_format($totalRevenue, 2) }}</h3>
                <p class="card-subtext">Overall earnings</p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title">TOP SERVICE</h6>
                    <div class="card-icon bg-info">
                        <i class="bi bi-star"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-1">{{ $topService->service_name ?? 'N/A' }}</h6>
                <p class="card-subtext">
                    {{ $topService->total_sold ?? 0 }} sales • ₹{{ number_format($topService->total_revenue ?? 0, 2) }}
                </p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title">LOWEST SERVICE</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-arrow-down"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-1">{{ $lowestService->service_name ?? 'N/A' }}</h6>
                <p class="card-subtext">
                    {{ $lowestService->total_sold ?? 0 }} sales • ₹{{ number_format($lowestService->total_revenue ?? 0, 2) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Bar Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Sales by Service</h5>
        </div>
        <div class="card-body">
            <canvas id="serviceChart" style="height: 400px; width: 100%;"></canvas>
        </div>
    </div>

    <!-- Data Table with Custom DataTable -->
    <div class="card">
        <div class="card-header">
            <h5>Detailed Service Statistics</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="serviceStatsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Service Name</th>
                            <th>Total Sold</th>
                            <th>Total Revenue (₹)</th>
                            <th>Average Price (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceStats as $index => $service)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $service->service_name }}</td>
                                <td>{{ number_format($service->total_sold) }}</td>
                                <td>₹{{ number_format($service->total_revenue, 2) }}</td>
                                <td>₹{{ number_format($service->total_sold ? $service->total_revenue / $service->total_sold : 0, 2) }}</td>
                            </tr>
                        @empty
                            <!-- ❌ সরানো হয়েছে – DataTable নিজেই empty message দেখাবে -->
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ---------- Chart ----------
        const serviceNames = @json($serviceStats->pluck('service_name'));
        const totalSold = @json($serviceStats->pluck('total_sold'));
        const totalRevenue = @json($serviceStats->pluck('total_revenue'));

        const ctx = document.getElementById('serviceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: serviceNames,
                datasets: [{
                    label: 'Total Sold (units)',
                    data: totalSold,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y-sold'
                }, {
                    label: 'Total Revenue (₹)',
                    data: totalRevenue,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    yAxisID: 'y-revenue'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    'y-sold': {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Units Sold'
                        },
                        grid: {
                            color: '#e9ecef'
                        }
                    },
                    'y-revenue': {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (₹)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw;
                                if (context.dataset.label.includes('Revenue')) {
                                    return label + ': ₹' + value.toLocaleString();
                                }
                                return label + ': ' + value.toLocaleString() + ' units';
                            }
                        }
                    }
                }
            }
        });

        // ---------- DataTable Initialization (custom) ----------
        // Check if DataTable is already initialized on this table
        if ($.fn.DataTable.isDataTable('#serviceStatsTable')) {
            $('#serviceStatsTable').DataTable().destroy();
        }

        $('#serviceStatsTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                emptyTable: 'No data available for the selected period.',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'Showing 0 to 0 of 0 entries',
                infoFiltered: '(filtered from _MAX_ total entries)',
                paginate: {
                    previous: 'Previous',
                    next: 'Next'
                },
                search: 'Search:',
                searchPlaceholder: 'Type to filter...'
            },
            responsive: true,
            dom: 'lfrtip'  // লেআউটের মতোই রাখা
        });
    });
</script>
@endpush