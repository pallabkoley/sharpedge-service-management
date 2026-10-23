<script>
// ============================================================
//  SALARY MODAL FUNCTIONS — COMPLETE & FIXED
// ============================================================

// ----- Helper Functions (safe fallback) -----
if (typeof parseMoney !== 'function') {
    window.parseMoney = function(val) {
        if (val === null || val === undefined) return 0;
        if (typeof val === 'number') return val;
        const cleaned = String(val).replace(/,/g, '').trim();
        return parseFloat(cleaned) || 0;
    };
}
if (typeof money !== 'function') {
    window.money = function(val) {
        const num = parseMoney(val);
        return num.toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    };
}
if (typeof fmtInp !== 'function') {
    window.fmtInp = function(val) {
        return money(val);
    };
}

// ----- Global State -----
let salaryData = [];
let exemptData = [];

// ----- Main Calculation (updates main sheet) -----
window.calcSalary = function() {
    console.log('🔄 calcSalary() called...');
    const salaryJsonEl = document.getElementById('salary_data_json');
    const exemptJsonEl = document.getElementById('exempt_data_json');
    let salaryEntries = [], exemptEntries = [];
    if (salaryJsonEl) {
        try { salaryEntries = JSON.parse(salaryJsonEl.value || '[]'); } 
        catch(e) { console.error('Salary JSON parse error:', e); }
    }
    if (exemptJsonEl) {
        try { exemptEntries = JSON.parse(exemptJsonEl.value || '[]'); } 
        catch(e) { console.error('Exempt JSON parse error:', e); }
    }
    let gross = 0;
    salaryEntries.forEach(item => { gross += parseMoney(item.amount); });
    let exemptTotal = 0;
    exemptEntries.forEach(item => { exemptTotal += parseMoney(item.amount); });
    const chargeable = gross - exemptTotal;
    const grossEl = document.getElementById('s_gross');
    const exemptEl = document.getElementById('s_exempt_total');
    const totalEl = document.getElementById('s_total');
    if (grossEl) grossEl.value = money(gross);
    if (exemptEl) exemptEl.value = money(exemptTotal);
    if (totalEl) totalEl.value = money(chargeable);
    if (typeof window.updateGTI === 'function') window.updateGTI();
    if (typeof window.calcFinal === 'function') window.calcFinal();
};

// ----- Open Modal -----
window.openSalaryModal = function() {
    console.log('📂 Opening Salary Modal...');
    const modalEl = document.getElementById('salaryModal');
    if (!modalEl) { alert('Salary modal missing.'); return; }
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        alert('Bootstrap JS not loaded.');
        return;
    }
    const salaryJsonEl = document.getElementById('salary_data_json');
    const exemptJsonEl = document.getElementById('exempt_data_json');
    let rawSalary = [], rawExempt = [];
    try { rawSalary = JSON.parse(salaryJsonEl?.value || '[]'); } catch(e) { rawSalary = []; }
    try { rawExempt = JSON.parse(exemptJsonEl?.value || '[]'); } catch(e) { rawExempt = []; }
    const cleanSalary = [], extraExempt = [];
    rawSalary.forEach(item => {
        if (item.remarks && item.remarks.includes('(DEDUCTION)')) {
            const cleanItem = { ...item };
            cleanItem.remarks = cleanItem.remarks.replace(/\s*\(DEDUCTION\)/, '').trim();
            extraExempt.push(cleanItem);
        } else {
            cleanSalary.push(item);
        }
    });
    const finalExempt = [...rawExempt];
    extraExempt.forEach(ex => {
        const exists = finalExempt.some(e => e.particular === ex.particular && e.section === ex.section);
        if (!exists) finalExempt.push(ex);
    });
    salaryData = cleanSalary;
    exemptData = finalExempt;
    renderModalTable('modal_salary_rows', salaryData, 'salary_entries', 'modal_tpl_salary');
    renderModalTable('modal_exempt_rows', exemptData, 'exempt_incomes', 'modal_tpl_exempt');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
};

// ----- Render Table -----
window.renderModalTable = function(tbodyId, data, namePrefix, templateId) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) { console.error('❌ Tbody not found:', tbodyId); return; }
    tbody.innerHTML = '';
    const tpl = document.getElementById(templateId);
    if (!tpl) { console.error('❌ Template not found:', templateId); return; }
    if (data.length === 0) {
        const row = tpl.content.firstElementChild.cloneNode(true);
        const html = row.outerHTML.replace(/__INDEX__/g, '0').replace(/__INDEX_PLUS_ONE__/g, '1');
        tbody.insertAdjacentHTML('beforeend', html);
    } else {
        data.forEach((item, idx) => {
            const row = tpl.content.firstElementChild.cloneNode(true);
            let html = row.outerHTML.replace(/__INDEX__/g, idx).replace(/__INDEX_PLUS_ONE__/g, idx + 1);
            tbody.insertAdjacentHTML('beforeend', html);
            const tr = tbody.lastElementChild;
            tr.querySelectorAll('input').forEach(inp => {
                const name = inp.getAttribute('name');
                if (name) {
                    const matches = name.match(/\[([^\]]+)\]\[([^\]]+)\]/);
                    if (matches) {
                        const field = matches[2];
                        if (item[field] !== undefined) inp.value = item[field] || '';
                    }
                }
            });
        });
    }
    renumberModalRows(tbodyId);
    recalcSalaryModal();
};

