<div class="modal fade" id="turnoverModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="turnoverForm" method="POST" action="{{ route('admin.client-master.turnovers.store', $client) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Turnover</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Financial Year <span class="text-danger">*</span></label>
                        <input type="text" name="financial_year" class="form-control" placeholder="e.g., 2024-25" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Month <span class="text-danger">*</span></label>
                        <select name="month" class="form-select" required>
                            <option value="">Select</option>
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Turnover</button>
                </div>
            </form>
        </div>
    </div>
</div>