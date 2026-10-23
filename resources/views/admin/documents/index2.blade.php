@extends('layouts.admin')

@section('title', 'Document Upload Management')

@section('content')
    <!-- Header -->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Document Upload Management</h2>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <a href="{{ route('admin.services.index') }}" class="card-link">
                <div class="stats-card clickable-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-muted mb-0">ACTIVE SERVICES</h6>
                        <div class="card-icon bg-primary">
                            <i class="bi bi-briefcase"></i>
                        </div>
                    </div>
                    <h3 class="card-value">{{ $services->count() }}</h3>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <a href="{{ route('admin.clients.index') }}" class="card-link">
                <div class="stats-card clickable-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-muted mb-0">ACTIVE CLIENTS</h6>
                        <div class="card-icon bg-success">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <h3 class="card-value">{{ $clients->count() }}</h3>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">PENDING UPLOADS</h6>
                    <div class="card-icon bg-warning">
                        <i class="bi bi-cloud-upload"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $documents ? $documents->where('upload_status', 'pending')->count() : 0 }}</h3>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted mb-0">COMPLETED TODAY</h6>
                    <div class="card-icon bg-info">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <h3 class="card-value">{{ $documents ? $documents->where('upload_status', 'approved')->where('uploaded_at', '>=', today())->count() : 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Upload Card -->
    <div class="content-section">
        <div class="section-card">
            <div class="mb-4">
                <h4 class="section-title mb-1">Upload Required Documents</h4>
            </div>

            <!-- Client & Service Selection -->
            <form action="{{ route('admin.documents.upload.index') }}" method="GET" class="mb-5">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="client_id" class="form-label">Select Client</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <select name="client_id" id="client_id" class="form-select border-start-0 ps-0" required>
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $clientOption)
                                    <option value="{{ $clientOption->id }}" 
                                        {{ $client && $client->id == $clientOption->id ? 'selected' : '' }}>
                                        {{ $clientOption->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <label for="service_id" class="form-label">Select Service</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-briefcase"></i>
                            </span>
                            <select name="service_id" id="service_id" class="form-select border-start-0 ps-0" required>
                                <option value="">-- Select Service --</option>
                                @foreach($services as $serviceOption)
                                    <option value="{{ $serviceOption->id }}" 
                                        {{ $service && $service->id == $serviceOption->id ? 'selected' : '' }}>
                                        {{ $serviceOption->service_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-folder2-open me-1"></i> Load
                        </button>
                    </div>
                </div>
            </form>

            @if($client && $service)
            <!-- Document List -->
            @if($documents->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No document requirements found for this service.
            </div>
            @else
            <!-- Progress Summary -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <h6 class="mb-0">Upload Progress</h6>
                            <small class="text-muted">
                                {{ $documents->where('upload_status', '!=', 'pending')->count() }} of {{ $documents->count() }} documents
                            </small>
                        </div>
                        <div class="flex-grow-1">
                            <div class="progress" style="height: 8px;">
                                @php
                                    $completed = $documents->where('upload_status', '!=', 'pending')->count();
                                    $total = $documents->count();
                                    $percentage = $total > 0 ? ($completed / $total) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $percentage }}%" 
                                     aria-valuenow="{{ $percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="ms-3">
                            <span class="fw-bold">{{ round($percentage) }}%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h5 class="mb-1 text-primary">{{ $documents->where('type', 'mandatory')->count() }}</h5>
                                <small class="text-muted">Mandatory</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h5 class="mb-1 text-success">{{ $documents->where('upload_status', 'approved')->count() }}</h5>
                                <small class="text-muted">Uploaded</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40%;">Document</th>
                            <th style="width: 15%;">Type</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;">Max Size</th>
                            <th style="width: 15%;" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                        <tr class="{{ $doc->upload_status == 'pending' ? 'table-warning-light' : '' }}">
                            <td>
                                <div>
                                    <strong>{{ $doc->document_name }}</strong>
                                    @if($doc->file_count > 0)
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-file-earmark me-1"></i>
                                        {{ $doc->file_count }} file(s) uploaded
                                        @if($doc->original_file_name)
                                        <span class="ms-2">({{ $doc->original_file_name }})</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $doc->type == 'mandatory' ? 'danger' : 'warning' }}-subtle text-{{ $doc->type == 'mandatory' ? 'danger' : 'warning' }} rounded-pill px-3 py-1">
                                    {{ ucfirst($doc->type) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => ['bg' => 'warning', 'text' => 'warning'],
                                        'approved' => ['bg' => 'success', 'text' => 'success'],
                                        'rejected' => ['bg' => 'danger', 'text' => 'danger']
                                    ];
                                    $color = $statusColors[$doc->upload_status] ?? ['bg' => 'secondary', 'text' => 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $color['bg'] }}-subtle text-{{ $color['text'] }} rounded-pill px-3 py-1">
                                    {{ ucfirst($doc->upload_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $doc->max_size }} MB</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($doc->file_count > 0)
                                    <a href="{{ route('admin.documents.view', $doc->upload_id) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-document"
                                            data-id="{{ $doc->upload_id }}"
                                            data-name="{{ $doc->document_name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                    
                                    <button type="button" 
                                            class="btn btn-sm {{ $doc->file_count > 0 ? 'btn-outline-info' : 'btn-primary' }} upload-document-btn"
                                            data-document-id="{{ $doc->document_id }}"
                                            data-document-name="{{ $doc->document_name }}"
                                            data-upload-id="{{ $doc->upload_id }}">
                                        <i class="bi bi-upload me-1"></i>
                                        {{ $doc->file_count > 0 ? 'Replace' : 'Upload' }}
                                    </button>
                                </div>
                                
                                <!-- Hidden upload form - UPDATED FOR BETTER RESPONSIVENESS -->
                                <div id="upload-form-{{ $doc->document_id }}" class="upload-form mt-3 p-3 border rounded" style="display: none;">
                                    <form action="{{ route('admin.documents.upload.store') }}" 
                                          method="POST" 
                                          enctype="multipart/form-data" 
                                          class="document-upload-form"
                                          data-document-id="{{ $doc->document_id }}">
                                        @csrf
                                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                                        <input type="hidden" name="document_id" value="{{ $doc->document_id }}">
                                        
                                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                            <input type="file" 
                                                   name="document" 
                                                   class="form-control form-control-sm flex-grow-1" 
                                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx" 
                                                   required>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-cloud-upload me-1"></i> Upload File
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary cancel-upload">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Max file size: {{ $doc->max_size }}MB. Allowed: {{ $doc->allowed_extensions }}
                                            </small>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            @else
            <!-- Empty State -->
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-folder2-open" style="font-size: 3rem; color: #dee2e6;"></i>
                </div>
                <h5 class="mb-2">Select Client & Service</h5>
                <p class="text-muted mb-0">Choose a client and service to view their document requirements</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Delete Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteDocumentName"></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Document</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i>
                    <span id="toastMessage"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <span id="errorToastMessage"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .clickable-card {
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .clickable-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .clickable-card:hover .card-icon {
        transform: scale(1.1);
        transition: transform 0.3s ease;
    }
    
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #333;
    }
    
    .upload-form {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }
    
    .table-warning-light {
        background-color: rgba(255, 193, 7, 0.05);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    
    /* Responsive fix for upload form */
    @media (max-width: 768px) {
        .upload-form .d-flex {
            flex-direction: column !important;
            gap: 8px !important;
        }
        
        .upload-form .d-flex .form-control {
            width: 100%;
        }
        
        .upload-form .d-flex .d-flex {
            width: 100%;
            justify-content: space-between;
        }
        
        .upload-form .d-flex .d-flex .btn {
            flex: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle upload form
    document.querySelectorAll('.upload-document-btn').forEach(button => {
        button.addEventListener('click', function() {
            const documentId = this.getAttribute('data-document-id');
            const uploadId = this.getAttribute('data-upload-id');
            const formElement = document.getElementById(`upload-form-${documentId}`);
            
            // Hide all other forms first
            document.querySelectorAll('.upload-form').forEach(form => {
                form.style.display = 'none';
                const docId = form.id.replace('upload-form-', '');
                const btn = document.querySelector(`.upload-document-btn[data-document-id="${docId}"]`);
                if (btn) {
                    btn.innerHTML = `<i class="bi bi-upload me-1"></i> ${btn.getAttribute('data-upload-id') ? 'Replace' : 'Upload'}`;
                    btn.classList.remove('btn-secondary');
                    btn.classList.add(btn.getAttribute('data-upload-id') ? 'btn-outline-info' : 'btn-primary');
                }
            });
            
            // Toggle current form
            if (formElement.style.display === 'none' || !formElement.style.display) {
                formElement.style.display = 'block';
                this.innerHTML = '<i class="bi bi-x me-1"></i> Cancel';
                this.classList.remove('btn-primary', 'btn-outline-info');
                this.classList.add('btn-secondary');
            } else {
                formElement.style.display = 'none';
                this.innerHTML = `<i class="bi bi-upload me-1"></i> ${uploadId ? 'Replace' : 'Upload'}`;
                this.classList.remove('btn-secondary');
                this.classList.add(uploadId ? 'btn-outline-info' : 'btn-primary');
            }
        });
    });
    
    // Cancel upload button
    document.querySelectorAll('.cancel-upload').forEach(button => {
        button.addEventListener('click', function() {
            const formElement = this.closest('.upload-form');
            const documentId = formElement.id.replace('upload-form-', '');
            const uploadBtn = document.querySelector(`.upload-document-btn[data-document-id="${documentId}"]`);
            
            formElement.style.display = 'none';
            
            // Reset the upload button
            const uploadId = uploadBtn.getAttribute('data-upload-id');
            uploadBtn.innerHTML = `<i class="bi bi-upload me-1"></i> ${uploadId ? 'Replace' : 'Upload'}`;
            uploadBtn.classList.remove('btn-secondary');
            uploadBtn.classList.add(uploadId ? 'btn-outline-info' : 'btn-primary');
        });
    });
    
    // Handle document upload via AJAX
    document.querySelectorAll('.document-upload-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Document uploaded successfully!');
                    
                    // Reload the page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showError(data.errors?.document?.[0] || data.message || 'Upload failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred during upload.');
            });
        });
    });
    
    // Delete document
    document.querySelectorAll('.delete-document').forEach(button => {
        button.addEventListener('click', function() {
            const documentId = this.getAttribute('data-id');
            const documentName = this.getAttribute('data-name');
            
            // Set up the delete modal
            document.getElementById('deleteDocumentName').textContent = documentName;
            document.getElementById('deleteForm').action = "{{ route('admin.documents.delete', '') }}/" + documentId;
            
            // Show the modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
    });
    
    // Toast functions
    function showSuccess(message) {
        const toastElement = document.getElementById('successToast');
        const toast = new bootstrap.Toast(toastElement);
        
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }
    
    function showError(message) {
        const toastElement = document.getElementById('errorToast');
        const toast = new bootstrap.Toast(toastElement);
        
        document.getElementById('errorToastMessage').textContent = message;
        toast.show();
    }
});
</script>
@endpush