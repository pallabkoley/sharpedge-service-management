<script>
// ============================================================
//  MODULAR DYNAMIC ROW SYSTEM + CSRF FIX + BARCODE UPLOAD/REMOVE
//  FULLY FIXED: HP, VDA, CG, OS, DEDUCTIONS, TAX PAID (JSON)
// ============================================================

// ===== 1. Cached DOM references =====
const DOM = {
    templates: {},
    tbodies: {},
    groups: ['bank', 'exempt', 'salary', 'hp', 'bp', 'cg', 'vda', 'os', 'ded', 'tds', 'tcs', 'at']
};

function getTemplate(group) {
    if (DOM.templates[group]) return DOM.templates[group];
    const tpl = document.getElementById('tpl_' + group);
    if (tpl) DOM.templates[group] = tpl;
    return tpl;
}

function getTbody(group) {
    if (DOM.tbodies[group]) return DOM.tbodies[group];
    const tbody = document.getElementById(group + '_rows');
    if (tbody) DOM.tbodies[group] = tbody;
    return tbody;
}

// ===== 2. Number formatting (FIXED: handles commas, currency symbols, whitespace) =====
function money(v) {
    return Math.round(v).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// 🔥 FIXED: Properly handles comma-separated values like "1,000", "₹10,000", "$5,000"
function parseMoney(v) {
    if (v === null || v === undefined || v === '') return 0;
    if (typeof v === 'number') return v;
    // Remove currency symbols, commas, spaces, and convert to number
    const cleaned = String(v).replace(/[₹,$,€,£,\s]/g, '').replace(/,/g, '').trim();
    const result = parseFloat(cleaned);
    return isNaN(result) ? 0 : result;
}

function fmt(v) { return money(v); }
function fmtInp(v) { if (v < 0) return '-' + money(Math.abs(v)); return money(v); }

// ===== 3. Add / Delete Row =====
function addDynRow(group, tbodyId, templateId) {
    const tpl = document.getElementById(templateId) || getTemplate(group);
    const tbody = document.getElementById(tbodyId) || getTbody(group);
    if (!tpl) { console.error('❌ Template not found:', templateId || 'tpl_' + group); return; }
    if (!tbody) { console.error('❌ Tbody not found:', tbodyId || group + '_rows'); return; }
    const rows = tbody.querySelectorAll('tr[data-group="' + group + '"]');
    const idx = rows.length;
    const row = tpl.content.firstElementChild.cloneNode(true);
    const html = row.outerHTML
        .replace(/__INDEX__/g, idx)
        .replace(/__INDEX_PLUS_ONE__/g, idx + 1);
    tbody.insertAdjacentHTML('beforeend', html);
    renumberGroup(group, tbodyId);
    triggerRecalc(group);
    autoSave();
}

function deleteDynRow(btn, group, tbodyId) {
    const row = btn.closest('tr');
    if (!row) return;
    if (!confirm('Remove this row?')) return;
    row.remove();
    renumberGroup(group, tbodyId);
    triggerRecalc(group);
    autoSave();
}

function renumberGroup(group, tbodyId) {
    const tbody = document.getElementById(tbodyId) || getTbody(group);
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr[data-group="' + group + '"]');
    rows.forEach((tr, i) => {
        const td = tr.querySelector('.rn, .ctr:first-child');
        if (td) td.textContent = i + 1;
        tr.dataset.index = i;
        tr.querySelectorAll('input, select').forEach(inp => {
            const name = inp.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, '[' + i + ']');
                if (newName !== name) inp.setAttribute('name', newName);
            }
        });
    });
}

// ===== 4. Calculation Map (with fallback) =====
function calcSalary() {}
function calcHP() {}
function calcBP() {}
function calcCG() {}
function calcVDA() {}
function calcOS() {}
function calcDed() {}
function calcTDS() {}
function calcTCS() {}
function calcAT() {}

const calcMap = {
    salary: calcSalary,
    hp: calcHP,
    bp: calcBP,
    cg: calcCG,
    vda: calcVDA,
    os: calcOS,
    ded: calcDed,
    tds: calcTDS,
    tcs: calcTCS,
    at: calcAT
};

function triggerRecalc(group) {
    if (calcMap[group] && typeof calcMap[group] === 'function') calcMap[group]();
    if (typeof updateGTI === 'function') updateGTI();
    if (typeof calcFinal === 'function') calcFinal();
}

