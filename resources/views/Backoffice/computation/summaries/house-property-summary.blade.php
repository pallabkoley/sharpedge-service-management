<div class="section-block" id="sec_hp_block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">2</td>
            <td colspan="3">
                B. INCOME FROM HOUSE PROPERTY
                <button class="collapse-btn" data-toggle="sec_hp">▼</button>
                <span style="float:right;margin-right:15px;">
                    <button type="button" class="dyn-btn add" style="background:#0054a6;border-color:#0054a6;" onclick="openHpModal()">
                        ✏️ Edit House Property
                    </button>
                </span>
            </td>
            <td class="amt" style="color:#ffdd44;">Schedule HP</td>
        </tr>
        <tbody id="sec_hp">
            {{-- Only one summary row --}}
            <tr class="row-grandtotal">
                <td class="rn"></td>
                <td><strong>Income From House Property</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="hp_total_display" readonly value="0" style="font-weight:bold;color:#000;">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    {{-- Hidden JSON for modal data (loads from DB) --}}
    <input type="hidden" id="hp_data_json" value='@json($computation->housePropertyEntries ?? [])'>
    {{-- Hidden field to store the computed total --}}
    <input type="hidden" id="hp_total_value" value="0">
</div>