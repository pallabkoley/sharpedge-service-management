<div class="modal fade" id="customFieldModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="customFieldForm" method="POST" action="{{ route('admin.client-master.custom-fields.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Custom Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Name <span class="text-danger">*</span></label>
                        <input type="text" name="field_name" class="form-control" required>
                        <small class="text-muted">Unique identifier, e.g., `industry`</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Label <span class="text-danger">*</span></label>
                        <input type="text" name="field_label" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                        <select name="field_type" class="form-select" required>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="email">Email</option>
                            <option value="date">Date</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Select</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Options</label>
                        <input type="text" name="options" class="form-control" placeholder="Comma separated for select fields">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_required" class="form-check-input" id="fieldRequired" value="1">
                        <label class="form-check-label" for="fieldRequired">Required</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="fieldActive" value="1" checked>
                        <label class="form-check-label" for="fieldActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Custom Field</button>
                </div>
            </form>
        </div>
    </div>
</div>