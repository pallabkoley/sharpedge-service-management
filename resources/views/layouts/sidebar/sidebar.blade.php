<style>
.sidebar {
    height: 100vh;
    overflow-y: auto;
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    background-color: #1f2937;
    color: white;
    display: flex;
    flex-direction: column;
}

.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-track {
    background: #374151;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #6b7280;
    border-radius: 3px;
}

.logo-container {
    padding: 20px 15px;
    border-bottom: 1px solid #374151;
    flex-shrink: 0;
}

.sharpedge-logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: white;
}

.logo-icon {
    width: 36px;
    height: 36px;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    font-weight: bold;
    font-size: 20px;
}

.logo-text {
    font-size: 20px;
    font-weight: bold;
}

.logo-text span {
    color: #93c5fd;
}

.nav {
    padding: 15px 0;
    flex-grow: 1;
}

.nav-item {
    margin: 0;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #d1d5db;
    text-decoration: none;
    transition: all 0.2s;
    border-left: 4px solid transparent;
}

.nav-link i {
    margin-right: 12px;
    font-size: 18px;
    width: 24px;
    text-align: center;
}

.nav-link:hover {
    background-color: #374151;
    color: white;
    border-left-color: #3b82f6;
}

.nav-link.active {
    background-color: #374151;
    color: white;
    border-left-color: #3b82f6;
    font-weight: 500;
}

.sidebar-divider {
    height: 1px;
    background-color: #374151;
    margin: 10px 20px;
    border: none;
}

.section-header {
    padding: 8px 20px;
    color: #9ca3af;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 5px;
}

.badge {
    padding: 3px 8px;
    font-size: 11px;
    border-radius: 10px;
    margin-left: auto;
}

.logout-section {
    padding: 15px 0;
    border-top: 1px solid #374151;
    flex-shrink: 0;
}

.logout-form {
    margin: 0;
    padding: 0;
}

.logout-button {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 12px 20px;
    background: none;
    border: none;
    color: #d1d5db;
    text-align: left;
    cursor: pointer;
    transition: all 0.2s;
    border-left: 4px solid transparent;
    text-decoration: none;
    font-size: 16px;
}

.logout-button:hover {
    background-color: #374151;
    color: white;
    border-left-color: #ef4444;
}

.logout-button i {
    margin-right: 12px;
    font-size: 18px;
    width: 24px;
    text-align: center;
}

/* Make sure main content doesn't go under sidebar */
.main-content {
    margin-left: 250px;
    padding: 20px;
}
</style>

@php
    // Support tickets needing admin attention
    $openTicketsCount = \App\Models\SupportTicket::whereIn('status', [
        'open',
        'assigned',
        'in_progress',
        'pending',
        'reopened'
    ])->count();

    // Meetings pending admin approval
    $pendingMeetingsCount = \App\Models\Meeting::where('status', 'pending_approval')->count();

    // Workflow Health records needing attention
    $attentionWorkflowCount = \App\Models\ServiceWorkflowHealth::where('health_status', 'ATTENTION')->count();

    // Leave requests pending approval
    $pendingLeaveCount = \App\Models\LeaveRequest::where('status', 'pending')->count();

    // Submitted tasks awaiting verification
    $pendingTaskCount = \App\Models\Task::where('status', 'submitted')->count();

    // Uploaded documents pending verification
    $pendingDocVerifyCount = \App\Models\OrderDocument::where('status', 'UPLOADED')->count();

    // Payments pending approval (from orders)
    $pendingPaymentCount = \App\Models\Order::paymentApprovalCount();

    // ===== NEW: Client Master reminders =====
    // Case Follow-up reminders (open cases with next_follow_up_date <= today + 10 days)
    $clientCaseReminderCount = \App\Models\ClientCase::where('status', '!=', 'Closed')
        ->whereNotNull('next_follow_up_date')
        ->whereDate('next_follow_up_date', '<=', now()->addDays(10))
        ->count();

    // Compliance Due reminders (non-completed compliances with due_date <= today + 10 days)
    $clientComplianceReminderCount = \App\Models\ClientCompliance::where('status', '!=', 'completed')
        ->whereNotNull('due_date')
        ->whereDate('due_date', '<=', now()->addDays(10))
        ->count();

    // Total reminder count for Client Master badge
    $clientMasterReminderCount = $clientCaseReminderCount + $clientComplianceReminderCount;
