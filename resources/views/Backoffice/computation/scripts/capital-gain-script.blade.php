<script>
// ============================================================
//  CAPITAL GAIN – MODAL + MAIN SHEET SUMMARY
//  WITH FULL INITIALIZATION ON PAGE LOAD
// ============================================================

let cgData = [];

// ----- Main sheet: update cg_total from hidden value -----
function calcCG() {
    const totalEl = document.getElementById('cg_total_value');
    const displayEl = document.getElementById('cg_total');
    if (totalEl && displayEl) {
        const val = parseFloat(totalEl.value) || 0;
        displayEl.value = fmtInp(val);
    }
}

// ----- Initialize CG total from JSON on page load -----
function initCgTotal() {
    const cgJsonEl = document.getElementById('cg_data_json');
    if (!cgJsonEl) {
        console.warn('⚠️ cg_data_json element not found!');
        return;
    }
    
    const cgJson = cgJsonEl.value || '[]';
    let entries = [];
    try {
        entries = JSON.parse(cgJson);
        if (!Array.isArray(entries)) entries = [];
    } catch(e) {
        entries = [];
    }

    // Extract values from entries
    let stcgSale = 0, stcgAcq = 0, stcgBrokerage = 0, stcgImprove = 0;
    let ltcgSale = 0, ltcgAcq = 0, ltcgBrokerage = 0, ltcgImprove = 0;
    let exempt54 = 0, exempt54ec = 0, exempt54f = 0;

    entries.forEach((item) => {
        const label = (item.particular || '').toLowerCase();
        const amt = parseMoney(item.amount);
        
        if (label.includes('stcg') && label.includes('sale')) stcgSale = amt;
        else if (label.includes('stcg') && label.includes('acquisition')) stcgAcq = amt;
        else if (label.includes('stcg') && label.includes('brokerage')) stcgBrokerage = amt;
        else if (label.includes('stcg') && label.includes('improvement')) stcgImprove = amt;
        else if (label.includes('ltcg') && label.includes('sale')) ltcgSale = amt;
        else if (label.includes('ltcg') && label.includes('acquisition')) ltcgAcq = amt;
        else if (label.includes('ltcg') && label.includes('brokerage')) ltcgBrokerage = amt;
        else if (label.includes('ltcg') && label.includes('improvement')) ltcgImprove = amt;
        else if (label.includes('54') && !label.includes('54ec') && !label.includes('54f')) exempt54 = amt;
        else if (label.includes('54ec')) exempt54ec = amt;
        else if (label.includes('54f')) exempt54f = amt;
    });

    // Calculate totals
    const stcg = Math.max(0, stcgSale - stcgAcq - stcgBrokerage - stcgImprove);
    const ltcg = Math.max(0, ltcgSale - ltcgAcq - ltcgBrokerage - ltcgImprove);
    const taxableLtcg = Math.max(0, ltcg - exempt54 - exempt54ec - exempt54f);
    const totalCg = stcg + taxableLtcg;

    // Update hidden total field
    const totalValueEl = document.getElementById('cg_total_value');
    if (totalValueEl) totalValueEl.value = totalCg;

    // Update display field
    const displayEl = document.getElementById('cg_total');
    if (displayEl) displayEl.value = fmtInp(totalCg);

    // Update GTI if needed
    if (typeof updateGTI === 'function') updateGTI();
}

