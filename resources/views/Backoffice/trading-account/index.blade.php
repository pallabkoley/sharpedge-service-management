@extends('layouts.Backoffice')

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600;700&family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet" />
    <style>
        /* ─── ROOT TOKENS ─── */
        .ta-page {
            --navy: #0d1b2e;
            --navy-mid: #132238;
            --navy-light: #1a2f4a;
            --navy-border: #1e3a5f;
            --blue: #1565c0;
            --blue-light: #1e88e5;
            --sky: #dbeafe;
            --slate: #546e7a;
            --white: #ffffff;
            --off-white: #f4f6fa;
            --border: #b0bec5;
            --border-light: #e0e7ef;
            --emerald: #00695c;
            --emerald-bg: #e0f2f1;
            --red: #b71c1c;
            --gold: #f57f17;
            --tbl-head-bg: #1a3a6c;
            --tbl-head-txt: #ffffff;
            --tbl-sec-bg: #d6e4f7;
            --tbl-sec-txt: #0d2a5e;
            --tbl-row-alt: #f0f5fb;
            --tbl-gp-bg: #c8e6c9;
            --tbl-gp-txt: #1b5e20;
            --tbl-tot-bg: #1a3a6c;
            --tbl-tot-txt: #ffffff;
            --font-mono: 'IBM Plex Mono', monospace;
            --font-sans: 'IBM Plex Sans', sans-serif;
            --font-serif: 'Tinos', serif;
            --shadow: 0 2px 12px rgba(0,0,0,0.07);
            --shadow-hover: 0 8px 32px rgba(0,0,0,0.14);
            --radius: 8px;
            --transition: 0.25s cubic-bezier(0.4,0,0.2,1);
            --radius-sm: 6px;
            --text-muted: #64748b;
            --accent: var(--blue-light);
        }

        /* ─── PAPER ─── */
        .ta-page .paper {
            background: var(--white);
            border: 2px solid #4a6fa5;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            position: relative;
            max-width: 1600px;
            margin: 0 auto;
        }
        .ta-page .paper-inner { position: relative; z-index: 2; padding: 0; }
        .ta-page .wm {
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
        .ta-page .pgnum {
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
        .ta-page .hr {
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
        .ta-page .hr.light {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 11px;
            font-weight: 600;
            border-bottom: 1px solid #4a6fa5;
        }
        .ta-page .hr.title {
            font-size: 14px;
            letter-spacing: 2px;
            background: #1a3a6c;
            padding: 6px 6px;
            font-family: var(--font-display);
        }
        .ta-page .hr.period {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 10.5px;
            font-weight: 500;
            border-bottom: 2px solid #4a6fa5;
        }
        .ta-page .hr-input {
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
        .ta-page .hr-input:focus { background: #fffde7; }
        .ta-page .hr-input.white { color: #fff; }
        .ta-page .hr-input.white:focus { color: #0d2a5e; }

        /* ─── TOOLBAR ─── */
        .ta-page .ta-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 8px 14px;
            background: #f1f5f9;
            border-bottom: 2px solid #4a6fa5;
        }
        .ta-page .ta-toolbar-left {
            font-size: 13px;
            font-weight: 500;
            color: #1a2332;
        }
        .ta-page .ta-toolbar-right {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .ta-page .btn-erp {
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
        .ta-page .btn-erp i { font-size: 13px; }
        .ta-page .btn-ghost {
            background: #f1f5f9;
            border-color: #d0d8e4;
            color: #1a2332;
        }
        .ta-page .btn-ghost:hover {
            background: #e2e8f0;
            border-color: #b0bec5;
        }
        .ta-page .btn-blue {
            background: #1565c0;
            color: #fff;
        }
        .ta-page .btn-blue:hover { background: #0d47a1; }
        .ta-page .btn-orange {
            background: #e65100;
            color: #fff;
        }
        .ta-page .btn-orange:hover { background: #bf360c; }
        .ta-page .btn-green {
            background: #00897b;
            color: #fff;
        }
        .ta-page .btn-green:hover { background: #00695c; }

        /* ─── TABLE ─── */
        .ta-page .ta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            table-layout: fixed;
        }
        .ta-page .ta-table td,
        .ta-page .ta-table th {
            border: 1px solid #4a6fa5;
            padding: 0;
            vertical-align: middle;
        }
        .ta-page .ta-table .col-head {
            background: #1a3a6c;
            color: #fff;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .5px;
            padding: 5px 4px;
            font-family: var(--font-sans);
            text-transform: uppercase;
        }
        .ta-page .ta-table .col-subhead {
            background: #2a5aa0;
            color: #c8ddf0;
            text-align: center;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 4px;
            font-family: var(--font-mono);
        }
        .ta-page .ta-table .col-head-debit {
            background: #0d2a5e;
            color: #fff;
            text-align: center;
            font-size: 9.5px;
            font-weight: 700;
            padding: 5px 4px;
            font-family: var(--font-sans);
            text-transform: uppercase;
            border-right: 2px solid #fff;
        }
        .ta-page .ta-table .col-head-credit {
            background: #0d2a5e;
            color: #fff;
            text-align: center;
            font-size: 9.5px;
            font-weight: 700;
            padding: 5px 4px;
            font-family: var(--font-sans);
            text-transform: uppercase;
        }

        /* Row styles */
        .ta-page .sec-head td {
            background: #d6e4f7;
            color: #0d2a5e;
            font-weight: 700;
            font-size: 11px;
            padding: 4px 8px;
            font-family: var(--font-sans);
            letter-spacing: .3px;
            text-transform: uppercase;
        }
        .ta-page .sec-head .sec-lnk { color: #0d47a1; font-weight: 700; }
        .ta-page .sec-head .adj-label {
            font-weight: 500;
            font-size: 10px;
            color: #0d2a5e;
            margin-left: 12px;
            font-family: var(--font-sans);
            letter-spacing: 0.3px;
        }
        .ta-page .sec-head .adj-selector {
            font-size: 10px;
            padding: 1px 6px;
            border: 1px solid #4a6fa5;
            border-radius: 3px;
            background: #fff;
            font-family: var(--font-sans);
            font-weight: 500;
            color: #0d2a5e;
            cursor: pointer;
            margin-left: 4px;
        }

        .ta-page .dr td { vertical-align: middle; }
        .ta-page .dr:nth-child(odd) td { background: #f7faff; }
        .ta-page .dr:nth-child(even) td { background: #ffffff; }
        .ta-page .dr:hover td { background: #e3f0fd !important; }

        .ta-page .td-part {
            padding: 3px 8px;
            font-size: 11.5px;
            color: #1a2332;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 1px;
            font-family: var(--font-serif);
        }
        .ta-page .td-part .main-line {
            display: flex;
            align-items: center;
            gap: 4px;
            width: 100%;
        }
        .ta-page .td-part .bullet {
            color: #2a5aa0;
            font-size: 9px;
            flex-shrink: 0;
        }
        .ta-page .td-part .source-text {
            font-size: 8.5px;
            font-family: var(--font-sans);
            color: #78909c;
            font-weight: 400;
            padding-left: 18px;
            line-height: 1.2;
        }
        .ta-page .td-part .delete-btn {
            margin-left: auto;
            color: #c62828;
            cursor: pointer;
            font-size: 13px;
            opacity: 0.6;
            transition: opacity 0.12s;
            padding: 0 2px;
            flex-shrink: 0;
        }
        .ta-page .td-part .delete-btn:hover { opacity: 1; }
        .ta-page .td-part .part-input {
            border: none;
            background: transparent;
            outline: none;
            font-family: var(--font-serif);
            font-size: 11.5px;
            color: #1a2332;
            width: 100%;
            cursor: text;
        }
        .ta-page .td-part .part-input:focus { background: #fffde7; }

        .ta-page .td-amt {
            font-family: var(--font-mono);
            font-size: 11.5px;
            text-align: right;
            padding: 3px 6px;
            color: #1a2332;
            cursor: text;
            vertical-align: middle;
            white-space: nowrap;
        }
        .ta-page .td-amt:hover { background: #fffbf0 !important; }
        .ta-page .td-amt:focus { background: #fffde7 !important; outline: none; }
        .ta-page .td-amt.blank { background: #eef3fa !important; }
        .ta-page .td-amt.sub { color: #0d47a1; font-weight: 700; border-top: 2px solid #4a6fa5; }
        .ta-page .td-amt.neg { color: #c62828; }
        .ta-page .td-amt.pos { color: #1b5e20; }
        .ta-page .td-rs {
            font-family: var(--font-mono);
            font-size: 11px;
            color: #455a64;
            text-align: center;
            padding: 3px 2px;
            width: 24px;
            background: inherit;
        }

        /* Subtotal rows */
        .ta-page .sub-row td {
            background: #e3edf7 !important;
            font-weight: 600;
            font-family: var(--font-sans);
            color: #0d2a5e;
            border-top: 2px solid #4a6fa5;
            border-bottom: 2px solid #4a6fa5;
        }
        .ta-page .sub-row .td-amt { font-weight: 700; color: #0d47a1; }

        /* GP row */
        .ta-page .gp-row td {
            background: #c8e6c9 !important;
            color: #1b5e20;
            font-weight: 700;
            font-size: 12px;
            padding: 4px 8px;
            border-top: 2px solid #2e7d32;
            border-bottom: 2px solid #2e7d32;
        }
        .ta-page .gp-row .td-part { font-weight: 700; font-style: italic; color: #1b5e20; }
        .ta-page .gp-row .td-amt { color: #1b5e20 !important; background: #c8e6c9 !important; font-weight: 700; }
        .ta-page .gp-row.gp-loss td { background: #ffcdd2 !important; color: #b71c1c !important; }
        .ta-page .gp-row.gp-loss .td-part { color: #b71c1c !important; }
        .ta-page .gp-row.gp-loss .td-amt { color: #b71c1c !important; background: #ffcdd2 !important; }

        /* Total row */
        .ta-page .gt-row td {
            background: #1a3a6c !important;
            color: #fff !important;
            font-weight: 700;
            font-size: 12px;
            padding: 5px 8px;
            border-top: 3px double #4a6fa5;
        }
        .ta-page .gt-row .td-amt { color: #fff !important; background: #1a3a6c !important; font-weight: 700; }

        /* Hidden rows */
        .ta-page .hidden-row { display: none !important; }

        /* ─── FOOTER ─── */
        .ta-page .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 0;
            border: 1px solid #4a6fa5;
            font-family: var(--font-sans);
        }
        .ta-page .footer-table td {
            border: 1px solid #4a6fa5;
            padding: 5px 10px;
            vertical-align: top;
        }
        .ta-page .ft-inner {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }
        .ta-page .ft-inner td {
            border: none;
            padding: 2px 4px;
            vertical-align: middle;
            background: transparent !important;
        }
        .ta-page .ft-label-sm {
            font-weight: 600;
            color: #1a3a6c;
            font-size: 10px;
            white-space: nowrap;
            width: 1%;
            padding-right: 4px;
        }
        .ta-page .ft-value-sm {
            font-weight: 500;
            color: #1a2332;
            font-size: 10px;
        }
        .ta-page .ft-value-sm input {
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
        .ta-page .ft-value-sm input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .ta-page .ft-col-left {
            width: 38%;
            background: #fafcff;
            border-right: 2px solid #4a6fa5;
        }
        .ta-page .ft-col-right {
            width: 60%;
            background: #fafcff;
        }
        .ta-page .ft-divider {
            width: 2%;
            background: #e8edf4;
            border-right: 2px solid #4a6fa5;
            border-left: 2px solid #4a6fa5;
        }
        .ta-page .ft-sign-line {
            border-bottom: 1.5px solid #1a2332;
            min-height: 18px;
            width: 75%;
            display: inline-block;
        }
        .ta-page .ft-sign-line input {
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
        .ta-page .ft-sign-line input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .ta-page .ft-ca-sig {
            text-align: center;
            padding: 2px 0;
        }
        .ta-page .ft-ca-sig img {
            max-height: 38px;
            max-width: 130px;
            border: 1px solid #e0e7ef;
            border-radius: 3px;
            padding: 2px;
            background: #fff;
            display: inline-block;
        }
        .ta-page .ft-ca-sig .ft-sign-line {
            border-bottom: 1.5px solid #1a2332;
            min-height: 18px;
            width: 70%;
            margin: 0 auto;
            display: inline-block;
        }
        .ta-page .ft-upload-btn {
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
        .ta-page .ft-upload-btn:hover {
            background: #e8edf4;
            border-color: #78909c;
        }
        .ta-page .ft-upload-btn i { font-size: 9px; margin-right: 2px; }
        .ta-page .ft-ca-name {
            font-weight: 600;
            color: #0d2a5e;
            font-size: 10px;
            margin-top: 1px;
        }
        .ta-page .ft-ca-mem {
            font-size: 9px;
            color: #455a64;
            font-family: var(--font-mono);
        }
        .ta-page .ft-ack-label {
            font-weight: 600;
            color: #1a3a6c;
            font-size: 10px;
            white-space: nowrap;
            padding-right: 4px;
            background: transparent !important;
        }
        .ta-page .ft-ack-val input {
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
        .ta-page .ft-ack-val input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .ta-page .ft-assessee-line { margin-top: 4px; }
        .ta-page .ft-assessee-line .ft-sign-line { width: 75%; display: inline-block; }

        /* ─── TOAST ─── */
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
        .ta-page .fade-up { animation: fadeUp 0.35s ease both; }
        .ta-page .delay-1 { animation-delay: 0.05s; }
        .ta-page .delay-2 { animation-delay: 0.1s; }

        @media print {
            .ta-page .btn-erp,
            .ta-page .ta-toolbar { display: none !important; }
            .ta-page .paper { box-shadow: none; border: 1px solid #000; border-radius: 0; }
            .ta-page .wm { color: rgba(0,0,0,0.04) !important; }
            .ta-page .ta-table thead th {
                position: static;
                background: #1a3a6c !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .ta-page .ta-table tbody .sub-row td {
                background: #e3edf7 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .ta-page .ta-table tbody .gp-row td {
                background: #c8e6c9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .ta-page .ta-table tbody .gt-row td {
                background: #1a3a6c !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .ta-page .ta-table tbody .dr:nth-child(odd) td {
                background: #f7faff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .ta-page .ta-table tbody .dr:nth-child(even) td {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .ta-page .footer-table td { padding: 3px 6px; }
            .ta-page .ft-value-sm input { border-bottom: 1px solid #000; }
            .ta-page .ft-sign-line { border-bottom: 1.5px solid #000; }
            .ta-page .ft-sign-line input { border-bottom: 1.5px solid #000; }
            .ta-page .ft-upload-btn { display: none !important; }
            .ta-page .ft-ca-sig img { border: 1px solid #999; }
            .ta-page .pgnum { display: block !important; }
        }
    </style>
@endsection

@section('content')
    <div class="ta-page">
        <div class="paper fade-up delay-1">
            <div class="wm" id="wmText">DRAFT</div>
            <div class="paper-inner">

                {{-- ─── TOOLBAR (উপরে) ─── --}}
                @include('Backoffice.trading-account.partials._toolbar')

                {{-- ─── HEADER ─── --}}
                @include('Backoffice.trading-account.partials._header')

                {{-- ─── TABLE ─── --}}
                @include('Backoffice.trading-account.partials._table')

                {{-- ─── FOOTER ─── --}}
                @include('Backoffice.trading-account.partials._footer')

            </div>
            <div class="pgnum">Page 1 of 1</div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('Backoffice.trading-account.partials._scripts')
@endsection