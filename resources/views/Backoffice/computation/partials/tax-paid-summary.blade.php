<div class="section-block" id="sec_tax_paid_block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">I</td>
            <td colspan="3">
                TAX PAID
                <button class="collapse-btn" data-toggle="sec_tax_paid">▼</button>
                <span style="float:right;margin-right:15px;">
                    <button type="button" class="dyn-btn add" style="background:#0054a6;border-color:#0054a6;" onclick="openTaxPaidModal()">
                        👁️ View Details
                    </button>
                </span>
            </td>
            <td class="amt" style="color:#ffdd44;">Schedule IT</td>
        </tr>
        <tbody id="sec_tax_paid">
            {{-- Hidden summary fields (used by calcFinal) --}}
            <tr class="row-normal" style="display:none;">
                <td class="rn"></td>
                <td><strong>TDS Deducted</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="tds_total_cl" readonly value="0">
                </td>
                <td></td>
            </tr>
            <tr class="row-normal" style="display:none;">
                <td class="rn"></td>
                <td><strong>TCS Collected</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="tcs_total_cl" readonly value="0">
                </td>
                <td></td>
            </tr>
            <tr class="row-normal" style="display:none;">
                <td class="rn"></td>
                <td><strong>Advance Tax Paid</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="at_total" readonly value="0">
                </td>
                <td></td>
            </tr>
            <tr class="row-normal" style="display:none;">
                <td class="rn"></td>
                <td><strong>Self Assessment Tax (SAT)</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="sat_total" readonly value="0">
                </td>
                <td></td>
            </tr>
            {{-- Main summary row --}}
            <tr class="row-grandtotal">
                <td class="rn"></td>
                <td><strong>Total Tax Paid</strong></td>
                <td></td>
                <td class="amt">
                    <input class="inp" id="tax_paid_total_display" readonly value="0" style="font-weight:bold;color:#000;">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    {{-- 🔥 FIXED: Load data from database relationships --}}
    @php
        $interest = $computation->interestEntry;
        $taxPaidData = [
            'interest_234a' => $interest->interest_234a ?? 0,
            'interest_234b' => $interest->interest_234b ?? 0,
            'interest_234c' => $interest->interest_234c ?? 0,
            'fee_234f'      => $interest->fee_234f ?? 0,
            'tds_entries'   => $computation->tdsEntries ?? [],
            'tcs_entries'   => $computation->tcsEntries ?? [],
            'advance_tax_entries' => $computation->advanceTaxEntries ?? [],
            'sat_entries'   => $computation->selfAssessmentTaxEntries ?? [],
        ];
    @endphp
    <input type="hidden" id="tax_paid_data_json" value="{{ json_encode($taxPaidData) }}">
    <input type="hidden" id="tax_paid_total_value" value="0">
</div>