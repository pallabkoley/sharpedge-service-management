<div class="section-title">EXEMPT INCOME</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Particulars</th>
            <th>Section</th>
            <th class="text-right">Amount (₹)</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @php $totalExempt = 0; @endphp
        @forelse($computation->exemptIncomes as $index => $item)
            @php $totalExempt += $item->amount; @endphp
            <tr>
                <td class="ctr">{{ $loop->iteration }}</td>
                <td>{{ $item->particular ?? 'N/A' }}</td>
                <td>{{ $item->section ?? 'N/A' }}</td>
                <td class="amt">{{ number_format($item->amount ?? 0, 2) }}</td>
                <td>{{ $item->remarks ?? '' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted">No exempt income entries</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="row-grandtotal">
            <td colspan="3" class="text-right">Total Exempt Income</td>
            <td class="amt">{{ number_format($totalExempt, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>