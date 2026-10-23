@extends('layouts.admin')

@section('title', 'Edit Service Category')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Category: {{ $category->name }}</h1>
        <div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Categories
            </a>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Category Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $category->name) }}" 
                                   placeholder="e.g., Trade License, GST Registration" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug *</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug', $category->slug) }}" 
                                   placeholder="e.g., trade-license, gst-registration" required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">URL-friendly version of the name</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" 
                              placeholder="Brief description of this category...">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas {{ $category->icon ? 'fa-' . $category->icon : 'fa-icons' }}"></i>
                                </span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon', $category->icon) }}" 
                                       placeholder="e.g., file-contract, passport, building">
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Font Awesome icon name</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <div>
                        <p class="text-muted mb-0">
                            <small>
                                <i class="fas fa-calendar"></i> Created: {{ $category->created_at->format('d M Y, h:i A') }}
                            </small>
                            <br>
                            <small>
                                <i class="fas fa-history"></i> Updated: {{ $category->updated_at->format('d M Y, h:i A') }}
                            </small>
                        </p>
                    </div>
                    <div>
                        <button type="reset" class="btn btn-secondary me-2">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Category Statistics</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Services
                            <span class="badge badge-primary badge-pill">{{ $category->services_count ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Active Services
                            <span class="badge badge-success badge-pill">{{ $activeServicesCount ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Inactive Services
                            <span class="badge badge-secondary badge-pill">{{ $inactiveServicesCount ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Icon Preview</h6>
                </div>
                <div class="card-body text-center">
                    <div id="iconPreview">
                        @if($category->icon)
                            <i class="fas fa-{{ $category->icon }} fa-3x text-primary"></i>
                            <p class="mt-2">fa-{{ $category->icon }}</p>
                        @else
                            <i class="fas fa-icons fa-3x text-muted"></i>
                            <p class="mt-2 text-muted">No icon set</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const slugField = document.getElementById('slug');
        if (!slugField.value || slugField.value === '{{ $category->slug }}') {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();
            slugField.value = slug;
        }
    });
    
    // Icon preview
    document.getElementById('icon').addEventListener('input', function() {
        const preview = document.getElementById('iconPreview');
        const iconName = this.value.trim();
        
        if (iconName) {
            preview.innerHTML = `
                <i class="fas fa-${iconName} fa-3x text-primary"></i>
                <p class="mt-2">fa-${iconName}</p>
            `;
        } else {
            preview.innerHTML = `
                <i class="fas fa-icons fa-3x text-muted"></i>
                <p class="mt-2 text-muted">No icon set</p>
            `;
        }
    });
</script>
@endsection