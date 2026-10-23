<style>
    /* ── Reset & Base ── */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: 'Helvetica', 'Arial', 'DejaVu Sans', sans-serif;
        font-size: 10px;
        color: #1a1a1a;
        background: #fff;
        margin: 15px 20px;
        line-height: 1.5;
    }

    /* ── Header ── */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        margin-bottom: 6px;
    }
    .header-table td {
        border: none;
        vertical-align: top;
        padding: 0;
    }
    .header-left {
        width: 60%;
        display: flex;
        align-items: center;
    }
    .header-left .logo {
        max-height: 55px;
        max-width: 80px;
        margin-right: 12px;
        object-fit: contain;
    }
    .firm-details {
        display: inline-block;
    }
    .firm-name {
        font-size: 18px;
        font-weight: 700;
        color: #1a3a5c;
        letter-spacing: 0.5px;
    }
    .firm-tagline {
        font-size: 10px;
        color: #555;
        font-style: italic;
        margin-top: 1px;
    }
    .firm-address {
        font-size: 9px;
        color: #333;
        margin-top: 2px;
    }
    .firm-contact {
        font-size: 8px;
        color: #666;
        margin-top: 3px;
        line-height: 1.6;
    }
    .firm-contact span {
        margin-right: 10px;
    }

    .header-right {
        width: 40%;
        text-align: right;
        vertical-align: top;
    }
    .doc-title {
        font-size: 14px;
        font-weight: 700;
        color: #1a3a5c;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .meta-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        font-size: 9px;
        margin-top: 2px;
    }
    .meta-table td {
        border: none;
        padding: 1px 0;
    }
    .meta-table td:first-child {
        font-weight: 600;
        color: #444;
        width: 45%;
    }
    .meta-table td:last-child {
        font-weight: 500;
        color: #1a1a1a;
        text-align: left;
    }

    .header-divider {
        border-bottom: 2px solid #1a3a5c;
        margin: 4px 0 10px 0;
        width: 100%;
    }

    /* ── Section Titles ── */
    .section-title {
        background: #1a3a5c;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        margin: 14px 0 6px 0;
        border-radius: 0;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .sub-section-title {
        font-size: 10px;
        font-weight: 700;
        color: #1a3a5c;
        padding: 3px 0 3px 6px;
        margin: 10px 0 4px 0;
        border-bottom: 1px solid #ccc;
        text-transform: uppercase;
    }

    /* ── Tables ── */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9px;
        margin-bottom: 8px;
    }
    th, td {
        border: 1px solid #d0d0d0;
        padding: 3px 6px;
        vertical-align: middle;
    }
    th {
        background: #f0f2f5;
        font-weight: 700;
        font-size: 8px;
        text-transform: uppercase;
        color: #1a3a5c;
        text-align: left;
    }

    /* ── Alignment ── */
    .text-left   { text-align: left; }
    .text-center { text-align: center; }
    .text-right  { text-align: right; }
    .amt {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: 500;
    }
    .ctr { text-align: center; }

    /* ── Row Types ── */
    .row-grandtotal {
        background: #f4f6fa;
        font-weight: 700;
        border-top: 2px solid #1a3a5c;
    }
    .row-subtotal {
        background: #f9fafc;
        font-weight: 700;
        border-top: 1px dashed #999;
    }
    .row-final {
        background: #1a3a5c;
        color: #ffdd44;
        font-weight: 700;
        font-size: 11px;
    }
    .row-final td {
        border-color: #1a3a5c;
        padding: 5px 6px;
    }
    .row-less {
        color: #b22222;
    }
    .row-section {
        background: #1a3a5c;
        color: #fff;
        font-weight: 700;
        font-size: 10px;
    }
    .row-section td {
        border-color: #1a3a5c;
        padding: 4px 6px;
    }
    .row-normal:nth-child(even) {
        background: #fafafa;
    }

    /* ── Page Breaks ── */
    .page-break {
        page-break-before: always;
    }
    .page-break-after {
        page-break-after: always;
    }
    .no-break {
        page-break-inside: avoid;
    }

    /* ── Signature / Verification ── */
    .signature-box {
        margin-top: 25px;
        border-top: 1px solid #aaa;
        padding-top: 12px;
        font-size: 9px;
    }
    .signature-box .left {
        float: left;
        width: 48%;
    }
    .signature-box .right {
        float: right;
        width: 48%;
        text-align: right;
    }
    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    /* ── Barcode ── */
    .barcode-container {
        text-align: center;
        margin: 5px 0;
        padding: 6px;
        border: 1px dashed #aaa;
        background: #f9f9f9;
    }
    .barcode-container img {
        max-width: 180px;
        height: auto;
    }

    /* ── Footer ── */
    .footer-divider {
        border-top: 1px solid #ccc;
        margin: 8px 0 4px 0;
    }
    .footer-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        font-size: 8px;
        color: #888;
        margin: 0;
    }
    .footer-table td {
        border: none;
        padding: 2px 0;
    }
    .footer-left {
        text-align: left;
    }
    .footer-center {
        text-align: center;
    }
    .footer-right {
        text-align: right;
    }

    /* ── Misc Helpers ── */
    .label { font-weight: 700; color: #333; }
    .text-muted { color: #999; font-size: 8px; }
    .mt-10 { margin-top: 10px; }
    .mb-10 { margin-bottom: 10px; }
</style>