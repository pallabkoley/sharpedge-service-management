@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-semibold" style="color: #0f172a;">Order Workflow Health</h1>
            <p class="text-muted mb-0">Detailed workflow status for order #{{ $health->order->order_number ?? $health->order_id }}</p>
        </div>
        <!-- Recalculate Workflow Button -->
        <button type="button" class="btn btn-outline-primary rounded-pill px-4" id="recalculateBtn" onclick="recalculateHealth()">
            <i class="bi bi-arrow-repeat me-2"></i>Recalculate Workflow
        </button>
    </div>

    @php
        $order = $health->order;
        $service = $health->service;
        $clientName = $order->client->name ?? 'N/A'; // adjust based on your relations
        $status = $health->health_status; // READY, NOT_READY, ATTENTION
        $statusColors = [
            'READY' => ['bg' => 'success', 'text' => 'Ready', 'icon' => 'check-circle-fill'],
            'NOT_READY' => ['bg' => 'warning', 'text' => 'Not Ready', 'icon' => 'clock-fill'],
            'ATTENTION' => ['bg' => 'danger', 'text' => 'Attention', 'icon' => 'exclamation-triangle-fill'],
        ];
        $statusInfo = $statusColors[$status] ?? ['bg' => 'secondary', 'text' => $status, 'icon' => 'question-circle-fill'];

        $totalTasks = $health->total_tasks;
        $verifiedTasks = $health->verified_tasks;
        $pendingTasks = $health->pending_tasks;
        $rejectedTasks = $health->rejected_tasks;
        $completionPercent = $totalTasks > 0 ? round(($verifiedTasks / $totalTasks) * 100) : 0;
        $workflowValid = $health->order_ok; // boolean
    @endphp

    <!-- Order Header with Status Badge -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-4">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Order ID</span>
                            <div class="fw-bold fs-5" style="color: #0f172a;">#{{ $order->order_number ?? $health->order_id }}</div>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Service</span>
                            <div class="fw-semibold">{{ $service->name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Client</span>
                            <div class="fw-semibold">{{ $clientName }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="d-inline-flex align-items-center gap-3">
                        <span class="text-muted me-2">Status:</span>
                        <span class="badge bg-{{ $statusInfo['bg'] }} bg-opacity-15 text-{{ $statusInfo['bg'] }} px-4 py-3 rounded-pill fs-6 fw-semibold">
                            <i class="bi bi-{{ $statusInfo['icon'] }} me-2"></i>
                            {{ $statusInfo['text'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Total Tasks -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                                <i class="bi bi-grid-3x3-gap-fill text-primary fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-0">{{ $totalTasks }}</h2>
                            <span class="text-muted">Total Tasks</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Verified Tasks -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-3 p-3 bg-success bg-opacity-10">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-0">{{ $verifiedTasks }}</h2>
                            <span class="text-muted">Verified Tasks</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pending Tasks -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                                <i class="bi bi-clock-fill text-warning fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-0">{{ $pendingTasks }}</h2>
                            <span class="text-muted">Pending Tasks</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Rejected Tasks -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                                <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-0">{{ $rejectedTasks }}</h2>
                            <span class="text-muted">Rejected Tasks</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow Progress & Integrity -->
    <div class="row g-4 mb-4">
        <!-- Progress Bar -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Workflow Progress</h5>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="text-muted">Completion</span>
                        <span class="fw-bold fs-5">{{ $completionPercent }}%</span>
                    </div>
                    <div class="progress" style="height: 12px; background-color: #e9ecef;">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $completionPercent }}%;" aria-valuenow="{{ $completionPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small text-muted">
                        <span>Verified: {{ $verifiedTasks }}</span>
                        <span>Total: {{ $totalTasks }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Workflow Integrity -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title fw-semibold mb-3">Workflow Integrity</h5>
                    <div class="d-flex align-items-center">
                        @if($workflowValid)
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="bi bi-check-circle-fill text-success fs-2"></i>
                            </div>
                            <div>
                                <h4 class="fw-semibold text-success mb-1">Valid Workflow</h4>
                                <p class="text-muted mb-0">All steps are in correct order</p>
                            </div>
                        @else
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-2"></i>
                            </div>
                            <div>
                                <h4 class="fw-semibold text-danger mb-1">Broken Workflow</h4>
                                <p class="text-muted mb-0">Steps are out of sequence</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="card-title fw-semibold mb-0">Workflow Steps</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="rounded-start">Step</th>
                            <th>Task Name</th>
                            <th>Assigned Staff</th>
                            <th>Status</th>
                            <th>Submitted Time</th>
                            <th class="rounded-end">Verified Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($health->order->tasks ?? [] as $task)
                            @php
                                $status = $task->status; // verified, pending, rejected (or other)
                                $statusClass = match($status) {
                                    'verified' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <tr>
                                <td>{{ $task->step_number ?? $loop->iteration }}</td>
                                <td class="fw-medium">{{ $task->name }}</td>
                                <td>{{ $task->assignedUser->name ?? 'Unassigned' }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusClass }} bg-opacity-15 text-{{ $statusClass }} px-3 py-2 rounded-pill">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>{{ $task->submitted_at ? \Carbon\Carbon::parse($task->submitted_at)->format('d M Y, H:i') : '-' }}</td>
                                <td>{{ $task->verified_at ? \Carbon\Carbon::parse($task->verified_at)->format('d M Y, H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-clipboard-x display-4 text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No tasks found for this order.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Admin Decision Panel -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-semibold mb-2">Admin Decision</h5>
                    @if($status === 'READY')
                        <p class="text-success mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            All tasks verified and workflow valid. Ready to issue certificate.
                        </p>
                    @elseif($status === 'NOT_READY')
                        <p class="text-warning mb-0">
                            <i class="bi bi-clock-fill me-2"></i>
                            Some tasks are still pending. Workflow cannot proceed.
                        </p>
                    @elseif($status === 'ATTENTION')
                        <p class="text-danger mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Rejected tasks require correction before proceeding.
                        </p>
                    @endif
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @if($status === 'READY')
                        <a href="{{ route('admin.orders.certificate', $health->order_id) }}" class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Upload Certificate
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for recalculate (used by JS) -->
<form id="recalculateForm" method="POST" action="{{ route('admin.workflow-health.recalculate', $health->order_id) }}" style="display: none;">
    @csrf
</form>
@endsection

@push('scripts')
<script>
    function recalculateHealth() {
        if (!confirm('Recalculate workflow health for this order?')) return;

        const btn = document.getElementById('recalculateBtn');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Recalculating...';
        btn.disabled = true;

        fetch('{{ route('admin.workflow-health.recalculate', $health->order_id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showNotification('Workflow health recalculated successfully!', 'success');
            setTimeout(() => window.location.reload(), 1500);
        })
        .catch(error => {
            showNotification('Error recalculating workflow. Please try again.', 'error');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    function showNotification(message, type = 'info') {
        // Simple toast notification
        const toast = document.createElement('div');
        toast.className = `position-fixed top-0 end-0 m-3 p-3 bg-${type === 'success' ? 'success' : 'danger'} text-white rounded-4 shadow`;
        toast.style.zIndex = 9999;
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endpush

@push('styles')
<style>
    /* Additional custom styling for premium look */
    .badge.bg-opacity-15 {
        --bs-bg-opacity: 0.15;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.02) !important;
    }
    .progress {
        background-color: #f1f5f9;
    }
    .table th {
        font-weight: 600;
        font-size: 0.9rem;
        color: #475569;
        border-bottom-width: 1px;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.95rem;
    }
</style>
@endpush