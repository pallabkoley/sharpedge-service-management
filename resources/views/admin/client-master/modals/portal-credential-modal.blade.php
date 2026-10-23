<div class="modal fade" id="portalCredentialModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="portalCredentialForm" method="POST" action="{{ route('admin.client-master.portal-credentials.store', $client) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Portal Credential</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Portal <span class="text-danger">*</span></label>
                        <select name="portal" class="form-select" required>
                            <option value="">Select</option>
                            <option value="GST Portal">GST Portal</option>
                            <option value="Income Tax">Income Tax</option>
                            <option value="MCA">MCA</option>
                            <option value="TDS">TDS</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Other Portal Name</label>
                        <input type="text" name="other_portal_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Login Username</label>
                        <input type="text" name="login_username" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">Leave blank to keep existing password (if editing).</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Credential</button>
                </div>
            </form>
        </div>
    </div>
</div>