<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-bank me-2"></i>Bank Accounts</h6>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bankAccountModal">
            <i class="bi bi-plus-circle me-1"></i> Add Bank Account
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>IFSC</th>
                        <th>Account Type</th>
                        <th>Primary</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->bankAccounts as $account)
                    <tr>
                        <td>{{ $account->bank_name }}</td>
                        <td>{{ $account->account_number }}</td>
                        <td>{{ $account->ifsc_code ?? 'N/A' }}</td>
                        <td>{{ $account->account_type ?? 'N/A' }}</td>
                        <td>
                            @if($account->is_primary)
                                <span class="badge bg-success">Primary</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="editBankAccount({{ $account->id }})"><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('admin.client-master.bank-accounts.destroy', $account) }}', 'Bank Account')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No bank accounts added.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>