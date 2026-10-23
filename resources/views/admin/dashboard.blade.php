@extends('layouts.admin')

@section('content')
 <!-- Header -->
<header class="header">
    <div class="d-flex justify-content-between align-items-center">

        {{-- LEFT : Title --}}
        <div>
            <h2>Dashboard</h2>
            <p class="text-muted mb-0">
                Welcome back, Admin. Here's what's happening with your business today.
            </p>
        </div>

        {{-- RIGHT : Profile with Attendance stacked vertically --}}
        <div class="d-flex flex-column align-items-end header-actions">

            {{-- PROFILE & NOTIFICATION ROW --}}
            <div class="d-flex align-items-center justify-content-end mb-2" style="gap: 15px;">
                
                {{-- NOTIFICATION BUTTON (LEFT SIDE OF PROFILE) --}}
                <div class="dropdown">
                    <div class="notification-icon" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <i class="bi bi-bell fs-4"></i>
                        @php
                            // Fix: Check for 'pending_approval' status in meetings
                            $pendingMeetingsCount = \App\Models\Meeting::where('status', 'pending_approval')->count();
                            
                            // Keep payment approvals as is
                            $pendingPaymentsCount = \App\Models\Order::whereHas('payments', function ($q) {
                                $q->where('status', 'submitted');
                            })->count();
                            
                            $totalPendingApprovals = $pendingMeetingsCount + $pendingPaymentsCount;

                            // Today's follow-ups (Pay Later)
                            $todayFollowUpCount = $todayFollowUpCount ?? 0;

                            // 🆕 Client Master reminders
                            $clientMasterReminderCount = $clientMasterReminderCount ?? 0;

                            $totalNotifications = $totalPendingApprovals + $todayFollowUpCount + $clientMasterReminderCount;
                        @endphp
                        @if($totalNotifications > 0)
                            <span class="notification-badge">{{ $totalNotifications }}</span>
                        @endif
                    </div>

                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 380px;">
                        <div class="notification-header">
                            <span>Notifications</span>
                            @if($totalNotifications > 0)
                                <span class="badge bg-white text-primary">{{ $totalNotifications }} total</span>
                            @endif
                        </div>
                        
                        <div class="notification-list">
                            {{-- 1. Meeting Requests Section --}}
                            @if($pendingMeetingsCount > 0)
                                <div class="notification-section-header">
                                    <i class="bi bi-calendar-check me-2"></i>Meeting Requests ({{ $pendingMeetingsCount }})
                                </div>
                                @php
                                    $pendingMeetings = \App\Models\Meeting::with(['client', 'requester'])
                                        ->where('status', 'pending_approval')
                                        ->latest()
                                        ->limit(5)
                                        ->get();
                                @endphp
                                
                                @foreach($pendingMeetings as $meeting)
                                    <div class="notification-item unread" id="notification-{{ $meeting->id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="bi bi-calendar-check fs-5 text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong>{{ $meeting->client->name ?? $meeting->manual_client_name ?? 'Unknown Client' }}</strong>
                                                <p class="mb-0 small text-muted">
                                                    {{ Str::limit($meeting->purpose ?? 'Meeting request', 40) }}
                                                </p>
                                                <div class="notification-time">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $meeting->created_at->diffForHumans() }}
                                                    <span class="badge bg-warning ms-2">Pending Approval</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            {{-- 2. Payment Approvals Section --}}
                            @if($pendingPaymentsCount > 0)
                                <div class="notification-section-header mt-2">
                                    <i class="bi bi-cash-stack me-2"></i>Payment Approvals ({{ $pendingPaymentsCount }})
                                </div>
                                @php
                                    $pendingPayments = \App\Models\Order::with(['client'])
                                        ->whereHas('payments', function ($q) {
                                            $q->where('status', 'submitted');
                                        })
                                        ->latest()
                                        ->limit(3)
                                        ->get();
                                @endphp
                                
                                @foreach($pendingPayments as $order)
                                    <div class="notification-item unread">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="bi bi-cash-stack fs-5 text-success"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong>{{ $order->client->name ?? 'Unknown Client' }}</strong>
                                                <p class="mb-0 small text-muted">
                                                    Payment pending approval
                                                </p>
                                                <div class="notification-time">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $order->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            {{-- 3. Today's Follow-ups (Pay Later) --}}
                            @if(isset($todayFollowUps) && $todayFollowUps->count() > 0)
                                <div class="notification-section-header mt-2">
                                    <i class="bi bi-bell me-2"></i>Today's Follow-ups ({{ $todayFollowUps->count() }})
                                </div>
                                @foreach($todayFollowUps as $followUp)
                                    <a href="{{ route('admin.payments.index') }}" class="dropdown-item notification-item unread" style="text-decoration: none; color: inherit;">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="bi bi-clock-history fs-5 text-warning"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong>{{ $followUp->payment?->client?->name ?? 'Unknown Client' }}</strong>
                                                <p class="mb-0 small text-muted">
                                                    Follow-up is due today
                                                </p>
                                                @if($followUp->payment)
                                                    <div class="small text-muted">
                                                        Amount: ₹{{ number_format($followUp->payment->amount, 2) }}
                                                    </div>
                                                @endif
                                                <div class="notification-time">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    Next: {{ $followUp->next_follow_up_date->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif

                            {{-- 🆕 4. Client Master Reminders (Case & Compliance) --}}
                            @if(isset($clientMasterReminders) && count($clientMasterReminders) > 0)
                                <div class="notification-section-header mt-2">
                                    <i class="bi bi-folder2-open me-2"></i>Client Master Reminders ({{ count($clientMasterReminders) }})
                                </div>
                                @foreach($clientMasterReminders as $reminder)
                                    @php
                                        $statusClass = $reminder['status'] == 'overdue' ? 'bg-danger' : ($reminder['status'] == 'today' ? 'bg-warning' : 'bg-info');
                                        $statusText = $reminder['status'] == 'overdue' ? 'Overdue' : ($reminder['status'] == 'today' ? 'Due Today' : 'Upcoming');
                                        $iconClass = $reminder['status'] == 'overdue' ? 'bi-exclamation-circle' : ($reminder['status'] == 'today' ? 'bi-calendar-event' : 'bi-clock');
                                    @endphp
                                    <a href="{{ $reminder['url'] }}" class="dropdown-item notification-item unread" style="text-decoration: none; color: inherit;">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="bi {{ $iconClass }} fs-5 text-{{ $reminder['status'] == 'overdue' ? 'danger' : ($reminder['status'] == 'today' ? 'warning' : 'primary') }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong>{{ $reminder['client_name'] }}</strong>
                                                <p class="mb-0 small text-muted">
                                                    {{ $reminder['type'] }}: {{ $reminder['title'] }}
                                                    @if($reminder['period'])
                                                        ({{ $reminder['period'] }})
                                                    @endif
                                                </p>
                                                <div class="notification-time">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $reminder['target_date'] }}
                                                    <span class="badge {{ $statusClass }} ms-2">{{ $statusText }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif

                            {{-- No Notifications --}}
                            @if($pendingMeetingsCount == 0 && $pendingPaymentsCount == 0 && ($todayFollowUps->count() ?? 0) == 0 && (count($clientMasterReminders ?? []) == 0))
                                <div class="text-center py-4">
                                    <i class="bi bi-bell-slash fs-1 text-muted"></i>
                                    <p class="text-muted mt-2 mb-0">No pending notifications</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="notification-footer d-flex justify-content-between">
                            <a href="{{ route('admin.meetings.index') }}?status=pending_approval">View Meetings</a>
                            <a href="{{ route('admin.orders.pending') }}">View Payments</a>
                            @if(isset($todayFollowUps) && $todayFollowUps->count() > 0)
                                <a href="{{ route('admin.payments.index') }}?payment_tracking_status=upcoming">View Follow-ups</a>
                            @endif
                            @if(isset($clientMasterReminders) && count($clientMasterReminders) > 0)
                                <a href="{{ route('admin.client-master.index') }}">View Clients</a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- PROFILE DROPDOWN --}}
                @php
                    $user = Auth::user();
                    $userName = $user->name ?? 'User';
                    $firstLetter = strtoupper(substr($userName, 0, 1));
                    $roleName = $user->role->name ?? 'User';

                    $profileImage = $user->profile_image ?? null;
                    $profileImagePath = $profileImage
                        ? asset('storage/Staff/profile_images/' . $profileImage)
                        : null;
                @endphp

                <div class="dropdown">
                    <div class="d-flex align-items-center dropdown-toggle"
                         data-bs-toggle="dropdown"
                         style="cursor: pointer;">

                        <div class="me-2">
                            @if($profileImage)
                                <img src="{{ $profileImagePath }}"
                                     class="rounded-circle"
                                     style="width:45px;height:45px;object-fit:cover;">
                            @else
                                <div class="user-avatar header-user rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:45px;height:45px;">
                                    <strong>{{ $firstLetter }}</strong>
                                </div>
                            @endif
                        </div>

                        <div style="line-height:1.1">
                            <p class="mb-0 fw-bold">{{ $userName }}</p>
                            <small class="text-muted">{{ ucfirst($roleName) }}</small>
                        </div>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end mt-2 shadow">
                        <li class="px-3 py-2 text-muted small">
                            Signed in as <strong>{{ ucfirst($roleName) }}</strong>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ATTENDANCE SECTION --}}
            <div class="attendance-section text-end">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-2">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-2">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($attendance === null)
                    <form method="POST" action="{{ route('admin.attendance.clockIn') }}">
                        @csrf
                        <button class="btn btn-success shadow-sm">
                            <i class="bi bi-clock me-1"></i> Clock In
                        </button>
                        <small class="d-block text-muted mt-1">
                            Click to start your workday
                        </small>
                    </form>

                @elseif($attendance->clock_out_time === null)
                    <div class="mb-2">
                        <small class="text-muted d-block">Clocked In</small>
                        <strong>{{ date('h:i A', strtotime($attendance->clock_in_time)) }}</strong>
                    </div>

                    <form method="POST" action="{{ route('admin.attendance.clockOut') }}">
                        @csrf
                        <button class="btn btn-warning shadow-sm">
                            <i class="bi bi-box-arrow-right me-1"></i> Clock Out
                        </button>
                    </form>

                @else
                    <div class="alert alert-info mb-0">
                        <strong>Attendance Completed</strong><br>
                        <small>
                            {{ date('h:i A', strtotime($attendance->clock_in_time)) }}
                            →
                            {{ date('h:i A', strtotime($attendance->clock_out_time)) }}
                        </small>
                    </div>
                @endif
            </div>

        </div>
    </div>
