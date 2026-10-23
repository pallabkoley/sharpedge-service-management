@php
    $tdsClaimed = $computation->tdsEntries->sum('tds_claimed');
    $tcsClaimed = $computation->tcsEntries->sum('tcs_claimed');
    $advanceTax = $computation->advanceTaxEntries->sum('amount_paid');
    $satPaid = $computation->selfAssessmentTaxEntries->sum('amount_paid');
    $totalTaxPaid = $tdsClaimed + $tcsClaimed + $advanceTax + $satPaid;
@endphp

<div class="section-title">TAX PAID</div>
<table>
    <tr>
        <td width="70%">TDS Claimed</td>
        <td class="amt" width="30%">{{ number_format($tdsClaimed, 2) }}</td>
    </tr>
    <tr>
        <td>TCS Claimed</td>
        <td class="amt">{{ number_format($tcsClaimed, 2) }}</td>
    </tr>
    <tr>
        <td>Advance Tax Paid</td>
        <td class="amt">{{ number_format($advanceTax, 2) }}</td>
    </tr>
    <tr>
        <td>Self Assessment Tax (SAT) Paid</td>
        <td class="amt">{{ number_format($satPaid, 2) }}</td>
    </tr>
    <tr class="row-grandtotal">
        <td><strong>Total Tax Paid</strong></td>
        <td class="amt">{{ number_format($totalTaxPaid, 2) }}</td>
    </tr>
</table>