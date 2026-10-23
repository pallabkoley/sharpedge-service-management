{{-- ============================================================
    GST & IT CASES – SharpEdge Admin Style
============================================================ --}}

<div class="client-cases-wrapper">

    {{-- ========================================================
        ADD NEW CASE
    ========================================================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bi bi-plus-circle me-2"></i> Add New Case
            </h5>
        </div>
        <div class="card-body">
            <form id="caseForm" method="POST" action="{{ route('admin.client-master.cases.store', $client) }}">
                @csrf

                <div class="row g-3">
                    {{-- Case Type --}}
                    <div class="col-md-4">
                        <label for="case_type" class="form-label fw-semibold small">Case Type <span class="text-danger">*</span></label>
                        <select id="case_type" name="case_type" class="form-select" required>
                            <option value="">Select</option>
                            <option value="GST">GST</option>
                            <option value="Income Tax">Income Tax</option>
                            <option value="Customs">Customs</option>
                            <option value="Excise">Excise</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    {{-- Other Case Type --}}
                    <div class="col-md-4">
                        <label for="other_case_type" class="form-label fw-semibold small">Other Case Type</label>
                        <input type="text" id="other_case_type" name="other_case_type" class="form-control" placeholder="Enter case type">
                    </div>

                    {{-- Financial Year --}}
                    <div class="col-md-4">
                        <label for="financial_year" class="form-label fw-semibold small">Financial Year</label>
                        <input type="text" id="financial_year" name="financial_year" class="form-control" placeholder="e.g. 2024-25">
                    </div>

                    {{-- Reference / Notice / DIN --}}
                    <div class="col-md-4">
                        <label for="reference_notice_din" class="form-label fw-semibold small">Reference / Notice / DIN</label>
                        <input type="text" id="reference_notice_din" name="reference_notice_din" class="form-control" placeholder="Enter reference / DIN">
                    </div>

                    {{-- Issuing Authority --}}
                    <div class="col-md-4">
                        <label for="issuing_authority_office" class="form-label fw-semibold small">Issuing Authority / Office</label>
                        <input type="text" id="issuing_authority_office" name="issuing_authority_office" class="form-control" placeholder="Enter authority / office">
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold small">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="Notice Received">Notice Received</option>
                            <option value="Reply Under Preparation">Reply Under Preparation</option>
                            <option value="Reply Submitted">Reply Submitted</option>
                            <option value="Order Received">Order Received</option>
                            <option value="Appeal Filed">Appeal Filed</option>
                            <option value="In Appeal">In Appeal</option>
                            <option value="Recovery">Recovery</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>

                    {{-- Next Follow-up --}}
                    <div class="col-md-4">
                        <label for="next_follow_up_date" class="form-label fw-semibold small">Next Follow-up Date</label>
                        <input type="date" id="next_follow_up_date" name="next_follow_up_date" class="form-control">
                    </div>

                    {{-- Amount --}}
                    <div class="col-md-4">
                        <label for="amount_involved" class="form-label fw-semibold small">Amount Involved</label>
                        <input type="number" step="0.01" min="0" id="amount_involved" name="amount_involved" class="form-control" placeholder="0.00">
                    </div>

                    {{-- Case Description --}}
                    <div class="col-12">
                        <label for="case_description" class="form-label fw-semibold small">Case Description</label>
                        <textarea id="case_description" name="case_description" class="form-control" rows="3" placeholder="What the notice / case is about"></textarea>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Add Case
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================================
        CASE HISTORY
    ========================================================= --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bi bi-clock-history me-2"></i> Case History
            </h5>
        </div>
        <div class="card-body p-0">

            @forelse($client->cases->sortByDesc('id') as $case)

                @php
                    $statusClasses = [
                        'Notice Received' => 'warning',
                        'Reply Under Preparation' => 'info',
                        'Reply Submitted' => 'info',
                        'Order Received' => 'primary',
                        'Appeal Filed' => 'warning',
                        'In Appeal' => 'primary',
                        'Recovery' => 'danger',
                        'Closed' => 'success',
                    ];
                    $statusClass = $statusClasses[$case->status ?? ''] ?? 'secondary';

                    $followUpDate = $case->next_follow_up_date ? \Carbon\Carbon::parse($case->next_follow_up_date) : null;
                    $today = \Carbon\Carbon::today();
                    $isOverdue = $followUpDate && $followUpDate->lt($today) && !in_array(strtolower($case->status ?? ''), ['closed']);
                    $isDueToday = $followUpDate && $followUpDate->isSameDay($today);
                    $daysDifference = $followUpDate ? $today->diffInDays($followUpDate, false) : null;
                @endphp

                {{-- Single Case Card --}}
                <div class="border-bottom p-3">

                    {{-- Header Row --}}
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold text-dark">{{ $case->case_type }}</span>
                                @if($case->other_case_type)
                                    <span class="badge bg-secondary">{{ $case->other_case_type }}</span>
                                @endif
                                @if($case->reference_notice_din)
                                    <span class="text-muted small ms-2">{{ $case->reference_notice_din }}</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-{{ $statusClass }}">{{ $case->status ?: 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="row g-2 mt-2 small">
                        <div class="col-md-3">
                            <span class="text-muted">Financial Year</span><br>
                            <span class="fw-semibold">{{ $case->financial_year ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted">Authority / Office</span><br>
                            <span class="fw-semibold">{{ $case->issuing_authority_office ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted">Amount Involved</span><br>
                            <span class="fw-semibold">@if($case->amount_involved !== null) ₹{{ number_format((float) $case->amount_involved, 2) }} @else N/A @endif</span>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted">Next Follow-up</span><br>
                            <span class="fw-semibold">{{ $followUpDate ? $followUpDate->format('d M Y') : 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Overdue / Due Badge --}}
                    @if($followUpDate && !in_array(strtolower($case->status ?? ''), ['closed']))
                        <div class="mt-2">
                            @if($isOverdue)
                                <span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i> Overdue by {{ abs($daysDifference) }}d</span>
                            @elseif($isDueToday)
                                <span class="badge bg-warning text-dark"><i class="bi bi-calendar-event me-1"></i> Due Today</span>
                            @elseif($daysDifference !== null && $daysDifference > 0)
                                <span class="badge bg-info"><i class="bi bi-clock me-1"></i> Due in {{ $daysDifference }}d</span>
                            @endif
                        </div>
                    @endif

                    {{-- Case Description --}}
                    @if($case->case_description)
                        <div class="mt-2 p-2 bg-light rounded small">
                            <strong class="text-muted">Description:</strong>
                            <span class="text-secondary">{{ $case->case_description }}</span>
                        </div>
                    @endif

                    {{-- Follow-up Log --}}
                    <div class="mt-3">
                        <h6 class="fw-bold text-primary small text-uppercase"><i class="bi bi-arrow-repeat me-1"></i> Follow-up Log</h6>

                        @if($case->followUps && $case->followUps->count())
                            @foreach($case->followUps as $followUp)
                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded mb-1 small flex-wrap">
                                    <span class="fw-semibold text-primary">{{ \Carbon\Carbon::parse($followUp->follow_up_date)->format('d M Y') }}</span>
                                    <span class="text-secondary flex-grow-1">{{ $followUp->note }}</span>
                                    @if($followUp->createdBy)
                                        <span class="text-muted">— {{ $followUp->createdBy->name }}</span>
                                    @endif
                                    <form method="POST" action="{{ route('admin.client-master.cases.follow-ups.destroy', ['client' => $client->id, 'case' => $case->id, 'followUp' => $followUp->id]) }}" style="display:inline-block;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger p-0 border-0" onclick="return confirm('Delete this follow-up?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted small">No follow-up logged yet.</div>
                        @endif

                        {{-- Add Follow-up Form --}}
                        <form method="POST" action="{{ route('admin.client-master.cases.follow-ups.store', ['client' => $client->id, 'case' => $case->id]) }}" class="row g-2 mt-2">
                            @csrf
                            <div class="col-auto">
                                <input type="date" name="follow_up_date" class="form-control form-control-sm" required>
                            </div>
                            <div class="col">
                                <input type="text" name="note" class="form-control form-control-sm" placeholder="What happened / next step..." required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-sm">Log Follow-up</button>
                            </div>
                        </form>
                    </div>

                    {{-- ===== ACTIONS – EDITED ===== --}}
                    <div class="mt-3 d-flex gap-2 justify-content-end">

                        {{-- EDIT CASE BUTTON (triggers modal) --}}
                        <button type="button"
                                class="btn btn-outline-secondary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editCaseModal{{ $case->id }}">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </button>

                        {{-- DELETE CASE --}}
                        <form method="POST"
                              action="{{ route('admin.client-master.cases.destroy', ['client' => $client->id, 'case' => $case->id]) }}"
                              style="display:inline-block;"
                              onsubmit="return confirm('Delete this case? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>

                    </div>

                </div> {{-- end single case card --}}

                {{-- ============================================================
                     EDIT CASE MODAL (per case)
                ============================================================ --}}
                <div class="modal fade"
                     id="editCaseModal{{ $case->id }}"
                     tabindex="-1"
                     aria-hidden="true">

                    <div class="modal-dialog modal-lg modal-dialog-centered">

                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title fw-bold text-primary">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Edit Case
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form method="POST"
                                  action="{{ route('admin.client-master.cases.update', ['client' => $client->id, 'case' => $case->id]) }}">

                                @csrf
                                @method('PUT')

                                <div class="modal-body">

                                    <div class="row g-3">

                                        {{-- Case Type --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Case Type <span class="text-danger">*</span></label>
                                            <select name="case_type" class="form-select" required>
                                                <option value="">Select</option>
                                                <option value="GST" {{ $case->case_type == 'GST' ? 'selected' : '' }}>GST</option>
                                                <option value="Income Tax" {{ $case->case_type == 'Income Tax' ? 'selected' : '' }}>Income Tax</option>
                                                <option value="Customs" {{ $case->case_type == 'Customs' ? 'selected' : '' }}>Customs</option>
                                                <option value="Excise" {{ $case->case_type == 'Excise' ? 'selected' : '' }}>Excise</option>
                                                <option value="Other" {{ $case->case_type == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>

                                        {{-- Other Case Type --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Other Case Type</label>
                                            <input type="text" name="other_case_type" class="form-control"
                                                   value="{{ $case->other_case_type }}" placeholder="Enter case type">
                                        </div>

                                        {{-- Financial Year --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Financial Year</label>
                                            <input type="text" name="financial_year" class="form-control"
                                                   value="{{ $case->financial_year }}" placeholder="e.g. 2024-25">
                                        </div>

                                        {{-- Reference --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Reference / Notice / DIN</label>
                                            <input type="text" name="reference_notice_din" class="form-control"
                                                   value="{{ $case->reference_notice_din }}" placeholder="Enter reference / DIN">
                                        </div>

                                        {{-- Authority --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Issuing Authority / Office</label>
                                            <input type="text" name="issuing_authority_office" class="form-control"
                                                   value="{{ $case->issuing_authority_office }}" placeholder="Enter authority / office">
                                        </div>

                                        {{-- Status --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="Notice Received" {{ $case->status == 'Notice Received' ? 'selected' : '' }}>Notice Received</option>
                                                <option value="Reply Under Preparation" {{ $case->status == 'Reply Under Preparation' ? 'selected' : '' }}>Reply Under Preparation</option>
                                                <option value="Reply Submitted" {{ $case->status == 'Reply Submitted' ? 'selected' : '' }}>Reply Submitted</option>
                                                <option value="Order Received" {{ $case->status == 'Order Received' ? 'selected' : '' }}>Order Received</option>
                                                <option value="Appeal Filed" {{ $case->status == 'Appeal Filed' ? 'selected' : '' }}>Appeal Filed</option>
                                                <option value="In Appeal" {{ $case->status == 'In Appeal' ? 'selected' : '' }}>In Appeal</option>
                                                <option value="Recovery" {{ $case->status == 'Recovery' ? 'selected' : '' }}>Recovery</option>
                                                <option value="Closed" {{ $case->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                        </div>

                                        {{-- Next Follow-up --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Next Follow-up Date</label>
                                            <input type="date" name="next_follow_up_date" class="form-control"
                                                   value="{{ $case->next_follow_up_date ? \Carbon\Carbon::parse($case->next_follow_up_date)->format('Y-m-d') : '' }}">
                                        </div>

                                        {{-- Amount --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Amount Involved</label>
                                            <input type="number" step="0.01" min="0" name="amount_involved" class="form-control"
                                                   value="{{ $case->amount_involved }}" placeholder="0.00">
                                        </div>

                                        {{-- Description --}}
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Case Description</label>
                                            <textarea name="case_description" class="form-control" rows="5"
                                                      placeholder="What the notice / case is about">{{ $case->case_description }}</textarea>
                                        </div>

                                    </div> {{-- end row --}}

                                </div> {{-- end modal-body --}}

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i> Update Case
                                    </button>
                                </div>

                            </form>

                        </div> {{-- end modal-content --}}
                    </div> {{-- end modal-dialog --}}
                </div> {{-- end modal --}}

            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                    <p>No cases recorded.</p>
                </div>
            @endforelse

        </div> {{-- end card-body --}}
    </div> {{-- end card --}}

</div> {{-- end wrapper --}}