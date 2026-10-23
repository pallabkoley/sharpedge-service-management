{{-- resources/views/Backoffice/computation/modals/house-property-modal.blade.php --}}

<div class="modal fade" id="hpModal" tabindex="-1" aria-labelledby="hpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background:#2c2c2c;color:#fff;padding:6px 12px;">
                <h5 class="modal-title" id="hpModalLabel">✏️ Edit House Property Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:12px;">
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:4px;margin-bottom:8px;">House Property Entries</h6>
                <table class="salary-modal-table" style="width:100%;border-collapse:collapse;margin-bottom:10px;">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>Particulars</th>
                            <th style="width:100px;">Section</th>
                            <th style="width:130px;">Amount (₹)</th>
                            <th style="width:100px;">Remarks</th>
                            <th style="width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="modal_hp_rows">
                        <!-- Rows inserted by JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" style="padding:4px;text-align:right;">
                                <button type="button" class="dyn-btn add" onclick="addHpModalRow()">+ Add Entry</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Calculation Summary (read‑only) --}}
                <div style="margin-top:10px;background:#f5f8ff;padding:8px 12px;border:1px solid #ccc;border-radius:3px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px 20px;font-size:11px;">
                        <div><strong>Total Annual Rental Income</strong></div>
                        <div style="text-align:right;"><input type="text" id="hp_modal_total_rent" readonly style="border:none;background:transparent;font-weight:bold;text-align:right;width:100%;"></div>
                        
                        <div><strong>Less: Municipal Taxes Paid</strong></div>
                        <div style="text-align:right;"><input type="text" id="hp_modal_muni_tax" readonly style="border:none;background:transparent;text-align:right;width:100%;"></div>
                        
                        <div><strong>Net Annual Value (NAV)</strong></div>
                        <div style="text-align:right;"><input type="text" id="hp_modal_nav" readonly style="border:none;background:transparent;font-weight:bold;text-align:right;width:100%;"></div>
                        
                        <div><strong>Less: Standard Deduction @ 30% of NAV</strong></div>
                        <div style="text-align:right;"><input type="text" id="hp_modal_std" readonly style="border:none;background:transparent;text-align:right;width:100%;"></div>
                        
                        <div><strong>Less: Interest on Housing Loan</strong></div>
                        <div style="text-align:right;"><input type="text" id="hp_modal_interest" readonly style="border:none;background:transparent;text-align:right;width:100%;"></div>
                        
                        <div style="border-top:2px double #555;padding-top:4px;font-weight:bold;font-size:12px;color:#1a3a5c;">
                            <strong>Income From House Property</strong>
                        </div>
                        <div style="border-top:2px double #555;padding-top:4px;text-align:right;font-weight:bold;font-size:12px;color:#1a3a5c;">
                            <input type="text" id="hp_modal_final" readonly style="border:none;background:transparent;font-weight:bold;text-align:right;width:100%;color:#1a3a5c;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#f5f5f5;padding:8px 12px;">
                <button type="button" class="dyn-btn" style="background:#666;" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="dyn-btn" style="background:#2c6c2c;" onclick="saveHpModal()">✅ Save & Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Template for modal rows -->
<template id="modal_tpl_hp">
    <tr data-group="modal_hp" data-index="__INDEX__">
        <td class="ctr">__INDEX_PLUS_ONE__</td>
        <td><input class="inp inp-text" name="hp_entries[__INDEX__][particular]" placeholder="Particulars"></td>
        <td><input class="inp" style="text-align:center;" name="hp_entries[__INDEX__][section]" placeholder="Sec."></td>
        <td><input class="inp" name="hp_entries[__INDEX__][amount]" value="0" onchange="recalcHpModal()" onkeyup="recalcHpModal()"></td>
        <td><input class="inp inp-text" name="hp_entries[__INDEX__][remarks]" placeholder="Remarks"></td>
        <td class="action-cell"><button class="dyn-btn del" onclick="deleteHpModalRow(this)">✕</button></td>
    </tr>
</template>