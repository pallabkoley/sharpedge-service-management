{{-- ─── ASSETS TABLE ─── --}}
<div class="section-header">
    <h5>📋 Assets Reconciliation <span class="badge-type" id="assetCount">0 assets</span></h5>
    <div class="section-tools">
        <span class="collapse-toggle" onclick="toggleSection('assetsBody')">
            <span class="arrow open" id="assetsArrow">▶</span> Collapse
        </span>
        <button class="btn-erp btn-ghost btn-sm" onclick="addAssetRow()" style="font-size:10px;padding:2px 10px;">
            <i class="bi bi-plus-circle"></i> Add Asset
        </button>
    </div>
</div>
<div class="section-body" id="assetsBody">
    <div class="table-wrap" id="assetTableWrap">
        <table class="recon-table" id="assetsTable">
            <thead>
                <tr>
                    <th style="width:30px;">SL</th>
                    <th class="sticky-col" style="min-width:140px;">Asset Name</th>
                    <th style="min-width:80px;">Block</th>
                    <th style="min-width:90px;">Opening WDV <span class="sub-label">(₹)</span></th>
                    <th style="min-width:90px;">Purchase / Addition <span class="sub-label">(₹)</span></th>
                    <th style="min-width:100px;">Revaluation / Appreciation <span class="sub-label">(₹)</span></th>
                    <th style="min-width:90px;">Sale Value <span class="sub-label">(₹)</span></th>
                    <th style="min-width:90px;">Date Put to Use</th>
                    <th style="min-width:90px;">Sale Date</th>
                    <th style="min-width:110px;">Depreciable WDV <span class="sub-label">(₹)</span></th>
                    <th style="min-width:100px;">Total Depreciation <span class="sub-label">(₹)</span></th>
                    <th style="min-width:100px;">Closing WDV <span class="sub-label">(₹)</span></th>
                    <th style="min-width:60px;">Breakup</th>
                    <th style="min-width:50px;">&nbsp;</th>
                </tr>
            </thead>
            <tbody id="assetsBodyRows">
                {{-- Rows will be injected by JavaScript --}}
            </tbody>
        </table>
    </div>
</div>