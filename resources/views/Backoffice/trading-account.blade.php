@extends('layouts.Backoffice')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ===== CLAUDE PREMIUM ENTERPRISE TRADING ACCOUNT – NO SIDEBAR/TOPBAR ===== */
:root {
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
  --font-sans: 'Inter', system-ui, sans-serif;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  background: var(--off-white);
  font-family: var(--font-sans);
  color: #1a2332;
}

/* MAIN CONTENT – full width, no sidebar/topbar */
#main {
  overflow-y: auto;
  background: var(--off-white);
  padding: 20px 24px;
  min-height: 100vh;
}

/* PAPER WRAPPER */
.erp-wrap {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}

.paper {
  flex: 1;
  min-width: 0;
  background: #fff;
  border: 2px solid #4a6fa5;
  position: relative;
  font-family: var(--font-sans);
  border-radius: 0px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.paper-inner {
  position: relative;
  z-index: 2;
}

/* Watermark */
.wm {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) rotate(-35deg);
  font-size: 80px;
  font-weight: 700;
  color: rgba(180, 200, 230, 0.15);
  pointer-events: none;
  z-index: 1;
  letter-spacing: 8px;
  text-transform: uppercase;
  user-select: none;
  white-space: nowrap;
  font-family: var(--font-sans);
}

.pgnum {
  position: absolute;
  bottom: 6px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 11px;
  color: #546e7a;
  font-family: var(--font-sans);
  z-index: 5;
  font-weight: 600;
}

/* Statement header rows */
.sh-row {
  background: #1a3a6c;
  color: #fff;
  text-align: center;
  padding: 4px 6px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: .5px;
  border-bottom: 1px solid #2a5aa0;
  font-family: var(--font-sans);
}

.sh-row.light {
  background: #d6e4f7;
  color: #0d2a5e;
  font-size: 11px;
  font-weight: 600;
  border-bottom: 1px solid #4a6fa5;
}

.sh-row.title {
  font-size: 13.5px;
  letter-spacing: 2px;
  background: #1a3a6c;
  padding: 5px 6px;
}

.sh-row.period {
  background: #d6e4f7;
  color: #0d2a5e;
  font-size: 10.5px;
  font-weight: 500;
  border-bottom: 2px solid #4a6fa5;
}

.sh-input {
  background: transparent;
  border: none;
  outline: none;
  color: #0d2a5e;
  font-family: var(--font-sans);
  font-size: 11px;
  font-weight: 600;
  text-align: center;
  width: 100%;
  cursor: text;
}

.sh-input:focus {
  background: #fffde7;
}

/* MAIN TABLE – Tally style with multiple amount columns */
.ta-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 11.5px;
  table-layout: fixed;
}

.ta-table td,
.ta-table th {
  border: 1px solid #4a6fa5;
  padding: 0;
}

.col-head {
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

.col-subhead {
  background: #2a5aa0;
  color: #c8ddf0;
  text-align: center;
  font-size: 10px;
  font-weight: 600;
  padding: 3px 4px;
  font-family: var(--font-mono);
}

.sec-head td {
  background: #d6e4f7;
  color: #0d2a5e;
  font-weight: 700;
  font-size: 11px;
  padding: 4px 8px;
  font-family: var(--font-sans);
  letter-spacing: .3px;
}

.sec-head .sec-lnk {
  color: #0d47a1;
  font-weight: 700;
  text-decoration: underline;
  cursor: pointer;
  font-style: italic;
}

.dr td {
  vertical-align: middle;
}

.dr:nth-child(odd) td {
  background: #f7faff;
}

.dr:nth-child(even) td {
  background: #ffffff;
}

.dr:hover td {
  background: #e3f0fd !important;
}

.td-part {
  padding: 3px 8px;
  font-size: 11.5px;
  color: #1a2332;
  display: flex;
  align-items: center;
  gap: 4px;
}

.td-part .bullet {
  color: #2a5aa0;
  font-size: 9px;
  flex-shrink: 0;
}

.part-input {
  background: transparent;
  border: none;
  outline: none;
  font-family: var(--font-sans);
  font-size: 11.5px;
  color: #1a2332;
  width: 100%;
  cursor: text;
}

.part-input:focus {
  background: #fffde7;
}

.td-amt {
  font-family: var(--font-mono);
  font-size: 11.5px;
  text-align: right;
  padding: 3px 6px;
  color: #1a2332;
  cursor: text;
  vertical-align: middle;
  white-space: nowrap;
}

.td-amt:hover {
  background: #fffbf0 !important;
}

.td-amt:focus {
  background: #fffde7 !important;
  outline: none;
  border-color: var(--gold) !important;
}

.td-amt.sub {
  color: #1565c0;
  font-weight: 700;
  border-top: 2px solid #4a6fa5;
}

.td-amt.blank {
  background: #eef3fa !important;
}

.td-amt.cr-sub {
  color: #1565c0;
  font-weight: 700;
}

.td-rs {
  font-family: var(--font-mono);
  font-size: 11px;
  color: #455a64;
  text-align: center;
  padding: 3px 2px;
  width: 24px;
  background: inherit;
}

.gp-row td {
  background: #c8e6c9;
  color: #1b5e20;
  font-weight: 700;
  font-size: 11.5px;
  padding: 4px 8px;
  font-family: var(--font-sans);
}

.gp-row .td-amt {
  color: #1b5e20;
  background: #c8e6c9 !important;
  font-weight: 700;
}

.gp-row .td-rs {
  color: #1b5e20;
  background: #c8e6c9 !important;
}

.gp-lnk {
  color: #1b5e20;
  font-weight: 700;
  text-decoration: underline;
  cursor: pointer;
  font-style: italic;
}

.gt-row td {
  background: #1a3a6c;
  color: #fff;
  font-weight: 700;
  font-size: 12px;
  padding: 5px 8px;
  font-family: var(--font-mono);
}

.gt-row .td-rs {
  color: #fff;
  background: #1a3a6c !important;
}

/* Signature table */
.sig-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 10px;
  margin-top: 0;
}

