@extends('layouts.marketing')

@section('title', 'Leave Management - SharpEdge Marketing')

@section('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    /* Statistics Cards - EXACT SAME as client page */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-info h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
        line-height: 1;
    }
    
    .stat-info p {
        color: var(--gray-500);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0;
    }
    
    /* Leave Balance Cards */
    .balance-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .balance-card {
        background: white;
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        transition: transform 0.2s ease;
    }
    
    .balance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .balance-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 0.75rem;
    }
    
    .balance-type {
        font-weight: 600;
        color: var(--dark-color);
        font-size: 0.875rem;
    }
    
    .balance-count {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.25rem;
    }
    
    .balance-details {
        font-size: 0.75rem;
        color: var(--gray-600);
    }
    
    .balance-details span {
        display: inline-block;
        margin-right: 0.75rem;
    }
    
    .balance-progress {
        height: 6px;
        background: var(--gray-200);
        border-radius: 3px;
        margin-top: 0.75rem;
        overflow: hidden;
    }
    
    .balance-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
        border-radius: 3px;
        transition: width 0.3s ease;
    }
    
    .balance-progress-bar.warning {
        background: linear-gradient(90deg, var(--warning-color), #e58e0b);
    }
    
    .balance-progress-bar.danger {
        background: linear-gradient(90deg, var(--danger-color), #dc2626);
    }
    
    /* Main Content Card - EXACT SAME structure */
    .main-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }
    
    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .header-left h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .header-left p {
        color: var(--gray-500);
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    /* Header Actions - EXACT SAME */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    /* Search box on the right side */
    .header-search-box {
        position: relative;
        width: 250px;
    }
    
    .header-search-box input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-700);
    }
    
    .header-search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }
    
    /* Filters - EXACT SAME */
    .filters {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .status-filter {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .status-btn {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        background: white;
        color: var(--gray-600);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .status-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .status-btn:hover {
        background: var(--primary-light);
        color: white;
        border-color: var(--primary-color);
    }
    
    /* Table Styles - Simple Black Header - EXACT SAME */
    .table-container {
        padding: 0;
        overflow-x: auto;
    }
    
    .leaves-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    /* Black Table Header - EXACT SAME */
    .leaves-table thead {
        background-color: #3b4f6b !important;
    }
    
    .leaves-table th {
        padding: 1rem 1rem;
        text-align: left;
        font-weight: 600;
        color: white !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: none;
        white-space: nowrap;
    }
    
    /* Table body styles - EXACT SAME */
    .leaves-table td {
        padding: 1rem 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
        color: var(--gray-700);
        font-size: 0.875rem;
        background: white;
    }
    
    .leaves-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .leaves-table tbody tr:hover {
        background-color: var(--gray-50);
    }
    
    /* Leave Type Badge - Similar to client code box */
    .leave-type-box {
        display: inline-block;
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary-color);
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: 600;
        font-size: 0.875rem;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    /* Status Badge - Similar to client status */
    .status-with-dot {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .status-dot.pending {
        background: var(--warning-color);
    }
    
    .status-dot.approved {
        background: var(--secondary-color);
    }
    
    .status-dot.rejected {
        background: var(--danger-color);
    }
    
    .status-text {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .status-pending-text {
        color: var(--warning-color);
    }
    
    .status-approved-text {
        color: var(--secondary-color);
    }
    
    .status-rejected-text {
        color: var(--danger-color);
    }
    
    /* Action Buttons - EXACT SAME as client page */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* View button - Blue (same as edit) */
    .action-btn.view {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }
    
    .action-btn.view:hover {
        background: linear-gradient(135deg, var(--primary-dark), #1d4ed8);
    }
    
    /* Add Leave Button - EXACT SAME as Add Client */
    .btn-add {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    /* Laravel Pagination Styles - EXACT SAME */
    .pagination-container {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: white;
        display: flex;
        justify-content: center;
    }
    
    .pagination {
        display: flex;
        gap: 0.5rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .page-item .page-link {
        border: 1px solid var(--gray-300);
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        color: var(--primary-color);
        background: white;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .page-item.active .page-link {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .page-item.disabled .page-link {
        color: var(--gray-400);
        cursor: not-allowed;
    }
    
    .page-item .page-link:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }
    
    /* Modal Styles - EXACT SAME structure */
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .form-label.required:after {
        content: " *";
        color: #dc3545;
    }
    
    /* Leave Balance Info in Modal */
    .leave-balance-info {
        background: var(--primary-light-bg);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 0.5rem;
        padding: 0.75rem;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        display: none;
    }
    
    .balance-display {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.25rem;
    }
    
    .balance-label {
        color: var(--gray-600);
        font-weight: 500;
    }
    
    .balance-value {
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .balance-warning {
        color: var(--warning-color);
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .balance-danger {
        color: var(--danger-color);
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Form Helper Text */
    .form-text {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-top: 0.25rem;
    }
    
    /* Half Day Options */
    .half-day-options {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
    }
    
    .half-day-option {
        flex: 1;
        text-align: center;
        padding: 0.75rem;
        border: 2px solid var(--gray-300);
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }
    
    .half-day-option:hover {
        border-color: var(--primary-color);
        background: var(--primary-light-bg);
    }
    
    .half-day-option.active {
        border-color: var(--primary-color);
        background: var(--primary-light-bg);
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .half-day-option i {
        display: block;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    /* File Upload Area - Similar to profile upload */
    .file-upload-area {
        border: 2px dashed var(--gray-300);
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
        background: var(--gray-50);
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .file-upload-area:hover {
        border-color: var(--primary-color);
        background: var(--primary-light-bg);
    }
    
    .file-upload-area i {
        font-size: 2rem;
        color: var(--gray-400);
        margin-bottom: 0.5rem;
    }
    
    .file-upload-area p {
        color: var(--gray-600);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .file-upload-area small {
        color: var(--gray-500);
        font-size: 0.75rem;
    }
    
    /* Staff ID Display */
    .staff-id-display {
        background: var(--gray-100);
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 1rem;
        border: 1px solid var(--gray-300);
    }
    
    /* Half Day Indicator in table */
    .half-day-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background: rgba(108, 117, 125, 0.1);
        border-radius: 0.375rem;
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-left: 0.5rem;
    }
    
    /* Responsive - EXACT SAME */
    @media (max-width: 768px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }
        
        .balance-cards {
            grid-template-columns: 1fr;
        }
        
        .filters {
            flex-direction: column;
            align-items: stretch;
        }
        
        .header-actions {
            flex-direction: column;
            width: 100%;
            gap: 0.75rem;
        }
        
        .header-search-box {
            width: 100%;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .leaves-table {
            min-width: 1000px;
        }
        
        .half-day-options {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header - EXACT SAME structure -->
    <div class="header">
        <h1>Leave Management</h1>
        <p>Apply for leave and track your leave requests</p>
    </div>

    <!-- Statistics Cards - EXACT SAME layout -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats->total ?? 0 }}</h3>
                <p>Total Requests</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #e58e0b);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats->pending ?? 0 }}</h3>
                <p>Pending</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success-color), #1a7c3b);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats->approved ?? 0 }}</h3>
                <p>Approved</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--danger-color), #dc2626);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats->rejected ?? 0 }}</h3>
                <p>Rejected</p>
            </div>
        </div>
    </div>

    <!-- Leave Balances Section -->
    @if(count($leaveBalances) > 0)
    <div class="main-card">
        <div class="card-header">
            <div class="header-left">
                <h2>My Leave Balances ({{ Carbon\Carbon::now()->year }})</h2>
                <p>Available leave days for each leave type</p>
            </div>
        </div>
        <div class="table-container" style="padding: 1.5rem;">
            <div class="balance-cards">
                @foreach($leaveBalances as $balance)
                @php
                    $usedPercentage = $balance['max_per_year'] > 0 ? ($balance['approved_used'] / $balance['max_per_year']) * 100 : 0;
                    $progressClass = 'success';
                    if ($usedPercentage >= 80) {
                        $progressClass = 'danger';
                    } elseif ($usedPercentage >= 50) {
                        $progressClass = 'warning';
                    }
                @endphp
                <div class="balance-card">
                    <div class="balance-header">
                        <div class="balance-type">{{ $balance['leave_type_name'] }}</div>
                        <div class="balance-count">{{ number_format($balance['leave_left'], 1) }}</div>
                    </div>
                    <div class="balance-details">
                        <span>Max: {{ $balance['max_per_year'] }}</span>
                        <span>Used: {{ number_format($balance['approved_used'], 1) }}</span>
                    </div>
                    <div class="balance-progress">
                        <div class="balance-progress-bar {{ $progressClass }}" style="width: {{ min($usedPercentage, 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Card - EXACT SAME structure -->
    <div class="main-card">
        <div class="card-header">
            <div class="header-left">
                <h2>My Leave Requests</h2>
                <p>All your leave applications</p>
            </div>
            
            <!-- Search and Add Button on the right - EXACT SAME -->
            <div class="header-actions">
                <form method="GET" action="{{ route('marketing.leave-requests.index') }}" class="header-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search leave requests..." 
                           value="{{ request('search') }}" autocomplete="off">
                </form>
                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                    <i class="fas fa-plus"></i> Apply for Leave
                </button>
            </div>
        </div>
        
        <!-- Filters - Status Buttons - EXACT SAME pattern -->
        <div class="filters">
            <div class="status-filter">
                <a href="{{ route('marketing.leave-requests.index', ['search' => request('search')]) }}" 
                   class="status-btn {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('marketing.leave-requests.index', ['status' => 'pending', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'pending' ? 'active' : '' }}">
                    Pending
                </a>
                <a href="{{ route('marketing.leave-requests.index', ['status' => 'approved', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'approved' ? 'active' : '' }}">
                    Approved
                </a>
                <a href="{{ route('marketing.leave-requests.index', ['status' => 'rejected', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'rejected' ? 'active' : '' }}">
                    Rejected
                </a>
            </div>
        </div>

        <!-- Leave Requests Table - EXACT SAME table structure -->
        <div class="table-container">
            <table class="leaves-table">
                <thead>
                    <tr>
                        <th>LEAVE TYPE</th>
                        <th>DATES</th>
                        <th>DAYS</th>
                        <th>STATUS</th>
                        <th>APPLIED ON</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myLeaves as $leave)
                    <tr data-leave-id="{{ $leave->id }}">
                        <!-- LEAVE TYPE Column -->
                        <td>
                            <span class="leave-type-box">
                                {{ $leave->leaveType->name ?? 'N/A' }}
                            </span>
                            @if($leave->is_half_day)
                                <span class="half-day-indicator">
                                    <i class="fas fa-clock"></i>
                                    {{ $leave->half_day_type == 'first_half' ? '1st Half' : '2nd Half' }}
                                </span>
                            @endif
                        </td>
                        
                        <!-- DATES Column -->
                        <td>
                            <div class="fw-semibold">{{ date('M d, Y', strtotime($leave->from_date)) }}</div>
                            <div class="text-muted small">to {{ date('M d, Y', strtotime($leave->to_date)) }}</div>
                        </td>
                        
                        <!-- DAYS Column -->
                        <td>
                            <div class="fw-bold">{{ number_format($leave->total_days, 1) }}</div>
                            <div class="text-muted small">day(s)</div>
                        </td>
                        
                        <!-- STATUS Column - Same pattern as client status -->
                        <td>
                            <div class="status-with-dot">
                                @if($leave->status == 'pending')
                                    <span class="status-dot pending"></span>
                                    <span class="status-text status-pending-text">PENDING</span>
                                @elseif($leave->status == 'approved')
                                    <span class="status-dot approved"></span>
                                    <span class="status-text status-approved-text">APPROVED</span>
                                @else
                                    <span class="status-dot rejected"></span>
                                    <span class="status-text status-rejected-text">REJECTED</span>
                                @endif
                            </div>
                        </td>
                        
                        <!-- APPLIED ON Column -->
                        <td>
                            <div>{{ $leave->created_at->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $leave->created_at->format('h:i A') }}</div>
                        </td>
                        
                        <!-- ACTIONS Column - Only attachment button remains (cancel removed) -->
                        <td>
                            <div class="action-buttons">
                                @if($leave->attachment)
                                    <a href="{{ route('marketing.leave-requests.attachment', $leave->id) }}" 
                                       target="_blank" 
                                       class="action-btn view" 
                                       title="View Attachment">
                                        <i class="fas fa-paperclip"></i>
                                    </a>
                                @endif
                                {{-- Cancel button has been removed as per request --}}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No leave requests found</p>
                            @if(request('search') || request('status'))
                                <a href="{{ route('marketing.leave-requests.index') }}" class="btn btn-sm btn-primary mt-2">
                                    Clear Filters
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Laravel Pagination - EXACT SAME -->
            @if($myLeaves->hasPages())
            <div class="pagination-container">
                {{ $myLeaves->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Apply for Leave Modal - EXACT SAME modal structure as Add Client -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply for Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('marketing.leave-requests.store') }}" method="POST" enctype="multipart/form-data" id="applyLeaveForm">
                @csrf
                <div class="modal-body">
                    <!-- Staff ID Display -->
                    @if($staff)
                    <div class="staff-id-display">
                        <i class="fas fa-id-card me-2"></i>
                        Staff ID: {{ $staff->staff_id }}
                    </div>
                    @endif
                    
                    <div class="row">
                        <!-- Left Column: Leave Details -->
                        <div class="col-md-8">
                            <!-- Leave Type -->
                            <div class="mb-3">
                                <label class="form-label required">Select Leave Type</label>
                                <select class="form-select" name="leave_type_id" id="leave_type_id" required>
                                    <option value="">Select the type of leave you're applying for</option>
                                    @foreach($leaveTypes as $type)
                                        @php
                                            $balance = $leaveBalances->firstWhere('leave_type_id', $type->id);
                                            $remaining = $balance ? $balance['leave_left'] : 0;
                                        @endphp
                                        <option value="{{ $type->id }}" 
                                                data-max="{{ $balance ? $balance['max_per_year'] : 0 }}"
                                                data-used="{{ $balance ? $balance['approved_used'] : 0 }}"
                                                data-remaining="{{ $remaining }}">
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Select the type of leave you're applying for</div>
                                
                                <!-- Leave Balance Information -->
                                <div class="leave-balance-info" id="leaveBalanceInfo">
                                    <div class="balance-display">
                                        <span class="balance-label">Yearly Limit:</span>
                                        <span class="balance-value" id="maxLimit">0</span>
                                    </div>
                                    <div class="balance-display">
                                        <span class="balance-label">Used:</span>
                                        <span class="balance-value" id="usedDays">0</span>
                                    </div>
                                    <div class="balance-display">
                                        <span class="balance-label">Remaining:</span>
                                        <span class="balance-value" id="remainingDays">0</span>
                                    </div>
                                    <div class="balance-warning mt-2" id="balanceWarning" style="display: none;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        You have limited leave balance remaining
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Date Range -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">From Date *</label>
                                    <input type="text" class="form-control datepicker" name="from_date" id="from_date" 
                                           placeholder="Select start date" required autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">To Date *</label>
                                    <input type="text" class="form-control datepicker" name="to_date" id="to_date" 
                                           placeholder="Select end date" required autocomplete="off">
                                    <div class="form-text" id="halfDayWarning" style="display: none; color: var(--warning-color);">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        Half day must be for a single date only
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Half Day Option -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_half_day" name="is_half_day" value="1">
                                    <label class="form-check-label" for="is_half_day">
                                        This is a half-day leave
                                    </label>
                                </div>
                                <div class="form-text">Check if you're taking only half day leave</div>
                            </div>
                            
                            <!-- Half Day Type (Hidden by default) -->
                            <div class="mb-3" id="halfDayOptions" style="display: none;">
                                <label class="form-label">Half Day Type *</label>
                                <div class="half-day-options">
                                    <div class="half-day-option" data-value="first_half">
                                        <i class="fas fa-sun"></i>
                                        <div>First Half</div>
                                        <small>Morning (9 AM - 1 PM)</small>
                                    </div>
                                    <div class="half-day-option" data-value="second_half">
                                        <i class="fas fa-moon"></i>
                                        <div>Second Half</div>
                                        <small>Afternoon (1 PM - 6 PM)</small>
                                    </div>
                                </div>
                                <input type="hidden" name="half_day_type" id="half_day_type">
                                <div class="form-text">Half day type selection is required for half day leave</div>
                            </div>
                            
                            <!-- Reason -->
                            <div class="mb-3">
                                <label class="form-label required">Reason for Leave *</label>
                                <textarea class="form-control" name="reason" rows="3" 
                                          placeholder="Please provide a reason for your leave application" 
                                          required maxlength="1000"></textarea>
                                <div class="form-text">Maximum 1000 characters</div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Supporting Document -->
                        <div class="col-md-4">
                            <!-- Supporting Document -->
                            <div class="mb-4">
                                <label class="form-label">Supporting Document</label>
                                <div class="file-upload-area" onclick="document.getElementById('attachment').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Upload supporting document (PDF, JPG, PNG, max 2MB)</p>
                                    <small>Click to browse or drag and drop</small>
                                    <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png" hidden>
                                </div>
                                <div class="form-text">Optional: Upload medical certificate or other supporting documents</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Leave Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cancel Leave Modal has been removed as per request --}}

@endsection

@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
$(document).ready(function() {
    // Initialize Toastr - EXACT SAME
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Show success/error messages from session - EXACT SAME
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    @endif

    // Initialize date pickers
    flatpickr(".datepicker", {
        dateFormat: "Y-m-d",
        minDate: "today",
        disableMobile: true
    });

    // Half Day Toggle
    $('#is_half_day').change(function() {
        const isChecked = $(this).is(':checked');
        const fromDate = $('input[name="from_date"]');
        const toDate = $('input[name="to_date"]');
        const halfDayWarning = $('#halfDayWarning');
        
        if (isChecked) {
            $('#halfDayOptions').show();
            
            // If to date is different from from date, show warning and auto-correct
            if (fromDate.val() && toDate.val() && fromDate.val() !== toDate.val()) {
                halfDayWarning.show();
                toDate.val(fromDate.val());
            }
            
            // Disable to date field for half day
            toDate.prop('disabled', true).addClass('bg-light');
            
            // Validate half day type selection
            if (!$('#half_day_type').val()) {
                toastr.warning('Please select half day type');
            }
        } else {
            $('#halfDayOptions').hide();
            $('#half_day_type').val('');
            $('.half-day-option').removeClass('active');
            halfDayWarning.hide();
            toDate.prop('disabled', false).removeClass('bg-light');
        }
    });

    // Half Day Option Selection
    $('.half-day-option').click(function() {
        $('.half-day-option').removeClass('active');
        $(this).addClass('active');
        $('#half_day_type').val($(this).data('value'));
    });

    // Show leave balance when leave type is selected
    $('#leave_type_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const maxLimit = selectedOption.data('max');
        const usedDays = selectedOption.data('used');
        const remainingDays = selectedOption.data('remaining');
        const balanceInfo = $('#leaveBalanceInfo');
        const warningDiv = $('#balanceWarning');
        
        if (selectedOption.val() && maxLimit > 0) {
            // Update balance info
            $('#maxLimit').text(maxLimit);
            $('#usedDays').text(usedDays.toFixed(1));
            $('#remainingDays').text(remainingDays.toFixed(1));
            
            // Show/hide warning based on remaining balance
            if (remainingDays <= 0) {
                warningDiv.html('<i class="fas fa-exclamation-triangle me-1"></i> No leave balance remaining');
                warningDiv.removeClass('balance-warning').addClass('balance-danger');
                warningDiv.show();
            } else if (remainingDays < 1) {
                warningDiv.html('<i class="fas fa-exclamation-triangle me-1"></i> Less than 1 day remaining');
                warningDiv.removeClass('balance-danger').addClass('balance-warning');
                warningDiv.show();
            } else if (remainingDays < 3) {
                warningDiv.html('<i class="fas fa-exclamation-triangle me-1"></i> Low leave balance remaining');
                warningDiv.removeClass('balance-danger').addClass('balance-warning');
                warningDiv.show();
            } else {
                warningDiv.hide();
            }
            
            balanceInfo.show();
        } else {
            balanceInfo.hide();
        }
    });

    // Auto-update to date when from date changes for half day
    $('#from_date').change(function() {
        if ($('#is_half_day').is(':checked')) {
            $('#to_date').val($(this).val());
        }
    });

    // File Upload Handler - Similar to client page
    $('#attachment').on('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
        const uploadArea = $(this).closest('.file-upload-area');
        
        if (e.target.files[0]) {
            // Check file size (2MB = 2097152 bytes)
            if (e.target.files[0].size > 2097152) {
                toastr.error('File size must be less than 2MB');
                $(this).val('');
                return;
            }
            
            // Check file type
            const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(e.target.files[0].type)) {
                toastr.error('Only PDF, JPG, and PNG files are allowed');
                $(this).val('');
                return;
            }
            
            // Update UI to show file selected
            uploadArea.find('i').removeClass('fa-cloud-upload-alt').addClass('fa-check-circle text-success');
            uploadArea.find('p').html(`<strong>${fileName}</strong>`);
            uploadArea.css({
                'border-color': 'var(--success-color)',
                'background-color': 'rgba(40, 167, 69, 0.1)'
            });
        }
    });

    // Cancel Leave Button Click has been removed as per request

    // Form Validation - Enhanced with controller logic
    $('#applyLeaveForm').on('submit', function(e) {
        const leaveType = $('select[name="leave_type_id"]').val();
        const fromDate = $('input[name="from_date"]').val();
        const toDate = $('input[name="to_date"]').val();
        const reason = $('textarea[name="reason"]').val();
        const isHalfDay = $('#is_half_day').is(':checked');
        const halfDayType = $('#half_day_type').val();
        const selectedOption = $('#leave_type_id').find('option:selected');
        const remainingDays = parseFloat(selectedOption.data('remaining') || 0);
        
        // Basic validation
        if (!leaveType || !fromDate || !toDate || !reason) {
            e.preventDefault();
            toastr.error('Please fill all required fields');
            return false;
        }
        
        // Half day validation
        if (isHalfDay) {
            if (!halfDayType) {
                e.preventDefault();
                toastr.error('Please select half day type');
                return false;
            }
            
            // Check if it's single day only
            if (fromDate !== toDate) {
                e.preventDefault();
                toastr.error('Half day leave must be for a single date only');
                return false;
            }
        }
        
        // Calculate days
        const from = new Date(fromDate);
        const to = new Date(toDate);
        const diffTime = Math.abs(to - from);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        const totalDays = isHalfDay ? 0.5 : diffDays;
        
        // Check leave balance
        if (totalDays > remainingDays) {
            e.preventDefault();
            toastr.error(`Leave limit exceeded. Remaining: ${remainingDays.toFixed(1)} day(s) for this leave type.`);
            return false;
        }
        
        // Date validation
        if (to < from) {
            e.preventDefault();
            toastr.error('To date must be on or after from date');
            return false;
        }
        
        // Show loading state - EXACT SAME as client page
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
    });

    // Auto-submit search on Enter key - EXACT SAME
    $('input[name="search"]').on('keypress', function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });

    // Auto-hide toastr messages after 5 seconds - EXACT SAME
    setTimeout(function() {
        $('.toast').fadeOut('slow');
    }, 5000);

    // Reset file upload area on form reset
    $('#applyLeaveForm').on('reset', function() {
        $('#attachment').val('');
        const uploadArea = $('#attachment').closest('.file-upload-area');
        uploadArea.find('i').removeClass('fa-check-circle text-success').addClass('fa-cloud-upload-alt');
        uploadArea.find('p').text('Upload supporting document (PDF, JPG, PNG, max 2MB)');
        uploadArea.css({
            'border-color': 'var(--gray-300)',
            'background-color': 'var(--gray-50)'
        });
    });

    // Reset modal when closed
    $('#applyLeaveModal').on('hidden.bs.modal', function() {
        $('#applyLeaveForm')[0].reset();
        $('#halfDayOptions').hide();
        $('#half_day_type').val('');
        $('.half-day-option').removeClass('active');
        $('#leaveBalanceInfo').hide();
        $('#halfDayWarning').hide();
        $('input[name="to_date"]').prop('disabled', false).removeClass('bg-light');
    });
});
</script>
@endsection