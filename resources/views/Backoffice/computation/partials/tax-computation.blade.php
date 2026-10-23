<div class="section-block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">10</td>
            <td colspan="3">I. TAX CALCULATION</td>
            <td class="amt" style="color:#ffdd44;">
                @if($computation->tax_regime === 'old')
                    OLD REGIME
                @else
                    NEW REGIME
                @endif
            </td>
        </tr>
        <tbody id="sec_tax">
            {{-- Hidden input to pass regime to JavaScript --}}
            <input type="hidden" id="tax_regime" value="{{ $computation->tax_regime ?? 'old' }}">

            {{-- ============================================================
                 INCLUDE REGIME-SPECIFIC SLAB ROWS
            ============================================================ --}}
            @if($computation->tax_regime === 'old')
                @include('Backoffice.computation.tax.old-regime')
            @else
                @include('Backoffice.computation.tax.new-regime')
            @endif

            {{-- ============================================================
                 COMMON ROWS – Appear in Both Regimes
            ============================================================ --}}

            {{-- STCG Tax --}}
            <tr class="row-normal">
                <td class="rn">
                    @if($computation->tax_regime === 'old')
                        10.5
                    @else
                        10.8
                    @endif
                </td>
                <td>Tax on STCG u/s 111A @ 20%</td>
                <td class="ctr">111A</td>
                <td class="amt"><input class="inp" id="tax_stcg" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- LTCG Tax --}}
            <tr class="row-normal">
                <td class="rn">
                    @if($computation->tax_regime === 'old')
                        10.6
                    @else
                        10.9
                    @endif
                </td>
                <td>Tax on LTCG u/s 112A @ 12.5% (Excess over ₹1.25L)</td>
                <td class="ctr">112A</td>
                <td class="amt"><input class="inp" id="tax_ltcg" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- VDA Tax --}}
            <tr class="row-normal">
                <td class="rn">
                    @if($computation->tax_regime === 'old')
                        10.7
                    @else
                        10.10
                    @endif
                </td>
                <td>Tax on VDA / Crypto Income @ 30% u/s 115BBH</td>
                <td class="ctr">115BBH</td>
                <td class="amt"><input class="inp" id="tax_vda" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- Tax Payable --}}
            <tr class="row-subtotal">
                <td class="rn"></td>
                <td><strong>Tax Payable</strong></td>
                <td></td>
                <td class="amt"><input class="inp" id="tax_before_rebate" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- Rebate u/s 87A --}}
            <tr class="row-less">
                <td class="rn">
                    @if($computation->tax_regime === 'old')
                        10.8
                    @else
                        10.11
                    @endif
                </td>
                <td>Less: Rebate Under Section 87A</td>
                <td class="ctr">87A</td>
                <td class="amt"><input class="inp" id="tax_87a" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- Tax after Rebate --}}
            <tr class="row-subtotal">
                <td class="rn"></td>
                <td><strong>Tax Payable after Rebate</strong></td>
                <td></td>
                <td class="amt"><input class="inp" id="tax_after_rebate" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- Health & Education Cess --}}
            <tr class="row-normal">
                <td class="rn">
                    @if($computation->tax_regime === 'old')
                        10.9
                    @else
                        10.12
                    @endif
                </td>
                <td>Add: Health and Education Cess @ 4%</td>
                <td class="ctr">Cess</td>
                <td class="amt"><input class="inp" id="tax_cess" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- Gross Tax --}}
            <tr class="row-subtotal">
                <td class="rn"></td>
                <td><strong>Gross Tax</strong></td>
                <td></td>
                <td class="amt"><input class="inp" id="tax_gross" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- Interest & Fees --}}
            <tr class="row-normal">
                <td class="rn">
                    @if($computation->tax_regime === 'old')
                        10.10
                    @else
                        10.13
                    @endif
                </td>
                <td>Add: Total Interest and Fees</td>
                <td class="ctr"></td>
                <td class="amt"><input class="inp" id="int_total" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- Total Tax, Interest and Fees Payable --}}
            <tr class="row-subtotal">
                <td class="rn"></td>
                <td><strong>Total Tax, Interest and Fees Payable</strong></td>
                <td></td>
                <td class="amt"><input class="inp" id="tax_total_payable" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- Rounding u/s 288B --}}
            <tr class="row-normal">
                <td class="rn">
                    @if($computation->tax_regime === 'old')
                        10.11
                    @else
                        10.14
                    @endif
                </td>
                <td>Section 288B Rounding Off</td>
                <td class="ctr"></td>
                <td class="amt"><input class="inp" id="rounding_off_tax" readonly value="0"></td>
                <td></td>
            </tr>

            {{-- Net Tax Payable / Refund --}}
            <tr class="row-final">
                <td class="rn"></td>
                <td><strong>Net Tax Payable / Tax Refund</strong></td>
                <td></td>
                <td class="amt"><input class="inp" id="tax_net" readonly value="0" style="color:#ffdd44;background:#1a3a5c;font-weight:bold;"></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>