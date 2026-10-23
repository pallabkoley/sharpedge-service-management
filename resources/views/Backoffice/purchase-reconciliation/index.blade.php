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
            --sidebar-w: 220px;
            --top-h: 56px;
            --bottom-h: 28px;
            --font-mono: 'IBM Plex Mono', monospace;
            --font-sans: 'IBM Plex Sans', sans-serif;
            --font-display: 'Playfair Display', serif;
            --shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            --radius: 8px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html,
        body {
            height: 100%;
            overflow: hidden;
            font-family: var(--font-sans);
            background: var(--off-white);
            color: var(--text-dark);
        }

        /* ─────────────── APP LAYOUT ─────────────── */
        #app {
            display: grid;
            grid-template-columns: var(--sidebar-w) 1fr;
            grid-template-rows: var(--top-h) 1fr var(--bottom-h);
            height: 100vh;
        }

        /* ─────────────── SIDEBAR ─────────────── */
        #sidebar {
            grid-column: 1;
            grid-row: 1 / 4;
            background: var(--navy);
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--navy-border);
            overflow: hidden;
        }
        .sb-logo {
            padding: 14px 16px 12px;
            border-bottom: 1px solid var(--navy-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sb-mark {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #1e88e5, #0d47a1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-size: 14px;
            color: #fff;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(30, 136, 229, 0.35);
        }
        .sb-brand {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.3px;
        }
        .sb-sub {
            font-size: 9px;
            color: #7090b0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .sb-label {
            padding: 14px 16px 5px;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1.5px;
            color: #4a6a8a;
            text-transform: uppercase;
        }
        .sb-link {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 16px;
            margin: 1px 8px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12.5px;
            font-weight: 400;
            color: #8aabb8;
            transition: all 0.15s;
            text-decoration: none;
        }
        .sb-link i {
            font-size: 14px;
            width: 16px;
            flex-shrink: 0;
        }
        .sb-link:hover {
            background: var(--navy-light);
            color: #cde;
        }
        .sb-link.active {
            background: linear-gradient(90deg, rgba(30, 136, 229, 0.22), rgba(30, 136, 229, 0.08));
            color: #7cc4fa;
            font-weight: 600;
            border-left: 2px solid var(--blue-light);
            margin-left: 6px;
            padding-left: 14px;
        }
        .sb-link.active i {
            color: #5db8ff;
        }
        .sb-divider {
            border-color: var(--navy-border);
            margin: 6px 16px;
        }
        .sb-footer {
            margin-top: auto;
            padding: 10px 14px;
            border-top: 1px solid var(--navy-border);
            font-size: 10px;
            color: #4a6a8a;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ─────────────── TOPBAR ─────────────── */
        #topbar {
            grid-column: 2;
            grid-row: 1;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 18px;
            gap: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
            z-index: 100;
        }
        .tb-bread {
            font-size: 11.5px;
            color: var(--slate-light);
        }
        .tb-bread b {
            color: var(--text-dark);
            font-weight: 600;
        }
        .tb-sep {
            width: 1px;
            height: 22px;
            background: var(--border);
            margin: 0 4px;
        }
        .tb-search {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 5px 10px;
            width: 200px;
        }
        .tb-search input {
            border: none;
            background: none;
            outline: none;
            font-size: 12px;
            color: var(--text-dark);
            width: 100%;
        }
        .tb-search i {
            color: var(--slate-light);
            font-size: 13px;
        }
        .tb-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }
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
        .btn-red {
            background: var(--red);
            color: #fff;
        }
        .btn-red:hover {
            background: #a71e1e;
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

        /* ─────────────── MAIN ─────────────── */
        #main {
            grid-column: 2;
            grid-row: 2;
            overflow-y: auto;
            background: var(--off-white);
            padding: 18px 22px;
        }

        /* ─────────────── PAPER ─────────────── */
        .paper {
            background: var(--white);
            border: 2px solid #4a6fa5;
            border-radius: var(--radius);
            overflow: visible;
            box-shadow: var(--shadow);
            position: relative;
            max-width: 1480px;
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

        /* ─── TEMPLATE TOGGLE ─── */
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

        /* ─── TABLE ─── */
        .table-wrap {
            overflow-x: auto;
            padding: 0;
            position: relative;
        }
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
            min-width: 1200px;
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
        .recon-table thead th:first-child {
            min-width: 60px;
        }
        .recon-table thead th:last-child {
            min-width: 100px;
        }
        .recon-table thead th .sub-label {
            font-weight: 400;
            font-size: 8px;
            opacity: 0.8;
            display: block;
            margin-top: 1px;
            letter-spacing: 0.2px;
        }
        .recon-table tbody td {
            padding: 2px 3px;
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
        .recon-table .td-amt.na {
            color: #b0b8c4;
            font-style: italic;
        }
        .recon-table .td-remark {
            padding: 3px 4px;
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

        /* ─── FOOTER (Trading Account style) ─── */
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

        /* ─────────────── STATUSBAR ─────────────── */
        #statusbar {
            grid-column: 2;
            grid-row: 3;
            background: var(--navy);
            color: #6a8ea8;
            display: flex;
            align-items: center;
            padding: 0 14px;
            gap: 0;
            font-size: 10px;
            font-family: var(--font-mono);
            border-top: 1px solid var(--navy-border);
        }
        .sb-item {
            padding: 0 10px;
            border-right: 1px solid var(--navy-border);
            display: flex;
            align-items: center;
            gap: 5px;
            height: 100%;
        }
        .sb-item:first-child {
            padding-left: 0;
        }
        .sb-item.active {
            color: #7cc4fa;
        }
        .sb-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--emerald);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,
            100% {
                opacity: 1
            }
            50% {
                opacity: 0.4
            }
        }
        .sb-key {
            background: var(--navy-light);
            border: 1px solid var(--navy-border);
            border-radius: 3px;
            padding: 0 5px;
            font-size: 9px;
            color: #8aaabb;
        }
        .sb-spacer {
            flex: 1;
        }
        .sb-time {
            padding: 0 10px;
            color: #5db8ff;
            font-weight: 600;
        }

        /* ─────────────── SCROLL ─────────────── */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--slate-light);
        }

        /* ─────────────── PRINT ─────────────── */
        @media print {
            #sidebar,
            #topbar,
            #statusbar,
            .tb-actions,
            .btn,
            .icon-btn,
            .template-toggle .ft-upload-btn,
            .del-btn {
                display: none !important;
            }
            #app {
                display: block;
            }
            #main {
                overflow: visible;
                padding: 0;
            }
            .paper {
                box-shadow: none;
                border: 1px solid #000;
                border-radius: 0;
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
            .template-toggle {
                display: none !important;
            }
        }

        /* ─────────────── RESPONSIVE ─────────────── */
        @media (max-width: 1024px) {
            #sidebar {
                width: 60px;
                min-width: 60px;
            }
            .sb-brand,
            .sb-sub,
            .sb-label,
            .sb-link span,
            .sb-footer span {
                display: none;
            }
            .sb-link {
                padding: 10px 12px;
                justify-content: center;
            }
            .sb-link i {
                font-size: 18px;
                width: auto;
            }
            .sb-logo {
                justify-content: center;
                padding: 10px 8px;
            }
            .sb-mark {
                margin: 0;
            }
            .sb-footer {
                justify-content: center;
            }
            #app {
                grid-template-columns: 60px 1fr;
            }
            .topbar-search {
                width: 140px;
            }
            .tb-actions .btn span {
                display: none;
            }
            .tb-actions .btn i {
                margin: 0;
            }
            .tb-actions .btn {
                padding: 5px 8px;
            }
        }
        @media (max-width: 768px) {
            #main {
                padding: 10px 8px;
            }
            .paper {
                border-radius: 4px;
            }
            .recon-table {
                font-size: 10px;
                min-width: 900px;
            }
            .recon-table thead th {
                font-size: 8px;
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

        /* ─────────────── ANIMATIONS ─────────────── */
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
        .delay-2 {
            animation-delay: 0.1s;
        }

        /* ─────────────── UTILITIES ─────────────── */
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-600 {
            font-weight: 600;
        }
        .mono {
            font-family: var(--font-mono);
        }
        .mt-1 {
            margin-top: 4px;
        }
        .mb-1 {
            margin-bottom: 4px;
        }
        .gap-1 {
            gap: 4px;
        }
        .flex {
            display: flex;
        }
        .flex-center {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hidden {
            display: none !important;
        }
        .na-text {
            color: #b0b8c4;
            font-style: italic;
            font-weight: 400;
        }
        
        
        /* ─────────────── APPLICATION TOOLBAR ─────────────── */
.app-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 6px 18px;
    background: var(--white);
    border-bottom: 1px solid var(--border);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    flex-wrap: nowrap;
    min-height: 50px;
    margin-bottom: 4px;
}

/* Breadcrumb */
.app-toolbar .tb-bread {
    font-size: 12px;
    color: var(--slate-light);
    white-space: nowrap;
    flex-shrink: 0;
}
.app-toolbar .tb-bread b {
    color: var(--text-dark);
    font-weight: 600;
}

/* Search */
.app-toolbar .tb-search {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--off-white);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 4px 10px;
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

/* Right actions */
.app-toolbar .tb-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
    flex-wrap: wrap;
}
.app-toolbar .tb-actions .tb-sep {
    width: 1px;
    height: 24px;
    background: var(--border);
    margin: 0 4px;
}

