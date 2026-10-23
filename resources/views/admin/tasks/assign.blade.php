@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Task Assignment</h2>
                <p class="text-muted mb-0">Assign unassigned tasks to backoffice staff members.</p>
            </div>
            <div class="dropdown">
                <div class="d-flex align-items-center dropdown-toggle"
                     data-bs-toggle="dropdown"
                     style="cursor: pointer;">
                </div>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">UNASSIGNED TASKS</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $tasks->count() }}</h3>
                <div class="card-change up">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <span>Awaiting assignment</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">AVAILABLE STAFF</h6>
                    <div class="card-icon bg-success">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $staffs->count() }}</h3>
                <div class="card-change up">
                    <i class="bi bi-check-circle me-1"></i>
                    <span>Active backoffice staff</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">HIGH PRIORITY</h6>
                    <div class="card-icon bg-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $tasks->where('priority', 'high')->count() }}</h3>
                <div class="card-change up">
                    <i class="bi bi-arrow-up me-1"></i>
                    <span>Set priority during assignment</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">AVG TASK AGE</h6>
                    <div class="card-icon bg-info">
                        <i class="bi bi-calendar"></i>
                    </div>
                </div>
                <h3 class="card-value">
                    @php
                        $avgDays = $tasks->count() > 0 ? 
                            round($tasks->avg(function($task) {
                                return now()->diffInDays($task->created_at);
                            }), 1) : 0;
                    @endphp
                    {{ $avgDays }}d
                </h3>
                <div class="card-change down">
                    <i class="bi bi-clock me-1"></i>
                    <span>Days since creation</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Unassigned Tasks Table -->
    <div class="content-section">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="section-title mb-0">Unassigned Tasks</h4>
                <div>
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-inline-flex align-items-center py-2" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show d-inline-flex align-items-center py-2" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
            </div>

            @if($tasks->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 48px;"></i>
                    <h4 class="mt-3">No Unassigned Tasks</h4>
                    <p class="text-muted">All tasks have been assigned to staff members.</p>
                </div>
            @else
                <!-- Search Input -->
                <div class="mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="input-group search-box">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="taskSearch" class="form-control border-start-0" placeholder="Search tasks...">
                                <span class="input-group-text bg-white border-start-0">
                                    <i class="bi bi-funnel text-muted"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                Showing {{ $tasks->count() }} unassigned task(s)
                            </small>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                   <table class="table table-hover datatable" id="clientsTable">
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Order/Client</th>
                                <th>Task Name</th>
                                <th>Process Step</th>
                                <th>Created</th>
                                <th>Assignment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td class="fw-bold">
                                        {{ $task->task_code ?? ('#' . $task->id) }}
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">Order #{{ $task->order_id ?? 'N/A' }}</span>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>
                                                {{ $task->order->client->name ?? 'Client Not Found' }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $task->task_name ?? ($task->service->name ?? 'N/A') }}
                                        </div>
                                        @if($task->description)
                                            <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->step_name)
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ $task->step_name }}
                                            </span>
                                        @elseif($task->workProcessStep)
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ $task->workProcessStep->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">No Step</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $task->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.tasks.assign.store', $task) }}" 
                                              class="assign-form">
                                            @csrf
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <select name="assigned_to" class="form-select form-select-sm flex-grow-1" required style="min-width: 150px;">
                                                    <option value="">Select Staff</option>
                                                    @foreach($staffs as $staff)
                                                        <option value="{{ $staff->id }}">
                                                            {{ $staff->name }} ({{ $staff->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm assign-btn" style="white-space: nowrap;">
                                                    <i class="bi bi-person-check me-1"></i> Assign
                                                </button>
                                            </div>
                                            
                                            <!-- Priority, Task Recurrence, and Due Date Row -->
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <!-- Priority -->
                                                <select name="priority" class="form-select form-select-sm" required style="width: auto; min-width: 140px;">
                                                    <option value="medium" selected>Medium Priority</option>
                                                    <option value="low">Low Priority</option>
                                                    <option value="high">High Priority</option>
                                                </select>
                                                
                                                <!-- Task Recurrence -->
                                                <select class="form-select form-select-sm recurrence-select" 
                                                        style="width: auto; min-width: 140px;"
                                                        data-task-id="{{ $task->id }}">
                                                    <option value="non-recurring" selected>Non-Recurring</option>
                                                    <option value="recurring">Recurring</option>
                                                </select>
                                                
                                                <!-- Due Date -->
                                                <input type="date" 
                                                       name="due_date" 
                                                       class="form-control form-control-sm" 
                                                       min="{{ date('Y-m-d') }}"
                                                       style="width: auto; min-width: 130px;"
                                                       value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                                            </div>
                                            
                                            <!-- Recurrence Frequency (Hidden by default) -->
                                            <div class="frequency-container" 
                                                 id="frequency_container_{{ $task->id }}" 
                                                 style="display: none;">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <label class="form-label-sm mb-0 text-muted" style="font-size: 12px; min-width: 120px;">
                                                        Recurrence Frequency:
                                                    </label>
                                                    <select class="form-select form-select-sm frequency-select" 
                                                            style="width: auto; min-width: 110px;"
                                                            data-task-id="{{ $task->id }}">
                                                        <option value="monthly">Monthly</option>
                                                        <option value="quarterly">Quarterly</option>
                                                        <option value="yearly">Yearly</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <!-- Admin Notes Textarea -->
                                            <div class="mt-1">
                                                <textarea name="admin_notes" 
                                                          class="form-control form-control-sm" 
                                                          rows="1" 
                                                          placeholder="Admin notes..."
                                                          style="font-size: 12px; resize: vertical; min-height: 32px;"></textarea>
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <!-- Reassign Button (Modal Trigger) -->
                                        <button type="button" 
                                                class="btn btn-outline-warning btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#reassignModal"
                                                data-task-id="{{ $task->id }}"
                                                data-task-name="{{ $task->task_name ?? ($task->service->name ?? 'Task #' . $task->id) }}"
                                                title="Reassign">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Task count display -->
                <div class="mt-3 text-end">
                    <small class="text-muted">
                        Total: {{ $tasks->count() }} unassigned task(s)
                    </small>
                </div>
            @endif
        </div>
    </div>

    <!-- Reassign Modal -->
    <div class="modal fade" id="reassignModal" tabindex="-1" aria-labelledby="reassignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reassignModalLabel">Reassign Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="reassignForm">
                    @csrf
                    <div class="modal-body">
                        <p id="modalTaskInfo" class="mb-3">Reassign task to another staff member.</p>
                        
                        <div class="mb-3">
                            <label for="reassign_staff" class="form-label">Assign To</label>
                            <select name="assigned_to" id="reassign_staff" class="form-select" required>
                                <option value="">Select Staff</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}">
                                        {{ $staff->name }} ({{ $staff->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reassign_notes" class="form-label">Admin Notes</label>
                            <textarea name="admin_notes" id="reassign_notes" class="form-control" rows="2" placeholder="Reason for reassignment..."></textarea>
                        </div>
                        
                        <input type="hidden" id="reassign_task_id" name="task_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Reassign Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="mb-0">© {{ date('Y') }} <strong>SharpEdge</strong>. All rights reserved. | Task Assignment Module</p>
    </footer>
@endsection

@push('styles')
<style>
    /* Table styling to match the image */
    #tasksTable {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
    
    #tasksTable thead {
        background-color: #f8fafc;
    }
    
    #tasksTable th {
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 15px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    #tasksTable td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
        font-size: 14px;
        vertical-align: top;
    }
    
    #tasksTable tbody tr:last-child td {
        border-bottom: none;
    }
    
    #tasksTable tbody tr:hover {
        background-color: #f8fafc;
    }
    
    /* Form styling within table */
    .assign-form {
        min-width: 280px;
    }
    
    .assign-form .form-select-sm,
    .assign-form .form-control-sm {
        height: 32px;
        font-size: 12px;
    }
    
    .assign-form textarea {
        font-size: 12px;
        padding: 4px 8px;
    }
    
    /* Badge styling */
    .badge.bg-opacity-10 {
        padding: 4px 8px;
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    /* Priority badges */
    .badge.bg-danger.bg-opacity-10 {
        background-color: rgba(220, 53, 69, 0.1) !important;
        color: #dc3545 !important;
    }
    
    .badge.bg-warning.bg-opacity-10 {
        background-color: rgba(255, 193, 7, 0.1) !important;
        color: #ffc107 !important;
    }
    
    .badge.bg-success.bg-opacity-10 {
        background-color: rgba(25, 135, 84, 0.1) !important;
        color: #198754 !important;
    }
    
    /* Search box styling */
    .search-box {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    #taskSearch {
        padding: 10px 15px;
        border: none;
        background-color: white;
        transition: all 0.3s;
    }
    
    #taskSearch:focus {
        box-shadow: none;
        outline: none;
    }
    
    .search-box:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Alert styling */
    .alert {
        padding: 0.5rem 1rem;
        margin-bottom: 0;
        border-radius: 6px;
    }
    
    /* Button styling */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 12px;
    }
    
    /* Ensure form controls are properly spaced */
    .assign-form .d-flex.gap-2 {
        gap: 8px !important;
    }
    
    /* Recurrence dropdown styling - matches Priority selector */
    .recurrence-select {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 0.25rem 0.5rem;
        font-size: 12px;
        color: #495057;
        height: 32px;
    }
    
    .frequency-container {
        background-color: #f8f9fa;
        border-radius: 4px;
        padding: 6px 10px;
        border-left: 3px solid #3b82f6;
        margin-top: 4px;
        margin-bottom: 4px;
        transition: all 0.3s ease;
    }
    
    .frequency-select {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 0.25rem 0.5rem;
        font-size: 12px;
        color: #495057;
        height: 32px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all recurrence dropdowns
        document.querySelectorAll('.recurrence-select').forEach(select => {
            const taskId = select.getAttribute('data-task-id');
            const frequencyContainer = document.getElementById(`frequency_container_${taskId}`);
            
            // Set initial state based on selected value
            if (select.value === 'recurring') {
                frequencyContainer.style.display = 'block';
            } else {
                frequencyContainer.style.display = 'none';
            }
            
            // Add change event listener
            select.addEventListener('change', function() {
                const taskId = this.getAttribute('data-task-id');
                const frequencyContainer = document.getElementById(`frequency_container_${taskId}`);
                const frequencySelect = frequencyContainer.querySelector('.frequency-select');
                
                // Strict Display Logic
                if (this.value === 'recurring') {
                    // Show frequency dropdown when Recurring is selected
                    frequencyContainer.style.display = 'block';
                } else if (this.value === 'non-recurring') {
                    // Hide frequency dropdown when Non-Recurring is selected
                    frequencyContainer.style.display = 'none';
                    
                    // State Reset Rule: Clear/reset the frequency selection
                    frequencySelect.value = 'monthly'; // Reset to default value (Monthly)
                }
            });
        });
        
        // Original functionality remains unchanged below
        
        // Reassign modal functionality
        const reassignModal = document.getElementById('reassignModal');
        if (reassignModal) {
            reassignModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const taskId = button.getAttribute('data-task-id');
                const taskName = button.getAttribute('data-task-name');
                
                // Update modal content
                document.getElementById('modalTaskInfo').textContent = 
                    `Reassign task: ${taskName} to another staff member.`;
                
                // Update form action
                const form = document.getElementById('reassignForm');
                form.action = "{{ route('admin.tasks.reassign', '') }}/" + taskId;
                
                // Clear previous selection
                document.getElementById('reassign_staff').value = '';
                document.getElementById('reassign_notes').value = '';
            });
        }
        
        // Form submission handling for assign forms
        document.querySelectorAll('.assign-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const assignBtn = this.querySelector('.assign-btn');
                const staffSelect = this.querySelector('select[name="assigned_to"]');
                
                if (!staffSelect.value) {
                    e.preventDefault();
                    staffSelect.focus();
                    staffSelect.classList.add('is-invalid');
                    
                    // Add error message
                    if (!staffSelect.nextElementSibling?.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'Please select a staff member';
                        staffSelect.parentNode.appendChild(errorDiv);
                    }
                    return false;
                }
                
                // Disable button and show loading
                assignBtn.disabled = true;
                assignBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Assigning...';
            });
        });
        
        // Remove invalid class when user starts typing/selecting
        document.querySelectorAll('select[name="assigned_to"]').forEach(select => {
            select.addEventListener('change', function() {
                this.classList.remove('is-invalid');
                const errorDiv = this.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.remove();
                }
            });
        });
        
        // Simple search functionality for the table
        const searchInput = document.getElementById('taskSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const table = document.getElementById('tasksTable');
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                let visibleCount = 0;
                
                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    let textContent = row.textContent || row.innerText;
                    
                    if (textContent.toLowerCase().indexOf(filter) > -1) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                }
                
                // Update count display
                const countElement = document.querySelector('.text-muted:last-child');
                if (countElement) {
                    countElement.textContent = `Showing ${visibleCount} of ${rows.length} task(s)`;
                }
            });
        }
        
        // Auto-resize textarea in admin notes
        document.querySelectorAll('textarea[name="admin_notes"]').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
    });
</script>
@endpush