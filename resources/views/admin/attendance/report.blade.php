@extends('layouts.admin')

@section('content')
<!-- Header -->
<header class="header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">Attendance Report</h2>
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted">{{ Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</div>
                <div class="badge bg-light text-dark border">
                    {{ $staffs->count() }} Staff
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <!--<button class="btn btn-outline-primary btn-sm" onclick="window.print()">-->
            <!--    <i class="bi bi-printer me-1"></i> Print-->
            <!--</button>-->
        </div>
    </div>
</header>

<!-- Filters -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.attendance.report') }}" class="row g-2">
            <div class="col-md-2">
                <select name="staff_id" id="staff_id" class="form-select form-select-sm">
                    <option value="">All Staff</option>
                    @foreach($staffs as $staff)
                        <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                            {{ $staff->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!--<div class="col-md-2">-->
            <!--    <select name="department" id="department" class="form-select form-select-sm">-->
            <!--        <option value="">All Departments</option>-->
            <!--        @foreach($departments as $dept)-->
            <!--            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>-->
            <!--                {{ $dept }}-->
            <!--            </option>-->
            <!--        @endforeach-->
            <!--    </select>-->
            <!--</div>-->
            
            <!-- Designation dropdown uses roles -->
            <div class="col-md-2">
                <select name="designation" id="designation" class="form-select form-select-sm">
                    <option value="">All Designations</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('designation') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <select name="month" id="month" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div class="col-md-2">
                <select name="year" id="year" class="form-select form-select-sm">
                    @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-filter me-1"></i> Apply
                </button>
                <a href="{{ route('admin.attendance.report') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Stats Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card border-0 bg-success bg-opacity-10">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-25 p-2 rounded me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">Present</h6>
                        <h3 class="mb-0">
                            @php
                                $totalPresent = 0;
                                foreach($attendanceGrid as $staffDays) {
                                    foreach($staffDays as $day => $data) {
                                        if($data['status'] == 'P') $totalPresent++;
                                    }
                                }
                            @endphp
                            {{ $totalPresent }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="card border-0 bg-secondary bg-opacity-10">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-secondary bg-opacity-25 p-2 rounded me-3">
                        <i class="bi bi-circle text-secondary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">Not Marked</h6>
                        <h3 class="mb-0">
                            @php
                                $totalNM = 0;
                                foreach($attendanceGrid as $staffDays) {
                                    foreach($staffDays as $day => $data) {
                                        if($data['status'] == 'NM') $totalNM++;
                                    }
                                }
                            @endphp
                            {{ $totalNM }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="card border-0 bg-primary bg-opacity-10">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-25 p-2 rounded me-3">
                        <i class="bi bi-calendar-event text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">Holidays</h6>
                        <h3 class="mb-0">
                            @php
                                $totalHolidays = 0;
                                foreach($attendanceGrid as $staffDays) {
                                    foreach($staffDays as $day => $data) {
                                        if($data['status'] == 'H') $totalHolidays++;
                                    }
                                }
                            @endphp
                            {{ $totalHolidays }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="card border-0 bg-warning bg-opacity-10">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-25 p-2 rounded me-3">
                        <i class="bi bi-clock text-warning"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">In Progress</h6>
                        <h3 class="mb-0">
                            @php
                                $totalInProgress = 0;
                                foreach($attendanceGrid as $staffDays) {
                                    foreach($staffDays as $day => $data) {
                                        if($data['status'] == 'IN') $totalInProgress++;
                                    }
                                }
                            @endphp
                            {{ $totalInProgress }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="card border-0" style="background-color: rgba(111, 66, 193, 0.1);">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="p-2 rounded me-3" style="background-color: rgba(111, 66, 193, 0.2);">
                        <i class="bi bi-calendar2-check" style="color: #7c3aed;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">On Leave</h6>
                        <h3 class="mb-0">
                            @php
                                $totalLeave = 0;
                                foreach($attendanceGrid as $staffDays) {
                                    foreach($staffDays as $day => $data) {
                                        if($data['status'] == 'L') $totalLeave++;
                                    }
                                }
                            @endphp
                            {{ $totalLeave }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="card border-0 bg-info bg-opacity-10">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-25 p-2 rounded me-3">
                        <i class="bi bi-calendar-week text-info"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">Total Days</h6>
                        <h3 class="mb-0">
                            @php
                                $totalDays = $staffs->count() * $daysInMonth;
                            @endphp
                            {{ number_format($totalDays) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded">
    <div class="text-muted small">Legend:</div>
    <div class="d-flex flex-wrap gap-3">
        <div class="d-flex align-items-center gap-1">
            <div class="status-icon status-present"></div>
            <span class="small">Present (P)</span>
        </div>
        <div class="d-flex align-items-center gap-1">
            <div class="status-icon status-holiday"></div>
            <span class="small">Holiday (H)</span>
        </div>
        <div class="d-flex align-items-center gap-1">
            <div class="status-icon status-leave"></div>
            <span class="small">On Leave (L)</span>
        </div>
        <div class="d-flex align-items-center gap-1">
            <div class="status-icon status-inprogress"></div>
            <span class="small">In Progress (IN)</span>
        </div>
        <div class="d-flex align-items-center gap-1">
            <div class="status-icon status-nm"></div>
            <span class="small">Not Marked (Blank)</span>
        </div>
    </div>
</div>

<!-- Attendance Grid -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive attendance-grid-container">
            <table class="table table-bordered mb-0 attendance-grid">
                <thead>
                    <tr>
                        <th class="bg-light border-end sticky-column" style="width: 280px; min-width: 280px;">
                            <div class="d-flex justify-content-between align-items-center p-2">
                                <span class="fw-semibold">Employee / Department</span>
                                <span class="text-muted small">Days</span>
                            </div>
                        </th>
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = Carbon\Carbon::create($year, $month, $day);
                                $isWeekend = $date->isWeekend();
                                $isToday = $date->isToday();
                            @endphp
                            <th class="text-center p-1 {{ $isWeekend ? 'bg-light-blue' : '' }} {{ $isToday ? 'border-primary border-2' : '' }}" 
                                style="width: 38px; min-width: 38px;">
                                <div class="d-flex flex-column small">
                                    <span class="text-muted">{{ $date->format('D') }}</span>
                                    <span class="{{ $isWeekend ? 'text-primary' : 'fw-medium' }} {{ $isToday ? 'text-primary' : '' }}">
                                        {{ $day }}
                                    </span>
                                </div>
                            </th>
                        @endfor
                        <th class="bg-light border-start sticky-column-right text-center" style="width: 120px; min-width: 120px;">
                            <div class="p-2">
                                <span class="fw-semibold">Summary</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $staff)
                        @php
                            // Retrieve pre‑computed summary data from the controller
                            $summary = $staffSummaries[$staff->id] ?? null;
                            if ($summary) {
                                $staffPresent   = $summary['present_days'];
                                $staffLeave     = $summary['leave_days'];
                                $staffInProgress= $summary['inprogress_days'];
                                $workedHoursText= $summary['worked_hours_formatted'];
                            } else {
                                // Fallback (should never happen)
                                $staffPresent = $staffLeave = $staffInProgress = 0;
                                $workedHoursText = '0h 0m';
                            }
                            
                            // Optionally calculate working days (excluding holidays) if needed
                            // Holidays are already in the grid, but you could also store them in summary.
                            // For now we'll just use $daysInMonth minus leave days.
                            $workingDays = $daysInMonth - $staffLeave;
                        @endphp
                        <tr>
                            <td class="sticky-column bg-white border-end">
                                <div class="p-2">
                                    <div class="fw-semibold">{{ $staff->name }}</div>
                                    <div class="small text-muted">{{ $staff->role->name ?? '—' }}</div>
                                    <div class="small text-muted">{{ $staff->department }}</div>
                                </div>
                            </td>
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $attendance = $attendanceGrid[$staff->id][$day] ?? null;
                                    $date = Carbon\Carbon::create($year, $month, $day);
                                    $isWeekend = $date->isWeekend();
                                    $isToday = $date->isToday();
                                @endphp
                                <td class="text-center p-0 border-1 {{ $isWeekend ? 'bg-light-blue' : '' }} {{ $isToday ? 'border-primary' : '' }}"
                                    data-bs-toggle="tooltip"
                                    data-bs-trigger="hover"
                                    data-bs-html="true"
                                    data-bs-placement="top"
                                    tabindex="-1"
                                    title="
                                    <div class='text-start p-2'>
                                        <strong>{{ $date->format('l, F j, Y') }}</strong><br>
                                        <small class='text-muted'>{{ $staff->name }}</small>
                                        <hr class='my-2'>
                                        @if($attendance['status'] == 'P')
                                            <div class='text-success'><i class='bi bi-check-circle me-1'></i> Present</div>
                                        @elseif($attendance['status'] == 'NM')
                                            <div><i class='bi bi-circle me-1'></i> Not Marked</div>
                                        @elseif($attendance['status'] == 'H')
                                            <div class='text-primary'><i class='bi bi-calendar-event me-1'></i> Holiday</div>
                                        @elseif($attendance['status'] == 'IN')
                                            <div class='text-warning'><i class='bi bi-clock me-1'></i> In Progress</div>
                                        @elseif($attendance['status'] == 'L')
                                            <div class='text-purple'><i class='bi bi-calendar2-check me-1'></i> On Leave</div>
                                        @else
                                            <div>Unknown Status</div>
                                        @endif
                                    </div>">
                                    
                                    @if($attendance['status'] == 'P')
                                        <div class="status-cell status-present" title="Present">
                                            <i class="bi bi-check-circle"></i>
                                        </div>
                                    @elseif($attendance['status'] == 'H')
                                        <div class="status-cell status-holiday" title="Holiday">
                                            H
                                        </div>
                                    @elseif($attendance['status'] == 'IN')
                                        <div class="status-cell status-inprogress" title="In Progress">
                                            <i class="bi bi-clock"></i>
                                        </div>
                                    @elseif($attendance['status'] == 'L')
                                        <div class="status-cell status-leave" title="On Leave">
                                            <i class="bi bi-calendar2-check"></i>
                                        </div>
                                    @elseif($attendance['status'] == 'NM')
                                        <!-- Empty cell for Not Marked -->
                                        <div class="status-cell status-nm" title="Not Marked"></div>
                                    @endif
                                </td>
                            @endfor
                            <td class="sticky-column-right bg-white border-start text-center">
                                <div class="p-2">
                                    <div class="mb-1">
                                        <span class="fw-medium">{{ $staffPresent }}/{{ $workingDays }}</span>
                                        <div class="small text-muted">Present Days</div>
                                    </div>
                                    <div class="small">
                                        <!-- ✅ Now using the pre‑formatted string from the controller -->
                                        <span class="text-success">{{ $workedHoursText }}</span>
                                        <div class="text-muted">Worked Hours</div>
                                    </div>
                                    @if($staffLeave > 0)
                                        <div class="small text-purple mt-1">
                                            {{ $staffLeave }} Leave
                                        </div>
                                    @endif
                                    @if($staffInProgress > 0)
                                        <div class="small text-warning mt-1">
                                            {{ $staffInProgress }} In Progress
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 2 }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people display-5"></i>
                                    <p class="mt-2">No staff members found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Month Navigation -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <div>
        @php
            $prevMonth = Carbon\Carbon::create($year, $month, 1)->subMonth();
            $nextMonth = Carbon\Carbon::create($year, $month, 1)->addMonth();
        @endphp
        <a href="{{ route('admin.attendance.report', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" 
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-chevron-left me-1"></i> Previous Month
        </a>
    </div>
    
    <div class="text-muted small">
        Report generated: {{ now()->format('M d, Y h:i A') }}
    </div>
    
    <div>
        <a href="{{ route('admin.attendance.report', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" 
           class="btn btn-outline-secondary btn-sm">
            Next Month <i class="bi bi-chevron-right ms-1"></i>
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --color-present: #198754;
        --color-holiday: #0d6efd;
        --color-leave: #7c3aed;
        --color-inprogress: #ffc107;
        --color-nm: #f8f9fa;
        --color-weekend: #e3f2fd;
        --color-border: #d1d9e6;
        --color-hover: rgba(13, 110, 253, 0.08);
        --color-grid-bg: #f8fafc;
    }
    
    /* Overall darker UI */
    .card {
        background-color: var(--color-grid-bg);
        border-color: #e2e8f0;
    }
    
    .attendance-grid-container {
        max-height: 70vh;
        overflow-x: auto;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: var(--color-grid-bg);
    }
    
    /* Stronger table borders */
    .attendance-grid.table-bordered th,
    .attendance-grid.table-bordered td {
        border: 1px solid var(--color-border) !important;
    }
    
    .attendance-grid.table-bordered {
        border: 2px solid #cbd5e1;
    }
    
    .sticky-column {
        position: sticky;
        left: 0;
        z-index: 10;
        box-shadow: 3px 0 5px rgba(0,0,0,0.08);
        background-color: white !important;
    }
    
    .sticky-column-right {
        position: sticky;
        right: 0;
        z-index: 10;
        box-shadow: -3px 0 5px rgba(0,0,0,0.08);
        background-color: white !important;
    }
    
    .attendance-grid thead th {
        background-color: #f1f5f9 !important;
        font-weight: 600;
        border-bottom: 2px solid #cbd5e1 !important;
        position: sticky;
        top: 0;
        z-index: 5;
        vertical-align: middle;
    }
    
    .attendance-grid tbody td {
        vertical-align: middle;
        border: 1px solid var(--color-border) !important;
        background-color: white;
        transition: all 0.2s ease;
    }
    
    /* Stronger weekend background */
    .bg-light-blue {
        background-color: var(--color-weekend) !important;
    }
    
    /* Hover effect for cells */
    .attendance-grid tbody td:hover {
        background-color: var(--color-hover) !important;
        transform: scale(1.02);
        z-index: 2;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Status Icons - Increased opacity */
    .status-cell {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin: 0 auto;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    
    .status-cell:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        z-index: 3;
    }
    
    .status-present {
        background-color: rgba(25, 135, 84, 0.25) !important;
        color: var(--color-present) !important;
        border-color: rgba(25, 135, 84, 0.3) !important;
    }
    
    .status-holiday {
        background-color: rgba(13, 110, 253, 0.25) !important;
        color: var(--color-holiday) !important;
        font-weight: 700;
        border-color: rgba(13, 110, 253, 0.3) !important;
    }
    
    .status-leave {
        background-color: rgba(124, 58, 237, 0.25) !important;
        color: var(--color-leave) !important;
        border-color: rgba(124, 58, 237, 0.3) !important;
    }
    
    .status-inprogress {
        background-color: rgba(255, 193, 7, 0.3) !important;
        color: var(--color-inprogress) !important;
        border-color: rgba(255, 193, 7, 0.4) !important;
    }
    
    .status-nm {
        background-color: rgba(248, 249, 250, 0.8) !important;
        border: 1px dashed #cbd5e1 !important;
        position: relative;
    }
    
    .status-nm:hover::after {
        content: "NM";
        position: absolute;
        font-size: 10px;
        color: #94a3b8;
        font-weight: 600;
    }
    
    /* Legend Icons */
    .status-icon {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: 1px solid;
    }
    
    .status-icon.status-present {
        background-color: rgba(25, 135, 84, 0.2);
        color: var(--color-present);
        border-color: rgba(25, 135, 84, 0.3);
    }
    
    .status-icon.status-holiday {
        background-color: rgba(13, 110, 253, 0.2);
        color: var(--color-holiday);
        font-weight: 700;
        border-color: rgba(13, 110, 253, 0.3);
    }
    
    .status-icon.status-leave {
        background-color: rgba(124, 58, 237, 0.2);
        color: var(--color-leave);
        border-color: rgba(124, 58, 237, 0.3);
    }
    
    .status-icon.status-inprogress {
        background-color: rgba(255, 193, 7, 0.25);
        color: var(--color-inprogress);
        border-color: rgba(255, 193, 7, 0.4);
    }
    
    .status-icon.status-nm {
        background-color: rgba(248, 249, 250, 0.9);
        border: 1px dashed #cbd5e1;
    }
    
    .text-purple {
        color: #7c3aed;
    }
    
    /* Tooltip styling */
    .tooltip-inner {
        max-width: 300px;
        text-align: left;
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        border-radius: 8px;
        background-color: white;
        color: #334155;
        border: 1px solid #e2e8f0;
    }
    
    /* Custom scrollbar */
    .attendance-grid-container::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
    
    .attendance-grid-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 6px;
    }
    
    .attendance-grid-container::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 6px;
        border: 2px solid #f1f5f9;
    }
    
    .attendance-grid-container::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .attendance-grid-container {
            font-size: 0.85rem;
        }
        
        .sticky-column {
            width: 200px;
            min-width: 200px;
        }
        
        .sticky-column-right {
            width: 100px;
            min-width: 100px;
        }
        
        .attendance-grid thead th {
            font-size: 0.8rem;
            padding: 8px 2px !important;
        }
        
        .status-cell {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }
    }
    
    @media print {
        .attendance-grid-container {
            overflow: visible;
            max-height: none;
        }
        
        .sticky-column,
        .sticky-column-right {
            position: static;
            box-shadow: none;
        }
        
        .attendance-grid thead th {
            position: static;
        }
        
        .status-nm {
            background-color: #f8f9fa !important;
            border: 1px dashed #dee2e6 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips with hover-only behavior
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            // Create tooltip with hover-only trigger
            const tooltip = new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover',          // Only on hover, no click/focus
                html: true,
                delay: { show: 150, hide: 100 },
                boundary: 'viewport',
                container: 'body'           // Prevents tooltip from getting stuck inside table
            });

            // 🔒 Prevent click from toggling tooltip
            tooltipTriggerEl.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });

            return tooltip;
        });
        
        // Highlight today's column
        const today = new Date();
        const currentMonth = today.getMonth() + 1;
        const currentYear = today.getFullYear();
        const currentDay = today.getDate();
        
        if (parseInt(document.getElementById('month').value) === currentMonth && 
            parseInt(document.getElementById('year').value) === currentYear) {
            // Today is in the current view – highlight the column
            // Note: We use the column index (day + 1) because first column is employee info
            const dayIndex = currentDay + 1; // +1 because employee column is first
            document.querySelectorAll(`.attendance-grid tbody tr td:nth-child(${dayIndex})`).forEach(cell => {
                cell.classList.add('today-highlight');
            });
        }
        
        // Auto-refresh for current month view
        if (parseInt(document.getElementById('month').value) === currentMonth && 
            parseInt(document.getElementById('year').value) === currentYear) {
            // Refresh every 5 minutes for current month
            setTimeout(() => {
                window.location.reload();
            }, 300000); // 5 minutes
        }
        
        // NOTE: Month/Year change listeners removed – user must click Apply button
        // (fix for filter not working properly)
        
        // If department dropdown is ever uncommented, add listener here.
        // Currently commented out, so no listener needed.
    });
</script>
@endpush