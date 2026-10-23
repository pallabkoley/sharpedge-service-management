{{-- ============================================================
    COMPLIANCES – Full Redesign (Matches Cases)
============================================================ --}}

<div class="row g-4">
    <div class="col-12">
        {{-- ========================================================
            ADD COMPLIANCE ITEM (Full Width)
        ========================================================= --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-plus-circle me-2"></i> Add Compliance Item
                </h6>
            </div>
            <div class="card-body pt-0">
                <form action="{{ route('admin.client-master.compliances.store', $client) }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Compliance Type <span class="text-danger">*</span></label>
                            <input type="text" name="compliance_type" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Period</label>
                            <input type="text" name="period" class="form-control form-control-sm" placeholder="e.g., Quarterly, Monthly">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Due Date</label>
                            <input type="date" name="due_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="pending">Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="filed">Filed</option>
                                <option value="paid">Paid</option>
                                <option value="completed">Completed</option>
                                <option value="overdue">Overdue</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Notes</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="repeat_automatically" class="form-check-input" id="repeatAuto" value="1">
                                <label class="form-check-label small" for="repeatAuto">Repeat automatically</label>
                            </div>
                            <div class="form-check form-check-inline ms-3">
                                <label class="form-label fw-semibold small text-uppercase text-muted me-2">Parent Compliance</label>
                                <select name="parent_compliance_id" class="form-select form-select-sm d-inline-block w-auto">
                                    <option value="">None</option>
                                    @foreach($client->compliances as $comp)
                                        <option value="{{ $comp->id }}">{{ $comp->compliance_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-plus-lg me-1"></i> Add Compliance
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ========================================================
            COMPLIANCE HISTORY (Full Width)
        ========================================================= --}}
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-clock-history me-2"></i> Compliance History
                </h6>
            </div>
            <div class="card-body pt-0">

                @forelse($client->compliances->sortByDesc('id') as $compliance)

                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'in-progress' => 'info',
                            'filed' => 'primary',
                            'paid' => 'success',
                            'completed' => 'success',
                            'overdue' => 'danger',
                            'closed' => 'secondary',
                        ];
                        $statusColor = $statusColors[$compliance->status ?? ''] ?? 'secondary';

                        $dueDate = $compliance->due_date ? \Carbon\Carbon::parse($compliance->due_date) : null;
                        $today = \Carbon\Carbon::today();
                        $isOverdue = $dueDate && $dueDate->lt($today) && !in_array($compliance->status, ['completed', 'closed', 'filed', 'paid']);
                        $isDueToday = $dueDate && $dueDate->isSameDay($today);
                        $daysDiff = $dueDate ? $today->diffInDays($dueDate, false) : null;
                    @endphp

                    <div class="case-entry border rounded-3 p-3 mb-3 bg-white shadow-sm">

                        {{-- Header with inline edit form --}}
                        <form method="POST" action="{{ route('admin.client-master.compliances.update', ['client' => $client->id, 'compliance' => $compliance->id]) }}" class="row g-2 align-items-end">
                            @csrf @method('PUT')

                            <div class="col-md-3">
                                <span class="fw-bold fs-6 text-dark d-block">{{ $compliance->compliance_type }}</span>
                                <span class="text-muted small">{{ $compliance->period ?: 'N/A' }}</span>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="pending" {{ $compliance->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in-progress" {{ $compliance->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="filed" {{ $compliance->status == 'filed' ? 'selected' : '' }}>Filed</option>
                                    <option value="paid" {{ $compliance->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="completed" {{ $compliance->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="overdue" {{ $compliance->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="closed" {{ $compliance->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Due Date</label>
                                <input type="date" name="due_date" class="form-control form-control-sm" value="{{ $compliance->due_date ? $compliance->due_date->format('Y-m-d') : '' }}">
                            </div>

                            <div class="col-md-2">
                                <div class="form-check">
                                    <input type="checkbox" name="repeat_automatically" class="form-check-input" id="repeat_{{ $compliance->id }}" value="1" {{ $compliance->repeat_automatically ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="repeat_{{ $compliance->id }}">Repeat</label>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100" title="Update compliance">
                                    <i class="bi bi-check"></i>
                                </button>
                            </div>

                            <div class="col-md-1">
                                <form method="POST" action="{{ route('admin.client-master.compliances.destroy', ['client' => $client->id, 'compliance' => $compliance->id]) }}" onsubmit="return confirm('Delete this compliance?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </form>

                        {{-- Overdue / Due badge --}}
                        @if($dueDate)
                            <div class="mt-2">
                                @if($isOverdue)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-exclamation-circle me-1"></i> Overdue by {{ abs($daysDiff) }}d
                                    </span>
                                @elseif($isDueToday)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-calendar-event me-1"></i> Due Today
                                    </span>
                                @elseif($daysDiff !== null && $daysDiff > 0 && !in_array($compliance->status, ['completed','closed','filed','paid']))
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-clock me-1"></i> Due in {{ $daysDiff }}d
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Notes --}}
                        @if($compliance->notes)
                            <div class="mt-2 p-2 bg-light rounded-2 small">
                                <strong class="text-muted">Notes:</strong>
                                <span class="text-secondary">{{ $compliance->notes }}</span>
                            </div>
                        @endif

                        {{-- Parent compliance --}}
                        @if($compliance->parentCompliance)
                            <div class="mt-2 small">
                                <span class="text-muted">Parent:</span>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $compliance->parentCompliance->compliance_type }}</span>
                            </div>
                        @endif

                        {{-- =================================================
                            FOLLOW-UP LOG
                        ================================================== --}}
                        <div class="mt-3">
                            <h6 class="fw-bold text-primary small text-uppercase mb-2">
                                <i class="bi bi-arrow-repeat me-1"></i> Follow-up Log
                            </h6>

                            @if($compliance->followUps && $compliance->followUps->count())
                                <div class="followup-list">
                                    @foreach($compliance->followUps as $followUp)
                                        <div class="d-flex align-items-center gap-2 p-2 bg-light rounded-2 mb-1 small flex-wrap">
                                            <span class="fw-semibold text-primary" style="min-width:90px;">
                                                <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($followUp->follow_up_date)->format('d M Y') }}
                                            </span>
                                            <span class="text-secondary flex-grow-1">{{ $followUp->note }}</span>
                                            @if($followUp->createdBy)
                                                <span class="text-muted fst-italic">— {{ $followUp->createdBy->name }}</span>
                                            @endif
                                            <form method="POST" action="{{ route('admin.client-master.compliances.follow-ups.destroy', ['client' => $client->id, 'compliance' => $compliance->id, 'followUp' => $followUp->id]) }}" style="display:inline-block;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger p-0 border-0" onclick="return confirm('Delete this follow-up?')" title="Delete follow-up">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-muted small fst-italic">No follow-up logged yet.</div>
                            @endif

                            {{-- Add Follow-up Form --}}
                            <form method="POST" action="{{ route('admin.client-master.compliances.follow-ups.store', ['client' => $client->id, 'compliance' => $compliance->id]) }}" class="row g-2 mt-2 align-items-end">
                                @csrf
                                <div class="col-auto">
                                    <label class="visually-hidden">Follow-up Date</label>
                                    <input type="date" name="follow_up_date" class="form-control form-control-sm" required>
                                </div>
                                <div class="col">
                                    <label class="visually-hidden">Note</label>
                                    <input type="text" name="note" class="form-control form-control-sm" placeholder="What happened / next step..." required>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i> Log
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>

                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-check fs-1 d-block mb-2 opacity-50"></i>
                        <p class="mb-0">No compliances recorded.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</div>

{{-- ============================================================
    STYLES (consistent with Cases)
============================================================ --}}
<style>
    .fs-10 {
        font-size: 0.65rem;
        letter-spacing: 0.02em;
    }
    .case-entry {
        transition: box-shadow 0.15s ease;
    }
    .case-entry:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.06) !important;
    }
    .bg-warning-subtle { background-color: #fff3cd !important; }
    .text-warning-emphasis { color: #856404 !important; }
    .bg-danger-subtle { background-color: #f8d7da !important; }
    .text-danger { color: #dc3545 !important; }
    .bg-info-subtle { background-color: #cff4fc !important; }
    .text-info { color: #0dcaf0 !important; }
    .bg-primary-subtle { background-color: #cfe2ff !important; }
    .text-primary { color: #0d6efd !important; }
    .bg-success-subtle { background-color: #d1e7dd !important; }
    .text-success { color: #198754 !important; }
    .bg-secondary-subtle { background-color: #e9ecef !important; }
    .text-secondary { color: #6c757d !important; }
</style>