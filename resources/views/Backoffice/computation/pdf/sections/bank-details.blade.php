<div class="section-title">BANK DETAILS</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Bank Name</th>
            <th>Account Number</th>
            <th>IFSC</th>
            <th>Refund Account</th>
        </tr>
    </thead>
    <tbody>
        @forelse($computation->bankAccounts as $index => $bank)
            <tr>
                <td class="ctr">{{ $loop->iteration }}</td>
                <td>{{ $bank->bank_name ?? 'N/A' }}</td>
                <td class="ctr">{{ $bank->account_number ?? 'N/A' }}</td>
                <td class="ctr">{{ $bank->ifsc ?? 'N/A' }}</td>
                <td class="ctr">{{ $bank->is_refund_account ? '✅ Yes' : '' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted">No bank details available</td></tr>
        @endforelse
    </tbody>
</table>