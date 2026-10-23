@extends('layouts.admin')

@section('title', 'Add New Lead')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Lead</h1>
        <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Leads
        </a>
    </div>

    <!-- Create Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lead Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.leads.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Enter full name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" name="company_name" value="{{ old('company_name') }}" 
                                   placeholder="Enter company name (if any)">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="Enter email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                   placeholder="Enter phone number" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="source" class="form-label">Lead Source *</label>
                            <select class="form-control @error('source') is-invalid @enderror" 
                                    id="source" name="source" required>
                                <option value="">-- Select Source --</option>
                                <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                                <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="walkin" {{ old('source') == 'walkin' ? 'selected' : '' }}>Walk-in</option>
                                <option value="call" {{ old('source') == 'call' ? 'selected' : '' }}>Phone Call</option>
                                <option value="social_media" {{ old('source') == 'social_media' ? 'selected' : '' }}>Social Media</option>
                                <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>New</option>
                                <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="qualified" {{ old('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                                <option value="proposal" {{ old('status') == 'proposal' ? 'selected' : '' }}>Proposal Sent</option>
                                <option value="negotiation" {{ old('status') == 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                                <option value="won" {{ old('status') == 'won' ? 'selected' : '' }}>Won</option>
                                <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="lead_score" class="form-label">Lead Score (0-100)</label>
                            <input type="range" class="form-range @error('lead_score') is-invalid @enderror" 
                                   id="lead_score" name="lead_score" min="0" max="100" 
                                   value="{{ old('lead_score', 0) }}" oninput="updateScoreValue(this.value)">
                            @error('lead_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between">
                                <small>0</small>
                                <small id="scoreValue">0%</small>
                                <small>100</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assign To</label>
                            <select class="form-control @error('assigned_to') is-invalid @enderror" 
                                    id="assigned_to" name="assigned_to">
                                <option value="">-- Select Marketing Staff --</option>
                                @foreach($marketingStaff as $staff)
                                <option value="{{ $staff->id }}" {{ old('assigned_to') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ $staff->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="followup_date" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control @error('followup_date') is-invalid @enderror" 
                                   id="followup_date" name="followup_date" 
                                   value="{{ old('followup_date') }}">
                            @error('followup_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="4" 
                              placeholder="Add any notes about this lead...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary me-2">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Lead
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Update score value display
    function updateScoreValue(value) {
        document.getElementById('scoreValue').textContent = value + '%';
        
        // Update slider color based on value
        const slider = document.getElementById('lead_score');
        if (value >= 70) {
            slider.style.backgroundColor = '#28a745';
        } else if (value >= 40) {
            slider.style.backgroundColor = '#ffc107';
        } else {
            slider.style.backgroundColor = '#dc3545';
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const initialValue = document.getElementById('lead_score').value;
        updateScoreValue(initialValue);
    });
</script>
@endsection
@endsection