@extends('Backoffice.computation.pdf.layouts.master')

@section('content')
    {{-- Summary Sections --}}
    @include('Backoffice.computation.pdf.sections.assessee-info')
    @include('Backoffice.computation.pdf.sections.bank-details')
    @include('Backoffice.computation.pdf.sections.barcode')
    @include('Backoffice.computation.pdf.sections.exempt-income')
    @include('Backoffice.computation.pdf.sections.salary-summary')
    @include('Backoffice.computation.pdf.sections.house-property-summary')
    @include('Backoffice.computation.pdf.sections.business-summary')
    @include('Backoffice.computation.pdf.sections.capital-gain-summary')
    @include('Backoffice.computation.pdf.sections.vda-summary')
    @include('Backoffice.computation.pdf.sections.other-source-summary')
    @include('Backoffice.computation.pdf.sections.deduction-summary')
    @include('Backoffice.computation.pdf.sections.gross-total')
    @include('Backoffice.computation.pdf.sections.taxable-income')
    @include('Backoffice.computation.pdf.sections.tax-calculation')
    @include('Backoffice.computation.pdf.sections.tax-paid-summary')

    {{-- Verification --}}
    <div class="signature-box clearfix">
        <div class="left">
            <strong>Prepared by:</strong><br>
            {{ $computation->verification->prepared_by_name ?? '_________________' }}<br>
            Membership No: {{ $computation->verification->prepared_membership_no ?? 'N/A' }}<br>
            Firm: {{ $computation->verification->prepared_firm_name ?? 'N/A' }}<br>
            UDIN: {{ $computation->verification->prepared_udin ?? 'N/A' }}
        </div>
        <div class="right">
            <strong>Verified by:</strong><br>
            {{ $computation->verification->verified_by_name ?? '_________________' }}<br>
            Place: {{ $computation->verification->verified_place ?? 'N/A' }}<br>
            Date: {{ $computation->verification->verified_date ?? 'N/A' }}
        </div>
        <div style="clear:both;"></div>
        <div style="margin-top:8px;">
            <strong>Assessee:</strong> {{ $computation->verification->assessee_name ?? 'N/A' }}<br>
            PAN: {{ $computation->verification->assessee_pan ?? 'N/A' }}<br>
            Place: {{ $computation->verification->assessee_place ?? 'N/A' }}<br>
            Date: {{ $computation->verification->assessee_date ?? 'N/A' }}
        </div>
    </div>

    {{-- ===== DETAILED SCHEDULES ===== --}}
    <div class="page-break"></div>
    @include('Backoffice.computation.pdf.schedules.salary')

    <div class="page-break"></div>
    @include('Backoffice.computation.pdf.schedules.house-property')

    <div class="page-break"></div>
    @include('Backoffice.computation.pdf.schedules.business')

    <div class="page-break"></div>
    @include('Backoffice.computation.pdf.schedules.capital-gain')

    <div class="page-break"></div>
    @include('Backoffice.computation.pdf.schedules.vda')

    <div class="page-break"></div>
    @include('Backoffice.computation.pdf.schedules.other-source')

    <div class="page-break"></div>
    @include('Backoffice.computation.pdf.schedules.deduction')

    <div class="page-break"></div>
    @include('Backoffice.computation.pdf.schedules.tax-paid')
@endsection