@extends('layouts.admin')

@section('title', 'Service Request Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Service Request: {{ $serviceRequest->request_number }}
            <span class="badge badge-{{ 
                $serviceRequest->status == 'completed' ? 'success' : 
                ($serviceRequest->status == 'pending' ? 'warning' : 
                ($serviceRequest->status == 'cancelled' ? 'danger' : 'info')) 
            }}">
                {{ ucfirst(str_replace('_', ' ', $serviceRequest->status)) }}
            </span>
        </h1>
        <div>
            <a href="{{ route('admin.service-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.service-requests.edit', $serviceRequest) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <!-- Details Row -->
    <div class="row">
        <!-- Client & Service Info -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Request Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Request Number:</th>
                            <td>{{ $serviceRequest->request_number }}</td>
                        </tr>
                        <tr>
                            <th>Client:</th>
                            <td>{{ $serviceRequest->client->name }}</td>
                        </tr>
                        <tr>
                            <th>Service:</th>
                            <td>{{ $serviceRequest->service->name }}</td>
                        </tr>
                        <tr>
                            <th>Government Fee:</th>
                            <td>₹{{ number_format($serviceRequest->government_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Professional Fee:</th>
                            <td>₹{{ number_format($serviceRequest->professional_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td class="font-weight-bold">₹{{ number_format($serviceRequest->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Created Date:</th>
                            <td>{{ $serviceRequest->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status & Payment Info -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status & Payment</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Status:</th>
                            <td>
                                <span class="badge badge-{{ 
                                    $serviceRequest->status == 'completed' ? 'success' : 
                                    ($serviceRequest->status == 'pending' ? 'warning' : 
                                    ($serviceRequest->status == 'cancelled' ? 'danger' : 'info')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $serviceRequest->status)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Payment Status:</th>
                            <td>
                                <span class="badge badge-{{ 
                                    $serviceRequest->payment_status == 'paid' ? 'success' : 
                                    ($serviceRequest->payment_status == 'partial' ? 'warning' : 'danger') 
                                }}">
                                    {{ ucfirst($serviceRequest->payment_status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td>₹{{ number_format($serviceRequest->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Due Amount:</th>
                            <td class="font-weight-bold text-danger">₹{{ number_format($serviceRequest->due_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Start Date:</th>
                            <td>{{ $serviceRequest->start_date ? date('d M Y', strtotime($serviceRequest->start_date)) : 'Not started' }}</td>
                        </tr>
                        <tr>
                            <th>Expected Completion:</th>
                            <td>{{ $serviceRequest->expected_completion_date ? date('d M Y', strtotime($serviceRequest->expected_completion_date)) : 'Not set' }}</td>
                        </tr>
                        @if($serviceRequest->marketing_staff)
                        <tr>
                            <th>Marketing Staff:</th>
                            <td>{{ $serviceRequest->marketing_staff->name }}</td>
                        </tr>
                        @endif
                        @if($serviceRequest->backoffice_staff)
                        <tr>
                            <th>Back Office Staff:</th>
                            <td>{{ $serviceRequest->backoffice_staff->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Row -->
    <div class="row">
        <!-- Client Notes -->
        @if($serviceRequest->client_notes)
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Client Notes</h6>
                </div>
                <div class="card-body">
                    <p>{{ $serviceRequest->client_notes }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Internal Notes -->
        @if($serviceRequest->internal_notes)
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Internal Notes</h6>
                </div>
                <div class="card-body">
                    <p>{{ $serviceRequest->internal_notes }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Tasks Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tasks ({{ $serviceRequest->tasks->count() }})</h6>
            <a href="#" class="btn btn-sm btn-primary">Add Task</a>
        </div>
        <div class="card-body">
            @if($serviceRequest->tasks->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($serviceRequest->tasks as $task)
                        <tr>
                            <td>{{ $task->taskMaster->step_name ?? 'N/A' }}</td>
                            <td>{{ $task->assignedTo->name ?? 'Unassigned' }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $task->status == 'completed' ? 'success' : 
                                    ($task->status == 'pending' ? 'warning' : 'info') 
                                }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </td>
                            <td>{{ $task->due_date ? date('d M Y', strtotime($task->due_date)) : 'Not set' }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-muted">No tasks assigned yet.</p>
            @endif
        </div>
    </div>

    <!-- Documents Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Documents ({{ $serviceRequest->documents->count() }})</h6>
            <a href="#" class="btn btn-sm btn-primary">Upload Document</a>
        </div>
        <div class="card-body">
            @if($serviceRequest->documents->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>Uploaded By</th>
                            <th>Status</th>
                            <th>Upload Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($serviceRequest->documents as $document)
                        <tr>
                            <td>{{ $document->document_name ?? $document->requirement->document_name }}</td>
                            <td>{{ $document->uploadedBy->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $document->verification_status == 'verified' ? 'success' : 
                                    ($document->verification_status == 'rejected' ? 'danger' : 'warning') 
                                }}">
                                    {{ ucfirst($document->verification_status) }}
                                </span>
                            </td>
                            <td>{{ $document->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-download"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-muted">No documents uploaded yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection