<div class="modal fade" id="caseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="caseForm" method="POST" action="{{ route('admin.client-master.cases.store', $client) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Case</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Case Type --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Case Type <span class="text-danger">*</span></label>
                            <select name="case_type" class="form-select" required>
                                <option value="">Select</option>
                                <option value="GST">GST</option>
                                <option value="Income Tax">Income Tax</option>
                                <option value="Customs">Customs</option>
                                <option value="Excise">Excise</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        {{-- Other Case Type --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Other Case Type</label>
                            <input type="text" name="other_case_type" class="form-control">
                        </div>

                        {{-- Financial Year --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Financial Year</label>
                            <input type="text" name="financial_year" class="form-control" placeholder="e.g., 2024-25">
                        </div>

                        {{-- Reference / Notice / DIN --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Reference Notice / DIN</label>
                            <input type="text" name="reference_notice_din" class="form-control">
                        </div>

                        {{-- Issuing Authority / Office --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Issuing Authority / Office</label>
                            <input type="text" name="issuing_authority_office" class="form-control">
                        </div>

                        {{-- 🔥 Status options updated --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Notice Received">Notice Received</option>
                                <option value="Reply Under Preparation">Reply Under Preparation</option>
                                <option value="Reply Submitted">Reply Submitted</option>
                                <option value="Order Received">Order Received</option>
                                <option value="Appeal Filed">Appeal Filed</option>
                                <option value="In Appeal">In Appeal</option>
                                <option value="Recovery">Recovery</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>

                        {{-- Next Follow-up Date (kept) --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Next Follow-up Date</label>
                            <input type="date" name="next_follow_up_date" class="form-control">
                        </div>

                        {{-- Amount Involved --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Amount Involved</label>
                            <input type="number" step="0.01" name="amount_involved" class="form-control">
                        </div>

                        {{-- 🔥 Case Description – rows বড় করা হয়েছে --}}
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Case Description</label>
                            <textarea name="case_description" class="form-control" rows="4" placeholder="Enter case details..."></textarea>
                        </div>

                        {{-- ❌ Follow-up Log বা Overdue badge এখানে নেই --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Case</button>
                </div>
            </form>
        </div>
    </div>
</div>