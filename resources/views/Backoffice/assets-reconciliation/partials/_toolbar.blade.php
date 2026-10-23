<div class="br-toolbar" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;padding:8px 14px;background:#f8fafc;border-bottom:1px solid #d0d8e4;">
    <div class="br-toolbar-left">
        <span style="font-size:13px;font-weight:500;color:#1a2332;">Financials &rsaquo; Assets Reconciliation</span>
    </div>
    <div class="br-toolbar-right" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
        {{-- Search Box --}}
        <div class="br-search" style="display:flex;align-items:center;gap:4px;background:#fff;border:1px solid #cbd5e1;border-radius:4px;padding:3px 8px;font-size:12px;">
            <i class="bi bi-search"></i>
            <input type="text" id="globalSearch" placeholder="Search assets..." oninput="filterAssets()" style="border:none;background:transparent;outline:none;font-size:12px;width:150px;" />
        </div>

        {{-- Save Button --}}
        <button class="btn-erp btn-ghost" onclick="saveDraft()" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;font-size:11.5px;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all 0.15s;white-space:nowrap;font-family:var(--font-sans);background:#f1f5f9;border-color:#d0d8e4;color:#1a2332;">
            <i class="bi bi-cloud-arrow-up"></i> Save
        </button>

        {{-- Print Button --}}
        <button class="btn-erp btn-ghost" onclick="window.print()" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;font-size:11.5px;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all 0.15s;white-space:nowrap;font-family:var(--font-sans);background:#f1f5f9;border-color:#d0d8e4;color:#1a2332;">
            <i class="bi bi-printer-fill"></i> Print
        </button>

        {{-- PDF Button --}}
        <a href="{{ route('backoffice.computations.pdf', ['id' => $computation->id, 'type' => 'summary']) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;font-size:11.5px;font-weight:600;cursor:pointer;border:1px solid #d0d8e4;transition:all 0.15s;white-space:nowrap;font-family:var(--font-sans);background:#f1f5f9;color:#1a2332;text-decoration:none;">
            <i class="bi bi-file-pdf-fill"></i> PDF
        </a>

        {{-- Add Row Button (drops down to add asset/investment/security) --}}
        <div style="display:inline-flex;align-items:center;gap:2px;position:relative;">
            <button class="btn-erp btn-blue" onclick="addRowDropdown()" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;font-size:11.5px;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all 0.15s;white-space:nowrap;font-family:var(--font-sans);background:#1565c0;color:#fff;">
                <i class="bi bi-plus-circle"></i> Add Row
            </button>
            <div id="addRowDropdown" style="display:none;position:absolute;top:100%;left:0;background:#fff;border:1px solid #d0d8e4;border-radius:5px;box-shadow:0 4px 12px rgba(0,0,0,0.1);z-index:100;min-width:180px;margin-top:2px;">
                <button onclick="addAssetRow();closeDropdown();" style="display:block;width:100%;padding:8px 14px;border:none;background:transparent;text-align:left;font-size:12px;cursor:pointer;font-family:var(--font-sans);color:#1a2332;border-bottom:1px solid #e2e8f0;">
                    <i class="bi bi-building"></i> Add Asset
                </button>
                <button onclick="addInvestmentRow();closeDropdown();" style="display:block;width:100%;padding:8px 14px;border:none;background:transparent;text-align:left;font-size:12px;cursor:pointer;font-family:var(--font-sans);color:#1a2332;border-bottom:1px solid #e2e8f0;">
                    <i class="bi bi-graph-up-arrow"></i> Add Investment
                </button>
                <button onclick="addSecurityRow();closeDropdown();" style="display:block;width:100%;padding:8px 14px;border:none;background:transparent;text-align:left;font-size:12px;cursor:pointer;font-family:var(--font-sans);color:#1a2332;">
                    <i class="bi bi-shield-lock"></i> Add Security Deposit
                </button>
            </div>
        </div>

        {{-- Submit Button --}}
        <button class="btn-erp btn-green" onclick="finalizeReport()" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;font-size:11.5px;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all 0.15s;white-space:nowrap;font-family:var(--font-sans);background:#00897b;color:#fff;">
            <i class="bi bi-check2-circle"></i> Submit
        </button>

        {{-- Finalize Button --}}
        <button class="btn-erp btn-green" onclick="finalizeReport()" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;font-size:11.5px;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all 0.15s;white-space:nowrap;font-family:var(--font-sans);background:#00897b;color:#fff;">
            <i class="bi bi-check2-circle"></i> Finalize
        </button>
    </div>
</div>

<script>
    // Dropdown toggle function
    function addRowDropdown() {
        const dropdown = document.getElementById('addRowDropdown');
        if (dropdown) {
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    }

    function closeDropdown() {
        const dropdown = document.getElementById('addRowDropdown');
        if (dropdown) {
            dropdown.style.display = 'none';
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('addRowDropdown');
        const btn = e.target.closest('.btn-erp.btn-blue');
        if (dropdown && !dropdown.contains(e.target) && !btn) {
            dropdown.style.display = 'none';
        }
    });
</script>