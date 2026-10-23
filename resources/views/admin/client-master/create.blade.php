@extends('layouts.admin')

@section('title', 'Add New Client')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2 text-primary"></i>Add New Client</h4>
            <p class="text-muted small">Create a new client profile</p>
        </div>
        <a href="{{ route('admin.client-master.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>

    <form action="{{ route('admin.client-master.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Personal Information</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Father's Name</label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name') }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">DOB</label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">Select</option>
                                    <option value="Male" {{ old('gender')=='Male'?'selected':'' }}>Male</option>
                                    <option value="Female" {{ old('gender')=='Female'?'selected':'' }}>Female</option>
                                    <option value="Other" {{ old('gender')=='Other'?'selected':'' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Alternate Mobile</label>
                                <input type="text" name="alternate_mobile" class="form-control @error('alternate_mobile') is-invalid @enderror" value="{{ old('alternate_mobile') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Profile Photo</label>
                            <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2"></i>Identity & Address</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">PAN</label>
                                <input type="text" name="pan" class="form-control @error('pan') is-invalid @enderror" value="{{ old('pan') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Aadhaar</label>
                                <input type="text" name="aadhaar" class="form-control @error('aadhaar') is-invalid @enderror" value="{{ old('aadhaar') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">State</label>
                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Pincode</label>
                                <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Residential Status</label>
                            <input type="text" name="residential_status" class="form-control @error('residential_status') is-invalid @enderror" value="{{ old('residential_status') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-building me-2"></i>Business & Tax Information</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Company Name</label>
                                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">GST Number</label>
                                <input type="text" name="gst_number" class="form-control @error('gst_number') is-invalid @enderror" value="{{ old('gst_number') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">TAN</label>
                                <input type="text" name="tan" class="form-control @error('tan') is-invalid @enderror" value="{{ old('tan') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">CIN/LLPIN</label>
                                <input type="text" name="cin_llpin" class="form-control @error('cin_llpin') is-invalid @enderror" value="{{ old('cin_llpin') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Nature of Business</label>
                                <input type="text" name="nature_of_business" class="form-control @error('nature_of_business') is-invalid @enderror" value="{{ old('nature_of_business') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Constitution</label>
                                <input type="text" name="constitution" class="form-control @error('constitution') is-invalid @enderror" value="{{ old('constitution') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Approx Annual Turnover</label>
                                <input type="number" step="0.01" name="approx_annual_turnover" class="form-control @error('approx_annual_turnover') is-invalid @enderror" value="{{ old('approx_annual_turnover') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Client Type</label>
                                <select name="client_type" class="form-select @error('client_type') is-invalid @enderror">
                                    <option value="">Select</option>
                                    <option value="individual" {{ old('client_type')=='individual'?'selected':'' }}>Individual</option>
                                    <option value="company" {{ old('client_type')=='company'?'selected':'' }}>Company</option>
                                    <option value="firm" {{ old('client_type')=='firm'?'selected':'' }}>Firm</option>
                                    <option value="huf" {{ old('client_type')=='huf'?'selected':'' }}>HUF</option>
                                    <option value="trust" {{ old('client_type')=='trust'?'selected':'' }}>Trust</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status')=='active'?'selected':'' }}>Active</option>
                                    <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>Inactive</option>
                                    <option value="suspended" {{ old('status')=='suspended'?'selected':'' }}>Suspended</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Last UDIN Used</label>
                                <input type="text" name="last_udin_used" class="form-control @error('last_udin_used') is-invalid @enderror" value="{{ old('last_udin_used') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Preferred Contact</label>
                                <input type="text" name="preferred_contact" class="form-control @error('preferred_contact') is-invalid @enderror" value="{{ old('preferred_contact') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Source</label>
                            <input type="text" name="source" class="form-control @error('source') is-invalid @enderror" value="{{ old('source') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Compliance Notes</label>
                            <textarea name="compliance_notes" class="form-control @error('compliance_notes') is-invalid @enderror" rows="2">{{ old('compliance_notes') }}</textarea>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_credit_allowed" class="form-check-input" id="creditAllowed" value="1" {{ old('is_credit_allowed') ? 'checked' : '' }}>
                            <label class="form-check-label" for="creditAllowed">Allow Credit</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('admin.client-master.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Create Client</button>
        </div>
    </form>
</div>
@endsection