// ===== 5. Event Delegation =====
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.dyn-btn.add');
        if (addBtn) {
            const group = addBtn.dataset.group;
            const tbody = addBtn.dataset.tbody || group + '_rows';
            const template = addBtn.dataset.template || 'tpl_' + group;
            if (group && !group.startsWith('modal_')) {
                addDynRow(group, tbody, template);
                e.preventDefault();
                e.stopPropagation();
                return;
            }
        }
        const delBtn = e.target.closest('.dyn-btn.del');
        if (delBtn) {
            const group = delBtn.dataset.group;
            const tbody = group + '_rows';
            if (group && !group.startsWith('modal_')) {
                deleteDynRow(delBtn, group, tbody);
                e.preventDefault();
                e.stopPropagation();
                return;
            }
        }
        const collBtn = e.target.closest('.collapse-btn');
        if (collBtn) {
            const target = collBtn.dataset.toggle;
            if (target) {
                const el = document.getElementById(target);
                if (el) {
                    if (el.style.display === 'none') { el.style.display = ''; collBtn.textContent = '▼'; }
                    else { el.style.display = 'none'; collBtn.textContent = '▶'; }
                }
            }
            e.preventDefault();
        }
    });
});

// ===== 6. Save / Auto‑save =====
let saveTimer;

function autoSave() {
    clearTimeout(saveTimer);
    const statusEl = document.getElementById('save-status');
    if (statusEl) statusEl.textContent = '● Saving...';
    saveTimer = setTimeout(() => saveSheet('draft'), 1500);
}

function getCsrfToken() {
    let meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.content;
    let input = document.getElementById('csrf_token_input') || document.querySelector('input[name="_token"]');
    if (input) return input.value;
    return '';
}

