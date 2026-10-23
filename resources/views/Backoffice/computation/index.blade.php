@extends('layouts.Backoffice')

@section('styles')
    <!-- Additional DataTables styling (already in layout) -->
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📊 Tax Computations</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createComputationModal">
            <i class="bi bi-plus-circle"></i> New Computation
        </button>
    </div>

    <!-- Computations Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="computationsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Client Type</th>
                            <th>Assessment Year</th>
                            <th>Financial Year</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($computations as $comp)
                        <tr>
                            <td>{{ $comp->id }}</td>
                           <td>
    {{
        $comp->client->name
        ?? $comp->client->company_name
        ?? 'N/A'
    }}
</td>
                            <td>{{ $comp->clientType->name ?? 'N/A' }}</td>
                            <td>{{ $comp->assessment_year }}</td>
                            <td>{{ $comp->financial_year }}</td>
                            <td>
                                <span class="badge bg-{{ $comp->computation_status == 'draft' ? 'secondary' : ($comp->computation_status == 'completed' ? 'success' : 'warning') }}">
                                    {{ ucfirst($comp->computation_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $comp->payment_status == 'paid' ? 'success' : 'danger' }}">
                                    {{ ucfirst($comp->payment_status) }}
                                </span>
                            </td>
                            <td>{{ $comp->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('backoffice.computations.sheet', $comp->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $comp->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $computations->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Create Computation Modal -->
<div class="modal fade" id="createComputationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('backoffice.computations.sheet') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Computation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Client *</label>
                        <select name="client_id" class="form-select" required>
                            <option value="">Select Client</option>
                            @foreach($clients ?? [] as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Client Type</label>
                        <select name="client_type_id" class="form-select">
                            <option value="">Select Type</option>
                            @foreach($clientTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assessment Year *</label>
                            <input type="text" name="assessment_year" class="form-control" placeholder="2024-25" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Financial Year *</label>
                            <input type="text" name="financial_year" class="form-control" placeholder="2023-24" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Previous Year</label>
                        <input type="text" name="previous_year" class="form-control" placeholder="2022-23">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Return Type</label>
                            <select name="return_type" class="form-select">
                                <option value="original">Original</option>
                                <option value="revised">Revised</option>
                                <option value="belated">Belated</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tax Regime</label>
                            <select name="tax_regime" class="form-select">
                                <option value="old">Old Regime</option>
                                <option value="new">New Regime</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Draft</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#computationsTable')) {
        $('#computationsTable').DataTable().destroy();
    }
    $('#computationsTable').DataTable({
        paging: false,   // using Laravel pagination
        searching: true,
        ordering: true,
        info: false,
        columnDefs: [{ orderable: false, targets: -1 }]
    });
});

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this computation? All data will be lost.')) {
        let form = document.getElementById('deleteForm');
        form.action = "{{ route('backoffice.computations.destroy', ':id') }}".replace(':id', id);
        form.submit();
    }
}
</script>
@endsection