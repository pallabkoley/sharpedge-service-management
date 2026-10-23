<div class="ta-toolbar" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;padding:8px 14px;background:#f8fafc;border-bottom:1px solid #d0d8e4;">
    <div class="ta-toolbar-left">
        <span style="font-size:13px;font-weight:500;color:#1a2332;">Financials &rsaquo; Trading Account</span>
    </div>
    <div class="ta-toolbar-right" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
        <button class="btn-erp btn-ghost" id="saveBtn" onclick="saveDraft()"><i class="bi bi-cloud-arrow-up"></i> Save</button>
        <button class="btn-erp btn-ghost" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print</button>
        <a href="{{ route('backoffice.computations.pdf', ['id' => $computation->id, 'type' => 'summary']) }}" target="_blank" class="btn-erp btn-ghost"><i class="bi bi-file-pdf-fill"></i> PDF</a>
        <button class="btn-erp btn-blue" onclick="addRowDebit()"><i class="bi bi-plus-circle"></i> Add Row (Dr)</button>
        <button class="btn-erp btn-orange" onclick="addRowCredit()"><i class="bi bi-plus-circle"></i> Add Row (Cr)</button>
        <button class="btn-erp btn-green" onclick="finalizeReport()"><i class="bi bi-check2-circle"></i> Finalize</button>
    </div>
</div>