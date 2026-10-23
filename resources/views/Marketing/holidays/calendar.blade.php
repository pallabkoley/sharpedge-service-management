@extends('layouts.marketing')

@section('title', 'Holiday Calendar')
@section('page-title', 'Holiday Calendar')
@section('page-subtitle', 'View upcoming holidays and time off')

@section('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    /* Calendar Custom Styles */
    #calendar {
        background-color: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        padding: 1.5rem;
    }
    
    .fc {
        font-family: 'Inter', sans-serif;
    }
    
    .fc .fc-toolbar {
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--dark-color);
    }
    
    .fc .fc-button {
        background-color: white;
        border: 1px solid var(--gray-300);
        color: var(--gray-700);
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .fc .fc-button:hover {
        background-color: var(--gray-100);
        border-color: var(--gray-400);
    }
    
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    
    .fc .fc-button-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .fc .fc-daygrid-day-number {
        color: var(--gray-700);
        font-weight: 500;
        padding: 8px;
    }
    
    .fc .fc-daygrid-day.fc-day-today {
        background-color: rgba(59, 130, 246, 0.05);
    }
    
    .fc .fc-col-header-cell {
        background-color: var(--gray-100);
        padding: 12px 0;
    }
    
    .fc .fc-col-header-cell-cushion {
        color: var(--gray-700);
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
        cursor: default;
    }
    
    .fc .fc-daygrid-event-dot {
        display: none;
    }
    
    .fc .fc-event-title {
        font-weight: 500;
    }
    
    .fc .fc-event-time {
        font-weight: 400;
        opacity: 0.9;
    }
    
    /* Holiday Type Colors */
    .holiday-national {
        background-color: #ef4444;
        border-color: #ef4444;
    }
    
    .holiday-state {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }
    
    .holiday-company {
        background-color: #10b981;
        border-color: #10b981;
    }
    
    .holiday-religious {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
    }
    
    .holiday-other {
        background-color: #f59e0b;
        border-color: #f59e0b;
    }
    
    /* Stats Cards */
    .holiday-stats-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem;
        border: 1px solid var(--gray-200);
        height: 100%;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .holiday-stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .holiday-stats-content h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .holiday-stats-content p {
        color: var(--gray-500);
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
        background: var(--gray-100);
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
        color: var(--gray-700);
    }
    
    /* Filter Section */
    .filter-section {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid var(--gray-200);
        margin-bottom: 1.5rem;
    }
    
    .filter-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-color);
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
        border: 1px solid var(--gray-300);
        background: white;
        border-radius: 0.375rem;
        color: var(--gray-700);
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .year-btn:hover {
        background: var(--gray-100);
    }
    
    .year-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    /* Status badges */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-national {
        background-color: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    
    .status-state {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
    
    .status-company {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    
    .status-religious {
        background-color: rgba(139, 92, 246, 0.1);
        color: #8b5cf6;
    }
    
    .status-other {
        background-color: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    
    .status-weekend {
        background-color: rgba(249, 115, 22, 0.1);
        color: #f97316;
    }
    
    /* Section card */
    .section-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid var(--gray-200);
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 1.5rem;
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
    
    
    <!-- Filter Section -->

    
    <!-- Calendar Container -->
    <div class="row">
        <div class="col-12">
            <div id="calendar"></div>
        </div>
    </div>
    
    <!-- Legend -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="holiday-legend">
                <div class="legend-item">
                    <div class="legend-color bg-danger"></div>
                    <span class="legend-label">National Holiday</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-primary"></div>
                    <span class="legend-label">State/Regional</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-success"></div>
                    <span class="legend-label">Company Holiday</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #8b5cf6;"></div>
                    <span class="legend-label">Religious</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-warning"></div>
                    <span class="legend-label">Other</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Holidays List -->
   
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
                    'other': '#f59e0b',
                    'weekend': info.event.backgroundColor || '#f97316',
                    'custom': info.event.backgroundColor || '#db2777',
                };
                
                const color = typeColorMap[type] || info.event.backgroundColor || '#6b7280';
                info.el.style.backgroundColor = color;
                info.el.style.borderColor = color;
                
                // Add native browser tooltip for basic info
                const tooltipContent = `${info.event.title} (${info.event.extendedProps.type})`;
                info.el.setAttribute('title', tooltipContent);
                
                // Optional: Add custom hover effect
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
                    ${event.extendedProps.description ? `\nNotes: ${event.extendedProps.description}` : ''}
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
        
        // Add active class to current month/year in filter
        document.querySelectorAll('.year-btn').forEach(btn => {
            if (btn.classList.contains('active')) {
                btn.style.pointerEvents = 'none';
            }
        });
    });
</script>
@endsection