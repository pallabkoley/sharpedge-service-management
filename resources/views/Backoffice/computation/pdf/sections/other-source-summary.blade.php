@php $osTotal = $computation->otherSourceEntries->sum('amount'); @endphp
<div class="section-title">F. INCOME FROM OTHER SOURCES</div>
<table>
    <tr class="row-grandtotal">
        <td width="70%"><strong>Total Income From Other Sources</strong></td>
        <td class="amt" width="30%">{{ number_format($osTotal, 2) }}</td>
    </tr>
</table>