function saveSheet(action) {
    let url = '{{ route("backoffice.computations.save-computation", $computation->id) }}';
    let data = { computation_status: action === 'submit' ? 'submitted' : 'draft' };

    document.querySelectorAll('.master-val input, .master-val select').forEach(el => {
        if (el.name) data[el.name] = el.value;
    });

    const groups = [
        { id: 'bank_rows', key: 'bank_accounts', fields: ['bank_name','account_number','ifsc'] },
        { id: 'exempt_rows', key: 'exempt_incomes', fields: ['particular','section','amount','remarks'] },
        { id: 'hp_rows', key: 'hp_entries', fields: ['particular','section','amount'] },
        { id: 'bp_rows', key: 'bp_entries', fields: ['particular','section','amount'] },
        { id: 'cg_rows', key: 'cg_entries', fields: ['particular','section','amount'] },
        { id: 'vda_rows', key: 'vda_entries', fields: ['particular','section','amount'] },
        { id: 'os_rows', key: 'os_entries', fields: ['particular','section','amount'] },
        { id: 'ded_rows', key: 'ded_entries', fields: ['particular','section','amount'] },
        { id: 'tds_rows', key: 'tds_entries', fields: ['tan_number','deductor_name','section_code','amount_paid','tds_deducted','tds_claimed'] },
        { id: 'tcs_rows', key: 'tcs_entries', fields: ['tan_number','collector_name','section_code','amount_collected','tcs_collected','tcs_claimed'] },
        { id: 'at_rows', key: 'advance_tax_entries', fields: ['payment_date','bsr_code','challan_no','installment','amount_paid'] }
    ];

    groups.forEach(g => {
        let rows = document.querySelectorAll('#' + g.id + ' tr[data-group="' + g.id.replace('_rows','') + '"]');
        let arr = [];
        rows.forEach(row => {
            let obj = {};
            g.fields.forEach(f => {
                let inp = row.querySelector('[name*="[' + f + ']"]');
                if (inp) obj[f] = inp.value;
            });
            if (Object.values(obj).some(v => v !== '' && v !== '0')) arr.push(obj);
        });
        data[g.key] = arr;
    });

    // =====  FIX: Save BOTH salary and exemption data  =====
    const salaryJson = document.getElementById('salary_data_json')?.value || '[]';
    const exemptJson = document.getElementById('exempt_data_json')?.value || '[]';

    let salaryEntries = [];
    let exemptEntries = [];

    try {
        const salaryData = JSON.parse(salaryJson);
        if (Array.isArray(salaryData)) salaryEntries = salaryData;
    } catch (e) {}

    try {
        const exemptData = JSON.parse(exemptJson);
        if (Array.isArray(exemptData)) exemptEntries = exemptData;
    } catch (e) {}

    // Merge exemptions into salary_entries with a flag in remarks
    const combinedEntries = [...salaryEntries];
    exemptEntries.forEach(ex => {
        let remarks = ex.remarks || '';
        if (!remarks.includes('(DEDUCTION)')) {
            remarks = (remarks ? remarks + ' ' : '') + '(DEDUCTION)';
        }
        combinedEntries.push({
            particular: ex.particular || '',
            section: ex.section || '',
            amount: ex.amount || '0',
            remarks: remarks.trim()
        });
    });

    data.salary_entries = combinedEntries;

    // ✅ House Property from hidden JSON
    const hpJson = document.getElementById('hp_data_json')?.value || '[]';
    let hpEntries = [];
    try {
        const hpData = JSON.parse(hpJson);
        if (Array.isArray(hpData)) hpEntries = hpData;
    } catch (e) {}
    data.hp_entries = hpEntries;

    // ✅ VDA from hidden JSON
    const vdaJson = document.getElementById('vda_data_json')?.value || '[]';
    let vdaEntries = [];
    try {
        const vdaData = JSON.parse(vdaJson);
        if (Array.isArray(vdaData)) vdaEntries = vdaData;
    } catch (e) {}
    data.vda_entries = vdaEntries;

    // ✅ Capital Gain from hidden JSON
    const cgJson = document.getElementById('cg_data_json')?.value || '[]';
    let cgEntries = [];
    try {
        const cgData = JSON.parse(cgJson);
        if (Array.isArray(cgData)) cgEntries = cgData;
    } catch (e) {}
    data.cg_entries = cgEntries;

    // ✅ Other Sources from hidden JSON
    const osJson = document.getElementById('os_data_json')?.value || '[]';
    let osEntries = [];
    try {
        const osData = JSON.parse(osJson);
        if (Array.isArray(osData)) osEntries = osData;
    } catch (e) {}
    data.os_entries = osEntries;

    // ✅ Deductions from hidden JSON
    const dedJson = document.getElementById('ded_data_json')?.value || '[]';
    let dedEntries = [];
    try {
        const dedData = JSON.parse(dedJson);
        if (Array.isArray(dedData)) dedEntries = dedData;
    } catch (e) {}
    data.ded_entries = dedEntries;

    // ===== 🔥 NEW: Tax Paid from unified JSON  =====
    const taxPaidJson = document.getElementById('tax_paid_data_json')?.value || '{}';
    let taxPaidData = {};
    try {
        taxPaidData = JSON.parse(taxPaidJson);
        if (typeof taxPaidData !== 'object') taxPaidData = {};
    } catch (e) {
        console.error('Error parsing tax_paid_data_json:', e);
    }

    // ✅ Send the entire tax_paid_data object to the controller
    data.tax_paid_data = taxPaidData;

    // Override TDS, TCS, Advance Tax, SAT from the JSON (if present)
    // This ensures the controller has the data even if the DOM rows are missing
    if (taxPaidData.tds_entries && Array.isArray(taxPaidData.tds_entries)) {
        data.tds_entries = taxPaidData.tds_entries;
    }
    if (taxPaidData.tcs_entries && Array.isArray(taxPaidData.tcs_entries)) {
        data.tcs_entries = taxPaidData.tcs_entries;
    }
    if (taxPaidData.advance_tax_entries && Array.isArray(taxPaidData.advance_tax_entries)) {
        data.advance_tax_entries = taxPaidData.advance_tax_entries;
    }
    if (taxPaidData.sat_entries && Array.isArray(taxPaidData.sat_entries)) {
        data.sat_entries = taxPaidData.sat_entries;
    } else {
        data.sat_entries = [];
    }

    // ===== END Tax Paid JSON =====

    const csrfToken = getCsrfToken();

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(json => {
        const statusEl = document.getElementById('save-status');
        if (json.success) {
            if (statusEl) statusEl.textContent = '● Saved ' + new Date().toLocaleTimeString();
            if (action === 'submit') {
                alert('✅ Computation submitted successfully!');
                window.location.href = '{{ route("backoffice.computations.index") }}';
            }
        } else {
            if (statusEl) statusEl.textContent = '✗ Error: ' + json.message;
            console.error(json);
            alert('Error: ' + json.message);
        }
    })
    .catch(err => {
        const statusEl = document.getElementById('save-status');
        if (statusEl) statusEl.textContent = '✗ Network Error';
        console.error(err);
        alert('Network error. Please try again.');
    });
}

// ===== 7. Toggle / Clear / Demo =====
function toggleAll(expand) {
    ['sec_salary','sec_hp','sec_bp','sec_cg','sec_vda','sec_os','sec_ded','sec_tax','sec_int','sec_tds','sec_tcs','sec_at','sec_sat','sec_bank','sec_exempt'].forEach(id => {
        let el = document.getElementById(id); if (el) el.style.display = expand ? '' : 'none';
    });
    document.querySelectorAll('.collapse-btn').forEach(b => b.textContent = expand ? '▼' : '▶');
}

