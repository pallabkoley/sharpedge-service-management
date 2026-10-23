<div class="modal fade" id="complianceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="complianceForm" method="POST" action="{{ route('admin.client-master.compliances.store', $client) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Compliance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Compliance Type <span class="text-danger">*</span></label>
                            <input type="text" name="compliance_type" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Period</label>
                            <input type="text" name="period" class="form-control" placeholder="e.g., Quarterly, Monthly">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Due Date</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Repeat Automatically</label>
                            <div class="form-check">
                                <input type="checkbox" name="repeat_automatically" class="form-check-input" id="repeatAuto" value="1">
                                <label class="form-check-label" for="repeatAuto">Yes</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Parent Compliance</label>
                            <select name="parent_compliance_id" class="form-select">
                                <option value="">None</option>
                                @foreach($client->compliances as $comp)
                                    <option value="{{ $comp->id }}">{{ $comp->compliance_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Compliance</button>
                </div>
            </form>
        </div>
    </div>
</div>