@extends('layouts.marketing')

@section('title', 'Dashboard - SharpEdge Marketing')

@section('content')
<div class="header d-flex justify-content-between align-items-center">
    <div>
        <h2>Dashboard</h2>
        <p>Welcome back! Here's what's happening with your clients today.</p>
    </div>

    <div class="attendance-section">
        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Attendance Button --}}
        @if($attendance === null)
            <form action="{{ route('marketing.attendance.clockIn') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success shadow-sm">
                    <i class="fas fa-clock me-2"></i> Clock In
                </button>
                <small class="d-block text-muted mt-1">Click to start your workday</small>
            </form>

        @elseif($attendance->clock_out_time === null)
            <div class="attendance-info">
                <div class="d-flex align-items-center mb-2">
                    <div class="clock-status bg-success rounded-circle p-2 me-3">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Clocked In</h6>
                        <p class="text-muted mb-0">{{ date('h:i A', strtotime($attendance->clock_in_time)) }}</p>
                    </div>
                </div>

                <form action="{{ route('marketing.attendance.clockOut') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning shadow-sm w-100">
                        <i class="fas fa-sign-out-alt me-2"></i> Clock Out
                    </button>
                </form>
            </div>

        @else
            <div class="attendance-completed">
                <div class="alert alert-info mb-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-1">Attendance Completed</h6>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-primary">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    {{ date('h:i A', strtotime($attendance->clock_in_time)) }}
                                </span>

                                <span class="badge bg-success">
                                    <i class="fas fa-sign-out-alt me-1"></i>
                                    {{ date('h:i A', strtotime($attendance->clock_out_time)) }}
                                </span>

                                @if($attendance->total_work_seconds > 0)
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ floor($attendance->total_work_seconds / 3600) }}h
                                        {{ floor(($attendance->total_work_seconds % 3600) / 60) }}m
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- 5 Data Cards in Single Row -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 mb-4">
    <!-- Card 1: Total Clients -->
    <div class="col">
        <a href="{{ route('marketing.clients.index') }}" class="text-decoration-none">
            <div class="stats-card clickable-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title">Total Clients</h6>
                        <div class="card-value">{{ $totalClients }}</div>
                        <div class="card-change up">
                            <i class="fas fa-arrow-up"></i> 12% from last month
                        </div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 2: Active Campaigns -->
<div class="col">
    <a href="{{ route('marketing.orders.index') }}" class="text-decoration-none text-dark">
        <div class="stats-card h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="card-title">Active Campaigns</h6>
                    <div class="card-value">{{ $activeOrdersCount }}</div>
                    <div class="card-change text-muted">Click to view orders</div>
                </div>
                <div class="card-icon bg-success">
                    <i class="fas fa-bullhorn"></i>
                </div>
            </div>
        </div>
    </a>
</div>


    <!-- Card 3: Pending Invoices -->
 <div class="col">
    <a href="{{ route('marketing.orders.index') }}" class="text-decoration-none text-dark">
        <div class="stats-card h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="card-title">Pending Invoices</h6>
                    <div class="card-value">
                        ₹{{ number_format($pendingInvoiceAmount, 2) }}
                    </div>
                    <div class="card-change text-muted">Click to view orders</div>
                </div>
                <div class="card-icon bg-warning">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </a>
</div>


    <!-- Card 4: Conversion Rate -->
<div class="col">
    <a href="{{ route('marketing.orders.index') }}" class="text-decoration-none text-dark">
        <div class="stats-card h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="card-title">Conversion Rate</h6>
                    <div class="card-value">{{ $conversionRate }}%</div>
                    <div class="card-change text-muted">Click to view orders</div>
                </div>
                <div class="card-icon bg-info">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </a>
