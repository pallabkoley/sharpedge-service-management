<div class="sheet-header" style="padding:8px 15px;">
    <div class="firm-name">M/S SHARMA & ASSOCIATES — CHARTERED ACCOUNTANTS</div>
    <div class="main-title">COMPUTATION OF TOTAL INCOME AND TAX PAYABLE</div>
    <div class="sub-title">UNDER THE INCOME TAX ACT, 1961</div>
    <div class="header-meta">
        <span><strong>Assessment Year :</strong> {{ $computation->assessment_year ?? '2025-26' }}</span>
        <span><strong>Financial Year :</strong> {{ $computation->financial_year ?? '2024-25' }}</span>
        <span><strong>Previous Year :</strong> {{ $computation->previous_year ?? '2024-25' }}</span>
        <span><strong>Computation Date :</strong> <span id="comp-date">{{ date('d/m/Y') }}</span></span>
    </div>
</div>