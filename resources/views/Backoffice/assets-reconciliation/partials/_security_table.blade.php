{{-- ─── SECURITY DEPOSITS TABLE ─── --}}
<div class="section-header" style="border-top:2px solid #4a6fa5;">
    <h5>🔒 Security Deposit</h5>
    <div class="section-tools">
        <span class="collapse-toggle" onclick="toggleSection('securityBody')">
            <span class="arrow open" id="securityArrow">▶</span> Collapse
        </span>
        <button class="btn-erp btn-ghost btn-sm" onclick="addSecurityRow()" style="font-size:10px;padding:2px 10px;">
            <i class="bi bi-plus-circle"></i> Add
        </button>
    </div>
</div>
<div class="section-body" id="securityBody">
    <div class="table-wrap" style="max-height:200px;">
        <table class="recon-table" style="min-width:550px;">
            <thead>
                <tr>
                    <th style="min-width:150px;">Particulars</th>
                    <th style="min-width:110px;">Opening <span class="sub-label">(₹)</span></th>
                    <th style="min-width:110px;">Deposit <span class="sub-label">(₹)</span></th>
                    <th style="min-width:100px;">Withdrawal <span class="sub-label">(₹)</span></th>
                    <th style="min-width:120px;">Net Deposit <span class="sub-label">(₹)</span></th>
                    <th style="width:40px;">&nbsp;</th>
                </tr>
            </thead>
            <tbody id="securityBodyRows">
                {{-- Rows will be injected by JavaScript --}}
            </tbody>
        </table>
    </div>
</div>