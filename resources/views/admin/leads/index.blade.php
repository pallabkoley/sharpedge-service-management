@extends('layouts.admin')

@section('title', 'Leads Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Leads Management</h1>
        <a href="{{ route('admin.leads.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Lead
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Leads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                New Leads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['new'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Hot Leads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['hot'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fire fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Converted</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['converted'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Leads</h6>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    Filter by Status
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="?status=new">New</a>
                    <a class="dropdown-item" href="?status=contacted">Contacted</a>
                    <a class="dropdown-item" href="?status=qualified">Qualified</a>
                    <a class="dropdown-item" href="?status=won">Won</a>
                    <a class="dropdown-item" href="?status=lost">Lost</a>
                </div>
            </div>
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
                            <th>Lead Code</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Assigned To</th>
                            <th>Followup</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leads as $lead)
                        <tr>
                            <td>
                                <strong>{{ $lead->lead_code }}</strong>
                                @if($lead->converted_to_client)
                                <span class="badge badge-success" title="Converted to Client">
                                    <i class="fas fa-check"></i>
                                </span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $lead->name }}</strong>
                                @if($lead->company_name)
                                <br><small class="text-muted">{{ $lead->company_name }}</small>
                                @endif
                            </td>
                            <td>
                                @if($lead->email)
                                <div>{{ $lead->email }}</div>
                                @endif
                                <div>{{ $lead->phone }}</div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($lead->source) }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'new' => 'secondary',
                                        'contacted' => 'info',
                                        'qualified' => 'primary',
                                        'proposal' => 'warning',
                                        'negotiation' => 'warning',
                                        'won' => 'success',
                                        'lost' => 'danger',
                                    ];
                                @endphp
                                <span class="badge badge-{{ $statusColors[$lead->status] ?? 'secondary' }}">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar 
                                        {{ $lead->lead_score >= 70 ? 'bg-success' : 
                                           ($lead->lead_score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                        role="progressbar" 
                                        style="width: {{ $lead->lead_score }}%;" 
                                        aria-valuenow="{{ $lead->lead_score }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                        {{ $lead->lead_score }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($lead->assignedTo)
                                    {{ $lead->assignedTo->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->followup_date)
                                    @php
                                        $today = \Carbon\Carbon::today();
                                        $followupDate = \Carbon\Carbon::parse($lead->followup_date);
                                        $diff = $today->diffInDays($followupDate, false);
                                        
                                        if ($diff < 0) {
                                            $badgeClass = 'danger';
                                            $text = 'Overdue';
                                        } elseif ($diff == 0) {
                                            $badgeClass = 'warning';
                                            $text = 'Today';
                                        } elseif ($diff <= 2) {
                                            $badgeClass = 'info';
                                            $text = 'Soon';
                                        } else {
                                            $badgeClass = 'success';
                                            $text = 'Scheduled';
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">
                                        {{ $text }} ({{ $lead->followup_date->format('d/m') }})
                                    </span>
                                @else
                                    <span class="text-muted">No followup</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.leads.show', $lead) }}" 
                                       class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.leads.edit', $lead) }}" 
                                       class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$lead->converted_to_client)
                                    <form action="{{ route('leads.convert', $lead) }}" 
                                          method="POST" class="d-inline" onsubmit="return confirm('Convert this lead to client?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Convert to Client">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.leads.destroy', $lead) }}" 
                                          method="POST" class="d-inline" onsubmit="return confirm('Delete this lead?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $leads->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
</script>
@endsection