</header>

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <a href="{{ route('admin.services.index') }}" class="card-link">
                <div class="stats-card clickable-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-muted mb-0">TOTAL SERVICES</h6>
                        <div class="card-icon bg-primary">
                            <i class="bi bi-briefcase"></i>
                        </div>
                    </div>
                    <h3 class="card-value">{{ $totalServices }}</h3>
                    <div class="card-change {{ $serviceIncrease >= 0 ? 'up' : 'down' }}">
                        @if($serviceIncrease >= 0)
                            <i class="bi bi-arrow-up me-1"></i>
                        @else
                            <i class="bi bi-arrow-down me-1"></i>
                        @endif
                        <span>{{ abs($serviceIncrease) }} from last month</span>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <a href="{{ route('admin.clients.index') }}" class="card-link">
                <div class="stats-card clickable-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-muted mb-0">ACTIVE CLIENTS</h6>
                        <div class="card-icon bg-success">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <h3 class="card-value">{{ $activeClients }}</h3>
                    <div class="card-change {{ $clientIncrease >= 0 ? 'up' : 'down' }}">
                        @if($clientIncrease >= 0)
                            <i class="bi bi-arrow-up me-1"></i>
                        @else
                            <i class="bi bi-arrow-down me-1"></i>
                        @endif
                        <span>{{ abs($clientIncrease) }} from last month</span>
                    </div>
                </div>
            </a>
        </div>
        
