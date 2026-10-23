@extends('layouts.admin')

@section('title', 'Service Requests')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Service Requests</h1>
        <a href="{{ route('admin.service-requests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Request
        </a>
    </div>

    <!-- Requests Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Service Requests</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>{{ $request->request_number }}</td>
                            <td>{{ $request->client->name }}</td>
                            <td>{{ $request->service->name }}</td>
                            <td>₹{{ number_format($request->total_amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $request->status == 'completed' ? 'success' : 
                                    ($request->status == 'pending' ? 'warning' : 
                                    ($request->status == 'cancelled' ? 'danger' : 'info')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ 
                                    $request->payment_status == 'paid' ? 'success' : 
                                    ($request->payment_status == 'partial' ? 'warning' : 'danger') 
                                }}">
                                    {{ ucfirst($request->payment_status) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.service-requests.show', $request) }}" 
                                   class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.service-requests.edit', $request) }}" 
                                   class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.service-requests.destroy', $request) }}" 
                                      method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            title="Delete" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection