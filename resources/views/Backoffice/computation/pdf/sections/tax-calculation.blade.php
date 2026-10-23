@php
    // Get STCG and LTCG from capital gain entries (assuming section field)
    $stcg = $computation->capitalGainEntries->where('section', 'STCG')->sum('amount');
    $ltcg = $computation->capitalGainEntries->where('section', 'LTCG')->sum('amount');
    $vdaIncome = $computation->vdaEntries->sum('amount');

    // Simplified tax calculation (you should expand this to match your live JS logic)
    $normalIncome = max(0, $tti - $stcg - $ltcg - $vdaIncome);
    $taxNormal = 0;
    if ($normalIncome > 250000) {
        $taxNormal += min($normalIncome, 500000) - 250000 * 0.05;
    }
    // Add more slabs as per your actual tax rules

    $taxStcg = $stcg * 0.20;
    $taxLtcg = max(0, $ltcg - 125000) * 0.125;
    $taxVda = $vdaIncome * 0.30;

    $taxBeforeRebate = $taxNormal + $taxStcg + $taxLtcg + $taxVda;
    $rebate = ($tti <= 500000) ? min($taxBeforeRebate, 12500) : 0;
    $taxAfterRebate = max(0, $taxBeforeRebate - $rebate);
    $cess = round($taxAfterRebate * 0.04);
    $grossTax = round(($taxAfterRebate + $cess) / 10) * 10;

    $interest = ($computation->interestEntry->interest_234a ?? 0)
              + ($computation->interestEntry->interest_234b ?? 0)
              + ($computation->interestEntry->interest_234c ?? 0)
              + ($computation->interestEntry->fee_234f ?? 0);

    $totalPayable = $grossTax + $interest;
    $tdsClaimed = $computation->tdsEntries->sum('tds_claimed');
    $tcsClaimed = $computation->tcsEntries->sum('tcs_claimed');
    $advanceTax = $computation->advanceTaxEntries->sum('amount_paid');
    $satPaid = $computation->selfAssessmentTaxEntries->sum('amount_paid');
    $totalPaid = $tdsClaimed + $tcsClaimed + $advanceTax + $satPaid;

    $netTax = $totalPayable - $totalPaid;
    $netTaxRounded = round($netTax / 10) * 10;
    $roundingOff = $netTaxRounded - $netTax;
@endphp

<div class="section-title">I. TAX CALCULATION</div>
<table>
    <tr>
        <td width="70%">Tax on STCG u/s 111A @ 20%</td>
        <td class="amt" width="30%">{{ number_format($taxStcg, 2) }}</td>
    </tr>
    <tr>
        <td>Tax on LTCG u/s 112A @ 12.5% (Excess over ₹1.25L)</td>
        <td class="amt">{{ number_format($taxLtcg, 2) }}</td>
    </tr>
    <tr>
        <td>Tax on VDA / Crypto Income @ 30% u/s 115BBH</td>
        <td class="amt">{{ number_format($taxVda, 2) }}</td>
    </tr>
    <tr>
        <td>Tax on Normal Income (Slab)</td>
        <td class="amt">{{ number_format($taxNormal, 2) }}</td>
    </tr>
    <tr class="row-subtotal">
        <td><strong>Tax Payable</strong></td>
        <td class="amt">{{ number_format($taxBeforeRebate, 2) }}</td>
    </tr>
    <tr>
        <td>Less: Rebate Under Section 87A</td>
        <td class="amt">{{ number_format($rebate, 2) }}</td>
    </tr>
    <tr class="row-subtotal">
        <td><strong>Tax Payable after Rebate</strong></td>
        <td class="amt">{{ number_format($taxAfterRebate, 2) }}</td>
    </tr>
    <tr>
        <td>Add: Health and Education Cess @ 4%</td>
        <td class="amt">{{ number_format($cess, 2) }}</td>
    </tr>
    <tr class="row-subtotal">
        <td><strong>Gross Tax</strong></td>
        <td class="amt">{{ number_format($grossTax, 2) }}</td>
    </tr>
    <tr>
        <td>Add: Total Interest and Fees</td>
        <td class="amt">{{ number_format($interest, 2) }}</td>
    </tr>
    <tr class="row-grandtotal">
        <td><strong>Total Tax, Interest and Fees Payable</strong></td>
        <td class="amt">{{ number_format($totalPayable, 2) }}</td>
    </tr>
    <tr>
        <td>Section 288B Rounding Off</td>
        <td class="amt">{{ number_format($roundingOff, 2) }}</td>
    </tr>
    <tr class="row-final">
        <td><strong>Net Tax Payable / Tax Refund</strong></td>
        <td class="amt">{{ number_format($netTaxRounded, 2) }}</td>
    </tr>
</table>