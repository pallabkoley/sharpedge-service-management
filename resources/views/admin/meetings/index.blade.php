@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="header">
        <h2>Meetings</h2>
        <p class="text-muted mb-0">Manage and approve client meeting requests</p>
    </div>

    <!-- Stats Cards (based on actual statuses) -->
    @php
        $total      = $meetings->count();
        $pending    = $meetings->where('status', 'pending_approval')->count();
        $approved   = $meetings->where('status', 'approved')->count();
        $rejected   = $meetings->where('status', 'rejected')->count();
    @endphp
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $total }}</div>
                        <div class="text-muted">Total Meetings</div>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $pending }}</div>
                        <div class="text-muted">Pending Approval</div>
                    </div>
                    <div class="card-icon" style="background-color: #f59e0b;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $approved }}</div>
                        <div class="text-muted">Approved</div>
                    </div>
                    <div class="card-icon" style="background-color: #10b981;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $rejected }}</div>
                        <div class="text-muted">Rejected</div>
                    </div>
                    <div class="card-icon" style="background-color: #ef4444;">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meetings Table Card -->
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title mb-0">Meeting Requests</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="meetingsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Requester</th>
                        <th>Scheduled Time</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $meeting)
                    <tr>
                        <td>#{{ $meeting->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $meeting->client_display_name }}</div>
                            @if($meeting->client)
                                <small class="text-muted">{{ $meeting->client->email }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $meeting->requester->name ?? 'System' }}
                        </td>
                        <td>
                            @if($meeting->meeting_date && $meeting->meeting_time)
                                {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y') }} {{ $meeting->meeting_time }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClasses = [
                                    'pending_approval' => 'warning',
                                    'approved'         => 'success',
                                    'rejected'         => 'danger',
                                ];
                                $statusLabels = [
                                    'pending_approval' => 'Pending Approval',
                                    'approved'         => 'Approved',
                                    'rejected'         => 'Rejected',
                                ];
                                $class = $statusClasses[$meeting->status] ?? 'secondary';
                                $label = $statusLabels[$meeting->status] ?? ucfirst($meeting->status);
                            @endphp
                            <span class="badge bg-{{ $class }} bg-opacity-10 text-{{ $class }} px-3 py-2">
                                {{ $label }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $meeting->created_at->format('M d, Y') }}</small>
                        </td>
                        <td>
                            @if($meeting->status === 'pending_approval')
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.meetings.approve', $meeting->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this meeting?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.meetings.reject', $meeting->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this meeting?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small">No actions</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 48px; color: #cbd5e1;"></i>
                            <p class="text-muted mt-3">No meetings found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stats-card {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    .card-value {
        font-size: 24px;
        font-weight: bold;
        color: #1e293b;
    }
    .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .section-card {
        background-color: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .section-title {
        color: #1e293b;
        font-weight: 600;
    }
    #meetingsTable tbody tr:hover {
        background-color: #f8fafc;
    }
</style>
@endpush

@push('scripts')
<!-- No additional scripts needed; approve/reject use standard form submissions -->
@endpush

@endsection