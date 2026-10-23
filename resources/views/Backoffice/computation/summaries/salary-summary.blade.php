<div class="section-block" id="sec_salary_block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">1</td>
            <td colspan="3">
                A. INCOME FROM SALARY
                <button class="collapse-btn" data-toggle="sec_salary">▼</button>
                <span style="float:right;margin-right:15px;">
                    <button type="button" class="dyn-btn add" style="background:#0054a6;border-color:#0054a6;" onclick="openSalaryModal()">
                        ✏️ Edit Salary Schedule
                    </button>
                </span>
            </td>
            <td class="amt" style="color:#ffdd44;">Schedule S</td>
        </tr>
        <tbody id="sec_salary">
            <tr class="row-normal"><td class="rn"></td><td><strong>Gross Salary</strong></td><td></td><td class="amt"><input class="inp" id="s_gross" readonly value="0"></td><td></td></tr>
            <tr class="row-normal"><td class="rn"></td><td><strong>Less: Exemptions / Deductions</strong></td><td></td><td class="amt"><input class="inp" id="s_exempt_total" readonly value="0"></td><td></td></tr>
            <tr class="row-grandtotal"><td class="rn"></td><td><strong>INCOME CHARGEABLE UNDER SALARY (I)</strong></td><td></td><td class="amt"><input class="inp" id="s_total" readonly value="0"></td><td></td></tr>
            <input type="hidden" id="salary_data_json" value='@json($computation->salaryEntries ?? [])'>
            <input type="hidden" id="exempt_data_json" value='@json($computation->exemptIncomes ?? [])'>
        </tbody>
    </table>
</div>