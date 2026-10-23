{{-- resources/views/Backoffice/computation/modals/salary-modal.blade.php --}}

<div class="modal fade" id="salaryModal" tabindex="-1" aria-labelledby="salaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background:#2c2c2c;color:#fff;padding:6px 12px;">
                <h5 class="modal-title" id="salaryModalLabel">✏️ Edit Salary Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:12px;">
                <!-- Salary Components Table -->
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:4px;margin-bottom:8px;">Salary Components</h6>
                <table class="salary-modal-table" style="width:100%;border-collapse:collapse;margin-bottom:15px;">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>Particulars</th>
                            <th style="width:120px;">Section</th>
                            <th style="width:140px;">Amount (₹)</th>
                            <th style="width:120px;">Remarks</th>
                            <th style="width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="modal_salary_rows">
                        <!-- Rows will be inserted here -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align:right;font-weight:bold;padding:4px 8px;border-top:2px double #555;">
                                Gross Salary
                            </td>
                            <td style="border-top:2px double #555;padding:4px 8px;text-align:right;">
                                <input type="text" id="modal_gross" readonly style="border:none;background:transparent;font-weight:bold;text-align:right;width:100%;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding:4px;text-align:right;">
                                <button type="button" class="dyn-btn add" onclick="addSalaryModalRow('modal_salary')">+ Add Component</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Exemptions / Deductions Table -->
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:4px;margin:15px 0 8px;">Exemptions / Deductions</h6>
                <table class="salary-modal-table" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>Particulars</th>
                            <th style="width:120px;">Section</th>
                            <th style="width:140px;">Amount (₹)</th>
                            <th style="width:120px;">Remarks</th>
                            <th style="width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="modal_exempt_rows">
                        <!-- Rows will be inserted here -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align:right;font-weight:bold;padding:4px 8px;border-top:2px double #555;">
                                Total Exemptions / Deductions
                            </td>
                            <td style="border-top:2px double #555;padding:4px 8px;text-align:right;">
                                <input type="text" id="modal_exempt_total" readonly style="border:none;background:transparent;font-weight:bold;text-align:right;width:100%;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding:4px;text-align:right;">
                                <button type="button" class="dyn-btn add" onclick="addSalaryModalRow('modal_exempt')">+ Add Exemption</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Summary -->
                <div style="margin-top:15px;background:#f0f4f0;padding:8px 12px;border:2px solid #555;border-radius:3px;">
                    <div style="display:flex;justify-content:space-between;font-weight:bold;font-size:12px;">
                        <span>Income Chargeable under Salary</span>
                        <span id="modal_chargeable" style="color:#1a3a5c;font-size:14px;">0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#f5f5f5;padding:8px 12px;">
                <button type="button" class="dyn-btn" style="background:#666;" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="dyn-btn" style="background:#2c6c2c;" onclick="saveSalaryModal()">✅ Save & Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Templates for modal rows -->
<template id="modal_tpl_salary">
    <tr data-group="modal_salary" data-index="__INDEX__">
        <td class="ctr">__INDEX_PLUS_ONE__</td>
        <td><input class="inp inp-text" name="salary_entries[__INDEX__][particular]" placeholder="Particulars"></td>
        <td><input class="inp" style="text-align:center;" name="salary_entries[__INDEX__][section]" placeholder="Sec."></td>
        <td><input class="inp" name="salary_entries[__INDEX__][amount]" value="0" onchange="recalcSalaryModal()"></td>
        <td><input class="inp inp-text" name="salary_entries[__INDEX__][remarks]" placeholder="Remarks"></td>
        <td class="action-cell"><button class="dyn-btn del" onclick="deleteSalaryModalRow(this, 'modal_salary')">✕</button></td>
    </tr>
</template>

<template id="modal_tpl_exempt">
    <tr data-group="modal_exempt" data-index="__INDEX__">
        <td class="ctr">__INDEX_PLUS_ONE__</td>
        <td><input class="inp inp-text" name="exempt_incomes[__INDEX__][particular]" placeholder="Particulars"></td>
        <td><input class="inp" style="text-align:center;" name="exempt_incomes[__INDEX__][section]" placeholder="Sec."></td>
        <td><input class="inp" name="exempt_incomes[__INDEX__][amount]" value="0" onchange="recalcSalaryModal()"></td>
        <td><input class="inp inp-text" name="exempt_incomes[__INDEX__][remarks]" placeholder="Remarks"></td>
        <td class="action-cell"><button class="dyn-btn del" onclick="deleteSalaryModalRow(this, 'modal_exempt')">✕</button></td>
    </tr>
</template>