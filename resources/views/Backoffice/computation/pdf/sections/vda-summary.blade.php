@php $vdaTotal = $computation->vdaEntries->sum('amount'); @endphp
<div class="section-title">E. INCOME FROM TRANSFER OF VIRTUAL DIGITAL ASSETS</div>
<table>
    <tr class="row-grandtotal">
        <td width="70%"><strong>Total VDA Income</strong></td>
        <td class="amt" width="30%">{{ number_format($vdaTotal, 2) }}</td>
    </tr>
</table>