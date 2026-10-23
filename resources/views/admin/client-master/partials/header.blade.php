<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-7 d-flex align-items-center">
                @if($client->profile_photo)
                    <img src="{{ asset('storage/' . $client->profile_photo) }}" class="rounded-circle me-3" style="width:70px;height:70px;object-fit:cover;">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:70px;height:70px;font-size:28px;font-weight:700;">
                        {{ strtoupper(substr($client->name,0,1)) }}
                    </div>
                @endif
                <div>
                    <h4 class="mb-1 fw-bold">{{ $client->name }}</h4>
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        <span><i class="bi bi-tag me-1"></i> Code: <strong>{{ $client->client_code ?? 'N/A' }}</strong></span>
                        <span><i class="bi bi-file-earmark-text me-1"></i> PAN: <strong>{{ $client->pan ?? 'N/A' }}</strong></span>
                        <span><i class="bi bi-circle-fill me-1" style="color: {{ $client->status == 'active' ? '#10b981' : ($client->status == 'inactive' ? '#94a3b8' : '#ef4444') }};"></i> <span class="fw-semibold">{{ ucfirst($client->status ?? 'N/A') }}</span></span>
                        <span><i class="bi bi-phone me-1"></i> {{ $client->phone ?? 'N/A' }}</span>
                        <span><i class="bi bi-envelope me-1"></i> {{ $client->email ?? 'N/A' }}</span>
                        <span><i class="bi bi-building me-1"></i> {{ $client->company_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                {{-- ===== REMINDER BELL ===== --}}
                <div class="dropdown d-inline-block me-1" id="reminderDropdown">
                    <button class="btn btn-outline-primary btn-sm position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Reminders">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="reminderBadge" style="font-size:9px; min-width:18px;">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 380px; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header bg-light fw-bold text-dark py-2">
                            <i class="bi bi-bell me-1"></i> Reminders
                        </div>
                        <div id="reminderList">
                            <div class="text-center text-muted py-3 small">Loading...</div>
                        </div>
                        <div class="dropdown-footer text-center py-1 border-top">
                            <a href="#" class="text-muted small text-decoration-none">View all</a>
                        </div>
                    </div>
                </div>

                <!--<button type="button" class="btn btn-outline-secondary btn-sm me-1" onclick="window.print()" title="Print / PDF"><i class="bi bi-printer"></i></button>-->
                <!--<a href="{{ route('admin.client-master.edit', $client) }}" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil"></i> Edit</a>-->
                <!--<button type="button" class="btn btn-danger btn-sm me-1" onclick="confirmDelete('{{ route('admin.client-master.destroy', $client) }}', '{{ $client->name }}')"><i class="bi bi-trash"></i></button>-->
                <!--<a href="{{ route('admin.client-master.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>-->
            </div>
        </div>
    </div>
</div>

{{-- Styles for reminder dropdown --}}
<style>
    .dropdown-item-reminder {
        white-space: normal;
        padding: 10px 15px;
        border-bottom: 1px solid #f1f5f9;
    }
    .dropdown-item-reminder:last-child {
        border-bottom: none;
    }
    .dropdown-item-reminder:hover {
        background: #f8fafc;
    }
    .reminder-status-upcoming { color: #0ea5e9; }
    .reminder-status-today { color: #f59e0b; }
    .reminder-status-overdue { color: #ef4444; }
</style>

{{-- JavaScript for fetching reminders --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reminderBadge = document.getElementById('reminderBadge');
        const reminderList = document.getElementById('reminderList');

        function fetchReminders() {
            // ✅ Use url() helper for correct path
fetch('{{ route("admin.client-master.reminders", $client) }}', {
    headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const count = data.count || 0;
                reminderBadge.textContent = count > 99 ? '99+' : count;
                reminderBadge.style.display = count > 0 ? 'inline' : 'none';

                if (count === 0) {
                    reminderList.innerHTML = `<div class="text-center text-muted py-3 small">No active reminders</div>`;
                    return;
                }

                let html = '';
                data.reminders.forEach(rem => {
                    let statusClass = '';
                    let statusText = '';
                    if (rem.status === 'overdue') {
                        statusClass = 'reminder-status-overdue';
                        statusText = 'Overdue';
                    } else if (rem.days === 0) {
                        statusClass = 'reminder-status-today';
                        statusText = 'Due today';
                    } else {
                        statusClass = 'reminder-status-upcoming';
                        statusText = `Due in ${rem.days} day${rem.days > 1 ? 's' : ''}`;
                    }

                    html += `
                        <a href="${rem.url}" class="dropdown-item dropdown-item-reminder text-decoration-none">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>${rem.type}</strong>
                                    <div class="small text-muted">${rem.client_name}</div>
                                    <div class="small">
                                        ${rem.case_type || rem.compliance_type}
                                        ${rem.period ? ` (${rem.period})` : ''}
                                    </div>
                                </div>
                                <span class="small fw-bold ${statusClass}">${statusText}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-calendar3 me-1"></i> ${rem.target_date}
                            </div>
                        </a>
                    `;
                });
                reminderList.innerHTML = html;
            })
            .catch(error => {
                console.error('Error fetching reminders:', error);
                reminderList.innerHTML = `<div class="text-center text-danger py-3 small">Failed to load reminders</div>`;
            });
        }

        const dropdownToggle = document.querySelector('#reminderDropdown .btn');
        dropdownToggle.addEventListener('click', function(e) {
            setTimeout(fetchReminders, 100);
        });

        fetchReminders();
    });
</script>