@extends('layouts.marketing')

@section('title', 'Admin Notices - Marketing Panel')

@section('page-title', 'Admin Notices')
@section('page-subtitle', 'Important updates and announcements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Full Width Column -->
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header mb-5">
                <h1 class="page-title">Admin Notices</h1>
                <p class="page-subtitle">Important updates and announcements for the marketing team</p>
            </div>

            <!-- Notices List -->
            <div class="notices-container">
                @forelse($notices as $notice)
                    <div class="notice-card {{ $notice->priority == 'high' ? 'notice-high-priority' : '' }}">
                        <!-- Vertical accent line -->
                        <div class="notice-accent {{ $notice->priority == 'high' ? 'accent-high' : ($notice->priority == 'medium' ? 'accent-medium' : 'accent-normal') }}"></div>
                        
                        <div class="notice-content">
                            <!-- Notice Header -->
                            <div class="notice-header">
                                <h2 class="notice-title">{{ $notice->title }}</h2>
                                <div class="notice-meta">
                                    <span class="meta-item">
                                        <i class="far fa-calendar"></i>
                                        Posted on: {{ $notice->start_date ? $notice->start_date->format('Y-m-d') : $notice->created_at->format('Y-m-d') }}
                                    </span>
                                    
                                    @if($notice->target_role)
                                        <span class="meta-divider">•</span>
                                        <span class="meta-item">
                                            <i class="fas fa-users"></i>
                                            {{ $notice->target_role == 'all' ? 'All Departments' : ucfirst($notice->target_role) }}
                                        </span>
                                    @endif
                                    
                                    @if($notice->priority == 'high')
                                        <span class="meta-divider">•</span>
                                        <span class="meta-item priority-high">
                                            <i class="fas fa-exclamation-circle"></i>
                                            High Priority
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
                            @if($notice->end_date || $notice->status || $notice->creator)
                                <div class="notice-footer">
                                    @if($notice->end_date)
                                        <div class="footer-item">
                                            <span class="footer-label">Valid until:</span>
                                            <span class="footer-value">{{ $notice->end_date->format('Y-m-d') }}</span>
                                        </div>
                                    @else
                                        <div class="footer-item">
                                            <span class="footer-label">Valid until:</span>
                                            <span class="footer-value text-muted">No end date</span>
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
                                    
                                    @if($notice->creator)
                                        <div class="footer-item">
                                            <span class="footer-label">Posted by:</span>
                                            <span class="footer-value">{{ $notice->creator->name ?? 'Admin' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="far fa-bell"></i>
                        </div>
                        <h3 class="empty-state-title">No Notices Available</h3>
                        <p class="empty-state-description">
                            There are no active notices at the moment. Check back later for updates.
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
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Page Header */
    .page-header {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
    }
    
    .page-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
    }
    
    .page-subtitle {
        font-size: 1rem;
        color: var(--gray-600);
        font-weight: 400;
        margin-bottom: 0;
    }
    
    /* Notices Container */
    .notices-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    /* Notice Card */
    .notice-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.2s ease;
    }
    
    .notice-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }
    
    .notice-card.notice-high-priority {
        border-left: 3px solid var(--danger-color);
        background: linear-gradient(90deg, rgba(239, 68, 68, 0.03) 0%, white 100%);
    }
    
    /* Vertical Accent Line */
    .notice-accent {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 4px 0 0 4px;
    }
    
    .accent-normal {
        background: var(--primary-color);
    }
    
    .accent-medium {
        background: var(--warning-color);
    }
    
    .accent-high {
        background: var(--danger-color);
    }
    
    /* Notice Content */
    .notice-content {
        padding: 1.75rem;
        margin-left: 4px; /* Make room for accent line */
    }
    
    /* Notice Header */
    .notice-header {
        margin-bottom: 1.25rem;
    }
    
    .notice-title {
        font-size: 1.375rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    
    .notice-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        font-size: 0.875rem;
        color: var(--gray-500);
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .meta-item i {
        font-size: 0.75rem;
    }
    
    .meta-divider {
        color: var(--gray-300);
    }
    
    .priority-high {
        color: var(--danger-color);
        font-weight: 500;
    }
    
    /* Notice Body */
    .notice-body {
        margin-bottom: 1.5rem;
    }
    
    .notice-description {
        font-size: 1rem;
        line-height: 1.6;
        color: var(--gray-700);
        margin-bottom: 0;
        white-space: pre-line;
    }
    
    /* Notice Footer */
    .notice-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--gray-100);
        font-size: 0.875rem;
    }
    
    .footer-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .footer-label {
        color: var(--gray-500);
        font-weight: 500;
    }
    
    .footer-value {
        color: var(--gray-700);
        font-weight: 400;
    }
    
    .status-active {
        color: var(--secondary-color);
        font-weight: 500;
    }
    
    .status-inactive {
        color: var(--gray-500);
        font-weight: 500;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 8px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: var(--gray-300);
        margin-bottom: 1.5rem;
    }
    
    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.75rem;
    }
    
    .empty-state-description {
        font-size: 1rem;
        color: var(--gray-500);
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
        border-top: 1px solid var(--gray-200);
    }
    
    .pagination-info {
        font-size: 0.875rem;
        color: var(--gray-600);
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
        color: var(--gray-700);
        background-color: white;
        border: 1px solid var(--gray-300);
        border-radius: 0.375rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .page-link:hover {
        color: var(--primary-color);
        background-color: var(--gray-100);
        border-color: var(--gray-400);
    }
    
    .page-item.active .page-link {
        color: white;
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .page-item.disabled .page-link {
        color: var(--gray-500);
        pointer-events: none;
        background-color: white;
        border-color: var(--gray-300);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.875rem;
        }
        
        .notice-content {
            padding: 1.25rem;
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
            border-radius: 6px;
        }
        
        .notice-content {
            padding: 1rem;
        }
        
        .empty-state {
            padding: 3rem 1.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add subtle interaction for notice cards
        const noticeCards = document.querySelectorAll('.notice-card');
        
        noticeCards.forEach(card => {
            // Add click effect for better UX
            card.addEventListener('click', function() {
                // Optional: Could expand/collapse details here
                this.classList.toggle('notice-expanded');
            });
            
            // Add keyboard navigation
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.classList.toggle('notice-expanded');
                }
            });
        });
        
        // Add tabindex for accessibility
        noticeCards.forEach(card => {
            card.setAttribute('tabindex', '0');
        });
    });
</script>
@endsection