function clearAll() {
    if (!confirm('Clear all data?')) return;
    document.querySelectorAll('input:not([readonly])').forEach(inp => { if (!inp.classList.contains('inp-text')) inp.value = '0'; });
    Object.values(calcMap).forEach(fn => { if (typeof fn === 'function') fn(); });
    if (typeof updateGTI === 'function') updateGTI();
    if (typeof calcFinal === 'function') calcFinal();
    autoSave();
}

function loadDemo() {
    Object.values(calcMap).forEach(fn => { if (typeof fn === 'function') fn(); });
    if (typeof updateGTI === 'function') updateGTI();
    if (typeof calcFinal === 'function') calcFinal();
    alert('Demo data loaded & recalculated!');
    autoSave();
}

// ===== 8. Barcode =====
function uploadBarcode(event) {
    const fileInput = event.target;
    const file = fileInput.files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append('media', file);
    formData.append('type', 'barcode');
    const url = '{{ route("backoffice.computations.upload-media", $computation->id) }}';
    const csrfToken = getCsrfToken();
    const statusEl = document.getElementById('save-status');
    if (statusEl) statusEl.textContent = '● Uploading...';
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        body: formData
    })
    .then(res => res.json())
    .then(json => {
        if (json.success) {
            document.getElementById('barcode_display').innerHTML = `<img src="${json.url}" alt="Barcode">`;
            if (statusEl) statusEl.textContent = '● Barcode uploaded ✅';
            document.getElementById('barcode-date').textContent = new Date().toLocaleString('en-IN');
        } else {
            if (statusEl) statusEl.textContent = '✗ Upload failed: ' + json.message;
            alert('Upload failed: ' + json.message);
        }
    })
    .catch(err => {
        if (statusEl) statusEl.textContent = '✗ Network error';
        alert('Error uploading barcode');
        console.error(err);
    });
    fileInput.value = '';
}

function removeBarcode() {
    const url = '{{ route("backoffice.computations.remove-media", $computation->id) }}';
    const csrfToken = getCsrfToken();
    if (!confirm('Remove the uploaded barcode?')) return;
    const statusEl = document.getElementById('save-status');
    if (statusEl) statusEl.textContent = '● Removing...';
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ type: 'barcode' })
    })
    .then(res => res.json())
    .then(json => {
        if (json.success) {
            document.getElementById('barcode_display').innerHTML = '||| ██████ ████ ██████ |||';
            if (statusEl) statusEl.textContent = '● Barcode removed ✅';
            document.getElementById('barcode-date').textContent = new Date().toLocaleString('en-IN');
        } else {
            if (statusEl) statusEl.textContent = '✗ Remove failed: ' + json.message;
            alert('Remove failed: ' + json.message);
        }
    })
    .catch(err => {
        if (statusEl) statusEl.textContent = '✗ Network error';
        alert('Error removing barcode');
        console.error(err);
    });
}

// ===== 9. Initialisation =====
window.addEventListener('load', function() {
    console.log('✅ Computation sheet loaded.');

    // Verify templates
    DOM.groups.forEach(g => {
        const tpl = document.getElementById('tpl_' + g);
        const tbody = document.getElementById(g + '_rows');
        if (tpl) DOM.templates[g] = tpl;
        if (tbody) DOM.tbodies[g] = tbody;
    });

    // Renumber
    DOM.groups.forEach(g => { renumberGroup(g, g + '_rows'); });

    // Run calculations
    Object.values(calcMap).forEach(fn => { if (typeof fn === 'function') fn(); });
    if (typeof updateGTI === 'function') updateGTI();
    if (typeof calcFinal === 'function') calcFinal();

    // Set dates & clock
    const now = new Date();
    document.getElementById('comp-date').textContent = now.toLocaleDateString('en-IN');
    document.getElementById('barcode-date').textContent = now.toLocaleString('en-IN');
    document.getElementById('footer-ts').textContent = now.toLocaleString('en-IN');
    function tick() {
        document.getElementById('sb_time').textContent = new Date().toLocaleTimeString('en-IN');
    }
    tick();
    setInterval(tick, 1000);

    // Auto-save listeners
    document.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('change', autoSave);
        el.addEventListener('keyup', function(e) { if (e.key === 'Enter') autoSave(); });
    });

    console.log('✅ System ready.');
});
</script>