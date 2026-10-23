<script>
// ============================================================
//  TAX PAID – Modal with all payment details
//  + INITIALIZATION ON PAGE LOAD
//  + Auto‑recalc Tax section after save
// ============================================================

// ----- Load modal data from hidden JSON -----
function loadTaxPaidData() {
    const jsonEl = document.getElementById('tax_paid_data_json');
    if (!jsonEl) return {};

    try {
        const data = JSON.parse(jsonEl.value);
        if (!data.tds_entries) data.tds_entries = [];
        if (!data.tcs_entries) data.tcs_entries = [];
        if (!data.advance_tax_entries) data.advance_tax_entries = [];
        if (!data.sat_entries) data.sat_entries = [];
        return data;
    } catch(e) {
        return {
            tds_entries: [],
            tcs_entries: [],
            advance_tax_entries: [],
            sat_entries: [],
            interest_234a: 0,
            interest_234b: 0,
            interest_234c: 0,
            fee_234f: 0
        };
    }
}

// ----- Render modal with data -----
function renderTaxPaidModal(data) {
    // Interest & Fees
    document.getElementById('modal_int_234a').value = data.interest_234a || 0;
    document.getElementById('modal_int_234b').value = data.interest_234b || 0;
    document.getElementById('modal_int_234c').value = data.interest_234c || 0;
    document.getElementById('modal_fee_234f').value = data.fee_234f || 0;

    // TDS
    renderModalTable('modal_tds_rows', data.tds_entries || [], 'tds_entries', 'modal_tpl_tds', ['tan_number','deductor_name','section_code','amount_paid','tds_deducted','tds_claimed']);

    // TCS
    renderModalTable('modal_tcs_rows', data.tcs_entries || [], 'tcs_entries', 'modal_tpl_tcs', ['tan_number','collector_name','section_code','amount_collected','tcs_collected','tcs_claimed']);

    // Advance Tax
    renderModalTable('modal_at_rows', data.advance_tax_entries || [], 'advance_tax_entries', 'modal_tpl_at', ['payment_date','bsr_code','challan_no','installment','amount_paid']);

    // SAT
    if (data.sat_entries && data.sat_entries.length > 0) {
        const sat = data.sat_entries[0];
        document.getElementById('modal_sat_date_0').value = sat.payment_date || '';
        document.getElementById('modal_sat_bsr_0').value = sat.bsr_code || '';
        document.getElementById('modal_sat_challan_0').value = sat.challan_no || '';
        document.getElementById('modal_sat_amount_0').value = sat.amount_paid || '0';
        document.getElementById('modal_sat_remarks_0').value = sat.remarks || '';
    }

    recalcTaxPaidModal();
}

