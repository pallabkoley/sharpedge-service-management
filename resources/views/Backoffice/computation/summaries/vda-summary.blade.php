<div class="section-block" id="sec_vda_block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">5</td>
            <td colspan="3">
                E. INCOME FROM TRANSFER OF VIRTUAL DIGITAL ASSETS
                <button class="collapse-btn" data-toggle="sec_vda">▼</button>
                <span style="float:right;margin-right:15px;">
                    <button type="button" class="dyn-btn add" style="background:#0054a6;border-color:#0054a6;" onclick="openVdaModal()">
                        ✏️ Edit VDA
                    </button>
                </span>
            </td>
            <td class="amt" style="color:#ffdd44;">Schedule VDA</td>
        </tr>
        <tbody id="sec_vda">
            <tr class="row-grandtotal">
                <td class="rn"></td>
                <td><strong>Total VDA Income</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="vda_total" readonly value="0" style="font-weight:bold;color:#000;">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    {{-- Hidden JSON for modal data (loads from DB) --}}
    <input type="hidden" id="vda_data_json" value='@json($computation->vdaEntries ?? [])'>
    {{-- Hidden field to store the computed total --}}
    <input type="hidden" id="vda_total_value" value="0">
</div>