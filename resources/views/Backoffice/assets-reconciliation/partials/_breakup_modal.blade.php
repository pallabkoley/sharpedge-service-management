{{-- ─── BREAKUP MODAL ─── --}}
<div class="modal-overlay" id="breakupModal">
    <div class="modal-box">
        <button class="close-modal" onclick="closeBreakupModal()">✕</button>
        <h3 id="breakupModalTitle">Depreciation Breakup</h3>
        <div class="modal-sub" id="breakupModalSub">
            <span id="breakupAssetName">Asset Name</span> &nbsp;|&nbsp;
            <strong>Depreciable WDV: <span id="breakupDepBase">₹0</span></strong>
        </div>
        <div class="table-wrap" style="max-height:350px;">
            <table class="breakup-table" id="breakupTable">
                <thead>
                    <tr>
                        <th style="width:40%;">Amount <span class="sub-label">(₹)</span></th>
                        <th style="width:25%;">Rate %</th>
                        <th style="width:30%;">Depreciation <span class="sub-label">(₹)</span></th>
                        <th style="width:5%;">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="breakupBody">
                    {{-- Injected by JavaScript --}}
                </tbody>
            </table>
        </div>
        <div class="breakup-total">
            <span class="label">Total Breakup Amount:</span>
            <span class="value" id="breakupTotalAmount">₹0</span>
            <span style="margin-left:20px;" class="label">Depreciable WDV:</span>
            <span class="value" id="breakupDepBaseDisplay">₹0</span>
        </div>
        <div id="breakupValidation" class="breakup-validation"></div>
        <div class="modal-actions">
            <button class="btn-erp btn-ghost btn-sm" onclick="addBreakupRow()"><i class="bi bi-plus-circle"></i> Add Row</button>
            <button class="btn-erp btn-blue btn-sm" onclick="saveBreakup()"><i class="bi bi-check2"></i> Save Breakup</button>
        </div>
    </div>
</div>