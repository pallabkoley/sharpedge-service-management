@extends('layouts.admin')

@section('title', 'Service Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Service: {{ $service->name }}
            <span class="badge badge-{{ $service->status == 'active' ? 'success' : 'secondary' }}">
                {{ ucfirst($service->status) }}
            </span>
        </h1>
        <div>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <!-- Details Row -->
    <div class="row">
        <!-- Service Information -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Service Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Service ID:</th>
                                    <td>{{ $service->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $service->name }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $service->category->name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Slug:</th>
                                    <td><code>{{ $service->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($service->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                        @else
                                        <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Government Fee:</th>
                                    <td>₹{{ number_format($service->government_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Professional Fee:</th>
                                    <td>₹{{ number_format($service->professional_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Fee:</th>
                                    <td class="font-weight-bold">₹{{ number_format($service->government_fee + $service->professional_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Processing Time:</th>
                                    <td>{{ $service->processing_days }} days</td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $service->created_at->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($service->description)
                    <div class="mt-4">
                        <h6 class="font-weight-bold">Description</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $service->description }}
                        </div>
                    </div>
                    @endif
                    
                    @if($service->internal_notes)
                    <div class="mt-4">
                        <h6 class="font-weight-bold">Internal Notes</h6>
                        <div class="border rounded p-3 bg-warning bg-opacity-10">
                            {{ $service->internal_notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Document Requirements -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Document Requirements ({{ $service->requirements->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($service->requirements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Document Name</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>File Types</th>
                                    <th>Max Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service->requirements->sortBy('sort_order') as $requirement)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $requirement->document_name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $requirement->document_type == 'mandatory' ? 'danger' : 'warning' }}">
                                            {{ ucfirst($requirement->document_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $requirement->description ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $requirement->file_types }}</span>
                                    </td>
                                    <td>{{ $requirement->max_size }} KB</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center text-muted">No document requirements defined.</p>
                    @endif
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <h6 class="text-primary">Total Requests</h6>
                            <h3>{{ $service->serviceRequests->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <h6 class="text-success">Active Requests</h6>
                            <h3>{{ $service->serviceRequests->whereNotIn('status', ['completed', 'cancelled'])->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <h6 class="text-info">Completed</h6>
                            <h3>{{ $service->serviceRequests->where('status', 'completed')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
            <!-- Workflow Steps -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Workflow Steps</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @php
                            $steps = is_array($service->workflow_steps) ? $service->workflow_steps : json_decode($service->workflow_steps, true);
                            $steps = $steps ?: ['Document Collection', 'Form Filling', 'Submission', 'Verification', 'Approval'];
                        @endphp
                        
                        @foreach($steps as $index => $step)
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker">{{ $index + 1 }}</div>
                            <div class="timeline-content">
                                <h6 class="font-weight-bold">{{ $step }}</h6>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.service-requests.create') }}?service_id={{ $service->id }}" 
                           class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Request
                        </a>
                        <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Service
                        </a>
                        @if($service->serviceRequests->count() == 0)
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                              onsubmit="return confirm('Delete this service?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Delete Service
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Creator Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Created By</h6>
                </div>
                <div class="card-body">
                    @if($service->creator)
                    <div class="text-center">
                        <div class="mb-2">
                            <i class="fas fa-user-circle fa-3x text-primary"></i>
                        </div>
                        <h5>{{ $service->creator->name }}</h5>
                        <p class="text-muted">{{ $service->creator->email }}</p>
                        <p class="mb-0">
                            <i class="fas fa-calendar"></i> {{ $service->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    @else
                    <p class="text-muted text-center">Creator information not available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -40px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #4e73df;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.timeline-content {
    margin-left: 10px;
}
</style>
@endsection