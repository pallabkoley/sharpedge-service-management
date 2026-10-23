<script>
// ============================================================
//  VDA (VIRTUAL DIGITAL ASSETS) – MODAL + MAIN SHEET SUMMARY
//  WITH FULL INITIALIZATION ON PAGE LOAD
// ============================================================

let vdaData = [];

// ----- Main sheet: update vda_total from hidden value -----
function calcVDA() {
    const totalEl = document.getElementById('vda_total_value');
    const displayEl = document.getElementById('vda_total');
    if (totalEl && displayEl) {
        const val = parseFloat(totalEl.value) || 0;
        displayEl.value = fmtInp(val);
    }
}

// ----- NEW: Initialize VDA total from JSON on page load -----
function initVdaTotal() {
    console.log('🔄 initVdaTotal() called...');
    const vdaJson = document.getElementById('vda_data_json')?.value || '[]';
    let entries = [];
    try {
        entries = JSON.parse(vdaJson);
        if (!Array.isArray(entries)) entries = [];
    } catch(e) {
        entries = [];
        console.error('Error parsing VDA JSON:', e);
    }

    let total = 0;
    entries.forEach(item => {
        total += parseMoney(item.amount);
    });

    console.log('💰 VDA Total:', total, '| Entries:', entries.length);

    // Update hidden total field
    const totalValueEl = document.getElementById('vda_total_value');
    if (totalValueEl) totalValueEl.value = total;

    // Update display field
    const displayEl = document.getElementById('vda_total');
    if (displayEl) displayEl.value = fmtInp(total);

    // Update GTI if needed
    if (typeof updateGTI === 'function') updateGTI();
}

// ----- Open Modal -----
function openVdaModal() {
    console.log('📂 Opening VDA Modal...');
    const modalEl = document.getElementById('vdaModal');
    if (!modalEl) {
        alert('VDA modal is missing from the page.');
        return;
    }
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        alert('Bootstrap JS is not loaded.');
        return;
    }

    // Load data from hidden JSON
    const vdaJson = document.getElementById('vda_data_json')?.value || '[]';
    try {
        vdaData = JSON.parse(vdaJson);
        if (!Array.isArray(vdaData)) vdaData = [];
    } catch(e) {
        vdaData = [];
    }

    renderVdaModalTable(vdaData);
    recalcVdaModal();

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// ----- Render table inside modal -----
function renderVdaModalTable(data) {
    const tbody = document.getElementById('modal_vda_rows');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (data.length === 0) {
        // Show ONE blank row
        const tpl = document.getElementById('modal_tpl_vda');
        if (!tpl) return;
        const row = tpl.content.firstElementChild.cloneNode(true);
        const html = row.outerHTML
            .replace(/__INDEX__/g, '0')
            .replace(/__INDEX_PLUS_ONE__/g, '1');
        tbody.insertAdjacentHTML('beforeend', html);
    } else {
        data.forEach((item, idx) => {
            const tpl = document.getElementById('modal_tpl_vda');
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
    renumberVdaModalRows();
}

// ----- Renumber modal rows -----
function renumberVdaModalRows() {
    const tbody = document.getElementById('modal_vda_rows');
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
function recalcVdaModal() {
    let total = 0;
    document.querySelectorAll('#modal_vda_rows input[name*="[amount]"]').forEach(inp => {
        total += parseMoney(inp.value);
    });
    
    const totalEl = document.getElementById('modal_vda_total');
    if (totalEl) totalEl.value = money(total);
    
    // Tax @ 30%
    const tax = total * 0.30;
    const taxEl = document.getElementById('vda_modal_tax');
    if (taxEl) taxEl.textContent = money(tax);
}

// ----- Add row in modal -----
function addVdaModalRow() {
    const tbody = document.getElementById('modal_vda_rows');
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr');
    const idx = rows.length;
    const tpl = document.getElementById('modal_tpl_vda');
    if (!tpl) return;
    const row = tpl.content.firstElementChild.cloneNode(true);
    const html = row.outerHTML
        .replace(/__INDEX__/g, idx)
        .replace(/__INDEX_PLUS_ONE__/g, idx + 1);
    tbody.insertAdjacentHTML('beforeend', html);
    renumberVdaModalRows();
    recalcVdaModal();
}

// ----- Delete row in modal -----
function deleteVdaModalRow(btn) {
    const row = btn.closest('tr');
    if (!row) return;
    if (!confirm('Remove this row?')) return;
    row.remove();
    renumberVdaModalRows();
    recalcVdaModal();
}

// ----- Save modal and update main sheet -----
function saveVdaModal() {
    console.log('💾 Saving VDA Modal...');
    
    // 1. Collect rows from modal
    const rows = document.querySelectorAll('#modal_vda_rows tr');
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

    console.log('📊 VDA Entries saved:', entries.length, 'Total:', total);

    // 3. Update hidden fields
    const dataJsonEl = document.getElementById('vda_data_json');
    const totalValueEl = document.getElementById('vda_total_value');
    if (dataJsonEl) dataJsonEl.value = JSON.stringify(entries);
    if (totalValueEl) totalValueEl.value = total;

    // 4. Update main sheet summary row
    const displayEl = document.getElementById('vda_total');
    if (displayEl) displayEl.value = fmtInp(total);

    // 5. Close modal
    const modalEl = document.getElementById('vdaModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    // 6. Trigger GTI and final recalculation
    if (typeof updateGTI === 'function') updateGTI();
    if (typeof calcFinal === 'function') calcFinal();
    if (typeof autoSave === 'function') autoSave();

    console.log('✅ VDA modal saved successfully!');
}

// ----- Initialize VDA total on page load -----
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded – initializing VDA...');
    setTimeout(function() {
        initVdaTotal();
        calcVDA();
    }, 150);
});

console.log('✅ vda-script.js loaded successfully!');
</script>