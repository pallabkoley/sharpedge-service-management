<script>
// ============================================================
//  HOUSE PROPERTY – MODAL + MAIN SHEET SUMMARY
//  WITH FULL INITIALIZATION ON PAGE LOAD
// ============================================================

let hpData = [];

// ----- Main sheet: update hp_total_display from hidden value -----
function calcHP() {
    const totalEl = document.getElementById('hp_total_value');
    const displayEl = document.getElementById('hp_total_display');
    if (totalEl && displayEl) {
        const val = parseFloat(totalEl.value) || 0;
        displayEl.value = fmtInp(val);
    }
}

// ----- NEW: Initialize HP total from JSON on page load -----
function initHpTotal() {
    console.log('🔄 initHpTotal() called...');
    const hpJson = document.getElementById('hp_data_json')?.value || '[]';
    let entries = [];
    try {
        entries = JSON.parse(hpJson);
        if (!Array.isArray(entries)) entries = [];
    } catch(e) {
        entries = [];
    }

    let totalRent = 0, totalMuni = 0, totalInterest = 0;
    entries.forEach(item => {
        const amt = parseMoney(item.amount);
        const label = (item.particular || '').toLowerCase();
        if (label.includes('municipal') || label.includes('tax')) {
            totalMuni += amt;
        } else if (label.includes('interest') || label.includes('loan')) {
            totalInterest += amt;
        } else {
            totalRent += amt;
        }
    });
    const nav = totalRent - totalMuni;
    const stdDed = Math.round(nav * 0.3);
    const finalHp = nav - stdDed - totalInterest;

    console.log('💰 HP Total:', finalHp, '| Rent:', totalRent, '| Muni:', totalMuni, '| Interest:', totalInterest);

    // Update hidden total field
    const totalValueEl = document.getElementById('hp_total_value');
    if (totalValueEl) totalValueEl.value = finalHp;

    // Update display field (CORRECTED ID)
    const displayEl = document.getElementById('hp_total_display');
    if (displayEl) displayEl.value = fmtInp(finalHp);

    // Update GTI if needed
    if (typeof updateGTI === 'function') updateGTI();
}

// ----- Open Modal -----
function openHpModal() {
    console.log('📂 Opening House Property Modal...');
    const modalEl = document.getElementById('hpModal');
    if (!modalEl) {
        alert('House Property modal is missing from the page.');
        return;
    }
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        alert('Bootstrap JS is not loaded.');
        return;
    }

    const hpJson = document.getElementById('hp_data_json')?.value || '[]';
    try {
        hpData = JSON.parse(hpJson);
        if (!Array.isArray(hpData)) hpData = [];
    } catch(e) {
        hpData = [];
    }

    renderHpModalTable(hpData);
    recalcHpModal();

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// ----- Render table inside modal -----
function renderHpModalTable(data) {
    const tbody = document.getElementById('modal_hp_rows');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (data.length === 0) {
        const tpl = document.getElementById('modal_tpl_hp');
        if (!tpl) return;
        const row = tpl.content.firstElementChild.cloneNode(true);
        const html = row.outerHTML
            .replace(/__INDEX__/g, '0')
            .replace(/__INDEX_PLUS_ONE__/g, '1');
        tbody.insertAdjacentHTML('beforeend', html);
    } else {
        data.forEach((item, idx) => {
            const tpl = document.getElementById('modal_tpl_hp');
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
    renumberHpModalRows();
}

function renumberHpModalRows() {
    const tbody = document.getElementById('modal_hp_rows');
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

function recalcHpModal() {
    let totalRent = 0, totalMuni = 0, totalInterest = 0;
    document.querySelectorAll('#modal_hp_rows tr').forEach(tr => {
        const particular = tr.querySelector('[name*="[particular]"]')?.value || '';
        const amount = parseMoney(tr.querySelector('[name*="[amount]"]')?.value || 0);
        const lower = particular.toLowerCase();
        if (lower.includes('municipal') || lower.includes('tax')) totalMuni += amount;
        else if (lower.includes('interest') || lower.includes('loan')) totalInterest += amount;
        else totalRent += amount;
    });

    document.getElementById('hp_modal_total_rent').value = money(totalRent);
    document.getElementById('hp_modal_muni_tax').value = money(totalMuni);
    const nav = totalRent - totalMuni;
    document.getElementById('hp_modal_nav').value = money(nav);
    const stdDed = Math.round(nav * 0.3);
    document.getElementById('hp_modal_std').value = money(stdDed);
    document.getElementById('hp_modal_interest').value = money(totalInterest);
    const finalHp = nav - stdDed - totalInterest;
    document.getElementById('hp_modal_final').value = fmtInp(finalHp);
}

function addHpModalRow() {
    const tbody = document.getElementById('modal_hp_rows');
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr');
    const idx = rows.length;
    const tpl = document.getElementById('modal_tpl_hp');
    if (!tpl) return;
    const row = tpl.content.firstElementChild.cloneNode(true);
    const html = row.outerHTML
        .replace(/__INDEX__/g, idx)
        .replace(/__INDEX_PLUS_ONE__/g, idx + 1);
    tbody.insertAdjacentHTML('beforeend', html);
    renumberHpModalRows();
    recalcHpModal();
}

function deleteHpModalRow(btn) {
    const row = btn.closest('tr');
    if (!row) return;
    if (!confirm('Remove this row?')) return;
    row.remove();
    renumberHpModalRows();
    recalcHpModal();
}

// ----- Save modal and update main sheet -----
function saveHpModal() {
    console.log('💾 Saving House Property Modal...');
    const rows = document.querySelectorAll('#modal_hp_rows tr');
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

    // Compute final HP income
    let totalRent = 0, totalMuni = 0, totalInterest = 0;
    entries.forEach(item => {
        const amt = parseMoney(item.amount);
        const label = item.particular.toLowerCase();
        if (label.includes('municipal') || label.includes('tax')) totalMuni += amt;
        else if (label.includes('interest') || label.includes('loan')) totalInterest += amt;
        else totalRent += amt;
    });
    const nav = totalRent - totalMuni;
    const stdDed = Math.round(nav * 0.3);
    const finalHp = nav - stdDed - totalInterest;

    // Update hidden fields
    document.getElementById('hp_data_json').value = JSON.stringify(entries);
    document.getElementById('hp_total_value').value = finalHp;

    // CRITICAL: Update hp_total_display (CORRECTED ID)
    const displayEl = document.getElementById('hp_total_display');
    if (displayEl) displayEl.value = fmtInp(finalHp);

    // Close modal
    const modalEl = document.getElementById('hpModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    // Trigger GTI and final recalculation
    if (typeof updateGTI === 'function') updateGTI();
    if (typeof calcFinal === 'function') calcFinal();
    if (typeof autoSave === 'function') autoSave();

    console.log('✅ House Property modal saved.');
}

// ----- Initialize HP total on page load -----
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded – initializing HP...');
    setTimeout(function() {
        initHpTotal();
        calcHP();
    }, 150);
});

console.log('✅ house-property-script.js loaded successfully!');
</script>