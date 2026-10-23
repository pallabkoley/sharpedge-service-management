@extends('layouts.Backoffice')

@section('title', 'Holiday Calendar')
@section('page-title', 'Holiday Calendar')
@section('page-subtitle', 'View all holidays')

@section('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    /* Calendar Custom Styles */
    #calendar {
        background-color: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        padding: 1.5rem;
    }
    
    .fc {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .fc .fc-toolbar {
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
    }
    
    .fc .fc-button {
        background-color: white;
        border: 1px solid #d1d5db;
        color: #374151;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .fc .fc-button:hover {
        background-color: #f3f4f6;
        border-color: #9ca3af;
    }
    
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    
    .fc .fc-button-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .fc .fc-daygrid-day-number {
        color: #374151;
        font-weight: 500;
        padding: 8px;
    }
    
    .fc .fc-daygrid-day.fc-day-today {
        background-color: rgba(59, 130, 246, 0.05);
    }
    
    .fc .fc-col-header-cell {
        background-color: #f3f4f6;
        padding: 12px 0;
    }
    
    .fc .fc-col-header-cell-cushion {
        color: #374151;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    
    .fc .fc-event {
        border: none;
        border-radius: 4px;
        font-size: 0.75rem;
        padding: 2px 6px;
        margin: 1px 0;
        cursor: pointer;
    }
    
    .fc .fc-daygrid-event-dot {
        display: none;
    }
    
    .fc .fc-event-title {
        font-weight: 500;
    }
    
    /* Stats Cards - Matching Backoffice Style */
    .holiday-stats-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 20px;
        height: 100%;
        border: none;
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .holiday-stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    
    .holiday-stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    
    .holiday-stats-content h3 {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        margin: 10px 0 5px 0;
    }
    
    .holiday-stats-content p {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    /* Legend */
    .holiday-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 1rem;
        padding: 1rem;
        background: #f3f4f6;
        border-radius: 0.5rem;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }
    
    .legend-label {
        font-size: 0.875rem;
        color: #374151;
    }
    
    /* Filter Section */
    .filter-section {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 20px;
        border: none;
        margin-bottom: 1.5rem;
    }
    
    .filter-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1rem;
    }
    
    .year-selector {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .year-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        background: white;
        border-radius: 0.375rem;
        color: #374151;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .year-btn:hover {
        background: #f3f4f6;
        text-decoration: none;
    }
    
    .year-btn.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    
    .month-selector {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    
    .month-btn {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        background: white;
        border-radius: 0.375rem;
        color: #374151;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        font-size: 0.875rem;
    }
    
    .month-btn:hover {
        background: #f3f4f6;
        text-decoration: none;
    }
    
    .month-btn.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    
    /* Status badges */
    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-national {
        background-color: #fee2e2;
        color: #dc2626;
    }
    
    .status-state {
        background-color: #dbeafe;
        color: #1d4ed8;
    }
    
    .status-company {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-religious {
        background-color: #e0e7ff;
        color: #4f46e5;
    }
    
    .status-other {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    /* Holidays Table */
    .holidays-table {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: none;
    }
    
    .holidays-table th {
        background-color: #3B4F6B;
        color: #ffffff;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: none;
        padding: 16px 18px;
    }
    
    .holidays-table td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .holidays-table tr:hover {
        background-color: #f8fafc;
    }
    
    .holidays-table tr:last-child td {
        border-bottom: none;
    }
    
    /* Badge styles matching backoffice */
    .badge-success {
        background-color: #d1fae5;
        color: #065f46;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-secondary {
        background-color: #f1f5f9;
        color: #64748b;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        #calendar {
            padding: 1rem;
        }
        
        .fc .fc-toolbar {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .fc .fc-toolbar-chunk {
            margin-bottom: 10px;
        }
        
        .holiday-legend {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="holiday-stats-card">
                <div class="holiday-stats-icon" style="background-color: #3b82f6;">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="holiday-stats-content">
                    <h3>{{ $totalHolidays }}</h3>
                    <p>Total Holidays</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="holiday-stats-card">
                <div class="holiday-stats-icon" style="background-color: #ef4444;">
                    <i class="fas fa-flag"></i>
                </div>
                <div class="holiday-stats-content">
                    <h3>{{ $nationalCount }}</h3>
                    <p>National Holidays</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="holiday-stats-card">
                <div class="holiday-stats-icon" style="background-color: #10b981;">
                    <i class="fas fa-building"></i>
                </div>
                <div class="holiday-stats-content">
                    <h3>{{ $companyCount }}</h3>
                    <p>Company Holidays</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="holiday-stats-card">
                <div class="holiday-stats-icon" style="background-color: #8b5cf6;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="holiday-stats-content">
                    <h3>{{ $upcomingCount }}</h3>
                    <p>Upcoming Holidays</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-title">Filter by Year & Month</div>
        
        <!-- Year Filter -->
        <div class="year-selector">
            @php
                $currentYear = date('Y');
                $years = range($currentYear - 2, $currentYear + 2);
            @endphp
            
            @foreach($years as $filterYear)
                <a href="{{ route('backoffice.holidays.calendar', ['year' => $filterYear, 'month' => $month]) }}" 
                   class="year-btn {{ $year == $filterYear ? 'active' : '' }}">
                    {{ $filterYear }}
                </a>
            @endforeach
        </div>
        
        <!-- Month Filter -->
        <div class="month-selector">
            @php
                $months = [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ];
            @endphp
            
            <a href="{{ route('backoffice.holidays.calendar', ['year' => $year]) }}" 
               class="month-btn {{ !$month ? 'active' : '' }}">
                All Months
            </a>
            
            @foreach($months as $num => $name)
                <a href="{{ route('backoffice.holidays.calendar', ['year' => $year, 'month' => $num]) }}" 
                   class="month-btn {{ $month == $num ? 'active' : '' }}">
                    {{ substr($name, 0, 3) }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="row mb-4">
        <div class="col-12">
            <div id="calendar"></div>
        </div>
    </div>
    
    <!-- Legend -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="holiday-legend">
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #ef4444;"></div>
                    <span class="legend-label">National Holiday</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #3b82f6;"></div>
                    <span class="legend-label">State/Regional</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #10b981;"></div>
                    <span class="legend-label">Company Holiday</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #8b5cf6;"></div>
                    <span class="legend-label">Religious</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #f59e0b;"></div>
                    <span class="legend-label">Other</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Holidays List -->
    <div class="row">
        <div class="col-12">
            <div class="card section-card">
                <div class="section-title">
                    <i class="fas fa-list-alt mr-2"></i>
                    Holidays for {{ $month ? date('F Y', mktime(0, 0, 0, $month, 1, $year)) : $year }}
                </div>
                <div class="p-0">
                    @if($holidays->count() > 0)
                        <div class="table-responsive">
                            <table class="table holidays-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Holiday Name</th>
                                        <th>Type</th>
                                        <th>Default</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($holidays as $holiday)
                                        <tr>
                                            <td>
                                                <strong>{{ $holiday->date->format('M d, Y') }}</strong><br>
                                                <small class="text-muted">{{ $holiday->date->format('l') }}</small>
                                            </td>
                                            <td>{{ $holiday->title }}</td>
                                            <td>
                                                @switch($holiday->type)
                                                    @case('national')
                                                        <span class="status-badge status-national">National</span>
                                                        @break
                                                    @case('state')
                                                        <span class="status-badge status-state">State</span>
                                                        @break
                                                    @case('company')
                                                        <span class="status-badge status-company">Company</span>
                                                        @break
                                                    @case('religious')
                                                        <span class="status-badge status-religious">Religious</span>
                                                        @break
                                                    @default
                                                        <span class="status-badge status-other">Other</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($holiday->is_default)
                                                    <span class="badge-success">Yes</span>
                                                @else
                                                    <span class="badge-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No holidays found for this period.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize calendar
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,listMonth'
            },
            events: @json($calendarEvents),
            eventDisplay: 'block',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            },
            eventDidMount: function(info) {
                // Set color based on holiday type
                const type = (info.event.extendedProps.type || '').toLowerCase();
                
                const typeColorMap = {
                    'national': '#ef4444',
                    'state': '#3b82f6',
                    'company': '#10b981',
                    'religious': '#8b5cf6',
                    'other': '#f59e0b'
                };
                
                const color = typeColorMap[type] || info.event.color || '#6b7280';
                info.el.style.backgroundColor = color;
                info.el.style.borderColor = color;
                
                // Add tooltip
                const tooltipContent = `${info.event.title} (${info.event.extendedProps.type})`;
                info.el.setAttribute('title', tooltipContent);
                
                // Add hover effect
                info.el.addEventListener('mouseenter', function() {
                    this.style.filter = 'brightness(1.1)';
                    this.style.transform = 'translateY(-1px)';
                });
                
                info.el.addEventListener('mouseleave', function() {
                    this.style.filter = '';
                    this.style.transform = '';
                });
            },
            dateClick: function(info) {
                // Show events on date click
                const eventsOnDay = calendar.getEvents().filter(event => {
                    return event.start.toDateString() === info.date.toDateString();
                });
                
                if (eventsOnDay.length > 0) {
                    let eventList = eventsOnDay.map(event => {
                        return `• ${event.title} (${event.extendedProps.type})`;
                    }).join('\n');
                    
                    alert(`Holidays on ${info.dateStr}:\n\n${eventList}`);
                }
            },
            eventClick: function(info) {
                // Show event details on click
                const event = info.event;
                const eventDetails = `
                    ${event.title}
                    
                    Type: ${event.extendedProps.type}
                    Date: ${event.start.toLocaleDateString()}
                    Default: ${event.extendedProps.is_default ? 'Yes' : 'No'}
                `;
                
                alert(eventDetails);
                
                // Prevent default browser behavior
                info.jsEvent.preventDefault();
            }
        });
        
        calendar.render();
        
        // Make calendar responsive on window resize
        window.addEventListener('resize', function() {
            calendar.updateSize();
        });
    });
</script>
@endsection