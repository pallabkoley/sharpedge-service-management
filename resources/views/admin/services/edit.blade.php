@extends('layouts.admin')

@section('title', 'Edit Service')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Service: {{ $service->name }}</h1>
        <div>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.services.show', $service) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Service Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.services.update', $service) }}" method="POST" id="serviceForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Service Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $service->name) }}" 
                                   placeholder="e.g., Trade License, GST Registration" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" 
                              placeholder="Describe this service...">{{ old('description', $service->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="government_fee" class="form-label">Government Fee (₹) *</label>
                            <input type="number" step="0.01" class="form-control @error('government_fee') is-invalid @enderror" 
                                   id="government_fee" name="government_fee" value="{{ old('government_fee', $service->government_fee) }}" 
                                   placeholder="0.00" required>
                            @error('government_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="professional_fee" class="form-label">Our Fee (₹) *</label>
                            <input type="number" step="0.01" class="form-control @error('professional_fee') is-invalid @enderror" 
                                   id="professional_fee" name="professional_fee" value="{{ old('professional_fee', $service->professional_fee) }}" 
                                   placeholder="0.00" required>
                            @error('professional_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="processing_days" class="form-label">Processing Days *</label>
                            <input type="number" class="form-control @error('processing_days') is-invalid @enderror" 
                                   id="processing_days" name="processing_days" value="{{ old('processing_days', $service->processing_days) }}" 
                                   min="1" required>
                            @error('processing_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="internal_notes" class="form-label">Internal Notes</label>
                    <textarea class="form-control @error('internal_notes') is-invalid @enderror" 
                              id="internal_notes" name="internal_notes" rows="3" 
                              placeholder="Internal notes for staff...">{{ old('internal_notes', $service->internal_notes) }}</textarea>
                    @error('internal_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-control @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Requirements Section -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Document Requirements *</h6>
                        <small>List all documents needed for this service</small>
                    </div>
                    <div class="card-body">
                        <div id="requirements-container">
                            <!-- Requirements will be added here by JavaScript -->
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-success" id="add-requirement">
                            <i class="fas fa-plus"></i> Add Document Requirement
                        </button>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-0">
                            <small>
                                <i class="fas fa-calendar"></i> Created: {{ $service->created_at->format('d M Y, h:i A') }}
                            </small>
                            <br>
                            <small>
                                <i class="fas fa-history"></i> Updated: {{ $service->updated_at->format('d M Y, h:i A') }}
                            </small>
                        </p>
                    </div>
                    <div>
                        <button type="reset" class="btn btn-secondary me-2">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Service
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Requirement Template (Hidden) -->
<div id="requirement-template" class="d-none">
    <div class="requirement-item card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">Document Name *</label>
                        <input type="text" class="form-control requirement-document_name" 
                               name="requirements[INDEX][document_name]" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select class="form-control requirement-document_type" 
                                name="requirements[INDEX][document_type]" required>
                            <option value="mandatory">Mandatory</option>
                            <option value="optional">Optional</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Max Size (KB)</label>
                        <input type="number" class="form-control requirement-max_size" 
                               name="requirements[INDEX][max_size]" value="2048">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Actions</label>
                        <button type="button" class="btn btn-sm btn-danger w-100 remove-requirement">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control requirement-description" 
                                  name="requirements[INDEX][description]" rows="2"></textarea>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">Allowed File Types</label>
                        <input type="text" class="form-control requirement-file_types" 
                               name="requirements[INDEX][file_types]" value="pdf,jpg,png"
                               placeholder="pdf,jpg,png">
                    </div>
                </div>
            </div>
            <input type="hidden" class="requirement-id" name="requirements[INDEX][id]">
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('requirements-container');
        const template = document.getElementById('requirement-template').innerHTML;
        let requirementCount = 0;
        
        // Add existing requirements
        @foreach($service->requirements as $requirement)
            requirementCount++;
            addRequirement(
                '{{ $requirement->id }}',
                '{{ addslashes($requirement->document_name) }}',
                '{{ $requirement->document_type }}',
                '{{ addslashes($requirement->description) }}',
                '{{ $requirement->file_types }}',
                '{{ $requirement->max_size }}'
            );
        @endforeach
        
        // If no requirements, add one empty
        if (requirementCount === 0) {
            addRequirement();
        }
        
        // Add requirement button
        document.getElementById('add-requirement').addEventListener('click', function() {
            addRequirement();
        });
        
        function addRequirement(id = '', name = '', type = 'mandatory', description = '', fileTypes = 'pdf,jpg,png', maxSize = '2048') {
            requirementCount++;
            let html = template.replace(/INDEX/g, requirementCount);
            
            const div = document.createElement('div');
            div.innerHTML = html;
            container.appendChild(div);
            
            // Fill data if provided
            if (id) {
                div.querySelector('.requirement-id').value = id;
            }
            if (name) {
                div.querySelector('.requirement-document_name').value = name;
            }
            if (type) {
                div.querySelector('.requirement-document_type').value = type;
            }
            if (description) {
                div.querySelector('.requirement-description').value = description;
            }
            if (fileTypes) {
                div.querySelector('.requirement-file_types').value = fileTypes;
            }
            if (maxSize) {
                div.querySelector('.requirement-max_size').value = maxSize;
            }
            
            // Add event listener to remove button
            const removeBtn = div.querySelector('.remove-requirement');
            removeBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.requirement-item').length > 1) {
                    div.remove();
                } else {
                    alert('At least one requirement is required.');
                }
            });
        }
        
        // Form validation
        document.getElementById('serviceForm').addEventListener('submit', function(e) {
            const requirementItems = document.querySelectorAll('.requirement-item');
            if (requirementItems.length === 0) {
                e.preventDefault();
                alert('Please add at least one document requirement.');
                return false;
            }
            
            // Validate each requirement
            let isValid = true;
            requirementItems.forEach((item, index) => {
                const nameInput = item.querySelector('.requirement-document_name');
                if (!nameInput.value.trim()) {
                    isValid = false;
                    nameInput.classList.add('is-invalid');
                } else {
                    nameInput.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill all required fields in document requirements.');
                return false;
            }
        });
    });
</script>
@endsection