@extends('layouts.marketing')

@section('title', 'Upload Documents - ' . $service->name . ' - ' . $client->name)

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .client-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .client-avatar {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 600;
        color: white;
    }

    .client-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .client-details h2 {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: white;
    }

    .client-details p {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }

    .service-info {
        background: rgba(255, 255, 255, 0.1);
        padding: 1rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .service-icon {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 1.25rem;
    }

    .service-details h4 {
        color: white;
        margin-bottom: 0.25rem;
        font-weight: 600;
    }

    .service-details p {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 0;
        font-size: 0.875rem;
    }

    /* Document Checklist Card */
    .document-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .card-header {
        background: var(--gray-50);
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
    }

    .card-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }

    .card-header p {
        color: var(--gray-600);
        margin-bottom: 0;
        font-size: 0.875rem;
    }

    /* Document List */
    .document-list {
        padding: 0;
    }

    .document-item {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background-color 0.2s ease;
    }

    .document-item:hover {
        background-color: var(--gray-50);
    }

    .document-item:last-child {
        border-bottom: none;
    }

    .document-info {
        flex: 1;
    }

    .document-name {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .document-type {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .type-mandatory {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .type-optional {
        background-color: rgba(148, 163, 184, 0.1);
        color: var(--gray-600);
    }

    .document-status {
        margin-right: 1.5rem;
        min-width: 100px;
        text-align: center;
    }

    .status-badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background-color: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .status-uploaded {
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--secondary-color);
    }

    .status-rejected {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .rejection-reason {
        margin-top: 0.5rem;
        padding: 0.75rem;
        background: rgba(239, 68, 68, 0.05);
        border-radius: 0.375rem;
        border-left: 3px solid var(--danger-color);
        font-size: 0.875rem;
    }

    .rejection-reason strong {
        color: var(--danger-color);
        font-weight: 600;
    }

    /* Upload Area */
    .upload-area {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .file-input-wrapper input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .btn-file {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.625rem 1.25rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-file:hover {
        background: var(--primary-dark);
    }

    .btn-file:disabled {
        background: var(--gray-300);
        color: var(--gray-500);
        cursor: not-allowed;
    }

    .file-name {
        font-size: 0.875rem;
        color: var(--gray-600);
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .btn-upload {
        background: var(--secondary-color);
        color: white;
        border: none;
        padding: 0.625rem 1.25rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-upload:hover {
        background: #0da271;
    }

    .btn-upload:disabled {
        background: var(--gray-300);
        color: var(--gray-500);
        cursor: not-allowed;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .btn-view {
        background: var(--gray-100);
        color: var(--gray-700);
        border-color: var(--gray-300);
    }

    .btn-view:hover {
        background: var(--gray-200);
    }

    .btn-replace {
        background: transparent;
        color: var(--warning-color);
        border-color: var(--warning-color);
    }

    .btn-replace:hover {
        background: var(--warning-color);
        color: white;
    }

    .btn-delete {
        background: transparent;
        color: var(--danger-color);
        border-color: var(--danger-color);
    }

    .btn-delete:hover {
        background: var(--danger-color);
        color: white;
    }

    /* Progress Section */
    .progress-section {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .progress-header h4 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0;
    }

    .progress-text {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .progress-bar {
        height: 8px;
        background-color: var(--gray-200);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transition: width 0.3s ease;
        border-radius: 4px;
    }

    .progress-stats {
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    /* Validation Panel */
    .validation-panel {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .validation-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .validation-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .icon-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--secondary-color);
    }

    .icon-warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .validation-text h5 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }

    .validation-text p {
        color: var(--gray-600);
        margin-bottom: 0;
        font-size: 0.875rem;
    }

    .btn-proceed {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-proceed:hover:not(:disabled) {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-proceed:disabled {
        background: var(--gray-300);
        color: var(--gray-500);
        cursor: not-allowed;
    }

    /* Upload Success Modal */
    .upload-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        max-width: 400px;
        width: 90%;
        text-align: center;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-icon {
        width: 80px;
        height: 80px;
        background: rgba(16, 185, 129, 0.1);
        color: var(--secondary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1.5rem;
    }

    .modal-content h4 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }

    .modal-content p {
        color: var(--gray-600);
        margin-bottom: 1.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .client-info {
            flex-direction: column;
            text-align: center;
        }
        
        .document-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .document-status {
            margin-right: 0;
            align-self: flex-start;
        }
        
        .upload-area {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }
        
        .action-buttons {
            width: 100%;
            justify-content: flex-start;
        }
        
        .validation-panel {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        
        .validation-info {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="client-info">
            @if($client->profile_photo)
                @php
                    $imagePath = str_replace('public/', '', $client->profile_photo);
                @endphp
                <div class="client-avatar">
                    <img src="{{ asset('storage/' . $imagePath) }}" 
                         alt="{{ $client->name }}"
                         onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><rect width=\"100\" height=\"100\" fill=\"%236b7280\"/><text x=\"50\" y=\"50\" font-size=\"50\" text-anchor=\"middle\" dy=\".3em\" fill=\"white\">{{ strtoupper(substr($client->name, 0, 1)) }}</text></svg>'">
                </div>
            @else
                <div class="client-avatar">
                    {{ strtoupper(substr($client->name, 0, 1)) }}
                </div>
            @endif
            <div class="client-details">
                <h2>{{ $client->name }}</h2>
                <p><strong>Client ID:</strong> {{ $client->client_code ?? 'CLT-' . str_pad($client->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Email:</strong> {{ $client->email }} | <strong>Phone:</strong> {{ $client->phone }}</p>
            </div>
        </div>
        
        <div class="service-info">
            <div class="service-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="service-details">
                <h4>{{ $service->name }}</h4>
                <p>Service ID: {{ $service->id }} | Document Upload Checklist</p>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    @php
        $totalDocuments = $documents->count();
        $uploadedDocuments = $documents->where('upload_status', 'uploaded')->count();
        $pendingDocuments = $documents->where('upload_status', 'pending')->count();
        $rejectedDocuments = $documents->where('upload_status', 'rejected')->count();
        $progressPercentage = $totalDocuments > 0 ? round(($uploadedDocuments / $totalDocuments) * 100) : 0;
    @endphp
    
    <div class="progress-section">
        <div class="progress-header">
            <h4>Document Upload Progress</h4>
            <span class="progress-text">{{ $uploadedDocuments }} of {{ $totalDocuments }} documents uploaded</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $progressPercentage }}%"></div>
        </div>
        <div class="progress-stats">
            <span>0%</span>
            <span>{{ $progressPercentage }}%</span>
            <span>100%</span>
        </div>
    </div>

    <!-- Document Checklist Card -->
    <div class="document-card">
        <div class="card-header">
            <h3>Required Documents Checklist</h3>
            <p>Upload all required documents for {{ $service->name }} service. Mandatory documents are marked with <span class="type-mandatory" style="display: inline-block;">Mandatory</span>.</p>
        </div>
        
        <div class="document-list">
            @foreach($documents as $document)
            <div class="document-item" id="document-{{ $document->document_id }}">
                <div class="document-info">
                    <div class="document-name">
                        {{ $document->document_name }}
                        <span class="document-type {{ $document->type === 'mandatory' ? 'type-mandatory' : 'type-optional' }}">
                            {{ $document->type === 'mandatory' ? 'Mandatory' : 'Optional' }}
                        </span>
                    </div>
                    
                    @if($document->upload_status === 'rejected' && $document->rejected_reason)
                    <div class="rejection-reason">
                        <strong>Rejection Reason:</strong> {{ $document->rejected_reason }}
                    </div>
                    @endif
                </div>
                
                <div class="document-status">
                    <span class="status-badge status-{{ $document->upload_status }}">
                        {{ ucfirst($document->upload_status) }}
                    </span>
                </div>
                
                <div class="document-actions">
                    @if($document->upload_status === 'pending' || $document->upload_status === 'rejected')
                    <div class="upload-area">
                        <div class="file-input-wrapper">
                            <button type="button" class="btn-file" onclick="document.getElementById('file-{{ $document->document_id }}').click()">
                                <i class="fas fa-paperclip"></i> Choose File
                            </button>
                            <input type="file" 
                                   id="file-{{ $document->document_id }}" 
                                   data-document-id="{{ $document->document_id }}"
                                   data-client-id="{{ $client->id }}"
                                   data-service-id="{{ $service->id }}"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   style="display: none;">
                        </div>
                        <span class="file-name" id="file-name-{{ $document->document_id }}">No file chosen</span>
                        <button type="button" 
                                class="btn-upload upload-btn" 
                                data-document-id="{{ $document->document_id }}"
                                data-client-id="{{ $client->id }}"
                                data-service-id="{{ $service->id }}"
                                disabled>
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </div>
                    @elseif($document->upload_status === 'uploaded')
                    <div class="action-buttons">
                        @if($document->upload_id && $document->file_path)
                        <a href="{{ route('marketing.documents.view', $document->upload_id) }}" 
                           target="_blank" 
                           class="btn-action btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @endif
                        
                        <div class="file-input-wrapper">
                            <button type="button" 
                                    class="btn-action btn-replace replace-btn"
                                    onclick="document.getElementById('replace-file-{{ $document->document_id }}').click()">
                                <i class="fas fa-exchange-alt"></i> Replace
                            </button>
                            <input type="file" 
                                   id="replace-file-{{ $document->document_id }}" 
                                   data-document-id="{{ $document->document_id }}"
                                   data-client-id="{{ $client->id }}"
                                   data-service-id="{{ $service->id }}"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   style="display: none;">
                        </div>
                        
                        @if($document->upload_id)
                        <form action="{{ route('marketing.documents.delete', $document->upload_id) }}" 
                              method="POST" 
                              class="delete-form"
                              onsubmit="return confirm('Are you sure you want to delete this document?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Validation Panel -->
    @php
        $allMandatoryUploaded = $documents
            ->where('type', 'mandatory')
            ->every(fn($doc) => $doc->upload_status === 'uploaded');
        
        $pendingMandatory = $documents
            ->where('type', 'mandatory')
            ->whereIn('upload_status', ['pending', 'rejected'])
            ->count();
    @endphp
    
    <div class="validation-panel">
        <div class="validation-info">
            <div class="validation-icon {{ $allMandatoryUploaded ? 'icon-success' : 'icon-warning' }}">
                @if($allMandatoryUploaded)
                <i class="fas fa-check-circle"></i>
                @else
                <i class="fas fa-exclamation-triangle"></i>
                @endif
            </div>
            <div class="validation-text">
                @if($allMandatoryUploaded)
                <h5>All documents are ready!</h5>
                <p>All mandatory documents have been uploaded successfully.</p>
                @else
                <h5>Action Required</h5>
                <p>{{ $pendingMandatory }} mandatory document(s) need to be uploaded.</p>
                @endif
            </div>
        </div>
        
        <div class="validation-actions">
            <a href="{{ route('marketing.documents.upload') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Selection
            </a>
            <button type="button" 
                    class="btn-proceed" 
                    id="proceedBtn"
                    {{ $allMandatoryUploaded ? '' : 'disabled' }}>
                <i class="fas fa-check-circle"></i> Complete Upload
            </button>
        </div>
    </div>
</div>

<!-- Upload Success Modal -->
<div class="upload-modal" id="uploadModal">
    <div class="modal-content">
        <div class="modal-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h4>Upload Successful!</h4>
        <p id="modalMessage">Document has been uploaded successfully.</p>
        <button type="button" class="btn btn-primary" onclick="closeModal()">
            Continue
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle file selection
    $('input[type="file"]').on('change', function() {
        const documentId = $(this).data('document-id');
        const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        const fileSize = this.files[0] ? this.files[0].size : 0;
        
        // Update file name display
        $(`#file-name-${documentId}`).text(fileName);
        
        // Enable upload button if file is selected
        if (this.files.length > 0) {
            // Check file size (2MB = 2097152 bytes)
            if (fileSize > 2097152) {
                alert('File size must be less than 2MB');
                $(this).val('');
                $(`#file-name-${documentId}`).text('No file chosen');
                $(`.upload-btn[data-document-id="${documentId}"]`).prop('disabled', true);
                return;
            }
            
            // Check file type
            const allowedTypes = ['.pdf', '.jpg', '.jpeg', '.png', '.doc', '.docx'];
            const fileExtension = fileName.split('.').pop().toLowerCase();
            const isValidType = allowedTypes.some(type => fileName.toLowerCase().endsWith(type));
            
            if (!isValidType) {
                alert('Please select a valid file type (PDF, JPG, PNG, DOC, DOCX)');
                $(this).val('');
                $(`#file-name-${documentId}`).text('No file chosen');
                $(`.upload-btn[data-document-id="${documentId}"]`).prop('disabled', true);
                return;
            }
            
            $(`.upload-btn[data-document-id="${documentId}"]`).prop('disabled', false);
        } else {
            $(`.upload-btn[data-document-id="${documentId}"]`).prop('disabled', true);
        }
    });

    // Handle upload button click
    $('.upload-btn').on('click', function() {
        const documentId = $(this).data('document-id');
        const clientId = $(this).data('client-id');
        const serviceId = $(this).data('service-id');
        const fileInput = $(`#file-${documentId}`);
        
        if (!fileInput[0].files.length) {
            alert('Please select a file to upload');
            return;
        }
        
        uploadDocument(documentId, clientId, serviceId, fileInput[0].files[0]);
    });

    // Handle replace button functionality
    $('input[id^="replace-file-"]').on('change', function() {
        const documentId = $(this).data('document-id');
        const clientId = $(this).data('client-id');
        const serviceId = $(this).data('service-id');
        
        if (this.files.length > 0) {
            uploadDocument(documentId, clientId, serviceId, this.files[0], true);
        }
    });

    // Function to upload document
    function uploadDocument(documentId, clientId, serviceId, file, isReplace = false) {
        const formData = new FormData();
        formData.append('client_id', clientId);
        formData.append('service_id', serviceId);
        formData.append('document_id', documentId);
        formData.append('document', file);
        
        // Show loading state
        const uploadBtn = $(`.upload-btn[data-document-id="${documentId}"]`);
        const originalText = uploadBtn.html();
        uploadBtn.html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
        uploadBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("marketing.documents.upload") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Show success modal
                showModal('Document uploaded successfully!');
                
                // Reload page after 2 seconds to show updated status
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while uploading the document.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                alert(errorMessage);
                
                // Reset button state
                uploadBtn.html(originalText);
                uploadBtn.prop('disabled', false);
            }
        });
    }

    // Handle delete form submission
    $('.delete-form').on('submit', function(e) {
        if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
        
        // Show loading
        const deleteBtn = $(this).find('button[type="submit"]');
        const originalText = deleteBtn.html();
        deleteBtn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
        deleteBtn.prop('disabled', true);
        
        return true; // Allow form submission
    });

    // Proceed button click
    $('#proceedBtn').on('click', function() {
        if (!$(this).prop('disabled')) {
            // Check if all mandatory documents are uploaded
            const allMandatoryUploaded = {{ $allMandatoryUploaded ? 'true' : 'false' }};
            
            if (allMandatoryUploaded) {
                // Redirect to next step or show success
                showModal('All documents uploaded successfully! You can proceed to the next step.');
                
                // Here you can redirect to the next step
                // window.location.href = '/marketing/next-step';
            } else {
                alert('Please upload all mandatory documents before proceeding.');
            }
        }
    });

    // Show modal function
    function showModal(message) {
        $('#modalMessage').text(message);
        $('#uploadModal').css('display', 'flex');
    }

    // Close modal function
    function closeModal() {
        $('#uploadModal').css('display', 'none');
    }

    // Close modal on outside click
    $('#uploadModal').on('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        // You can implement a toast notification system here
        // For now, using alert
        if (type === 'error') {
            alert('Error: ' + message);
        } else {
            alert(message);
        }
    }

    // Check for session messages
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('{{ $error }}', 'error');
        @endforeach
    @endif
});
</script>
@endsection