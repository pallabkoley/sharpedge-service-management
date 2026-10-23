@php $dedTotal = $computation->deductionEntries->sum('amount'); @endphp
<div class="section-title">H. DEDUCTIONS UNDER CHAPTER VI-A</div>
<table>
    <tr class="row-grandtotal">
        <td width="70%"><strong>Total Deductions Under Chapter VI-A</strong></td>
        <td class="amt" width="30%">{{ number_format($dedTotal, 2) }}</td>
    </tr>
</table>