// ----- Open Modal -----
function openCgModal() {
    const modalEl = document.getElementById('cgModal');
    if (!modalEl) {
        alert('Capital Gain modal is missing from the page.');
        return;
    }
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        alert('Bootstrap JS is not loaded.');
        return;
    }

    const cgJson = document.getElementById('cg_data_json')?.value || '[]';
    try {
        cgData = JSON.parse(cgJson);
        if (!Array.isArray(cgData)) cgData = [];
    } catch(e) {
        cgData = [];
    }

    populateCgModal(cgData);
    setTimeout(function() {
        recalcCgModal();
    }, 100);

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// ----- Populate modal fields from JSON data -----
function populateCgModal(data) {
    let stcgSale = 0, stcgAcq = 0, stcgBrokerage = 0, stcgImprove = 0;
    let ltcgSale = 0, ltcgAcq = 0, ltcgBrokerage = 0, ltcgImprove = 0;
    let exempt54 = 0, exempt54ec = 0, exempt54f = 0;

    data.forEach(item => {
        const label = (item.particular || '').toLowerCase();
        const amt = parseMoney(item.amount);
        if (label.includes('stcg') && label.includes('sale')) stcgSale = amt;
        else if (label.includes('stcg') && label.includes('acquisition')) stcgAcq = amt;
        else if (label.includes('stcg') && label.includes('brokerage')) stcgBrokerage = amt;
        else if (label.includes('stcg') && label.includes('improvement')) stcgImprove = amt;
        else if (label.includes('ltcg') && label.includes('sale')) ltcgSale = amt;
        else if (label.includes('ltcg') && label.includes('acquisition')) ltcgAcq = amt;
        else if (label.includes('ltcg') && label.includes('brokerage')) ltcgBrokerage = amt;
        else if (label.includes('ltcg') && label.includes('improvement')) ltcgImprove = amt;
        else if (label.includes('54') && !label.includes('54ec') && !label.includes('54f')) exempt54 = amt;
        else if (label.includes('54ec')) exempt54ec = amt;
        else if (label.includes('54f')) exempt54f = amt;
    });

    const fields = {
        'cg_stcg_sale': stcgSale,
        'cg_stcg_acq': stcgAcq,
        'cg_stcg_brokerage': stcgBrokerage,
        'cg_stcg_improve': stcgImprove,
        'cg_ltcg_sale': ltcgSale,
        'cg_ltcg_acq': ltcgAcq,
        'cg_ltcg_brokerage': ltcgBrokerage,
        'cg_ltcg_improve': ltcgImprove,
        'cg_exempt_54': exempt54,
        'cg_exempt_54ec': exempt54ec,
        'cg_exempt_54f': exempt54f
    };

    Object.keys(fields).forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = money(fields[id]);
    });
}

// ----- Recalculate modal totals -----
function recalcCgModal() {
    const stcgSale = parseMoney(document.getElementById('cg_stcg_sale')?.value || 0);
    const stcgAcq = parseMoney(document.getElementById('cg_stcg_acq')?.value || 0);
    const stcgBrokerage = parseMoney(document.getElementById('cg_stcg_brokerage')?.value || 0);
    const stcgImprove = parseMoney(document.getElementById('cg_stcg_improve')?.value || 0);
    const stcg = Math.max(0, stcgSale - stcgAcq - stcgBrokerage - stcgImprove);
    document.getElementById('cg_stcg_result').textContent = money(stcg);

    const ltcgSale = parseMoney(document.getElementById('cg_ltcg_sale')?.value || 0);
    const ltcgAcq = parseMoney(document.getElementById('cg_ltcg_acq')?.value || 0);
    const ltcgBrokerage = parseMoney(document.getElementById('cg_ltcg_brokerage')?.value || 0);
    const ltcgImprove = parseMoney(document.getElementById('cg_ltcg_improve')?.value || 0);
    const ltcg = Math.max(0, ltcgSale - ltcgAcq - ltcgBrokerage - ltcgImprove);
    document.getElementById('cg_ltcg_result').textContent = money(ltcg);

    const exempt54 = parseMoney(document.getElementById('cg_exempt_54')?.value || 0);
    const exempt54ec = parseMoney(document.getElementById('cg_exempt_54ec')?.value || 0);
    const exempt54f = parseMoney(document.getElementById('cg_exempt_54f')?.value || 0);
    const taxableLtcg = Math.max(0, ltcg - exempt54 - exempt54ec - exempt54f);
    document.getElementById('cg_ltcg_taxable').textContent = money(taxableLtcg);

    const totalCg = stcg + taxableLtcg;
    document.getElementById('cg_final_total').textContent = money(totalCg);
}

