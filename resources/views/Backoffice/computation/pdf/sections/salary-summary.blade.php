@php
    $salaryTotal = $computation->salaryEntries->sum('amount');
    $exemptTotal = $computation->exemptIncomes->sum('amount');
    $chargeable = max(0, $salaryTotal - $exemptTotal);
@endphp

<div class="section-title">A. INCOME FROM SALARY</div>
<table>
    <tr>
        <td width="70%"><strong>Gross Salary</strong></td>
        <td class="amt" width="30%">{{ number_format($salaryTotal, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Less: Exemptions / Deductions</strong></td>
        <td class="amt">{{ number_format($exemptTotal, 2) }}</td>
    </tr>
    <tr class="row-grandtotal">
        <td><strong>Income Chargeable Under Salary (I)</strong></td>
        <td class="amt">{{ number_format($chargeable, 2) }}</td>
    </tr>
</table>