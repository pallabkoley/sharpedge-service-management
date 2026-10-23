<div class="modal fade" id="bankAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="bankAccountForm" method="POST" action="{{ route('admin.client-master.bank-accounts.store', $client) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Bank Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">IFSC Code</label>
                        <input type="text" name="ifsc_code" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Type</label>
                        <select name="account_type" class="form-select">
                            <option value="">Select</option>
                            <option value="Savings">Savings</option>
                            <option value="Current">Current</option>
                            <option value="Overdraft">Overdraft</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_primary" class="form-check-input" id="primaryBank" value="1">
                        <label class="form-check-label" for="primaryBank">Set as Primary Account</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Bank Account</button>
                </div>
            </form>
        </div>
    </div>
</div>