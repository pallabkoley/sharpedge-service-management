<div class="section-block" id="sec_ded_block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">7</td>  {{-- ✅ এখন সেকশন নম্বর ৭ --}}
            <td colspan="3">
                H. DEDUCTIONS UNDER CHAPTER VI-A
                <button class="collapse-btn" data-toggle="sec_ded">▼</button>
                <span style="float:right;margin-right:15px;">
                    <button type="button" class="dyn-btn add" style="background:#0054a6;border-color:#0054a6;" onclick="openDedModal()">
                        ✏️ Edit Deductions
                    </button>
                </span>
            </td>
            <td class="amt" style="color:#ffdd44;">Schedule VIA</td>
        </tr>
        <tbody id="sec_ded">
            <tr class="row-grandtotal">
                <td class="rn"></td>
                <td><strong>Total Deductions Under Chapter VI-A</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="ded_total" readonly value="0" style="font-weight:bold;color:#000;">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    <input type="hidden" id="ded_data_json" value='@json($computation->deductionEntries ?? [])'>
    <input type="hidden" id="ded_total_value" value="0">
</div>