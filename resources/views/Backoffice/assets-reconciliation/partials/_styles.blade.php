<style>
    /* ─── ROOT TOKENS ─── */
    :root {
        --navy: #0d1b2e;
        --navy-mid: #132238;
        --navy-light: #1a2f4a;
        --navy-border: #1e3a5f;
        --blue: #1565c0;
        --blue-light: #1e88e5;
        --sky: #e3f2fd;
        --slate: #546e7a;
        --slate-light: #78909c;
        --white: #ffffff;
        --off-white: #f4f7fc;
        --border: #d0d8e4;
        --border-light: #e6ecf3;
        --emerald: #00897b;
        --emerald-light: #e0f2f1;
        --red: #c62828;
        --red-light: #ffebee;
        --gold: #f9a825;
        --text-dark: #1a2332;
        --text-mid: #3a4a5c;
        --text-light: #607080;
        --font-mono: 'IBM Plex Mono', monospace;
        --font-sans: 'IBM Plex Sans', sans-serif;
        --font-display: 'Playfair Display', serif;
        --shadow: 0 2px 12px rgba(0,0,0,0.07);
        --shadow-hover: 0 8px 32px rgba(0,0,0,0.14);
        --radius: 8px;
        --transition: 0.25s cubic-bezier(0.4,0,0.2,1);
    }

    .paper {
        background: var(--white);
        border: 2px solid #4a6fa5;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        position: relative;
        max-width: 1600px;
        margin: 0 auto;
    }
    .paper-inner {
        position: relative;
        z-index: 2;
        padding: 0;
    }
    .wm {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%) rotate(-35deg);
        font-family: var(--font-display);
        font-size: 72px;
        font-weight: 700;
        color: rgba(180,195,215,0.10);
        pointer-events: none;
        z-index: 1;
        letter-spacing: 14px;
        text-transform: uppercase;
        user-select: none;
        white-space: nowrap;
    }
    .pgnum {
        position: absolute;
        bottom: 6px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 10.5px;
        color: var(--slate-light);
        font-family: var(--font-sans);
        z-index: 5;
        font-weight: 600;
    }

    /* ─── HEADER ROWS ─── */
    .hr {
        background: #1a3a6c;
        color: #fff;
        text-align: center;
        padding: 4px 6px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #2a5aa0;
        font-family: var(--font-sans);
    }
    .hr.light {
        background: #d6e4f7;
        color: #0d2a5e;
        font-size: 11px;
        font-weight: 600;
        border-bottom: 1px solid #4a6fa5;
    }
    .hr.title {
        font-size: 14px;
        letter-spacing: 2px;
        background: #1a3a6c;
        padding: 6px 6px;
        font-family: var(--font-display);
    }
    .hr.period {
        background: #d6e4f7;
        color: #0d2a5e;
        font-size: 10.5px;
        font-weight: 500;
        border-bottom: 2px solid #4a6fa5;
    }
    .hr-input {
        background: transparent;
        border: none;
        outline: none;
        color: #0d2a5e;
        font-family: var(--font-sans);
        font-size: 10.5px;
        font-weight: 600;
        text-align: center;
        width: 100%;
        cursor: text;
    }
    .hr-input:focus {
        background: #fffde7;
    }
    .hr-input.white {
        color: #fff;
    }
    .hr-input.white:focus {
        color: #0d2a5e;
    }

    /* ─── CARDS ─── */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        padding: 12px 14px;
        background: var(--off-white);
        border-bottom: 1px solid var(--border-light);
    }
    .stat-card {
        background: var(--white);
        border-radius: 6px;
        padding: 12px 14px;
        border: 1px solid var(--border-light);
        box-shadow: var(--shadow);
        transition: var(--transition);
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }
    .stat-card .label {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-mid);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .stat-card .value {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin-top: 2px;
        font-family: var(--font-mono);
    }
    .stat-card .sub {
        font-size: 10px;
        color: var(--text-mid);
    }

    /* ─── SECTION HEADERS ─── */
    .section-header {
        background: #eef3f8;
        padding: 8px 14px;
        border-bottom: 2px solid #4a6fa5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 6px;
    }
    .section-header h5 {
        font-family: var(--font-sans);
        font-weight: 700;
        color: #0d2a5e;
        font-size: 13px;
        margin: 0;
        letter-spacing: 0.3px;
    }
    .section-header .badge-type {
        font-size: 10px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 12px;
        background: #1a3a6c;
        color: #fff;
    }
    .section-tools {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .section-tools .search-box {
        display: flex;
        align-items: center;
        gap: 4px;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 12px;
    }
    .section-tools .search-box input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 12px;
        font-family: var(--font-sans);
        color: var(--text-dark);
        width: 120px;
        padding: 3px 0;
    }
    .section-tools .search-box input::placeholder {
        color: var(--text-mid);
    }
    .collapse-toggle {
        cursor: pointer;
        user-select: none;
        font-size: 12px;
        font-weight: 500;
        color: var(--text-mid);
        display: flex;
        align-items: center;
        gap: 4px;
        transition: var(--transition);
    }
    .collapse-toggle:hover {
        color: var(--blue);
    }
    .collapse-toggle .arrow {
        transition: var(--transition);
        display: inline-block;
        font-size: 10px;
    }
    .collapse-toggle .arrow.open {
        transform: rotate(90deg);
    }
    .section-body {
        transition: var(--transition);
        overflow: hidden;
    }
    .section-body.collapsed {
        max-height: 0 !important;
        padding-top: 0 !important;
        opacity: 0;
        margin: 0;
    }

    /* ─── TABLES ─── */
    .table-wrap {
        overflow: auto;
        padding: 0;
        position: relative;
        max-height: 460px;
    }
    .table-wrap::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    .table-wrap::-webkit-scrollbar-thumb {
        background: var(--blue);
        border-radius: 10px;
    }
    .recon-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        table-layout: fixed;
        min-width: 1300px;
    }
    .recon-table th,
    .recon-table td {
        border: 1px solid #4a6fa5;
        padding: 0;
        vertical-align: middle;
        text-align: center;
    }
    .recon-table thead th {
        background: #1a3a6c;
        color: #fff;
        font-size: 8.5px;
        font-weight: 600;
        letter-spacing: 0.3px;
        padding: 5px 2px;
        font-family: var(--font-sans);
        text-transform: uppercase;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .recon-table thead th .sub-label {
        font-weight: 400;
        font-size: 7px;
        opacity: 0.8;
        display: block;
        margin-top: 1px;
    }
    .recon-table thead th.sticky-col {
        position: sticky;
        left: 0;
        z-index: 11;
        background: #1a3a6c;
    }
    .recon-table tbody td {
        padding: 2px 2px;
        transition: background 0.12s;
    }
    .recon-table tbody tr:hover td {
        background: #eaf2fd !important;
    }
    .recon-table tbody tr:nth-child(even) td {
        background: #fafcff;
    }
    .recon-table tbody tr:nth-child(odd) td {
        background: #ffffff;
    }
    .recon-table tbody tr.total-row td {
        background: #1a3a6c !important;
        color: #fff;
        font-weight: 700;
        font-size: 11px;
        padding: 4px 2px;
        font-family: var(--font-mono);
        border-top: 2px solid #4a6fa5;
    }
    .recon-table tbody tr.total-row td:first-child {
        text-transform: uppercase;
        font-family: var(--font-sans);
        letter-spacing: 0.5px;
    }
    .recon-table .td-label {
        font-weight: 600;
        color: var(--text-dark);
        padding: 3px 4px;
        text-align: left;
        font-size: 10.5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .recon-table .td-label .idx {
        font-weight: 400;
        color: var(--text-mid);
        font-size: 9px;
        min-width: 16px;
    }
    .recon-table .td-amt {
        font-family: var(--font-mono);
        font-size: 10.5px;
        text-align: right;
        padding: 2px 4px;
        color: var(--text-dark);
        cursor: text;
        background: transparent;
        border: none;
        width: 100%;
        outline: none;
        transition: background 0.12s;
        min-height: 22px;
    }
    .recon-table .td-amt:hover {
        background: #fffbf0 !important;
    }
    .recon-table .td-amt:focus {
        background: #fffde7 !important;
        outline: 2px solid #f9a825;
        outline-offset: -2px;
        border-radius: 2px;
    }
    .recon-table .td-amt.total-cell {
        color: #fff;
        background: transparent !important;
        font-weight: 700;
    }
    .recon-table .td-amt.total-cell:hover {
        background: transparent !important;
    }
    .recon-table .td-amt.total-cell:focus {
        background: rgba(255,255,255,0.15) !important;
        color: #fff;
    }
    .recon-table .td-amt.negative {
        color: var(--red);
        background: var(--red-light) !important;
    }
    .recon-table .td-amt.readonly {
        background: #f4f7fc;
        cursor: default;
    }
    .recon-table .td-amt.readonly:hover {
        background: #f4f7fc !important;
    }
    .recon-table .td-amt.sticky-col {
        position: sticky;
        left: 0;
        background: inherit;
        z-index: 2;
    }
    .recon-table .td-amt.sticky-col:hover {
        background: inherit;
    }

    /* ─── BREAKUP BUTTON ─── */
    .btn-breakup {
        background: var(--blue);
        color: #fff;
        border: none;
        border-radius: 3px;
        padding: 2px 8px;
        font-size: 9px;
        font-weight: 600;
        cursor: pointer;
        font-family: var(--font-sans);
        transition: var(--transition);
        white-space: nowrap;
    }
    .btn-breakup:hover {
        background: var(--blue-light);
    }
    .btn-breakup.has-breakup {
        background: var(--emerald);
    }
    .btn-breakup.has-breakup:hover {
        background: #00766c;
    }

    /* ─── BREAKUP MODAL ─── */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-box {
        background: var(--white);
        border-radius: var(--radius);
        padding: 24px;
        max-width: 700px;
        width: 92%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: var(--shadow-hover);
    }
    .modal-box h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 6px;
    }
    .modal-box .close-modal {
        float: right;
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: var(--text-mid);
    }
    .modal-box .close-modal:hover {
        color: var(--text-dark);
    }
    .modal-box .modal-sub {
        font-size: 12px;
        color: var(--text-mid);
        margin-bottom: 12px;
    }
    .modal-box .modal-sub strong {
        color: var(--text-dark);
    }
    .modal-box .breakup-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }
    .modal-box .breakup-table th,
    .modal-box .breakup-table td {
        padding: 4px 6px;
        border-bottom: 1px solid var(--border-light);
        text-align: center;
    }
    .modal-box .breakup-table th {
        background: var(--off-white);
        font-weight: 600;
        color: var(--text-mid);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .modal-box .breakup-table .td-amt-sm {
        font-family: var(--font-mono);
        font-size: 10.5px;
        text-align: right;
        padding: 2px 4px;
        border: 1px solid var(--border);
        border-radius: 3px;
        width: 100%;
        outline: none;
        background: var(--white);
        color: var(--text-dark);
        min-height: 26px;
    }
    .modal-box .breakup-table .td-amt-sm:focus {
        border-color: var(--blue);
        outline: 2px solid rgba(21,101,192,0.3);
    }
    .modal-box .breakup-table .td-amt-sm.readonly {
        background: #f4f7fc;
        cursor: default;
    }
    .modal-box .breakup-table .td-amt-sm.readonly:focus {
        outline: none;
        border-color: var(--border);
    }
    .modal-box .breakup-table .td-rate-sm {
        font-family: var(--font-mono);
        font-size: 10.5px;
        text-align: center;
        padding: 2px 4px;
        border: 1px solid var(--border);
        border-radius: 3px;
        width: 100%;
        outline: none;
        background: var(--white);
        color: var(--text-dark);
        min-height: 26px;
    }
    .modal-box .breakup-table .td-rate-sm:focus {
        border-color: var(--blue);
        outline: 2px solid rgba(21,101,192,0.3);
    }
    .modal-box .breakup-table .td-rate-sm option {
        font-family: var(--font-mono);
    }
    .modal-box .breakup-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0 6px;
        font-size: 12px;
        border-top: 2px solid var(--border);
        margin-top: 6px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .modal-box .breakup-total .label {
        font-weight: 600;
        color: var(--text-mid);
    }
    .modal-box .breakup-total .value {
        font-weight: 700;
        font-family: var(--font-mono);
        color: var(--text-dark);
    }
    .modal-box .breakup-total .value.match {
        color: var(--emerald);
    }
    .modal-box .breakup-total .value.mismatch {
        color: var(--red);
    }
    .modal-box .breakup-validation {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
        margin-top: 4px;
    }
    .modal-box .breakup-validation.error {
        color: var(--red);
        background: var(--red-light);
    }
    .modal-box .breakup-validation.success {
        color: var(--emerald);
        background: var(--emerald-light);
    }
    .modal-box .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px solid var(--border-light);
        flex-wrap: wrap;
    }

    /* ─── FLOAT PANEL ─── */
    .float-panel {
        position: fixed;
        right: 24px;
        bottom: 40px;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-hover);
        padding: 12px 16px;
        min-width: 160px;
        max-width: 220px;
        z-index: 50;
        transition: var(--transition);
        backdrop-filter: blur(8px);
        background: rgba(255,255,255,0.92);
    }
    .float-panel:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }
    .float-panel .title {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--text-mid);
        border-bottom: 1px solid var(--border-light);
        padding-bottom: 6px;
        margin-bottom: 6px;
    }
    .float-panel .row {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        padding: 2px 0;
    }
    .float-panel .row .lbl {
        color: var(--text-mid);
    }
    .float-panel .row .val {
        font-weight: 600;
        font-family: var(--font-mono);
        color: var(--text-dark);
    }
    .float-panel .row.total {
        border-top: 2px solid var(--border);
        margin-top: 4px;
        padding-top: 6px;
        font-weight: 700;
        font-size: 12px;
    }
    .float-panel .row.total .val {
        color: var(--blue);
    }
    .float-panel .close-panel {
        position: absolute;
        top: 6px;
        right: 8px;
        background: none;
        border: none;
        color: var(--text-mid);
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        line-height: 1;
    }
    .float-panel .close-panel:hover {
        color: var(--text-dark);
    }

    /* ─── FOOTER ─── */
    .footer-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin-top: 0;
        border: 1px solid #4a6fa5;
        font-family: var(--font-sans);
    }
    .footer-table td {
        border: 1px solid #4a6fa5;
        padding: 5px 10px;
        vertical-align: top;
    }
    .ft-inner {
        width: 100%;
        border-collapse: collapse;
        background: transparent;
    }
    .ft-inner td {
        border: none;
        padding: 2px 4px;
        vertical-align: middle;
        background: transparent !important;
    }
    .ft-label-sm {
        font-weight: 600;
        color: #1a3a6c;
        font-size: 10px;
        white-space: nowrap;
        width: 1%;
        padding-right: 4px;
    }
    .ft-value-sm {
        font-weight: 500;
        color: #1a2332;
        font-size: 10px;
    }
    .ft-value-sm input {
        background: transparent;
        border: none;
        border-bottom: 1px solid #b0bec5;
        outline: none;
        font-family: var(--font-mono);
        font-size: 10px;
        color: #1a2332;
        width: 100%;
        padding: 0 2px;
    }
    .ft-value-sm input:focus {
        border-bottom-color: #1565c0;
        background: #fffde7;
    }
    .ft-col-left {
        width: 38%;
        background: #fafcff;
        border-right: 2px solid #4a6fa5;
    }
    .ft-col-right {
        width: 60%;
        background: #fafcff;
    }
    .ft-divider {
        width: 2%;
        background: #e8edf4;
        border-right: 2px solid #4a6fa5;
        border-left: 2px solid #4a6fa5;
    }
    .ft-sign-line {
        border-bottom: 1.5px solid #1a2332;
        min-height: 18px;
        width: 75%;
        display: inline-block;
    }
    .ft-sign-line input {
        background: transparent;
        border: none;
        border-bottom: 1.5px solid #1a2332;
        outline: none;
        font-family: var(--font-serif);
        font-size: 10px;
        color: #1a2332;
        width: 100%;
        padding: 0 2px;
        min-height: 20px;
        font-style: italic;
    }
    .ft-sign-line input:focus {
        border-bottom-color: #1565c0;
        background: #fffde7;
    }
    .ft-ca-sig {
        text-align: center;
        padding: 2px 0;
    }
    .ft-ca-sig img {
        max-height: 38px;
        max-width: 130px;
        border: 1px solid #e0e7ef;
        border-radius: 3px;
        padding: 2px;
        background: #fff;
        display: inline-block;
    }
    .ft-ca-sig .ft-sign-line {
        border-bottom: 1.5px solid #1a2332;
        min-height: 18px;
        width: 70%;
        margin: 0 auto;
        display: inline-block;
    }
    .ft-upload-btn {
        font-size: 8px;
        padding: 0 5px;
        border: 1px solid #b0bec5;
        border-radius: 3px;
        background: #f5f7fa;
        cursor: pointer;
        font-family: var(--font-sans);
        color: #455a64;
        transition: all 0.12s;
        display: inline-block;
        margin-top: 2px;
    }
    .ft-upload-btn:hover {
        background: #e8edf4;
        border-color: #78909c;
    }
    .ft-upload-btn i {
        font-size: 9px;
        margin-right: 2px;
    }
    .ft-ca-name {
        font-weight: 600;
        color: #0d2a5e;
        font-size: 10px;
        margin-top: 1px;
    }
    .ft-ca-mem {
        font-size: 9px;
        color: #455a64;
        font-family: var(--font-mono);
    }
    .ft-ack-label {
        font-weight: 600;
        color: #1a3a6c;
        font-size: 10px;
        white-space: nowrap;
        padding-right: 4px;
        background: transparent !important;
    }
    .ft-ack-val input {
        background: transparent;
        border: none;
        border-bottom: 1px solid #b0bec5;
        outline: none;
        font-family: var(--font-mono);
        font-size: 10px;
        color: #1a2332;
        width: 100%;
        padding: 0 2px;
    }
    .ft-ack-val input:focus {
        border-bottom-color: #1565c0;
        background: #fffde7;
    }
    .ft-assessee-line {
        margin-top: 4px;
    }
    .ft-assessee-line .ft-sign-line {
        width: 75%;
        display: inline-block;
    }

    /* ─── TOAST ─── */
