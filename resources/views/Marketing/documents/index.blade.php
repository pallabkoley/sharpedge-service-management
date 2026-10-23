@extends('layouts.marketing')

@section('title', 'Document Requirements - SharpEdge Marketing')

@section('content')
    <div class="header">
        <div class="row align-items-center">
            <div class="col">
                <h2>Document Requirements</h2>
                <p>View and manage document requirements for all services</p>
            </div>
            <div class="col-auto">
                <!-- User Dropdown -->
                <div class="dropdown">
                    <!--<button class="btn btn-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">-->
                    <!--    <i class="fas fa-user-circle me-2"></i> {{ Auth::user()->name ?? 'Marketing User' }}-->
                    <!--</button>-->
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards - 4 cards in one row -->
    <div class="stats-cards mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalDocuments }}</h3>
                <p>Total Documents</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success-color), #0d9e5d);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $mandatoryCount }}</h3>
                <p>Mandatory</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #e58e0b);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $optionalCount }}</h3>
                <p>Optional</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--info-color), #0a7ea0);">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $servicesCount }}</h3>
                <p>Services</p>
            </div>
        </div>
    </div>

    <!-- Document Cards Section - 2 cards per row -->
    <div class="content-section">
        <!-- Tabs Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-tabs" id="documentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                            <i class="fas fa-layer-group me-2"></i> All Documents
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mandatory-tab" data-bs-toggle="tab" data-bs-target="#mandatory" type="button" role="tab">
                            <i class="fas fa-exclamation-circle me-2"></i> Mandatory
                            <span class="badge bg-danger ms-2">{{ $mandatoryCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="optional-tab" data-bs-toggle="tab" data-bs-target="#optional" type="button" role="tab">
                            <i class="fas fa-clock me-2"></i> Optional
                            <span class="badge bg-warning ms-2">{{ $optionalCount }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Search Bar - Placed below tabs as shown in image -->
        <div class="row mb-4">
            <div class="col-12">
                <form method="GET" action="{{ route('marketing.documents.index') }}" class="search-form">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-input" 
                               placeholder="Search documents..." 
                               value="{{ request('search') }}">
                        <button class="btn btn-primary search-btn" type="submit">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                        @if(request('search'))
                        <a href="{{ route('marketing.documents.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Results Info -->
        @if(request('search'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="search-results-info">
                    <i class="fas fa-search me-2"></i>
                    Search results for: <strong>"{{ request('search') }}"</strong>
                    <span class="badge bg-primary ms-2">{{ $servicesWithDocs->count() }} services found</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Tab Content -->
        <div class="tab-content" id="documentTabsContent">
            <!-- All Documents Tab - 2 cards per row -->
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="row g-4">
                    @forelse($servicesWithDocs as $service)
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="document-card">
                                <div class="document-card-header">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="service-icon me-2">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                        <div>
                                            <h5 class="document-title">{{ $service->service_name }}</h5>
                                            <span class="document-code">{{ $service->service_code }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="document-card-body">
                                    <h6 class="section-title">Required Documents:</h6>
                                    
                                    <!-- Mandatory Documents -->
                                    @if($service->documentRequirements->where('type', 'mandatory')->count() > 0)
                                        <div class="document-section">
                                            <span class="document-type-badge mandatory">Mandatory</span>
                                            <ul class="document-list">
                                                @foreach($service->documentRequirements->where('type', 'mandatory') as $requirement)
                                                    <li>
                                                        <i class="fas fa-file-circle-check text-success me-2"></i>
                                                        {{ $requirement->documentMaster->document_name }}
                                                        <span class="file-type-badge">{{ $requirement->documentMaster->file_type }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <!-- Optional Documents -->
                                    @if($service->documentRequirements->where('type', 'optional')->count() > 0)
                                        <div class="document-section mt-3">
                                            <span class="document-type-badge optional">Optional</span>
                                            <ul class="document-list">
                                                @foreach($service->documentRequirements->where('type', 'optional') as $requirement)
                                                    <li>
                                                        <i class="fas fa-file-circle-plus text-warning me-2"></i>
                                                        {{ $requirement->documentMaster->document_name }}
                                                        <span class="file-type-badge">{{ $requirement->documentMaster->file_type }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="document-card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="document-count">
                                            <i class="fas fa-file me-1"></i>
                                            {{ $service->documentRequirements->count() }} documents
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h3>No Document Requirements</h3>
                                <p>
                                    @if(request('search'))
                                        No document requirements found for your search.
                                    @else
                                        No document requirements have been set up for any services yet.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Mandatory Documents Tab - 2 cards per row -->
            <div class="tab-pane fade" id="mandatory" role="tabpanel">
                <div class="row g-4">
                    @php
                        $mandatoryServices = $servicesWithDocs->filter(function($service) {
                            return $service->documentRequirements->where('type', 'mandatory')->count() > 0;
                        });
                    @endphp
                    
                    @forelse($mandatoryServices as $service)
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="document-card">
                                <div class="document-card-header">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="service-icon me-2">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                        <div>
                                            <h5 class="document-title">{{ $service->service_name }}</h5>
                                            <span class="document-code">{{ $service->service_code }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="document-card-body">
                                    <h6 class="section-title">Mandatory Documents:</h6>
                                    <ul class="document-list">
                                        @foreach($service->documentRequirements->where('type', 'mandatory') as $requirement)
                                            <li>
                                                <i class="fas fa-file-circle-check text-success me-2"></i>
                                                {{ $requirement->documentMaster->document_name }}
                                                <span class="file-type-badge">{{ $requirement->documentMaster->file_type }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                
                                <div class="document-card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="document-count">
                                            <i class="fas fa-file me-1"></i>
                                            {{ $service->documentRequirements->where('type', 'mandatory')->count() }} mandatory
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <h3>No Mandatory Documents</h3>
                                <p>No mandatory document requirements found.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Optional Documents Tab - 2 cards per row -->
            <div class="tab-pane fade" id="optional" role="tabpanel">
                <div class="row g-4">
                    @php
                        $optionalServices = $servicesWithDocs->filter(function($service) {
                            return $service->documentRequirements->where('type', 'optional')->count() > 0;
                        });
                    @endphp
                    
                    @forelse($optionalServices as $service)
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="document-card">
                                <div class="document-card-header">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="service-icon me-2">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                        <div>
                                            <h5 class="document-title">{{ $service->service_name }}</h5>
                                            <span class="document-code">{{ $service->service_code }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="document-card-body">
                                    <h6 class="section-title">Optional Documents:</h6>
                                    <ul class="document-list">
                                        @foreach($service->documentRequirements->where('type', 'optional') as $requirement)
                                            <li>
                                                <i class="fas fa-file-circle-plus text-warning me-2"></i>
                                                {{ $requirement->documentMaster->document_name }}
                                                <span class="file-type-badge">{{ $requirement->documentMaster->file_type }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                
                                <div class="document-card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="document-count">
                                            <i class="fas fa-file me-1"></i>
                                            {{ $service->documentRequirements->where('type', 'optional')->count() }} optional
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h3>No Optional Documents</h3>
                                <p>No optional document requirements found.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>© {{ date('Y') }} <strong>SharpEdge Marketing</strong>. All rights reserved.</p>
        <p class="text-muted small">Document Requirements • Last updated: {{ now()->format('M d, Y H:i') }}</p>
    </div>
@endsection

@section('styles')
<style>
    /* Custom Styles for Document Requirements Page */
    
    /* Statistics Cards */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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
    
    .stat-info p {
        color: var(--gray-500);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0;
    }
    
    /* Tab Styling */
    .nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        padding: 0 5px;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        margin: 0 0.25rem;
        border-radius: 0.5rem 0.5rem 0 0;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .nav-tabs .nav-link:hover {
        color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.05);
    }
    
    .nav-tabs .nav-link.active {
        color: #3b82f6;
        background-color: white;
        border-bottom: 3px solid #3b82f6;
        font-weight: 600;
    }
    
    .nav-tabs .badge {
        font-size: 0.65rem;
        padding: 0.2rem 0.4rem;
    }
    
    /* Search Form */
    .search-form {
        margin-bottom: 1.5rem;
    }
    
    .search-input {
        border-radius: 0.5rem 0 0 0.5rem;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
    
    .search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .search-btn {
        border-radius: 0 0.5rem 0.5rem 0;
        padding: 0.75rem 1.5rem;
    }
    
    .btn-outline-secondary {
        margin-left: 0.5rem;
        padding: 0.75rem 1.25rem;
    }
    
    /* Search Results Info */
    .search-results-info {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        color: #475569;
        font-size: 0.875rem;
    }
    
    .search-results-info .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Document Card */
    .document-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .document-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-color: #3b82f6;
    }
    
    .document-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, #3b82f6, #60a5fa);
    }
    
    .document-card-header {
        padding: 1.5rem 1.5rem 0;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 1rem;
    }
    
    .service-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .document-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .document-code {
        font-size: 0.75rem;
        color: #64748b;
        background-color: #f8fafc;
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
    }
    
    .document-card-body {
        padding: 0 1.5rem;
        flex-grow: 1;
    }
    
    .section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .document-section {
        margin-bottom: 1rem;
    }
    
    .document-type-badge {
        display: inline-block;
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    
    .document-type-badge.mandatory {
        background-color: rgba(34, 197, 94, 0.1);
        color: #16a34a;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }
    
    .document-type-badge.optional {
        background-color: rgba(245, 158, 11, 0.1);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }
    
    .document-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .document-list li {
        padding: 0.5rem 0;
        color: #475569;
        font-size: 0.875rem;
        display: flex;
        align-items: flex-start;
        border-bottom: 1px dashed #f1f5f9;
    }
    
    .document-list li:last-child {
        border-bottom: none;
    }
    
    .document-list i {
        margin-top: 0.125rem;
        font-size: 0.75rem;
    }
    
    .file-type-badge {
        font-size: 0.65rem;
        padding: 0.125rem 0.375rem;
        background-color: #f1f5f9;
        color: #64748b;
        border-radius: 3px;
        margin-left: 0.5rem;
        font-weight: 500;
    }
    
    .document-card-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        margin-top: auto;
        background-color: #f8fafc;
        border-radius: 0 0 0.75rem 0.75rem;
    }
    
    .document-count {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background: white;
        border-radius: 0.75rem;
        border: 2px dashed #e2e8f0;
    }
    
    .empty-state-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #94a3b8;
        font-size: 1.5rem;
    }
    
    .empty-state h3 {
        color: #475569;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: #94a3b8;
        max-width: 400px;
        margin: 0 auto;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stats-cards {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .nav-tabs .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .document-card {
            margin-bottom: 1rem;
        }
        
        .header .row {
            flex-direction: column;
            text-align: center;
        }
        
        .header .col-auto {
            margin-top: 1rem;
        }
        
        .search-input {
            width: 100%;
        }
        
        .input-group {
            flex-wrap: nowrap;
        }
    }
    
    @media (max-width: 576px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }
        
        .nav-tabs {
            flex-direction: column;
        }
        
        .nav-tabs .nav-link {
            margin: 0.125rem 0;
            border-radius: 0.5rem;
        }
        
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #3b82f6;
            border-radius: 0.5rem;
        }
        
        .input-group {
            flex-direction: column;
        }
        
        .search-input {
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .search-btn {
            border-radius: 0.5rem;
            width: 100%;
        }
        
        .btn-outline-secondary {
            margin-left: 0;
            margin-top: 0.5rem;
            width: 100%;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit search on Enter key
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    this.closest('form').submit();
                }
            });
        }
    });
</script>
@endsection