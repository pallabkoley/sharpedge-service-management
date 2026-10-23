{{-- resources/views/Backoffice/computation/modals/tax-paid-modal.blade.php --}}

<div class="modal fade" id="taxPaidModal" tabindex="-1" aria-labelledby="taxPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background:#2c2c2c;color:#fff;padding:6px 12px;">
                <h5 class="modal-title" id="taxPaidModalLabel">💰 Tax Paid Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:12px;background:#f9f9f9;">

                {{-- ============================================================
                SECTION 1: INTEREST & FEES
                ============================================================ --}}
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:4px;margin-bottom:8px;color:#1a3a5c;">
                    Interest & Fees
                </h6>
                <table class="tds-table" style="width:100%;border-collapse:collapse;margin-bottom:15px;">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>Particulars</th>
                            <th style="width:100px;">Section</th>
                            <th style="width:150px;">Amount (₹)</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ctr">1</td>
                            <td>Interest for Non-filing of Return</td>
                            <td class="ctr">234A</td>
                            <td class="amt"><input class="inp" id="modal_int_234a" value="0" style="width:140px;text-align:right;"></td>
                            <td><input class="inp inp-text" id="modal_int_234a_remarks" value="Nil — Return filed before due date"></td>
                        </tr>
                        <tr>
                            <td class="ctr">2</td>
                            <td>Interest for Short Payment of Advance Tax</td>
                            <td class="ctr">234B</td>
                            <td class="amt"><input class="inp" id="modal_int_234b" value="0" style="width:140px;text-align:right;"></td>
                            <td><input class="inp inp-text" id="modal_int_234b_remarks" value="Nil — Advance Tax paid adequately"></td>
                        </tr>
                        <tr>
                            <td class="ctr">3</td>
                            <td>Interest for Deferment of Advance Tax</td>
                            <td class="ctr">234C</td>
                            <td class="amt"><input class="inp" id="modal_int_234c" value="0" style="width:140px;text-align:right;"></td>
                            <td><input class="inp inp-text" id="modal_int_234c_remarks" value="Nil — Instalments paid on time"></td>
                        </tr>
                        <tr>
                            <td class="ctr">4</td>
                            <td>Late Fee for Late Filing of Return</td>
                            <td class="ctr">234F</td>
                            <td class="amt"><input class="inp" id="modal_fee_234f" value="0" style="width:140px;text-align:right;"></td>
                            <td><input class="inp inp-text" id="modal_fee_234f_remarks" value="Nil — Return to be filed before 31.07.2025"></td>
                        </tr>
                        <tr class="tds-total">
                            <td colspan="3" style="text-align:right;font-weight:bold;">Total Interest & Fees</td>
                            <td class="amt"><input class="inp" id="modal_int_total" readonly value="0" style="font-weight:bold;width:140px;text-align:right;"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                {{-- ============================================================
                SECTION 2: TDS DETAILS
                ============================================================ --}}
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:4px;margin:15px 0 8px;color:#1a3a5c;">
                    TAX DEDUCTED AT SOURCE (TDS)
                </h6>
                <table class="tds-table" style="width:100%;border-collapse:collapse;margin-bottom:15px;">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>TAN of Deductor</th>
                            <th>Name of Deductor</th>
                            <th style="width:70px;">Section</th>
                            <th style="width:110px;">Amount Paid (₹)</th>
                            <th style="width:110px;">TDS Deducted (₹)</th>
                            <th style="width:110px;">TDS Claimed (₹)</th>
                            <th style="width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="modal_tds_rows">
                        <!-- Rows inserted by JS -->
                    </tbody>
                    <tfoot>
                        <tr class="tds-total">
                            <td colspan="4" style="text-align:right;font-weight:bold;">TOTAL TDS</td>
                            <td class="amt"><input class="inp" id="modal_tds_total_amt" readonly value="0" style="width:110px;text-align:right;"></td>
                            <td class="amt"><input class="inp" id="modal_tds_total_ded" readonly value="0" style="width:110px;text-align:right;"></td>
                            <td class="amt"><input class="inp" id="modal_tds_total_cl" readonly value="0" style="width:110px;text-align:right;"></td>
                            <td><button class="dyn-btn add" onclick="addTdsModalRow()">+ Add</button></td>
                        </tr>
                    </tfoot>
                </table>

                {{-- ============================================================
                SECTION 3: TCS DETAILS
                ============================================================ --}}
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:4px;margin:15px 0 8px;color:#1a3a5c;">
                    TAX COLLECTED AT SOURCE (TCS)
                </h6>
                <table class="tds-table" style="width:100%;border-collapse:collapse;margin-bottom:15px;">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>TAN of Collector</th>
                            <th>Name of Collector</th>
                            <th style="width:70px;">Section</th>
                            <th style="width:110px;">Amount Collected (₹)</th>
                            <th style="width:110px;">TCS Collected (₹)</th>
                            <th style="width:110px;">TCS Claimed (₹)</th>
                            <th style="width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="modal_tcs_rows">
                        <!-- Rows inserted by JS -->
                    </tbody>
                    <tfoot>
                        <tr class="tds-total">
                            <td colspan="4" style="text-align:right;font-weight:bold;">TOTAL TCS</td>
                            <td class="amt"><input class="inp" id="modal_tcs_total_amt" readonly value="0" style="width:110px;text-align:right;"></td>
                            <td class="amt"><input class="inp" id="modal_tcs_total_col" readonly value="0" style="width:110px;text-align:right;"></td>
                            <td class="amt"><input class="inp" id="modal_tcs_total_cl" readonly value="0" style="width:110px;text-align:right;"></td>
                            <td><button class="dyn-btn add" onclick="addTcsModalRow()">+ Add</button></td>
                        </tr>
                    </tfoot>
                </table>

                {{-- ============================================================
                SECTION 4: ADVANCE TAX
                ============================================================ --}}
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:4px;margin:15px 0 8px;color:#1a3a5c;">
                    ADVANCE TAX
                </h6>
                <table class="tds-table" style="width:100%;border-collapse:collapse;margin-bottom:15px;">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th style="width:120px;">Date of Payment</th>
                            <th>BSR Code</th>
                            <th>Challan No.</th>
                            <th style="width:100px;">Instalment</th>
                            <th style="width:120px;">Amount Paid (₹)</th>
                            <th style="width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="modal_at_rows">
                        <!-- Rows inserted by JS -->
                    </tbody>
                    <tfoot>
                        <tr class="tds-total">
                            <td colspan="5" style="text-align:right;font-weight:bold;">TOTAL ADVANCE TAX PAID</td>
                            <td class="amt"><input class="inp" id="modal_at_total" readonly value="0" style="width:120px;text-align:right;"></td>
                            <td><button class="dyn-btn add" onclick="addAtModalRow()">+ Add</button></td>
                        </tr>
                    </tfoot>
                </table>

                {{-- ============================================================
                SECTION 5: SELF ASSESSMENT TAX
                ============================================================ --}}
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:4px;margin:15px 0 8px;color:#1a3a5c;">
                    SELF ASSESSMENT TAX (Challan 280)
                </h6>
                <table class="tds-table" style="width:100%;border-collapse:collapse;margin-bottom:10px;">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th style="width:120px;">Date of Payment</th>
                            <th>BSR Code</th>
                            <th>Challan No.</th>
                            <th style="width:120px;">Amount Paid (₹)</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="modal_sat_rows">
                        <tr>
                            <td class="ctr">1</td>
                            <td><input class="inp inp-text" id="modal_sat_date_0" value=""></td>
                            <td><input class="inp inp-text" id="modal_sat_bsr_0" value=""></td>
                            <td><input class="inp inp-text" id="modal_sat_challan_0" value=""></td>
                            <td class="amt"><input class="inp" id="modal_sat_amount_0" value="0" style="width:120px;text-align:right;"></td>
                            <td><input class="inp inp-text" id="modal_sat_remarks_0" value="SAT to be paid after filing"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="tds-total">
                            <td colspan="4" style="text-align:right;font-weight:bold;">TOTAL SELF ASSESSMENT TAX PAID</td>
                            <td class="amt"><input class="inp" id="modal_sat_total" readonly value="0" style="width:120px;text-align:right;"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Summary Box --}}
                <div style="margin-top:15px;background:#f0f4f8;padding:8px 12px;border:2px solid #555;border-radius:3px;">
                    <div style="display:flex;justify-content:space-between;font-weight:bold;font-size:12px;">
                        <span>Total Tax Paid (Interest + TDS + TCS + Advance Tax + SAT)</span>
                        <span id="modal_total_tax_paid" style="color:#1a3a5c;font-size:14px;">0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#f5f5f5;padding:8px 12px;">
                <button type="button" class="dyn-btn" style="background:#666;" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="dyn-btn" style="background:#2c6c2c;" onclick="saveTaxPaidModal()">✅ Save & Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Templates for modal rows --}}