<div class="col-xl-3 col-lg-6 col-md-6">
    <a href="{{ route('admin.tasks.submitted') }}" class="card-link">
        <div class="stats-card clickable-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="card-title text-muted mb-0">SUBMITTED TASKS</h6>
                <div class="card-icon bg-info text-white">
                    <i class="bi bi-send-check"></i>
                </div>
            </div>

            <h3 class="card-value">
                {{ $verifiedTasks }}
            </h3>

            <div class="card-change text-muted small">
                Tasks awaiting verification
            </div>
        </div>
    </a>
</div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <!-- MONTHLY REVENUE - Now Dynamic -->
            <a href="{{ route('admin.orders.pending') }}" class="card-link">
                <div class="stats-card clickable-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-muted mb-0">OVERALL REVENUE</h6>
                        <div class="card-icon bg-info">
                            <i class="bi bi-currency-rupee"></i>
                        </div>
                    </div>
                    <h3 class="card-value">{{ number_format($currentMonthRevenue, 2) }}</h3>
                    <div class="card-change {{ $revenueIncrease >= 0 ? 'up' : 'down' }}">
                        @if($revenueIncrease >= 0)
                            <i class="bi bi-arrow-up me-1"></i>
                        @else
                            <i class="bi bi-arrow-down me-1"></i>
                        @endif
                        <span>{{ number_format(abs($revenueIncrease), 2) }} from last month</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content-section">
        <div class="row g-4">
            <!-- Revenue Overview Chart -->
            <div class="col-lg-8">
                <div class="section-card">
                    <h4 class="section-title">Revenue Overview</h4>
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="col-lg-4">
                <div class="section-card">
                    <h4 class="section-title">Quick Stats</h4>
                    <div class="quick-stats">
                        <div class="stat-item">
                            <div class="stat-icon bg-primary">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Total Approved Payments</h6>
                                <h4>{{ $totalApprovedPayments }}</h4>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon bg-warning">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Pending Approvals</h6>
                                <h4>{{ $pendingPaymentsCount }}</h4>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon bg-success">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="stat-content">
                                <h6>Active Payment Clients</h6>
                                <h4>{{ $uniqueClientsWithPayments }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Latest Meetings Table -->
<div class="mt-4">
    <div class="section-card">
        <h4 class="section-title">
            <i class="bi bi-calendar-check me-2"></i>
            Latest Meeting Requests
            @php
                $pendingMeetingsCount = \App\Models\Meeting::whereIn('status', ['availability_requested', 'availability_provided'])->count();
            @endphp
            @if($pendingMeetingsCount > 0)
                <span class="badge bg-warning ms-2">{{ $pendingMeetingsCount }} pending</span>
            @endif
        </h4>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Purpose</th>
                        <th>Requested By</th>
                        <th>Status</th>
                        <th>Requested On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $latestMeetings = \App\Models\Meeting::with(['client', 'requester'])
                            ->latest()
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @forelse($latestMeetings as $meeting)
                        <tr>
                            <td>
                                <strong>{{ $meeting->client->name ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ Str::limit($meeting->purpose ?? 'Meeting Request', 30) }}</td>
                            <td>{{ $meeting->requester->name ?? 'N/A' }}</td>
                            <td>
                                @if($meeting->status == 'availability_requested')
                                    <span class="status-badge status-availability-requested">
                                        <i class="bi bi-clock me-1"></i>Awaiting Availability
                                    </span>
                                @elseif($meeting->status == 'availability_provided')
                                    <span class="status-badge status-availability-provided">
                                        <i class="bi bi-check-circle me-1"></i>Availability Provided
                                    </span>
                                @elseif($meeting->status == 'scheduled')
                                    <span class="status-badge status-scheduled">
                                        <i class="bi bi-calendar-check me-1"></i>Scheduled
                                    </span>
                                @elseif($meeting->status == 'completed')
                                    <span class="status-badge status-completed">
                                        <i class="bi bi-check2-all me-1"></i>Completed
                                    </span>
                                @elseif($meeting->status == 'cancelled')
                                    <span class="status-badge status-cancelled">
                                        <i class="bi bi-x-circle me-1"></i>Cancelled
                                    </span>
                                @else
                                    <span class="status-badge status-pending">
                                        {{ ucfirst(str_replace('_', ' ', $meeting->status)) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <i class="bi bi-calendar me-1"></i>
                                {{ $meeting->created_at->format('M d, Y') }}
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $meeting->created_at->format('h:i A') }}
                                </small>
                            </td>
                            <td>
                                <a href="{{ route('admin.meetings.index', ['view' => $meeting->id]) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">No meeting requests found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="text-end mt-3">
            <a href="{{ route('admin.meetings.index') }}" class="btn btn-sm btn-link">
                View All Meetings <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

    <!-- Recent Tasks Table -->
   <div class="mt-4">
    <div class="section-card">
        <h4 class="section-title">Recent Tasks</h4>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Service</th>
                        <th>Client</th>
                        <th>Assigned To</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <!--<th>Actions</th>-->
                    </tr>
                </thead>

                {{-- 🔴 NOTHING between thead and tbody --}}

                <tbody>
                    @forelse($recentTasks as $task)
                        <tr>
                            <td class="fw-bold">{{ $task->task_code }}</td>
                            <td>{{ $task->service?->service_name ?? '-' }}</td>
                            <td>{{ $task->order?->client?->name ?? '-' }}</td>
                            <td>{{ $task->assignedStaff?->name ?? 'Unassigned' }}</td>
                            <td>{{ $task->due_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>
                                <span class="status-badge status-pending">
                                    {{ ucfirst(str_replace('_',' ',$task->status)) }}
                                </span>
                            </td>
                            <!--<td>-->
                            <!--    <button class="btn btn-sm btn-outline-primary me-1" disabled>-->
                            <!--        <i class="bi bi-eye"></i>-->
                            <!--    </button>-->
                            <!--    <button class="btn btn-sm btn-outline-success" disabled>-->
                            <!--        <i class="bi bi-pencil"></i>-->
                            <!--    </button>-->
                            <!--</td>-->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No recent tasks found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>

    <!-- Footer -->
    <footer class="footer">
        <p class="mb-0">© {{ date('Y') }} <strong>SharpEdge</strong>. All rights reserved. | Admin Dashboard v2.4</p>
    </footer>
@endsection

@push('styles')
<style>
    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .clickable-card {
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .clickable-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .clickable-card:hover .card-icon {
        transform: scale(1.1);
        transition: transform 0.3s ease;
    }
    
    .quick-stats {
        padding: 15px 0;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }
    
    .stat-item:last-child {
        border-bottom: none;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
    }
    
    .stat-content h6 {
        margin: 0;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .stat-content h4 {
        margin: 5px 0 0 0;
        color: #333;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    /* Updated header styles for vertical stacking */
    .header-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }
    
    .attendance-section {
        text-align: right;
        min-width: 200px;
    }
    
    /* Keep existing responsive styles */
    @media (max-width: 768px) {
        .header > div {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            align-items: flex-start;
            width: 100%;
            margin-top: 15px;
        }
        
        .attendance-section {
            text-align: left;
            min-width: unset;
            width: 100%;
        }
        
        .attendance-section small {
            line-height: 1.2;
        }
    }
    
    /* Notification Dropdown Enhancements */
    .notification-section-header {
        padding: 8px 15px;
        background-color: #f8fafc;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
    }

    .notification-icon {
        position: relative;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .notification-icon:hover {
        background-color: #f1f5f9;
    }

    .notification-icon i {
        color: #475569;
    }

    .notification-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 10px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        border: 2px solid white;
    }

    .notification-dropdown {
        width: 380px;
        max-height: 500px;
        overflow-y: auto;
        padding: 0;
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-radius: 12px;
    }

    .notification-header {
        background: linear-gradient(135deg, #1e293b, #334155);
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 12px 12px 0 0;
    }

    .notification-header .badge {
        background: rgba(255,255,255,0.2) !important;
        color: white !important;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
    }

    .notification-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s;
        cursor: pointer;
    }

    .notification-item:hover {
        background-color: #f8fafc;
        transform: translateX(2px);
    }

    .notification-item.unread {
        background-color: #f0f9ff;
        border-left: 3px solid #3b82f6;
    }

    .notification-item.unread:hover {
        background-color: #e0f2fe;
    }

    .notification-time {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .notification-footer {
        padding: 12px 15px;
        background-color: #f8fafc;
        border-top: 1px solid #e2e8f0;
        border-radius: 0 0 12px 12px;
    }

    .notification-footer a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        font-size: 13px;
        transition: all 0.2s;
    }

    .notification-footer a:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    /* Profile dropdown enhancements */
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-radius: 10px;
        padding: 8px 0;
    }

    .dropdown-item {
        padding: 8px 20px;
        font-size: 14px;
        transition: all 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f1f5f9;
    }

    .dropdown-item.text-danger:hover {
        background-color: #fee2e2;
        color: #dc2626 !important;
    }

    .user-avatar {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        color: white;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ================================
       Clickable cards navigation
    ================================ */
    document.querySelectorAll('.clickable-card').forEach(card => {
        card.addEventListener('click', function () {
            const link = this.closest('.card-link');
            if (link && link.href) {
                window.location.href = link.href;
            }
        });
    });

    /* ================================
       Revenue Overview Chart
    ================================ */
    const ctx = document.getElementById('revenueChart').getContext('2d');

    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($revenueData['months']),
            datasets: [{
                label: 'Revenue (₹)',
                data: @json($revenueData['revenues']),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function (context) {
                            return 'Revenue: ₹' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return '₹' + value.toFixed(2);
                        }
                    },
                    title: {
                        display: true,
                        text: 'Revenue (₹)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                }
            },

            interaction: {
                intersect: false,
                mode: 'nearest'
            }
        }
    });

});
</script>
@endpush