/* Buttons */
.app-toolbar .btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all 0.15s;
    white-space: nowrap;
    font-family: var(--font-sans);
    height: 30px;
}
.app-toolbar .btn i {
    font-size: 13px;
}
.app-toolbar .btn-ghost {
    background: var(--off-white);
    border-color: var(--border);
    color: var(--text-mid);
}
.app-toolbar .btn-ghost:hover {
    background: var(--border-light);
    border-color: #b8c4d4;
}
.app-toolbar .btn-blue {
    background: var(--blue);
    color: #fff;
}
.app-toolbar .btn-blue:hover {
    background: #1251a3;
}
.app-toolbar .btn-green {
    background: var(--emerald);
    color: #fff;
}
.app-toolbar .btn-green:hover {
    background: #00766c;
}
.app-toolbar .btn-primary {
    background: #1a3a6c;
    color: #fff;
}
.app-toolbar .btn-primary:hover {
    background: #0d2a5e;
}

/* Icon buttons */
.app-toolbar .icon-btn {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 1px solid var(--border);
    background: var(--off-white);
    color: var(--slate);
    font-size: 14px;
    transition: all 0.15s;
    position: relative;
    flex-shrink: 0;
}
.app-toolbar .icon-btn:hover {
    background: var(--border-light);
    color: var(--text-dark);
}
.app-toolbar .notif-dot {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--red);
    border: 1.5px solid #fff;
}

