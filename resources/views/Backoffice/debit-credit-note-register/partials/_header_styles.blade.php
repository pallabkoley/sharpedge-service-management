<style>
    /* ─── All styles from the HTML demo ─── */
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
        --shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        --radius: 8px;
    }

    .paper {
        background: #fff;
        border: 2px solid #4a6fa5;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        position: relative;
        max-width: 1480px;
        margin: 0 auto;
    }
    .paper-inner { position: relative; z-index: 2; padding: 0; }
    .wm {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-35deg);
        font-family: var(--font-display);
        font-size: 72px;
        font-weight: 700;
        color: rgba(180, 195, 215, 0.10);
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
    .hr-input:focus { background: #fffde7; }
    .hr-input.white { color: #fff; }
    .hr-input.white:focus { color: #0d2a5e; }

    .nav-tabs-custom {
        border-bottom: 2px solid var(--border-light);
        padding: 0 14px;
        background: var(--off-white);
    }
    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 12px;
        color: var(--text-mid);
        border-radius: 0;
        transition: all 0.2s;
        font-family: var(--font-sans);
    }
    .nav-tabs-custom .nav-link:hover {
        color: var(--blue);
        background: transparent;
    }
    .nav-tabs-custom .nav-link.active {
        color: var(--blue);
        border-bottom-color: var(--blue);
        background: transparent;
    }
    .nav-tabs-custom .nav-link i { margin-right: 6px; }

    .table-wrap { overflow-x: auto; padding: 0; position: relative; }
    .recon-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11.5px;
        table-layout: fixed;
        min-width: 950px;
    }
    .recon-table th, .recon-table td {
        border: 1px solid #4a6fa5;
        padding: 0;
        vertical-align: middle;
        text-align: center;
    }
    .recon-table thead th {
        background: #1a3a6c;
        color: #fff;
        font-size: 9.5px;
        font-weight: 600;
        letter-spacing: 0.4px;
        padding: 6px 3px;
        font-family: var(--font-sans);
        text-transform: uppercase;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .recon-table thead th .sub-label {
        font-weight: 400;
        font-size: 8px;
        opacity: 0.8;
        display: block;
        margin-top: 1px;
        letter-spacing: 0.2px;
    }
    .recon-table tbody td { padding: 2px 3px; transition: background 0.12s; }
    .recon-table tbody tr:hover td { background: #eaf2fd !important; }
    .recon-table tbody tr:nth-child(even) td { background: #fafcff; }
    .recon-table tbody tr:nth-child(odd) td { background: #ffffff; }
    .recon-table tbody tr.month-row td { font-weight: 500; }
    .recon-table tbody tr.total-row td {
        background: #1a3a6c !important;
        color: #fff;
        font-weight: 700;
        font-size: 11.5px;
        padding: 5px 3px;
        font-family: var(--font-mono);
        border-top: 2px solid #4a6fa5;
    }
    .recon-table tbody tr.total-row td:first-child {
        text-transform: uppercase;
        font-family: var(--font-sans);
        letter-spacing: 0.5px;
    }
    .recon-table .td-month {
        font-weight: 600;
        color: var(--text-dark);
        padding: 4px 6px;
        text-align: left;
        font-size: 11px;
    }
    .recon-table .td-month input {
        border: none;
        background: transparent;
        outline: none;
        font-weight: 600;
        width: 100%;
        font-size: 11px;
        color: var(--text-dark);
        padding: 2px 4px;
        text-align: left;
    }
    .recon-table .td-month input:focus {
        background: #fffde7;
        outline: 2px solid #f9a825;
        outline-offset: -2px;
        border-radius: 2px;
    }
    .recon-table .td-amt {
        font-family: var(--font-mono);
        font-size: 11.5px;
        text-align: right;
        padding: 3px 6px;
        color: var(--text-dark);
        cursor: text;
        background: transparent;
        border: none;
        width: 100%;
        outline: none;
        transition: background 0.12s;
        min-height: 26px;
    }
    .recon-table .td-amt:hover { background: #fffbf0 !important; }
    .recon-table .td-amt:focus {
        background: #fffde7 !important;
        outline: 2px solid #f9a825;
        outline-offset: -2px;
        border-radius: 2px;
    }
    .recon-table .td-amt.total {
        color: #fff;
        background: transparent !important;
        font-weight: 700;
    }
    .recon-table .td-amt.total:hover { background: transparent !important; }
    .recon-table .td-amt.total:focus {
        background: #fffde7 !important;
        color: var(--text-dark);
    }
    .recon-table .td-remark { padding: 3px 4px; }
    .recon-table .td-remark input {
        border: none;
        background: transparent;
        outline: none;
        font-family: var(--font-sans);
        font-size: 11px;
        color: var(--text-dark);
        width: 100%;
        padding: 2px 4px;
        text-align: left;
        min-height: 26px;
    }
    .recon-table .td-remark input:focus {
        background: #fffde7;
        outline: 2px solid #f9a825;
        outline-offset: -2px;
        border-radius: 2px;
    }
    .recon-table .td-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2px;
        padding: 2px 4px;
        flex-wrap: wrap;
    }
    .recon-table .td-actions .action-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 14px;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--slate-light);
        opacity: 0.6;
    }
    .recon-table .td-actions .action-btn:hover {
        opacity: 1;
        background: var(--border-light);
    }
    .recon-table .td-actions .action-btn.del-btn { color: var(--red); }
    .recon-table .td-actions .action-btn.del-btn:hover { background: var(--red-light); }
    .recon-table .td-actions .action-btn.del-btn.disabled {
        opacity: 0.2;
        cursor: not-allowed;
        pointer-events: none;
    }
    .recon-table .td-actions .action-btn i { font-size: 14px; }

    .recon-table .td-breakup { padding: 2px 3px; min-width: 80px; }
    .recon-table .td-breakup .btn-breakup {
        background: var(--blue);
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 2px 10px;
        font-size: 10.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        font-family: var(--font-sans);
        white-space: nowrap;
    }
    .recon-table .td-breakup .btn-breakup:hover { background: #1251a3; }
    .recon-table .td-breakup .btn-breakup i { margin-right: 4px; }

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
    .ft-assessee-line { margin-top: 4px; }
    .ft-assessee-line .ft-sign-line {
        width: 75%;
        display: inline-block;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 14px;
        background: #eaf2fd;
        border-bottom: 1px solid #4a6fa5;
    }
    .section-header h5 {
        font-size: 11px;
        font-weight: 700;
        color: #0d2a5e;
        margin: 0;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }
    .section-header .badge-type {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 12px;
        border-radius: 10px;
        background: #1a3a6c;
        color: #fff;
    }

    .toast-container {
        position: fixed;
        bottom: 40px;
        right: 16px;
        z-index: 99999;
    }
    .toast-erp {
        background: #00695c;
        color: #fff;
        padding: 9px 16px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 7px;
        font-family: var(--font-sans);
        animation: fadeUp 0.25s ease;
        max-width: 360px;
        margin-top: 6px;
    }
    .toast-erp.info { background: #1565c0; }
    .toast-erp.warning { background: #e65100; }
    .toast-erp.error { background: #c62828; }
    .toast-erp.success { background: #00695c; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ─── Table Enhancements ─── */
    .recon-table {
        border-collapse: collapse;
        width: 100%;
        font-size: 12px;
    }
    .recon-table thead th {
        background: #1a3a6c;
        color: #fff;
        font-weight: 600;
        padding: 10px 6px;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.3px;
        border: 1px solid #2a5aa0;
        vertical-align: middle;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .recon-table thead th .sub-label {
        font-weight: 400;
        font-size: 8px;
        opacity: 0.8;
        display: block;
        margin-top: 1px;
    }
    .recon-table tbody td {
        padding: 6px 4px;
        vertical-align: middle;
        border: 1px solid #4a6fa5;
    }
    .recon-table tbody tr:nth-child(even) td {
        background: #fafcff;
    }
    .recon-table tbody tr:nth-child(odd) td {
        background: #ffffff;
    }
    .recon-table tbody tr:hover td {
        background: #eaf2fd !important;
    }
    .recon-table .td-month {
        font-weight: 600;
        color: #1a2332;
        text-align: left;
        font-size: 11.5px;
        padding-left: 8px;
    }
    .recon-table .td-amt {
        font-family: var(--font-mono);
        font-size: 12px;
        text-align: right;
        padding: 4px 8px;
        background: transparent;
        border: none;
        width: 100%;
        outline: none;
        min-height: 30px;
    }
    .recon-table .td-amt:focus {
        background: #fffde7;
        outline: 2px solid #f9a825;
        border-radius: 2px;
    }
    .recon-table .td-amt.total {
        color: #fff;
        font-weight: 700;
        background: transparent !important;
    }
    .recon-table .td-remark input {
        border: none;
        background: transparent;
        outline: none;
        width: 100%;
        padding: 4px 6px;
        font-size: 11px;
        min-height: 30px;
    }
    .recon-table .td-remark input:focus {
        background: #fffde7;
        outline: 2px solid #f9a825;
        border-radius: 2px;
    }
    .recon-table .td-breakup .btn-breakup {
        background: var(--blue);
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 3px 10px;
        font-size: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .recon-table .td-breakup .btn-breakup:hover {
        background: #1251a3;
    }
    .recon-table .td-breakup .btn-breakup i {
        margin-right: 4px;
    }
    .recon-table .td-actions .action-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 16px;
        color: var(--slate-light);
        opacity: 0.5;
        transition: all 0.15s;
    }
    .recon-table .td-actions .action-btn:hover {
        opacity: 1;
        background: var(--border-light);
    }
    .recon-table .td-actions .action-btn.del-btn {
        color: var(--red);
    }
    .recon-table .td-actions .action-btn.del-btn:hover {
        background: var(--red-light);
    }
    .recon-table .td-actions .action-btn.del-btn.disabled {
        opacity: 0.2;
        cursor: not-allowed;
        pointer-events: none;
    }
    .recon-table tbody tr.total-row td {
        background: #1a3a6c !important;
        color: #fff;
        font-weight: 700;
        font-size: 12px;
        padding: 8px 4px;
        border-top: 2px solid #4a6fa5;
        border-bottom: 2px solid #4a6fa5;
    }
    .recon-table tbody tr.total-row td:first-child {
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ─── Scroll Fix ─── */
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
        overflow-x: auto;
        overflow-y: visible;
    }
    
    /* ─── Application Toolbar (identical to Purchase Reconciliation) ─── */
.app-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 16px;
    background: #ffffff;
    border-bottom: 1px solid #e6ecf3;
    gap: 12px;
    flex-wrap: wrap;
    min-height: 48px;
}
.tb-bread {
    font-size: 12px;
    color: #78909c;
    flex-shrink: 0;
}
.tb-bread b {
    color: #1a2332;
    font-weight: 600;
}
.tb-search {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f4f7fc;
    border: 1px solid #d0d8e4;
    border-radius: 6px;
    padding: 4px 10px;
    width: 200px;
    flex-shrink: 0;
}
.tb-search input {
    border: none;
    background: none;
    outline: none;
    font-size: 12px;
    color: #1a2332;
    width: 100%;
}
.tb-search input::placeholder {
    color: #78909c;
    font-weight: 400;
}
.tb-search i {
    color: #78909c;
    font-size: 14px;
}
.tb-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.tb-sep {
    width: 1px;
    height: 24px;
    background: #d0d8e4;
    margin: 0 4px;
}
.btn-ghost {
    background: #f4f7fc;
    border: 1px solid #d0d8e4;
    color: #3a4a5c;
    padding: 4px 10px;
    border-radius: 5px;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-family: 'IBM Plex Sans', sans-serif;
    white-space: nowrap;
}
.btn-ghost:hover {
    background: #e6ecf3;
    border-color: #b8c4d4;
}
.btn-ghost i {
    font-size: 13px;
}
.btn-blue {
    background: #1565c0;
    color: #fff;
    border: 1px solid #1565c0;
    padding: 4px 12px;
    border-radius: 5px;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-family: 'IBM Plex Sans', sans-serif;
    white-space: nowrap;
}
.btn-blue:hover {
    background: #1251a3;
    border-color: #1251a3;
}
.btn-blue i {
    font-size: 13px;
}
.btn-green {
    background: #00897b;
    color: #fff;
    border: 1px solid #00897b;
    padding: 4px 12px;
    border-radius: 5px;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-family: 'IBM Plex Sans', sans-serif;
    white-space: nowrap;
}
.btn-green:hover {
    background: #00766c;
    border-color: #00766c;
}
.btn-green i {
    font-size: 13px;
}
.icon-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 1px solid #d0d8e4;
    background: #f4f7fc;
    color: #546e7a;
    font-size: 15px;
    transition: all 0.15s;
    position: relative;
}
.icon-btn:hover {
    background: #e6ecf3;
    color: #1a2332;
}
.notif-dot {
    position: absolute;
    top: 3px;
    right: 3px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #c62828;
    border: 1.5px solid #fff;
}
.user-pill {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 3px 8px 3px 3px;
    border-radius: 20px;
    background: #f4f7fc;
    border: 1px solid #d0d8e4;
    cursor: pointer;
    transition: all 0.15s;
}
.user-pill:hover {
    background: #e6ecf3;
}
.user-avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1565c0, #00897b);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    color: #fff;
}
.user-name {
    font-size: 11.5px;
    font-weight: 600;
    color: #1a2332;
}
</style>