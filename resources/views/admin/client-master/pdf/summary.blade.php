<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Client Master Report – {{ $client->name }}</title>
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
        font-size: 10px;
        color: #334155;
        background: #ffffff;
        padding: 14px 16px;
        line-height: 1.5;
    }
    .report { max-width: 100%; }

    /* HEADER */
    .report-header {
        border-bottom: 2px solid #1e3a5f;
        padding-bottom: 10px;
        margin-bottom: 18px;
        display: table;
        width: 100%;
    }
    .report-header .left { display: table-cell; vertical-align: top; }
    .report-header .left .firm-name {
        font-weight: 700; font-size: 18px; color: #1e3a5f; letter-spacing: 0.3px;
    }
    .report-header .left .firm-sub { font-size: 10px; color: #64748b; margin-top: 2px; }
    .report-header .right { display: table-cell; vertical-align: top; text-align: right; }
    .report-header .right .client-name {
        font-weight: 700; font-size: 17px; color: #1e3a5f;
    }
    .report-header .right .meta { font-size: 9.5px; color: #64748b; margin-top: 2px; }
    .report-header .right .meta span { margin-left: 10px; }

    /* SECTION TITLES */
    .section-title {
        font-weight: 700; font-size: 11px; color: #1e3a5f; text-transform: uppercase;
        letter-spacing: 0.5px; border-bottom: 1px solid #dbe3ec; padding-bottom: 4px;
        margin-top: 18px; margin-bottom: 10px;
        page-break-after: avoid;
        page-break-before: avoid;
    }
    .section-title.first { margin-top: 0; }
    .section-title .num { color: #64748b; margin-right: 6px; }

    /* TWO-COLUMN INFO GRID */
    .info-grid { width: 100%; border-collapse: collapse; }
    .info-grid .row { border-bottom: 1px solid #f1f5f9; }
    .info-grid .row .label {
        font-weight: 600; color: #64748b; font-size: 9px; padding: 4px 12px 4px 0;
        width: 130px; text-transform: uppercase; letter-spacing: 0.3px; vertical-align: top;
    }
    .info-grid .row .value {
        color: #334155; font-size: 10px; padding: 4px 0; vertical-align: top;
    }
    .info-grid .row .label-right { padding-left: 24px; }
    .info-grid .row .value-right { padding-left: 24px; }

    /* TABLES */
    .data-table {
        width: 100%; border-collapse: collapse; font-size: 9px; margin-top: 4px;
        border: 1px solid #dbe3ec;
    }
    .data-table thead { display: table-header-group; }
    .data-table tfoot { display: table-footer-group; }
    .data-table tr { page-break-inside: auto; page-break-after: auto; }
    .data-table td { word-wrap: break-word; }
    .data-table thead th {
        background: #f8fafc; color: #1e3a5f; font-weight: 700; font-size: 8px;
        text-transform: uppercase; letter-spacing: 0.3px; padding: 6px 6px;
        text-align: left; border-bottom: 1px solid #dbe3ec; border-right: 1px solid #dbe3ec;
    }
    .data-table thead th:last-child { border-right: none; }
    .data-table tbody td {
        padding: 5px 6px; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9;
        color: #334155; vertical-align: top;
    }
    .data-table tbody td:last-child { border-right: none; }
    .data-table tbody tr:last-child td { border-bottom: none; }

    /* STATUS BADGE */
    .badge-status {
        font-size: 7.5px; padding: 2px 8px; border-radius: 10px; font-weight: 600;
        text-transform: uppercase; display: inline-block; background: #f1f5f9; color: #475569;
    }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-danger  { background: #fde8e5; color: #b3362a; }
    .badge-info    { background: #dbeafe; color: #1e40af; }

    /* FOLLOW-UP SUB-ROW */
    .followup-row td {
        background: #fafbfc; padding: 4px 6px 4px 20px; font-size: 8.5px; color: #64748b;
        border-bottom: 1px solid #f1f5f9;
    }
    .followup-row .followup-item { display: inline-block; margin-right: 16px; }
    .followup-row .followup-item .date { font-weight: 600; color: #1e3a5f; }

    /* NOTES BOX */
    .notes-box {
        border: 1px solid #dbe3ec; border-radius: 3px; padding: 6px 10px;
        background: #fafbfc; font-size: 10px; color: #334155; line-height: 1.6;
    }
    .notes-box.empty { color: #94a3b8; font-style: italic; }

    /* SECURITY NOTE */
    .security-note { font-size: 7.5px; color: #94a3b8; margin-top: 2px; font-style: italic; }

    /* TURNOVER TABLE */
    .turnover-table {
        width: 100%; border-collapse: collapse; font-size: 8.5px; margin-top: 4px;
        border: 1px solid #dbe3ec;
    }
    .turnover-table thead { display: table-header-group; }
    .turnover-table tfoot { display: table-footer-group; }
    .turnover-table tr { page-break-inside: auto; page-break-after: auto; }
    .turnover-table td { word-wrap: break-word; }
    .turnover-table thead th {
        background: #f8fafc; color: #1e3a5f; font-weight: 700; font-size: 7.5px;
        text-transform: uppercase; letter-spacing: 0.3px; padding: 4px 4px; text-align: center;
        border-bottom: 1px solid #dbe3ec; border-right: 1px solid #dbe3ec;
    }
    .turnover-table thead th:last-child { border-right: none; }
    .turnover-table tbody td {
        padding: 4px 4px; text-align: center; border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9; color: #334155; font-size: 8.5px;
    }
    .turnover-table tbody td:last-child { border-right: none; font-weight: 700; background: #fafbfc; }
    .turnover-table tbody tr:last-child td { border-bottom: none; }
    .turnover-table .year-label td {
        font-weight: 700; background: #f8fafc; text-align: left; padding: 6px 6px;
        color: #1e3a5f; font-size: 9.5px; border-top: 1px solid #dbe3ec;
    }

    /* FOOTER */
    .report-footer {
        margin-top: 24px; padding-top: 6px; border-top: 1px solid #dbe3ec;
        display: table; width: 100%; font-size: 7.5px; color: #94a3b8;
    }
    .report-footer .left { display: table-cell; text-align: left; }
    .report-footer .right { display: table-cell; text-align: right; }

    /* PAGE SETTINGS */
    @page { margin: 12mm 14mm 10mm 14mm; size: A4 portrait; }

    /* UTILITY */
    .no-data { color: #94a3b8; font-style: italic; font-size: 9px; padding: 4px 0; }
    .mt-1 { margin-top: 4px; }
    .mb-1 { margin-bottom: 4px; }
    .page-break { page-break-before: always; }
    .keep-together { page-break-inside: avoid; } /* ঐচ্ছিক – শুধু প্রয়োজনীয় জায়গায় ব্যবহার করুন */
    .text-muted { color: #64748b; }
    .text-center { text-align: center; }

    /* @media print সম্পূর্ণ সরানো হয়েছে */
</style>
</head>
<body>

<div class="report">

    <!-- ============================================================
    HEADER
    ============================================================ -->
    <div class="report-header">
        <div class="left">
            <div class="firm-name">TSE India</div>
            <div class="firm-sub">Chartered Accountants &amp; Advisory</div>
        </div>
        <div class="right">
            <div class="client-name">{{ $client->name }}</div>
            <div class="meta">
                <span>PAN: {{ $client->pan ?? 'N/A' }}</span>
                <span>Code: {{ $client->client_code ?? 'N/A' }}</span>
                <span>Status: {{ ucfirst($client->status ?? 'Active') }}</span>
            </div>
        </div>
    </div>

    <!-- ============================================================
    1. CLIENT OVERVIEW
    ============================================================ -->
    <div class="section-title first"><span class="num">01</span> Client Overview</div>
    <table class="info-grid">
        <tr class="row">
            <td class="label">Client Name</td>
            <td class="value">{{ $client->name }}</td>
            <td class="label label-right">Client Code</td>
            <td class="value value-right">{{ $client->client_code ?? 'N/A' }}</td>
        </tr>
        <tr class="row">
            <td class="label">PAN</td>
            <td class="value">{{ $client->pan ?? 'N/A' }}</td>
            <td class="label label-right">Client Type</td>
            <td class="value value-right">{{ ucfirst($client->client_type ?? 'N/A') }}</td>
        </tr>
        <tr class="row">
            <td class="label">DOB / Incorporation</td>
            <td class="value">{{ $client->dob ? \Carbon\Carbon::parse($client->dob)->format('d-m-Y') : 'N/A' }}</td>
            <td class="label label-right">Status</td>
            <td class="value value-right"><span class="badge-status">{{ ucfirst($client->status ?? 'Active') }}</span></td>
        </tr>
    </table>

    <!-- ============================================================
    2. CONTACT INFORMATION
    ============================================================ -->
    <div class="section-title"><span class="num">02</span> Contact Information</div>
    <table class="info-grid">
        <tr class="row">
            <td class="label">Mobile Number</td>
            <td class="value">{{ $client->phone ?? 'N/A' }}</td>
            <td class="label label-right">Alternate Mobile</td>
            <td class="value value-right">{{ $client->alternate_mobile ?? 'N/A' }}</td>
        </tr>
        <tr class="row">
            <td class="label">Email</td>
            <td class="value">{{ $client->email ?? 'N/A' }}</td>
            <td class="label label-right">Address</td>
            <td class="value value-right">{{ $client->address ?? 'N/A' }}</td>
        </tr>
        <tr class="row">
            <td class="label">City</td>
            <td class="value">{{ $client->city ?? 'N/A' }}</td>
            <td class="label label-right">State</td>
            <td class="value value-right">{{ $client->state ?? 'N/A' }}</td>
        </tr>
        <tr class="row">
            <td class="label">Pincode</td>
            <td class="value">{{ $client->pincode ?? 'N/A' }}</td>
            <td class="label label-right"></td>
            <td class="value value-right"></td>
        </tr>
    </table>

    <!-- ============================================================
    3. STATUTORY REGISTRATIONS
    ============================================================ -->
    <div class="section-title"><span class="num">03</span> Statutory Registrations</div>
    <table class="info-grid">
        <tr class="row">
            <td class="label">GSTIN</td>
            <td class="value">{{ $client->gst_number ?? 'N/A' }}</td>
            <td class="label label-right">TAN</td>
            <td class="value value-right">{{ $client->tan ?? 'N/A' }}</td>
        </tr>
        <tr class="row">
            <td class="label">CIN / LLPIN</td>
            <td class="value">{{ $client->cin_llpin ?? 'N/A' }}</td>
            <td class="label label-right">Last UDIN Used</td>
            <td class="value value-right">{{ $client->last_udin_used ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- ============================================================
    4. BANKING
    ============================================================ -->
    @if($client->bankAccounts->count())
    <div class="section-title"><span class="num">04</span> Banking</div>
    <table class="data-table">
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
    @endif

    <!-- ============================================================
    5. BUSINESS PROFILE
    ============================================================ -->
    <div class="section-title"><span class="num">05</span> Business Profile</div>
    <table class="info-grid">
        <tr class="row">
            <td class="label">Company Name</td>
            <td class="value">{{ $client->company_name ?? 'N/A' }}</td>
            <td class="label label-right">Nature of Business</td>
            <td class="value value-right">{{ $client->nature_of_business ?? 'N/A' }}</td>
        </tr>
        <tr class="row">
            <td class="label">Constitution</td>
            <td class="value">{{ $client->constitution ?? 'N/A' }}</td>
            <td class="label label-right">Approx Annual Turnover</td>
            <td class="value value-right">{{ $client->approx_annual_turnover ? '₹ ' . number_format($client->approx_annual_turnover,2) : 'N/A' }}</td>
        </tr>
    </table>

    <!-- ============================================================
    6. COMPLIANCE NOTES
    ============================================================ -->
    <div class="section-title"><span class="num">06</span> Compliance Notes</div>
    @if(trim($client->compliance_notes ?? '') !== '')
        <div class="notes-box">{{ $client->compliance_notes }}</div>
    @else
        <div class="notes-box empty">No compliance notes recorded.</div>
    @endif

    <!-- ============================================================
    7. IDENTITY DETAILS
    ============================================================ -->
    <div class="section-title"><span class="num">07</span> Identity Details</div>
    <table class="info-grid">
        <tr class="row">
            <td class="label">Father's Name</td>
            <td class="value">{{ $client->father_name ?? 'N/A' }}</td>
            <td class="label label-right">Date of Birth / Incorporation</td>
            <td class="value value-right">{{ $client->dob ? \Carbon\Carbon::parse($client->dob)->format('d-m-Y') : 'N/A' }}</td>
        </tr>
        <tr class="row">
            <td class="label">Gender</td>
            <td class="value">{{ $client->gender ?? 'N/A' }}</td>
            <td class="label label-right">Residential Status</td>
            <td class="value value-right">{{ $client->residential_status ?? 'N/A' }}</td>
        </tr>
        <tr class="row">
            <td class="label">Client Type</td>
            <td class="value">{{ ucfirst($client->client_type ?? 'N/A') }}</td>
            <td class="label label-right">Source</td>
            <td class="value value-right">{{ $client->source ?? 'N/A' }}</td>
        </tr>
        <tr class="row">
            <td class="label">Preferred Contact</td>
            <td class="value">{{ $client->preferred_contact ?? 'N/A' }}</td>
            <td class="label label-right">Credit Allowed</td>
            <td class="value value-right">{{ $client->is_credit_allowed ? 'Yes' : 'No' }}</td>
        </tr>
        @if(!empty($client->aadhaar))
        <tr class="row">
            <td class="label">Aadhaar</td>
            <td class="value">{{ $client->aadhaar }}</td>
            <td class="label label-right">Registration Type</td>
            <td class="value value-right">{{ $client->registration_type ?? 'N/A' }}</td>
        </tr>
        @endif
    </table>

    <!-- ============================================================
    8. GST & INCOME TAX CASES
    ============================================================ -->
    @if($client->cases->count())
    <div class="section-title"><span class="num">08</span> GST &amp; Income Tax Cases</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:11%;">Case Type</th>
                <th style="width:10%;">Ref / DIN</th>
                <th style="width:8%;">FY</th>
                <th style="width:15%;">Authority</th>
                <th style="width:10%;">Status</th>
                <th style="width:10%;">Next Follow-up</th>
                <th style="width:10%;">Amount</th>
                <th style="width:26%;">Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($client->cases as $case)
                @php
                    $badgeClass = match($case->status) {
                        'Closed' => 'badge-success',
                        'Recovery' => 'badge-danger',
                        'Order Received', 'Reply Submitted' => 'badge-info',
                        'Notice Received' => 'badge-warning',
                        default => '',
                    };
                @endphp
                <tr>
                    <td>
                        {{ $case->case_type }}
                        @if($case->other_case_type)
                            <br><span style="font-size:7.5px;color:#64748b;">({{ $case->other_case_type }})</span>
                        @endif
                    </td>
                    <td>{{ $case->reference_notice_din ?? 'N/A' }}</td>
                    <td>{{ $case->financial_year ?? 'N/A' }}</td>
                    <td>{{ $case->issuing_authority_office ?? 'N/A' }}</td>
                    <td><span class="badge-status {{ $badgeClass }}">{{ $case->status ?? 'N/A' }}</span></td>
                    <td>{{ $case->next_follow_up_date ? \Carbon\Carbon::parse($case->next_follow_up_date)->format('d-m-Y') : 'N/A' }}</td>
                    <td>{{ $case->amount_involved ? '₹ ' . number_format($case->amount_involved,2) : 'N/A' }}</td>
                    <td>{{ $case->case_description ?? 'N/A' }}</td>
                </tr>
                @if($case->followUps->count())
                    <tr class="followup-row">
                        <td colspan="8">
                            <strong>Follow-ups:</strong>
                            @foreach($case->followUps as $fu)
                                <span class="followup-item">
                                    <span class="date">{{ \Carbon\Carbon::parse($fu->follow_up_date)->format('d-m-Y') }}</span>
                                    {{ $fu->note }}
                                    @if($fu->createdBy)
                                        <span style="font-style:italic;color:#94a3b8;">— {{ $fu->createdBy->name }}</span>
                                    @endif
                                </span>
                            @endforeach
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    @else
        <div class="no-data">No GST / Income Tax cases recorded.</div>
    @endif

    <!-- ============================================================
    9. COMPLIANCE HISTORY
    ============================================================ -->
    @if($client->compliances->count())
    <div class="section-title"><span class="num">09</span> Compliance History</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:20%;">Compliance Type</th>
                <th style="width:12%;">Period</th>
                <th style="width:14%;">Due Date</th>
                <th style="width:12%;">Status</th>
                <th style="width:42%;">Notes / Follow-ups</th>
            </tr>
        </thead>
        <tbody>
            @foreach($client->compliances as $compliance)
                @php
                    $compBadge = match($compliance->status) {
                        'completed' => 'badge-success',
                        'overdue'   => 'badge-danger',
                        default     => '',
                    };
                @endphp
                <tr>
                    <td>
                        {{ $compliance->compliance_type }}
                        @if($compliance->repeat_automatically)
                            <br><span style="font-size:7.5px;color:#1e3a5f;">(Repeat)</span>
                        @endif
                        @if($compliance->parentCompliance)
                            <br><span style="font-size:7.5px;color:#64748b;">Parent: {{ $compliance->parentCompliance->compliance_type }}</span>
                        @endif
                    </td>
                    <td>{{ $compliance->period ?? 'N/A' }}</td>
                    <td>{{ $compliance->due_date ? \Carbon\Carbon::parse($compliance->due_date)->format('d-m-Y') : 'N/A' }}</td>
                    <td><span class="badge-status {{ $compBadge }}">{{ ucfirst($compliance->status ?? 'Pending') }}</span></td>
                    <td>
                        @if($compliance->notes)
                            <div style="margin-bottom:2px;">{{ $compliance->notes }}</div>
                        @endif
                        @if($compliance->followUps->count())
                            <div style="margin-top:2px;">
                                <strong style="font-size:7.5px;color:#64748b;">Follow-ups:</strong>
                                @foreach($compliance->followUps as $fu)
                                    <div style="font-size:7.5px;padding:1px 0;">
                                        <span style="font-weight:600;color:#1e3a5f;">{{ \Carbon\Carbon::parse($fu->follow_up_date)->format('d-m-Y') }}</span>
                                        {{ $fu->note }}
                                        @if($fu->createdBy)
                                            <span style="font-style:italic;color:#94a3b8;">— {{ $fu->createdBy->name }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="no-data">No compliance records found.</div>
    @endif

    <!-- ============================================================
    10. TURNOVER – MONTHLY BREAKDOWN
    ============================================================ -->
    @php
        $turnovers = $client->turnovers;
        $years = $turnovers->pluck('financial_year')->unique()->sort()->reverse();
        $months = ['APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC','JAN','FEB','MAR'];
        $monthMap = [1=>'APR',2=>'MAY',3=>'JUN',4=>'JUL',5=>'AUG',6=>'SEP',7=>'OCT',8=>'NOV',9=>'DEC',10=>'JAN',11=>'FEB',12=>'MAR'];
    @endphp
    @if($years->count())
    <div class="section-title"><span class="num">10</span> Turnover – Year Wise</div>
    @foreach($years as $year)
        @php
            $yearData = $turnovers->where('financial_year', $year);
            $total = $yearData->sum('amount');
            $monthly = [];
            foreach($yearData as $item) {
                $monthly[$item->month] = $item->amount;
            }
        @endphp
        <table class="turnover-table">
            <thead>
                <tr>
                    @foreach($months as $m)
                        <th>{{ $m }}</th>
                    @endforeach
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($months as $idx => $m)
                        @php $monthNum = $idx + 1; @endphp
                        <td>{{ isset($monthly[$monthNum]) ? '₹ ' . number_format($monthly[$monthNum], 0) : '—' }}</td>
                    @endforeach
                    <td>₹ {{ number_format($total, 2) }}</td>
                </tr>
                <tr class="year-label">
                    <td colspan="13">
                        <strong>FY {{ $year }}</strong> &nbsp;|&nbsp; Total Turnover: ₹ {{ number_format($total, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
        @if(!$loop->last)
            <div style="height:6px;"></div>
        @endif
    @endforeach
    @endif

    <!-- ============================================================
    11. PORTAL CREDENTIALS
    ============================================================ -->
    @if($client->portalCredentials->count())
    <div class="section-title"><span class="num">11</span> Portal Credentials</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:25%;">Portal</th>
                <th style="width:30%;">Username</th>
                <th style="width:45%;">Notes</th>
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
    <div class="security-note">* Passwords are encrypted and not displayed for security.</div>
    @endif

    <!-- ============================================================
    12. CUSTOM FIELDS
    ============================================================ -->
    @if(isset($customFields) && $customFields->count())
    <div class="section-title"><span class="num">12</span> Custom Fields</div>
    <table class="info-grid">
        @foreach($customFields as $field)
        @php
            $valueObj = $client->customFieldValues->where('custom_field_id', $field->id)->first();
            $displayValue = $valueObj ? $valueObj->field_value : 'N/A';
            if (is_array($displayValue) || is_object($displayValue)) {
                $displayValue = json_encode($displayValue);
            }
        @endphp
        <tr class="row">
            <td class="label">{{ $field->field_label }}</td>
            <td class="value">{{ $displayValue }}</td>
            <td class="label label-right"></td>
            <td class="value value-right"></td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- ============================================================
    FOOTER
    ============================================================ -->
    <div class="report-footer">
        <div class="left">
            <span>TSE India — Chartered Accountants &amp; Advisory</span>
            &nbsp;|&nbsp;
            <span>Confidential Client Document</span>
        </div>
        <div class="right">
            <span>Generated: {{ now()->format('d-m-Y H:i') }}</span>
            &nbsp;|&nbsp;
            <span>Page 1</span>
        </div>
    </div>

</div>

</body>
</html>