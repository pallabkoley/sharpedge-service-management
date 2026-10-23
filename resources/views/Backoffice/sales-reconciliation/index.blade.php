@extends('layouts.Backoffice')

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
    <style>
        /* ─────────────── ROOT TOKENS ─────────────── */
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

        /* ─────────────── APPLICATION TOOLBAR ─────────────── */
        .app-toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 18px;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
            flex-wrap: wrap;
            margin-bottom: 4px;
        }
        .app-toolbar .tb-bread {
            font-size: 11.5px;
            color: var(--slate-light);
        }
        .app-toolbar .tb-bread b {
            color: var(--text-dark);
            font-weight: 600;
        }
        .app-toolbar .tb-search {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 5px 10px;
            width: 200px;
            flex-shrink: 0;
        }
        .app-toolbar .tb-search input {
            border: none;
            background: none;
            outline: none;
            font-size: 12px;
            color: var(--text-dark);
            width: 100%;
        }
        .app-toolbar .tb-search i {
            color: var(--slate-light);
            font-size: 13px;
        }
        .app-toolbar .tb-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
            flex-wrap: wrap;
        }
        .app-toolbar .tb-sep {
            width: 1px;
            height: 22px;
            background: var(--border);
            margin: 0 4px;
        }

        /* ─────────────── PAPER (scrollbar fix) ─────────────── */
        .paper {
            background: var(--white);
            border: 2px solid #4a6fa5;
            border-radius: var(--radius);
            overflow: visible;
            box-shadow: var(--shadow);
            position: relative;
            max-width: 1440px;
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
            transform: translate(-50%, -50%) rotate(-35deg);
            font-family: var(--font-display);
            font-size: 72px;
            font-weight: 700;
            color: rgba(180, 195, 215, 0.12);
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
        .hr-input:focus {
            background: #fffde7;
        }
        .hr-input.white {
            color: #fff;
        }
        .hr-input.white:focus {
            color: #0d2a5e;
        }

        .template-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            background: var(--off-white);
            border-bottom: 1px solid var(--border-light);
        }
        .template-toggle label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-mid);
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            padding: 4px 10px;
            border-radius: 4px;
            transition: all 0.15s;
            border: 1px solid transparent;
        }
        .template-toggle label:hover {
            background: var(--border-light);
        }
        .template-toggle input[type="radio"] {
            display: none;
        }
        .template-toggle input[type="radio"]:checked+span {
            color: var(--blue);
            font-weight: 700;
        }
        .template-toggle .active-label {
            background: var(--sky);
            border-color: var(--blue-light);
            border-radius: 4px;
        }
        .template-toggle .active-label span {
            color: var(--blue);
            font-weight: 700;
        }

        .table-wrap {
            overflow-x: auto;
            padding: 0;
            position: relative;
        }
        /* ─── Custom scrollbar styling ─── */
        .table-wrap::-webkit-scrollbar {
            height: 8px;
        }
        .table-wrap::-webkit-scrollbar-track {
            background: var(--border-light);
            border-radius: 4px;
        }
        .table-wrap::-webkit-scrollbar-thumb {
            background: var(--slate-light);
            border-radius: 4px;
        }
        .table-wrap::-webkit-scrollbar-thumb:hover {
            background: var(--slate);
        }

        .recon-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            table-layout: fixed;
            min-width: 1100px;
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
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.4px;
            padding: 6px 4px;
            font-family: var(--font-sans);
            text-transform: uppercase;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .recon-table thead th:first-child {
            min-width: 70px;
        }
        .recon-table thead th:last-child {
            min-width: 100px;
        }
        .recon-table thead th .sub-label {
            font-weight: 400;
            font-size: 8.5px;
            opacity: 0.8;
            display: block;
            margin-top: 1px;
            letter-spacing: 0.3px;
        }
        .recon-table tbody td {
            padding: 2px 4px;
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
        .recon-table tbody tr.month-row td {
            font-weight: 500;
        }
        .recon-table tbody tr.total-row td {
            background: #1a3a6c !important;
            color: #fff;
            font-weight: 700;
            font-size: 11.5px;
            padding: 5px 4px;
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
        .recon-table .td-amt {
            font-family: var(--font-mono);
            font-size: 11.5px;
            text-align: right;
            padding: 3px 8px;
            color: var(--text-dark);
            cursor: text;
            background: transparent;
            border: none;
            width: 100%;
            outline: none;
            transition: background 0.12s;
            min-height: 26px;
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
        .recon-table .td-amt.total {
            color: #fff;
            background: transparent !important;
            font-weight: 700;
        }
        .recon-table .td-amt.total:hover {
            background: transparent !important;
        }
        .recon-table .td-amt.total:focus {
            background: #fffde7 !important;
            color: var(--text-dark);
        }
        .recon-table .td-remark {
            padding: 3px 6px;
        }
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
        }
        .recon-table .td-actions .del-btn {
            color: var(--red);
            cursor: pointer;
            font-size: 15px;
            opacity: 0.5;
            transition: opacity 0.15s;
            background: none;
            border: none;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .recon-table .td-actions .del-btn:hover {
            opacity: 1;
            background: var(--red-light);
        }
        .recon-table .td-actions .del-btn.disabled {
            opacity: 0.2;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Footer (Trading Account style) */
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

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up {
            animation: fadeUp 0.35s ease both;
        }

        /* ─────────────── BUTTONS & ICONS (reused from original) ─────────────── */
        .btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s;
            white-space: nowrap;
            font-family: var(--font-sans);
        }
        .btn i {
            font-size: 13px;
        }
        .btn-ghost {
            background: var(--off-white);
            border-color: var(--border);
            color: var(--text-mid);
        }
        .btn-ghost:hover {
            background: var(--border-light);
            border-color: #b8c4d4;
        }
        .btn-blue {
            background: var(--blue);
            color: #fff;
        }
        .btn-blue:hover {
            background: #1251a3;
        }
        .btn-green {
            background: var(--emerald);
            color: #fff;
        }
        .btn-green:hover {
            background: #00766c;
        }
        .icon-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 1px solid var(--border);
            background: var(--off-white);
            color: var(--slate);
            font-size: 15px;
            transition: all 0.15s;
            position: relative;
        }
        .icon-btn:hover {
            background: var(--border-light);
            color: var(--text-dark);
        }
        .notif-dot {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--red);
            border: 1.5px solid #fff;
        }
        .user-pill {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 4px 10px 4px 4px;
            border-radius: 20px;
            background: var(--off-white);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.15s;
        }
        .user-pill:hover {
            background: var(--border-light);
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
            color: var(--text-dark);
        }

        /* ─────────────── PRINT ─────────────── */
        @media print {
            .app-toolbar {
                display: none !important;
            }
            .paper {
                box-shadow: none;
                border: 1px solid #000;
                border-radius: 0;
                overflow: visible;
            }
            .wm {
                color: rgba(0, 0, 0, 0.04) !important;
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
        }

        @media (max-width: 768px) {
            .app-toolbar {
                padding: 6px 10px;
                gap: 8px;
            }
            .app-toolbar .tb-search {
                width: 140px;
            }
            .app-toolbar .tb-actions .btn span {
                display: none;
            }
            .app-toolbar .tb-actions .btn i {
                margin: 0;
            }
            .app-toolbar .tb-actions .btn {
                padding: 5px 8px;
            }
            .paper {
                border-radius: 4px;
            }
            .recon-table {
                font-size: 10px;
                min-width: 800px;
            }
            .recon-table thead th {
                font-size: 8.5px;
                padding: 4px 2px;
            }
            .recon-table .td-amt {
                font-size: 10px;
                padding: 2px 4px;
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
        }
    </style>
@endsection

@section('content')
    {{-- 1. Application Toolbar (above report header) --}}
    @include('Backoffice.sales-reconciliation.partials._toolbar')

    <div class="paper fade-up">
        <div class="wm" id="wmText">DRAFT</div>
        <div class="paper-inner">

            {{-- 2. Report Header --}}
            @include('Backoffice.sales-reconciliation.partials._header')

            {{-- 3. Register / Unregister Tabs --}}
            @include('Backoffice.sales-reconciliation.partials._tabs')

            {{-- 4. Sales Table --}}
            @include('Backoffice.sales-reconciliation.partials._table')

            {{-- 5. Signature Section --}}
            @include('Backoffice.sales-reconciliation.partials._signature')

            {{-- 6. Footer (page number) --}}
            @include('Backoffice.sales-reconciliation.partials._footer')

        </div><!-- /paper-inner -->
    </div><!-- /paper -->
@endsection

@section('scripts')
    @include('Backoffice.sales-reconciliation.partials._scripts')
@endsection