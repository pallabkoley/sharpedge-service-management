@extends('layouts.Backoffice')

@section('page-title', 'All Client Documents')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">All Client Documents</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Documents</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Upload Form with Client Dropdown -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Upload Documents for Any Client</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('backoffice.documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="client_id" class="form-label">Select Client <span class="text-danger">*</span></label>
                        <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                            <option value="">-- Choose Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} ({{ $client->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="documents" class="form-label">Select Files <span class="text-danger">*</span></label>
                        <input type="file" name="documents[]" id="documents" class="form-control @error('documents.*') is-invalid @enderror" multiple required
                               accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">Allowed: PDF, JPG, JPEG, PNG. Max 5MB per file.</div>
                        @error('documents.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Visibility</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visibility" id="visibility_client" value="client_visible" checked>
                            <label class="form-check-label" for="visibility_client">
                                <i class="bi bi-eye"></i> Client Visible
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visibility" id="visibility_internal" value="internal">
                            <label class="form-check-label" for="visibility_internal">
                                <i class="bi bi-lock"></i> Internal Only
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Upload Documents
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- All Documents List Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-files me-2"></i>All Uploaded Documents ({{ $documents->count() }})</h5>
        </div>
        <div class="card-body">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="globalDocsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>File Name</th>
                                <th>Visibility</th>
                                <th>Size</th>
                                <th>Uploaded By</th>
                                <th>Uploaded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $index => $doc)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('backoffice.clients.show', $doc->client_id) }}" class="text-decoration-none">
                                            {{ $doc->client->name ?? 'Deleted Client' }}
                                        </a>
                                    </td>
                                    <td>
                                        <i class="bi {{ str_ends_with($doc->file_name, '.pdf') ? 'bi-file-pdf text-danger' : (str_ends_with($doc->file_name, '.png') ? 'bi-file-image text-primary' : 'bi-file-earmark') }} me-2"></i>
                                        {{ $doc->file_name }}
                                    </td>
                                    <td>
                                        @if($doc->visibility === 'client_visible')
                                            <span class="badge bg-success">Client Visible</span>
                                        @else
                                            <span class="badge bg-secondary">Internal Only</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($doc->file_size / 1024, 2) }} KB</td>
                                    <td>{{ $doc->uploader->name ?? 'Unknown' }}</td>
                                    <td>{{ $doc->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('backoffice.documents.show', $doc->id) }}"
                                           class="btn btn-sm btn-outline-primary" target="_blank" title="View/Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-global-doc"
                                                data-doc-id="{{ $doc->id }}" data-doc-name="{{ $doc->file_name }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text fs-1 text-muted"></i>
                    <p class="mt-2 text-muted">No documents uploaded yet. Use the form above to upload.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteGlobalModal" tabindex="-1" aria-labelledby="deleteGlobalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteGlobalModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="deleteDocNameGlobal"></strong>?
                <br>This action cannot be undone.
            </div>
            <div class="modal-footer">
                <form id="deleteGlobalForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Document</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        if ($('#globalDocsTable').length && !$.fn.DataTable.isDataTable('#globalDocsTable')) {
            $('#globalDocsTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [[6, 'desc']],
                language: {
                    search: "Search documents:",
                    info: "Showing _START_ to _END_ of _TOTAL_ documents",
                    infoEmpty: "No documents to show"
                }
            });
        }

        // Global delete handler
        $('.delete-global-doc').on('click', function() {
            const docId = $(this).data('doc-id');
            const docName = $(this).data('doc-name');
            const deleteUrl = "{{ route('backoffice.documents.destroy', ':id') }}".replace(':id', docId);
            $('#deleteDocNameGlobal').text(docName);
            $('#deleteGlobalForm').attr('action', deleteUrl);
            $('#deleteGlobalModal').modal('show');
        });
    });
</script>
@endsection