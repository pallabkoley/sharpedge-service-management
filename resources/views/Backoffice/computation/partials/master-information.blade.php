<div class="master-block">
    <div class="master-block-title">ASSESSEE MASTER INFORMATION</div>
    <div class="master-grid">
        <!-- LEFT COLUMN -->
        <div class="master-col">
            <div class="master-row"><div class="master-label">Name of Assessee</div><div class="master-val"><input type="text" name="name" value="{{ $computation->client->name ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Father's Name</div><div class="master-val"><input type="text" name="father_name" value="{{ $computation->client->father_name ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Date of Birth</div><div class="master-val"><input type="date" name="dob" value="{{ optional($computation->client->dob)->format('Y-m-d') ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">PAN Number</div><div class="master-val"><input type="text" name="pan" value="{{ $computation->client->pan ?? '' }}" class="inp-text" style="font-weight:bold;letter-spacing:1px;"></div></div>
            <div class="master-row"><div class="master-label">Aadhaar Number</div><div class="master-val"><input type="text" name="aadhaar" value="{{ $computation->client->aadhaar ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Residential Status</div><div class="master-val"><input type="text" name="residential_status" value="{{ $computation->client->residential_status ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Constitution</div><div class="master-val"><input type="text" name="constitution" value="{{ $computation->constitution ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Address</div><div class="master-val"><input type="text" name="address" value="{{ $computation->client->address ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">City</div><div class="master-val"><input type="text" name="city" value="{{ $computation->client->city ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">State</div><div class="master-val"><input type="text" name="state" value="{{ $computation->client->state ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">PIN Code</div><div class="master-val"><input type="text" name="pincode" value="{{ $computation->client->pincode ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Mobile Number</div><div class="master-val"><input type="text" name="phone" value="{{ $computation->client->phone ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Alternate Mobile Number</div><div class="master-val"><input type="text" name="alternate_mobile" value="{{ $computation->client->alternate_mobile ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Email Address</div><div class="master-val"><input type="email" name="email" value="{{ $computation->client->email ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Income Tax ID</div><div class="master-val"><input type="text" name="income_tax_id" value="{{ $computation->income_tax_id ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Income Tax Password</div><div class="master-val"><input type="password" name="income_tax_password" value="{{ $computation->income_tax_password ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Gmail Password</div><div class="master-val"><input type="password" name="gmail_password" value="{{ $computation->gmail_password ?? '' }}" class="inp-text"></div></div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="master-col">
            <div class="master-row"><div class="master-label">Trade Name</div><div class="master-val"><input type="text" name="trade_name" value="{{ $computation->trade_name ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Trade Code</div><div class="master-val"><input type="text" name="trade_code" value="{{ $computation->trade_code ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">GSTIN</div><div class="master-val"><input type="text" name="gst_number" value="{{ $computation->client->gst_number ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">UDIN</div><div class="master-val"><input type="text" name="udin" value="{{ $computation->udin ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">DIN Number</div><div class="master-val"><input type="text" name="din_number" value="{{ $computation->din_number ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">BSR Number</div><div class="master-val"><input type="text" name="bsr_no" value="{{ $computation->bsr_no ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Auditor Name</div><div class="master-val"><input type="text" name="auditor_name" value="{{ $computation->auditor_name ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Verified By</div><div class="master-val"><input type="text" name="verified_by" value="{{ $computation->verified_by ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Filing Status</div><div class="master-val">
                <select name="filing_status" class="inp inp-text">
                    <option value="">Select</option>
                    <option value="original" {{ $computation->filing_status == 'original' ? 'selected' : '' }}>Original</option>
                    <option value="revised" {{ $computation->filing_status == 'revised' ? 'selected' : '' }}>Revised</option>
                    <option value="belated" {{ $computation->filing_status == 'belated' ? 'selected' : '' }}>Belated</option>
                </select>
            </div></div>
            <div class="master-row"><div class="master-label">Return Type</div><div class="master-val">
                <select name="return_type" class="inp inp-text">
                    <option value="">Select</option>
                    <option value="ITR1" {{ $computation->return_type == 'ITR1' ? 'selected' : '' }}>ITR-1</option>
                    <option value="ITR2" {{ $computation->return_type == 'ITR2' ? 'selected' : '' }}>ITR-2</option>
                    <option value="ITR3" {{ $computation->return_type == 'ITR3' ? 'selected' : '' }}>ITR-3</option>
                    <option value="ITR4" {{ $computation->return_type == 'ITR4' ? 'selected' : '' }}>ITR-4</option>
                    <option value="ITR5" {{ $computation->return_type == 'ITR5' ? 'selected' : '' }}>ITR-5</option>
                    <option value="ITR6" {{ $computation->return_type == 'ITR6' ? 'selected' : '' }}>ITR-6</option>
                    <option value="ITR7" {{ $computation->return_type == 'ITR7' ? 'selected' : '' }}>ITR-7</option>
                </select>
            </div></div>
            <div class="master-row"><div class="master-label">Payment Status</div><div class="master-val">
                <select name="payment_status" class="inp inp-text">
                    <option value="">Select</option>
                    <option value="paid" {{ $computation->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ $computation->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="refund_due" {{ $computation->payment_status == 'refund_due' ? 'selected' : '' }}>Refund Due</option>
                </select>
            </div></div>
            <div class="master-row"><div class="master-label">Payment Date</div><div class="master-val"><input type="date" name="payment_date" value="{{ old('payment_date', optional($computation->payment_date)->format('Y-m-d')) }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Payment Mode</div><div class="master-val">
                <select name="payment_mode" class="inp inp-text">
                    <option value="">Select</option>
                    <option value="online" {{ $computation->payment_mode == 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ $computation->payment_mode == 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="challan" {{ $computation->payment_mode == 'challan' ? 'selected' : '' }}>Challan</option>
                </select>
            </div></div>
            <div class="master-row"><div class="master-label">Paid Amount</div><div class="master-val"><input type="number" step="0.01" name="paid_amount" value="{{ old('paid_amount', $computation->paid_amount ?? 0) }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Verify Status</div><div class="master-val">
                <select name="verify_status" class="inp inp-text">
                    <option value="">Select</option>
                    <option value="Pending" {{ $computation->verify_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Verified" {{ $computation->verify_status == 'Verified' ? 'selected' : '' }}>Verified</option>
                    <option value="Rejected" {{ $computation->verify_status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div></div>
            <div class="master-row"><div class="master-label">Verify Success</div><div class="master-val">
                <select name="verify_success" class="inp inp-text">
                    <option value="">Select</option>
                    <option value="Yes" {{ $computation->verify_success == 'Yes' ? 'selected' : '' }}>Yes</option>
                    <option value="No" {{ $computation->verify_success == 'No' ? 'selected' : '' }}>No</option>
                </select>
            </div></div>
            <div class="master-row"><div class="master-label">Audit Acknowledgement No.</div><div class="master-val"><input type="text" name="audit_acknowledgement_number" value="{{ $computation->audit_acknowledgement_number ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">10IE Acknowledgement No.</div><div class="master-val"><input type="text" name="ie10_acknowledgement_number" value="{{ $computation->ie10_acknowledgement_number ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Form Under Section</div><div class="master-val"><input type="text" name="form_under_section" value="{{ $computation->form_under_section ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">ITR Acknowledgement No.</div><div class="master-val"><input type="text" name="itr_acknowledgement_number" value="{{ $computation->itr_acknowledgement_number ?? '' }}" class="inp-text"></div></div>
            <div class="master-row"><div class="master-label">Notes</div><div class="master-val"><input type="text" name="notes" value="{{ $computation->notes ?? '' }}" class="inp-text"></div></div>
        </div>
    </div>
</div>