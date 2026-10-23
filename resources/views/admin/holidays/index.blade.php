@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2">Holiday Calendar</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Holidays</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <!-- Add Holiday Button -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Holiday
                </button>
                
                <!-- Mark Default Holidays Button -->
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#defaultHolidaysModal">
                    <i class="bi bi-calendar-check me-1"></i> Mark Default Holidays
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar View Tabs -->
    <div class="section-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <!--<h4 class="section-title mb-0">Holiday Calendar</h4>-->
                <!--<p class="text-muted mb-0">View and manage holidays in different calendar views</p>-->
            </div>
            <div class="btn-group" role="group" aria-label="Calendar view options">
                <button type="button" id="monthView" class="btn btn-outline-primary active">Month</button>
                <button type="button" id="weekView" class="btn btn-outline-primary">Week</button>
                <button type="button" id="dayView" class="btn btn-outline-primary">Day</button>
                <button type="button" id="listView" class="btn btn-outline-primary">List</button>
            </div>
        </div>
        
        <!-- Calendar Container -->
        <div id="calendarContainer">
            <div id="calendar"></div>
        </div>
        
        <!-- List View (Hidden by default) -->
        <div id="listViewContainer" class="d-none">
            <div class="table-responsive">
                <table class="table table-hover datatable" id="holidaysTable">
                    <thead>
                        <tr>
                            <th width="120">Date</th>
                            <th>Holiday Name</th>
                            <th width="100">Type</th>
                            <th width="120">Status</th>
                            <th width="80" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($holidays as $holiday)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($holiday->date)->format('d M Y') }}</strong><br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($holiday->date)->format('l') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="color-indicator me-2" style="width: 12px; height: 12px; background-color: {{ $holiday->color }}; border-radius: 2px;"></div>
                                    {{ $holiday->title }}
                                </div>
                            </td>
                            <td>
                                @if($holiday->type === 'custom')
                                <span class="badge bg-info">Custom</span>
                                @elseif($holiday->type === 'weekend')
                                <span class="badge bg-warning">Weekend</span>
                                @else
                                <span class="badge bg-secondary">{{ $holiday->type }}</span>
                                @endif
                            </td>
                            <td>
                                @if($holiday->is_default)
                                <span class="badge bg-success">Default</span>
                                @else
                                <span class="badge bg-primary">Custom</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger delete-holiday-btn" data-id="{{ $holiday->id }}" data-title="{{ $holiday->title }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Holiday Modal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addHolidayForm" action="{{ route('admin.holidays.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="holidayDate" class="form-label">Date</label>
                        <input type="date" class="form-control" id="holidayDate" name="date" 
                               value="{{ old('date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="holidayTitle" class="form-label">Holiday Name</label>
                        <input type="text" class="form-control" id="holidayTitle" name="title" 
                               placeholder="e.g., New Year's Day" required>
                    </div>
                    <div class="mb-3">
                        <label for="holidayColor" class="form-label">Color</label>
                        <div class="d-flex gap-2">
                            @php
                                $colorOptions = [
                                    '#2563eb' => 'Blue',
                                    '#dc2626' => 'Red',
                                    '#059669' => 'Green',
                                    '#d97706' => 'Amber',
                                    '#7c3aed' => 'Purple',
                                    '#db2777' => 'Pink',
                                ];
                            @endphp
                            @foreach($colorOptions as $color => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="color" 
                                           id="color{{ $loop->index }}" value="{{ $color }}" 
                                           {{ $loop->first ? 'checked' : '' }}>
                                    <label class="form-check-label" for="color{{ $loop->index }}">
                                        <div class="color-option" style="background-color: {{ $color }}; width: 20px; height: 20px; border-radius: 4px;"></div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Holiday</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Default Holidays Modal -->
<!-- Mark Holiday Modal -->
<div class="modal fade" id="defaultHolidaysModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="defaultHolidaysForm" action="{{ route('admin.holidays.defaults') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <p class="text-muted mb-3">Mark days for default Holidays for the current year</p>

                    <div class="mb-3">
                        <div class="d-flex flex-wrap gap-3">
                            @php
                                $days = [
                                    'sunday' => 'Sunday',
                                    'monday' => 'Monday',
                                    'tuesday' => 'Tuesday',
                                    'wednesday' => 'Wednesday',
                                    'thursday' => 'Thursday',
                                    'friday' => 'Friday',
                                    'saturday' => 'Saturday',
                                ];
                            @endphp

                            @foreach($days as $key => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]"
                                           id="day_{{ $key }}" value="{{ $key }}">
                                    <label class="form-check-label" for="day_{{ $key }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Occasion</label>
                        <input type="text" class="form-control" name="occasion" placeholder="Occasion name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Year</label>
                        <select class="form-select" name="year" required>
                            @for($y = now()->year - 1; $y <= now()->year + 3; $y++)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteHolidayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteHolidayTitle"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteHolidayForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<!-- FullCalendar CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
<style>
    #calendar {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        min-height: 600px;
    }
    
    .fc .fc-toolbar {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
    }
    
    .fc .fc-button {
        background-color: var(--sharpedge-blue);
        border-color: var(--sharpedge-blue);
        color: white;
    }
    
    .fc .fc-button:hover {
        background-color: #1e40af;
        border-color: #1e40af;
        color: white;
    }
    
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background-color: #1e40af;
        border-color: #1e40af;
        color: white;
    }
    
    .fc-event {
        border-radius: 4px;
        border: none;
        padding: 2px 4px;
        font-size: 0.85rem;
        cursor: pointer;
    }
    
    .fc-day-today {
        background-color: rgba(59, 130, 246, 0.1) !important;
    }
    
    .color-option {
        cursor: pointer;
        border: 2px solid transparent;
        transition: border 0.2s;
    }
    
    .form-check-input:checked + .form-check-label .color-option {
        border: 2px solid #1e293b;
    }
    
    .color-indicator {
        flex-shrink: 0;
    }
    
    #listViewContainer {
        margin-top: 20px;
    }
    
    .fc .fc-button-primary:disabled {
        background-color: #94a3b8;
        border-color: #94a3b8;
    }
    
    .fc .fc-button-group .fc-button {
        margin: 0;
    }
    
    .fc .fc-daygrid-day-number {
        font-weight: 500;
        padding: 4px;
    }
    
    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        font-weight: bold;
        color: #2563eb;
    }
</style>
@endpush

@push('scripts')
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
    // Wait for all resources to load
    window.addEventListener('load', function() {
        // Check if FullCalendar is loaded
        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar library failed to load. Please check the CDN.');
            document.getElementById('calendar').innerHTML = 
                '<div class="alert alert-danger">Calendar failed to load. Please check your internet connection and refresh the page.</div>';
            return;
        }
        
        // Initialize FullCalendar
        const calendarEl = document.getElementById('calendar');
        
        // Prepare events from Laravel data
        const events = @json($calendarEvents);
        
        try {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events,
                eventClick: function(info) {
                    const eventId = info.event.id;
                    const eventTitle = info.event.title;
                    
                    // Show delete confirmation modal
                    document.getElementById('deleteHolidayTitle').textContent = eventTitle;
                    document.getElementById('deleteHolidayForm').action = `/admin/holidays/${eventId}`;
                    
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteHolidayModal'));
                    deleteModal.show();
                },
                height: 'auto',
                dayMaxEvents: 3,
                eventDisplay: 'block',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                }
            });
            
            calendar.render();
            
            // View toggle functionality
            const monthBtn = document.getElementById('monthView');
            const weekBtn = document.getElementById('weekView');
            const dayBtn = document.getElementById('dayView');
            const listBtn = document.getElementById('listView');
            const calendarContainer = document.getElementById('calendarContainer');
            const listViewContainer = document.getElementById('listViewContainer');
            
            function activateButton(button) {
                [monthBtn, weekBtn, dayBtn, listBtn].forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            }
            
            monthBtn.addEventListener('click', function() {
                activateButton(this);
                calendar.changeView('dayGridMonth');
                listViewContainer.classList.add('d-none');
                calendarContainer.classList.remove('d-none');
            });
            
            weekBtn.addEventListener('click', function() {
                activateButton(this);
                calendar.changeView('timeGridWeek');
                listViewContainer.classList.add('d-none');
                calendarContainer.classList.remove('d-none');
            });
            
            dayBtn.addEventListener('click', function() {
                activateButton(this);
                calendar.changeView('timeGridDay');
                listViewContainer.classList.add('d-none');
                calendarContainer.classList.remove('d-none');
            });
            
            listBtn.addEventListener('click', function() {
                activateButton(this);
                calendarContainer.classList.add('d-none');
                listViewContainer.classList.remove('d-none');
            });
        } catch (error) {
            console.error('Error initializing calendar:', error);
            document.getElementById('calendar').innerHTML = 
                '<div class="alert alert-danger">Error loading calendar: ' + error.message + '</div>';
        }
        
        // Delete holiday button handlers
        document.querySelectorAll('.delete-holiday-btn').forEach(button => {
            button.addEventListener('click', function() {
                const holidayId = this.getAttribute('data-id');
                const holidayTitle = this.getAttribute('data-title');
                
                document.getElementById('deleteHolidayTitle').textContent = holidayTitle;
                document.getElementById('deleteHolidayForm').action = `/admin/holidays/${holidayId}`;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteHolidayModal'));
                deleteModal.show();
            });
        });
        
        // Form submission handling with feedback
        const addHolidayForm = document.getElementById('addHolidayForm');
        const defaultHolidaysForm = document.getElementById('defaultHolidaysForm');
        
        if (addHolidayForm) {
            addHolidayForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Adding...';
                submitBtn.disabled = true;
            });
        }
        
        if (defaultHolidaysForm) {
            defaultHolidaysForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...';
                submitBtn.disabled = true;
            });
        }
        
        // Show success/error messages
        @if(session('success'))
            showToast('Success', '{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showToast('Error', '{{ session('error') }}', 'error');
        @endif
        
        function showToast(title, message, type) {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type === 'success' ? 'success' : 'danger'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}:</strong> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', function() {
                document.body.removeChild(toast);
            });
        }
    });
</script>
@endpush