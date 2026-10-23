@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Service Categories</h2>
                <p class="text-muted mb-0">Manage and organize your services into categories</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="bi bi-plus-circle me-1"></i> Add New Category
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="card-icon bg-primary me-3"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                    <div>
                        <div class="card-value">{{ $totalCategories }}</div>
                        <div class="text-muted">Total Categories</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="card-icon bg-success me-3"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="card-value">{{ $activeCategories }}</div>
                        <div class="text-muted">Active Categories</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="card-icon bg-danger me-3"><i class="bi bi-x-circle-fill"></i></div>
                    <div>
                        <div class="card-value">{{ $inactiveCategories }}</div>
                        <div class="text-muted">Inactive Categories</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="card-icon bg-purple me-3"><i class="bi bi-gear-fill"></i></div>
                    <div>
                        <div class="card-value">{{ $totalServices }}</div>
                        <div class="text-muted">Total Services</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title mb-0">Service Categories</h3>
            <div class="d-flex gap-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <input type="text" class="form-control" placeholder="Search categories..." id="searchCategories">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn"><i class="bi bi-search"></i></button>
                </div>
                <select class="form-select form-select-sm" style="width: 120px;" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        @if($categories->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover datatable" id="categoriesTable">
                <thead>
                    <tr>
                        <th width="100">Code</th>
                        <th>Name</th>
                        <th width="120">Services</th>
                        <th width="100">Status</th>
                        <th width="80">Order</th>
                        <th width="180">Created</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr id="category-{{ $category->id }}" data-status="{{ $category->status }}">
                        <td><span class="badge bg-light text-dark border">{{ $category->category_code }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ $category->name }}</div>
                            <small class="text-muted">Slug: {{ $category->slug }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary rounded-pill me-2">{{ $category->services_count }}</span>
                                <button type="button" class="btn btn-link text-decoration-none p-0 btn-sm view-services-btn"
                                        data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                    View Services
                                </button>
                            </div>
                        </td>
                        <td>
                            @if($category->status == 'active')
                                <span class="badge bg-success bg-opacity-25 text-success border-0"><i class="bi bi-check-circle me-1"></i> Active</span>
                            @else
                                <span class="badge bg-danger bg-opacity-25 text-danger border-0"><i class="bi bi-x-circle me-1"></i> Inactive</span>
                            @endif
                        </td>
                        <td><span class="text-muted">{{ $category->display_order }}</span></td>
                        <td>
                            <div class="text-muted small">{{ $category->created_at->format('M d, Y') }}</div>
                            <div class="text-muted smaller">{{ $category->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary edit-category"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-status="{{ $category->status }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger delete-category"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} entries
            </div>
            <nav>{{ $categories->links('pagination::bootstrap-5') }}</nav>
        </div>
        @else
        <div class="text-center py-5">
            <div class="py-5">
                <i class="bi bi-grid-3x3-gap" style="font-size: 64px; color: #cbd5e1;"></i>
                <h4 class="mt-4 text-muted">No Categories Found</h4>
                <p class="text-muted">Start by creating your first service category</p>
                <button type="button" class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="bi bi-plus-circle me-1"></i> Create Category
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createCategoryForm" action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required
                               placeholder="e.g., Tax Services, Legal Services">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="createCategoryBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span> Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_category_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status *</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="updateCategoryBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteCategoryName"></strong>?</p>
                <p class="text-danger small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Services Popup Modal -->
<div class="modal fade" id="servicesModal" tabindex="-1" aria-labelledby="servicesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="servicesModalLabel">Services</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="servicesModalBody">
                <!-- Content loaded via AJAX -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stats-card {
        background-color: #fff;
        border-radius: 8px;
        padding: 15px;
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }
    .card-icon.bg-purple {
        background-color: #8b5cf6;
    }
    .card-value {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1;
    }
    .section-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e2e8f0;
    }
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
    }
    .table td, .table th {
        padding: 12px 15px;
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.05);
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .badge.bg-purple {
        background-color: #8b5cf6 !important;
    }
    .smaller {
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchCategories');
        const searchBtn = document.getElementById('searchBtn');
        
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            document.querySelectorAll('#categoriesTable tbody tr').forEach(row => {
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const code = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                row.style.display = (name.includes(searchTerm) || code.includes(searchTerm)) ? '' : 'none';
            });
        }
        
        searchInput.addEventListener('input', filterTable);
        searchBtn.addEventListener('click', filterTable);
        
        // Status filter
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value;
            document.querySelectorAll('#categoriesTable tbody tr').forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                row.style.display = (status === 'all' || rowStatus === status) ? '' : 'none';
            });
        });

        // Services Popup
        const servicesModal = new bootstrap.Modal(document.getElementById('servicesModal'));
        document.querySelectorAll('.view-services-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const categoryId = this.dataset.id;
                const categoryName = this.dataset.name;
                document.getElementById('servicesModalLabel').textContent = `Services - ${categoryName}`;
                const modalBody = document.getElementById('servicesModalBody');
                modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                servicesModal.show();

                // Fetch services via AJAX
                fetch(`/admin/service-categories/${categoryId}/services-popup`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalBody.innerHTML = data.html;
                    } else {
                        modalBody.innerHTML = '<div class="alert alert-danger">Failed to load services.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">An error occurred while loading services.</div>';
                });
            });
        });
        
        // Edit modal
        document.querySelectorAll('.edit-category').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_category_id').value = this.dataset.id;
                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_status').value = this.dataset.status;
                
                // Use Laravel route helper to generate the correct URL
                document.getElementById('editCategoryForm').action = "{{ route('admin.categories.update', ':id') }}".replace(':id', this.dataset.id);
                new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
            });
        });
        
        // Delete functionality
        let categoryToDelete = null;
        
        // Open delete modal
        document.querySelectorAll('.delete-category').forEach(btn => {
            btn.addEventListener('click', function() {
                categoryToDelete = this.dataset.id;
                document.getElementById('deleteCategoryName').innerText = this.dataset.name;
                new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
            });
        });
        
        // Confirm delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!categoryToDelete) return;

            const btn = this;
            const spinner = btn.querySelector('.spinner-border');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            spinner.classList.remove('d-none');
            btn.innerHTML = btn.innerHTML.replace('Delete', 'Deleting...');
            
            // Generate the correct delete URL using Laravel route helper
            const deleteUrl = "{{ route('admin.categories.destroy', ':id') }}".replace(':id', categoryToDelete);
            
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove the row from table
                    const row = document.getElementById(`category-${categoryToDelete}`);
                    if (row) {
                        row.remove();
                    }
                    
                    // Show success message
                    showToast('Category deleted successfully!', 'success');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
                    modal.hide();
                    
                    // Reload page to update counters
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Delete failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Failed to delete category. Please try again.', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                spinner.classList.add('d-none');
                categoryToDelete = null;
            });
        });
        
        // Create category
        document.getElementById('createCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('createCategoryBtn');
            const spinner = submitBtn.querySelector('.spinner-border');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            submitBtn.innerHTML = submitBtn.innerHTML.replace('Create Category', 'Creating...');
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Category created successfully!', 'success');
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Creation failed');
                }
            })
            .catch(error => {
                console.error(error);
                showToast(error.message || 'Failed to create category. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                spinner.classList.add('d-none');
            });
        });
        
        // Update category
        document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('updateCategoryBtn');
            const spinner = submitBtn.querySelector('.spinner-border');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            submitBtn.innerHTML = submitBtn.innerHTML.replace('Update Category', 'Updating...');
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Category updated successfully!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Update failed');
                }
            })
            .catch(error => {
                console.error(error);
                showToast(error.message || 'Failed to update category. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                spinner.classList.add('d-none');
            });
        });
        
        // Helper function for toast notifications
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast, {
                autohide: true,
                delay: 3000
            });
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', function () {
                document.body.removeChild(toast);
            });
        }
    });
</script>
@endpush

@endsection