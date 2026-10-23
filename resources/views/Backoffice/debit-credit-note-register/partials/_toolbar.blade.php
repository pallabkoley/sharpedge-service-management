{{-- ─── TOOLBAR ─── --}}
<div class="gst-toolbar" style="display:flex;align-items:center;gap:14px;padding:6px 14px;background:var(--off-white);border-bottom:1px solid var(--border-light);flex-wrap:wrap;">
    <label style="font-size:11px;font-weight:600;color:var(--text-mid);">
        GST Type:
        <select id="gstType" onchange="onGstTypeChange()" style="margin-left:4px;padding:2px 8px;border-radius:4px;border:1px solid var(--border);font-size:11px;">
            <option value="intra">Intra State</option>
            <option value="inter">Inter State</option>
        </select>
    </label>
    <label style="font-size:11px;font-weight:600;color:var(--text-mid);">
        Mode:
        <span class="mode-toggle" style="display:inline-flex;gap:2px;background:var(--border-light);border-radius:4px;padding:2px;margin-left:4px;">
            <button class="active" id="modeManual" onclick="setMode('manual')" style="border:none;background:transparent;padding:2px 10px;border-radius:3px;font-size:10.5px;font-weight:600;color:var(--text-mid);">Manual</button>
            <button id="modeAuto" onclick="setMode('auto')" style="border:none;background:transparent;padding:2px 10px;border-radius:3px;font-size:10.5px;font-weight:600;color:var(--text-mid);">Auto</button>
        </span>
    </label>
    <button class="btn-erp btn-outline-blue btn-sm" onclick="applyAutoGst()" style="font-size:10px;padding:2px 12px;">
        <i class="bi bi-calculator"></i> Apply Auto GST
    </button>
</div>