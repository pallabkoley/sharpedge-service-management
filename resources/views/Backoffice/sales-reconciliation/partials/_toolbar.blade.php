{{-- Application Toolbar – appears below top navbar, above report header --}}
<div class="app-toolbar">
    {{-- Left: Breadcrumb --}}
    <div class="tb-bread">
        Financials &rsaquo; <b>Sales Reconciliation</b>
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

        <button class="btn btn-ghost" onclick="saveDraft()">
            <i class="bi bi-cloud-arrow-up"></i> <span>Save</span>
        </button>
        <button class="btn btn-ghost" onclick="window.print()">
            <i class="bi bi-printer-fill"></i> <span>Print</span>
        </button>
        <button class="btn btn-ghost" onclick="exportPdf()">
            <i class="bi bi-file-pdf-fill"></i> <span>PDF</span>
        </button>

        <div class="tb-sep"></div>

        <button class="btn btn-blue" onclick="addRow()">
            <i class="bi bi-plus-circle"></i> <span>Add Row</span>
        </button>
        <button class="btn btn-primary" onclick="submitReport()">
            <i class="bi bi-send-fill"></i> <span>Submit</span>
        </button>
        <button class="btn btn-green" onclick="finalizeReport()">
            <i class="bi bi-check2-circle"></i> <span>Finalize</span>
        </button>

        <div class="tb-sep"></div>

        <!--<div class="user-pill">-->
        <!--    <div class="user-avatar">RK</div>-->
        <!--    <span class="user-name">R. Kumar, CA</span>-->
        <!--    <i class="bi bi-chevron-down" style="font-size:10px;color:var(--slate-light);"></i>-->
        <!--</div>-->
    </div>
</div>