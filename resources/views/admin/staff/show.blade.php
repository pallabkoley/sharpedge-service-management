@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Staff Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staff Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $staff->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Staff
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Staff Profile Card -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($staff->profile_image)
                            <img src="{{ asset('storage/' . $staff->profile_image) }}" alt="Profile" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                <i class="bi bi-person-fill text-white" style="font-size: 80px;"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="mb-1">{{ $staff->name }}</h4>
                    <p class="text-muted mb-2">
                        <span class="badge bg-info">{{ $staff->role->name ?? 'N/A' }}</span>
                    </p>
                    <p class="text-muted">
                        <i class="bi bi-envelope"></i> {{ $staff->email }}<br>
                        <i class="bi bi-telephone"></i> {{ $staff->phone ?? 'Not provided' }}
                    </p>
                    <hr>
                    <div class="row text-start">
                        <div class="col-6">
                            <small class="text-muted">Staff Code</small><br>
                            <strong>{{ $staff->staff_code }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Status</small><br>
                            <span class="badge {{ $staff->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($staff->status) }}
                            </span>
                        </div>
                        <div class="col-6 mt-2">
                            <small class="text-muted">Department</small><br>
                            <strong>{{ $staff->department ?? '—' }}</strong>
                        </div>
                        <div class="col-6 mt-2">
                            <small class="text-muted">Designation</small><br>
                            <strong>{{ $staff->designation ?? '—' }}</strong>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted">Date Joined</small><br>
                            <strong>{{ $staff->date_joined ? \Carbon\Carbon::parse($staff->date_joined)->format('d M, Y') : '—' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance & Activity Tabs -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <ul class="nav nav-tabs card-header-tabs" id="staffTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button" role="tab">Marketing Performance</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">Tasks</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">Attendance</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-4 col-lg-3">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $totalOrders }}</h3>
                                                <p class="text-muted mb-0">Total Orders</p>
                                            </div>
                                            <div class="stats-icon bg-primary-light">
                                                <i class="bi bi-cart-check fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">₹{{ number_format($totalRevenue, 2) }}</h3>
                                                <p class="text-muted mb-0">Total Revenue</p>
                                            </div>
                                            <div class="stats-icon bg-success-light">
                                                <i class="bi bi-currency-rupee fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $totalTasks }}</h3>
                                                <p class="text-muted mb-0">Total Tasks</p>
                                            </div>
                                            <div class="stats-icon bg-warning-light">
                                                <i class="bi bi-list-check fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $totalHours }} hrs</h3>
                                                <p class="text-muted mb-0">Work Hours</p>
                                            </div>
                                            <div class="stats-icon bg-info-light">
                                                <i class="bi bi-clock fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $totalDays }}</h3>
                                                <p class="text-muted mb-0">Attendance Days</p>
                                            </div>
                                            <div class="stats-icon bg-purple-light">
                                                <i class="bi bi-calendar-check fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $overdueTasks }}</h3>
                                                <p class="text-muted mb-0">Overdue Tasks</p>
                                            </div>
                                            <div class="stats-icon bg-danger-light">
                                                <i class="bi bi-exclamation-triangle fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Tab (Marketing) -->
                        <div class="tab-pane fade" id="performance" role="tabpanel">
                            <div class="row mb-4 g-4">
                                <div class="col-md-3">
                                    <div class="stats-card-modern bg-primary text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $totalOrders }}</h3>
                                                <p class="mb-0">Total Orders</p>
                                            </div>
                                            <i class="bi bi-cart-check fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-card-modern bg-success text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">₹{{ number_format($totalRevenue, 2) }}</h3>
                                                <p class="mb-0">Revenue</p>
                                            </div>
                                            <i class="bi bi-currency-rupee fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-card-modern bg-info text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">₹{{ number_format($totalCollected, 2) }}</h3>
                                                <p class="mb-0">Collected</p>
                                            </div>
                                            <i class="bi bi-wallet2 fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-card-modern bg-warning text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $totalClients }}</h3>
                                                <p class="mb-0">Unique Clients</p>
                                            </div>
                                            <i class="bi bi-people fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5>Order History</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <th>Order ID</th>
                                            <th>Client</th>
                                            <th>Service</th>
                                            <th>Amount</th>
                                            <th>Paid</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $order)
<tr>
    <td>{{ $order->order_id }}</td>   <!-- ✅ FIXED -->
    <td>{{ $order->client->name ?? 'N/A' }}</td>
    <td>{{ $order->service->name ?? 'N/A' }}</td>
    <td>₹{{ number_format($order->final_amount, 2) }}</td>
    <td>₹{{ number_format($order->paid_amount, 2) }}</td>
    <td>
        <span class="badge bg-secondary">
            {{ $order->order_status }}   <!-- ⚠️ also fix this -->
        </span>
    </td>
</tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No orders found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tasks Tab -->
                        <div class="tab-pane fade" id="tasks" role="tabpanel">
                            <div class="row mb-4 g-4">
                                <div class="col-md-4">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $totalTasks }}</h3>
                                                <p class="text-muted mb-0">Total Tasks</p>
                                            </div>
                                            <div class="stats-icon bg-primary-light">
                                                <i class="bi bi-list-check fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0 text-success">{{ $completedTasks }}</h3>
                                                <p class="text-muted mb-0">Completed</p>
                                            </div>
                                            <div class="stats-icon bg-success-light">
                                                <i class="bi bi-check-circle fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0 text-danger">{{ $overdueTasks }}</h3>
                                                <p class="text-muted mb-0">Overdue</p>
                                            </div>
                                            <div class="stats-icon bg-danger-light">
                                                <i class="bi bi-exclamation-circle fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5>Task List</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Task ID</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th>Priority</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tasks as $task)
                                        <tr>
                                            <td>{{ $task->id }}</td>
                                            <td>{{ $task->task_name ?? '—' }}</td>
                                            <td>
                                                @php
                                                    $statusClass = '';
                                                    switch($task->status) {
                                                        case 'verified': $statusClass = 'bg-success'; break;
                                                        case 'in-progress': $statusClass = 'bg-primary'; break;
                                                        case 'pending': $statusClass = 'bg-warning'; break;
                                                        case 'overdue': $statusClass = 'bg-danger'; break;
                                                        default: $statusClass = 'bg-secondary';
                                                    }
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ ucfirst($task->status) }}</span>
                                            </td>
                                            <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '—' }}</td>
                                            <td><span class="badge bg-info">{{ $task->priority ?? 'Normal' }}</span></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No tasks assigned</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Attendance Tab -->
                        <div class="tab-pane fade" id="attendance" role="tabpanel">
                            <div class="row mb-4 g-4">
                                <div class="col-md-4">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $totalHours }}</h3>
                                                <p class="text-muted mb-0">Total Hours</p>
                                            </div>
                                            <div class="stats-icon bg-info-light">
                                                <i class="bi bi-hourglass-split fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $totalDays }}</h3>
                                                <p class="text-muted mb-0">Days Present</p>
                                            </div>
                                            <div class="stats-icon bg-purple-light">
                                                <i class="bi bi-calendar-week fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card-modern">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="mb-0">{{ $avgHours }}</h3>
                                                <p class="text-muted mb-0">Avg Hours/Day</p>
                                            </div>
                                            <div class="stats-icon bg-warning-light">
                                                <i class="bi bi-graph-up fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($todayAttendance)
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <strong>Today's Attendance:</strong>
                                    @if($todayAttendance->clock_in_time)
                                        Clock In: {{ \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A') }}
                                    @endif
                                    @if($todayAttendance->clock_out_time)
                                        | Clock Out: {{ \Carbon\Carbon::parse($todayAttendance->clock_out_time)->format('h:i A') }}
                                    @endif
                                    @if($todayAttendance->total_work_seconds)
                                        | Total: {{ floor($todayAttendance->total_work_seconds / 3600) }} hrs {{ floor(($todayAttendance->total_work_seconds % 3600) / 60) }} mins
                                    @endif
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <h5>Attendance Logs</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Clock In</th>
                                            <th>Clock Out</th>
                                            <th>Total Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendanceLogs as $log)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($log->date)->format('d M Y') }}</td>
                                            <td>{{ $log->clock_in_time ? \Carbon\Carbon::parse($log->clock_in_time)->format('h:i A') : '—' }}</td>
                                            <td>{{ $log->clock_out_time ? \Carbon\Carbon::parse($log->clock_out_time)->format('h:i A') : '—' }}</td>
                                            <td>{{ floor($log->total_work_seconds / 3600) }} hrs {{ floor(($log->total_work_seconds % 3600) / 60) }} mins</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No attendance records</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Modern Stats Cards */
