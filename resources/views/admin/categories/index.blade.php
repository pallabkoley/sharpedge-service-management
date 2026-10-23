@extends('layouts.admin')

@section('title', 'Service Categories')

@push('styles')
<style>
    /* Custom Styles for Service Categories Page */
    .page-header {
        padding-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 30px;
    }
    
    .page-header h2 {
        font-weight: 700;
        color: #1e293b;
    }
    
    /* Stats Cards */
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #1d4ed8;
        margin-bottom: 20px;
    }
    
    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 5px;
    }
    
    .stats-label {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    /* View Toggle */
    .view-toggle {
        display: flex;
        gap: 10px;
        background-color: #f1f5f9;
        padding: 5px;
        border-radius: 8px;
    }
    
    .view-toggle-btn {
        border: none;
        background: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #64748b;
        transition: all 0.2s;
    }
    
    .view-toggle-btn.active {
        background-color: white;
        color: #1d4ed8;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Card Styling */
    .category-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
        border-left: 4px solid #1d4ed8 !important;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .category-card .card-body {
        padding: 1.5rem;
    }
    
    .category-card .card-title {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    
    .category-card .services-count {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    .category-card .services-count strong {
        color: #1d4ed8;
        font-size: 1.1rem;
    }
    
    /* Table Styling */
    .category-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    
    .category-table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        padding: 15px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .category-table td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .category-table tr:hover {
        background-color: #f8fafc;
    }
    
    .category-code {
        font-weight: 600;
        color: #1d4ed8;
    }
    
    .category-slug {
        font-size: 12px;
        color: #64748b;
    }
    
    .services-badge {
        background-color: #eff6ff;
        color: #1d4ed8;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .services-badge:hover {
        background-color: #dbeafe;
    }
    
    /* Status Badges */
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .status-active {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-inactive {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    /* Card Actions */
    .card-actions {
        border-top: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        background-color: #f8fafc;
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin-right: 0.5rem;
        transition: all 0.2s;
    }
    
    .action-btn:hover {
        transform: scale(1.1);
    }
    
    .action-btn.edit {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    
    .action-btn.delete {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    
    .action-btn.edit:hover {
        background-color: #dbeafe;
    }
    
    .action-btn.delete:hover {
        background-color: #fee2e2;
    }
    
    .view-services-btn {
        background-color: #1d4ed8;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .view-services-btn:hover {
        background-color: #1e40af;
        transform: translateY(-2px);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1.5rem;
    }
    
    .empty-state-title {
        color: #64748b;
        margin-bottom: 0.5rem;
    }
    
    .empty-state-text {
        color: #94a3b8;
        max-width: 400px;
        margin: 0 auto;
    }
    
    /* Add Category Button */
    .add-category-btn {
        background-color: #1d4ed8;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .add-category-btn:hover {
        background-color: #1e40af;
        transform: translateY(-2px);
    }
    
    /* Modal Styling */
    .category-modal .modal-header {
        background-color: #1d4ed8;
        color: white;
    }
    
    .category-modal .modal-title {
        font-weight: 600;
    }
    
    .form-label {
        font-weight: 500;
        color: #475569;
    }
    
    .form-label.required:after {
        content: " *";
        color: #dc2626;
    }
    
    /* Pagination */
    .pagination-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .pagination-info {
        color: #64748b;
        font-size: 14px;
    }
    
    .pagination .page-item .page-link {
        color: #475569;
        border: 1px solid #e2e8f0;
        margin: 0 3px;
        border-radius: 6px;
        padding: 8px 14px;
        transition: all 0.2s;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
        color: white;
    }
    
    .pagination .page-item:not(.active) .page-link:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background-color: #f8fafc;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .category-card {
            margin-bottom: 1.5rem;
        }
        
        .card-actions {
            flex-direction: column;
            gap: 1rem;
        }
        
        .view-services-btn {
            width: 100%;
        }
        
        .pagination-container {
            flex-direction: column;
            text-align: center;
        }
        
        .pagination-info {
            order: 2;
        }
        
        .pagination {
            order: 1;
        }
    }
    
    /* Loading State */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Hidden elements */
    .d-none {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Service Categories</h2>
                <p class="text-muted mb-0">Manage all your service categories in one place</p>
            </div>
            <button class="btn btn-primary add-category-btn" id="addCategoryBtn">
                <i class="bi bi-plus-lg"></i>
                <span>Add Category</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-value" id="totalCategories">{{ $categories->total() }}</div>
                <div class="stats-label">Total Categories</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-value" id="activeCategories">{{ $activeCount ?? 0 }}</div>
                <div class="stats-label">Active Categories</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-value" id="inactiveCategories">{{ $inactiveCount ?? 0 }}</div>
                <div class="stats-label">Inactive Categories</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-value" id="totalServices">{{ $totalServices ?? 0 }}</div>
                <div class="stats-label">Total Services</div>
            </div>
        </div>
    </div>

    <!-- View Toggle and Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="view-toggle">
            <button class="view-toggle-btn active" data-view="grid" id="gridViewBtn">
                <i class="bi bi-grid-3x3-gap me-1"></i> Grid View
            </button>
            <button class="view-toggle-btn" data-view="table" id="tableViewBtn">
                <i class="bi bi-table me-1"></i> Table View
            </button>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="input-group" style="width: 250px;">
                <input type="text" class="form-control" placeholder="Search categories..." id="searchInput">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Service Categories Grid View -->
    <div class="row" id="gridView">
        @foreach($categories as $category)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4 category-card-container" data-id="{{ $category->id }}">
            <div class="card category-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <span class="status-badge {{ $category->status == 'active' ? 'status-active' : 'status-inactive' }}">
                            {{ ucfirst($category->status) }}
                        </span>
                    </div>
                    
                    <div class="services-count">
                        <i class="bi bi-list-check me-1"></i>
                        Services: <strong>{{ $category->services_count ?? 0 }}</strong>
                    </div>
                    
                    <div class="text-muted small mb-2">
                        <i class="bi bi-tag me-1"></i>
                        Code: <strong>{{ $category->code ?? 'CAT-' . str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                    
                    <div class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>
                        Created: {{ $category->created_at->format('M d, Y') }}
                    </div>
                </div>
                
                <div class="card-actions d-flex justify-content-between align-items-center">
                    <div class="action-buttons">
                        <button class="btn btn-sm action-btn edit edit-category-btn"
                                data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}"
                                data-status="{{ $category->status }}"
                                data-code="{{ $category->code ?? '' }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm action-btn delete delete-category-btn"
                                data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <button class="btn btn-sm view-services-btn view-category-services" data-id="{{ $category->id }}">
                        <i class="bi bi-eye me-1"></i> View Services
                    </button>
                </div>
            </div>
        </div>
        @endforeach
        
        @if($categories->isEmpty())
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-folder"></i>
                </div>
                <h4 class="empty-state-title">No Service Categories Yet</h4>
                <p class="empty-state-text">Start by adding your first service category. Click the "Add Category" button above to get started.</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Service Categories Table View -->
    <div class="category-table d-none" id="tableView">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Services</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr data-id="{{ $category->id }}">
                        <td>
                            <div class="category-code">{{ $category->code ?? 'CAT-' . str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $category->name }}</div>
                            <div class="category-slug">Slug: {{ $category->slug ?? strtolower(str_replace(' ', '-', $category->name)) }}</div>
                        </td>
                        <td>
                            <span class="services-badge view-category-services" data-id="{{ $category->id }}">
                                {{ $category->services_count ?? 0 }} Services
                            </span>
                        </td>
                        <td>
                            <span class="status-badge {{ $category->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                        </td>
                        <td>{{ $category->order ?? $loop->iteration }}</td>
                        <td>
                            <div>{{ $category->created_at->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $category->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm action-btn edit edit-category-btn"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-status="{{ $category->status }}"
                                        data-code="{{ $category->code ?? '' }}"
                                        data-order="{{ $category->order ?? '' }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm action-btn delete delete-category-btn"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($categories->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state-icon">
                                <i class="bi bi-folder"></i>
                            </div>
                            <h4 class="empty-state-title">No Service Categories Yet</h4>
                            <p class="empty-state-text">Start by adding your first service category.</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} entries
        </div>
        
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                {{-- Previous Page Link --}}
                @if($categories->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $categories->previousPageUrl() }}" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                    @if($page == $categories->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if($categories->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $categories->nextPageUrl() }}" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade category-modal" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" method="POST">
                @csrf
                <input type="hidden" id="categoryId" name="id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryCode" class="form-label">Category Code</label>
                        <input type="text" class="form-control" id="categoryCode" name="code"
                               placeholder="Enter category code (e.g., CAT-001)">
                        <div class="form-text">Leave blank to auto-generate</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categoryName" class="form-label required">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required
                               placeholder="Enter category name (e.g., Trade License Services)">
                        <div class="invalid-feedback" id="nameError">Please enter a category name.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="categoryOrder" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="categoryOrder" name="order" min="1"
                                   placeholder="Enter display order">
                            <div class="form-text">Lower numbers appear first</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="categoryStatus" class="form-label required">Status</label>
                            <select class="form-select" id="categoryStatus" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback" id="statusError">Please select a status.</div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitBtnText">Save Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteCategoryName" class="text-danger"></strong>?</p>
                <p class="text-muted small mb-0">This action cannot be undone. All services under this category will need to be reassigned.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <span id="deleteBtnText">Delete Category</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const categoryForm = document.getElementById('categoryForm');
        let categoryIdToDelete = null;
        let isSubmitting = false;
        
        // CSRF Token setup for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // View toggle functionality
        const gridViewBtn = document.getElementById('gridViewBtn');
        const tableViewBtn = document.getElementById('tableViewBtn');
        const gridView = document.getElementById('gridView');
        const tableView = document.getElementById('tableView');
        
        gridViewBtn.addEventListener('click', function() {
            this.classList.add('active');
            tableViewBtn.classList.remove('active');
            gridView.classList.remove('d-none');
            tableView.classList.add('d-none');
        });
        
        tableViewBtn.addEventListener('click', function() {
            this.classList.add('active');
            gridViewBtn.classList.remove('active');
            tableView.classList.remove('d-none');
            gridView.classList.add('d-none');
        });
        
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                // Search in grid view
                const gridCards = document.querySelectorAll('#gridView .category-card-container');
                gridCards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(searchTerm) ? '' : 'none';
                });
                
                // Search in table view
                const tableRows = document.querySelectorAll('#tableView tbody tr[data-id]');
                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
                
                // Show/hide empty states
                const visibleCards = Array.from(gridCards).filter(card => card.style.display !== 'none');
                const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
                
                // You can add logic here to show empty search results message
            });
        }
        
        // Toast Notification Function
        function showToast(title, message, type = 'success') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed`;
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '1060';
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}:</strong> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
            bsToast.show();
            
            // Remove from DOM after hide
            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }
        
        // Add Category Button
        document.getElementById('addCategoryBtn').addEventListener('click', function() {
            document.getElementById('modalTitle').textContent = 'Add Category';
            document.getElementById('submitBtnText').textContent = 'Save Category';
            document.getElementById('categoryId').value = '';
            categoryForm.reset();
            document.getElementById('categoryStatus').value = 'active';
            
            // Clear validation
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            
            categoryModal.show();
        });
        
        // Edit Category Button
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-category-btn')) {
                const button = e.target.closest('.edit-category-btn');
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const status = button.getAttribute('data-status');
                const code = button.getAttribute('data-code');
                const order = button.getAttribute('data-order');
                
                document.getElementById('modalTitle').textContent = 'Edit Category';
                document.getElementById('submitBtnText').textContent = 'Update Category';
                document.getElementById('categoryId').value = id;
                document.getElementById('categoryName').value = name;
                document.getElementById('categoryStatus').value = status;
                document.getElementById('categoryCode').value = code || '';
                document.getElementById('categoryOrder').value = order || '';
                
                // Clear validation
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                categoryModal.show();
            }
        });
        
        // Delete Category Button
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-category-btn')) {
                const button = e.target.closest('.delete-category-btn');
                categoryIdToDelete = button.getAttribute('data-id');
                const categoryName = button.getAttribute('data-name');
                
                document.getElementById('deleteCategoryName').textContent = categoryName;
                document.getElementById('deleteBtnText').textContent = 'Delete Category';
                
                deleteModal.show();
            }
        });
        
        // Confirm Delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!categoryIdToDelete || isSubmitting) return;
            
            isSubmitting = true;
            const deleteBtn = this;
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<div class="loading-spinner"></div>';
            deleteBtn.disabled = true;
            
            fetch(`/admin/service-categories/${categoryIdToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                isSubmitting = false;
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
                
                if (data.success) {
                    // Remove the card from DOM
                    const card = document.querySelector(`.category-card-container[data-id="${categoryIdToDelete}"]`);
                    if (card) card.remove();
                    
                    // Remove the row from table view
                    const row = document.querySelector(`#tableView tr[data-id="${categoryIdToDelete}"]`);
                    if (row) row.remove();
                    
                    // Update stats
                    updateStatsAfterDelete(data.stats);
                    
                    // Show success message
                    showToast('Success', data.message, 'success');
                    
                    // Close modal
                    deleteModal.hide();
                    
                    // Check if no categories left
                    const gridContainer = document.getElementById('gridView');
                    const tableContainer = document.getElementById('tableView');
                    
                    if (gridContainer.querySelectorAll('.category-card-container').length === 0) {
                        gridContainer.innerHTML = `
                            <div class="col-12">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-folder"></i>
                                    </div>
                                    <h4 class="empty-state-title">No Service Categories Yet</h4>
                                    <p class="empty-state-text">Start by adding your first service category. Click the "Add Category" button above to get started.</p>
                                </div>
                            </div>
                        `;
                    }
                    
                    if (tableContainer.querySelectorAll('tbody tr[data-id]').length === 0) {
                        tableContainer.querySelector('tbody').innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-folder"></i>
                                    </div>
                                    <h4 class="empty-state-title">No Service Categories Yet</h4>
                                    <p class="empty-state-text">Start by adding your first service category.</p>
                                </td>
                            </tr>
                        `;
                    }
                } else {
                    showToast('Error', data.message || 'Failed to delete category', 'error');
                }
            })
            .catch(error => {
                isSubmitting = false;
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
                console.error('Error:', error);
                showToast('Error', 'Failed to delete category. Please try again.', 'error');
            });
        });
        
        // Function to update stats after delete
        function updateStatsAfterDelete(stats) {
            if (stats) {
                if (stats.total !== undefined) {
                    document.getElementById('totalCategories').textContent = stats.total;
                }
                if (stats.active !== undefined) {
                    document.getElementById('activeCategories').textContent = stats.active;
                }
                if (stats.inactive !== undefined) {
                    document.getElementById('inactiveCategories').textContent = stats.inactive;
                }
            }
        }
        
        // Submit Category Form (Add/Edit)
        categoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (isSubmitting) return;
            
            const formData = new FormData(this);
            const id = document.getElementById('categoryId').value;
            const url = id ? `/admin/service-categories/${id}` : '/admin/service-categories';
            const method = id ? 'PUT' : 'POST';
            
            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            
            // Validate form
            let isValid = true;
            if (!document.getElementById('categoryName').value.trim()) {
                document.getElementById('categoryName').classList.add('is-invalid');
                isValid = false;
            }
            if (!document.getElementById('categoryStatus').value) {
                document.getElementById('categoryStatus').classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) return;
            
            isSubmitting = true;
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<div class="loading-spinner"></div>';
            submitBtn.disabled = true;
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                isSubmitting = false;
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    // Close modal
                    categoryModal.hide();
                    
                    // Show success message
                    showToast('Success', data.message, 'success');
                    
                    // Reload page to show updated data
                    setTimeout(() => location.reload(), 1000);
                } else {
                    // Show validation errors if any
                    if (data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const input = document.getElementById(`category${field.charAt(0).toUpperCase() + field.slice(1)}`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const errorEl = document.getElementById(`${field}Error`);
                                if (errorEl) errorEl.textContent = messages[0];
                            }
                        }
                    } else {
                        showToast('Error', data.message || 'An error occurred', 'error');
                    }
                }
            })
            .catch(error => {
                isSubmitting = false;
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                console.error('Error:', error);
                showToast('Error', 'An error occurred. Please try again.', 'error');
            });
        });
        
        // View Services Button
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-category-services')) {
                const button = e.target.closest('.view-category-services');
                const categoryId = button.getAttribute('data-id');
                
                // Redirect to services page filtered by this category
                window.location.href = `/admin/services?category=${categoryId}`;
            }
        });
        
        // Clear validation on modal hide
        document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        });
        
        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (categoryModal._isShown) categoryModal.hide();
                if (deleteModal._isShown) deleteModal.hide();
            }
        });
    });
</script>
@endpush