@php
    // ── Try to load branding from database ──
    $branding = null;
    $logoUrl = null;
    $firmName = 'THE SHARPEDGE INDIA';
    $firmTagline = 'Chartered Accountants & Tax Consultants';
    $address = '';
    $phone = '';
    $email = '';
    $gstin = '';
    $pan = '';
    $website = '';

    if (class_exists(\App\Models\Branding::class)) {
        try {
            $branding = \App\Models\Branding::first();
        } catch (\Exception $e) {
            $branding = null;
        }
    }

    if ($branding) {
        $logoUrl = $branding->logo_path ? Storage::url($branding->logo_path) : null;
        $firmName = $branding->firm_name ?? $firmName;
        $firmTagline = $branding->firm_tagline ?? $firmTagline;
        
        // Build full address
        $addressParts = array_filter([
            $branding->address,
            $branding->city,
            $branding->state,
            $branding->pincode,
        ]);
        $address = implode(', ', $addressParts);
        
        $phone = $branding->phone ?? '';
        $email = $branding->email ?? '';
        $gstin = $branding->gstin ?? '';
        $pan = $branding->pan ?? '';
        $website = $branding->website ?? '';
    }
@endphp

<div class="pdf-header">
    <table class="header-table">
        <tr>
            {{-- LEFT: Logo + Firm Details --}}
            <td class="header-left">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="logo">
                @endif
                <div class="firm-details">
                    <div class="firm-name">{{ $firmName }}</div>
                    <div class="firm-tagline">{{ $firmTagline }}</div>
                    @if($address)
                        <div class="firm-address">{{ $address }}</div>
                    @endif
                    <div class="firm-contact">
                        @if($phone)<span>📞 {{ $phone }}</span>@endif
                        @if($email)<span>✉ {{ $email }}</span>@endif
                        @if($website)<span>🌐 {{ $website }}</span>@endif
                        @if($gstin)<span>GSTIN: {{ $gstin }}</span>@endif
                        @if($pan)<span>PAN: {{ $pan }}</span>@endif
                    </div>
                </div>
            </td>

            {{-- RIGHT: Document Title & Meta --}}
            <td class="header-right">
                <div class="doc-title">COMPUTATION OF TOTAL INCOME &amp; TAX PAYABLE</div>
                <div class="doc-meta">
                    <table class="meta-table">
                        <tr><td>Assessment Year</td><td>: {{ $computation->assessment_year ?? 'N/A' }}</td></tr>
                        <tr><td>Financial Year</td><td>: {{ $computation->financial_year ?? 'N/A' }}</td></tr>
                        <tr><td>Computation Date</td><td>: {{ $computation->computation_date ?? now()->format('d-m-Y') }}</td></tr>
                        <tr><td>PAN</td><td>: {{ $computation->client->pan ?? 'N/A' }}</td></tr>
                        <tr><td>Return Type</td><td>: {{ ucfirst($computation->return_type ?? 'Original') }}</td></tr>
                        <tr><td>Status</td><td>: {{ ucfirst($computation->computation_status ?? 'Draft') }}</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    <div class="header-divider"></div>
</div>