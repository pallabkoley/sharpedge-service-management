@php $cgTotal = $computation->capitalGainEntries->sum('amount'); @endphp
<div class="section-title">D. CAPITAL GAINS</div>
<table>
    <tr class="row-grandtotal">
        <td width="70%"><strong>Total Capital Gains</strong></td>
        <td class="amt" width="30%">{{ number_format($cgTotal, 2) }}</td>
    </tr>
</table>