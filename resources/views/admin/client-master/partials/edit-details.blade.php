<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.client-master.update', $client) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                <!-- IDENTITY SECTION -->
                <div class="col-md-6">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">IDENTITY</h6>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Full Name / Entity Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name', $client->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Father's Name</label>
                        <input type="text" name="father_name" class="form-control form-control-sm @error('father_name') is-invalid @enderror" value="{{ old('father_name', $client->father_name) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">DOB / Incorporation</label>
                            <input type="date" name="dob" class="form-control form-control-sm @error('dob') is-invalid @enderror" value="{{ old('dob', $client->dob) }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">Gender</label>
                            <select name="gender" class="form-select form-select-sm @error('gender') is-invalid @enderror">
                                <option value="">Select</option>
                                <option value="Male" {{ old('gender', $client->gender)=='Male'?'selected':'' }}>Male</option>
                                <option value="Female" {{ old('gender', $client->gender)=='Female'?'selected':'' }}>Female</option>
                                <option value="Other" {{ old('gender', $client->gender)=='Other'?'selected':'' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">PAN</label>
                            <input type="text" name="pan" class="form-control form-control-sm text-uppercase @error('pan') is-invalid @enderror" value="{{ old('pan', $client->pan) }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">Aadhaar</label>
                            <input type="text" name="aadhaar" class="form-control form-control-sm @error('aadhaar') is-invalid @enderror" value="{{ old('aadhaar', $client->aadhaar) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">Client Type</label>
                            <select name="client_type" class="form-select form-select-sm @error('client_type') is-invalid @enderror">
                                <option value="">Select</option>
                                <option value="individual" {{ old('client_type', $client->client_type)=='individual'?'selected':'' }}>Individual</option>
                                <option value="company" {{ old('client_type', $client->client_type)=='company'?'selected':'' }}>Company</option>
                                <option value="firm" {{ old('client_type', $client->client_type)=='firm'?'selected':'' }}>Firm</option>
                                <option value="huf" {{ old('client_type', $client->client_type)=='huf'?'selected':'' }}>HUF</option>
                                <option value="trust" {{ old('client_type', $client->client_type)=='trust'?'selected':'' }}>Trust</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">Residential Status</label>
                            <input type="text" name="residential_status" class="form-control form-control-sm @error('residential_status') is-invalid @enderror" value="{{ old('residential_status', $client->residential_status) }}">
                        </div>
                    </div>
                </div>

                <!-- CONTACT SECTION -->
                <div class="col-md-6">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">CONTACT &amp; ADDRESS</h6>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">Mobile Number</label>
                            <input type="text" name="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror" value="{{ old('phone', $client->phone) }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">Alternate Mobile</label>
                            <input type="text" name="alternate_mobile" class="form-control form-control-sm @error('alternate_mobile') is-invalid @enderror" value="{{ old('alternate_mobile', $client->alternate_mobile) }}">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ old('email', $client->email) }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Address</label>
                        <textarea name="address" class="form-control form-control-sm @error('address') is-invalid @enderror" rows="2">{{ old('address', $client->address) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small">City</label>
                            <input type="text" name="city" class="form-control form-control-sm @error('city') is-invalid @enderror" value="{{ old('city', $client->city) }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small">State</label>
                            <input type="text" name="state" class="form-control form-control-sm @error('state') is-invalid @enderror" value="{{ old('state', $client->state) }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small">Pincode</label>
                            <input type="text" name="pincode" class="form-control form-control-sm @error('pincode') is-invalid @enderror" value="{{ old('pincode', $client->pincode) }}">
                        </div>
                    </div>
                </div>

                <!-- STATUTORY REGISTRATIONS -->
                <div class="col-12 mt-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">STATUTORY REGISTRATIONS</h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold small">GSTIN</label>
                            <input type="text" name="gst_number" class="form-control form-control-sm @error('gst_number') is-invalid @enderror" value="{{ old('gst_number', $client->gst_number) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold small">TAN</label>
                            <input type="text" name="tan" class="form-control form-control-sm @error('tan') is-invalid @enderror" value="{{ old('tan', $client->tan) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold small">CIN / LLPIN</label>
                            <input type="text" name="cin_llpin" class="form-control form-control-sm @error('cin_llpin') is-invalid @enderror" value="{{ old('cin_llpin', $client->cin_llpin) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold small">Last UDIN Used</label>
                            <input type="text" name="last_udin_used" class="form-control form-control-sm @error('last_udin_used') is-invalid @enderror" value="{{ old('last_udin_used', $client->last_udin_used) }}">
                        </div>
                    </div>
                </div>

                <!-- BUSINESS PROFILE -->
                <div class="col-12 mt-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">BUSINESS PROFILE</h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold small">Company Name</label>
                            <input type="text" name="company_name" class="form-control form-control-sm @error('company_name') is-invalid @enderror" value="{{ old('company_name', $client->company_name) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold small">Nature of Business</label>
                            <input type="text" name="nature_of_business" class="form-control form-control-sm @error('nature_of_business') is-invalid @enderror" value="{{ old('nature_of_business', $client->nature_of_business) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold small">Constitution</label>
                            <input type="text" name="constitution" class="form-control form-control-sm @error('constitution') is-invalid @enderror" value="{{ old('constitution', $client->constitution) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold small">Approx Annual Turnover</label>
                            <input type="number" step="0.01" name="approx_annual_turnover" class="form-control form-control-sm @error('approx_annual_turnover') is-invalid @enderror" value="{{ old('approx_annual_turnover', $client->approx_annual_turnover) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small">Client Status</label>
                            <select name="status" class="form-select form-select-sm @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', $client->status)=='active'?'selected':'' }}>Active</option>
                                <option value="inactive" {{ old('status', $client->status)=='inactive'?'selected':'' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $client->status)=='suspended'?'selected':'' }}>Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small">Source</label>
                            <input type="text" name="source" class="form-control form-control-sm @error('source') is-invalid @enderror" value="{{ old('source', $client->source) }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small">Preferred Contact</label>
                            <input type="text" name="preferred_contact" class="form-control form-control-sm @error('preferred_contact') is-invalid @enderror" value="{{ old('preferred_contact', $client->preferred_contact) }}">
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_credit_allowed" class="form-check-input" id="creditAllowed" value="1" {{ old('is_credit_allowed', $client->is_credit_allowed) ? 'checked' : '' }}>
                        <label class="form-check-label" for="creditAllowed">Allow Credit</label>
                    </div>
                </div>

                <!-- NOTES -->
                <div class="col-12 mt-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">NOTES</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">General Notes</label>
                            <textarea name="notes" class="form-control form-control-sm @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $client->notes) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold small">Compliance Notes</label>
                            <textarea name="compliance_notes" class="form-control form-control-sm @error('compliance_notes') is-invalid @enderror" rows="3">{{ old('compliance_notes', $client->compliance_notes) }}</textarea>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Profile Photo</label>
                        @if($client->profile_photo)
                            <div class="mb-1"><img src="{{ asset('storage/' . $client->profile_photo) }}" alt="Profile" style="width:50px;height:50px;object-fit:cover;border-radius:50%;"></div>
                        @endif
                        <input type="file" name="profile_photo" class="form-control form-control-sm @error('profile_photo') is-invalid @enderror">
                    </div>
                </div>

            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check2-circle me-1"></i> Update Client</button>
                <a href="{{ route('admin.client-master.show', $client) }}" class="btn btn-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>