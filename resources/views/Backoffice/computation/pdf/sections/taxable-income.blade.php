@php
    $ded = $computation->deductionEntries->sum('amount');
    $tti_raw = max(0, $gti - $ded);
    $tti = round($tti_raw / 10) * 10;
@endphp

<div class="section-title">TAXABLE INCOME</div>
<table>
    <tr>
        <td width="70%">Gross Total Income</td>
        <td class="amt" width="30%">{{ number_format($gti, 2) }}</td>
    </tr>
    <tr>
        <td>Less: Deductions Under Chapter VI-A</td>
        <td class="amt">{{ number_format($ded, 2) }}</td>
    </tr>
    <tr class="row-grandtotal">
        <td><strong>Total Taxable Income (TTI)</strong></td>
        <td class="amt">{{ number_format($tti, 2) }}</td>
    </tr>
</table>