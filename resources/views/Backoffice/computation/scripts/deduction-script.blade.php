<script>
// ============================================================
//  DEDUCTIONS – MODAL + MAIN SHEET SUMMARY
//  WITH FULL INITIALIZATION ON PAGE LOAD
// ============================================================

let dedData = [];

// ----- Main sheet: update ded_total from hidden value -----
function calcDed() {
    const totalEl = document.getElementById('ded_total_value');
    const displayEl = document.getElementById('ded_total');
    if (totalEl && displayEl) {
        const val = parseFloat(totalEl.value) || 0;
        displayEl.value = fmtInp(val);
    }
}

// ----- NEW: Initialize Deductions total from JSON on page load -----
function initDedTotal() {
    console.log('🔄 initDedTotal() called...');
    const dedJson = document.getElementById('ded_data_json')?.value || '[]';
    let entries = [];
    try {
        entries = JSON.parse(dedJson);
        if (!Array.isArray(entries)) entries = [];
    } catch(e) {
        entries = [];
        console.error('Error parsing Deductions JSON:', e);
    }

    let total = 0;
    entries.forEach(item => {
        total += parseMoney(item.amount);
    });

    console.log('💰 Deductions Total:', total, '| Entries:', entries.length);

    // Update hidden total field
    const totalValueEl = document.getElementById('ded_total_value');
    if (totalValueEl) totalValueEl.value = total;

    // Update display field
    const displayEl = document.getElementById('ded_total');
    if (displayEl) displayEl.value = fmtInp(total);

    // Update GTI if needed
    if (typeof updateGTI === 'function') updateGTI();
}

// ----- Open Modal -----
function openDedModal() {
    console.log('📂 Opening Deductions Modal...');
    const modalEl = document.getElementById('dedModal');
    if (!modalEl) {
        alert('Deductions modal is missing from the page.');
        return;
    }
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        alert('Bootstrap JS is not loaded.');
        return;
    }

    // Load data from hidden JSON
    const dedJson = document.getElementById('ded_data_json')?.value || '[]';
    try {
        dedData = JSON.parse(dedJson);
        if (!Array.isArray(dedData)) dedData = [];
    } catch(e) {
        dedData = [];
    }

    renderDedModalTable(dedData);
    recalcDedModal();

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// ----- Render table inside modal -----
function renderDedModalTable(data) {
    const tbody = document.getElementById('modal_ded_rows');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (data.length === 0) {
        // Show ONE blank row
        const tpl = document.getElementById('modal_tpl_ded');
        if (!tpl) return;
        const row = tpl.content.firstElementChild.cloneNode(true);
        const html = row.outerHTML
            .replace(/__INDEX__/g, '0')
            .replace(/__INDEX_PLUS_ONE__/g, '1');
        tbody.insertAdjacentHTML('beforeend', html);
    } else {
        data.forEach((item, idx) => {
            const tpl = document.getElementById('modal_tpl_ded');
            if (!tpl) return;
            const row = tpl.content.firstElementChild.cloneNode(true);
            let html = row.outerHTML
                .replace(/__INDEX__/g, idx)
                .replace(/__INDEX_PLUS_ONE__/g, idx+1);
            tbody.insertAdjacentHTML('beforeend', html);
            const tr = tbody.lastElementChild;
            const inputs = tr.querySelectorAll('input');
            inputs.forEach(inp => {
                const name = inp.getAttribute('name');
                if (name) {
                    const matches = name.match(/\[([^\]]+)\]\[([^\]]+)\]/);
                    if (matches) {
                        const field = matches[2];
                        if (item[field] !== undefined) {
                            inp.value = item[field] || '';
                        }
                    }
                }
            });
        });
    }
    renumberDedModalRows();
}

// ----- Renumber modal rows -----
function renumberDedModalRows() {
    const tbody = document.getElementById('modal_ded_rows');
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr');
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

// ----- Recalculate modal summary -----
function recalcDedModal() {
    let total = 0;
    document.querySelectorAll('#modal_ded_rows input[name*="[amount]"]').forEach(inp => {
        total += parseMoney(inp.value);
    });
    
    const totalEl = document.getElementById('modal_ded_total');
    if (totalEl) totalEl.value = money(total);
}

// ----- Add row in modal -----
function addDedModalRow() {
    const tbody = document.getElementById('modal_ded_rows');
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr');
    const idx = rows.length;
    const tpl = document.getElementById('modal_tpl_ded');
    if (!tpl) return;
    const row = tpl.content.firstElementChild.cloneNode(true);
    const html = row.outerHTML
        .replace(/__INDEX__/g, idx)
        .replace(/__INDEX_PLUS_ONE__/g, idx + 1);
    tbody.insertAdjacentHTML('beforeend', html);
    renumberDedModalRows();
    recalcDedModal();
}

// ----- Delete row in modal -----
function deleteDedModalRow(btn) {
    const row = btn.closest('tr');
    if (!row) return;
    if (!confirm('Remove this row?')) return;
    row.remove();
    renumberDedModalRows();
    recalcDedModal();
}

// ----- Save modal and update main sheet -----
function saveDedModal() {
    console.log('💾 Saving Deductions Modal...');
    
    // 1. Collect rows from modal
    const rows = document.querySelectorAll('#modal_ded_rows tr');
    const entries = [];
    rows.forEach((tr) => {
        const particular = tr.querySelector('[name*="[particular]"]')?.value || '';
        const section = tr.querySelector('[name*="[section]"]')?.value || '';
        const amount = tr.querySelector('[name*="[amount]"]')?.value || '0';
        const remarks = tr.querySelector('[name*="[remarks]"]')?.value || '';
        if (particular || section || parseMoney(amount) !== 0 || remarks) {
            entries.push({ particular, section, amount, remarks });
        }
    });

    // 2. Compute total
    let total = 0;
    entries.forEach(item => {
        total += parseMoney(item.amount);
    });

    console.log('📊 Deductions Entries saved:', entries.length, 'Total:', total);

    // 3. Update hidden fields
    const dataJsonEl = document.getElementById('ded_data_json');
    const totalValueEl = document.getElementById('ded_total_value');
    if (dataJsonEl) dataJsonEl.value = JSON.stringify(entries);
    if (totalValueEl) totalValueEl.value = total;

    // 4. Update main sheet summary row
    const displayEl = document.getElementById('ded_total');
    if (displayEl) displayEl.value = fmtInp(total);

    // 5. Close modal
    const modalEl = document.getElementById('dedModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    // 6. Trigger GTI and final recalculation
    if (typeof updateGTI === 'function') updateGTI();
    if (typeof calcFinal === 'function') calcFinal();
    if (typeof autoSave === 'function') autoSave();

    console.log('✅ Deductions modal saved successfully!');
}

// ----- Initialize Deductions total on page load -----
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded – initializing Deductions...');
    setTimeout(function() {
        initDedTotal();
        calcDed();
    }, 150);
});

console.log('✅ deduction-script.js loaded successfully!');
</script>