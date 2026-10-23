<script>
// ============================================================
//  GTI, TAX, FINAL SUMMARY CALCULATIONS — COMPLETE FIX
//  Supports both Old Regime and New Regime
//  Populates ALL fields in Section 10
//  Binds Interest & Fees from tax_paid_data_json
//  Prevents NaN with safe number handling
// ============================================================

// ── Helpers ──
function getVal(id) {
    const el = document.getElementById(id);
    return el ? el.value : '0';
}

function safeVal(id) {
    const el = document.getElementById(id);
    return el ? parseMoney(el.value) : 0;
}

// ── 🔥 Get Interest & Fees total from Tax Paid JSON (with safety) ──
function getInterestTotal() {
    const jsonEl = document.getElementById('tax_paid_data_json');
    if (!jsonEl) {
        console.warn('⚠️ tax_paid_data_json not found');
        return 0;
    }
    try {
        const rawValue = jsonEl.value;
        if (!rawValue || rawValue === '{}' || rawValue === '[]') {
            console.warn('⚠️ tax_paid_data_json is empty');
            return 0;
        }
        const data = JSON.parse(rawValue);
        // Sum the individual interest & fee fields
        const interest = (data.interest_234a || 0) + 
                         (data.interest_234b || 0) +
                         (data.interest_234c || 0) + 
                         (data.fee_234f || 0);
        const result = isNaN(interest) ? 0 : interest;
        console.log('📊 getInterestTotal() =', result);
        return result;
    } catch (e) {
        console.warn('⚠️ Failed to parse tax_paid_data_json:', e);
        return 0;
    }
}

// ── Get STCG and LTCG income from Capital Gain JSON ──
function getCapitalGainBreakdown() {
    const jsonEl = document.getElementById('cg_data_json');
    if (!jsonEl) return { stcg: 0, ltcg: 0 };
    try {
        const entries = JSON.parse(jsonEl.value) || [];
        let stcg = 0, ltcg = 0;
        entries.forEach(item => {
            const label = (item.particular || '').toLowerCase();
            const amt = parseMoney(item.amount);
            if (label.includes('stcg')) stcg += amt;
            else if (label.includes('ltcg')) ltcg += amt;
        });
        return { stcg: stcg || 0, ltcg: ltcg || 0 };
    } catch (e) {
        return { stcg: 0, ltcg: 0 };
    }
}

// ── Slab rates ──
function getSlabRates(regime) {
    if (regime === 'old') {
        return [
            { limit: 250000, rate: 0 },
            { limit: 500000, rate: 0.05 },
            { limit: 1000000, rate: 0.20 },
            { limit: Infinity, rate: 0.30 }
        ];
    } else {
        // New Regime (simplified, adjust as per latest rules)
        return [
            { limit: 300000, rate: 0 },
            { limit: 600000, rate: 0.05 },
            { limit: 900000, rate: 0.10 },
            { limit: 1200000, rate: 0.15 },
            { limit: 1500000, rate: 0.20 },
            { limit: Infinity, rate: 0.30 }
        ];
    }
}

function calculateSlabTax(income, regime) {
    const slabs = getSlabRates(regime);
    let tax = 0;
    let prevLimit = 0;
    for (const slab of slabs) {
        if (income > prevLimit) {
            const taxable = Math.min(income, slab.limit) - prevLimit;
            tax += taxable * slab.rate;
        }
        prevLimit = slab.limit;
        if (income <= slab.limit) break;
    }
    return isNaN(tax) ? 0 : tax;
}

