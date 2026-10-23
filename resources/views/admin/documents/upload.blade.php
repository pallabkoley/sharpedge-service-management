@extends('layouts.admin')

@section('title', 'Admin Document Upload')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Document Upload</li>
                    </ol>
                </div>
                <h4 class="page-title">Document Upload Management</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Select Client & Service</h5>
                    
                    <form action="{{ route('admin.documents.upload.index') }}" method="GET" id="documentForm">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Select Client <span class="text-danger">*</span></label>
                                    <select name="client_id" id="client_id" class="form-select" required>
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
                                <div class="mb-3">
                                    <label for="service_id" class="form-label">Select Service <span class="text-danger">*</span></label>
                                    <select name="service_id" id="service_id" class="form-select" required>
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
                            
                            <div class="col-md-2">
                                <div class="mb-3 d-grid">
                                    <button type="submit" class="btn btn-primary mt-4">Load Documents</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($client && $service)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            Documents for <span class="text-primary">{{ $client->name }}</span> - 
                            <span class="text-info">{{ $service->service_name }}</span>
                        </h5>
                        <div class="badge bg-{{ $documents->where('upload_status', 'pending')->count() > 0 ? 'warning' : 'success' }}">
                            {{ $documents->where('upload_status', 'pending')->count() }} Pending
                        </div>
                    </div>
                    
                    @if($documents->isEmpty())
                    <div class="alert alert-info">
                        No document requirements found for this service.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="35%">Document Name</th>
                                    <th width="15%">Type</th>
                                    <th width="20%">Status</th>
                                    <th width="25%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $index => $doc)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $doc->document_name }}</strong>
                                        @if($doc->upload_id && $doc->original_file_name)
                                        <br>
                                        <small class="text-muted">
                                            <i class="mdi mdi-file-document-outline"></i> 
                                            {{ $doc->original_file_name }}
                                        </small>
                                        @endif
                                        @if($doc->upload_status == 'rejected' && $doc->rejected_reason)
                                        <br>
                                        <small class="text-danger">
                                            <i class="mdi mdi-alert-circle-outline"></i> 
                                            Reason: {{ $doc->rejected_reason }}
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $doc->type == 'mandatory' ? 'danger' : 'warning' }}">
                                            {{ ucfirst($doc->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $color = $statusColors[$doc->upload_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">
                                            {{ ucfirst($doc->upload_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($doc->upload_id && $doc->file_path)
                                                <a href="{{ route('admin.documents.view', $doc->upload_id) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-eye-outline"></i> View
                                                </a>
                                                
                                                <form action="{{ route('admin.documents.delete', $doc->upload_id) }}" 
                                                      method="POST" 
                                                      class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this document?')">
                                                        <i class="mdi mdi-delete-outline"></i> Delete
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-{{ $doc->upload_id ? 'info' : 'success' }} upload-btn"
                                                    data-document-id="{{ $doc->document_id }}"
                                                    data-document-name="{{ $doc->document_name }}">
                                                <i class="mdi mdi-upload-outline"></i> 
                                                {{ $doc->upload_id ? 'Replace' : 'Upload' }}
                                            </button>
                                        </div>
                                        
                                        <!-- Hidden upload form -->
                                        <div id="upload-form-{{ $doc->document_id }}" class="upload-form mt-2" style="display: none;">
                                            <form action="{{ route('admin.documents.upload.store') }}" 
                                                  method="POST" 
                                                  enctype="multipart/form-data" 
                                                  class="row g-2 align-items-center document-upload-form"
                                                  data-document-id="{{ $doc->document_id }}">
                                                @csrf
                                                <input type="hidden" name="client_id" value="{{ $client->id }}">
                                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                                <input type="hidden" name="document_id" value="{{ $doc->document_id }}">
                                                
                                                <div class="col-auto">
                                                    <input type="file" 
                                                           name="document" 
                                                           class="form-control form-control-sm" 
                                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                                                           required>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="mdi mdi-check"></i> Upload
                                                    </button>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-sm btn-secondary cancel-upload">
                                                        Cancel
                                                    </button>
                                                </div>
                                                <div class="col-12">
                                                    <small class="text-muted">Max file size: 2MB. Allowed: PDF, DOC, JPG, PNG</small>
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
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Uploading document...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .upload-form {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 15px;
        background-color: #f9f9f9;
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .delete-form {
        display: inline-block;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle upload form
    document.querySelectorAll('.upload-btn').forEach(button => {
        button.addEventListener('click', function() {
            const documentId = this.getAttribute('data-document-id');
            const formElement = document.getElementById(`upload-form-${documentId}`);
            
            // Hide all other forms first
            document.querySelectorAll('.upload-form').forEach(form => {
                form.style.display = 'none';
            });
            
            // Toggle current form
            if (formElement.style.display === 'none' || !formElement.style.display) {
                formElement.style.display = 'block';
                this.innerHTML = '<i class="mdi mdi-close"></i> Cancel';
                this.classList.remove('btn-success', 'btn-info');
                this.classList.add('btn-secondary');
            } else {
                formElement.style.display = 'none';
                const isExisting = this.getAttribute('data-has-file') === 'true';
                this.innerHTML = '<i class="mdi mdi-upload-outline"></i> ' + (isExisting ? 'Replace' : 'Upload');
                this.classList.remove('btn-secondary');
                this.classList.add(isExisting ? 'btn-info' : 'btn-success');
            }
        });
    });
    
    // Cancel upload button
    document.querySelectorAll('.cancel-upload').forEach(button => {
        button.addEventListener('click', function() {
            const formElement = this.closest('.upload-form');
            formElement.style.display = 'none';
            
            // Reset the upload button
            const documentId = formElement.getAttribute('id').replace('upload-form-', '');
            const uploadBtn = document.querySelector(`.upload-btn[data-document-id="${documentId}"]`);
            const isExisting = uploadBtn.getAttribute('data-has-file') === 'true';
            
            uploadBtn.innerHTML = '<i class="mdi mdi-upload-outline"></i> ' + (isExisting ? 'Replace' : 'Upload');
            uploadBtn.classList.remove('btn-secondary');
            uploadBtn.classList.add(isExisting ? 'btn-info' : 'btn-success');
        });
    });
    
    // Handle document upload via AJAX
    document.querySelectorAll('.document-upload-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const documentId = this.getAttribute('data-document-id');
            const uploadBtn = document.querySelector(`.upload-btn[data-document-id="${documentId}"]`);
            
            // Show loading modal
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();
            
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
                loadingModal.hide();
                
                if (data.success) {
                    // Show success message
                    showToast('success', 'Document uploaded successfully!');
                    
                    // Reload the page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast('error', data.message || 'Upload failed. Please try again.');
                }
            })
            .catch(error => {
                loadingModal.hide();
                console.error('Error:', error);
                showToast('error', 'An error occurred during upload.');
            });
        });
    });
    
    // Auto-submit form when both client and service are selected
    const clientSelect = document.getElementById('client_id');
    const serviceSelect = document.getElementById('service_id');
    const documentForm = document.getElementById('documentForm');
    
    // Check if both are already selected on page load
    if (clientSelect.value && serviceSelect.value) {
        // Form will auto-submit to load documents
    }
    
    // Toast notification function
    function showToast(type, message) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            // Create toast container if it doesn't exist
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(container);
        }
        
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();
        
        // Remove toast after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    }
    
    // Set data-has-file attribute for upload buttons
    document.querySelectorAll('.upload-btn').forEach(button => {
        const documentId = button.getAttribute('data-document-id');
        const uploadForm = document.getElementById(`upload-form-${documentId}`);
        const fileInput = uploadForm ? uploadForm.querySelector('input[type="file"]') : null;
        
        if (fileInput && fileInput.value) {
            button.setAttribute('data-has-file', 'true');
        } else {
            button.setAttribute('data-has-file', 'false');
        }
    });
});
</script>
@endpush