// ----- Re-number Rows -----
window.renumberModalRows = function(tbodyId) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr');
    rows.forEach((tr, i) => {
        const td = tr.querySelector('.ctr:first-child');
        if (td) td.textContent = i + 1;
        tr.dataset.index = i;
        tr.querySelectorAll('input, select, textarea').forEach(inp => {
            const name = inp.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, '[' + i + ']');
                if (newName !== name) inp.setAttribute('name', newName);
            }
        });
    });
};

// ----- Recalculate Modal Totals -----
window.recalcSalaryModal = function() {
    let gross = 0;
    document.querySelectorAll('#modal_salary_rows input[name*="[amount]"]').forEach(inp => {
        gross += parseMoney(inp.value);
    });
    const grossEl = document.getElementById('modal_gross');
    if (grossEl) grossEl.value = money(gross);
    let exemptTotal = 0;
    document.querySelectorAll('#modal_exempt_rows input[name*="[amount]"]').forEach(inp => {
        exemptTotal += parseMoney(inp.value);
    });
    const exemptEl = document.getElementById('modal_exempt_total');
    if (exemptEl) exemptEl.value = money(exemptTotal);
    const chargeable = gross - exemptTotal;
    const chargeEl = document.getElementById('modal_chargeable');
    if (chargeEl) chargeEl.textContent = fmtInp(chargeable);
};

// ----- ADD ROW (Modal) — UNIQUE NAME TO AVOID CONFLICT -----
window.addSalaryModalRow = function(group) {
    console.log('➕ addSalaryModalRow called with:', group);
    const tbodyId = group === 'modal_salary' ? 'modal_salary_rows' : 'modal_exempt_rows';
    const templateId = group === 'modal_salary' ? 'modal_tpl_salary' : 'modal_tpl_exempt';
    const tbody = document.getElementById(tbodyId);
    if (!tbody) { console.error('❌ Tbody not found:', tbodyId); return; }
    const tpl = document.getElementById(templateId);
    if (!tpl) { console.error('❌ Template not found:', templateId); return; }
    const rows = tbody.querySelectorAll('tr');
    const idx = rows.length;
    const row = tpl.content.firstElementChild.cloneNode(true);
    const html = row.outerHTML.replace(/__INDEX__/g, idx).replace(/__INDEX_PLUS_ONE__/g, idx + 1);
    tbody.insertAdjacentHTML('beforeend', html);
    renumberModalRows(tbodyId);
    recalcSalaryModal();
};

// ----- DELETE ROW (Modal) — UNIQUE NAME -----
window.deleteSalaryModalRow = function(btn, group) {
    const row = btn.closest('tr');
    if (!row) return;
    if (!confirm('Remove this row?')) return;
    const tbodyId = group === 'modal_salary' ? 'modal_salary_rows' : 'modal_exempt_rows';
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    if (tbody.querySelectorAll('tr').length <= 1) { alert('Cannot delete last row.'); return; }
    row.remove();
    renumberModalRows(tbodyId);
    recalcSalaryModal();
};

// ----- Save Modal -----
window.saveSalaryModal = function() {
    console.log('💾 Saving Salary Modal...');
    const salaryRows = document.querySelectorAll('#modal_salary_rows tr');
    const salaryArr = [];
    salaryRows.forEach(tr => {
        const particular = tr.querySelector('[name*="[particular]"]')?.value || '';
        const section = tr.querySelector('[name*="[section]"]')?.value || '';
        const amount = tr.querySelector('[name*="[amount]"]')?.value || '0';
        const remarks = tr.querySelector('[name*="[remarks]"]')?.value || '';
        if (particular || section || parseMoney(amount) !== 0 || remarks) {
            salaryArr.push({ particular, section, amount, remarks });
        }
    });
    const exemptRows = document.querySelectorAll('#modal_exempt_rows tr');
    const exemptArr = [];
    exemptRows.forEach(tr => {
        const particular = tr.querySelector('[name*="[particular]"]')?.value || '';
        const section = tr.querySelector('[name*="[section]"]')?.value || '';
        const amount = tr.querySelector('[name*="[amount]"]')?.value || '0';
        const remarks = tr.querySelector('[name*="[remarks]"]')?.value || '';
        if (particular || section || parseMoney(amount) !== 0 || remarks) {
            exemptArr.push({ particular, section, amount, remarks });
        }
    });
    const salaryJsonEl = document.getElementById('salary_data_json');
    const exemptJsonEl = document.getElementById('exempt_data_json');
    if (salaryJsonEl) salaryJsonEl.value = JSON.stringify(salaryArr);
    if (exemptJsonEl) exemptJsonEl.value = JSON.stringify(exemptArr);
    let gross = 0;
    salaryArr.forEach(item => gross += parseMoney(item.amount));
    let exemptTotal = 0;
    exemptArr.forEach(item => exemptTotal += parseMoney(item.amount));
    const chargeable = gross - exemptTotal;
    const grossEl = document.getElementById('s_gross');
    const exemptEl = document.getElementById('s_exempt_total');
    const totalEl = document.getElementById('s_total');
    if (grossEl) grossEl.value = money(gross);
    if (exemptEl) exemptEl.value = money(exemptTotal);
    if (totalEl) totalEl.value = money(chargeable);
    const modalEl = document.getElementById('salaryModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
    if (typeof window.updateGTI === 'function') window.updateGTI();
    if (typeof window.calcFinal === 'function') window.calcFinal();
    if (typeof window.autoSave === 'function') window.autoSave();
    console.log('✅ Salary modal saved.');
};

// ----- DOM Ready -----
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded – initializing salary sheet...');
    setTimeout(function() { calcSalary(); }, 150);
});

// ----- Force Global Exposure (already via window.functionName) -----
console.log('✅ salary-script.js loaded successfully!');
</script>