@php $hpTotal = $computation->housePropertyEntries->sum('amount'); @endphp
<div class="section-title">B. INCOME FROM HOUSE PROPERTY</div>
<table>
    <tr class="row-grandtotal">
        <td width="70%"><strong>Income From House Property</strong></td>
        <td class="amt" width="30%">{{ number_format($hpTotal, 2) }}</td>
    </tr>
</table>