.stats-card-modern {
    background: #ffffff;
    border-radius: 20px;
    padding: 1.25rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
    border: 1px solid rgba(0,0,0,0.03);
}

.stats-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.stats-card-modern h3 {
    font-weight: 700;
    font-size: 1.75rem;
    margin-bottom: 0.25rem;
}

.stats-card-modern p {
    font-size: 0.85rem;
    font-weight: 500;
    color: #6c757d;
}

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-light {
    background-color: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}
.bg-success-light {
    background-color: rgba(16, 185, 129, 0.1);
    color: #10b981;
}
.bg-warning-light {
    background-color: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}
.bg-info-light {
    background-color: rgba(6, 182, 212, 0.1);
    color: #06b6d4;
}
.bg-danger-light {
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}
.bg-purple-light {
    background-color: rgba(139, 92, 246, 0.1);
    color: #8b5cf6;
}

/* Tab Styling */
.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 0.75rem 1.25rem;
    margin-right: 0.5rem;
    border-radius: 40px;
    transition: all 0.2s;
}
.nav-tabs .nav-link:hover {
    color: #3b82f6;
    background-color: rgba(59,130,246,0.1);
}
.nav-tabs .nav-link.active {
    color: #3b82f6;
    background-color: rgba(59,130,246,0.1);
    border-bottom: 2px solid #3b82f6;
}
.tab-pane {
    animation: fadeIn 0.2s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Table Enhancements */
.table thead th {
    font-weight: 600;
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.table-hover tbody tr:hover {
    background-color: rgba(59,130,246,0.05);
}
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,0.01);
}
.badge {
    font-weight: 500;
    padding: 0.4em 0.8em;
}
</style>
@endpush