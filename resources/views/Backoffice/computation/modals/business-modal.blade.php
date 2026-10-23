{{-- resources/views/Backoffice/computation/modals/business-modal.blade.php --}}

<div class="modal fade" id="bpModal" tabindex="-1" aria-labelledby="bpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#2c2c2c;color:#fff;padding:8px 15px;">
                <h5 class="modal-title" id="bpModalLabel">Business / Profession</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <h6 style="font-weight:bold;border-bottom:1px solid #ccc;padding-bottom:8px;margin-bottom:15px;">
                    Auto-fetched from Trading & Profit & Loss Account
                </h6>
                
                {{-- Business Income Display --}}
                <div style="background:#f8f9fa;padding:15px;border-radius:4px;margin-bottom:15px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:14px;">
                        <span style="font-weight:bold;">Business Income from Trading / Profit & Loss Account</span>
                        <span style="font-weight:bold;font-size:18px;color:#1a3a5c;">
                            ₹ {{ number_format($businessIncome ?? 0, 2) }}
                        </span>
                    </div>
                </div>

                {{-- Note --}}
                <div style="background:#fff3cd;padding:12px 15px;border-left:4px solid #ffc107;border-radius:3px;margin-bottom:15px;">
                    <p style="margin:0;font-size:12px;color:#856404;">
                        <strong>ⓘ This amount is automatically generated from the Trading / Profit & Loss module.</strong>
                    </p>
                    <p style="margin:5px 0 0;font-size:11px;color:#856404;">
                        No manual computation is required in this module.<br>
                        To modify this amount, please update the <strong>Trading / Profit & Loss</strong> module.
                    </p>
                </div>

                {{-- Final Summary (read-only) --}}
                <div style="margin-top:15px;background:#f0f4f0;padding:10px 15px;border:2px solid #555;border-radius:3px;">
                    <div style="display:flex;justify-content:space-between;font-weight:bold;font-size:13px;">
                        <span>Income From Business / Profession</span>
                        <span style="color:#1a3a5c;">
                            ₹ {{ number_format($businessIncome ?? 0, 2) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#f5f5f5;padding:8px 15px;">
                <button type="button" class="dyn-btn" style="background:#666;" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>