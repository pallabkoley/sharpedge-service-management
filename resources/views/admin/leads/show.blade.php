@extends('layouts.admin')

@section('title', 'Lead Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Lead: {{ $lead->name }}
            <span class="badge badge-{{ 
                $lead->status == 'won' ? 'success' : 
                ($lead->status == 'lost' ? 'danger' : 
                ($lead->status == 'qualified' ? 'primary' : 'secondary')) 
            }}">
                {{ ucfirst($lead->status) }}
            </span>
            @if($lead->converted_to_client)
            <span class="badge badge-success">
                <i class="fas fa-check-circle"></i> Converted
            </span>
            @endif
        </h1>
        <div>
            <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            @if(!$lead->converted_to_client)
            <form action="{{ route('leads.convert', $lead) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Convert this lead to client?')">
                    <i class="fas fa-user-check"></i> Convert to Client
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Details Row -->
    <div class="row">
        <!-- Lead Information -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lead Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Lead Code:</th>
                                    <td>{{ $lead->lead_code }}</td>
                                </tr>
                                <tr>
                                    <th>Full Name:</th>
                                    <td>{{ $lead->name }}</td>
                                </tr>
                                <tr>
                                    <th>Company:</th>
                                    <td>{{ $lead->company_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>
                                        @if($lead->email)
                                        <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Phone:</th>
                                    <td>{{ $lead->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Source:</th>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($lead->source) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
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
                                </tr>
                                <tr>
                                    <th>Lead Score:</th>
                                    <td>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar 
                                                {{ $lead->lead_score >= 70 ? 'bg-success' : 
                                                   ($lead->lead_score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                role="progressbar" 
                                                style="width: {{ $lead->lead_score }}%;" 
                                                aria-valuenow="{{ $lead->lead_score }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small>{{ $lead->lead_score }}%</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($lead->notes)
                    <div class="mt-4">
                        <h6 class="font-weight-bold">Notes</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $lead->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Follow-ups Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Follow-ups</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFollowupModal">
                        <i class="fas fa-plus"></i> Add Follow-up
                    </button>
                </div>
                <div class="card-body">
                    @if($lead->followups->count() > 0)
                    <div class="timeline">
                        @foreach($lead->followups->sortByDesc('followup_date') as $followup)
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker 
                                {{ $followup->status == 'completed' ? 'bg-success' : 
                                   ($followup->status == 'pending' ? 'bg-warning' : 'bg-secondary') }}">
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h6 class="font-weight-bold">
                                        {{ ucfirst($followup->followup_type) }} Follow-up
                                        @if($followup->user)
                                        <small class="text-muted">by {{ $followup->user->name }}</small>
                                        @endif
                                    </h6>
                                    <span class="text-muted">
                                        {{ $followup->followup_date->format('d M Y, h:i A') }}
                                    </span>
                                </div>
                                <p class="mb-2">{{ $followup->notes }}</p>
                                @if($followup->next_followup_date)
                                <div class="alert alert-info py-2 mb-0">
                                    <i class="fas fa-calendar-alt"></i>
                                    Next follow-up: {{ $followup->next_followup_date->format('d M Y') }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-center text-muted">No follow-ups yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
            <!-- Assigned Staff -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assignment</h6>
                </div>
                <div class="card-body">
                    @if($lead->assignedTo)
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-tie fa-3x text-primary"></i>
                        </div>
                        <h5>{{ $lead->assignedTo->name }}</h5>
                        <p class="text-muted">{{ $lead->assignedTo->email }}</p>
                        <p class="mb-0">
                            <i class="fas fa-phone"></i> {{ $lead->assignedTo->phone ?? 'N/A' }}
                        </p>
                    </div>
                    @else
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-user-slash fa-3x mb-3"></i>
                        <p>Not assigned to any staff</p>
                        <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-plus"></i> Assign Now
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Follow-up Schedule -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Follow-up Schedule</h6>
                </div>
                <div class="card-body">
                    @if($lead->followup_date)
                    @php
                        $today = \Carbon\Carbon::today();
                        $followupDate = \Carbon\Carbon::parse($lead->followup_date);
                        $diff = $today->diffInDays($followupDate, false);
                        
                        if ($diff < 0) {
                            $alertClass = 'danger';
                            $message = 'Overdue by ' . abs($diff) . ' days';
                        } elseif ($diff == 0) {
                            $alertClass = 'warning';
                            $message = 'Today';
                        } elseif ($diff <= 2) {
                            $alertClass = 'info';
                            $message = 'In ' . $diff . ' days';
                        } else {
                            $alertClass = 'success';
                            $message = 'Scheduled';
                        }
                    @endphp
                    <div class="alert alert-{{ $alertClass }}">
                        <h4 class="alert-heading">
                            <i class="fas fa-calendar-check"></i> 
                            {{ $lead->followup_date->format('d M Y') }}
                        </h4>
                        <p class="mb-0">{{ $message }}</p>
                    </div>
                    @else
                    <div class="alert alert-secondary">
                        <p class="mb-0">No follow-up scheduled</p>
                    </div>
                    @endif
                    
                    @if($lead->createdBy)
                    <hr>
                    <p class="mb-0">
                        <small class="text-muted">
                            <i class="fas fa-user-plus"></i> Created by {{ $lead->createdBy->name }}
                            on {{ $lead->created_at->format('d M Y') }}
                        </small>
                    </p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="tel:{{ $lead->phone }}" class="btn btn-success">
                            <i class="fas fa-phone"></i> Call Now
                        </a>
                        @if($lead->email)
                        <a href="mailto:{{ $lead->email }}" class="btn btn-primary">
                            <i class="fas fa-envelope"></i> Send Email
                        </a>
                        @endif
                        <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-info">
                            <i class="fas fa-edit"></i> Edit Lead
                        </a>
                        @if(!$lead->converted_to_client)
                        <form action="{{ route('leads.convert', $lead) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100" 
                                    onclick="return confirm('Convert this lead to client?')">
                                <i class="fas fa-user-check"></i> Convert to Client
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Followup Modal -->
<div class="modal fade" id="addFollowupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('leads.followups.store', $lead) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Follow-up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="followup_type" class="form-label">Follow-up Type *</label>
                        <select class="form-control" id="followup_type" name="followup_type" required>
                            <option value="call">Phone Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="message">Message</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="next_followup_date" class="form-label">Next Follow-up Date</label>
                        <input type="date" class="form-control" id="next_followup_date" name="next_followup_date" 
                               min="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes *</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" 
                                  placeholder="Enter follow-up details..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Follow-up</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -15px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
.timeline-content {
    margin-left: 20px;
}
</style>
@endsection