@endphp

<nav class="sidebar" id="sidebar">
    <div class="logo-container text-center">
        <a href="#" class="sharpedge-logo d-flex flex-column align-items-center">
            <img src="{{ asset('storage/SRP_LOGO/WhatsApp Image 2026-04-13 at 18.39.41.jpeg') }}" 
                 alt="Logo" 
                 style="height:100px;width:200px;">
            <div class="logo-text mt-2">
                The <span>SharpEdge</span> India
            </div>
        </a>
    </div>
    
    <ul class="nav flex-column">
        <!-- Section 1: Overview -->
        <div class="section-header">Overview</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <!-- NEW: Service Selling Analysis Link -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.service.selling.analysis') ? 'active' : '' }}"
               href="{{ route('admin.service.selling.analysis') }}">
                <i class="bi bi-graph-up"></i>
                <span>Service Selling Analysis</span>
            </a>
        </li>
        
        <hr class="sidebar-divider">
        
        <!-- Section 2: Service Configuration -->
        <div class="section-header">Service Configuration</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
               href="{{ route('admin.categories.index') }}">
                <i class="bi bi-grid"></i>
                <span>Service Categories</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}"
               href="{{ route('admin.services.index') }}">
                <i class="bi bi-briefcase"></i>
                <span>Service Master</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"
               href="{{ route('admin.coupons.index') }}">
                <i class="bi bi-tag"></i>
                <span>Discount Coupons</span>
            </a>
        </li>
        
        <hr class="sidebar-divider">
        
        <!-- Section 3: User Management -->
        <div class="section-header">User Management</div>
        
        <!-- ✅ Existing Client Management -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}"
               href="{{ route('admin.clients.index') }}">
                <i class="bi bi-people"></i>
                <span>Client Management</span>
            </a>
        </li>
        
        <!-- ✅ UPDATED: Client Master with badge -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.client-master.*') ? 'active' : '' }}"
               href="{{ route('admin.client-master.index') }}">
                <i class="bi bi-person-badge"></i>
                <span>Client Master</span>
                @if($clientMasterReminderCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $clientMasterReminderCount }}</span>
                @endif
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}"
               href="{{ route('admin.staff.index') }}">
                <i class="bi bi-gear"></i>
                <span>Staff Management</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.attendance.report') ? 'active' : '' }}"
               href="{{ route('admin.attendance.report') }}">
                <i class="bi bi-calendar2-week"></i>
                <span>Attendance Report</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        
        <!-- Section 4: Document & Workflow Management -->
        <div class="section-header">Document & Workflow Management</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.document-requirements.*') ? 'active' : '' }}"
               href="{{ route('admin.document-requirements.index') }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Document Requirements</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.work-processes.*') ? 'active' : '' }}"
               href="{{ route('admin.work-processes.index') }}">
                <i class="bi bi-check2-square"></i>
                <span>Work Processes</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.documents.upload.*') ? 'active' : '' }}"
               href="{{ route('admin.documents.upload.index') }}">
                <i class="bi bi-upload"></i>
                <span>Document Upload</span>
            </a>
        </li>
        
        <hr class="sidebar-divider">
        
        <!-- Section 5: Task & Approval Flow -->
        <div class="section-header">Task & Approval Flow</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.orders.pending') ? 'active' : '' }}"
               href="{{ route('admin.orders.pending') }}">
                <i class="bi bi-currency-exchange"></i>
                <span>Payment Approval</span>
                @if($pendingPaymentCount > 0)
                <span class="badge bg-danger ms-auto">{{ $pendingPaymentCount }}</span>
                @endif
            </a>
        </li>

        <!-- ✅ NEW: Payment Records -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}"
               href="{{ route('admin.payments.index') }}">
                <i class="bi bi-cash-stack"></i>
                <span>Payment Records</span>
                @if(isset($todayFollowUpCount) && $todayFollowUpCount > 0)
                    <span class="badge bg-danger ms-auto">
                        {{ $todayFollowUpCount }}
                    </span>
                @endif
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.leave-approvals.*') ? 'active' : '' }}"
               href="{{ route('admin.leave-approvals.index') }}">
                <i class="bi bi-calendar2-check"></i>
                <span>Leave Approval</span>
                @if($pendingLeaveCount > 0)
                    <span class="badge bg-warning ms-auto">{{ $pendingLeaveCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.tasks.assign.index') ? 'active' : '' }}"
               href="{{ route('admin.tasks.assign.index') }}">
                <i class="bi bi-person-check"></i>
                <span>Task Assignment</span>
            </a>
        </li>
        
        <!-- Task Verification Link -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.tasks.submitted') || request()->routeIs('admin.tasks.verify.*') ? 'active' : '' }}"
               href="{{ route('admin.tasks.submitted') }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Task Verification</span>
                @if($pendingTaskCount > 0)
                <span class="badge bg-warning ms-auto">{{ $pendingTaskCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.document-verification.*') ? 'active' : '' }}"
               href="{{ route('admin.document-verification.index') }}">
                <i class="bi bi-file-earmark-check"></i>
                <span>Document Verification</span>
                @if($pendingDocVerifyCount > 0)
                    <span class="badge bg-warning ms-auto">{{ $pendingDocVerifyCount }}</span>
                @endif
            </a>
        </li>

        <!-- NEW: Workflow Health -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.workflow-health.*') ? 'active' : '' }}"
               href="{{ route('admin.workflow-health.index') }}">
                <i class="bi bi-activity"></i>
                <span>Workflow Health</span>
                @if($attentionWorkflowCount > 0)
                    <span class="badge bg-warning ms-auto">{{ $attentionWorkflowCount }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}"
               href="{{ route('admin.orders.index') }}">
                <i class="bi bi-bag-plus"></i>
                <span>Buy Service</span>
            </a>
        </li>
        
        <hr class="sidebar-divider">
        
        <!-- Section 6: Communication & Support -->
        <div class="section-header">Communication & Support</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}"
               href="{{ route('admin.notices.index') }}">
                <i class="bi bi-megaphone"></i>
                <span>Announcement</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.support-tickets.*') ? 'active' : '' }}"
               href="{{ route('admin.support-tickets.index') }}">
                <i class="bi bi-headset"></i>
                <span>Support Tickets</span>
                @if($openTicketsCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $openTicketsCount }}</span>
                @endif
            </a>
        </li>

        <!-- Meetings Link -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.meetings.*') ? 'active' : '' }}"
               href="{{ route('admin.meetings.index') }}">
                <i class="bi bi-calendar-check"></i>
                <span>Meetings</span>
                @if($pendingMeetingsCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $pendingMeetingsCount }}</span>
                @endif
            </a>
        </li>
        
        <hr class="sidebar-divider">
        
        <!-- Section 7: System Settings -->
        <div class="section-header">System Settings</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.leave-policies.*') ? 'active' : '' }}"
               href="{{ route('admin.leave-policies.index') }}">
                <i class="bi bi-calendar2-check"></i>
                <span>Leave Policy</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}"
               href="{{ route('admin.holidays.index') }}">
                <i class="bi bi-calendar-event"></i>
                <span>Holidays</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.upi.*') ? 'active' : '' }}"
               href="{{ route('admin.upi.index') }}">
                <i class="bi bi-credit-card-2-front"></i>
                <span>UPI Settings</span>
            </a>
        </li>

        <!-- Partner Websites Link -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.partner-websites.*') ? 'active' : '' }}"
               href="{{ route('admin.partner-websites.index') }}">
                <i class="bi bi-link-45deg"></i>
                <span>Partner Websites</span>
            </a>
        </li>
    </ul>
    
    <!-- Logout Section -->
    <div class="logout-section">
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-button">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</nav>