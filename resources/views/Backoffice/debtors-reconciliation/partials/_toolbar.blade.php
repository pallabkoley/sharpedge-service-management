<div class="dr-toolbar">
    <div class="dr-toolbar-left">
        <span class="dr-breadcrumb">Financials &rsaquo; Debtors Reconciliation</span>
    </div>
    <div class="dr-toolbar-right">
        <div class="dr-search">
            <i class="bi bi-search"></i>
            <input type="text" id="globalSearch" placeholder="Search debtors..." oninput="filterTable()" />
        </div>
        <button class="btn-erp btn-ghost" onclick="saveDraft()"><i class="bi bi-cloud-arrow-up"></i> Save</button>
        <button class="btn-erp btn-ghost" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print</button>
        <a href="{{ route('backoffice.computations.pdf', ['id' => $computation->id, 'type' => 'summary']) }}" target="_blank" class="btn-erp btn-ghost"><i class="bi bi-file-pdf-fill"></i> PDF</a>
        <button class="btn-erp btn-blue" onclick="addRow()"><i class="bi bi-plus-circle"></i> Add Row</button>
        <button class="btn-erp btn-green" onclick="finalizeReport()"><i class="bi bi-check2-circle"></i> Submit</button>
        <button class="btn-erp btn-green" onclick="finalizeReport()"><i class="bi bi-check2-circle"></i> Finalize</button>
    </div>
</div>