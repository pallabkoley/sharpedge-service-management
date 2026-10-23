<div class="hr">IN THE BOOKS OF</div>
<div class="hr light">
    <input class="hr-input" id="companyName" value="{{ $computation->trade_name ?? 'M/S SHARMA TRADING COMPANY' }}" style="font-weight:700;font-size:12px;color:#0d2a5e;" />
</div>
<div class="hr" style="font-size:10px;font-weight:500;padding:3px 6px;background:#2a5aa0;display:flex;justify-content:center;gap:20px;flex-wrap:wrap;">
    <span>GSTIN : <input class="hr-input white" id="gstin" value="{{ $computation->client->gstin ?? '22AABCU1234D1Z5' }}" style="display:inline;width:140px;font-size:10px;font-weight:500;" /></span>
    <span>PAN : <input class="hr-input white" id="pan" value="{{ $computation->client->pan ?? 'AABCU1234D' }}" style="display:inline;width:100px;font-size:10px;font-weight:500;" /></span>
</div>
<div class="hr light">
    <input class="hr-input" id="reportTitle" value="CREDITORS RECONCILIATION STATEMENT" style="font-size:11px;font-weight:600;text-align:center;letter-spacing:1.5px;" />
</div>
<div class="hr title" id="reportTypeDisplay">CREDITORS RECONCILIATION – INCOME TAX</div>
<div class="hr period">
    FOR THE FINANCIAL YEAR ENDED &nbsp;
    <input class="hr-input" id="fyEnd" value="{{ $computation->financial_year ?? '31-MAR-2026' }}" style="display:inline;width:100px;font-size:10.5px;color:#0d2a5e;font-weight:700;" />
    &nbsp;|&nbsp; AY &nbsp;
    <input class="hr-input" id="ayVal" value="{{ $computation->assessment_year ?? '2026-27' }}" style="display:inline;width:70px;font-size:10.5px;color:#0d2a5e;font-weight:700;" />
</div>