.sig-table td {
  border: 1px solid #4a6fa5;
  padding: 4px 8px;
  vertical-align: top;
}

.sig-label {
  font-weight: 700;
  font-size: 10px;
  color: #1a2332;
  font-family: var(--font-sans);
}

.sig-val {
  font-size: 10px;
  color: #455a64;
  margin-top: 2px;
}

.sig-input {
  background: transparent;
  border: none;
  outline: none;
  font-family: var(--font-sans);
  font-size: 10px;
  color: #1a2332;
  width: 100%;
  cursor: text;
}

.sig-input:focus {
  background: #fffde7;
}

.ack-label {
  font-size: 9px;
  font-weight: 700;
  color: #1a3a6c;
  font-family: var(--font-sans);
}

.ack-val {
  font-family: var(--font-mono);
  font-size: 9.5px;
  color: #1a2332;
}

/* RIGHT PANEL – KPI Cards */
.right-panel {
  width: 260px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.rp-card {
  border: 1px solid #4a6fa5;
  background: #fff;
  font-family: var(--font-sans);
  border-radius: 4px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.rp-head {
  background: #1a3a6c;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 6px 8px;
  text-align: center;
  letter-spacing: .5px;
  text-transform: uppercase;
}

.rp-body {
  padding: 0;
}

.rp-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  border-bottom: 1px solid #b0bec5;
}

.rp-row:last-child {
  border-bottom: none;
}

.rp-lbl {
  padding: 5px 8px;
  font-size: 10px;
  color: #455a64;
  border-right: 1px solid #b0bec5;
  font-weight: 500;
}

.rp-val {
  padding: 5px 8px;
  font-family: var(--font-mono);
  font-size: 10.5px;
  font-weight: 700;
  text-align: right;
  color: #1a2332;
  cursor: text;
}

.rp-val:hover {
  background: #fffbf0;
}

.rp-val:focus {
  background: #fffde7;
  outline: none;
}

.rp-val.red-bg {
  background: #ffcdd2;
  color: #b71c1c;
}

.rp-val.green-bg {
  background: #c8e6c9;
  color: #1b5e20;
}

.rp-val.yellow-bg {
  background: #fff9c4;
  color: #f57f17;
}

.rp-gp-ratio {
  padding: 8px;
  text-align: center;
  font-family: var(--font-mono);
  font-size: 18px;
  font-weight: 700;
  color: #1b5e20;
  background: #e8f5e9;
}

.rp-gp-label {
  font-size: 9px;
  color: #388e3c;
  font-family: var(--font-sans);
  margin-top: 4px;
}

.rp-mini-head {
  background: #2a5aa0;
  color: #fff;
  font-size: 9.5px;
  font-weight: 700;
  padding: 4px 6px;
  text-align: center;
  font-family: var(--font-sans);
}

.rp-mini-val {
  text-align: center;
  padding: 8px;
  font-family: var(--font-mono);
  font-size: 14px;
  font-weight: 700;
  color: #1a2332;
}

/* TOOLBAR (compact, within main area) */
.erp-toolbar {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-bottom: 18px;
  background: transparent;
}

.ebtn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  border: 1px solid var(--border-light);
  background: white;
  color: #1a2332;
  transition: all 0.15s;
  font-family: var(--font-sans);
  box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}

.ebtn i {
  font-size: 12px;
}

.ebtn:hover {
  background: #e8edf4;
  border-color: #b0bec5;
  transform: translateY(-1px);
}

.ebtn-green {
  background: #00695c;
  border-color: #00695c;
  color: white;
}

.ebtn-green:hover {
  background: #004d40;
}

/* Status bar (bottom) */
#statusbar {
  background: var(--navy);
  color: #5a7a9a;
  display: flex;
  align-items: center;
  padding: 0 16px;
  gap: 0;
  font-size: 9.5px;
  font-family: var(--font-mono);
  border-top: 1px solid var(--navy-border);
  height: 28px;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1000;
}

.sb-item {
  padding: 0 12px;
  border-right: 1px solid var(--navy-border);
  display: flex;
  align-items: center;
  gap: 5px;
  height: 100%;
}

.sb-item:first-child {
  padding-left: 0;
}

.sb-item.act {
  color: #64b5f6;
}

.sb-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #00897b;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.sb-key {
  background: var(--navy-light);
  border: 1px solid var(--navy-border);
  border-radius: 2px;
  padding: 0 5px;
  font-size: 8px;
  color: #7a9ab0;
  font-family: monospace;
}

.sb-spacer {
  flex: 1;
}

.sb-time {
  padding: 0 10px;
  color: #42a5f5;
  font-weight: 600;
}

