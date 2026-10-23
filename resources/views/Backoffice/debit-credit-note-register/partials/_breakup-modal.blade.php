{{-- ─── BREAKUP MODAL ─── --}}
<div class="modal fade modal-erp" id="breakupModal" tabindex="-1" aria-labelledby="breakupModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="breakupModalLabel"><i class="bi bi-diagram-3 me-2"></i>GST Breakup - <span id="breakupMonthLabel">Apr-25</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="breakupModalBody">
                {{-- Dynamic content --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-erp btn-ghost" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancel</button>
                <button type="button" class="btn-erp btn-blue" onclick="saveBreakup()"><i class="bi bi-check2-circle"></i> Save Breakup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-erp .modal-content {
        border-radius: var(--radius);
        border: 2px solid #4a6fa5;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
        font-family: var(--font-sans);
    }
    .modal-erp .modal-header {
        background: #1a3a6c;
        color: #fff;
        border-bottom: 1px solid #4a6fa5;
        padding: 12px 20px;
    }
    .modal-erp .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.7;
    }
    .modal-erp .modal-header .btn-close:hover { opacity: 1; }
    .modal-erp .modal-title {
        font-family: var(--font-display);
        font-size: 16px;
        letter-spacing: 0.5px;
        font-weight: 700;
    }
    .modal-erp .modal-body {
        padding: 16px 20px 20px;
        background: var(--off-white);
    }
    .modal-erp .modal-footer {
        background: #fafcff;
        border-top: 1px solid var(--border);
        padding: 10px 20px;
    }

    .breakup-target {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
        box-shadow: var(--shadow);
    }
    .breakup-target .label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-mid);
    }
    .breakup-target .value {
        font-family: var(--font-mono);
        font-size: 18px;
        font-weight: 700;
        color: var(--blue);
    }
    .breakup-target .badge-month {
        background: #1a3a6c;
        color: #fff;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .breakup-table-wrap {
        overflow-x: auto;
        margin: 8px 0 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        background: #fff;
    }
    .breakup-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        min-width: 400px;
    }
    .breakup-table th {
        background: #d6e4f7;
        color: #0d2a5e;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.3px;
        padding: 6px 4px;
        border: 1px solid #4a6fa5;
        text-align: center;
        text-transform: uppercase;
    }
    .breakup-table td {
        padding: 2px 3px;
        border: 1px solid #4a6fa5;
        vertical-align: middle;
        text-align: center;
        background: #fff;
    }
    .breakup-table .br-amt input,
    .breakup-table .br-rate select {
        border: none;
        background: transparent;
        outline: none;
        font-family: var(--font-mono);
        font-size: 11px;
        color: var(--text-dark);
        width: 100%;
        padding: 3px 4px;
        min-height: 24px;
        text-align: right;
    }
    .breakup-table .br-rate select { text-align: center; }
    .breakup-table .br-amt input:focus,
    .breakup-table .br-rate select:focus {
        background: #fffde7;
        outline: 2px solid #f9a825;
        outline-offset: -2px;
        border-radius: 2px;
    }
    .breakup-table .br-actions .del-btn {
        background: none;
        border: none;
        color: var(--red);
        cursor: pointer;
        font-size: 16px;
        opacity: 0.5;
        transition: all 0.15s;
        padding: 2px 6px;
        border-radius: 3px;
    }
    .breakup-table .br-actions .del-btn:hover {
        opacity: 1;
        background: var(--red-light);
    }
    .breakup-table .br-idx {
        font-weight: 600;
        color: var(--slate-light);
        font-size: 10px;
        font-family: var(--font-mono);
    }

    .breakup-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 10px 14px;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 6px;
        margin-top: 6px;
        box-shadow: var(--shadow);
    }
    .breakup-summary .sum-item {
        display: flex;
        align-items: baseline;
        gap: 6px;
        font-size: 12px;
    }
    .breakup-summary .sum-item .label {
        font-weight: 600;
        color: var(--text-mid);
    }
    .breakup-summary .sum-item .val {
        font-family: var(--font-mono);
        font-weight: 700;
        font-size: 14px;
    }
    .breakup-summary .sum-item .val.green { color: var(--emerald); }
    .breakup-summary .sum-item .val.red { color: var(--red); }
    .breakup-summary .status-badge {
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .breakup-summary .status-badge.success {
        background: var(--emerald-light);
        color: var(--emerald);
    }
    .breakup-summary .status-badge.warning {
        background: #fff3e0;
        color: #e65100;
    }

    .breakup-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 4px 0 8px;
        flex-wrap: wrap;
    }
    .breakup-toolbar .btn-sm-erp {
        font-size: 10.5px;
        padding: 3px 12px;
        border-radius: 4px;
        font-weight: 600;
        border: 1px solid var(--border);
        background: var(--white);
        color: var(--text-mid);
        cursor: pointer;
        transition: all 0.15s;
        font-family: var(--font-sans);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .breakup-toolbar .btn-sm-erp:hover { background: var(--border-light); }
    .breakup-toolbar .btn-sm-erp.primary {
        background: var(--blue);
        color: #fff;
        border-color: var(--blue);
    }
    .breakup-toolbar .btn-sm-erp.primary:hover { background: #1251a3; }
    .breakup-toolbar .btn-sm-erp i { font-size: 12px; }
</style>