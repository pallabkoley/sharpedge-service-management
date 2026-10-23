@php
    $sal = $computation->salaryEntries->sum('amount') - $computation->exemptIncomes->sum('amount');
    $hp = $computation->housePropertyEntries->sum('amount');
    $bp = 1090000;
    $cg = $computation->capitalGainEntries->sum('amount');
    $vda = $computation->vdaEntries->sum('amount');
    $os = $computation->otherSourceEntries->sum('amount');
    $gti = max(0, $sal + $hp + $bp + $cg + $vda + $os);
@endphp

<div class="section-title">GROSS TOTAL INCOME</div>
<table>
    <tr>
        <td width="70%">Income from Salary (I)</td>
        <td class="amt" width="30%">{{ number_format(max(0, $sal), 2) }}</td>
    </tr>
    <tr>
        <td>Income from House Property (II)</td>
        <td class="amt">{{ number_format($hp, 2) }}</td>
    </tr>
    <tr>
        <td>Income from Business / Profession (III)</td>
        <td class="amt">{{ number_format($bp, 2) }}</td>
    </tr>
    <tr>
        <td>Capital Gains (IV)</td>
        <td class="amt">{{ number_format($cg, 2) }}</td>
    </tr>
    <tr>
        <td>VDA / Crypto Income (V)</td>
        <td class="amt">{{ number_format($vda, 2) }}</td>
    </tr>
    <tr>
        <td>Income from Other Sources (VI)</td>
        <td class="amt">{{ number_format($os, 2) }}</td>
    </tr>
    <tr class="row-grandtotal">
        <td><strong>Gross Total Income (GTI)</strong></td>
        <td class="amt">{{ number_format($gti, 2) }}</td>
    </tr>
</table>