/* Loading spinner */
.loading-spinner {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 9999;
}

/* Toast */
.toast-notification {
  position: fixed;
  bottom: 40px;
  right: 20px;
  z-index: 9998;
  animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* scrollbar */
::-webkit-scrollbar {
  width: 5px;
  height: 5px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: #b0bec5;
  border-radius: 3px;
}

/* print */
@media print {
  .erp-toolbar, #statusbar, .right-panel {
    display: none !important;
  }
  .paper {
    border: 1px solid #000;
    box-shadow: none;
  }
  .wm {
    color: rgba(0,0,0,0.05) !important;
  }
  body {
    background: white;
    padding: 0;
  }
  #main {
    padding: 0;
  }
}

[contenteditable="true"]:focus {
  outline: 2px solid #f9a825;
  outline-offset: -1px;
}
</style>
@endsection

@section('content')
<div id="main">
  <!-- Toolbar (compact, within content area) with Add Row button -->
  <div class="erp-toolbar">
    <button class="ebtn" onclick="saveToDatabase()"><i class="bi bi-cloud-arrow-up"></i> Save Draft</button>
    <button class="ebtn" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print</button>
    <button class="ebtn" onclick="exportToPDF()"><i class="bi bi-file-pdf-fill"></i> PDF</button>
    <button class="ebtn ebtn-green" id="finalizeBtn" onclick="finalizeAccount()"><i class="bi bi-check2-circle"></i> Finalize</button>
    <button class="ebtn" onclick="resetAccount()"><i class="bi bi-arrow-repeat"></i> Reset</button>
    <button class="ebtn" onclick="refreshData()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
    <button class="ebtn" onclick="addRow()"><i class="bi bi-plus-circle"></i> Add Row</button>
  </div>

  <div class="erp-wrap">
    <!-- PAPER STATEMENT -->
    <div class="paper">
      <div class="wm" id="watermark">DRAFT</div>
      <div class="paper-inner">
        <!-- Header rows -->
        <div class="sh-row">IN THE BOOKS OF</div>
        <div class="sh-row light" style="padding:2px 6px;">
          <input class="sh-input" id="companyName" value="M/S SHARMA TRADING COMPANY" style="font-weight:700;font-size:12px;">
        </div>
        <div class="sh-row" style="font-size:10px;font-weight:500;padding:2px 6px;background:#2a5aa0;">
          TRADE CODE &nbsp;|&nbsp;
          <input class="sh-input" id="tradeCode" value="STC / 2024-25" style="display:inline;width:120px;font-size:10px;font-weight:500;color:#fff;">
        </div>
        <div class="sh-row light" style="padding:2px 6px;">
          <input class="sh-input" id="tradeName" value="0 &nbsp;&nbsp; TRADE CODE &nbsp;&nbsp; 0 0" style="font-size:10px;font-weight:500;">
        </div>
        <div class="sh-row title">TRADING ACCOUNT</div>
        <div class="sh-row period">
          FOR THE FINANCIAL YEAR ENDED &nbsp;
          <input class="sh-input" id="fyEnd" value="31-MAR-2025" style="display:inline;width:100px;font-size:10.5px;color:#0d2a5e;font-weight:700;">
          &nbsp;|&nbsp; AY &nbsp;
          <input class="sh-input" id="ayVal" value="2025-26" style="display:inline;width:70px;font-size:10.5px;color:#0d2a5e;font-weight:700;">
        </div>

        <!-- Main Trading Table (Claude structure) -->
        <table class="ta-table" id="tradingTable">
          <colgroup>
            <col style="width:28%"><col style="width:24px"><col style="width:9%"><col style="width:24px"><col style="width:9%">
            <col style="width:28%"><col style="width:24px"><col style="width:9%"><col style="width:24px"><col style="width:9%">
          </colgroup>
          <thead>
            <tr>
              <th class="col-head" colspan="5" style="border-right:2px solid #fff;">PARTICULARS &nbsp;&nbsp; AMOUNT &nbsp;&nbsp;&nbsp; AMOUNT</th>
              <th class="col-head" colspan="5">PARTICULARS &nbsp;&nbsp; AMOUNT &nbsp;&nbsp;&nbsp; AMOUNT</th>
            </tr>
            <tr>
              <th class="col-head" style="background:#0d2a5e;">PARTICULARS</th>
              <th class="col-subhead">Rs</th>
              <th class="col-subhead">AMOUNT</th>
              <th class="col-subhead">Rs</th>
              <th class="col-subhead" style="border-right:2px solid #fff;">AMOUNT</th>
              <th class="col-head" style="background:#0d2a5e;">PARTICULARS</th>
              <th class="col-subhead">Rs</th>
              <th class="col-subhead">AMOUNT</th>
              <th class="col-subhead">Rs</th>
              <th class="col-subhead">AMOUNT</th>
            </tr>
          </thead>
          <tbody id="tBody">
            <!-- Section: Operating Expenses | Sales -->
            <tr class="sec-head">
              <td colspan="5" style="border-right:2px solid #4a6fa5;"><span class="sec-lnk">Operating expenses</span></td>
              <td colspan="5"><span class="sec-lnk">♦ SALES</span></td>
            </tr>

            <!-- Row 1: Opening Stock | Sales as per GST -->
            <tr class="dr">
              <td><div class="td-part"><span class="bullet">♦</span><span> OPENING STOCK (as per last A/c)</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="openingStock1" oninput="calc()">-</td>
              <td class="td-rs">₹</td>
              <td class="td-amt sub editable" contenteditable="true" id="openingStockT" style="border-right:2px solid #4a6fa5;" oninput="calc()">-</td>
              <td><div class="td-part"><span>SALES AS PER GST</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="salesGST1" oninput="calc()">38,20,500</td>
              <td class="td-rs">₹</td>
              <td class="td-amt cr-sub editable" contenteditable="true" id="salesGSTT" oninput="calc()">-</td>
            </tr>

            <!-- Row 2: Registered Purchases | Sales Without GST -->
            <tr class="dr">
              <td><div class="td-part"><span class="bullet">♦</span><span> REGISTER PURCHASES</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="regPurch1" oninput="calc()">24,60,000</td>
              <td class="td-rs"></td>
              <td class="td-amt blank" style="border-right:2px solid #4a6fa5;"></td>
              <td><div class="td-part"><span>SALES WITHOUT GST</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="salesNoGST1" oninput="calc()">4,80,000</td>
              <td class="td-rs">₹</td>
              <td class="td-amt cr-sub editable" contenteditable="true" id="salesNoGSTT" oninput="calc()">-</td>
            </tr>

            <!-- Row 3: Unregistered Purchases | Sales Return -->
            <tr class="dr">
              <td><div class="td-part"><span class="bullet">♦</span><span> UNREGISTER PURCHASES</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="unregPurch1" oninput="calc()">3,25,000</td>
              <td class="td-rs">₹</td>
              <td class="td-amt sub editable" contenteditable="true" id="unregPurchT" style="border-right:2px solid #4a6fa5;" oninput="calc()">-</td>
              <td><div class="td-part"><span>SALES RETURN</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="salesReturn1" oninput="calc()">1,20,000</td>
              <td class="td-rs">₹</td>
              <td class="td-amt cr-sub editable" contenteditable="true" id="salesReturnT" oninput="calc()">-</td>
            </tr>

            <!-- Row 4: Debit Note | Post Sale Discount Received -->
            <tr class="dr">
              <td><div class="td-part"><span class="bullet">♦</span><span> DEBIT NOTE</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="debitNote1" oninput="calc()">45,000</td>
              <td class="td-rs">₹</td>
              <td class="td-amt sub editable" contenteditable="true" id="debitNoteT" style="border-right:2px solid #4a6fa5;" oninput="calc()">-</td>
              <td><div class="td-part"><span>POST SALE DISCOUNT</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="postDisc1" oninput="calc()">65,000</td>
              <td class="td-rs">₹</td>
              <td class="td-amt cr-sub editable" contenteditable="true" id="postDiscT" oninput="calc()">RECEIVED</td>
            </tr>

            <!-- Row 5: Unloading Charges | Pre Sale Discount Received -->
            <tr class="dr">
              <td><div class="td-part"><span class="bullet">♦</span><span> UNLOADING CHARGES</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="unloading1" oninput="calc()">32,000</td>
              <td class="td-rs"></td>
              <td class="td-amt blank" style="border-right:2px solid #4a6fa5;"></td>
              <td><div class="td-part"><span>PRE SALE DISCOUNT</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="preDisc1" oninput="calc()">28,000</td>
              <td class="td-rs">₹</td>
              <td class="td-amt cr-sub editable" contenteditable="true" id="preDiscT" oninput="calc()">RECEIVED</td>
            </tr>

            <!-- Row 6: Wages | Closing Stock -->
            <tr class="dr">
              <td><div class="td-part"><span class="bullet">♦</span><span> WAGES</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="wages1" oninput="calc()">1,85,000</td>
              <td class="td-rs"></td>
              <td class="td-amt blank" style="border-right:2px solid #4a6fa5;"></td>
              <td><div class="td-part"><span class="bullet">♦</span><span> CLOSING STOCK</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="closingStock1" oninput="calc()">12,60,000</td>
              <td class="td-rs">₹</td>
              <td class="td-amt cr-sub" id="closingStockT">-</td>
            </tr>

            <!-- Row 7: Fuel Expenses -->
            <tr class="dr">
              <td><div class="td-part"><span class="bullet">♦</span><span> FUEL EXPENSES</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="fuel1" oninput="calc()">68,000</td>
              <td class="td-rs"></td>
              <td class="td-amt blank" style="border-right:2px solid #4a6fa5;"></td>
              <td colspan="5"></td>
            </tr>

            <!-- Row 8: Carriage Inward -->
            <tr class="dr">
              <td><div class="td-part"><span class="bullet">♦</span><span> CARRIAGE INWARD</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="carriage1" oninput="calc()">52,000</td>
              <td class="td-rs"></td>
              <td class="td-amt blank" style="border-right:2px solid #4a6fa5;"></td>
              <td colspan="5"></td>
            </tr>

            <!-- Row 9: Sale Platform -->
            <tr class="dr">
              <td><div class="td-part"><span class="bullet">♦</span><span> SALE PLATFORM</span></div></td>
              <td class="td-rs">₹</td>
              <td class="td-amt editable" contenteditable="true" id="platform1" oninput="calc()">1,24,000</td>
              <td class="td-rs">₹</td>
              <td class="td-amt sub editable" contenteditable="true" id="platformT" style="border-right:2px solid #4a6fa5;" oninput="calc()">-</td>
              <td colspan="5"></td>
            </tr>

            <!-- Gross Profit row -->
            <tr class="gp-row" id="gpRow">
              <td colspan="2" style="border-right:1px solid #4a6fa5;"><span class="gp-lnk">♦ GROSS PROFIT</span></td>
              <td class="td-rs" style="background:#c8e6c9;color:#1b5e20;">₹</td>
              <td class="td-amt" id="gpAmt1" style="background:#c8e6c9;color:#1b5e20;font-weight:700;border-right:1px solid #4a6fa5;">-</td>
              <td class="td-rs" style="background:#c8e6c9;color:#1b5e20;border-right:2px solid #4a6fa5;">₹</td>
              <td colspan="3" style="background:#c8e6c9;"></td>
              <td class="td-rs" style="background:#c8e6c9;color:#1b5e20;">₹</td>
              <td class="td-amt" id="gpAmt2" style="background:#c8e6c9;color:#1b5e20;font-weight:700;">-</td>
            </tr>

            <!-- Grand Total row -->
            <tr class="gt-row">
              <td style="font-family:var(--font-sans);font-size:11px;letter-spacing:.5px;text-transform:uppercase;font-weight:700;padding:5px 8px;" colspan="1">TOTAL</td>
              <td class="td-rs" style="background:#1a3a6c;color:#fff;">₹</td>
              <td class="td-amt" id="drGrandT1" style="background:#1a3a6c;color:#fff;font-weight:700;">-</td>
              <td class="td-rs" style="background:#1a3a6c;color:#fff;">₹</td>
              <td class="td-amt" id="drGrandT2" style="background:#1a3a6c;color:#a0c4ff;font-weight:700;border-right:2px solid #a0c4ff;">-</td>
              <td style="font-family:var(--font-sans);font-size:11px;letter-spacing:.5px;text-transform:uppercase;font-weight:700;padding:5px 8px;" colspan="1">TOTAL</td>
              <td class="td-rs" style="background:#1a3a6c;color:#fff;">₹</td>
              <td class="td-amt" id="crGrandT1" style="background:#1a3a6c;color:#fff;font-weight:700;">-</td>
              <td class="td-rs" style="background:#1a3a6c;color:#fff;">₹</td>
              <td class="td-amt" id="crGrandT2" style="background:#1a3a6c;color:#a0c4ff;font-weight:700;">-</td>
            </tr>
          </tbody>
        </table>

        <!-- Signature Table -->
        <table class="sig-table">
          <tr>
            <td style="width:22%"><div class="sig-label">THE SHARP</div><div class="sig-val"><input class="sig-input" id="auditStatus1" value="NON AUDITED"></div></td>
            <td style="width:22%"><div class="sig-label">THE SHARP</div><div class="sig-val"><input class="sig-input" id="auditStatus2" value="NON AUDITED"></div></td>
            <td style="width:33%"><div class="ack-label">ITR Acknowledgement Number :</div><div class="ack-val"><input class="sig-input" id="itrAck" placeholder="e.g. 987654321098765"></div></td>
            <td rowspan="3" style="text-align:center;vertical-align:middle;">
              <div class="sig-label" style="text-align:center;">Signature Of Chartered Accountant</div>
              <div style="height:45px;border-bottom:1px solid #546e7a;margin:8px 10px;"></div>
              <div style="font-size:9px;color:#546e7a;text-align:center;">R. Kumar, FCA &nbsp;|&nbsp; M.No. 123456</div>
            </td>
          </tr>
          <tr>
            <td><div class="sig-label">EDGE INDIA</div><div class="sig-val"><input class="sig-input" id="edgeIndia" value="NON AUDITED"></div></td>
            <td><div class="sig-label"></div><div class="sig-val"><input class="sig-input" id="rdis1" value="NO"></div></td>
            <td><div class="ack-label">Audit Acknowledgement Number :</div><div class="ack-val"><input class="sig-input" id="auditAck1" placeholder="e.g. 123456789012345"></div></td>
          </tr>
          <tr>
            <td><div class="sig-label">DATE OF AUDIT :</div><div class="sig-val"><input class="sig-input" id="auditDate" value="31-03-2025"></div></td>
            <td><div class="ack-label">RDIS</div><div class="ack-val"><input class="sig-input" id="rdis2" value="NO"></div></td>
            <td><div class="ack-label">Audit Acknowledgement Number :</div><div class="ack-val"><input class="sig-input" id="auditAck2" placeholder="Audit Ack. No."></div></td>
          </tr>
        </table>
      </div>
      <div class="pgnum">4</div>
    </div>

    <!-- RIGHT PANEL (KPIs) -->
    <div class="right-panel">
      <div class="rp-card">
        <div class="rp-head">Net Profit Percent</div>
        <div class="rp-body">
          <div class="rp-row"><div class="rp-lbl">NET PROFIT</div><div class="rp-val red-bg" id="rp-np" contenteditable="true">₹5,12,4281.00</div></div>
          <div class="rp-row"><div class="rp-lbl">PURCHASE</div><div class="rp-val red-bg" id="rp-pur">₹5,12,4281.00</div></div>
          <div class="rp-row"><div class="rp-lbl">GROSS PROFIT RATIO</div><div class="rp-val yellow-bg" id="rp-gpr">##DIV/0!</div></div>
        </div>
        <div style="height:36px;margin:6px;border:1.5px solid #ef9a9a;border-radius:3px;background:#fff9f9;"></div>
      </div>

      <div class="rp-card">
        <div class="rp-mini-head">TRADING expenses</div>
        <div class="rp-mini-val" id="rp-tradexp">0.00</div>
        <div style="text-align:center;padding:3px;font-size:9px;color:#b71c1c;font-family:var(--font-sans);font-weight:700;border-top:1px solid #b0bec5;" id="rp-diverr">##DIV/0!</div>
      </div>

      <div class="rp-card">
        <div class="rp-head">GP Ratio</div>
        <div style="text-align:center;padding:10px 6px;">
          <svg width="80" height="80" viewBox="0 0 80 80">
            <circle cx="40" cy="40" r="32" fill="none" stroke="#e0e7ef" stroke-width="8"/>
            <circle cx="40" cy="40" r="32" fill="none" stroke="#00897b" stroke-width="8" stroke-dasharray="201.1" stroke-dashoffset="70" stroke-linecap="round" transform="rotate(-90 40 40)" id="gpRingArc"/>
          </svg>
          <div class="rp-gp-ratio" id="rpGpRatio">0.00%</div>
          <div class="rp-gp-label">Gross Profit / Net Sales</div>
        </div>
      </div>

      <div class="rp-card">
        <div class="rp-head">Closing Stock</div>
        <div class="rp-body">
          <div class="rp-row"><div class="rp-lbl">Value</div><div class="rp-val green-bg" id="rp-cs">₹12,60,000</div></div>
          <div class="rp-row"><div class="rp-lbl">Opening</div><div class="rp-val" id="rp-os">₹0</div></div>
        </div>
      </div>

      <div class="rp-card">
        <div class="rp-head">Statement Status</div>
        <div class="rp-body">
          <div class="rp-row"><div class="rp-lbl">Status</div><div class="rp-val" id="rp-status" style="color:#f57f17;font-weight:700;">DRAFT</div></div>
          <div class="rp-row"><div class="rp-lbl">FY</div><div class="rp-val" style="color:#1565c0;">2024–25</div></div>
          <div class="rp-row"><div class="rp-lbl">AY</div><div class="rp-val" style="color:#1565c0;">2025–26</div></div>
          <div class="rp-row"><div class="rp-lbl">Audit</div><div class="rp-val" style="color:#b71c1c;">Non Audited</div></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Status Bar -->
