@extends('layouts.Backoffice')

@section('title', 'Notices - Backoffice Panel')

@section('styles')
<style>
    /* Notices Container */
    .notices-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    /* Notice Card */
    .notice-card {
        background: var(--bg-white);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--primary-color);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .notice-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }
    
    /* Notice Header */
    .notice-header {
        margin-bottom: 1.5rem;
    }
    
    .notice-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
    }
    
    .notice-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .meta-item i {
        font-size: 0.875rem;
    }
    
    .meta-divider {
        color: var(--border-color);
    }
    
    /* Priority Colors */
    .priority-high {
        color: #e53e3e;
        font-weight: 500;
    }
    
    .priority-medium {
        color: #d69e2e;
        font-weight: 500;
    }
    
    .priority-low {
        color: #38a169;
        font-weight: 500;
    }
    
    /* Notice Body */
    .notice-body {
        margin-bottom: 1.5rem;
    }
    
    .notice-description {
        font-size: 1rem;
        line-height: 1.6;
        color: var(--text-primary);
        margin-bottom: 0;
        white-space: pre-line;
    }
    
    /* Notice Footer */
    .notice-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
        font-size: 0.875rem;
    }
    
    .footer-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .footer-label {
        color: var(--text-secondary);
        font-weight: 600;
    }
    
    .footer-value {
        color: var(--text-primary);
        font-weight: 400;
    }
    
    /* Status Colors */
    .status-active {
        color: #38a169;
        font-weight: 500;
    }
    
    .status-expired {
        color: #e53e3e;
        font-weight: 500;
    }
    
    .status-draft {
        color: #d69e2e;
        font-weight: 500;
    }
    
    /* Notice Divider */
    .notice-divider {
        border: none;
        border-top: 1px solid var(--border-color);
        margin: 2rem 0;
        opacity: 0.5;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--bg-white);
        border-radius: 10px;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #a0aec0;
        margin-bottom: 1.5rem;
    }
    
    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
    }
    
    .empty-state-description {
        font-size: 1rem;
        color: var(--text-secondary);
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.5;
    }
    
    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }
    
    .pagination-info {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    
    /* Custom Pagination Styling */
    .pagination {
        display: flex;
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }
    
    .page-item:not(:first-child) .page-link {
        margin-left: 0.25rem;
    }
    
    .page-item:not(:last-child) .page-link {
        margin-right: 0.25rem;
    }
    
    .page-link {
        display: block;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
        background-color: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .page-link:hover {
        color: var(--primary-color);
        background-color: var(--bg-light);
        border-color: #cbd5e0;
    }
    
    .page-item.active .page-link {
        color: white;
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .page-item.disabled .page-link {
        color: #a0aec0;
        pointer-events: none;
        background-color: var(--bg-white);
        border-color: var(--border-color);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .notice-card {
            padding: 1.5rem;
        }
        
        .notice-title {
            font-size: 1.25rem;
        }
        
        .notice-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .meta-divider {
            display: none;
        }
        
        .notice-footer {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .pagination-container {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
    }
    
    @media (max-width: 576px) {
        .notice-card {
            padding: 1rem;
            border-radius: 8px;
        }
        
        .empty-state {
            padding: 3rem 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="content-container">
    <!-- Page Header -->
    <div class="page-header mb-5">
        <h1 class="page-title">Notices</h1>
        <p class="page-subtitle">View and manage all notices for the backoffice team</p>
    </div>

    <!-- Notices List -->
    <div class="notices-container">
        @forelse($notices as $notice)
            <div class="notice-card">
                <!-- Notice Header -->
                <div class="notice-header">
                    <h2 class="notice-title">{{ $notice->title }}</h2>
                    <div class="notice-meta">
                        <span class="meta-item">
                            <i class="bi bi-calendar"></i>
                            Posted on: {{ $notice->start_date ? $notice->start_date->format('Y-m-d') : $notice->created_at->format('Y-m-d') }}
                        </span>
                        
                        @if($notice->target_role)
                            <span class="meta-divider">•</span>
                            <span class="meta-item">
                                <i class="bi bi-people"></i>
                                {{ $notice->target_role == 'all' ? 'All Departments' : ucfirst($notice->target_role) }}
                            </span>
                        @endif
                        
                        @if($notice->priority)
                            <span class="meta-divider">•</span>
                            <span class="meta-item priority-{{ $notice->priority }}">
                                <i class="bi bi-flag"></i>
                                {{ ucfirst($notice->priority) }} Priority
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Notice Body -->
                <div class="notice-body">
                    <p class="notice-description">
                        {{ $notice->message }}
                    </p>
                </div>
                
                <!-- Notice Footer -->
                <div class="notice-footer">
                    @if($notice->end_date)
                        <div class="footer-item">
                            <span class="footer-label">Valid until:</span>
                            <span class="footer-value">{{ $notice->end_date->format('Y-m-d') }}</span>
                        </div>
                    @else
                        <div class="footer-item">
                            <span class="footer-label">Valid until:</span>
                            <span class="footer-value">No end date</span>
                        </div>
                    @endif
                    
                    @if($notice->status)
                        <div class="footer-item">
                            <span class="footer-label">Status:</span>
                            <span class="footer-value status-{{ $notice->status }}">
                                {{ ucfirst($notice->status) }}
                            </span>
                        </div>
                    @endif
                    
                    @if($notice->created_by)
                        <div class="footer-item">
                            <span class="footer-label">Posted by:</span>
                            <span class="footer-value">{{ $notice->creator->name ?? 'Admin' }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            @if(!$loop->last)
                <hr class="notice-divider">
            @endif
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-bell"></i>
                </div>
                <h3 class="empty-state-title">No Notices Available</h3>
                <p class="empty-state-description">
                    There are no active notices for the backoffice team at the moment.
                </p>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($notices->count() > 0 && $notices->total() > $notices->perPage())
        <div class="pagination-container mt-5">
            <div class="pagination-info">
                Showing {{ $notices->firstItem() }} to {{ $notices->lastItem() }} of {{ $notices->total() }} notices
            </div>
            <div class="pagination-links">
                {{ $notices->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click handlers for notice cards
        const noticeCards = document.querySelectorAll('.notice-card');
        
        noticeCards.forEach(card => {
            card.style.cursor = 'pointer';
            
            card.addEventListener('click', function(e) {
                // Don't trigger if clicking on links or buttons
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                // In a real implementation, this would navigate to the notice detail page
                console.log('Notice clicked, would navigate to detail page');
            });
        });
        
        // Add keyboard navigation
        noticeCards.forEach(card => {
            card.setAttribute('tabindex', '0');
            
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    // Navigate to notice detail
                }
            });
        });
    });
</script>
@endsection