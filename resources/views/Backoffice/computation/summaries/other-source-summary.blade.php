<div class="section-block" id="sec_os_block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">6</td>
            <td colspan="3">
                F. INCOME FROM OTHER SOURCES
                <button class="collapse-btn" data-toggle="sec_os">▼</button>
                <span style="float:right;margin-right:15px;">
                    <button type="button" class="dyn-btn add" style="background:#0054a6;border-color:#0054a6;" onclick="openOsModal()">
                        ✏️ Edit Other Sources
                    </button>
                </span>
            </td>
            <td class="amt" style="color:#ffdd44;">Schedule OS</td>
        </tr>
        <tbody id="sec_os">
            {{-- Only one summary row --}}
            <tr class="row-grandtotal">
                <td class="rn"></td>
                <td><strong>Total Income From Other Sources</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="os_total" readonly value="0" style="font-weight:bold;color:#000;">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    {{-- Hidden JSON for modal data (loads from DB) --}}
    <input type="hidden" id="os_data_json" value='@json($computation->otherSourceEntries ?? [])'>
    {{-- Hidden field to store the computed total --}}
    <input type="hidden" id="os_total_value" value="0">
</div>