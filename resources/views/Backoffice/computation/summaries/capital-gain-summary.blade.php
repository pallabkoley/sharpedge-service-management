<div class="section-block" id="sec_cg_block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">4</td>
            <td colspan="3">
                D. CAPITAL GAINS
                <button class="collapse-btn" data-toggle="sec_cg">▼</button>
                <span style="float:right;margin-right:15px;">
                    <button type="button" class="dyn-btn add" style="background:#0054a6;border-color:#0054a6;" onclick="openCgModal()">
                        ✏️ Edit Capital Gains
                    </button>
                </span>
            </td>
            <td class="amt" style="color:#ffdd44;">Schedule CG</td>
        </tr>
        <tbody id="sec_cg">
            <tr class="row-grandtotal">
                <td class="rn"></td>
                <td><strong>Total Capital Gains</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="cg_total" readonly value="0" style="font-weight:bold;color:#000;">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    {{-- 🔥 CRITICAL: These hidden fields MUST exist --}}
    <input type="hidden" id="cg_data_json" value='@json($computation->capitalGainEntries ?? [])'>
    <input type="hidden" id="cg_total_value" value="0">
</div>