<footer id="statusbar">
  <div class="sb-item act"><i class="bi bi-calendar3"></i>FY 2024–25</div>
  <div class="sb-item"><i class="bi bi-file-earmark-text"></i>Trading Account</div>
  <div class="sb-item"><span class="sb-dot"></span>Auto-saved</div>
  <div class="sb-item"><i class="bi bi-person-fill"></i>R. Kumar, CA</div>
  <div class="sb-item" id="sbPage">Page: 4</div>
  <div class="sb-spacer"></div>
  <div class="sb-item">
    <span class="sb-key">Tab</span>Next &nbsp;
    <span class="sb-key">F5</span>Refresh &nbsp;
    <span class="sb-key">Ctrl+S</span>Save &nbsp;
    <span class="sb-key">Ctrl+P</span>Print
  </div>
  <div class="sb-time" id="sbTime">00:00:00</div>
</footer>

<!-- Loading Spinner -->
<div class="loading-spinner" id="loadingSpinner">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let csrfToken = '{{ csrf_token() }}';

  // Helper functions
  function parseAmt(id) {
    let el = document.getElementById(id);
    if (!el) return 0;
    let s = (el.innerText || el.textContent || '').replace(/[₹,\s]/g, '').replace('RECEIVED','').replace('##DIV/0!','');
    let neg = s.includes('(-)') || s.startsWith('-');
    s = s.replace('(-)', '').replace(/^-/, '');
    let v = parseFloat(s) || 0;
    return neg ? -v : v;
  }

  function fmtINR(n) {
    if (isNaN(n) || n === null) return '-';
    let neg = n < 0;
    n = Math.abs(n);
    if (n === 0) return '-';
    let s = n.toFixed(0);
    let last3 = s.slice(-3);
    let rest = s.slice(0, s.length - 3);
    if (rest) last3 = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + last3;
    return (neg ? '(-) ' : '') + last3;
  }

  function setCell(id, val) {
    let el = document.getElementById(id);
    if (el) el.innerText = val === 0 ? '-' : fmtINR(val);
  }

  // Main calculation logic (front-end only)
  function calc() {
    // DEBIT SIDE
    let os    = parseAmt('openingStockT') || parseAmt('openingStock1');
    let rp    = parseAmt('regPurch1');
    let up    = parseAmt('unregPurch1');
    let dn    = parseAmt('debitNote1');
    let ul    = parseAmt('unloading1');
    let wg    = parseAmt('wages1');
    let fu    = parseAmt('fuel1');
    let ca    = parseAmt('carriage1');
    let pl    = parseAmt('platform1');

    let purchaseSub = rp + up + dn;
    setCell('unregPurchT', purchaseSub);
    setCell('debitNoteT', purchaseSub);

    let dirExp = ul + wg + fu + ca + pl;
    setCell('platformT', dirExp);

    let totalDebit = os + purchaseSub + dirExp;

    // CREDIT SIDE
    let sgst  = parseAmt('salesGST1');
    let sno   = parseAmt('salesNoGST1');
    let sr    = parseAmt('salesReturn1');
    let pd    = parseAmt('postDisc1');
    let prd   = parseAmt('preDisc1');
    let cs    = parseAmt('closingStock1');

    let netSales = sgst + sno - sr + pd + prd;
    setCell('salesGSTT', sgst);
    setCell('salesNoGSTT', netSales);
    document.getElementById('salesReturnT').innerText = fmtINR(sr);

    let totalCredit = netSales + cs;

    // Gross Profit
    let gp = totalCredit - totalDebit;

    if (gp >= 0) {
      setCell('gpAmt1', gp);
      setCell('gpAmt2', gp);
      let grand = totalDebit + gp;
      setCell('drGrandT1', totalDebit);
      setCell('drGrandT2', grand);
      setCell('crGrandT1', totalCredit);
      setCell('crGrandT2', grand);
    } else {
      document.getElementById('gpAmt1').innerText = 'LOSS: ' + fmtINR(-gp);
      document.getElementById('gpAmt2').innerText = fmtINR(-gp);
      let grand = totalCredit + (-gp);
      setCell('drGrandT1', totalDebit);
      setCell('drGrandT2', grand);
      setCell('crGrandT1', totalCredit);
      setCell('crGrandT2', grand);
    }

    // Right panel updates
    document.getElementById('rp-pur').innerText = '₹' + fmtINR(purchaseSub);
    document.getElementById('rp-np').innerText  = '₹' + fmtINR(gp * 0.66);
    document.getElementById('rp-cs').innerText  = '₹' + fmtINR(cs);
    document.getElementById('rp-os').innerText  = '₹' + fmtINR(os);

    let tradExp = dirExp + ul;
    document.getElementById('rp-tradexp').innerText = fmtINR(tradExp);

    if (netSales > 0) {
      let gpRatio = (gp / netSales) * 100;
      document.getElementById('rp-gpr').innerText = gpRatio.toFixed(2) + '%';
      document.getElementById('rp-gpr').className = 'rp-val green-bg';
      document.getElementById('rpGpRatio').innerText = gpRatio.toFixed(2) + '%';
      document.getElementById('rp-diverr').innerText = gpRatio.toFixed(2) + '%';
      document.getElementById('rp-diverr').style.color = gpRatio > 0 ? '#1b5e20' : '#b71c1c';
      let arc = 201.1;
      let offset = arc - Math.min(Math.max(gpRatio / 100, 0), 1) * arc;
      document.getElementById('gpRingArc').setAttribute('stroke-dashoffset', offset.toFixed(1));
    } else {
      document.getElementById('rp-gpr').innerText = '##DIV/0!';
      document.getElementById('rp-gpr').className = 'rp-val yellow-bg';
      document.getElementById('rpGpRatio').innerText = '0.00%';
      document.getElementById('rp-diverr').innerText = '##DIV/0!';
    }
  }

  // Function to add a new row (debit side only, before gross profit row)
  let addRowCount = 0;
  function addRow() {
    addRowCount++;
    const tbody = document.getElementById('tBody');
    const gpRow = document.getElementById('gpRow');
    if (!tbody || !gpRow) return;

    const newRowId = `custom_row_${addRowCount}`;
    const newRow = document.createElement('tr');
    newRow.className = 'dr';
    newRow.innerHTML = `
      <td><div class="td-part"><span class="bullet">♦</span><span contenteditable="true" class="part-input" style="outline:none;">New Item</span></div></td>
      <td class="td-rs">₹</td>
      <td class="td-amt editable" contenteditable="true" id="${newRowId}_amt" oninput="calc()">-</td>
      <td class="td-rs">₹</td>
      <td class="td-amt sub editable" contenteditable="true" id="${newRowId}_total" style="border-right:2px solid #4a6fa5;" oninput="calc()">-</td>
      <td colspan="5"></td>
    `;
    tbody.insertBefore(newRow, gpRow);
    calc();
    showToast('New row added', 'success');
  }

  // Backend integration (Laravel routes, CSRF protected)
  async function saveToDatabase() {
    showLoading();
    try {
      const data = {
        opening_stock: parseAmt('openingStock1'),
        registered_purchases: parseAmt('regPurch1'),
        unregistered_purchases: parseAmt('unregPurch1'),
        debit_note: parseAmt('debitNote1'),
        unloading_charges: parseAmt('unloading1'),
        wages_labour: parseAmt('wages1'),
        fuel_expenses: parseAmt('fuel1'),
        carriage_inward: parseAmt('carriage1'),
        platform_charges: parseAmt('platform1'),
        sales_gst: parseAmt('salesGST1'),
        sales_no_gst: parseAmt('salesNoGST1'),
        sales_return: parseAmt('salesReturn1'),
        post_sale_discount: parseAmt('postDisc1'),
        pre_sale_discount: parseAmt('preDisc1'),
        closing_stock: parseAmt('closingStock1'),
      };
      const response = await fetch('{{ route("backoffice.computations.trading-account.update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await response.json();
      if (result.success) {
        showToast('All changes saved successfully', 'success');
      } else {
        showToast(result.message || 'Error saving data', 'error');
      }
    } catch (error) {
      console.error('Error saving:', error);
      showToast('Error saving data', 'error');
    }
    hideLoading();
  }

  async function finalizeAccount() {
    if (!confirm('Are you sure you want to finalize the Trading Account? This action cannot be undone.')) return;
    showLoading();
    try {
      const response = await fetch('{{ route("backoffice.computations.trading-account.finalize") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
      });
      const result = await response.json();
      if (result.success) {
        document.getElementById('watermark').style.display = 'none';
        document.getElementById('rp-status').innerText = 'FINALIZED';
        document.getElementById('rp-status').style.color = '#1b5e20';
        showToast('Trading Account finalized successfully!', 'success');
        document.getElementById('finalizeBtn').disabled = true;
      } else {
        showToast(result.message || 'Error finalizing account', 'error');
      }
    } catch (error) {
      console.error('Error finalizing:', error);
      showToast('Error finalizing account', 'error');
    }
    hideLoading();
  }

  async function resetAccount() {
    if (!confirm('Reset will restore default values. This action cannot be undone. Continue?')) return;
    showLoading();
    try {
      const response = await fetch('{{ route("backoffice.computations.trading-account.reset") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
      });
      const result = await response.json();
      if (result.success) {
        await loadData();
        showToast('Account reset to default values', 'success');
        document.getElementById('finalizeBtn').disabled = false;
      } else {
        showToast(result.message || 'Error resetting account', 'error');
      }
    } catch (error) {
      console.error('Error resetting:', error);
      showToast('Error resetting account', 'error');
    }
    hideLoading();
  }

  async function loadData() {
    showLoading();
    try {
      const response = await fetch('{{ route("backoffice.computations.trading-account.data") }}', {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
      });
      const result = await response.json();
      if (result.success) {
        const a = result.account;
        document.getElementById('openingStock1').innerText = fmtINR(a.opening_stock);
        document.getElementById('regPurch1').innerText = fmtINR(a.registered_purchases);
        document.getElementById('unregPurch1').innerText = fmtINR(a.unregistered_purchases);
        document.getElementById('debitNote1').innerText = fmtINR(a.debit_note);
        document.getElementById('unloading1').innerText = fmtINR(a.unloading_charges);
        document.getElementById('wages1').innerText = fmtINR(a.wages_labour);
        document.getElementById('fuel1').innerText = fmtINR(a.fuel_expenses);
        document.getElementById('carriage1').innerText = fmtINR(a.carriage_inward);
        document.getElementById('platform1').innerText = fmtINR(a.platform_charges);
        document.getElementById('salesGST1').innerText = fmtINR(a.sales_gst);
        document.getElementById('salesNoGST1').innerText = fmtINR(a.sales_no_gst);
        document.getElementById('salesReturn1').innerText = fmtINR(a.sales_return);
        document.getElementById('postDisc1').innerText = fmtINR(a.post_sale_discount);
        document.getElementById('preDisc1').innerText = fmtINR(a.pre_sale_discount);
        document.getElementById('closingStock1').innerText = fmtINR(a.closing_stock);
        if (a.finalized) {
          document.getElementById('watermark').style.display = 'none';
          document.getElementById('rp-status').innerText = 'FINALIZED';
          document.getElementById('finalizeBtn').disabled = true;
        } else {
          document.getElementById('watermark').style.display = '';
          document.getElementById('rp-status').innerText = 'DRAFT';
          document.getElementById('finalizeBtn').disabled = false;
        }
        calc();
      }
    } catch (error) {
      console.error('Error loading data:', error);
      showToast('Error loading data', 'error');
    }
    hideLoading();
  }

  function refreshData() {
    loadData();
    showToast('Data refreshed', 'info');
  }

  function exportToPDF() {
    window.open('{{ route("backoffice.computations.trading-account.export-pdf") }}', '_blank');
  }

  function showToast(msg, type = 'success') {
    let t = document.createElement('div');
    t.className = 'toast-notification alert alert-' + (type === 'error' ? 'danger' : 'success');
    t.innerHTML = `<div class="d-flex align-items-center"><i class="bi bi-${type === 'error' ? 'exclamation-circle' : 'check-circle-fill'} me-2"></i><span>${msg}</span></div>`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2600);
  }

  function showLoading() {
    document.getElementById('loadingSpinner').style.display = 'block';
  }
  function hideLoading() {
    document.getElementById('loadingSpinner').style.display = 'none';
  }

  // Clock
  function tick() {
    let n = new Date();
    document.getElementById('sbTime').textContent =
      String(n.getHours()).padStart(2, '0') + ':' +
      String(n.getMinutes()).padStart(2, '0') + ':' +
      String(n.getSeconds()).padStart(2, '0');
  }
  setInterval(tick, 1000);
  tick();

  // Attach event listeners to editable cells
  document.querySelectorAll('.editable').forEach(el => {
    el.addEventListener('blur', calc);
    el.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') { e.preventDefault(); this.blur(); }
      if (e.key === 'Escape') { this.blur(); }
    });
  });

  // Auto-save every 30 seconds
  setInterval(() => saveToDatabase(), 30000);

  // Initial load
  document.addEventListener('DOMContentLoaded', () => {
    loadData();
  });
</script>
@endsection