{{-- Application Toolbar – Debit / Credit Note Register --}}
<div class="app-toolbar">
    {{-- Left: Breadcrumb --}}
    <div class="tb-bread">
        GST Backoffice &rsaquo; <b>Debit / Credit Note Register</b>
    </div>

    {{-- Center: Search --}}
    <div class="tb-search">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Search…" />
    </div>

    {{-- Right: Actions --}}
    <div class="tb-actions">
        <div class="icon-btn" title="Notifications">
            <i class="bi bi-bell-fill"></i>
            <span class="notif-dot"></span>
        </div>

        <div class="icon-btn" title="Help">
            <i class="bi bi-question-circle"></i>
        </div>

        <div class="tb-sep"></div>

        {{-- Save Button --}}
        <button class="btn btn-ghost" onclick="saveDraft()">
            <i class="bi bi-cloud-arrow-up"></i>
            <span>Save</span>
        </button>

        {{-- Print Button --}}
        <button class="btn btn-ghost" onclick="window.print()">
            <i class="bi bi-printer-fill"></i>
            <span>Print</span>
        </button>

        {{-- PDF Button (removed – function not implemented) --}}
        {{-- <button class="btn btn-ghost" onclick="exportPdf()">
            <i class="bi bi-file-pdf-fill"></i>
            <span>PDF</span>
        </button> --}}

        <div class="tb-sep"></div>

        {{-- Add Row Button --}}
        <button class="btn btn-blue" onclick="addRow()">
            <i class="bi bi-plus-circle"></i>
            <span>Add Row</span>
        </button>

        {{-- Finalize Button --}}
        <button class="btn btn-green" onclick="finalizeReport()">
            <i class="bi bi-check2-circle"></i>
            <span>Finalize</span>
        </button>

        <div class="tb-sep"></div>

        {{-- User Pill (optional) --}}
     
    </div>
</div>