// ----- Save modal and update main sheet -----
function saveCgModal() {
    const stcgSale = parseMoney(document.getElementById('cg_stcg_sale')?.value || 0);
    const stcgAcq = parseMoney(document.getElementById('cg_stcg_acq')?.value || 0);
    const stcgBrokerage = parseMoney(document.getElementById('cg_stcg_brokerage')?.value || 0);
    const stcgImprove = parseMoney(document.getElementById('cg_stcg_improve')?.value || 0);
    const ltcgSale = parseMoney(document.getElementById('cg_ltcg_sale')?.value || 0);
    const ltcgAcq = parseMoney(document.getElementById('cg_ltcg_acq')?.value || 0);
    const ltcgBrokerage = parseMoney(document.getElementById('cg_ltcg_brokerage')?.value || 0);
    const ltcgImprove = parseMoney(document.getElementById('cg_ltcg_improve')?.value || 0);
    const exempt54 = parseMoney(document.getElementById('cg_exempt_54')?.value || 0);
    const exempt54ec = parseMoney(document.getElementById('cg_exempt_54ec')?.value || 0);
    const exempt54f = parseMoney(document.getElementById('cg_exempt_54f')?.value || 0);

    const stcg = Math.max(0, stcgSale - stcgAcq - stcgBrokerage - stcgImprove);
    const ltcg = Math.max(0, ltcgSale - ltcgAcq - ltcgBrokerage - ltcgImprove);
    const taxableLtcg = Math.max(0, ltcg - exempt54 - exempt54ec - exempt54f);
    const totalCg = stcg + taxableLtcg;

    // Build entries array
    const entries = [];
    if (stcgSale > 0 || stcgAcq > 0 || stcgBrokerage > 0 || stcgImprove > 0) {
        entries.push({ particular: 'STCG - Sale', section: 'STCG', amount: String(stcgSale), remarks: '' });
        if (stcgAcq > 0) entries.push({ particular: 'STCG - Cost of Acquisition', section: 'STCG', amount: String(stcgAcq), remarks: '' });
        if (stcgBrokerage > 0) entries.push({ particular: 'STCG - Brokerage Charges', section: 'STCG', amount: String(stcgBrokerage), remarks: '' });
        if (stcgImprove > 0) entries.push({ particular: 'STCG - Cost of Improvement', section: 'STCG', amount: String(stcgImprove), remarks: '' });
    }

    if (ltcgSale > 0 || ltcgAcq > 0 || ltcgBrokerage > 0 || ltcgImprove > 0) {
        entries.push({ particular: 'LTCG - Sale', section: 'LTCG', amount: String(ltcgSale), remarks: '' });
        if (ltcgAcq > 0) entries.push({ particular: 'LTCG - Indexed Cost of Acquisition', section: 'LTCG', amount: String(ltcgAcq), remarks: '' });
        if (ltcgBrokerage > 0) entries.push({ particular: 'LTCG - Brokerage Charges', section: 'LTCG', amount: String(ltcgBrokerage), remarks: '' });
        if (ltcgImprove > 0) entries.push({ particular: 'LTCG - Indexed Cost of Improvement', section: 'LTCG', amount: String(ltcgImprove), remarks: '' });
    }

    if (exempt54 > 0) entries.push({ particular: 'Exemption u/s 54', section: 'EXEMPTION', amount: String(exempt54), remarks: '' });
    if (exempt54ec > 0) entries.push({ particular: 'Exemption u/s 54EC', section: 'EXEMPTION', amount: String(exempt54ec), remarks: '' });
    if (exempt54f > 0) entries.push({ particular: 'Exemption u/s 54F', section: 'EXEMPTION', amount: String(exempt54f), remarks: '' });

    // Update hidden fields
    document.getElementById('cg_data_json').value = JSON.stringify(entries);
    document.getElementById('cg_total_value').value = totalCg;
    document.getElementById('cg_total').value = fmtInp(totalCg);

    // Close modal
    const modalEl = document.getElementById('cgModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    // Trigger recalculations
    if (typeof updateGTI === 'function') updateGTI();
    if (typeof calcFinal === 'function') calcFinal();
    if (typeof autoSave === 'function') autoSave();
}

// ----- Initialize on page load -----
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        initCgTotal();
        calcCG();
    }, 300);
});

console.log('✅ capital-gain-script.js loaded successfully!');
</script>