<template id="modal_tpl_tds">
    <tr data-group="modal_tds" data-index="__INDEX__">
        <td class="ctr">__INDEX_PLUS_ONE__</td>
        <td><input class="inp inp-text" name="tds_entries[__INDEX__][tan_number]" placeholder="TAN"></td>
        <td><input class="inp inp-text" name="tds_entries[__INDEX__][deductor_name]" placeholder="Deductor"></td>
        <td class="ctr"><input class="inp" style="text-align:center;" name="tds_entries[__INDEX__][section_code]" placeholder="Sec."></td>
        <td class="amt"><input class="inp" name="tds_entries[__INDEX__][amount_paid]" value="0"></td>
        <td class="amt"><input class="inp" name="tds_entries[__INDEX__][tds_deducted]" value="0"></td>
        <td class="amt"><input class="inp" name="tds_entries[__INDEX__][tds_claimed]" value="0"></td>
        <td class="action-cell"><button class="dyn-btn del" onclick="deleteModalRow(this, 'modal_tds')">✕</button></td>
    </tr>
</template>

<template id="modal_tpl_tcs">
    <tr data-group="modal_tcs" data-index="__INDEX__">
        <td class="ctr">__INDEX_PLUS_ONE__</td>
        <td><input class="inp inp-text" name="tcs_entries[__INDEX__][tan_number]" placeholder="TAN"></td>
        <td><input class="inp inp-text" name="tcs_entries[__INDEX__][collector_name]" placeholder="Collector"></td>
        <td class="ctr"><input class="inp" style="text-align:center;" name="tcs_entries[__INDEX__][section_code]" placeholder="Sec."></td>
        <td class="amt"><input class="inp" name="tcs_entries[__INDEX__][amount_collected]" value="0"></td>
        <td class="amt"><input class="inp" name="tcs_entries[__INDEX__][tcs_collected]" value="0"></td>
        <td class="amt"><input class="inp" name="tcs_entries[__INDEX__][tcs_claimed]" value="0"></td>
        <td class="action-cell"><button class="dyn-btn del" onclick="deleteModalRow(this, 'modal_tcs')">✕</button></td>
    </tr>
</template>

<template id="modal_tpl_at">
    <tr data-group="modal_at" data-index="__INDEX__">
        <td class="ctr">__INDEX_PLUS_ONE__</td>
        <td><input class="inp inp-text" name="advance_tax_entries[__INDEX__][payment_date]" placeholder="Date"></td>
        <td><input class="inp inp-text" name="advance_tax_entries[__INDEX__][bsr_code]" placeholder="BSR"></td>
        <td><input class="inp inp-text" name="advance_tax_entries[__INDEX__][challan_no]" placeholder="Challan No."></td>
        <td class="ctr"><input class="inp" style="text-align:center;" name="advance_tax_entries[__INDEX__][installment]" placeholder="Instalment"></td>
        <td class="amt"><input class="inp" name="advance_tax_entries[__INDEX__][amount_paid]" value="0"></td>
        <td class="action-cell"><button class="dyn-btn del" onclick="deleteModalRow(this, 'modal_at')">✕</button></td>
    </tr>
</template>