// ----- Render table inside modal -----
function renderModalTable(tbodyId, data, namePrefix, templateId, fields) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    tbody.innerHTML = '';

    if (data.length === 0) {
        const tpl = document.getElementById(templateId);
        if (!tpl) return;
        const row = tpl.content.firstElementChild.cloneNode(true);
        const html = row.outerHTML
            .replace(/__INDEX__/g, '0')
            .replace(/__INDEX_PLUS_ONE__/g, '1');
        tbody.insertAdjacentHTML('beforeend', html);
    } else {
        data.forEach((item, idx) => {
            const tpl = document.getElementById(templateId);
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
    renumberModalRows(tbodyId);
}

// ----- Renumber modal rows -----
function renumberModalRows(tbodyId) {
    const tbody = document.getElementById(tbodyId);
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

// ----- Add/Delete modal rows -----
function addModalRow(group, tbodyId, templateId) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr');
    const idx = rows.length;
    const tpl = document.getElementById(templateId);
    if (!tpl) return;
    const row = tpl.content.firstElementChild.cloneNode(true);
    const html = row.outerHTML
        .replace(/__INDEX__/g, idx)
        .replace(/__INDEX_PLUS_ONE__/g, idx + 1);
    tbody.insertAdjacentHTML('beforeend', html);
    renumberModalRows(tbodyId);
    recalcTaxPaidModal();
}

function deleteModalRow(btn, tbodyId) {
    const row = btn.closest('tr');
    if (!row) return;
    if (!confirm('Remove this row?')) return;
    row.remove();
    renumberModalRows(tbodyId);
    recalcTaxPaidModal();
}

// ----- Add specific rows -----
function addTdsModalRow() { addModalRow('modal_tds', 'modal_tds_rows', 'modal_tpl_tds'); }
function addTcsModalRow() { addModalRow('modal_tcs', 'modal_tcs_rows', 'modal_tpl_tcs'); }
function addAtModalRow() { addModalRow('modal_at', 'modal_at_rows', 'modal_tpl_at'); }

// ----- Recalculate modal totals AND update main sheet -----
function recalcTaxPaidModal() {
    // Interest & Fees
    const int234a = parseMoney(document.getElementById('modal_int_234a').value);
    const int234b = parseMoney(document.getElementById('modal_int_234b').value);
    const int234c = parseMoney(document.getElementById('modal_int_234c').value);
    const fee234f = parseMoney(document.getElementById('modal_fee_234f').value);
    const totalInt = int234a + int234b + int234c + fee234f;

    const intTotalEl = document.getElementById('modal_int_total');
    if (intTotalEl) intTotalEl.value = money(totalInt);

    // TDS
    let tdsTotalAmt = 0, tdsTotalDed = 0, tdsTotalCl = 0;
    document.querySelectorAll('#modal_tds_rows input[name*="[amount_paid]"]').forEach(inp => {
        tdsTotalAmt += parseMoney(inp.value);
    });
    document.querySelectorAll('#modal_tds_rows input[name*="[tds_deducted]"]').forEach(inp => {
        tdsTotalDed += parseMoney(inp.value);
    });
    document.querySelectorAll('#modal_tds_rows input[name*="[tds_claimed]"]').forEach(inp => {
        tdsTotalCl += parseMoney(inp.value);
    });

    const modalTdsAmt = document.getElementById('modal_tds_total_amt');
    if (modalTdsAmt) modalTdsAmt.value = money(tdsTotalAmt);
    const modalTdsDed = document.getElementById('modal_tds_total_ded');
    if (modalTdsDed) modalTdsDed.value = money(tdsTotalDed);
    const modalTdsCl = document.getElementById('modal_tds_total_cl');
    if (modalTdsCl) modalTdsCl.value = money(tdsTotalCl);

    // TCS
    let tcsTotalAmt = 0, tcsTotalCol = 0, tcsTotalCl = 0;
    document.querySelectorAll('#modal_tcs_rows input[name*="[amount_collected]"]').forEach(inp => {
        tcsTotalAmt += parseMoney(inp.value);
    });
    document.querySelectorAll('#modal_tcs_rows input[name*="[tcs_collected]"]').forEach(inp => {
        tcsTotalCol += parseMoney(inp.value);
    });
    document.querySelectorAll('#modal_tcs_rows input[name*="[tcs_claimed]"]').forEach(inp => {
        tcsTotalCl += parseMoney(inp.value);
    });

    const modalTcsAmt = document.getElementById('modal_tcs_total_amt');
    if (modalTcsAmt) modalTcsAmt.value = money(tcsTotalAmt);
    const modalTcsCol = document.getElementById('modal_tcs_total_col');
    if (modalTcsCol) modalTcsCol.value = money(tcsTotalCol);
    const modalTcsCl = document.getElementById('modal_tcs_total_cl');
    if (modalTcsCl) modalTcsCl.value = money(tcsTotalCl);

    // Advance Tax
    let atTotal = 0;
    document.querySelectorAll('#modal_at_rows input[name*="[amount_paid]"]').forEach(inp => {
        atTotal += parseMoney(inp.value);
    });
    const modalAtTotal = document.getElementById('modal_at_total');
    if (modalAtTotal) modalAtTotal.value = money(atTotal);

    // SAT
    let satTotal = 0;
    document.querySelectorAll('#modal_sat_rows input[id*="amount"]').forEach(inp => {
        satTotal += parseMoney(inp.value);
    });
    const modalSatTotal = document.getElementById('modal_sat_total');
    if (modalSatTotal) modalSatTotal.value = money(satTotal);

    // Grand Total
    const grandTotal = totalInt + tdsTotalCl + tcsTotalCl + atTotal + satTotal;
    const modalGrand = document.getElementById('modal_total_tax_paid');
    if (modalGrand) modalGrand.textContent = money(grandTotal);

    // 🔥 UPDATE MAIN SHEET SUMMARY FIELDS
    const tdsEl = document.getElementById('tds_total_cl');
    if (tdsEl) tdsEl.value = money(tdsTotalCl);

    const tcsEl = document.getElementById('tcs_total_cl');
    if (tcsEl) tcsEl.value = money(tcsTotalCl);

    const atEl = document.getElementById('at_total');
    if (atEl) atEl.value = money(atTotal);

    const satEl = document.getElementById('sat_total');
    if (satEl) satEl.value = money(satTotal);

    const displayEl = document.getElementById('tax_paid_total_display');
    if (displayEl) displayEl.value = money(grandTotal);

    const totalValueEl = document.getElementById('tax_paid_total_value');
    if (totalValueEl) totalValueEl.value = grandTotal;

    // Trigger calcFinal to update balance
    if (typeof calcFinal === 'function') calcFinal();
}

// ----- Open Modal -----
function openTaxPaidModal() {
    const modalEl = document.getElementById('taxPaidModal');
    if (!modalEl) {
        alert('Tax Paid modal is missing from the page.');
        return;
    }
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        alert('Bootstrap JS is not loaded.');
        return;
    }

    const data = loadTaxPaidData();
    renderTaxPaidModal(data);

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// ----- Save Modal (UPDATED: triggers updateGTI) -----
function saveTaxPaidModal() {
    console.log('💾 saveTaxPaidModal() called...');

    // Collect all data from modal
    const data = {
        interest_234a: parseMoney(document.getElementById('modal_int_234a').value),
        interest_234b: parseMoney(document.getElementById('modal_int_234b').value),
        interest_234c: parseMoney(document.getElementById('modal_int_234c').value),
        fee_234f: parseMoney(document.getElementById('modal_fee_234f').value),
    };

    console.log('📊 Interest values:', data);

    // TDS
    const tdsRows = document.querySelectorAll('#modal_tds_rows tr');
    data.tds_entries = [];
    tdsRows.forEach((tr) => {
        const obj = {
            tan_number: tr.querySelector('[name*="[tan_number]"]')?.value || '',
            deductor_name: tr.querySelector('[name*="[deductor_name]"]')?.value || '',
            section_code: tr.querySelector('[name*="[section_code]"]')?.value || '',
            amount_paid: tr.querySelector('[name*="[amount_paid]"]')?.value || '0',
            tds_deducted: tr.querySelector('[name*="[tds_deducted]"]')?.value || '0',
            tds_claimed: tr.querySelector('[name*="[tds_claimed]"]')?.value || '0',
        };
        if (obj.tan_number || obj.deductor_name || parseMoney(obj.amount_paid) > 0 || parseMoney(obj.tds_deducted) > 0) {
            data.tds_entries.push(obj);
        }
    });

    // TCS
    const tcsRows = document.querySelectorAll('#modal_tcs_rows tr');
    data.tcs_entries = [];
    tcsRows.forEach((tr) => {
        const obj = {
            tan_number: tr.querySelector('[name*="[tan_number]"]')?.value || '',
            collector_name: tr.querySelector('[name*="[collector_name]"]')?.value || '',
            section_code: tr.querySelector('[name*="[section_code]"]')?.value || '',
            amount_collected: tr.querySelector('[name*="[amount_collected]"]')?.value || '0',
            tcs_collected: tr.querySelector('[name*="[tcs_collected]"]')?.value || '0',
            tcs_claimed: tr.querySelector('[name*="[tcs_claimed]"]')?.value || '0',
        };
        if (obj.tan_number || obj.collector_name || parseMoney(obj.amount_collected) > 0 || parseMoney(obj.tcs_collected) > 0) {
            data.tcs_entries.push(obj);
        }
    });

    // Advance Tax
    const atRows = document.querySelectorAll('#modal_at_rows tr');
    data.advance_tax_entries = [];
    atRows.forEach((tr) => {
        const obj = {
            payment_date: tr.querySelector('[name*="[payment_date]"]')?.value || '',
            bsr_code: tr.querySelector('[name*="[bsr_code]"]')?.value || '',
            challan_no: tr.querySelector('[name*="[challan_no]"]')?.value || '',
            installment: tr.querySelector('[name*="[installment]"]')?.value || '',
            amount_paid: tr.querySelector('[name*="[amount_paid]"]')?.value || '0',
            remarks: tr.querySelector('[name*="[remarks]"]')?.value || '',
        };
        if (obj.payment_date || obj.bsr_code || parseMoney(obj.amount_paid) > 0) {
            data.advance_tax_entries.push(obj);
        }
    });

    // SAT
    const satObj = {
        payment_date: document.getElementById('modal_sat_date_0')?.value || '',
        bsr_code: document.getElementById('modal_sat_bsr_0')?.value || '',
        challan_no: document.getElementById('modal_sat_challan_0')?.value || '',
        amount_paid: document.getElementById('modal_sat_amount_0')?.value || '0',
        remarks: document.getElementById('modal_sat_remarks_0')?.value || '',
    };
    data.sat_entries = [satObj];

    console.log('📊 Total entries:', data.tds_entries.length, 'TDS,', data.tcs_entries.length, 'TCS,', data.advance_tax_entries.length, 'AT');

    // Update hidden JSON
    const jsonEl = document.getElementById('tax_paid_data_json');
    if (jsonEl) {
        jsonEl.value = JSON.stringify(data);
        console.log('✅ tax_paid_data_json updated:', jsonEl.value);
    } else {
        console.error('❌ tax_paid_data_json element not found!');
    }

    // Update main sheet totals
    recalcTaxPaidModal();

    // Close modal
    const modalEl = document.getElementById('taxPaidModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    // ── 🔥 CRITICAL: Recalculate Tax Section ──
    if (typeof updateGTI === 'function') {
        console.log('🔄 Calling updateGTI() to refresh Tax Calculation...');
        updateGTI();
    } else {
        console.warn('⚠️ updateGTI() not found – Tax Calculation may not refresh.');
    }

    if (typeof autoSave === 'function') {
        autoSave();
    }

    console.log('✅ saveTaxPaidModal() completed.');
}

// ===== 🔥 INITIALIZE ON PAGE LOAD =====
function initTaxPaidSummary() {
    console.log('🔄 initTaxPaidSummary() called...');
    const data = loadTaxPaidData();

    console.log('📊 Loaded Tax Paid Data:', data);

    // Compute totals from JSON
    let tdsTotal = data.tds_entries.reduce((sum, item) => sum + parseMoney(item.tds_deducted || 0), 0);
    let tcsTotal = data.tcs_entries.reduce((sum, item) => sum + parseMoney(item.tcs_collected || 0), 0);
    let atTotal = data.advance_tax_entries.reduce((sum, item) => sum + parseMoney(item.amount_paid || 0), 0);
    let satTotal = data.sat_entries.reduce((sum, item) => sum + parseMoney(item.amount_paid || 0), 0);
    let intTotal = (data.interest_234a || 0) + (data.interest_234b || 0) + (data.interest_234c || 0) + (data.fee_234f || 0);
    let grandTotal = tdsTotal + tcsTotal + atTotal + satTotal + intTotal;

    console.log('📊 Tax Paid Totals: TDS=' + tdsTotal + ', TCS=' + tcsTotal + ', AT=' + atTotal + ', SAT=' + satTotal + ', Interest=' + intTotal + ', Grand=' + grandTotal);

    // Update summary fields
    const tdsEl = document.getElementById('tds_total_cl');
    if (tdsEl) tdsEl.value = money(tdsTotal);

    const tcsEl = document.getElementById('tcs_total_cl');
    if (tcsEl) tcsEl.value = money(tcsTotal);

    const atEl = document.getElementById('at_total');
    if (atEl) atEl.value = money(atTotal);

    const satEl = document.getElementById('sat_total');
    if (satEl) satEl.value = money(satTotal);

    const displayEl = document.getElementById('tax_paid_total_display');
    if (displayEl) displayEl.value = money(grandTotal);

    const totalValueEl = document.getElementById('tax_paid_total_value');
    if (totalValueEl) totalValueEl.value = grandTotal;

    // Trigger calcFinal to update balance
    if (typeof calcFinal === 'function') {
        calcFinal();
    }

    // ── Also trigger updateGTI to refresh Tax Calculation ──
    if (typeof updateGTI === 'function') {
        updateGTI();
    }
}

// ----- Initialize on page load -----
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded – initializing Tax Paid summary...');
    setTimeout(function() {
        initTaxPaidSummary();
    }, 300);
});

console.log('✅ tax-paid-script.js loaded successfully!');
</script>