// ============================================================
//  UPDATE GTI
// ============================================================
function updateGTI() {
    console.log('🔄 updateGTI() called...');

    let sal = parseMoney(getVal('s_total'));
    let hp = parseMoney(getVal('hp_total_display'));
    let bp = parseMoney(getVal('bp_total'));
    let cg = parseMoney(getVal('cg_total'));
    let vda = parseMoney(getVal('vda_total'));
    let os = parseMoney(getVal('os_total'));
    let gti = sal + hp + bp + cg + vda + os;

    document.getElementById('gti_salary').value = fmtInp(sal);
    document.getElementById('gti_hp').value = fmtInp(hp);
    document.getElementById('gti_bp').value = fmtInp(bp);
    document.getElementById('gti_cg').value = fmtInp(cg);
    document.getElementById('gti_vda').value = fmtInp(vda);
    document.getElementById('gti_os').value = fmtInp(os);
    document.getElementById('gti_total').value = fmtInp(gti);

    let ded = parseMoney(getVal('ded_total'));
    let dedAllowed = Math.min(ded, Math.max(0, gti - cg - vda));
    document.getElementById('total_ded').value = money(dedAllowed);

    let tti_raw = gti - dedAllowed;
    document.getElementById('tti_raw').value = fmtInp(tti_raw);
    let tti = Math.round(tti_raw / 10) * 10;
    document.getElementById('tti').value = fmtInp(tti);

    calcTax(tti);
    document.getElementById('sb_gti').textContent = fmtInp(gti);
    document.getElementById('sb_tti').textContent = fmtInp(tti);
    calcFinal();

    console.log('✅ updateGTI() completed.');
}

// ============================================================
//  CALCULATE TAX
// ============================================================
function calcTax(tti) {
    console.log('🔄 calcTax() called with TTI:', tti);

    const regime = document.getElementById('tax_regime')?.value || 'old';

    // Get special income components
    const vdaIncome = parseMoney(getVal('vda_total'));
    const { stcg, ltcg } = getCapitalGainBreakdown();

    // Normal income = TTI - (STCG + LTCG + VDA)
    const normalIncome = Math.max(0, tti - stcg - ltcg - vdaIncome);

    // Slab tax on normal income
    const slabTax = calculateSlabTax(normalIncome, regime);

    // Special rate taxes
    const stcgTax = stcg * 0.20;
    const ltcgTax = Math.max(0, ltcg - 125000) * 0.125;
    const vdaTax = vdaIncome * 0.30;

    // Total tax before rebate
    const totalTax = slabTax + stcgTax + ltcgTax + vdaTax;

    // Rebate u/s 87A
    let rebate = 0;
    if (tti <= 500000) {
        rebate = Math.min(totalTax, 12500);
    }

    // Tax after rebate
    const afterRebate = Math.max(0, totalTax - rebate);

    // Cess @ 4%
    const cess = Math.round(afterRebate * 0.04);

    // Gross tax
    const grossTax = Math.round((afterRebate + cess) / 10) * 10;

    // ── Populate fields ──
    document.getElementById('tax_stcg').value = money(stcgTax);
    document.getElementById('tax_ltcg').value = money(ltcgTax);
    document.getElementById('tax_vda').value = money(vdaTax);
    document.getElementById('tax_before_rebate').value = money(totalTax);
    document.getElementById('tax_87a').value = money(rebate);
    document.getElementById('tax_after_rebate').value = money(afterRebate);
    document.getElementById('tax_cess').value = money(cess);
    document.getElementById('tax_gross').value = money(grossTax);

    // 🔥 Get Interest & Fees total from Tax Paid JSON
    const interestTotal = getInterestTotal();
    document.getElementById('int_total').value = money(interestTotal);

    // Total Tax, Interest and Fees Payable = gross + interest
    const totalPayable = grossTax + interestTotal;
    document.getElementById('tax_total_payable').value = money(totalPayable);

    // Rounding off (will be updated in calcFinal)
    document.getElementById('rounding_off_tax').value = money(0);

    // Store for calcFinal
    window._taxGross = grossTax;
    window._totalPayable = totalPayable;

    // Net tax placeholder (will be refined in calcFinal)
    document.getElementById('tax_net').value = money(totalPayable);

    console.log('✅ calcTax() completed: Gross Tax=', grossTax, 'Interest=', interestTotal, 'Total Payable=', totalPayable);
}

