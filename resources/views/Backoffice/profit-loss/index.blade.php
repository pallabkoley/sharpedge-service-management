@extends('layouts.Backoffice')

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
    <style>
        /* ===== TOKENS ===== */
        .pl-page {
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
            --off-white: #f8f9fc;
            --border: #dde3ec;
            --border-light: #eef1f6;
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
            --th-bg: #1a3a6c;
            --sec-bg: #d6e4f7;
            --sec-txt: #0d2a5e;
            --gp-bg: #c8e6c9;
            --gp-txt: #1b5e20;
            --np-bg: #fff3e0;
            --np-txt: #e65100;
            --dep-bg: #fce4ec;
            --dep-txt: #880e4f;
            --tot-bg: #1a3a6c;
            --tot-txt: #ffffff;
            --tbl-brd: #4a6fa5;
        }

        .pl-page .paper {
            background: var(--white);
            border: 2px solid #4a6fa5;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            position: relative;
            max-width: 1600px;
            margin: 0 auto;
            font-family: 'Tinos', 'IBM Plex Sans', serif;
        }
        .pl-page .paper-in { position: relative; z-index: 2; }
        .pl-page .wm {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%) rotate(-35deg);
            font-family: var(--font-display);
            font-size: 72px;
            font-weight: 700;
            color: rgba(200,210,230,0.15);
            pointer-events: none;
            z-index: 1;
            letter-spacing: 12px;
            text-transform: uppercase;
            user-select: none;
            white-space: nowrap;
        }
        .pl-page .pgn {
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

        /* Header rows */
        .pl-page .hr-r {
            background: #1a3a6c;
            color: #fff;
            text-align: center;
            padding: 4px 6px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .5px;
            border-bottom: 1px solid #2a5aa0;
            font-family: var(--font-sans);
        }
        .pl-page .hr-r.lt {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 11px;
            font-weight: 600;
            border-bottom: 1px solid #4a6fa5;
        }
        .pl-page .hr-r.tt {
            font-size: 13px;
            letter-spacing: 2px;
            background: #1a3a6c;
            padding: 5px 6px;
            font-family: var(--font-display);
        }
        .pl-page .hr-r.pd {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 10px;
            font-weight: 500;
            border-bottom: 2px solid #4a6fa5;
        }
        .pl-page .shi {
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
        .pl-page .shi:focus { background: #fffde7; }

        /* Toolbar */
        .pl-page .pl-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 8px 14px;
            background: #f1f5f9;
            border-bottom: 2px solid #4a6fa5;
        }
        .pl-page .pl-toolbar-left {
            font-size: 13px;
            font-weight: 500;
            color: #1a2332;
        }
        .pl-page .pl-toolbar-right {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .pl-page .btn-erp {
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
        .pl-page .btn-erp i { font-size: 13px; }
        .pl-page .btn-ghost {
            background: var(--off-white);
            border-color: var(--border);
            color: var(--text-mid);
        }
        .pl-page .btn-ghost:hover { background: var(--border-light); border-color: #c0cad8; }
        .pl-page .btn-primary-erp { background: var(--blue); color: #fff; }
        .pl-page .btn-primary-erp:hover { background: #1251a3; }
        .pl-page .btn-success-erp { background: var(--emerald); color: #fff; }
        .pl-page .btn-success-erp:hover { background: #00766c; }

        /* ===== P&L TABLE ===== */
        .pl-page .pl-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
        }
        .pl-page .pl-tbl td, .pl-page .pl-tbl th {
            border: 1px solid #4a6fa5;
            padding: 0;
            vertical-align: middle;
        }
        .pl-page .ch {
            background: #1a3a6c;
            color: #fff;
            text-align: center;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .4px;
            padding: 5px 4px;
            font-family: var(--font-sans);
            text-transform: uppercase;
        }
        .pl-page .ch2 {
            background: #2a5aa0;
            color: #c8ddf0;
            text-align: center;
            font-size: 9.5px;
            font-weight: 600;
            padding: 3px 4px;
            font-family: var(--font-mono);
        }
        .pl-page .sh-r td {
            background: #d6e4f7;
            color: #0d2a5e;
            font-weight: 700;
            font-size: 10.5px;
            padding: 4px 8px;
            font-family: var(--font-sans);
            letter-spacing: .3px;
        }
        .pl-page .sh-lnk { color: #0d47a1; font-weight: 700; cursor: pointer; }
        .pl-page .sh-toggle { float: right; cursor: pointer; font-size: 11px; color: #1565c0; user-select: none; }
        .pl-page .dr td { background: #f7faff; }
        .pl-page .dr:nth-child(even) td { background: #fff; }
        .pl-page .dr:hover td { background: #e3f0fd !important; }
        .pl-page .td-pt { padding: 2px 8px; font-size: 11px; color: #1a2332; display: flex; align-items: center; gap: 4px; }
        .pl-page .bul { color: #2a5aa0; font-size: 8px; flex-shrink: 0; }
        .pl-page .td-rs {
            font-family: var(--font-mono);
            font-size: 10.5px;
            color: #455a64;
            text-align: center;
            padding: 2px 2px;
            width: 22px;
            background: inherit;
        }
        .pl-page .td-am {
            font-family: var(--font-mono);
            font-size: 11px;
            text-align: right;
            padding: 2px 6px;
            color: #1a2332;
            cursor: text;
            white-space: nowrap;
        }
        .pl-page .td-am:hover { background: #fffbf0 !important; }
        .pl-page .td-am:focus { background: #fffde7 !important; outline: 2px solid #f9a825; outline-offset: -1px; }
        .pl-page .td-am.sub { color: #1565c0; font-weight: 700; border-top: 1px solid #4a6fa5; }
        .pl-page .td-am.blank { background: #eef3fa !important; }
        .pl-page .td-am.cr { color: #00695c; font-weight: 700; }
        .pl-page .dc { background: #4a6fa5 !important; width: 2px !important; padding: 0 !important; border: none !important; }
        .pl-page .sec-body { transition: all .2s ease; }
        .pl-page .sec-body.collapsed { display: none; }

        .pl-page .st-r td { background: #dbeafe; color: #1565c0; font-weight: 700; font-size: 10.5px; padding: 3px 8px; font-family: var(--font-sans); }
        .pl-page .st-r .td-am { color: #1565c0; background: #dbeafe !important; font-weight: 700; }
        .pl-page .st-r .td-rs { background: #dbeafe !important; color: #1565c0; }

        .pl-page .gp-r td { background: #c8e6c9; color: #1b5e20; font-weight: 700; font-size: 11px; padding: 4px 8px; font-family: var(--font-sans); }
        .pl-page .gp-r .td-am { color: #1b5e20; background: #c8e6c9 !important; font-weight: 700; }
        .pl-page .gp-r .td-rs { background: #c8e6c9 !important; color: #1b5e20; }

        .pl-page .np-r td { background: #fff3e0; color: #e65100; font-weight: 700; font-size: 11px; padding: 4px 8px; font-family: var(--font-sans); }
        .pl-page .np-r .td-am { color: #e65100; background: #fff3e0 !important; font-weight: 700; }
        .pl-page .np-r .td-rs { background: #fff3e0 !important; color: #e65100; }

        .pl-page .dep-r td { background: #fce4ec; color: #880e4f; font-size: 11px; padding: 3px 8px; font-family: var(--font-sans); }
        .pl-page .dep-r .td-am { color: #880e4f; background: #fce4ec !important; font-weight: 600; }
        .pl-page .dep-r .td-rs { background: #fce4ec !important; color: #880e4f; }

        .pl-page .gt-r td { background: #1a3a6c; color: #fff; font-weight: 700; font-size: 11.5px; padding: 5px 8px; font-family: var(--font-mono); }
        .pl-page .gt-r .td-rs { background: #1a3a6c !important; color: #fff; }

        [contenteditable=true]:focus { outline: 2px solid #f9a825; outline-offset: -1px; }

        /* Footer */
        .pl-page .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 0;
            border: 1px solid #4a6fa5;
            font-family: var(--font-sans);
        }
        .pl-page .footer-table td {
            border: 1px solid #4a6fa5;
            padding: 5px 10px;
            vertical-align: top;
        }
        .pl-page .ft-inner-table {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }
        .pl-page .ft-inner-table td {
            border: none;
            padding: 2px 4px;
            vertical-align: middle;
            background: transparent !important;
        }
        .pl-page .ft-label-sm {
            font-weight: 600;
            color: #1a3a6c;
            font-size: 10px;
            white-space: nowrap;
            width: 1%;
            padding-right: 4px;
        }
        .pl-page .ft-value-sm {
            font-weight: 500;
            color: #1a2332;
            font-size: 10px;
        }
        .pl-page .ft-value-sm input {
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
        .pl-page .ft-value-sm input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .pl-page .ft-col-left {
            width: 38%;
            background: #fafcff;
            border-right: 2px solid #4a6fa5;
        }
        .pl-page .ft-col-right {
            width: 60%;
            background: #fafcff;
        }
        .pl-page .ft-divider {
            width: 2%;
            background: #e8edf4;
            border-right: 2px solid #4a6fa5;
            border-left: 2px solid #4a6fa5;
        }
        .pl-page .ft-assessee-sign-line { margin-top: 4px; }
        .pl-page .ft-assessee-sign-line .ft-signature-line {
            border-bottom: 1.5px solid #1a2332;
            min-height: 18px;
            width: 75%;
            display: inline-block;
        }
        .pl-page .ft-assessee-sign-line .ft-signature-line input {
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
        .pl-page .ft-assessee-sign-line .ft-signature-line input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }
        .pl-page .ft-ca-sig-wrapper { text-align: center; padding: 2px 0; }
        .pl-page .ft-ca-sig-wrapper img {
            max-height: 38px;
            max-width: 130px;
            border: 1px solid #e0e7ef;
            border-radius: 3px;
            padding: 2px;
            background: #fff;
            display: inline-block;
        }
        .pl-page .ft-ca-sig-wrapper .ft-signature-line {
            border-bottom: 1.5px solid #1a2332;
            min-height: 18px;
            width: 70%;
            margin: 0 auto;
            display: inline-block;
        }
        .pl-page .ft-upload-btn-sm {
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
        .pl-page .ft-upload-btn-sm:hover {
            background: #e8edf4;
            border-color: #78909c;
        }
        .pl-page .ft-upload-btn-sm i { font-size: 9px; margin-right: 2px; }
        .pl-page .ft-ca-name-line {
            font-weight: 600;
            color: #0d2a5e;
            font-size: 10px;
            margin-top: 1px;
        }
        .pl-page .ft-ca-membership-line {
            font-size: 9px;
            color: #455a64;
            font-family: var(--font-mono);
        }
        .pl-page .ft-label-ack {
            font-weight: 600;
            color: #1a3a6c;
            font-size: 10px;
            white-space: nowrap;
            padding-right: 4px;
            background: transparent !important;
        }
        .pl-page .ft-value-ack input {
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
        .pl-page .ft-value-ack input:focus {
            border-bottom-color: #1565c0;
            background: #fffde7;
        }

        /* ===== RIGHT PANEL ===== */
        .pl-page .right-panel {
            width: 230px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-left: 12px;
        }
        .pl-page .rp-heading {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--slate-light);
            padding-bottom: 6px;
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 2px;
        }
        .pl-page .kpi-card {
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 10px 12px;
            background: var(--off-white);
            transition: box-shadow 0.15s;
        }
        .pl-page .kpi-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .pl-page .kpi-label { font-size: 10px; color: var(--slate); font-weight: 500; }
        .pl-page .kpi-value { font-family: var(--font-mono); font-size: 15px; font-weight: 700; color: var(--text-dark); margin-top: 2px; line-height: 1.2; }
        .pl-page .kpi-change { font-size: 10px; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 3px; }
        .pl-page .kpi-change.up { color: var(--emerald); }
        .pl-page .kpi-change.down { color: var(--red); }
        .pl-page .kpi-change.warn { color: var(--gold); }
        .pl-page .kpi-bar-wrap { height: 4px; border-radius: 2px; background: var(--border-light); margin-top: 6px; overflow: hidden; }
        .pl-page .kpi-bar { height: 100%; border-radius: 2px; transition: width 0.6s ease; }
        .pl-page .kpi-bar.green { background: var(--emerald); }
        .pl-page .kpi-bar.blue { background: var(--blue); }
        .pl-page .kpi-bar.red { background: var(--red); }
        .pl-page .kpi-bar.gold { background: var(--gold); }
        .pl-page .ratio-ring-wrap { display: flex; align-items: center; gap: 10px; margin-top: 6px; }
        .pl-page .ratio-ring { width: 48px; height: 48px; flex-shrink: 0; }
        .pl-page .ratio-stats { flex: 1; }
        .pl-page .ratio-pct { font-family: var(--font-mono); font-size: 17px; font-weight: 700; color: var(--blue); }
        .pl-page .ratio-label { font-size: 9.5px; color: var(--slate); }
        .pl-page .rp-notes {
            background: var(--sky);
            border: 1px solid #bbdefb;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 10.5px;
            color: var(--blue);
        }
        .pl-page .rp-notes strong { display: block; margin-bottom: 3px; }

        .pl-page .erp-wrap { display: flex; gap: 12px; align-items: flex-start; }

        @media print {
            .pl-page .btn-erp,
            .pl-page .pl-toolbar,
            .pl-page .right-panel { display: none !important; }
            .pl-page .paper { box-shadow: none; border: 1px solid #000; border-radius: 0; }
            .pl-page .wm { color: rgba(0,0,0,0.04) !important; }
            .pl-page .footer-table td { padding: 3px 6px; }
            .pl-page .ft-value-sm input { border-bottom: 1px solid #000; }
            .pl-page .ft-assessee-sign-line .ft-signature-line { border-bottom: 1.5px solid #000; }
            .pl-page .ft-upload-btn-sm { display: none !important; }
        }

        @keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .pl-page .fade-up { animation: fadeUp 0.3s ease both; }
        .pl-page .delay-1 { animation-delay: 0.05s; }
        .pl-page .delay-2 { animation-delay: 0.1s; }
    </style>
@endsection

@section('content')
    <div class="pl-page">
        <div class="erp-wrap">
            <div class="paper fade-up delay-1" style="flex:1;min-width:0;">
                <div class="wm" id="wmTxt">DRAFT</div>
                <div class="paper-in">

                    {{-- ─── TOOLBAR ─── --}}
                    @include('Backoffice.profit-loss.partials._toolbar')

                    {{-- ─── HEADER ─── --}}
                    @include('Backoffice.profit-loss.partials._header')

                    {{-- ─── TABLE ─── --}}
                    @include('Backoffice.profit-loss.partials._table')

                    {{-- ─── FOOTER ─── --}}
                    @include('Backoffice.profit-loss.partials._footer')

                </div>
                <div class="pgn">Page 2 of 2</div>
            </div>

            {{-- ─── RIGHT PANEL ─── --}}
            @include('Backoffice.profit-loss.partials._right_panel')
        </div>
    </div>
@endsection

@section('scripts')
    @include('Backoffice.profit-loss.partials._scripts')
@endsection