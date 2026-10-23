@extends('layouts.Backoffice')

@section('page-title', 'Dashboard')

@section('content')
    <!-- Header with Attendance -->
    <div class="header d-flex justify-content-between align-items-center">
        <div>
            <h2>Welcome back, {{ Auth::user()->name }}!</h2>
            <p>Welcome back! Here's what's happening today.</p>
        </div>

        <!-- Attendance Status & Button -->
        <div class="attendance-section">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Attendance Button -->
            @if($attendance === null)
                <form action="{{ route('backoffice.attendance.clockIn') }}" method="POST">
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

                    <form action="{{ route('backoffice.attendance.clockOut') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-lg shadow-sm w-100">
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

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <a href="{{ route('backoffice.tasks.index', ['filter' => 'all']) }}" class="text-decoration-none">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-muted mb-0">TOTAL TASKS</h6>
                        <div class="card-icon bg-primary">
                            <i class="bi bi-list-task"></i>
                        </div>
                    </div>
                    <h3 class="card-value">{{ $totalTasks ?? 0 }}</h3>
                    <div class="card-change up">
                        <i class="bi bi-arrow-up me-1"></i>
                        <span>View all tasks</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <a href="{{ route('backoffice.tasks.index', ['filter' => 'in_progress']) }}" class="text-decoration-none">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-muted mb-0">IN PROGRESS</h6>
                        <div class="card-icon bg-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <h3 class="card-value">{{ $inProgress ?? 0 }}</h3>
                    <div class="card-change up">
                        <i class="bi bi-arrow-up me-1"></i>
                        <span>View in progress</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <a href="{{ route('backoffice.tasks.index', ['filter' => 'overdue']) }}" class="text-decoration-none">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-muted mb-0">OVERDUE</h6>
                        <div class="card-icon bg-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                    <h3 class="card-value">{{ $overdue ?? 0 }}</h3>
                    <div class="card-change down">
                        <i class="bi bi-arrow-down me-1"></i>
                        <span>View overdue</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <a href="{{ route('backoffice.tasks.index', ['filter' => 'completed']) }}" class="text-decoration-none">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-muted mb-0">COMPLETED</h6>
                        <div class="card-icon bg-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                    <h3 class="card-value">{{ $completed ?? 0 }}</h3>
                    <div class="card-change up">
                        <i class="bi bi-arrow-up me-1"></i>
                        <span>View completed</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content-section">
        <div class="row g-4">
            <!-- Task Completion Overview -->
            <div class="col-lg-8">
                <div class="section-card">
                    <h4 class="section-title">Task Completion Overview</h4>
                    <div class="chart-placeholder">
                        <canvas id="taskChart" height="250"></canvas>
                    </div>
                    
                    <div class="task-progress mt-4">
                        <div class="progress-item">
                            <div class="progress-label">
                                <span>Completed</span>
                                <span>{{ $completed ?? 0 }} tasks ({{ $totalTasks > 0 ? round(($completed/$totalTasks)*100) : 0 }}%)</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill progress-completed" style="width: {{ $totalTasks > 0 ? ($completed/$totalTasks)*100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-label">
                                <span>In Progress</span>
                                <span>{{ $inProgress ?? 0 }} tasks ({{ $totalTasks > 0 ? round(($inProgress/$totalTasks)*100) : 0 }}%)</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill progress-in-progress" style="width: {{ $totalTasks > 0 ? ($inProgress/$totalTasks)*100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-label">
                                <span>Overdue</span>
                                <span>{{ $overdue ?? 0 }} tasks ({{ $totalTasks > 0 ? round(($overdue/$totalTasks)*100) : 0 }}%)</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill progress-overdue" style="width: {{ $totalTasks > 0 ? ($overdue/$totalTasks)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Uploads -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="section-title mb-0">Recent Uploads</h4>
                        <span class="badge bg-secondary">{{ date('M d, Y') }}</span>
                    </div>
                    
                    <div class="recent-uploads">
                        <div class="upload-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-pdf text-danger file-icon"></i>
                                <div class="ms-3">
                                    <div class="file-name fw-medium">trade_license.pdf</div>
                                    <small class="text-muted">Client: Rahul Sharma</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="file-time d-block">2 hours ago</span>
                                <small class="text-success">Approved</small>
                            </div>
                        </div>
                        <div class="upload-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-image text-primary file-icon"></i>
                                <div class="ms-3">
                                    <div class="file-name fw-medium">passport.jpg</div>
                                    <small class="text-muted">Client: TechVision Inc.</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="file-time d-block">1 day ago</span>
                                <small class="text-warning">Pending Review</small>
                            </div>
                        </div>
                        <div class="upload-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-text text-info file-icon"></i>
                                <div class="ms-3">
                                    <div class="file-name fw-medium">application.docx</div>
                                    <small class="text-muted">Client: Global Retail LLC</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="file-time d-block">2 days ago</span>
                                <small class="text-success">Approved</small>
                            </div>
                        </div>
                        <div class="upload-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-pdf text-danger file-icon"></i>
                                <div class="ms-3">
                                    <div class="file-name fw-medium">business_plan.pdf</div>
                                    <small class="text-muted">Client: Anne Corp</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="file-time d-block">3 days ago</span>
                                <small class="text-success">Approved</small>
                            </div>
                        </div>
                        <div class="upload-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-image text-success file-icon"></i>
                                <div class="ms-3">
                                    <div class="file-name fw-medium">visa_copy.png</div>
                                    <small class="text-muted">Client: John Doe</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="file-time d-block">4 days ago</span>
                                <small class="text-success">Approved</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-top">
                        <a href="#" class="text-decoration-none small d-flex align-items-center justify-content-center">
                            <i class="bi bi-arrow-right-circle me-2"></i>
                            View all uploaded documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks Table -->
    <div class="mt-4">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">My Recent Tasks</h4>
                <div class="d-flex">
                    <a href="{{ route('backoffice.tasks.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-list-task me-1"></i> View All Tasks
                    </a>
                </div>
            </div>
            
            @if($recentTasks && count($recentTasks) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Order ID</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTasks as $task)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($task->due_date);
                                    $today = \Carbon\Carbon::today();
                                    $isOverdue = $dueDate->isPast() && !in_array($task->status, ['verified', 'submitted']);
                                    $isToday = $dueDate->isToday();
                                    
                                    // Priority mapping
                                    $priorityClass = 'priority-medium';
                                    $priorityText = 'Medium';
                                    if ($task->priority === 'high') {
                                        $priorityClass = 'priority-high';
                                        $priorityText = 'High';
                                    }
                                    if ($task->priority === 'low') {
                                        $priorityClass = 'priority-low';
                                        $priorityText = 'Low';
                                    }
                                    
                                    // Status mapping
                                    $statusClass = 'status-pending';
                                    $statusText = ucfirst(str_replace('_', ' ', $task->status));
                                    
                                    if ($task->status === 'in_progress') {
                                        $statusClass = 'status-in-progress';
                                        $statusText = 'In Progress';
                                    }
                                    if ($task->status === 'completed' || $task->status === 'verified') {
                                        $statusClass = 'status-completed';
                                        $statusText = 'Completed';
                                    }
                                    if ($task->status === 'submitted') {
                                        $statusClass = 'status-waiting';
                                        $statusText = 'Submitted';
                                    }
                                    if ($isOverdue) {
                                        $statusClass = 'status-overdue';
                                        $statusText = 'Overdue';
                                    }
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $task->title ?? 'Task #' . $task->id }}</td>
                                    <td>{{ $task->order->client->name ?? 'N/A' }}</td>
                                    <td>{{ $task->service->name ?? 'N/A' }}</td>
                                    <td>#ORD-{{ $task->order->id ?? $task->id }}</td>
                                    <td><span class="priority-badge {{ $priorityClass }}">{{ $priorityText }}</span></td>
                                    <td>
                                        @if($isToday)
                                            <span class="text-warning fw-bold">Today</span><br>
                                            <small>{{ $dueDate->format('d M Y') }}</small>
                                        @elseif($isOverdue)
                                            <span class="text-danger fw-bold">Overdue</span><br>
                                            <small>{{ $dueDate->format('d M Y') }}</small>
                                        @else
                                            {{ $today->diffInDays($dueDate) }} days<br>
                                            <small>{{ $dueDate->format('d M Y') }}</small>
                                        @endif
                                    </td>
                                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Showing {{ count($recentTasks) }} of {{ $totalTasks }} tasks
                    </div>
                    <div>
                        <a href="{{ route('backoffice.tasks.index') }}" class="btn btn-sm btn-outline-primary">
                            View All Tasks <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-check display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">No tasks assigned yet</h5>
                    <p class="text-muted">You don't have any tasks assigned to you at the moment.</p>
                    <a href="{{ route('backoffice.tasks.index') }}" class="btn btn-primary">
                        <i class="bi bi-list-task me-1"></i> Browse Available Tasks
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Upload Document Modal (kept for potential other uses) -->
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadDocumentForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="currentTaskId" name="task_id">
                        <div class="mb-3">
                            <label for="document" class="form-label">Select Document</label>
                            <input type="file" class="form-control" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            <div class="form-text">Supported formats: PDF, JPG, PNG, DOC (Max 10MB)</div>
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks (Optional)</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Add any remarks about this document..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .header {
        margin-bottom: 2rem;
    }

    .header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .header p {
        color: #718096;
        font-size: 0.95rem;
        margin-bottom: 0;
    }

    .attendance-section {
        min-width: 320px;
        text-align: right;
    }

    .attendance-section .btn-lg {
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .attendance-section .btn-success {
        background-color: #10b981;
        border-color: #10b981;
    }

    .attendance-section .btn-success:hover {
        background-color: #0da271;
        border-color: #0da271;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
    }

    .attendance-section .btn-warning {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: white;
    }

    .attendance-section .btn-warning:hover {
        background-color: #d97706;
        border-color: #d97706;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
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

    .alert-dismissible {
        border-radius: 8px;
        border: 1px solid transparent;
    }

    .alert-success {
        background-color: #d1fae5;
        border-color: #a7f3d0;
        color: #065f46;
    }

    .alert-danger {
        background-color: #fee2e2;
        border-color: #fecaca;
        color: #991b1b;
    }

    .alert-info {
        background-color: #dbeafe;
        border-color: #93c5fd;
        color: #1e40af;
    }

    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: 0.2s ease-in-out;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .card-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .card-change {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }

    .card-change.up {
        color: #10b981;
    }

    .card-change.down {
        color: #ef4444;
    }

    .section-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }

    .chart-placeholder {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1rem;
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .task-progress {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .progress-item {
        margin-bottom: 0.5rem;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }

    .progress-bar {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 4px;
    }

    .progress-completed {
        background: #10b981;
    }

    .progress-in-progress {
        background: #f59e0b;
    }

    .progress-overdue {
        background: #ef4444;
    }

    .recent-uploads {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .upload-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s;
    }

    .upload-item:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .file-icon {
        font-size: 1.5rem;
    }

    .file-name {
        font-size: 0.9rem;
        color: #374151;
    }

    .file-time {
        font-size: 0.8rem;
        color: #6b7280;
    }

    .priority-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .priority-high {
        background-color: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .priority-medium {
        background-color: #fef3c7;
        color: #d97706;
        border: 1px solid #fcd34d;
    }

    .priority-low {
        background-color: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-in-progress {
        background-color: #fef3c7;
        color: #d97706;
        border: 1px solid #fcd34d;
    }

    .status-waiting {
        background-color: #e0e7ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
    }

    .status-overdue {
        background-color: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .status-completed {
        background-color: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .status-pending {
        background-color: #f3f4f6;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }

    .task-actions {
        display: flex;
        gap: 8px;
    }

    .btn-task-action {
        background: none;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        padding: 4px 8px;
        color: #6b7280;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-task-action:hover {
        background-color: #f3f4f6;
        color: #374151;
        border-color: #d1d5db;
    }

    .btn-task-action button {
        background: none;
        border: none;
        color: inherit;
        padding: 0;
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
        
        .attendance-section .btn-lg {
            width: 100%;
        }
        
        .attendance-completed .alert {
            min-width: 100%;
        }
        
        .table-responsive {
            font-size: 0.85rem;
        }
        
        .task-actions {
            flex-direction: column;
        }
        
        .upload-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .upload-item > div:last-child {
            align-self: flex-end;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the chart if chart data is available
        @if(isset($chartData) && !empty($chartData['labels']))
        const ctx = document.getElementById('taskChart').getContext('2d');
        const taskChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: @json($chartData['datasets'])
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        @endif

        // Set current task ID for upload modal
        window.setCurrentTaskId = function(taskId) {
            document.getElementById('currentTaskId').value = taskId;
            document.getElementById('uploadDocumentForm').action = `/backoffice/tasks/${taskId}/documents/upload`;
        }
    });
</script>
@endsection