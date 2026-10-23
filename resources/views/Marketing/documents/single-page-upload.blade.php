@extends('layouts.marketing')

@section('title', 'Document Upload - SharpEdge Marketing')

@section('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    /* Statistics Cards */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-info h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
        line-height: 1;
    }
    
    .stat-change {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .stat-change.positive {
        color: var(--secondary-color);
    }
    
    .stat-change.negative {
        color: var(--danger-color);
    }
    
    .stat-info p {
        color: var(--gray-500);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* Main Content Card */
    .main-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }
    
    /* Form Section */
    .form-section {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
    }
    
    .form-row {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    
    .form-group {
        flex: 1;
        min-width: 250px;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .form-select, .form-control {
        width: 100%;
        padding: 0.625rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-700);
        background: white;
    }
    
    .form-select:focus, .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        height: 42px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }
    
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    /* Documents Table Section */
    .table-container {
        padding: 0;
        overflow-x: auto;
    }
    
    .documents-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    /* Black Table Header */
    .documents-table thead {
        background-color: #3b4f6b !important;
    }
    
    .documents-table th {
        padding: 1rem 1rem;
        text-align: left;
        font-weight: 600;
        color: white !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: none;
        white-space: nowrap;
    }
    
    /* Table body styles */
    .documents-table td {
        padding: 1rem 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
        color: var(--gray-700);
        font-size: 0.875rem;
        background: white;
    }
    
    .documents-table tbody tr:hover {
        background-color: var(--gray-50);
    }
    
    /* Document Type Badges */
    .document-type-badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .badge-mandatory {
        background: rgba(220, 38, 38, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(220, 38, 38, 0.2);
    }
    
    .badge-optional {
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    /* Document Status Badges */
    .document-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .status-dot.pending {
        background: var(--warning-color);
    }
    
    .status-dot.uploaded {
        background: var(--secondary-color);
    }
    
    .status-dot.rejected {
        background: var(--danger-color);
    }
    
    .status-dot.approved {
        background: var(--success-color);
    }
    
    .status-text {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .status-pending-text {
        color: var(--warning-color);
    }
    
    .status-uploaded-text {
        color: var(--secondary-color);
    }
    
    .status-rejected-text {
        color: var(--danger-color);
    }
    
    .status-approved-text {
        color: var(--success-color);
    }
    
    /* File Size */
    .file-size {
        font-family: 'Courier New', monospace;
        font-size: 0.8125rem;
        color: var(--gray-600);
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .btn-upload {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        text-decoration: none;
    }
    
    .btn-upload:hover {
        background: linear-gradient(135deg, var(--primary-dark), #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
        color: white;
    }
    
    .btn-view {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
        border: 1px solid rgba(34, 197, 94, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        text-decoration: none;
    }
    
    .btn-view:hover {
        background: rgba(34, 197, 94, 0.2);
        border-color: #16a34a;
        color: #16a34a;
    }
    
    .btn-replace {
        background: rgba(234, 179, 8, 0.1);
        color: #ca8a04;
        border: 1px solid rgba(234, 179, 8, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .btn-replace:hover {
        background: rgba(234, 179, 8, 0.2);
        border-color: #ca8a04;
        color: #ca8a04;
    }
    
    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .btn-delete:hover {
        background: rgba(239, 68, 68, 0.2);
        border-color: #dc2626;
        color: #dc2626;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: var(--gray-500);
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--gray-300);
    }
    
    .empty-state p {
        font-size: 1rem;
        margin-bottom: 0;
    }
    
    /* File Upload Input */
    .file-input-container {
        position: relative;
    }
    
    .file-input {
        position: absolute;
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        z-index: -1;
    }
    
    /* Rejected Reason */
    .rejected-reason {
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: var(--danger-color);
        background: rgba(239, 68, 68, 0.1);
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
        border-left: 3px solid var(--danger-color);
    }
    
    /* Upload Status Section */
    .upload-status-section {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
    }
    
    .status-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
    
    .status-left {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .status-right {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .status-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-200);
    }
    
    .status-card h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.75rem;
    }
    
    .upload-stats {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .stats-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .stats-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .stats-info p {
        color: var(--gray-600);
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    .current-status {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: rgba(59, 130, 246, 0.05);
        border-radius: 0.5rem;
        border-left: 4px solid var(--primary-color);
    }
    
    .current-status i {
        color: var(--primary-color);
        font-size: 1.25rem;
    }
    
    .current-status-content h5 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.125rem;
    }
    
    .current-status-content p {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-bottom: 0;
    }
    
    /* Recent Activity */
    .recent-activity-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        background: white;
        border-radius: 0.5rem;
        border: 1px solid var(--gray-200);
        transition: all 0.2s ease;
    }
    
    .activity-item:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
    }
    
    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--secondary-color), #0da271);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.875rem;
        flex-shrink: 0;
    }
    
    .activity-content h6 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .activity-content p {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-bottom: 0;
    }
    
    .activity-time {
        font-size: 0.75rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stats-cards {
            grid-template-columns: 1fr 1fr;
        }
        
        .form-row {
            flex-direction: column;
            gap: 1rem;
        }
        
        .form-group {
            min-width: 100%;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .documents-table {
            min-width: 800px;
        }
        
        .status-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .btn-upload, .btn-view, .btn-replace, .btn-delete {
            width: 100%;
            justify-content: center;
        }
    }
    
    @media (max-width: 576px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }
        
        .upload-stats {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="stat-info">
                <h3>24</h3>
                <p>ACTIVE SERVICES</p>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>15% from last month</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary-color), #0da271);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>18</h3>
                <p>ACTIVE CLIENTS</p>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>5% from last month</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #e58e0b);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>7</h3>
                <p>PENDING UPLOADS</p>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down"></i>
                    <span>3% from last week</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>5</h3>
                <p>COMPLETED TODAY</p>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>23% from yesterday</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="main-card">
        <!-- Selection Form -->
        <div class="form-section">
            <form method="GET" action="{{ route('marketing.documents.upload.index') }}" id="clientServiceForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="client_id">Select Client</label>
                        <select name="client_id" id="client_id" class="form-select" required>
                            <option value="">Choose a Client --</option>
                            @foreach($clients as $clientOption)
                                <option value="{{ $clientOption->id }}" 
                                    {{ $client && $client->id == $clientOption->id ? 'selected' : '' }}>
                                    {{ $clientOption->name }}
                                    @if($clientOption->gender == 'male')
                                        (Mr)
                                    @elseif($clientOption->gender == 'female')
                                        (Ms)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="service_id">Select Service</label>
                        <select name="service_id" id="service_id" class="form-select" 
                            {{ !$client ? 'disabled' : '' }} required>
                            <option value="">Select Service --</option>
                            @foreach($services as $serviceOption)
                                <option value="{{ $serviceOption->id }}" 
                                    {{ $service && $service->id == $serviceOption->id ? 'selected' : '' }}>
                                    {{ $serviceOption->service_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" 
                            {{ !$client || !$service ? 'disabled' : '' }}>
                            <i class="fas fa-search me-1"></i>
                            Show Required Documents
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Documents Table -->
        @if($client && $service)
            <div class="table-container">
                @if($documents && $documents->count() > 0)
                    <table class="documents-table">
                        <thead>
                            <tr>
                                <th>DOCUMENT NAME</th>
                                <th>TYPE</th>
                                <th>STATUS</th>
                                <th>FILE SIZE</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr data-document-id="{{ $document->document_id }}">
                                    <td>
                                        <strong>{{ $document->document_name }}</strong>
                                        @if($document->rejected_reason)
                                            <div class="rejected-reason">
                                                <strong>Rejection Reason:</strong> {{ $document->rejected_reason }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="document-type-badge {{ $document->type == 'mandatory' ? 'badge-mandatory' : 'badge-optional' }}">
                                            {{ strtoupper($document->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="document-status-badge">
                                            @php
                                                $status = $document->upload_status ?? 'pending';
                                                $statusClass = 'status-' . $status;
                                            @endphp
                                            <span class="status-dot {{ $statusClass }}"></span>
                                            <span class="status-text {{ $statusClass }}-text">
                                                {{ strtoupper($status) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($document->file_path)
                                            <span class="file-size">
                                                @php
                                                    // Format file size
                                                    $bytes = $document->file_size ?? 0;
                                                    if ($bytes >= 1048576) {
                                                        echo round($bytes / 1048576, 2) . ' MB';
                                                    } elseif ($bytes >= 1024) {
                                                        echo round($bytes / 1024, 2) . ' KB';
                                                    } else {
                                                        echo $bytes . ' Bytes';
                                                    }
                                                @endphp
                                            </span>
                                        @else
                                            <span class="text-muted">0 Bytes</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if(!$document->file_path || $document->upload_status == 'rejected')
                                                <div class="file-input-container">
                                                    <input type="file" 
                                                           class="file-input" 
                                                           id="file_{{ $document->document_id }}"
                                                           data-client-id="{{ $client->id }}"
                                                           data-service-id="{{ $service->id }}"
                                                           data-document-id="{{ $document->document_id }}"
                                                           accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx,.csv">
                                                    <button type="button" 
                                                            class="btn-upload"
                                                            onclick="document.getElementById('file_{{ $document->document_id }}').click()">
                                                        <i class="fas fa-upload me-1"></i>
                                                        {{ $document->upload_status == 'rejected' ? 'Re-upload' : 'Upload' }}
                                                    </button>
                                                </div>
                                            @endif
                                            
                                            @if($document->file_path)
                                                <a href="{{ route('marketing.documents.view', $document->upload_id) }}" 
                                                   target="_blank" 
                                                   class="btn-view">
                                                    <i class="fas fa-eye me-1"></i>
                                                    View
                                                </a>
                                                
                                                <div class="file-input-container">
                                                    <input type="file" 
                                                           class="file-input" 
                                                           id="replace_{{ $document->document_id }}"
                                                           data-client-id="{{ $client->id }}"
                                                           data-service-id="{{ $service->id }}"
                                                           data-document-id="{{ $document->document_id }}"
                                                           accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx,.csv">
                                                    <button type="button" 
                                                            class="btn-replace"
                                                            onclick="document.getElementById('replace_{{ $document->document_id }}').click()">
                                                        <i class="fas fa-sync me-1"></i>
                                                        Replace
                                                    </button>
                                                </div>
                                                
                                                <button type="button" 
                                                        class="btn-delete delete-document"
                                                        data-upload-id="{{ $document->upload_id }}"
                                                        data-document-name="{{ $document->document_name }}">
                                                    <i class="fas fa-trash me-1"></i>
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <p>No document requirements found for this service</p>
                    </div>
                @endif
            </div>

            <!-- Upload Status Section -->
            <div class="upload-status-section">
                <div class="status-grid">
                    <!-- Left Column: Upload Stats -->
                    <div class="status-left">
                        <div class="status-card">
                            <h4>Upload Status</h4>
                            <div class="upload-stats">
                                @php
                                    // Calculate upload stats
                                    $mandatoryDocuments = $documents->where('type', 'mandatory');
                                    $totalMandatory = $mandatoryDocuments->count();
                                    $uploadedMandatory = $mandatoryDocuments->where('upload_status', 'uploaded')->count() + 
                                                         $mandatoryDocuments->where('upload_status', 'approved')->count();
                                    $pendingMandatory = $totalMandatory - $uploadedMandatory;
                                @endphp
                                <div class="stats-circle">
                                    {{ $uploadedMandatory }}/{{ $totalMandatory }}
                                </div>
                                <div class="stats-info">
                                    <h3>{{ $uploadedMandatory }} of {{ $totalMandatory }} mandatory documents</h3>
                                    <p>
                                        @if($pendingMandatory > 0)
                                            {{ $pendingMandatory }} document(s) pending upload
                                        @else
                                            All mandatory documents uploaded
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <!-- MANDATORY / OPTIONAL breakdown -->
                            @php
                                $optionalDocuments = $documents->where('type', 'optional');
                                $totalOptional = $optionalDocuments->count();
                                $uploadedOptional = $optionalDocuments->where('upload_status', 'uploaded')->count() +
                                                    $optionalDocuments->where('upload_status', 'approved')->count();
                            @endphp
                            
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-sm text-gray-600">MANDATORY</span>
                                    <span class="text-sm font-semibold">{{ $uploadedMandatory }}/{{ $totalMandatory }}</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $totalMandatory > 0 ? ($uploadedMandatory / $totalMandatory) * 100 : 0 }}%"></div>
                                </div>
                                
                                @if($totalOptional > 0)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-sm text-gray-600">OPTIONAL</span>
                                    <span class="text-sm font-semibold">{{ $uploadedOptional }}/{{ $totalOptional }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-secondary" style="width: {{ $totalOptional > 0 ? ($uploadedOptional / $totalOptional) * 100 : 0 }}%"></div>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Current Status -->
                        <div class="status-card">
                            <h4>Current Status</h4>
                            <div class="current-status">
                                <i class="fas fa-info-circle"></i>
                                <div class="current-status-content">
                                    <h5>
                                        @if($uploadedMandatory == 0 && $totalMandatory > 0)
                                            NOT STARTED
                                        @elseif($uploadedMandatory > 0 && $uploadedMandatory < $totalMandatory)
                                            IN PROGRESS
                                        @elseif($uploadedMandatory == $totalMandatory && $totalMandatory > 0)
                                            COMPLETED
                                        @else
                                            NOT STARTED
                                        @endif
                                    </h5>
                                    <p>
                                        @if($client && $service)
                                            {{ $client->name }} - {{ $service->service_name }}
                                        @else
                                            Select client and service to begin document upload
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Recent Activity -->
                    <div class="status-right">
                        <div class="status-card">
                            <h4>Recent Activity</h4>
                            <div class="recent-activity-list">
                                @php
                                    // Get recent uploads (mock data - in real app, you'd fetch from database)
                                    $recentActivities = [
                                        (object) [
                                            'title' => 'Company PAN Card uploaded for ' . ($client ? $client->name : 'Client'),
                                            'time' => '10 minutes ago',
                                            'icon' => 'fas fa-file-upload'
                                        ],
                                        (object) [
                                            'title' => 'Aadhaar Card uploaded for ' . ($client ? $client->name : 'Client'),
                                            'time' => '1 hour ago',
                                            'icon' => 'fas fa-file-upload'
                                        ],
                                        (object) [
                                            'title' => 'Document verification started',
                                            'time' => '2 hours ago',
                                            'icon' => 'fas fa-clipboard-check'
                                        ]
                                    ];
                                @endphp
                                
                                @if($uploadedMandatory > 0)
                                    @foreach($recentActivities as $activity)
                                        <div class="activity-item">
                                            <div class="activity-icon">
                                                <i class="{{ $activity->icon }}"></i>
                                            </div>
                                            <div class="activity-content">
                                                <h6>{{ $activity->title }}</h6>
                                                <p class="activity-time">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ $activity->time }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-state" style="padding: 1rem;">
                                        <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No recent activity</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State when no client/service selected -->
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>Please select a client and service to view required documents</p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the document <strong id="deleteDocumentName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteDocumentForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Document</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Show success/error messages from session
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    @endif

    // Enable/Disable service dropdown based on client selection
    $('#client_id').on('change', function() {
        const clientId = $(this).val();
        const serviceSelect = $('#service_id');
        const submitBtn = $('button[type="submit"]');
        
        if (clientId) {
            serviceSelect.prop('disabled', false);
            
            // AJAX call to get client-specific services
            $.ajax({
                url: '{{ route("marketing.documents.upload.client.services", ":clientId") }}'.replace(':clientId', clientId),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        serviceSelect.empty();
                        serviceSelect.append('<option value="">Select Service --</option>');
                        
                        $.each(response.services, function(index, service) {
                            serviceSelect.append(
                                $('<option>', {
                                    value: service.id,
                                    text: service.service_name
                                })
                            );
                        });
                    }
                },
                error: function() {
                    toastr.error('Failed to load services. Please try again.');
                }
            });
        } else {
            serviceSelect.prop('disabled', true);
            serviceSelect.val('');
        }
        
        // Update submit button state
        submitBtn.prop('disabled', !clientId || !serviceSelect.val());
    });

    // Update submit button state when service changes
    $('#service_id').on('change', function() {
        const clientId = $('#client_id').val();
        const serviceId = $(this).val();
        const submitBtn = $('button[type="submit"]');
        
        submitBtn.prop('disabled', !clientId || !serviceId);
    });

    // Handle file upload (works for both new uploads and replacements)
    $('input[type="file"].file-input').on('change', function(e) {
        const file = this.files[0];
        const clientId = $(this).data('client-id');
        const serviceId = $(this).data('service-id');
        const documentId = $(this).data('document-id');
        const row = $(this).closest('tr');
        const button = $(this).siblings('button');
        
        if (!file) {
            return;
        }
        
        // Validate file size (2MB = 2097152 bytes)
        if (file.size > 2097152) {
            toastr.error('File size must be less than 2MB');
            $(this).val('');
            return;
        }
        
        // Validate file type
        const validTypes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv'
        ];
        
        if (!validTypes.includes(file.type)) {
            toastr.error('Only PDF, JPG, PNG, and Excel files are allowed');
            $(this).val('');
            return;
        }
        
        // Create FormData
        const formData = new FormData();
        formData.append('document', file);
        formData.append('client_id', clientId);
        formData.append('service_id', serviceId);
        formData.append('document_id', documentId);
        formData.append('_token', '{{ csrf_token() }}');
        
        // Show loading state
        button.prop('disabled', true);
        const originalText = button.html();
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> ' + (button.hasClass('btn-replace') ? 'Replacing...' : 'Uploading...'));
        
        // Send AJAX request to the same upload endpoint
        $.ajax({
            url: '{{ route("marketing.documents.upload.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(button.hasClass('btn-replace') ? 'Document replaced successfully' : 'Document uploaded successfully');
                    // Reload page to show updated status
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message || (button.hasClass('btn-replace') ? 'Replace failed' : 'Upload failed'));
                    button.prop('disabled', false);
                    button.html(originalText);
                }
            },
            error: function(xhr) {
                let errorMessage = button.hasClass('btn-replace') ? 'Replace failed' : 'Upload failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
                button.prop('disabled', false);
                button.html(originalText);
            }
        });
    });

    // Handle delete document
    $(document).on('click', '.delete-document', function() {
        const uploadId = $(this).data('upload-id');
        const documentName = $(this).data('document-name');
        
        $('#deleteDocumentName').text(documentName);
        $('#deleteDocumentForm').attr('action', '{{ route("marketing.documents.delete", ":id") }}'.replace(':id', uploadId));
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteDocumentModal'));
        deleteModal.show();
    });

    // Auto-hide toastr messages after 5 seconds
    setTimeout(function() {
        $('.toast').fadeOut('slow');
    }, 5000);
});
</script>
@endsection