<style>
    /* ----- Base Report Container ----- */
    .summary-print {
        background: #fff;
        padding: 20px 30px;
        border-radius: 8px;
        font-family: 'Segoe UI', 'Inter', Roboto, system-ui, sans-serif;
        color: #17365d;
    }

    /* ----- Header ----- */
    .summary-header {
        border-bottom: 2px solid #1d4ed8;
        padding-bottom: 15px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    .summary-header .brand {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .summary-header .brand img {
        height: 50px;
        width: auto;
        max-height: 60px;
        object-fit: contain;
    }
    .summary-header h5 {
        color: #1d4ed8;
        font-weight: 700;
        margin: 0;
    }
    .summary-header .firm-sub {
        font-size: 12px;
        color: #64748b;
    }
    .summary-header .client-info {
        text-align: right;
    }
    .summary-header .client-info .name {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    /* ----- Section Headings – Admin Blue ----- */
    .summary-section {
        margin-top: 22px;
    }
    .summary-section h6 {
        font-size: 11px;
        font-weight: 700;
        color: #1d4ed8;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        padding-bottom: 6px;
        border-bottom: 1px solid #d8d0bd;
        text-transform: uppercase;
    }

    /* ----- Two‑column Info Grid ----- */
    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 30px;
    }
    .summary-item {
        display: flex;
        padding: 4px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .summary-item .label {
        font-weight: 500;
        color: #64748b;
        width: 150px;
        flex-shrink: 0;
        font-size: 13px;
    }
    .summary-item .value {
        color: #17365d;
        font-size: 13px;
        font-weight: 500;
    }

    /* ----- Tables – Admin Dark Blue Headers ----- */
    .summary-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
    .summary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9px;
        color: #17365d;
        border: 1px solid #d8d0bd;
        margin-top: 4px;
    }
    .summary-table th {
        background: #3A4F73 !important;
        color: #ffffff !important;
        font-weight: 600;
        text-align: left;
        padding: 9px 8px;
        border-bottom: 1px solid #2c3e5a;
        border-right: 1px solid #4a6080;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .summary-table td {
        padding: 9px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #e3dfd4;
        border-right: 1px solid #e3dfd4;
        font-size: 9px;
    }
    .summary-table tr:last-child td {
        border-bottom: none;
    }
    .summary-table th:last-child,
    .summary-table td:last-child {
        border-right: none;
    }
    .summary-table tbody tr:hover {
        background: #fafaf7;
    }

    /* ----- Badges & Statuses inside tables ----- */
    .summary-badge {
        display: inline-block;
        margin-top: 3px;
        padding: 2px 6px;
        border-radius: 10px;
        background: #f3f0e8;
        color: #8b6508;
        font-size: 8px;
    }
    .summary-status {
        display: inline-block;
        padding: 3px 8px;
        border: 1px solid #d8d0bd;
        border-radius: 10px;
        background: #f8f6ef;
        color: #17365d;
        font-size: 8px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* ----- Follow‑up rows ----- */
    .followup-row td {
        background: #faf9f5;
        font-size: 8px;
        padding: 6px 8px;
    }
    .followup-item {
        display: flex;
        gap: 15px;
        padding: 3px 0;
        color: #555;
        align-items: baseline;
    }
    .followup-item .date {
        font-weight: 600;
        color: #1d4ed8;
        min-width: 80px;
    }
    .followup-item .user {
        color: #94a3b8;
        font-style: italic;
        margin-left: auto;
    }

    /* ----- Simple Progress Bars (Clean & Normal) ----- */
    .simple-progress-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        max-width: 180px;
    }
    .simple-progress-bar {
        height: 16px;
        background: #1d4ed8;
        border-radius: 3px;
        flex-shrink: 0;
        transition: width 0.3s;
    }
    .simple-progress-wrapper .simple-progress-label {
        font-size: 9px;
        font-weight: 600;
        color: #17365d;
        white-space: nowrap;
        min-width: 40px;
    }

    /* ----- No data message ----- */
    .no-data {
        color: #94a3b8;
        font-style: italic;
        font-size: 13px;
        padding: 4px 0;
    }

    /* ----- Responsive ----- */
    @media (max-width: 768px) {
        .summary-print { padding: 12px 16px; }
        .summary-grid { grid-template-columns: 1fr; }
        .summary-header { flex-direction: column; align-items: flex-start; }
        .summary-header .client-info { text-align: left; margin-top: 8px; }
        .summary-table { font-size: 8px; }
        .summary-table th, .summary-table td { padding: 6px 4px; }
        .followup-item { flex-wrap: wrap; gap: 5px; }
        .simple-progress-wrapper { max-width: 120px; }
        .simple-progress-bar { height: 12px; }
    }
    @media print {
        .no-print { display: none !important; }
        .summary-print { padding: 10px; }
        .summary-section { page-break-inside: avoid; }
    }
</style>

<div class="summary-print" id="clientSummary">

    {{-- ============================================================
        HEADER – TSE India + Client Name + PAN
    ============================================================ --}}
    <div class="summary-header">
        <div class="brand">
            <img src="{{ asset('storage/SRP_LOGO/logo.png') }}" alt="TSE India Logo" onerror="this.style.display='none'">
            <div>
                <h5 class="mb-0">TSE India</h5>
                <div class="firm-sub">Chartered Accountants &amp; Advisory</div>
            </div>
        </div>
        <div class="client-info">
            <div class="name">{{ $client->name }}</div>
            <div class="small text-muted">PAN: {{ $client->pan ?? 'N/A' }}</div>
        </div>
    </div>

    {{-- ============================================================
        SECTION 1 – CONTACT
    ============================================================ --}}
    <div class="summary-section">
        <h6>CONTACT</h6>
        <div class="summary-grid">
            <div class="summary-item"><span class="label">Mobile Number</span><span class="value">{{ $client->phone ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Alternate Mobile</span><span class="value">{{ $client->alternate_mobile ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Email</span><span class="value">{{ $client->email ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Address</span><span class="value">{{ $client->address ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">City</span><span class="value">{{ $client->city ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">State</span><span class="value">{{ $client->state ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Pincode</span><span class="value">{{ $client->pincode ?? 'N/A' }}</span></div>
        </div>
    </div>

    {{-- ============================================================
        SECTION 2 – STATUTORY REGISTRATIONS
    ============================================================ --}}
    <div class="summary-section">
        <h6>STATUTORY REGISTRATIONS</h6>
        <div class="summary-grid">
            <div class="summary-item"><span class="label">GSTIN</span><span class="value">{{ $client->gst_number ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">TAN</span><span class="value">{{ $client->tan ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">CIN / LLPIN</span><span class="value">{{ $client->cin_llpin ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Last UDIN Used</span><span class="value">{{ $client->last_udin_used ?? 'N/A' }}</span></div>
        </div>
    </div>

    {{-- ============================================================
        SECTION 3 – BANKING
    ============================================================ --}}
    @if($client->bankAccounts->count())
    <div class="summary-section">
        <h6>BANKING</h6>
        <div class="summary-table-wrapper">
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>IFSC Code</th>
                        <th>Account Type</th>
                        <th>Primary</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($client->bankAccounts as $account)
                    <tr>
                        <td>{{ $account->bank_name }}</td>
                        <td>{{ $account->account_number }}</td>
                        <td>{{ $account->ifsc_code ?? 'N/A' }}</td>
                        <td>{{ $account->account_type ?? 'N/A' }}</td>
                        <td>{{ $account->is_primary ? 'Yes' : 'No' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ============================================================
        SECTION 4 – BUSINESS PROFILE
    ============================================================ --}}
    <div class="summary-section">
        <h6>BUSINESS PROFILE</h6>
        <div class="summary-grid">
            <div class="summary-item"><span class="label">Company Name</span><span class="value">{{ $client->company_name ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Nature of Business</span><span class="value">{{ $client->nature_of_business ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Constitution</span><span class="value">{{ $client->constitution ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Approx Annual Turnover</span><span class="value">{{ $client->approx_annual_turnover ? '₹ ' . number_format($client->approx_annual_turnover,2) : 'N/A' }}</span></div>
        </div>
    </div>

    {{-- ============================================================
        SECTION 5 – COMPLIANCE NOTES
    ============================================================ --}}
    <div class="summary-section">
        <h6>COMPLIANCE NOTES</h6>
        @if(trim($client->compliance_notes ?? '') !== '')
            <div style="padding:4px 0; font-size:13px; color:#17365d;">{{ $client->compliance_notes }}</div>
        @else
            <div class="no-data">No notes on file</div>
        @endif
    </div>

    {{-- ============================================================
        SECTION 6 – IDENTITY DETAILS
    ============================================================ --}}
    <div class="summary-section">
        <h6>IDENTITY DETAILS</h6>
        <div class="summary-grid">
            <div class="summary-item"><span class="label">Father's Name</span><span class="value">{{ $client->father_name ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">DOB / Incorporation</span><span class="value">{{ $client->dob ? \Carbon\Carbon::parse($client->dob)->format('d-m-Y') : 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Gender</span><span class="value">{{ $client->gender ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Residential Status</span><span class="value">{{ $client->residential_status ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Client Type</span><span class="value">{{ ucfirst($client->client_type ?? 'N/A') }}</span></div>
            <div class="summary-item"><span class="label">Source</span><span class="value">{{ $client->source ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Preferred Contact</span><span class="value">{{ $client->preferred_contact ?? 'N/A' }}</span></div>
            <div class="summary-item"><span class="label">Credit Allowed</span><span class="value">{{ $client->is_credit_allowed ? 'Yes' : 'No' }}</span></div>
            @if(!empty($client->aadhaar))
            <div class="summary-item"><span class="label">Aadhaar</span><span class="value">{{ $client->aadhaar }}</span></div>
            @endif
            @if(!empty($client->registration_type))
            <div class="summary-item"><span class="label">Registration Type</span><span class="value">{{ $client->registration_type }}</span></div>
            @endif
        </div>
    </div>

    {{-- ============================================================
        SECTION 7 – GST & INCOME TAX CASES
    ============================================================ --}}
    @if($client->cases->count())
    <div class="summary-section">
        <h6>GST &amp; INCOME TAX CASES</h6>
        <div class="summary-table-wrapper">
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Case Type</th>
                        <th>Financial Year</th>
                        <th>Reference / DIN</th>
                        <th>Authority / Office</th>
                        <th>Next Follow-up</th>
                        <th>Amount Involved</th>
                        <th>Status</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($client->cases as $case)
                    <tr>
                        <td>
                            <strong>{{ $case->case_type }}</strong>
                            @if($case->other_case_type)
                                <br><span class="summary-badge">{{ $case->other_case_type }}</span>
                            @endif
                        </td>
                        <td>{{ $case->financial_year ?? 'N/A' }}</td>
                        <td>{{ $case->reference_notice_din ?? 'N/A' }}</td>
                        <td>{{ $case->issuing_authority_office ?? 'N/A' }}</td>
                        <td>
                            {{ $case->next_follow_up_date ? \Carbon\Carbon::parse($case->next_follow_up_date)->format('d-m-Y') : 'N/A' }}
                        </td>
                        <td>
                            {{ $case->amount_involved ? '₹ ' . number_format($case->amount_involved,2) : 'N/A' }}
                        </td>
                        <td><span class="summary-status">{{ $case->status ?? 'N/A' }}</span></td>
                        <td>{{ $case->case_description ?? 'N/A' }}</td>
                    </tr>
                    @if($case->followUps->count())
                    <tr class="followup-row">
                        <td colspan="8">
                            <strong>Follow-ups:</strong>
                            @foreach($case->followUps as $fu)
                            <div class="followup-item">
                                <span class="date">{{ \Carbon\Carbon::parse($fu->follow_up_date)->format('d-m-Y') }}</span>
                                <span>{{ $fu->note }}</span>
                                @if($fu->createdBy)
                                    <span class="user">— {{ $fu->createdBy->name }}</span>
                                @endif
                            </div>
                            @endforeach
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ============================================================
        SECTION 8 – COMPLIANCE TRACKER
    ============================================================ --}}
    @if($client->compliances->count())
    <div class="summary-section">
        <h6>COMPLIANCE TRACKER</h6>
        <div class="summary-table-wrapper">
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Compliance Type</th>
                        <th>Period</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($client->compliances as $compliance)
                    <tr>
                        <td>
                            <strong>{{ $compliance->compliance_type }}</strong>
                            @if($compliance->repeat_automatically)
                                <br><span class="summary-badge">Repeat</span>
                            @endif
                            @if($compliance->parentCompliance)
                                <br><span class="summary-badge">Parent: {{ $compliance->parentCompliance->compliance_type }}</span>
                            @endif
                        </td>
                        <td>{{ $compliance->period ?? 'N/A' }}</td>
                        <td>{{ $compliance->due_date ? \Carbon\Carbon::parse($compliance->due_date)->format('d-m-Y') : 'N/A' }}</td>
                        <td><span class="summary-status">{{ ucfirst($compliance->status ?? 'Pending') }}</span></td>
                        <td>{{ $compliance->notes ?? 'N/A' }}</td>
                    </tr>
                    @if($compliance->followUps->count())
                    <tr class="followup-row">
                        <td colspan="5">
                            <strong>Follow-ups:</strong>
                            @foreach($compliance->followUps as $fu)
                            <div class="followup-item">
                                <span class="date">{{ \Carbon\Carbon::parse($fu->follow_up_date)->format('d-m-Y') }}</span>
                                <span>{{ $fu->note }}</span>
                                @if($fu->createdBy)
                                    <span class="user">— {{ $fu->createdBy->name }}</span>
                                @endif
                            </div>
                            @endforeach
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ============================================================
        SECTION 9 – PORTAL IDs
    ============================================================ --}}
    @if($client->portalCredentials->count())
    <div class="summary-section">
        <h6>PORTAL IDs</h6>
        <div class="summary-table-wrapper">
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Portal</th>
                        <th>Username</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($client->portalCredentials as $cred)
                    <tr>
                        <td>{{ $cred->portal }} @if($cred->other_portal_name)({{ $cred->other_portal_name }})@endif</td>
                        <td>{{ $cred->login_username ?? 'N/A' }}</td>
                        <td>{{ $cred->notes ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="font-size:8px;color:#94a3b8;margin-top:4px;">* Passwords are not displayed for security.</div>
        </div>
    </div>
    @endif

    {{-- ============================================================
        SECTION 10 – TURNOVER – YEAR WISE (Simple Bars)
    ============================================================ --}}
    @php
        $turnovers = $client->turnovers->groupBy('financial_year')->map(function($items) {
            return $items->sum('amount');
        });
        $maxTotal = $turnovers->max() ?: 1;
    @endphp
    @if($turnovers->count())
    <div class="summary-section">
        <h6>TURNOVER – YEAR WISE</h6>
        <div class="summary-table-wrapper">
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width:25%;">Financial Year</th>
                        <th style="width:25%;">Total Turnover</th>
                        <th style="width:50%;">Relative</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($turnovers->sortKeysDesc() as $fy => $total)
                    @php $pct = ($maxTotal > 0) ? round(($total / $maxTotal) * 100, 2) : 0; @endphp
                    <tr>
                        <td><strong>{{ $fy }}</strong></td>
                        <td>₹ {{ number_format($total, 2) }}</td>
                        <td>
                            <div class="simple-progress-wrapper">
                                <div class="simple-progress-bar" style="width: {{ min($pct, 100) }}%;"></div>
                                <span class="simple-progress-label">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ============================================================
        CUSTOM FIELDS
    ============================================================ --}}
    @if(isset($customFields) && $customFields->count())
    <div class="summary-section">
        <h6>CUSTOM FIELDS</h6>
        <div class="summary-grid">
            @foreach($customFields as $field)
                @php
                    $valueObj = $client->customFieldValues->where('custom_field_id', $field->id)->first();
                    $displayValue = $valueObj ? $valueObj->field_value : 'N/A';
                    if (is_array($displayValue) || is_object($displayValue)) {
                        $displayValue = json_encode($displayValue);
                    }
                @endphp
                <div class="summary-item">
                    <span class="label">{{ $field->field_label }}</span>
                    <span class="value">{{ $displayValue }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ============================================================
        FOOTER + DOWNLOAD PDF
    ============================================================ --}}
    <div style="margin-top:30px; border-top:1px solid #e2e8f0; padding-top:10px; display:flex; justify-content:space-between; font-size:12px; color:#94a3b8;">
        <span>Generated: {{ now()->format('d-m-Y H:i') }}</span>
        <span class="no-print">
            <a href="{{ route('admin.client-master.pdf', $client) }}" class="btn btn-primary btn-sm" target="_blank" style="text-decoration:none; background:#1d4ed8; color:#fff; padding:4px 14px; border-radius:4px; font-weight:500;">
                <i class="bi bi-file-pdf me-1"></i> Download PDF
            </a>
        </span>
    </div>

</div>