/* User pill – optional */
.app-toolbar .user-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 2px 8px 2px 2px;
    border-radius: 20px;
    background: var(--off-white);
    border: 1px solid var(--border);
    cursor: pointer;
    transition: all 0.15s;
    height: 30px;
}
.app-toolbar .user-pill:hover {
    background: var(--border-light);
}
.app-toolbar .user-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1565c0, #00897b);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}
.app-toolbar .user-name {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-dark);
}
.app-toolbar .user-pill .bi-chevron-down {
    font-size: 10px;
    color: var(--slate-light);
}

/* ─── RESPONSIVE ─── */
@media (max-width: 1024px) {
    .app-toolbar {
        flex-wrap: wrap;
        padding: 6px 12px;
        gap: 8px;
        min-height: auto;
    }
    .app-toolbar .tb-search {
        width: 150px;
    }
    .app-toolbar .tb-actions .btn span {
        display: none;
    }
    .app-toolbar .tb-actions .btn i {
        margin: 0;
    }
    .app-toolbar .tb-actions .btn {
        padding: 4px 8px;
    }
}

@media (max-width: 768px) {
    .app-toolbar {
        flex-direction: column;
        align-items: stretch;
        gap: 6px;
    }
    .app-toolbar .tb-bread {
        text-align: center;
    }
    .app-toolbar .tb-search {
        width: 100%;
    }
    .app-toolbar .tb-actions {
        justify-content: center;
        flex-wrap: wrap;
    }
    .app-toolbar .tb-actions .btn span {
        display: inline;
    }
}
    </style>
@endsection

@section('content')
    {{-- Application Toolbar --}}
    @include('Backoffice.purchase-reconciliation.partials._toolbar')

    <div class="paper fade-up delay-1">
        <div class="wm" id="wmText">DRAFT</div>
        <div class="paper-inner">

            {{-- Report Header --}}
            @include('Backoffice.purchase-reconciliation.partials._header')

            {{-- Register / Unregister Tabs --}}
            @include('Backoffice.purchase-reconciliation.partials._tabs')

            {{-- Sales Table --}}
            @include('Backoffice.purchase-reconciliation.partials._table')

            {{-- Signature Section --}}
            @include('Backoffice.purchase-reconciliation.partials._signature')

            {{-- Footer --}}
            @include('Backoffice.purchase-reconciliation.partials._footer')

        </div><!-- /paper-inner -->
        <div class="pgnum">Page 1 of 1</div>
    </div><!-- /paper -->
@endsection

@section('scripts')
    @include('Backoffice.purchase-reconciliation.partials._scripts')
@endsection