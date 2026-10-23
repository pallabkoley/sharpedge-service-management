<div class="right-panel">
    <div class="rp-heading"><i class="bi bi-bar-chart-fill" style="margin-right:4px;"></i>P&amp;L Summary</div>

    <div class="kpi-card" style="background:#e8f5e9;border-color:#c8e6c9;">
        <div class="kpi-label" style="color:#388e3c;">Gross Profit b/d</div>
        <div class="kpi-value" id="rp-gp" style="color:var(--emerald);">₹13,99,500</div>
        <div class="kpi-change up"><i class="bi bi-arrow-up-short"></i> Carried from Trading A/c</div>
        <div class="kpi-bar-wrap"><div class="kpi-bar green" id="rp-gpBar" style="width:72%;"></div></div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">Total Expenses</div>
        <div class="kpi-value" id="rp-exp" style="color:var(--red);">₹0</div>
        <div class="kpi-change down"><i class="bi bi-arrow-down-short"></i> All indirect expenses</div>
        <div class="kpi-bar-wrap"><div class="kpi-bar red" id="rp-expBar" style="width:0%;"></div></div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">Total Other Income</div>
        <div class="kpi-value" id="rp-inc" style="color:var(--blue);">₹0</div>
        <div class="kpi-change up"><i class="bi bi-plus-circle"></i> Added to credit side</div>
        <div class="kpi-bar-wrap"><div class="kpi-bar blue" id="rp-incBar" style="width:0%;"></div></div>
    </div>

    <div class="kpi-card" style="background:#fff8e1;border-color:#ffe082;">
        <div class="kpi-label" style="color:#f57f17;">Net Profit c/d</div>
        <div class="kpi-value" id="rp-np" style="color:#e65100;">₹0</div>
        <div class="kpi-change warn"><i class="bi bi-exclamation-triangle-fill"></i> Subject to IT adjustments</div>
        <div class="kpi-bar-wrap"><div class="kpi-bar gold" id="rp-npBar" style="width:0%;"></div></div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">Net Profit Ratio</div>
        <div class="ratio-ring-wrap">
            <svg class="ratio-ring" viewBox="0 0 76 76">
                <circle cx="38" cy="38" r="30" fill="none" stroke="#eef1f6" stroke-width="7"/>
                <circle cx="38" cy="38" r="30" fill="none" stroke="#1565c0" stroke-width="7"
                    stroke-dasharray="188.5" stroke-dashoffset="150"
                    stroke-linecap="round" transform="rotate(-90 38 38)" id="npRing"/>
            </svg>
            <div class="ratio-stats">
                <div class="ratio-pct" id="rpNpRatio">0.00%</div>
                <div class="ratio-label">NP / (GP + Income)</div>
            </div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">EBITDA</div>
        <div class="kpi-value" id="rp-ebitda" style="color:var(--emerald);">₹0</div>
        <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:10px;color:var(--slate);">
            <span>Depr: <span id="rp-dep" style="font-family:var(--font-mono);color:var(--text-dark);">₹84,000</span></span>
            <span>Bad Debts: <span id="rp-bd" style="font-family:var(--font-mono);color:var(--text-dark);">₹28,000</span></span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">Statement Status</div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
            <span id="rp-stat" style="font-size:12px;font-weight:700;color:#f57f17;">DRAFT</span>
            <span style="font-size:10px;color:var(--slate);">FY 2024–25 &nbsp;|&nbsp; AY 2025–26</span>
        </div>
    </div>

    <div class="rp-notes">
        <strong><i class="bi bi-info-circle-fill"></i> Auditor Note</strong>
        Provisional figures. Subject to audit adjustments and IT provisions. Bad debts/disallowance may apply.
    </div>

    <div style="font-size:9.5px;color:var(--slate-light);text-align:center;padding:6px 0;">
        <i class="bi bi-shield-check" style="color:var(--emerald);"></i>
        Auto-calculated · FY 2024–25
    </div>
</div>