</div>


    <!-- Card 5: Approved Amount -->
    <div class="col">
        <div class="stats-card h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="card-title">Approved Amount</h6>
                    <div class="card-value">₹{{ number_format($approvedAmount, 2) }}</div>
                    <div class="card-change text-muted">Live data</div>
                </div>
                <div class="card-icon" style="background-color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-section">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="section-card">
                <h3 class="section-title">Client Growth ({{ date('Y') }})</h3>
                <div style="height: 300px; padding: 10px;">
                    <canvas id="clientGrowthChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="section-card">
                <h3 class="section-title">Recent Activities</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Activity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $order)
                                <tr>
                                    {{-- Client --}}
                                    <td>
                                        {{ $order->client->name ?? 'N/A' }}
                                    </td>

                                    {{-- Activity / Service --}}
                                    <td>
                                        {{ $order->items->first()->service->name ?? 'Service Order' }}
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @php
                                            $status = $order->order_status;
                                        @endphp

                                        <span class="status-badge
                                            @if(in_array($status, ['approved', 'in_progress', 'active']))
                                                status-in-progress
                                            @elseif($status === 'completed')
                                                status-completed
                                            @elseif($status === 'cancelled')
                                                status-cancelled
                                            @else
                                                status-pending
                                            @endif
                                        ">
                                            {{ strtoupper(str_replace('_', ' ', $status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        No recent activities found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>© {{ date('Y') }} <strong>SharpEdge Marketing</strong>. All rights reserved.</p>
</div>
@endsection

@section('styles')
<style>
    /* Chart container styling */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    /* Make sure the canvas fills its container */
    #clientGrowthChart {
        width: 100% !important;
        height: 300px !important;
    }
    
    /* Clickable card styling */
    .clickable-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .clickable-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        border-color: #3b82f6;
    }
    
    .clickable-card:hover .card-value {
        color: #3b82f6;
    }
    
    .clickable-card:hover .card-icon {
        background-color: #2563eb !important;
    }
    
    .attendance-section {
        min-width: 320px;
        text-align: right;
    }

    .attendance-info {
        background: white;
        padding: 1rem;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        min-width: 280px;
    }

    .clock-status {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .attendance-completed .alert {
        border-radius: 10px;
        border: 1px solid #93c5fd;
        background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
        min-width: 320px;
    }

    .attendance-completed .badge {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
    }

    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            align-items: flex-start;
        }

        .attendance-section {
            margin-top: 1.5rem;
            text-align: left;
            width: 100%;
        }

        .attendance-completed .alert {
            min-width: 100%;
        }
    }

    /* Responsive row for 5 cards */
    .row-cols-xl-5 > .col {
        flex: 0 0 20%;
        max-width: 20%;
    }

    /* Adjust stats cards for smaller screens */
    .stats-card {
        padding: 15px;
        border-radius: 10px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #eaeaea;
        transition: all 0.3s ease;
    }

    .card-title {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
        margin-bottom: 5px;
    }

    .card-change {
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .card-change.up {
        color: #27ae60;
    }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    @media (max-width: 1400px) {
        .row-cols-xl-5 > .col {
            flex: 0 0 20%;
            max-width: 20%;
        }
        
        .card-value {
            font-size: 1.6rem;
        }
        
        .card-icon {
            width: 42px;
            height: 42px;
            font-size: 1.1rem;
        }
    }

    @media (max-width: 1200px) {
        .row-cols-xl-5 > .col {
            flex: 0 0 20%;
            max-width: 20%;
        }
        
        .card-value {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 992px) {
        .row-cols-lg-4 > .col {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }

    @media (max-width: 768px) {
        .row-cols-md-3 > .col {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
        
        .card-value {
            font-size: 1.4rem;
        }
    }

    @media (max-width: 576px) {
        .row-cols-sm-2 > .col {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 400px) {
        .row-cols-1 > .col {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the canvas element
        const ctx = document.getElementById('clientGrowthChart').getContext('2d');
        
        // Use the actual monthly data from controller (passed from PHP)
        const monthlyData = @json($monthlyData);
        
        // Calculate cumulative data for line chart (running total)
        let cumulativeData = [];
        let runningTotal = 0;
        
        // Process the monthly data to create cumulative totals
        monthlyData.forEach(count => {
            runningTotal += count;
            cumulativeData.push(runningTotal);
        });
        
        // Create the line chart with REAL data
        const clientGrowthChart = new Chart(ctx, {
            type: 'line',
            data: {
                // Last 12 months labels
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Total Clients (Cumulative)',
                    data: cumulativeData,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'New Clients (Monthly)',
                    data: monthlyData,
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    pointBackgroundColor: '#e74a3b',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    borderDash: [5, 5],
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#333',
                            font: {
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 14
                        },
                        padding: 12,
                        cornerRadius: 6
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return value;
                            }
                        },
                        title: {
                            display: true,
                            text: 'Total Clients',
                            color: '#666',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            color: '#e74a3b',
                            font: {
                                size: 12
                            }
                        },
                        title: {
                            display: true,
                            text: 'New Clients (Monthly)',
                            color: '#e74a3b',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            }
                        },
                        title: {
                            display: true,
                            text: 'Months',
                            color: '#666',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                },
                elements: {
                    point: {
                        hoverBackgroundColor: '#2e59d9'
                    }
                }
            }
        });
    });
</script>
@endsection