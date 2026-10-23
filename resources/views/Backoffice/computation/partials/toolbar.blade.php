<div id="toolbar">
    <div class="app-title">◆ CA TAX ERP 2025-26</div>
    
    <button onclick="toggleAll(true)">▼ Expand All</button>
    <button onclick="toggleAll(false)">▶ Collapse All</button>
    <button onclick="clearAll()">⊘ Clear</button>
    <button onclick="loadDemo()">⬇ Load Demo Data</button>
    <div class="sep">|</div>
    <button onclick="saveSheet('draft')" style="background:#2c6c2c;">💾 Save Draft</button>
    <button onclick="saveSheet('submit')" style="background:#c0392b;">📤 Submit</button>

    {{-- 🔥 BUTTON GROUP: PDF + Reconciliation + Financial Statements + D/C Register + Assets Recon --}}
    <div style="display:inline-block;margin-left:5px;">
        {{-- Summary PDF (Client Copy) --}}
        <a href="{{ route('backoffice.computations.pdf', ['id' => $computation->id, 'type' => 'summary']) }}" 
           target="_blank" 
           style="background:#0054a6;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📄 Summary
        </a>

        {{-- Working Paper PDF (Office Copy) --}}
        <a href="{{ route('backoffice.computations.pdf', ['id' => $computation->id, 'type' => 'working']) }}" 
           target="_blank" 
           style="background:#8e44ad;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📑 Working Paper
        </a>

        {{-- Sales Reconciliation --}}
        <a href="{{ route('backoffice.computations.sales-reconciliation.index', ['computation' => $computation->id]) }}" 
           style="background:#f39c12;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📊 Sales Reconciliation
        </a>

        {{-- Purchase Reconciliation --}}
        <a href="{{ route('backoffice.computations.purchase-reconciliation.index', ['computation' => $computation->id]) }}" 
           style="background:#27ae60;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📊 Purchase Reconciliation
        </a>

        {{-- 🆕 Trading Account --}}
        <a href="{{ route('backoffice.computations.trading-account.index', ['computation' => $computation->id]) }}" 
           style="background:#6c3483;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📈 Trading Account
        </a>

        {{-- 🆕 Profit & Loss --}}
        <a href="{{ route('backoffice.computations.profit-loss.index', ['computation' => $computation->id]) }}" 
           style="background:#1a5276;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📊 Profit &amp; Loss
        </a>

        {{-- ✅ NEW: BALANCE SHEET ADDED HERE --}}
        <a href="{{ route('backoffice.computations.balance-sheet.index', ['computation' => $computation->id]) }}" 
           style="background:#0d47a1;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📋 Balance Sheet
        </a>

        {{-- Debtors Reconciliation --}}
        <a href="{{ route('backoffice.computations.debtors-reconciliation.index', ['computation' => $computation->id]) }}" 
           style="background:#e67e22;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📊 Debtors Reconciliation
        </a>

        {{-- Creditors Reconciliation --}}
        <a href="{{ route('backoffice.computations.creditors-reconciliation.index', ['computation' => $computation->id]) }}" 
           style="background:#1abc9c;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📊 Creditors Reconciliation
        </a>

        {{-- Bank Reconciliation --}}
        <a href="{{ route('backoffice.computations.bank-reconciliation.index', ['computation' => $computation->id]) }}" 
           style="background:#2c3e50;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            🏦 Bank Reconciliation
        </a>

        {{-- Debit / Credit Note Register --}}
        <a href="{{ route('backoffice.computations.debit-credit-registers.index', ['computation' => $computation->id]) }}" 
           style="background:#5e35b1;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📒 D/C Note Register
        </a>

        {{-- Assets Reconciliation (Income Tax) --}}
        <a href="{{ route('backoffice.computations.assets-reconciliation.index', ['computation' => $computation->id]) }}" 
           style="background:#2980b9;color:#fff;padding:4px 14px;border-radius:3px;text-decoration:none;font-size:12px;border:none;cursor:pointer;display:inline-block;margin-right:2px;">
            📊 Assets Reconciliation
        </a>
    </div>

    <div class="save-status" id="save-status">● Ready</div>
</div>