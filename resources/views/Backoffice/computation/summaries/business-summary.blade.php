<div class="section-block" id="sec_bp_block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">3</td>
            <td colspan="3">
                C. PROFITS & GAINS FROM BUSINESS OR PROFESSION
                <button class="collapse-btn" data-toggle="sec_bp">▼</button>
                <span style="float:right;margin-right:15px;">
                    <button type="button" class="dyn-btn add" style="background:#0054a6;border-color:#0054a6;" onclick="openBpModal()">
                        👁️ View
                    </button>
                </span>
            </td>
            <td class="amt" style="color:#ffdd44;">Schedule BP</td>
        </tr>
        <tbody id="sec_bp">
            {{-- Only ONE summary row --}}
            <tr class="row-grandtotal">
                <td class="rn"></td>
                <td><strong>Income From Business / Profession</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="bp_total" readonly value="{{ number_format($businessIncome ?? 0, 2) }}" style="font-weight:bold;color:#000;">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>