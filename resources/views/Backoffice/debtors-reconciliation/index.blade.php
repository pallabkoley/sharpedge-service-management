@extends('layouts.Backoffice')

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
    <style>
        /* ─── ROOT TOKENS (scoped inside .dr-page) ─── */
        .dr-page {
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

            /* ─── Missing variables (declared here) ─── */
            --radius-sm: 6px;
            --text-muted: #64748b;
            --accent: var(--blue-light);
            --font-serif: Georgia, 'Times New Roman', serif;
        }

        /* ─── PAPER ─── */
        .dr-page .paper {
            background: var(--white);
            border: 2px solid #4a6fa5;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            position: relative;
            max-width: 1600px;
            margin: 0 auto;
        }
        .dr-page .paper-inner { position: relative; z-index: 2; padding: 0; }
        .dr-page .wm {
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
        .dr-page .pgnum {
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
        .dr-page .hr {
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
        .dr-page .hr.light {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 11px;
            font-weight: 600;
            border-bottom: 1px solid #4a6fa5;
        }
        .dr-page .hr.title {
            font-size: 14px;
            letter-spacing: 2px;
            background: #1a3a6c;
            padding: 6px 6px;
            font-family: var(--font-display);
        }
        .dr-page .hr.period {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 10.5px;
            font-weight: 500;
            border-bottom: 2px solid #4a6fa5;
        }
        .dr-page .hr-input {
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
        .dr-page .hr-input:focus { background: #fffde7; }
        .dr-page .hr-input.white { color: #fff; }
        .dr-page .hr-input.white:focus { color: #0d2a5e; }

        /* ─── CARDS ─── */
        .dr-page .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            padding: 12px 14px;
            background: var(--off-white);
            border-bottom: 1px solid var(--border-light);
        }
        .dr-page .stat-card {
            background: var(--white);
            border-radius: var(--radius-sm);
            padding: 12px 14px;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        .dr-page .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        .dr-page .stat-card .label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .dr-page .stat-card .value {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-top: 2px;
            font-family: var(--font-mono);
        }
        .dr-page .stat-card .sub {
            font-size: 10px;
            color: var(--text-muted);
        }

        /* ─── SECTION HEADERS ─── */
        .dr-page .section-header {
            background: #eef3f8;
            padding: 8px 14px;
            border-bottom: 2px solid #4a6fa5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 6px;
        }
        .dr-page .section-header h5 {
            font-family: var(--font-sans);
            font-weight: 700;
            color: #0d2a5e;
            font-size: 13px;
            margin: 0;
            letter-spacing: 0.3px;
        }
        .dr-page .section-header .badge-type {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 12px;
            background: #1a3a6c;
            color: #fff;
        }
        .dr-page .section-tools {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .dr-page .section-tools .search-box {
            display: flex;
            align-items: center;
            gap: 4px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 12px;
        }
        .dr-page .section-tools .search-box input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 12px;
            font-family: var(--font-sans);
            color: var(--text-dark);
            width: 120px;
            padding: 3px 0;
        }
        .dr-page .section-tools .search-box input::placeholder {
            color: var(--text-muted);
        }
        .dr-page .collapse-toggle {
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
        .dr-page .collapse-toggle:hover { color: var(--blue); }
        .dr-page .collapse-toggle .arrow {
            transition: var(--transition);
            display: inline-block;
            font-size: 10px;
        }
        .dr-page .collapse-toggle .arrow.open { transform: rotate(90deg); }
        .dr-page .section-body {
            transition: var(--transition);
            overflow: hidden;
        }
        .dr-page .section-body.collapsed {
            max-height: 0 !important;
            padding-top: 0 !important;
            opacity: 0;
            margin: 0;
        }

        /* ─── TABLES ─── */
        .dr-page .table-wrap {
            overflow: auto;
            padding: 0;
            position: relative;
            max-height: 480px;
        }
        .dr-page .table-wrap::-webkit-scrollbar { width: 4px; height: 4px; }
        .dr-page .table-wrap::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }
        .dr-page .recon-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
            min-width: 1100px;
        }
        .dr-page .recon-table th,
        .dr-page .recon-table td {
            border: 1px solid #4a6fa5;
            padding: 0;
            vertical-align: middle;
            text-align: center;
        }
        .dr-page .recon-table thead th {
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
        .dr-page .recon-table thead th .sub-label {
            font-weight: 400;
            font-size: 7px;
            opacity: 0.8;
            display: block;
            margin-top: 1px;
        }
        .dr-page .recon-table thead th.sticky-col {
            position: sticky;
            left: 0;
            z-index: 11;
            background: #1a3a6c;
        }
        .dr-page .recon-table tbody td { padding: 2px 2px; transition: background 0.12s; }
        .dr-page .recon-table tbody tr:hover td { background: #eaf2fd !important; }
        .dr-page .recon-table tbody tr:nth-child(even) td { background: #fafcff; }
        .dr-page .recon-table tbody tr:nth-child(odd) td { background: #ffffff; }
        .dr-page .recon-table tbody tr.total-row td {
            background: #1a3a6c !important;
            color: #fff;
            font-weight: 700;
            font-size: 11px;
            padding: 4px 2px;
            font-family: var(--font-mono);
            border-top: 2px solid #4a6fa5;
        }
        .dr-page .recon-table tbody tr.total-row td:first-child {
            text-transform: uppercase;
            font-family: var(--font-sans);
            letter-spacing: 0.5px;
        }
        .dr-page .recon-table .td-label {
            font-weight: 600;
            color: var(--text-dark);
            padding: 3px 4px;
            text-align: left;
            font-size: 10.5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .dr-page .recon-table .td-label .idx {
            font-weight: 400;
            color: var(--text-muted);
            font-size: 9px;
            min-width: 16px;
        }
        .dr-page .recon-table .td-amt {
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
        .dr-page .recon-table .td-amt:hover { background: #fffbf0 !important; }
        .dr-page .recon-table .td-amt:focus {
            background: #fffde7 !important;
            outline: 2px solid #f9a825;
            outline-offset: -2px;
            border-radius: 2px;
        }
        .dr-page .recon-table .td-amt.total-cell {
            color: #fff;
            background: transparent !important;
            font-weight: 700;
        }
        .dr-page .recon-table .td-amt.total-cell:hover { background: transparent !important; }
        .dr-page .recon-table .td-amt.total-cell:focus {
            background: rgba(255,255,255,0.15) !important;
            color: #fff;
        }
        .dr-page .recon-table .td-amt.negative {
            color: var(--red);
            background: var(--red-light) !important;
        }
        .dr-page .recon-table .td-amt.readonly {
            background: #f4f7fc;
            cursor: default;
        }
        .dr-page .recon-table .td-amt.readonly:hover { background: #f4f7fc !important; }
        .dr-page .recon-table .td-amt.sticky-col {
            position: sticky;
            left: 0;
            background: inherit;
            z-index: 2;
        }
        .dr-page .recon-table .td-amt.sticky-col:hover { background: inherit; }
        .dr-page .recon-table .td-text {
            font-family: var(--font-sans);
            font-size: 10px;
            text-align: left;
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
        .dr-page .recon-table .td-text:hover { background: #fffbf0 !important; }
        .dr-page .recon-table .td-text:focus {
            background: #fffde7 !important;
            outline: 2px solid #f9a825;
            outline-offset: -2px;
            border-radius: 2px;
        }
        .dr-page .recon-table .td-text.uppercase { text-transform: uppercase; }
        .dr-page .recon-table .td-date {
            font-family: var(--font-mono);
            font-size: 9px;
            text-align: center;
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
        .dr-page .recon-table .td-date:hover { background: #fffbf0 !important; }
        .dr-page .recon-table .td-date:focus {
            background: #fffde7 !important;
            outline: 2px solid #f9a825;
            outline-offset: -2px;
            border-radius: 2px;
        }
        .dr-page .recon-table .sticky-col {
            position: sticky;
            left: 0;
            z-index: 2;
            background: inherit;
        }
        .dr-page .recon-table .sticky-col.sticky-header { z-index: 12; }

        /* ─── TOOLBAR ─── */
        .dr-page .dr-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 8px 14px;
            background: #f8fafc;
            border-bottom: 1px solid #d0d8e4;
        }
        .dr-page .dr-toolbar-left {
            font-size: 13px;
            font-weight: 500;
            color: #1a2332;
        }
        .dr-page .dr-toolbar-right {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .dr-page .dr-search {
            display: flex;
            align-items: center;
            gap: 4px;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 12px;
        }
        .dr-page .dr-search input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 12px;
            width: 150px;
        }
        .dr-page .btn-erp {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s;
            white-space: nowrap;
            font-family: var(--font-sans);
            text-decoration: none;
        }
        .dr-page .btn-erp i { font-size: 13px; }
        .dr-page .btn-ghost {
            background: #f1f5f9;
            border-color: #d0d8e4;
            color: #1a2332;
        }
        .dr-page .btn-ghost:hover {
            background: #e2e8f0;
            border-color: #b0bec5;
        }
        .dr-page .btn-blue {
            background: #1565c0;
            color: #fff;
        }
        .dr-page .btn-blue:hover { background: #0d47a1; }
        .dr-page .btn-green {
            background: #00897b;
            color: #fff;
        }
        .dr-page .btn-green:hover { background: #00695c; }
        .dr-page .btn-sm { font-size: 10px; padding: 2px 8px; }

        /* ─── FOOTER ─── */
        .dr-page .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 0;
            border: 1px solid #4a6fa5;
            font-family: var(--font-sans);
        }
        .dr-page .footer-table td {
            border: 1px solid #4a6fa5;
            padding: 5px 10px;
            vertical-align: top;
        }
        .dr-page .ft-inner {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }
        .dr-page .ft-inner td {
            border: none;
            padding: 2px 4px;
            vertical-align: middle;
            background: transparent !important;
        }
        .dr-page .ft-label-sm {
            font-weight: 600;
            color: #1a3a6c;
            font-size: 10px;
            white-space: nowrap;
            width: 1%;
            padding-right: 4px;
        }
        .dr-page .ft-value-sm {
            font-weight: 500;
            color: #1a2332;
            font-size: 10px;
        }
        .dr-page .ft-value-sm input {
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
        .dr-page .ft-value-sm input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .dr-page .ft-col-left {
            width: 38%;
            background: #fafcff;
            border-right: 2px solid #4a6fa5;
        }
        .dr-page .ft-col-right {
            width: 60%;
            background: #fafcff;
        }
        .dr-page .ft-divider {
            width: 2%;
            background: #e8edf4;
            border-right: 2px solid #4a6fa5;
            border-left: 2px solid #4a6fa5;
        }
        .dr-page .ft-sign-line {
            border-bottom: 1.5px solid #1a2332;
            min-height: 18px;
            width: 75%;
            display: inline-block;
        }
        .dr-page .ft-sign-line input {
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
        .dr-page .ft-sign-line input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .dr-page .ft-ca-sig {
            text-align: center;
            padding: 2px 0;
        }
        .dr-page .ft-ca-sig img {
            max-height: 38px;
            max-width: 130px;
            border: 1px solid #e0e7ef;
            border-radius: 3px;
            padding: 2px;
            background: #fff;
            display: inline-block;
        }
        .dr-page .ft-ca-sig .ft-sign-line {
            border-bottom: 1.5px solid #1a2332;
            min-height: 18px;
            width: 70%;
            margin: 0 auto;
            display: inline-block;
        }
        .dr-page .ft-upload-btn {
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
        .dr-page .ft-upload-btn:hover {
            background: #e8edf4;
            border-color: #78909c;
        }
        .dr-page .ft-upload-btn i { font-size: 9px; margin-right: 2px; }
        .dr-page .ft-ca-name {
            font-weight: 600;
            color: #0d2a5e;
            font-size: 10px;
            margin-top: 1px;
        }
        .dr-page .ft-ca-mem {
            font-size: 9px;
            color: #455a64;
            font-family: var(--font-mono);
        }
        .dr-page .ft-ack-label {
            font-weight: 600;
            color: #1a3a6c;
            font-size: 10px;
            white-space: nowrap;
            padding-right: 4px;
            background: transparent !important;
        }
        .dr-page .ft-ack-val input {
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
        .dr-page .ft-ack-val input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .dr-page .ft-assessee-line { margin-top: 4px; }
        .dr-page .ft-assessee-line .ft-sign-line { width: 75%; display: inline-block; }

        /* ─── TOAST (global – not scoped because it's fixed) ─── */
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
        .dr-page .fade-up { animation: fadeUp 0.35s ease both; }
        .dr-page .delay-1 { animation-delay: 0.05s; }
        .dr-page .delay-2 { animation-delay: 0.1s; }
        .dr-page .text-right { text-align: right; }
        .dr-page .text-center { text-align: center; }
        .dr-page .fw-600 { font-weight: 600; }
        .dr-page .mono { font-family: var(--font-mono); }
        .dr-page .mt-1 { margin-top: 4px; }
        .dr-page .mb-1 { margin-bottom: 4px; }
        .dr-page .gap-1 { gap: 4px; }
        .dr-page .flex { display: flex; }
        .dr-page .flex-center { display: flex; align-items: center; justify-content: center; }
        .dr-page .hidden { display: none !important; }

        @media print {
            .dr-page .tb-actions,
            .dr-page .btn-erp,
            .dr-page .icon-btn,
            .dr-page .collapse-toggle,
            .dr-page .section-tools .search-box { display: none !important; }
            .dr-page .paper { box-shadow: none; border: 1px solid #000; border-radius: 0; }
            .dr-page .wm { color: rgba(0,0,0,0.04) !important; }
            .dr-page .recon-table thead th {
                position: static;
                background: #1a3a6c !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .dr-page .recon-table tbody tr.total-row td {
                background: #1a3a6c !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .dr-page .recon-table tbody tr:nth-child(even) td {
                background: #fafcff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .dr-page .recon-table tbody tr:nth-child(odd) td {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .dr-page .footer-table td { padding: 3px 6px; }
            .dr-page .ft-value-sm input { border-bottom: 1px solid #000; }
            .dr-page .ft-sign-line { border-bottom: 1.5px solid #000; }
            .dr-page .ft-sign-line input { border-bottom: 1.5px solid #000; }
            .dr-page .ft-upload-btn { display: none !important; }
            .dr-page .ft-ca-sig img { border: 1px solid #999; }
            .dr-page .pgnum { display: block !important; }
            .dr-page .cards-grid { display: none !important; }
        }
    </style>
@endsection

@section('content')
    <div class="dr-page">
        <div class="paper fade-up delay-1">
            <div class="wm" id="wmText">DRAFT</div>
            <div class="paper-inner">
   @include('Backoffice.debtors-reconciliation.partials._toolbar')
                @include('Backoffice.debtors-reconciliation.partials._header')
                @include('Backoffice.debtors-reconciliation.partials._totals')
                
                {{-- 🆕 Toolbar (added here) --}}
             
                
                @include('Backoffice.debtors-reconciliation.partials._table')
                @include('Backoffice.debtors-reconciliation.partials._signature')
                @include('Backoffice.debtors-reconciliation.partials._footer')

            </div>
            <div class="pgnum">Page 1 of 1</div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('Backoffice.debtors-reconciliation.partials._scripts')
@endsection