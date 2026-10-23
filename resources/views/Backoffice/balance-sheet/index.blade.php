@extends('layouts.Backoffice')

@section('styles')

    {{-- Balance Sheet Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           BALANCE SHEET PAGE
           ========================================================= */

        :root {
            --bs-navy: #0d1b2e;
            --bs-navy-mid: #132238;
            --bs-navy-light: #1a2f4a;
            --bs-navy-border: #1e3a5f;

            --bs-blue: #1565c0;
            --bs-blue-light: #1e88e5;
            --bs-sky: #e3f2fd;

            --bs-slate: #546e7a;
            --bs-slate-light: #78909c;

            --bs-white: #ffffff;
            --bs-off-white: #f8f9fc;

            --bs-border: #dde3ec;
            --bs-border-light: #eef1f6;

            --bs-emerald: #00897b;
            --bs-red: #c62828;
            --bs-gold: #f9a825;

            --bs-text-dark: #1a2332;
            --bs-text-mid: #3a4a5c;
            --bs-text-light: #607080;

            --bs-font-mono: 'IBM Plex Mono', monospace;
            --bs-font-sans: 'IBM Plex Sans', sans-serif;
            --bs-font-display: 'Playfair Display', serif;

            --bs-table: #4a6fa5;
        }

        .balance-sheet-page {
            width: 100%;
            min-width: 0;
            background: var(--bs-off-white);
            font-family: var(--bs-font-sans);
            color: var(--bs-text-dark);
        }

        /* =========================================================
           PAPER
           ========================================================= */

        .balance-sheet-page .paper {
            width: 100%;
            max-width: 1400px;
            margin: 20px auto;
            background: var(--bs-white);
            border: 2px solid var(--bs-table);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
            position: relative;
        }

        .balance-sheet-page .balance-sheet-inner {
            position: relative;
            z-index: 2;
            width: 100%;
        }

        .balance-sheet-page .wm {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-family: var(--bs-font-display);
            font-size: 72px;
            font-weight: 700;
            color: rgba(200, 210, 230, .14);
            pointer-events: none;
            z-index: 1;
            letter-spacing: 12px;
            text-transform: uppercase;
            user-select: none;
            white-space: nowrap;
        }

        .balance-sheet-page .pgn {
            position: absolute;
            bottom: 6px;
            right: 14px;
            font-size: 10px;
            color: var(--bs-slate-light);
            font-family: var(--bs-font-mono);
            z-index: 5;
        }

        /* =========================================================
           HEADER ROWS
           ========================================================= */

        .balance-sheet-page .hr-r {
            background: #1a3a6c;
            color: #fff;
            text-align: center;
            padding: 4px 6px;
            font-size: 11.5px;
            font-weight: 700;
            border-bottom: 1px solid #2a5aa0;
        }

        .balance-sheet-page .hr-r.lt {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 11px;
            font-weight: 600;
            border-bottom: 1px solid var(--bs-table);
        }

        .balance-sheet-page .hr-r.tt {
            font-size: 13px;
            letter-spacing: 2px;
            background: #1a3a6c;
            padding: 5px 6px;
            font-family: var(--bs-font-display);
        }

        .balance-sheet-page .hr-r.pd {
            background: #d6e4f7;
            color: #0d2a5e;
            font-size: 10px;
            font-weight: 500;
            border-bottom: 2px solid var(--bs-table);
        }

        .balance-sheet-page .shi {
            background: transparent;
            border: none;
            outline: none;
            color: #0d2a5e;
            font-family: var(--bs-font-sans);
            font-size: 10.5px;
            font-weight: 600;
            text-align: center;
            width: 100%;
            cursor: text;
        }

        .balance-sheet-page .shi:focus {
            background: #fffde7;
        }

        /* =========================================================
           BALANCE SHEET TABLE
           ========================================================= */

        .balance-sheet-page .bs-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
        }

        .balance-sheet-page .bs-tbl td,
        .balance-sheet-page .bs-tbl th {
            border: 1px solid var(--bs-table);
            padding: 0;
            vertical-align: middle;
        }

        .balance-sheet-page .col-h {
            background: #1a3a6c;
            color: #fff;
            text-align: center;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .4px;
            padding: 5px 4px;
            font-family: var(--bs-font-sans);
            text-transform: uppercase;
        }

        .balance-sheet-page .col-h2 {
            background: #2a5aa0;
            color: #c8ddf0;
            text-align: center;
            font-size: 9.5px;
            font-weight: 600;
            padding: 3px 4px;
            font-family: var(--bs-font-mono);
        }

        .balance-sheet-page .sh-r td {
            background: #d6e4f7;
            color: #0d2a5e;
            font-weight: 700;
            font-size: 10.5px;
            padding: 4px 8px;
            font-family: var(--bs-font-sans);
            letter-spacing: .3px;
        }

        .balance-sheet-page .sh-lnk {
            color: #0d47a1;
            font-weight: 700;
            text-decoration: underline;
            cursor: pointer;
            font-style: italic;
        }

        .balance-sheet-page .sh-toggle {
            float: right;
            cursor: pointer;
            font-size: 11px;
            color: #1565c0;
            user-select: none;
        }

        .balance-sheet-page .dr td {
            background: #f7faff;
        }

        .balance-sheet-page .dr:nth-child(even) td {
            background: #fff;
        }

        .balance-sheet-page .dr:hover td {
            background: #e3f0fd !important;
        }

        .balance-sheet-page .td-pt {
            padding: 2px 8px;
            font-size: 11px;
            color: #1a2332;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
        }

        .balance-sheet-page .td-pt .main-line {
            display: flex;
            align-items: center;
            gap: 4px;
            width: 100%;
        }

        .balance-sheet-page .td-pt .source-text {
            font-size: 8px;
            font-family: var(--bs-font-sans);
            color: #78909c;
            font-weight: 400;
            padding-left: 18px;
            line-height: 1.3;
            margin-top: 0;
        }

        .balance-sheet-page .bul {
            color: #2a5aa0;
            font-size: 8px;
            flex-shrink: 0;
        }

        .balance-sheet-page .td-rs {
            font-family: var(--bs-font-mono);
            font-size: 10px;
            color: #455a64;
            text-align: center;
            padding: 2px;
            width: 18px;
            background: inherit;
        }

        .balance-sheet-page .td-am {
            font-family: var(--bs-font-mono);
            font-size: 11px;
            text-align: right;
            padding: 2px 6px;
            color: #1a2332;
            white-space: nowrap;
            cursor: text;
        }

        .balance-sheet-page .td-am:focus {
            background: #fffde7 !important;
            outline: 2px solid var(--bs-gold);
        }

        .balance-sheet-page .td-am.sub {
            color: #1565c0;
            font-weight: 700;
            border-top: 1px solid var(--bs-table);
        }

        .balance-sheet-page .td-am.blank {
            background: #eef3fa !important;
        }

        .balance-sheet-page .td-am.neg {
            color: var(--bs-red);
        }

        .balance-sheet-page .td-note {
            font-family: var(--bs-font-mono);
            font-size: 10px;
            text-align: center;
            padding: 2px 4px;
            color: #546e7a;
            width: 28px;
        }

        .balance-sheet-page .st-r td {
            background: #dbeafe;
            color: #1565c0;
            font-weight: 700;
            font-size: 10.5px;
            padding: 3px 8px;
            font-family: var(--bs-font-sans);
        }

        .balance-sheet-page .st-r .td-am {
            color: #1565c0;
            background: #dbeafe !important;
            font-weight: 700;
        }

        .balance-sheet-page .st-r .td-rs {
            background: #dbeafe !important;
            color: #1565c0;
        }

        .balance-sheet-page .dc {
            background: var(--bs-table) !important;
            width: 3px !important;
            padding: 0 !important;
            border: none !important;
        }

        .balance-sheet-page .gt-r td {
            background: #1a3a6c;
            color: #fff;
            font-weight: 700;
            font-size: 11.5px;
            padding: 5px 8px;
            font-family: var(--bs-font-mono);
        }

        .balance-sheet-page .gt-r .td-note {
            background: #1a3a6c !important;
            color: #a0c4ff;
        }

        .balance-sheet-page .sec-body.collapsed {
            display: none;
        }

        .balance-sheet-page [contenteditable="true"]:focus {
            outline: 2px solid var(--bs-gold);
            outline-offset: -1px;
        }

        /* =========================================================
           SUMMARY
           ========================================================= */

        .balance-sheet-page .sum-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 4px;
            margin-bottom: 20px;
        }

        .balance-sheet-page .sum-card {
            background: var(--bs-white);
            border: 1px solid var(--bs-border);
            border-radius: 7px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
        }

        .balance-sheet-page .sum-head {
            background: #1a3a6c;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 5px 10px;
            letter-spacing: .5px;
            text-transform: uppercase;
            font-family: var(--bs-font-sans);
        }

        .balance-sheet-page .sum-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .balance-sheet-page .sum-tbl td {
            padding: 4px 10px;
            border-bottom: 1px solid var(--bs-border-light);
        }

        .balance-sheet-page .sum-tbl td:last-child {
            text-align: right;
            font-family: var(--bs-font-mono);
            font-weight: 600;
        }

        .balance-sheet-page .sum-tbl tr:hover td {
            background: #f0f5fb;
        }

        .balance-sheet-page .sum-tbl .tot td {
            background: #1a3a6c;
            color: #fff;
            font-weight: 700;
        }

        .balance-sheet-page .sum-tbl .tot td:last-child {
            color: #7cc4fa;
        }

        /* =========================================================
           SIGNATURE
           ========================================================= */

        .balance-sheet-page .sig-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }

        .balance-sheet-page .sig-tbl td {
            border: 1px solid var(--bs-table);
            padding: 4px 8px;
            vertical-align: top;
        }

        .balance-sheet-page .sl {
            font-weight: 700;
            font-size: 9.5px;
            color: #1a2332;
            font-family: var(--bs-font-sans);
        }

        .balance-sheet-page .sv {
            font-size: 9.5px;
            color: #455a64;
            margin-top: 2px;
        }

        .balance-sheet-page .sii {
            background: transparent;
            border: none;
            outline: none;
            font-family: var(--bs-font-sans);
            font-size: 9.5px;
            color: #1a2332;
            width: 100%;
            cursor: text;
        }

        .balance-sheet-page .sii:focus {
            background: #fffde7;
        }

        /* =========================================================
           RESPONSIVE
           ========================================================= */

        @media (max-width: 992px) {
            .balance-sheet-page .paper {
                margin: 15px 0;
            }

            .balance-sheet-page .sum-wrap {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .balance-sheet-page .paper {
                margin: 10px 0;
                border-radius: 4px;
            }

            .balance-sheet-page .wm {
                font-size: 42px;
                letter-spacing: 6px;
            }

            .balance-sheet-page .sum-wrap {
                grid-template-columns: 1fr;
            }
        }

        /* =========================================================
           PRINT
           ========================================================= */

        @media print {
            #sidebar,
            #topbar,
            #right-panel,
            #statusbar,
            .mobile-menu-btn,
            .refresh-indicator,
            .refresh-toggle-btn {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
                height: auto !important;
                overflow: visible !important;
            }

            .balance-sheet-page {
                background: #fff;
            }

            .balance-sheet-page .paper {
                max-width: none;
                margin: 0;
                box-shadow: none;
                border: 1px solid #000;
                border-radius: 0;
            }

            .balance-sheet-page .wm {
                color: rgba(0, 0, 0, .04) !important;
            }
        }
    </style>

@endsection


@section('content')

<div class="balance-sheet-page">

    {{-- Toolbar --}}
    @include('Backoffice.computation.partials.toolbar', [
        'computation' => $computation
    ])

    {{-- Balance Sheet Paper --}}
    <div class="paper">

        <div class="wm" id="wmTxt">DRAFT</div>

        <div class="pgn">
            Page 3 of 3
        </div>

        <div class="balance-sheet-inner">

            {{-- Header --}}
            @include(
                'Backoffice.balance-sheet.partials._header',
                compact('balanceSheet')
            )

            {{-- Balance Sheet Table --}}
            @include(
                'Backoffice.balance-sheet.partials._table',
                compact(
                    'balanceSheet',
                    'liabilities',
                    'assets'
                )
            )

            {{-- Signature --}}
            @include(
                'Backoffice.balance-sheet.partials._signature',
                compact('balanceSheet')
            )

        </div>

    </div>

    {{-- Summary --}}
    @include('Backoffice.balance-sheet.partials._summary')

</div>

@endsection


@section('scripts')

    @include('Backoffice.balance-sheet.partials._scripts')

@endsection