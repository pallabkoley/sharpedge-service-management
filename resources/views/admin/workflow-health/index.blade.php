@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">Workflow Health Dashboard</h1>
                <p class="text-muted mb-0">Monitor order readiness and workflow integrity across all services</p>
            </div>
            <button type="button" class="btn btn-primary" onclick="refreshAllHealth()">
                <i class="bi bi-arrow-repeat me-2"></i>Refresh All Status
            </button>
        </div>
    </div>

    @php
        // Calculate percentages for the stats cards
        $totalStatus = ($stats['ready'] ?? 0) + ($stats['attention'] ?? 0) + ($stats['not_ready'] ?? 0);
        $readyPercentage     = $totalStatus > 0 ? round(($stats['ready'] ?? 0) / $totalStatus * 100) : 0;
        $attentionPercentage = $totalStatus > 0 ? round(($stats['attention'] ?? 0) / $totalStatus * 100) : 0;
        $notReadyPercentage  = $totalStatus > 0 ? round(($stats['not_ready'] ?? 0) / $totalStatus * 100) : 0;
        $brokenPercentage    = $totalStatus > 0 ? round(($stats['broken'] ?? 0) / $totalStatus * 100) : 0;
    @endphp

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Ready Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card ready">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $stats['ready'] ?? 0 }}</div>
                        <div class="card-label">Ready for Certificate</div>
                    </div>
                    <div class="card-icon bg-success">
                        <i class="bi bi-check-circle-fill text-white"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                    {{ $readyPercentage }}% of total
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ $readyPercentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Need Attention -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card attention">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $stats['attention'] ?? 0 }}</div>
                        <div class="card-label">Need Attention</div>
                    </div>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-exclamation-triangle-fill text-white"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                    Has rejected tasks
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-warning" style="width: {{ $attentionPercentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card progress">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $stats['not_ready'] ?? 0 }}</div>
                        <div class="card-label">In Progress</div>
                    </div>
                    <div class="card-icon bg-secondary">
                        <i class="bi bi-clock-fill text-white"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="bi bi-clock-fill text-secondary me-1"></i>
                    Pending tasks
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-secondary" style="width: {{ $notReadyPercentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Broken Workflows -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card broken">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-value">{{ $stats['broken'] ?? 0 }}</div>
                        <div class="card-label">Broken Workflows</div>
                    </div>
                    <div class="card-icon bg-danger">
                        <i class="bi bi-x-circle-fill text-white"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="bi bi-x-circle-fill text-danger me-1"></i>
                    Integrity failed
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-danger" style="width: {{ $brokenPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="section-card mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0 fw-bold">Workflow Health Records</h6>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.workflow-health.index') }}" 
                       class="btn btn-outline-primary {{ !request()->status ? 'active' : '' }}">All</a>
                    <a href="{{ route('admin.workflow-health.index', ['status' => 'READY']) }}" 
                       class="btn btn-outline-success {{ request()->status == 'READY' ? 'active' : '' }}">Ready</a>
                    <a href="{{ route('admin.workflow-health.index', ['status' => 'ATTENTION']) }}" 
                       class="btn btn-outline-warning {{ request()->status == 'ATTENTION' ? 'active' : '' }}">Attention</a>
                    <a href="{{ route('admin.workflow-health.index', ['status' => 'NOT_READY']) }}" 
                       class="btn btn-outline-secondary {{ request()->status == 'NOT_READY' ? 'active' : '' }}">Not Ready</a>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" class="form-control" placeholder="Search orders..." id="tableSearch">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="exportTable()">
                    <i class="bi bi-download me-1"></i>Export
                </button>
            </div>
        </div>
    </div>

    <!-- Workflow Health Table -->
    <div class="section-card">
        <div class="table-responsive">
            <table class="table table-hover datatable" id="healthTable">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Service Details</th>
                        <th>Work Process</th>
                        <th>Tasks Progress</th>
                        <th>Integrity</th>
                        <th>Health Status</th>
                        <th>Last Evaluated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($healthRecords as $health)
                    <tr>
                        <td>
                            <strong class="text-primary">#{{ $health->order->order_id ?? 'N/A' }}</strong>
                            @if($health->order && $health->order->created_at)
                            <br>
                            <small class="text-muted">{{ $health->order->created_at->format('d M Y') }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $health->service->name ?? 'N/A' }}</div>
                            @if($health->service)
                                <small class="text-muted">{{ $health->service?->category?->name }}</small>
                            @endif
                        </td>
                        <td>
                            @if($health->workProcess)
                                <span class="badge bg-info-subtle text-info-emphasis px-3 py-2 rounded-pill">
                                    <i class="bi bi-diagram-3 me-1"></i>
                                    {{ $health->workProcess->name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="min-width: 200px;">
                            <div class="d-flex justify-content-between mb-1 small">
                                <span>Verified</span>
                                <span class="fw-bold text-success">{{ $health->verified_tasks }}/{{ $health->total_tasks }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                @php
                                    $verifiedPercent = $health->total_tasks > 0 ? ($health->verified_tasks / $health->total_tasks) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-success" style="width: {{ $verifiedPercent }}%"></div>
                            </div>
                            <div class="d-flex gap-3 small">
                                <span class="text-secondary"><i class="bi bi-clock me-1"></i>Pending: {{ $health->pending_tasks }}</span>
                                <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Rejected: {{ $health->rejected_tasks }}</span>
                            </div>
                        </td>
                        <td>
                            @if($health->order_ok)
                                <span class="badge bg-success-subtle text-success-emphasis px-3 py-2 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i>OK
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-2 rounded-pill">
                                    <i class="bi bi-exclamation-circle me-1"></i>BROKEN
                                </span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusConfig = [
                                    'READY' => ['bg' => 'success', 'icon' => 'check-circle', 'text' => 'Ready'],
                                    'ATTENTION' => ['bg' => 'warning', 'icon' => 'exclamation-triangle', 'text' => 'Attention'],
                                    'NOT_READY' => ['bg' => 'secondary', 'icon' => 'clock', 'text' => 'Not Ready'],
                                ];
                                $config = $statusConfig[$health->health_status] ?? ['bg' => 'secondary', 'icon' => 'question-circle', 'text' => 'Unknown'];
                            @endphp
                            <span class="badge bg-white text-{{ $config['bg'] }} border border-{{ $config['bg'] }} px-3 py-2 rounded-pill">
                                <i class="bi bi-{{ $config['icon'] }} me-1"></i>
                                {{ $config['text'] }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $health->last_evaluated_at ? $health->last_evaluated_at->format('H:i, d M') : '-' }}</div>
                            <small class="text-muted">{{ $health->last_evaluated_at ? $health->last_evaluated_at->diffForHumans() : '' }}</small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button"
                                        class="btn btn-outline-primary btn-sm"
                                        onclick="viewHealth({{ $health->order_id }})"
                                        title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm"
                                        onclick="recalculateHealth({{ $health->order_id }}, this)"
                                        title="Recalculate Health">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                @if($health->order)
                                    <a href="{{ route('admin.orders.show', $health->order_id) }}"
                                       class="btn btn-outline-info btn-sm"
                                       title="View Order">
                                        <i class="bi bi-bag"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-activity display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">No Workflow Health Records Found</h5>
                            <p class="text-muted mb-3">Health records will appear once orders are created and tasks are assigned.</p>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-right me-2"></i>View Orders
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Showing {{ $healthRecords->firstItem() ?? 0 }} to {{ $healthRecords->lastItem() ?? 0 }} of {{ $healthRecords->total() ?? 0 }} entries
            </div>
            <div>
                {{ $healthRecords->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-4">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h6 class="mb-2" id="loadingMessage">Processing...</h6>
            <p class="text-muted small mb-0" id="loadingSubtext">Please wait</p>
        </div>
    </div>
</div>

<!-- Health Details Modal -->
<div class="modal fade" id="healthModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Workflow Health Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="healthModalBody">
                <div class="text-center p-4">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
}
.stats-card.ready { border-left-color: #10b981; }
.stats-card.attention { border-left-color: #f59e0b; }
.stats-card.progress { border-left-color: #6b7280; }
.stats-card.broken { border-left-color: #ef4444; }
.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}
.card-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
    margin-bottom: 0.25rem;
}
.card-label {
    font-size: 0.875rem;
    color: #6b7280;
}
.card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.section-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.table th {
    background-color: #f8fafc;
    color: #64748b;
    font-weight: 600;
    border-bottom: 1px solid #e2e8f0;
}
.table td {
    vertical-align: middle;
}
.badge.bg-success-subtle {
    background-color: #d1fae5;
    color: #065f46;
}
.badge.bg-danger-subtle {
    background-color: #fee2e2;
    color: #991b1b;
}
.badge.bg-info-subtle {
    background-color: #dbeafe;
    color: #1e40af;
}
.progress {
    background-color: #f1f5f9;
    border-radius: 10px;
    overflow: hidden;
}
.btn-group .btn {
    margin: 0 2px;
}
/* NEW: optional style for document list */
.list-group-item a {
    text-decoration: none;
    color: #1f2937;
}
.list-group-item a:hover {
    color: #0d6efd;
}
</style>
@endpush

@push('scripts')
<script>
// ==================== GLOBAL NOTIFICATION ====================
function showNotification(message, type = 'info') {
    document.querySelectorAll('.toast-notification').forEach(el => el.remove());
    const toast = document.createElement('div');
    toast.className = `toast-notification position-fixed top-0 end-0 m-3 p-3 bg-${type === 'success' ? 'success' : 'danger'} text-white rounded-4 shadow`;
    toast.style.zIndex = 9999;
    toast.style.animation = 'slideIn 0.3s ease';
    toast.innerHTML = `<div class="d-flex align-items-center"><i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i><span>${message}</span></div>`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ==================== TABLE SEARCH ====================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#healthTable tbody tr').forEach(row => {
                if (row.style.display !== 'none') {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                }
            });
        });
    }
});

// ==================== EXPORT CSV ====================
function exportTable() {
    const rows = [];
    document.querySelectorAll('#healthTable thead th').forEach(th => {
        if (th.textContent.trim() !== 'Actions') rows.push(th.textContent.trim());
    });
    const csv = [rows.join(',')];
    document.querySelectorAll('#healthTable tbody tr').forEach(tr => {
        if (tr.style.display !== 'none') {
            const row = [];
            tr.querySelectorAll('td').forEach((td, idx) => {
                if (idx < 7) {
                    row.push('"' + td.textContent.trim().replace(/"/g, '""') + '"');
                }
            });
            csv.push(row.join(','));
        }
    });
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'workflow-health.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// ==================== RECALCULATE SINGLE ORDER ====================
async function recalculateHealth(orderId, button) {
    if (!confirm('Recalculate workflow health for this order?')) return;
    const originalHtml = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    button.disabled = true;
    try {
        const response = await fetch(`/admin/workflow-health/order/${orderId}/recalculate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        if (response.ok) {
            showNotification('Health recalculated successfully!', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error('Recalculation failed');
        }
    } catch (error) {
        showNotification(error.message, 'error');
        button.innerHTML = originalHtml;
        button.disabled = false;
    }
}

// ==================== REFRESH ALL ====================
async function refreshAllHealth() {
    if (!confirm('Refresh all workflow health records? This may take a moment.')) return;
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    document.getElementById('loadingMessage').textContent = 'Refreshing All Health Records...';
    document.getElementById('loadingSubtext').textContent = 'This may take a few moments';
    loadingModal.show();
    try {
        const response = await fetch(`/admin/workflow-health/refresh-all`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        if (response.ok) {
            showNotification('All workflow health refreshed!', 'success');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            throw new Error('Refresh failed');
        }
    } catch (error) {
        showNotification(error.message, 'error');
    } finally {
        loadingModal.hide();
    }
}

// ==================== VIEW HEALTH DETAILS (with Upload/Replace + SUPPORT DOCS) ====================
async function viewHealth(orderId) {
    const modal = new bootstrap.Modal(document.getElementById('healthModal'));
    modal.show();
    const body = document.getElementById('healthModalBody');
    body.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';

    try {
        const response = await fetch(`/admin/workflow-health/order/${orderId}`);
        const data = await response.json();
        const health = data.health;
        const canUpload = data.can_upload_certificate;
        const hasCertificate = health.order && health.order.certificate_path;

        let actionSection = '';

        if (hasCertificate) {
            const certUrl = `/storage/${health.order.certificate_path}`;
            actionSection = `
                <hr>
                <h6 class="fw-bold mb-3">Certificate</h6>
                <div id="certificateActions">
                    <a href="${certUrl}" class="btn btn-primary" target="_blank">
                        <i class="bi bi-eye me-1"></i>View Certificate
                    </a>
                    <button type="button" class="btn btn-warning" onclick="showReplaceForm(${health.order_id})">
                        <i class="bi bi-arrow-repeat me-1"></i>Replace Certificate
                    </button>
                </div>
                <div id="replaceForm" style="display: none;">
                    <hr>
                    <h6 class="fw-bold mb-3">Replace Certificate</h6>
                    <form id="certificateReplaceForm">
                        <input type="hidden" name="order_id" value="${health.order_id}">
                        <div class="mb-3">
                            <input type="file" name="certificate" class="form-control" 
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" required>
                            <small class="text-muted">Allowed: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX (max 5MB)</small>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-cloud-upload me-1"></i>Upload New Certificate
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="hideReplaceForm()">
                            Cancel
                        </button>
                    </form>
                </div>
            `;
        } else if (canUpload) {
            actionSection = `
                <hr>
                <h6 class="fw-bold mb-3">Upload Final Certificate</h6>
                <form id="certificateUploadForm">
                    <input type="hidden" name="order_id" value="${health.order_id}">
                    <div class="mb-3">
                        <input type="file" name="certificate" class="form-control" 
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" required>
                        <small class="text-muted">Allowed: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX (max 5MB)</small>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-cloud-upload me-1"></i>Upload Certificate
                    </button>
                </form>
            `;
        }

        // NEW: Support Documents UI block
        let supportDocsSection = `
            <hr>
            <h6 class="fw-bold mb-3">Support Documents</h6>
            <form id="supportDocumentsUploadForm">
                <input type="hidden" name="order_id" value="${health.order_id}">
                <div class="mb-3">
                    <input type="file" name="documents[]" class="form-control" multiple required>
                    <small class="text-muted">Upload multiple documents (PDF, images, etc.)</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload me-1"></i>Upload Documents
                </button>
            </form>
            <div id="supportDocumentsList" class="mt-3"></div>
        `;

        body.innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <strong>Order ID</strong>
                    <div>${health.order.order_id}</div>
                </div>
                <div class="col-md-6">
                    <strong>Service</strong>
                    <div>${health.service?.name || '-'}</div>
                </div>
                <div class="col-md-6">
                    <strong>Work Process</strong>
                    <div>${health.work_process?.name ?? '-'}</div>
                </div>
                <div class="col-md-6">
                    <strong>Total Tasks</strong>
                    <div>${health.total_tasks}</div>
                </div>
                <div class="col-md-6">
                    <strong>Verified Tasks</strong>
                    <div class="text-success">${health.verified_tasks}</div>
                </div>
                <div class="col-md-6">
                    <strong>Pending Tasks</strong>
                    <div class="text-warning">${health.pending_tasks}</div>
                </div>
                <div class="col-md-6">
                    <strong>Rejected Tasks</strong>
                    <div class="text-danger">${health.rejected_tasks}</div>
                </div>
                <div class="col-md-6">
                    <strong>Workflow Integrity</strong>
                    <div>${health.order_ok ? '✅ OK' : '❌ BROKEN'}</div>
                </div>
                <div class="col-md-6">
                    <strong>Health Status</strong>
                    <div>${health.health_status}</div>
                </div>
                <div class="col-md-6">
                    <strong>Last Evaluated</strong>
                    <div>${health.last_evaluated_at ? new Date(health.last_evaluated_at).toLocaleString() : '-'}</div>
                </div>
                ${actionSection}
            </div>
            ${supportDocsSection}
        `;

        // Load existing support documents
        loadSupportDocuments(health.order_id);

        // Attach event listeners (certificate forms)
        if (!hasCertificate && canUpload) {
            document.getElementById('certificateUploadForm')?.addEventListener('submit', uploadCertificate);
        }
        if (hasCertificate) {
            document.getElementById('certificateReplaceForm')?.addEventListener('submit', replaceCertificate);
        }
        // NEW: attach support docs upload listener
        document.getElementById('supportDocumentsUploadForm')?.addEventListener('submit', uploadSupportDocuments);

    } catch (error) {
        console.error(error);
        body.innerHTML = '<div class="alert alert-danger">Failed to load workflow details.</div>';
    }
}

// ==================== UPLOAD CERTIFICATE ====================
async function uploadCertificate(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Uploading...';
    submitBtn.disabled = true;

    try {
        const orderId = formData.get('order_id');
        const response = await fetch(`/admin/workflow-health/order/${orderId}/upload-certificate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            showNotification('Certificate uploaded successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('healthModal')).hide();
            setTimeout(() => window.location.reload(), 1200);
        } else {
            showNotification(result.message || 'Upload failed', 'error');
            submitBtn.innerHTML = originalHtml;
            submitBtn.disabled = false;
        }
    } catch (error) {
        showNotification('Upload error: ' + error.message, 'error');
        submitBtn.innerHTML = originalHtml;
        submitBtn.disabled = false;
    }
}

// ==================== REPLACE CERTIFICATE ====================
async function replaceCertificate(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Uploading...';
    submitBtn.disabled = true;

    try {
        const orderId = formData.get('order_id');
        const response = await fetch(`/admin/workflow-health/order/${orderId}/replace-certificate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            showNotification('Certificate replaced successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('healthModal')).hide();
            setTimeout(() => window.location.reload(), 1200);
        } else {
            showNotification(result.message || 'Replace failed', 'error');
            submitBtn.innerHTML = originalHtml;
            submitBtn.disabled = false;
        }
    } catch (error) {
        showNotification('Replace error: ' + error.message, 'error');
        submitBtn.innerHTML = originalHtml;
        submitBtn.disabled = false;
    }
}

// ==================== SHOW/HIDE REPLACE FORM ====================
function showReplaceForm() {
    document.getElementById('certificateActions').style.display = 'none';
    document.getElementById('replaceForm').style.display = 'block';
}

function hideReplaceForm() {
    document.getElementById('certificateActions').style.display = 'block';
    document.getElementById('replaceForm').style.display = 'none';
}

// ==================== NEW: SUPPORT DOCUMENTS FUNCTIONS ====================

/**
 * Load and display all support documents for an order
 */
async function loadSupportDocuments(orderId) {
    const container = document.getElementById('supportDocumentsList');
    if (!container) return;

    try {
        const response = await fetch(`/admin/order-support-documents/${orderId}`);
        const result = await response.json();

        if (!result.documents || result.documents.length === 0) {
            container.innerHTML = `<p class="text-muted small">No support documents uploaded</p>`;
            return;
        }

        let html = '<ul class="list-group">';
        result.documents.forEach(doc => {
            html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="/storage/${doc.file_path}" target="_blank">
                        <i class="bi bi-file-earmark"></i> ${doc.file_name}
                    </a>
                    <button class="btn btn-sm btn-danger" onclick="deleteSupportDocument(${doc.id}, ${orderId})">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
            `;
        });
        html += '</ul>';
        container.innerHTML = html;
    } catch (err) {
        container.innerHTML = '<p class="text-danger">Failed to load documents</p>';
    }
}

/**
 * Handle support documents upload (multiple files)
 */
async function uploadSupportDocuments(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Uploading...';
    submitBtn.disabled = true;

    try {
        const response = await fetch('/admin/order-support-documents/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const result = await response.json();

        if (result.status) {
            showNotification('Documents uploaded successfully', 'success');
            // Refresh the list
            loadSupportDocuments(formData.get('order_id'));
            form.reset();
        } else {
            showNotification('Upload failed: ' + (result.message || ''), 'error');
        }
    } catch (error) {
        showNotification('Upload error: ' + error.message, 'error');
    } finally {
        submitBtn.innerHTML = originalHtml;
        submitBtn.disabled = false;
    }
}

/**
 * Delete a specific support document
 */
async function deleteSupportDocument(id, orderId) {
    if (!confirm('Delete this document?')) return;

    try {
        const response = await fetch(`/admin/order-support-documents/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const result = await response.json();

        if (result.status) {
            showNotification('Document deleted', 'success');
            loadSupportDocuments(orderId);
        } else {
            showNotification('Delete failed: ' + (result.message || ''), 'error');
        }
    } catch (error) {
        showNotification('Delete error: ' + error.message, 'error');
    }
}

// ==================== STYLES FOR NOTIFICATIONS ====================
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
@endpush