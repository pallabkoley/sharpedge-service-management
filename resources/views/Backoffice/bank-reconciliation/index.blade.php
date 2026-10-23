@extends('layouts.Backoffice')

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
    <style>
        .br-page {
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
            --transition: 0.2s ease;
            --radius-sm: 6px;
            --text-muted: #64748b;
            --accent: var(--blue-light);
            --font-serif: Georgia, 'Times New Roman', serif;
        }

        .br-page .paper {
            background: var(--white);
            border: 2px solid #4a6fa5;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            position: relative;
            max-width: 1480px;
            margin: 0 auto;
        }
        .br-page .paper-inner { position: relative; z-index: 2; padding: 0; }
        .br-page .wm {
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
        .br-page .pgnum {
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
        .br-page .hr {
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
        .br-page .hr.light {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 11px;
            font-weight: 600;
            border-bottom: 1px solid #4a6fa5;
        }
        .br-page .hr.title {
            font-size: 14px;
            letter-spacing: 2px;
            background: #1a3a6c;
            padding: 6px 6px;
            font-family: var(--font-display);
        }
        .br-page .hr.period {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 10.5px;
            font-weight: 500;
            border-bottom: 2px solid #4a6fa5;
        }
        .br-page .hr-input {
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
        .br-page .hr-input:focus { background: #fffde7; }
        .br-page .hr-input.white { color: #fff; }
        .br-page .hr-input.white:focus { color: #0d2a5e; }

        /* ─── COMPACT SUMMARY STRIP ─── */
        .br-page .summary-strip {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px 28px;
            padding: 8px 18px;
            background: var(--off-white);
            border-bottom: 1px solid var(--border-light);
            font-size: 10.5px;
        }
        .br-page .summary-strip .item {
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .br-page .summary-strip .item .lbl {
            font-weight: 600;
            color: var(--text-mid);
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .br-page .summary-strip .item .val {
            font-family: var(--font-mono);
            font-weight: 700;
            color: var(--text-dark);
            font-size: 12px;
            min-width: 72px;
            text-align: right;
        }
        .br-page .summary-strip .item .val.positive { color: var(--emerald); }
        .br-page .summary-strip .item .val.negative { color: var(--red); }
        .br-page .summary-strip .item .val.zero { color: var(--text-mid); }
        .br-page .summary-strip .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 10.5px;
            font-weight: 700;
            font-family: var(--font-sans);
        }
        .br-page .summary-strip .status-badge.balanced {
            background: var(--emerald-light);
            color: var(--emerald);
        }
        .br-page .summary-strip .status-badge.not-balanced {
            background: var(--red-light);
            color: var(--red);
        }

        /* ─── TWO-COLUMN BODY ─── */
        .br-page .brs-body {
            display: flex;
            gap: 0;
            border-bottom: 1px solid #4a6fa5;
        }
        .br-page .brs-left {
            flex: 0 0 42%;
            border-right: 2px solid #4a6fa5;
            padding: 12px 16px;
            background: #fafcff;
        }
        .br-page .brs-right {
            flex: 1;
            padding: 12px 16px;
            background: #fafcff;
            overflow-x: auto;
        }

        /* ─── LEFT COLUMN ─── */
        .br-page .field-row {
            display: flex;
            align-items: center;
            padding: 3px 0;
            border-bottom: 1px solid var(--border-light);
        }
        .br-page .field-row .lbl {
            font-size: 11px;
            font-weight: 500;
            color: var(--text-dark);
            width: 180px;
            flex-shrink: 0;
        }
        .br-page .field-row .lbl.indent {
            padding-left: 20px;
            font-weight: 400;
            color: var(--text-mid);
        }
        .br-page .field-row .lbl.total {
            font-weight: 700;
            color: var(--blue);
        }
        .br-page .field-row .fld { flex: 1; }
        .br-page .field-row .fld input,
        .br-page .field-row .fld select {
            width: 100%;
            padding: 3px 8px;
            border: 1px solid var(--border);
            border-radius: 3px;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-dark);
            background: var(--white);
            outline: none;
            transition: var(--transition);
            text-align: right;
        }
        .br-page .field-row .fld input:focus,
        .br-page .field-row .fld select:focus {
            border-color: var(--blue);
            outline: 2px solid rgba(21,101,192,0.15);
        }
        .br-page .field-row .fld input.readonly {
            background: #f4f7fc;
            cursor: default;
            color: var(--text-mid);
        }
        .br-page .field-row .fld input.readonly:focus {
            outline: none;
            border-color: var(--border);
        }
        .br-page .field-row .fld input.total-value {
            font-weight: 700;
            color: var(--blue);
        }
        .br-page .field-row .fld input.variance-zero {
            color: var(--emerald);
            font-weight: 700;
        }
        .br-page .field-row .fld input.variance-not-zero {
            color: var(--red);
            font-weight: 700;
        }
        .br-page .field-row .fld .status-badge {
            display: inline-block;
            padding: 2px 14px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            min-width: 110px;
            font-family: var(--font-sans);
        }
        .br-page .field-row .fld .status-badge.balanced {
            background: var(--emerald-light);
            color: var(--emerald);
        }
        .br-page .field-row .fld .status-badge.not-balanced {
            background: var(--red-light);
            color: var(--red);
        }

        /* ─── RIGHT COLUMN TABLES ─── */
        .br-page .brs-table-wrap {
            margin-bottom: 16px;
        }
        .br-page .brs-table-wrap:last-child { margin-bottom: 0; }
        .br-page .brs-table-wrap .tbl-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 3px 4px 3px 8px;
            border-bottom: 2px solid #4a6fa5;
            background: #eef3f8;
        }
        .br-page .brs-table-wrap .tbl-header .tbl-title {
            font-size: 10.5px;
            font-weight: 700;
            color: #0d2a5e;
            letter-spacing: 0.3px;
        }
        .br-page .brs-table-wrap .tbl-header .tbl-total {
            font-size: 10px;
            font-weight: 600;
            color: var(--blue);
            font-family: var(--font-mono);
        }
        .br-page .brs-table-wrap .tbl-header .tbl-actions {
            display: flex;
            gap: 4px;
        }
        .br-page .brs-table-wrap .tbl-header .tbl-actions button {
            font-size: 8.5px;
            padding: 1px 8px;
            border: 1px solid var(--border);
            border-radius: 3px;
            background: var(--white);
            cursor: pointer;
            color: var(--text-mid);
            transition: var(--transition);
            font-family: var(--font-sans);
            font-weight: 500;
        }
        .br-page .brs-table-wrap .tbl-header .tbl-actions button:hover {
            background: var(--border-light);
            color: var(--blue);
            border-color: var(--blue-light);
        }
        .br-page .brs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            table-layout: fixed;
        }
        .br-page .brs-table th,
        .br-page .brs-table td {
            border: 1px solid #d0d8e4;
            padding: 2px 4px;
            vertical-align: middle;
        }
        .br-page .brs-table thead th {
            background: #1a3a6c;
            color: #fff;
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 0.3px;
            padding: 4px 4px;
            font-family: var(--font-sans);
            text-transform: uppercase;
            position: sticky;
            top: 0;
            z-index: 5;
        }
        .br-page .brs-table thead th:first-child { min-width: 30px; }
        .br-page .brs-table tbody tr:nth-child(even) td { background: #fafcff; }
        .br-page .brs-table tbody tr:nth-child(odd) td { background: #ffffff; }
        .br-page .brs-table tbody tr:hover td { background: #eaf2fd !important; }
        .br-page .brs-table tbody tr.total-row td {
            background: #1a3a6c !important;
            color: #fff;
            font-weight: 700;
            font-size: 9.5px;
            padding: 3px 4px;
            font-family: var(--font-mono);
            border-top: 2px solid #4a6fa5;
        }
        .br-page .brs-table .td-amt {
            font-family: var(--font-mono);
            font-size: 9.5px;
            text-align: right;
            padding: 2px 4px;
            color: var(--text-dark);
            cursor: text;
            background: transparent;
            border: none;
            width: 100%;
            outline: none;
            min-height: 20px;
        }
        .br-page .brs-table .td-amt:hover { background: #fffbf0 !important; }
        .br-page .brs-table .td-amt:focus {
            background: #fffde7 !important;
            outline: 2px solid #f9a825;
            outline-offset: -2px;
            border-radius: 2px;
        }
        .br-page .brs-table .td-amt.total-cell {
            color: #fff;
            background: transparent !important;
            font-weight: 700;
        }
        .br-page .brs-table .td-amt.total-cell:hover { background: transparent !important; }
        .br-page .brs-table .td-date {
            font-family: var(--font-mono);
            font-size: 8.5px;
            text-align: center;
            padding: 2px 4px;
            color: var(--text-dark);
            cursor: text;
            background: transparent;
            border: none;
            width: 100%;
            outline: none;
            min-height: 20px;
        }
        .br-page .brs-table .td-date:hover { background: #fffbf0 !important; }
        .br-page .brs-table .td-date:focus {
            background: #fffde7 !important;
            outline: 2px solid #f9a825;
            outline-offset: -2px;
            border-radius: 2px;
        }
        .br-page .brs-table .td-text {
            font-family: var(--font-sans);
            font-size: 8.5px;
            text-align: left;
            padding: 2px 4px;
            color: var(--text-dark);
            cursor: text;
            background: transparent;
            border: none;
            width: 100%;
            outline: none;
            min-height: 20px;
        }
        .br-page .brs-table .td-text:hover { background: #fffbf0 !important; }
        .br-page .brs-table .td-text:focus {
            background: #fffde7 !important;
            outline: 2px solid #f9a825;
            outline-offset: -2px;
            border-radius: 2px;
        }
        .br-page .brs-table .td-select {
            font-family: var(--font-sans);
            font-size: 8.5px;
            padding: 1px 2px;
            border: 1px solid var(--border);
            border-radius: 2px;
            background: var(--white);
            color: var(--text-dark);
            width: 100%;
            outline: none;
            min-height: 20px;
            cursor: pointer;
        }
        .br-page .brs-table .td-select:focus {
            border-color: var(--blue);
            outline: 2px solid rgba(21,101,192,0.15);
        }
        .br-page .brs-table .td-actions {
            display: flex;
            gap: 3px;
            justify-content: center;
            align-items: center;
        }
        .br-page .brs-table .td-actions button {
            width: 22px;
            height: 22px;
            padding: 0;
            border: 1px solid var(--border);
            border-radius: 3px;
            background: var(--white);
            cursor: pointer;
            color: var(--text-mid);
            transition: var(--transition);
            font-family: var(--font-sans);
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .br-page .brs-table .td-actions button:hover {
            background: var(--border-light);
            color: var(--blue);
            border-color: var(--blue-light);
        }
        .br-page .brs-table .td-actions button.del { color: var(--red); }
        .br-page .brs-table .td-actions button.del:hover {
            background: var(--red-light);
            border-color: var(--red);
            color: var(--red);
        }

        .br-page .brs-table-cheques th:first-child { width: 30px; }
        .br-page .brs-table-cheques th:nth-child(2) { width: 80px; }
        .br-page .brs-table-cheques th:nth-child(3) { width: 80px; }
        .br-page .brs-table-cheques th:nth-child(4) { width: 85px; }
        .br-page .brs-table-cheques th:nth-child(5) { width: 50px; }
        .br-page .brs-table-deposits th:first-child { width: 30px; }
        .br-page .brs-table-deposits th:nth-child(2) { width: 80px; }
        .br-page .brs-table-deposits th:nth-child(3) { width: 85px; }
        .br-page .brs-table-deposits th:nth-child(4) { width: 50px; }
        .br-page .brs-table-adjustments th:first-child { width: 30px; }
        .br-page .brs-table-adjustments th:nth-child(2) { width: 80px; }
        .br-page .brs-table-adjustments th:nth-child(3) { width: 100px; }
        .br-page .brs-table-adjustments th:nth-child(4) { width: 85px; }
        .br-page .brs-table-adjustments th:nth-child(5) { width: 50px; }

        /* ─── FOOTER ─── */
        .br-page .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-top: 0;
            border: 1px solid #4a6fa5;
            font-family: var(--font-sans);
        }
        .br-page .footer-table td {
            border: 1px solid #4a6fa5;
            padding: 4px 10px;
            vertical-align: top;
        }
        .br-page .ft-inner {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }
        .br-page .ft-inner td {
            border: none;
            padding: 2px 6px;
            vertical-align: middle;
            background: transparent !important;
        }
        .br-page .ft-label-sm {
            font-weight: 600;
            color: #1a3a6c;
            font-size: 9px;
            white-space: nowrap;
            width: 1%;
            padding-right: 6px;
        }
        .br-page .ft-value-sm {
            font-weight: 500;
            color: #1a2332;
            font-size: 9px;
        }
        .br-page .ft-value-sm input {
            background: transparent;
            border: none;
            border-bottom: 1px solid #b0bec5;
            outline: none;
            font-family: var(--font-mono);
            font-size: 9px;
            color: #1a2332;
            width: 100%;
            padding: 0 2px;
        }
        .br-page .ft-value-sm input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .br-page .ft-col-left {
            width: 38%;
            background: #fafcff;
            border-right: 2px solid #4a6fa5;
        }
        .br-page .ft-col-right {
            width: 60%;
            background: #fafcff;
        }
        .br-page .ft-divider {
            width: 2%;
            background: #e8edf4;
            border-right: 2px solid #4a6fa5;
            border-left: 2px solid #4a6fa5;
        }
        .br-page .ft-sign-line {
            border-bottom: 1.5px solid #1a2332;
            min-height: 18px;
            width: 80%;
            display: inline-block;
        }
        .br-page .ft-sign-line input {
            background: transparent;
            border: none;
            border-bottom: 1.5px solid #1a2332;
            outline: none;
            font-family: var(--font-serif);
            font-size: 9px;
            color: #1a2332;
            width: 100%;
            padding: 0 2px;
            min-height: 18px;
            font-style: italic;
        }
        .br-page .ft-sign-line input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .br-page .ft-ca-sig {
            text-align: center;
            padding: 2px 0;
        }
        .br-page .ft-ca-sig img {
            max-height: 34px;
            max-width: 120px;
            border: 1px solid #e0e7ef;
            border-radius: 3px;
            padding: 2px;
            background: #fff;
            display: inline-block;
        }
        .br-page .ft-ca-sig .ft-sign-line {
            border-bottom: 1.5px solid #1a2332;
            min-height: 16px;
            width: 70%;
            margin: 0 auto;
            display: inline-block;
        }
        .br-page .ft-upload-btn {
            font-size: 7.5px;
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
        .br-page .ft-upload-btn:hover {
            background: #e8edf4;
            border-color: #78909c;
        }
        .br-page .ft-upload-btn i { font-size: 8px; margin-right: 2px; }
        .br-page .ft-ca-name {
            font-weight: 600;
            color: #0d2a5e;
            font-size: 9px;
            margin-top: 1px;
        }
        .br-page .ft-ca-mem {
            font-size: 8px;
            color: #455a64;
            font-family: var(--font-mono);
        }
        .br-page .ft-ack-label {
            font-weight: 600;
            color: #1a3a6c;
            font-size: 9px;
            white-space: nowrap;
            padding-right: 4px;
            background: transparent !important;
        }
        .br-page .ft-ack-val input {
            background: transparent;
            border: none;
            border-bottom: 1px solid #b0bec5;
            outline: none;
            font-family: var(--font-mono);
            font-size: 9px;
            color: #1a2332;
            width: 100%;
            padding: 0 2px;
        }
        .br-page .ft-ack-val input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .br-page .ft-assessee-line { margin-top: 4px; }
        .br-page .ft-assessee-line .ft-sign-line { width: 80%; display: inline-block; }

        /* ─── TOAST (global) ─── */
        .toast-container {
            position: fixed;
            bottom: 80px;
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
        .toast.success { border-left-color: var(--emerald); }
        .toast.warning { border-left-color: var(--gold); }
        .toast.error { border-left-color: var(--red); }
        .toast .close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
            padding: 0 4px;
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(10px); }
            to { opacity:1; transform:translateY(0); }
        }
        .br-page .fade-up { animation: fadeUp 0.35s ease both; }
        .br-page .delay-1 { animation-delay: 0.05s; }
        .br-page .delay-2 { animation-delay: 0.1s; }

        @media print {
            .br-page .tb-actions,
            .br-page .btn-erp,
            .br-page .icon-btn,
            .br-page .collapse-toggle,
            .br-page .section-tools .search-box,
            .br-page .tbl-actions { display: none !important; }
            .br-page .paper { box-shadow: none; border: 1px solid #000; border-radius: 0; }
            .br-page .wm { color: rgba(0,0,0,0.04) !important; }
            .br-page .brs-table thead th {
                position: static;
                background: #1a3a6c !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .br-page .brs-table tbody tr.total-row td {
                background: #1a3a6c !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .br-page .brs-table tbody tr:nth-child(even) td {
                background: #fafcff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .br-page .brs-table tbody tr:nth-child(odd) td {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .br-page .footer-table td { padding: 3px 6px; }
            .br-page .ft-value-sm input { border-bottom: 1px solid #000; }
            .br-page .ft-sign-line { border-bottom: 1.5px solid #000; }
            .br-page .ft-sign-line input { border-bottom: 1.5px solid #000; }
            .br-page .ft-upload-btn { display: none !important; }
            .br-page .ft-ca-sig img { border: 1px solid #999; }
            .br-page .pgnum { display: block !important; }
            .br-page .summary-strip {
                display: flex !important;
                border-bottom: 1px solid #ccc;
                padding: 4px 8px;
                background: #f5f5f5;
            }
            .br-page .brs-body { display: flex !important; }
            .br-page .brs-left { flex: 0 0 40% !important; }
            .br-page .brs-right { flex: 1 !important; }
            .br-page .field-row .fld input { border: none !important; background: transparent !important; }
            .br-page .brs-table .td-amt,
            .br-page .brs-table .td-date,
            .br-page .brs-table .td-text,
            .br-page .brs-table .td-select { border: none !important; background: transparent !important; }
            .br-page .field-row .lbl { font-size: 9px !important; }
            .br-page .field-row .fld input { font-size: 9px !important; }
            .br-page .brs-table { font-size: 8.5px !important; }
            .br-page .summary-strip .item .val { font-size: 10px !important; }
        }
    </style>
@endsection

@section('content')
    <div class="br-page">
        <div class="paper fade-up delay-1">
            <div class="wm" id="wmText">DRAFT</div>
            <div class="paper-inner">
                @include('Backoffice.bank-reconciliation.partials._toolbar')
                @include('Backoffice.bank-reconciliation.partials._header')
                @include('Backoffice.bank-reconciliation.partials._totals')
                @include('Backoffice.bank-reconciliation.partials._table')
                @include('Backoffice.bank-reconciliation.partials._signature')
                @include('Backoffice.bank-reconciliation.partials._footer')

            </div>
            <div class="pgnum">Page 1 of 1</div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('Backoffice.bank-reconciliation.partials._scripts')
@endsection