// ============================================================
//  CALCULATE FINAL – Tax Paid & Net Tax
// ============================================================
function calcFinal() {
    console.log('🔄 calcFinal() called...');

    // Read computed values from calcTax
    let totalPayable = window._totalPayable;
    if (isNaN(totalPayable) || totalPayable === undefined) {
        totalPayable = parseMoney(getVal('tax_total_payable'));
        if (isNaN(totalPayable)) totalPayable = 0;
    }

    // Read tax paid components
    const tds = safeVal('tds_total_cl');
    const tcs = safeVal('tcs_total_cl');
    const at = safeVal('at_total');
    const sat = safeVal('sat_total');
    const paid = (tds || 0) + (tcs || 0) + (at || 0) + (sat || 0);

    // Net tax = total payable - paid
    let netTax = totalPayable - paid;

    // Round to nearest 10
    const rounded = Math.round(netTax / 10) * 10;
    const roundingDiff = rounded - netTax;

    // Update rounding off field
    document.getElementById('rounding_off_tax').value = money(roundingDiff);

    // Final net tax
    document.getElementById('tax_net').value = money(rounded);

    // ── Update final summary section ──
    const fBalance = document.getElementById('f_balance');
    if (fBalance) fBalance.value = fmtInp(rounded);

    const sbPaid = document.getElementById('sb_paid');
    if (sbPaid) sbPaid.textContent = money(paid);

    const sbBalance = document.getElementById('sb_balance');
    if (sbBalance) sbBalance.textContent = money(Math.abs(rounded));

    const sbBalanceLabel = document.getElementById('sb_balance_label');
    if (sbBalanceLabel) {
        sbBalanceLabel.innerHTML = (rounded >= 0 ? 'Balance Payable: ₹' : 'Refund Due: ₹') +
            '<span id="sb_balance">' + money(Math.abs(rounded)) + '</span>';
    }

    // Update final fields
    const fGti = document.getElementById('f_gti');
    if (fGti) fGti.value = document.getElementById('gti_total')?.value || '0';
    const fDed = document.getElementById('f_ded');
    if (fDed) fDed.value = document.getElementById('ded_total')?.value || '0';
    const fTti = document.getElementById('f_tti');
    if (fTti) fTti.value = document.getElementById('tti')?.value || '0';
    const fTax = document.getElementById('f_tax');
    if (fTax) fTax.value = document.getElementById('tax_gross')?.value || '0';
    const fTds = document.getElementById('f_tds');
    if (fTds) fTds.value = document.getElementById('tds_total_cl')?.value || '0';
    const fTcs = document.getElementById('f_tcs');
    if (fTcs) fTcs.value = document.getElementById('tcs_total_cl')?.value || '0';
    const fAt = document.getElementById('f_at');
    if (fAt) fAt.value = document.getElementById('at_total')?.value || '0';
    const fSat = document.getElementById('f_sat');
    if (fSat) fSat.value = document.getElementById('sat_total')?.value || '0';

    const finalLabel = document.getElementById('final_label');
    if (finalLabel) finalLabel.textContent = rounded >= 0 ? 'BALANCE TAX PAYABLE' : 'REFUND DUE';

    const fBalanceEl = document.getElementById('f_balance');
    if (fBalanceEl) fBalanceEl.style.color = rounded >= 0 ? '#ffdd44' : '#aaffaa';

    const finalNote = document.getElementById('final_note');
    if (finalNote) {
        finalNote.textContent = rounded >= 0
            ? 'Note: Balance tax of ₹' + money(Math.abs(rounded)) + '/- payable via Challan 280 as Self Assessment Tax (SAT) before filing return.'
            : 'Note: Refund of ₹' + money(Math.abs(rounded)) + '/- due. Claim in ITR. Will be credited to assessee\'s bank account after processing.';
    }

    console.log('✅ calcFinal() completed: Tax Paid=', paid, 'Net Tax=', rounded);
}

console.log('✅ tax-computation-script.js loaded successfully!');
</script>