.toast-container {
    position: fixed;
    top: 20px;  /* এই লাইনটি বদলান */
    right: 20px;
    z-index: 999;
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-width: 360px;
}
    .toast {
        padding: 10px 16px;
        border-radius: 6px;
        background: var(--white);
        color: var(--text-dark);
        font-size: 13px;
        box-shadow: var(--shadow-hover);
        display: flex;
        align-items: center;
        gap: 10px;
        border-left: 4px solid var(--blue);
        animation: fadeUp 0.3s ease;
    }
    .toast.success {
        border-left-color: var(--emerald);
    }
    .toast.warning {
        border-left-color: var(--gold);
    }
    .toast.error {
        border-left-color: var(--red);
    }
    .toast .close {
        background: none;
        border: none;
        color: var(--text-mid);
        cursor: pointer;
        font-size: 16px;
        padding: 0 4px;
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 1024px) {
        .float-panel {
            right: 12px;
            bottom: 20px;
            min-width: 150px;
            padding: 10px 12px;
        }
    }
    @media (max-width: 768px) {
        .paper {
            border-radius: 4px;
        }
        .recon-table {
            font-size: 9px;
            min-width: 1100px;
        }
        .recon-table thead th {
            font-size: 7px;
            padding: 3px 2px;
        }
        .recon-table .td-amt {
            font-size: 9px;
            padding: 2px 3px;
        }
        .hr.title {
            font-size: 12px;
        }
        .footer-table {
            font-size: 8.5px;
        }
        .footer-table td {
            padding: 3px 4px;
        }
        .ft-label-sm,
        .ft-ack-label {
            font-size: 8.5px;
        }
        .ft-value-sm input,
        .ft-ack-val input {
            font-size: 8.5px;
        }
        .section-header h5 {
            font-size: 11px;
        }
        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            padding: 8px 10px;
        }
        .stat-card .value {
            font-size: 15px;
        }
        .float-panel {
            right: 8px;
            bottom: 16px;
            min-width: 130px;
            padding: 8px 10px;
            font-size: 10px;
        }
        .float-panel .row {
            font-size: 10px;
        }
        .float-panel .row.total {
            font-size: 11px;
        }
        .modal-box {
            padding: 16px;
        }
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .fade-up {
        animation: fadeUp 0.35s ease both;
    }
    .delay-1 {
        animation-delay: 0.05s;
    }

    @media print {
        .float-panel,
        .modal-overlay,
        .btn-erp,
        .icon-btn,
        .collapse-toggle,
        .section-tools .search-box {
            display: none !important;
        }
        .paper {
            box-shadow: none;
            border: 1px solid #000;
            border-radius: 0;
        }
        .wm {
            color: rgba(0,0,0,0.04) !important;
        }
        .recon-table thead th {
            position: static;
            background: #1a3a6c !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .recon-table tbody tr.total-row td {
            background: #1a3a6c !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .recon-table tbody tr:nth-child(even) td {
            background: #fafcff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .recon-table tbody tr:nth-child(odd) td {
            background: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .footer-table td {
            padding: 3px 6px;
        }
        .ft-value-sm input {
            border-bottom: 1px solid #000;
        }
        .ft-sign-line {
            border-bottom: 1.5px solid #000;
        }
        .ft-sign-line input {
            border-bottom: 1.5px solid #000;
        }
        .ft-upload-btn {
            display: none !important;
        }
        .ft-ca-sig img {
            border: 1px solid #999;
        }
        .pgnum {
            display: block !important;
        }
        .cards-grid {
            display: none !important;
        }
    }

    /* ─── SCROLL FIX ──────────────────────────────────────────────── */
    .main-content {
        overflow-y: auto !important;
        height: 100vh;
    }

    .paper {
        overflow: visible !important;
    }

    .paper-inner {
        overflow: visible !important;
    }

    .table-wrap {
        overflow: auto !important;
        max-height: 460px;
    }

    .float-panel {
        bottom: 60px;
        right: 16px;
    }
</style>