{{-- resources/views/Backoffice/computation/modals/deduction-modal.blade.php --}}

<div class="modal fade" id="dedModal" tabindex="-1" aria-labelledby="dedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background:#2c2c2c;color:#fff;padding:6px 12px;">
                <h5 class="modal-title" id="dedModalLabel">✏️ Edit Deductions (Chapter VI-A) Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:12px;">
                {{-- Deduction Entries Table --}}
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:4px;margin-bottom:8px;">Deductions Under Chapter VI-A</h6>
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
                    <tbody id="modal_ded_rows">
                        <!-- Rows inserted by JS only -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align:right;font-weight:bold;padding:4px 8px;border-top:2px double #555;">
                                Total Deductions
                            </td>
                            <td style="border-top:2px double #555;padding:4px 8px;text-align:right;">
                                <input type="text" id="modal_ded_total" readonly style="border:none;background:transparent;font-weight:bold;text-align:right;width:100%;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding:4px;text-align:right;">
                                <button type="button" class="dyn-btn add" onclick="addDedModalRow()">+ Add Deduction</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer" style="background:#f5f5f5;padding:8px 12px;">
                <button type="button" class="dyn-btn" style="background:#666;" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="dyn-btn" style="background:#2c6c2c;" onclick="saveDedModal()">✅ Save & Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Template for modal rows --}}
<template id="modal_tpl_ded">
    <tr data-group="modal_ded" data-index="__INDEX__">
        <td class="ctr">__INDEX_PLUS_ONE__</td>
        <td><input class="inp inp-text" name="ded_entries[__INDEX__][particular]" placeholder="Enter particulars"></td>
        <td><input class="inp" style="text-align:center;" name="ded_entries[__INDEX__][section]" placeholder="Sec."></td>
        <td><input class="inp" name="ded_entries[__INDEX__][amount]" value="0" onchange="recalcDedModal()" onkeyup="recalcDedModal()"></td>
        <td><input class="inp inp-text" name="ded_entries[__INDEX__][remarks]" placeholder="Remarks"></td>
        <td class="action-cell"><button class="dyn-btn del" onclick="deleteDedModalRow(this)">✕</button></td>
    </tr>
</template>