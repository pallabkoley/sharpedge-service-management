<script>
    /* ================================================================
       ASSETS RECONCILIATION – DATABASE INTEGRATED WITH DEPRECIATION
       VERSION: 2.0 (FIXED CALCULATIONS)
       ================================================================ */

    // ─── CONFIG ──────────────────────────────────────────────────────
    const COMPUTATION_ID = {{ $computation->id ?? 0 }};
    const DATA_URL = "{{ route('backoffice.computations.assets-reconciliation.data', ['computation' => $computation->id ?? 0]) }}";
    const SAVE_URL = "{{ route('backoffice.computations.assets-reconciliation.save', ['computation' => $computation->id ?? 0]) }}";
    const DELETE_ASSET_URL = "{{ route('backoffice.computations.assets-reconciliation.delete-asset', ['computation' => $computation->id ?? 0, 'assetId' => '__ID__']) }}";
    const DELETE_INVESTMENT_URL = "{{ route('backoffice.computations.assets-reconciliation.delete-investment', ['computation' => $computation->id ?? 0, 'investmentId' => '__ID__']) }}";
    const DELETE_SECURITY_URL = "{{ route('backoffice.computations.assets-reconciliation.delete-security', ['computation' => $computation->id ?? 0, 'securityId' => '__ID__']) }}";

    // ─── STATE ────────────────────────────────────────────────────────
    let assetRows = [];
    let investments = [];
    let securityDeposits = [];
    let editingAssetIdx = -1;
    let tempBreakups = [];
    let saveTimeout = null;
    let isSaving = false;

    // ─── DOM REFS ──────────────────────────────────────────────────────
    const assetsBody = document.getElementById('assetsBodyRows');
    const investmentsBody = document.getElementById('investmentsBodyRows');
    const securityBody = document.getElementById('securityBodyRows');
    const breakupBody = document.getElementById('breakupBody');

    // ─── HELPERS ──────────────────────────────────────────────────────

    function fmt(n) {
        if (n === undefined || n === null || isNaN(n)) return '₹0';
        return '₹' + Math.round(n).toLocaleString('en-IN');
    }

    function fmtRaw(n) {
        if (n === undefined || n === null || isNaN(n)) return 0;
        return Math.round(n);
    }

    function parseNum(v) {
        if (v === undefined || v === null) return 0;
        if (typeof v === 'number') return isNaN(v) ? 0 : v;
        if (typeof v === 'string') {
            v = v.replace(/[₹,\s]/g, '').trim();
            if (v === '' || v === '-') return 0;
            const num = parseFloat(v);
            return isNaN(num) ? 0 : num;
        }
        return 0;
    }

    function daysInFinancialYear(dateStr) {
        if (!dateStr) return 180;
        try {
            const parts = dateStr.split('-');
            if (parts.length !== 3) return 180;
            const d = new Date(parts[0], parts[1] - 1, parts[2]);
            if (isNaN(d.getTime())) return 180;
            const fyStart = new Date(d.getFullYear(), 3, 1);
            if (d < fyStart) {
                const prevFY = new Date(d.getFullYear() - 1, 3, 1);
                return Math.floor((d - prevFY) / (1000 * 60 * 60 * 24)) + 1;
            }
            return Math.floor((d - fyStart) / (1000 * 60 * 60 * 24)) + 1;
        } catch (e) {
            return 180;
        }
    }

    function isHalfYear(putToUseDate) {
        if (!putToUseDate) return true;
        const days = daysInFinancialYear(putToUseDate);
        return days >= 180;
    }

    function calculateDepreciation(amount, rate, halfYear) {
        const effectiveRate = halfYear ? rate / 2 : rate;
        return amount * (effectiveRate / 100);
    }

    // ─── SHOW TOAST (REUSABLE NOTIFICATION SYSTEM) ──────────────────

    function showToast(msg, type) {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.className = `toast ${type || 'info'}`;
        const icons = { success: '✅', warning: '⚠️', error: '❌', info: 'ℹ️' };
        toast.innerHTML = `
            <span>${icons[type] || 'ℹ️'}</span>
            <span>${msg}</span>
            <button class="close" onclick="this.parentElement.remove()">✕</button>
        `;
        container.appendChild(toast);
        setTimeout(() => { if (toast.parentElement) toast.remove(); }, 4000);
    }

    // ─── LOAD DATA FROM DATABASE ─────────────────────────────────────

    function loadDataFromDatabase() {
        if (!COMPUTATION_ID) {
            loadDefaultData();
            return;
        }

        fetch(DATA_URL, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                assetRows = data.assets || [];
                investments = data.investments || [];
                securityDeposits = data.security_deposits || [];

                assetRows.forEach(asset => {
                    if (!asset.breakdowns) asset.breakdowns = [];
                    asset.breakups = asset.breakdowns.map(b => ({
                        id: b.id,
                        amount: parseFloat(b.amount) || 0,
                        rate: parseFloat(b.depreciation_rate) || 0
                    }));
                    if (asset.breakups.length === 0) {
                        const depBase = (asset.opening_wdv || 0) + (asset.purchase_amount || 0) + (asset.revaluation || 0) - (asset.sale_amount || 0);
                        asset.breakups = [{ amount: depBase > 0 ? depBase : 0, rate: 0 }];
                    }
                });

                renderAssets();
                renderInvestments();
                renderSecurity();
                updateFloatPanel();
                showToast('Data loaded successfully.', 'success');
            } else {
                loadDefaultData();
            }
        })
        .catch(err => {
            console.error('Load error:', err);
            loadDefaultData();
        });
    }

    function loadDefaultData() {
        assetRows = [
            {
                asset_name: 'Office Building',
                block: 'Block 1',
                opening_wdv: 8500000,
                purchase_amount: 0,
                revaluation: 0,
                sale_amount: 0,
                purchase_date: '2025-04-01',
                sale_date: '',
                put_to_use_date: '2025-04-01',
                breakups: [{ amount: 8500000, rate: 10 }]
            },
            {
                asset_name: 'Production Machinery',
                block: 'Block 3',
                opening_wdv: 6200000,
                purchase_amount: 800000,
                revaluation: 0,
                sale_amount: 0,
                purchase_date: '2025-09-20',
                sale_date: '',
                put_to_use_date: '2025-09-20',
                breakups: [{ amount: 7000000, rate: 30 }]
            }
        ];
        investments = [
            { investment_name: 'Business Investment', amount: 5000000, interest: 250000, company: 'Sharma Industries', pan: 'AAACS1234F' }
        ];
        securityDeposits = [
            { deposit_name: 'Rent Security', amount: 500000, deposit: 0, withdrawal: 0 }
        ];

        renderAssets();
        renderInvestments();
        renderSecurity();
        updateFloatPanel();
        showToast('Default data loaded.', 'info');
    }

    // ─── CALCULATE AND UPDATE SINGLE ASSET ───────────────────────────

    function calcAndUpdateAsset(idx) {
        const row = assetRows[idx];
        if (!row) return;

        const opening = row.opening_wdv || 0;
        const addition = row.purchase_amount || 0;
        const revaluation = row.revaluation || 0;
        const sale = row.sale_amount || 0;
        const depBase = opening + addition + revaluation - sale;
        const halfYear = isHalfYear(row.purchase_date || row.put_to_use_date);
        const breakups = row.breakups || [];

        let totalDepreciation = 0;
        breakups.forEach(b => {
            totalDepreciation += calculateDepreciation(b.amount, b.rate, halfYear);
        });

        const closingWDV = depBase - totalDepreciation;

        // Update readonly fields
        const depBaseEl = document.getElementById(`depBase_${idx}`);
        const depAmtEl = document.getElementById(`depAmt_${idx}`);
        const closingWDVEl = document.getElementById(`closingWDV_${idx}`);

        if (depBaseEl) depBaseEl.value = fmtRaw(depBase);
        if (depAmtEl) depAmtEl.value = fmtRaw(totalDepreciation);
        if (closingWDVEl) closingWDVEl.value = fmtRaw(closingWDV);

        // Update breakup button label
        const hasBreakup = breakups.length > 1 || (breakups.length === 1 && breakups[0].rate > 0);
        const breakupLabel = hasBreakup ? (breakups.length + ' parts') : '—';
        const btn = document.querySelector(`#assetsBodyRows tr[data-asset="${idx}"] .btn-breakup`);
        if (btn) {
            btn.innerHTML = `<i class="bi bi-pencil"></i> ${breakupLabel}`;
            if (hasBreakup) {
                btn.classList.add('has-breakup');
            } else {
                btn.classList.remove('has-breakup');
            }
        }

        return { depBase, totalDepreciation, closingWDV };
    }

    // ─── RENDER ASSETS ───────────────────────────────────────────────

    function renderAssets() {
        let html = '';
        let totalOpening = 0,
            totalAddition = 0,
            totalRevaluation = 0,
            totalSale = 0,
            totalDepBase = 0,
            totalDep = 0,
            totalClosing = 0;

        assetRows.forEach((row, idx) => {
            const opening = row.opening_wdv || 0;
            const addition = row.purchase_amount || 0;
            const revaluation = row.revaluation || 0;
            const sale = row.sale_amount || 0;
            const depBase = opening + addition + revaluation - sale;
            const halfYear = isHalfYear(row.purchase_date || row.put_to_use_date);
            const breakups = row.breakups || [];

            let totalDepreciation = 0;
            breakups.forEach(b => {
                totalDepreciation += calculateDepreciation(b.amount, b.rate, halfYear);
            });

            const closingWDV = depBase - totalDepreciation;

            totalOpening += opening;
            totalAddition += addition;
            totalRevaluation += revaluation;
            totalSale += sale;
            totalDepBase += depBase;
            totalDep += totalDepreciation;
            totalClosing += closingWDV;

            const hasBreakup = breakups.length > 1 || (breakups.length === 1 && breakups[0].rate > 0);
            const breakupLabel = hasBreakup ? (breakups.length + ' parts') : '—';

            html += `
                <tr data-asset="${idx}" data-name="${(row.asset_name || '').toLowerCase()}">
                    <td style="text-align:center;font-weight:500;color:var(--text-mid);">${idx+1}</td>
                    <td class="sticky-col" style="background:inherit;position:sticky;left:0;z-index:2;text-align:left;padding-left:6px;"><span class="td-label">${row.asset_name || 'New Asset'}</span></td>
                    <td>
                        <input
                            class="td-amt"
                            type="text"
                            id="block_${idx}"
                            value="${row.block || ''}"
                            placeholder="Block"
                            oninput="onRowChange(${idx})"
                            style="font-size:10px;text-align:left;"
                        />
                    </td>
                    <td><input class="td-amt" type="text" id="opening_${idx}" value="${fmtRaw(opening)}" oninput="onRowChange(${idx})" /></td>
                    <td><input class="td-amt" type="text" id="addition_${idx}" value="${fmtRaw(addition)}" oninput="onRowChange(${idx})" /></td>
                    <td><input class="td-amt" type="text" id="revaluation_${idx}" value="${fmtRaw(revaluation)}" oninput="onRowChange(${idx})" style="color:var(--slate-light);" /></td>
                    <td><input class="td-amt" type="text" id="sale_${idx}" value="${fmtRaw(sale)}" oninput="onRowChange(${idx})" /></td>
                    <td><input class="td-amt" type="date" id="putToUse_${idx}" value="${row.purchase_date || row.put_to_use_date || ''}" oninput="onRowChange(${idx})" style="font-size:9px;padding:2px;text-align:center;" /></td>
                    <td><input class="td-amt" type="date" id="saleDate_${idx}" value="${row.sale_date || ''}" oninput="onRowChange(${idx})" style="font-size:9px;padding:2px;text-align:center;" /></td>
                    <td><input class="td-amt readonly" type="text" id="depBase_${idx}" value="${fmtRaw(depBase)}" readonly /></td>
                    <td><input class="td-amt readonly" type="text" id="depAmt_${idx}" value="${fmtRaw(totalDepreciation)}" readonly /></td>
                    <td><input class="td-amt readonly" type="text" id="closingWDV_${idx}" value="${fmtRaw(closingWDV)}" readonly /></td>
                    <td>
                        <button class="btn-breakup ${hasBreakup ? 'has-breakup' : ''}" onclick="openBreakupModal(${idx})">
                            <i class="bi bi-pencil"></i> ${breakupLabel}
                        </button>
                    </td>
                    <td><button class="btn-erp btn-ghost btn-sm" onclick="deleteAssetRow(${idx})" style="font-size:10px;padding:2px 6px;color:var(--red);"><i class="bi bi-trash"></i></button></td>
                </tr>
            `;
        });

        html += `
            <tr class="total-row">
                <td></td>
                <td class="sticky-col" style="background:#1a3a6c;position:sticky;left:0;z-index:2;text-align:left;padding-left:6px;"><span class="td-label" style="color:#fff;"><strong>TOTAL</strong></span></td>
                <td></td>
                <td><input class="td-amt total-cell" type="text" id="totOpening" value="${fmtRaw(totalOpening)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totAddition" value="${fmtRaw(totalAddition)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totRevaluation" value="${fmtRaw(totalRevaluation)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totSale" value="${fmtRaw(totalSale)}" readonly /></td>
                <td></td><td></td>
                <td><input class="td-amt total-cell" type="text" id="totDepBase" value="${fmtRaw(totalDepBase)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totDepAmt" value="${fmtRaw(totalDep)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totClosingWDV" value="${fmtRaw(totalClosing)}" readonly /></td>
                <td></td><td></td>
            </tr>
        `;

        if (assetsBody) assetsBody.innerHTML = html;
        const countEl = document.getElementById('assetCount');
        if (countEl) countEl.textContent = assetRows.length + ' assets';

        // Recalculate each row to ensure values are displayed
        assetRows.forEach((row, idx) => {
            calcAndUpdateAsset(idx);
        });

        updateAllTotals();
    }

    // ─── RENDER INVESTMENTS ───────────────────────────────────────────

    function renderInvestments() {
        let html = '';
        let totalBasic = 0,
            totalInterest = 0,
            totalValue = 0;
        investments.forEach((item, idx) => {
            const basic = parseFloat(item.amount) || 0;
            const interest = parseFloat(item.interest) || 0;
            const val = basic + interest;
            totalBasic += basic;
            totalInterest += interest;
            totalValue += val;
            html += `
                <tr data-invest="${idx}">
                    <td style="text-align:left;padding-left:8px;"><span class="td-label">${item.investment_name || 'New Investment'}</span></td>
                    <td><input class="td-amt" type="text" id="invBasic_${idx}" value="${fmtRaw(basic)}" oninput="onInvestChange(${idx})" /></td>
                    <td><input class="td-amt" type="text" id="invInterest_${idx}" value="${fmtRaw(interest)}" oninput="onInvestChange(${idx})" /></td>
                    <td><input class="td-amt readonly" type="text" id="invTotal_${idx}" value="${fmtRaw(val)}" readonly /></td>
                    <td><input class="td-amt" type="text" id="invCompany_${idx}" value="${item.company || ''}" oninput="onInvestChange(${idx})" style="text-align:left;" /></td>
                    <td><input class="td-amt" type="text" id="invPan_${idx}" value="${item.pan || ''}" oninput="onInvestChange(${idx})" style="text-align:left;text-transform:uppercase;font-size:10px;" /></td>
                    <td><button class="btn-erp btn-ghost btn-sm" onclick="deleteInvestmentRow(${idx})" style="font-size:10px;padding:2px 6px;"><i class="bi bi-trash"></i></button></td>
                </tr>
            `;
        });
        html += `
            <tr class="total-row">
                <td><span class="td-label" style="color:#fff;"><strong>TOTAL</strong></span></td>
                <td><input class="td-amt total-cell" type="text" id="totInvBasic" value="${fmtRaw(totalBasic)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totInvInterest" value="${fmtRaw(totalInterest)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totInvValue" value="${fmtRaw(totalValue)}" readonly /></td>
                <td></td><td></td><td></td>
            </tr>
        `;
        if (investmentsBody) investmentsBody.innerHTML = html;
        updateInvestTotals();
    }

    // ─── RENDER SECURITY DEPOSITS ─────────────────────────────────────

    function renderSecurity() {
        let html = '';
        let totalOpening = 0,
            totalDeposit = 0,
            totalWithdrawal = 0,
            totalNet = 0;
        securityDeposits.forEach((item, idx) => {
            const opening = parseFloat(item.amount) || 0;
            const deposit = parseFloat(item.deposit) || 0;
            const withdrawal = parseFloat(item.withdrawal) || 0;
            const net = opening + deposit - withdrawal;
            totalOpening += opening;
            totalDeposit += deposit;
            totalWithdrawal += withdrawal;
            totalNet += net;
            html += `
                <tr data-security="${idx}">
                    <td style="text-align:left;padding-left:8px;"><span class="td-label">${item.deposit_name || 'New Deposit'}</span></td>
                    <td><input class="td-amt" type="text" id="secOpening_${idx}" value="${fmtRaw(opening)}" oninput="onSecurityChange(${idx})" /></td>
                    <td><input class="td-amt" type="text" id="secDeposit_${idx}" value="${fmtRaw(deposit)}" oninput="onSecurityChange(${idx})" /></td>
                    <td><input class="td-amt" type="text" id="secWithdrawal_${idx}" value="${fmtRaw(withdrawal)}" oninput="onSecurityChange(${idx})" /></td>
                    <td><input class="td-amt readonly" type="text" id="secNet_${idx}" value="${fmtRaw(net)}" readonly /></td>
                    <td><button class="btn-erp btn-ghost btn-sm" onclick="deleteSecurityRow(${idx})" style="font-size:10px;padding:2px 6px;"><i class="bi bi-trash"></i></button></td>
                </tr>
            `;
        });
        html += `
            <tr class="total-row">
                <td><span class="td-label" style="color:#fff;"><strong>TOTAL</strong></span></td>
                <td><input class="td-amt total-cell" type="text" id="totSecOpening" value="${fmtRaw(totalOpening)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totSecDeposit" value="${fmtRaw(totalDeposit)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totSecWithdrawal" value="${fmtRaw(totalWithdrawal)}" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totSecNet" value="${fmtRaw(totalNet)}" readonly /></td>
                <td></td>
            </tr>
        `;
        if (securityBody) securityBody.innerHTML = html;
        updateSecurityTotals();
    }

    // ─── BREAKUP MODAL ───────────────────────────────────────────────

    function openBreakupModal(idx) {
        editingAssetIdx = idx;
        const row = assetRows[idx];
        if (!row) return;

        const depBase = (row.opening_wdv || 0) + (row.purchase_amount || 0) + (row.revaluation || 0) - (row.sale_amount || 0);

        const titleEl = document.getElementById('breakupModalTitle');
        const nameEl = document.getElementById('breakupAssetName');
        const depBaseEl = document.getElementById('breakupDepBase');
        const depBaseDisplayEl = document.getElementById('breakupDepBaseDisplay');

        if (titleEl) titleEl.textContent = 'Depreciation Breakup – ' + (row.asset_name || 'Asset');
        if (nameEl) nameEl.textContent = row.asset_name || 'Asset';
        if (depBaseEl) depBaseEl.textContent = fmt(depBase);
        if (depBaseDisplayEl) depBaseDisplayEl.textContent = fmt(depBase);

        tempBreakups = (row.breakups || []).map(b => ({ amount: b.amount || 0, rate: b.rate || 0 }));
        if (tempBreakups.length === 0) {
            tempBreakups.push({ amount: depBase > 0 ? depBase : 0, rate: 0 });
        }

        renderBreakupRows();
        validateBreakup();

        const modal = document.getElementById('breakupModal');
        if (modal) modal.classList.add('active');
    }

    function closeBreakupModal() {
        const modal = document.getElementById('breakupModal');
        if (modal) modal.classList.remove('active');
        editingAssetIdx = -1;
    }

    function renderBreakupRows() {
        let html = '';
        const row = assetRows[editingAssetIdx];
        const halfYear = isHalfYear(row?.purchase_date || row?.put_to_use_date);

        tempBreakups.forEach((b, idx) => {
            const dep = calculateDepreciation(b.amount, b.rate, halfYear);
            html += `
                <tr data-breakup="${idx}">
                    <td><input class="td-amt-sm" type="text" id="bAmount_${idx}" value="${fmtRaw(b.amount)}" oninput="onBreakupChange(${idx})" /></td>
                    <td>
                        <select class="td-rate-sm" id="bRate_${idx}" onchange="onBreakupChange(${idx})">
                            ${[0,5,10,15,20,25,30,40].map(r => `<option value="${r}" ${r === b.rate ? 'selected' : ''}>${r}%</option>`).join('')}
                        </select>
                    </td>
                    <td><input class="td-amt-sm readonly" type="text" id="bDep_${idx}" value="${fmtRaw(dep)}" readonly /></td>
                    <td>
                        <button class="btn-erp btn-ghost btn-sm" onclick="deleteBreakupRow(${idx})" style="font-size:10px;padding:2px 6px;color:var(--red);"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        if (breakupBody) breakupBody.innerHTML = html;
        validateBreakup();
    }

    function onBreakupChange(idx) {
        const amount = parseNum(document.getElementById(`bAmount_${idx}`).value);
        const rate = parseFloat(document.getElementById(`bRate_${idx}`).value) || 0;

        if (amount < 0) {
            showToast('Amount cannot be negative.', 'warning');
            document.getElementById(`bAmount_${idx}`).value = '0';
            return;
        }

        tempBreakups[idx].amount = amount;
        tempBreakups[idx].rate = rate;

        const row = assetRows[editingAssetIdx];
        const halfYear = isHalfYear(row?.purchase_date || row?.put_to_use_date);
        const dep = calculateDepreciation(amount, rate, halfYear);
        document.getElementById(`bDep_${idx}`).value = fmtRaw(dep);

        validateBreakup();
    }

    function addBreakupRow() {
        tempBreakups.push({ amount: 0, rate: 0 });
        renderBreakupRows();
        const lastIdx = tempBreakups.length - 1;
        setTimeout(() => {
            const el = document.getElementById(`bAmount_${lastIdx}`);
            if (el) el.focus();
        }, 50);
    }

    function deleteBreakupRow(idx) {
        if (tempBreakups.length <= 1) {
            showToast('At least one breakup row is required.', 'warning');
            return;
        }
        tempBreakups.splice(idx, 1);
        renderBreakupRows();
        validateBreakup();
    }

    function validateBreakup() {
        const idx = editingAssetIdx;
        if (idx < 0 || idx >= assetRows.length) return;

        const row = assetRows[idx];
        const depBase = (row.opening_wdv || 0) + (row.purchase_amount || 0) + (row.revaluation || 0) - (row.sale_amount || 0);
        const totalAmount = tempBreakups.reduce((sum, b) => sum + b.amount, 0);

        const totalEl = document.getElementById('breakupTotalAmount');
        const validationEl = document.getElementById('breakupValidation');

        if (totalEl) {
            totalEl.textContent = fmt(totalAmount);
            totalEl.className = 'value';
            if (Math.abs(totalAmount - depBase) < 0.01) {
                totalEl.classList.add('match');
            } else {
                totalEl.classList.add('mismatch');
            }
        }

        if (validationEl) {
            if (Math.abs(totalAmount - depBase) < 0.01) {
                validationEl.textContent = '✓ Breakup total matches Depreciable WDV.';
                validationEl.className = 'breakup-validation success';
                validationEl.style.display = 'block';
            } else {
                validationEl.textContent = '✗ Breakup total must match Depreciable WDV. Difference: ' + fmt(Math.abs(totalAmount - depBase));
                validationEl.className = 'breakup-validation error';
                validationEl.style.display = 'block';
            }
        }
    }

    function saveBreakup() {
        const idx = editingAssetIdx;
        if (idx < 0 || idx >= assetRows.length) return;

        const row = assetRows[idx];
        const depBase = (row.opening_wdv || 0) + (row.purchase_amount || 0) + (row.revaluation || 0) - (row.sale_amount || 0);
        const totalAmount = tempBreakups.reduce((sum, b) => sum + b.amount, 0);

        if (Math.abs(totalAmount - depBase) > 0.01) {
            showToast('Breakup total must match Depreciable WDV.', 'warning');
            return;
        }

        let filtered = tempBreakups.filter(b => b.amount > 0);
        if (filtered.length === 0) {
            filtered = [{ amount: depBase > 0 ? depBase : 0, rate: 0 }];
        }

        row.breakups = filtered;

        closeBreakupModal();
        renderAssets();
        updateAllTotals();
        updateFloatPanel();
        showToast('Depreciation breakup saved for ' + row.asset_name, 'success');
        autoSave();
    }

    // ─── ON ROW CHANGE ───────────────────────────────────────────────

    function onRowChange(idx) {
        const row = assetRows[idx];
        if (!row) return;

        const opening = parseNum(document.getElementById(`opening_${idx}`).value);
        const addition = parseNum(document.getElementById(`addition_${idx}`).value);
        const revaluation = parseNum(document.getElementById(`revaluation_${idx}`).value);
        const sale = parseNum(document.getElementById(`sale_${idx}`).value);
        const putToUse = document.getElementById(`putToUse_${idx}`).value;

        row.opening_wdv = opening;
        row.purchase_amount = addition;
        row.revaluation = revaluation;
        row.sale_amount = sale;
        row.block = document.getElementById(`block_${idx}`).value;   // ← NEW: capture block
        row.purchase_date = putToUse;
        row.put_to_use_date = putToUse;
        row.sale_date = document.getElementById(`saleDate_${idx}`).value;

        const maxSale = opening + addition + revaluation;
        if (sale > maxSale) {
            document.getElementById(`sale_${idx}`).classList.add('negative');
            showToast('Sale cannot exceed Opening + Addition + Revaluation.', 'warning');
            row.sale_amount = maxSale;
            document.getElementById(`sale_${idx}`).value = fmtRaw(maxSale);
        } else {
            document.getElementById(`sale_${idx}`).classList.remove('negative');
        }

        calcAndUpdateAsset(idx);

        updateAllTotals();
        updateFloatPanel();
        autoSave();
    }

    // ─── INVESTMENT & SECURITY CHANGE HANDLERS ──────────────────────

    function onInvestChange(idx) {
        const basic = parseNum(document.getElementById(`invBasic_${idx}`).value);
        const interest = parseNum(document.getElementById(`invInterest_${idx}`).value);
        const total = basic + interest;
        document.getElementById(`invTotal_${idx}`).value = fmtRaw(total);
        investments[idx].amount = basic;
        investments[idx].interest = interest;
        investments[idx].company = document.getElementById(`invCompany_${idx}`).value;
        investments[idx].pan = document.getElementById(`invPan_${idx}`).value;
        updateInvestTotals();
        updateFloatPanel();
        autoSave();
    }

    function onSecurityChange(idx) {
        const opening = parseNum(document.getElementById(`secOpening_${idx}`).value);
        const deposit = parseNum(document.getElementById(`secDeposit_${idx}`).value);
        const withdrawal = parseNum(document.getElementById(`secWithdrawal_${idx}`).value);
        const net = opening + deposit - withdrawal;
        document.getElementById(`secNet_${idx}`).value = fmtRaw(net);
        securityDeposits[idx].amount = opening;
        securityDeposits[idx].deposit = deposit;
        securityDeposits[idx].withdrawal = withdrawal;
        updateSecurityTotals();
        updateFloatPanel();
        autoSave();
    }

    // ─── ADD / DELETE ROWS ───────────────────────────────────────────

    function addAssetRow() {
        const name = prompt('Enter asset name:');
        if (!name) return;
        if (assetRows.some(r => (r.asset_name || '').toLowerCase() === name.toLowerCase())) {
            showToast('Asset name already exists.', 'warning');
            return;
        }
        assetRows.push({
            asset_name: name,
            block: '',                           // ← NEW: empty block instead of 'N/A'
            opening_wdv: 0,
            purchase_amount: 0,
            revaluation: 0,
            sale_amount: 0,
            purchase_date: '',
            sale_date: '',
            put_to_use_date: '',
            breakups: [{ amount: 0, rate: 0 }]
        });
        renderAssets();
        showToast('Asset added successfully.', 'success');
        autoSave();
    }

    function deleteAssetRow(idx) {
        const row = assetRows[idx];
        if (!confirm(`Delete asset "${row.asset_name || 'Unknown'}"?`)) return;

        if (row.id) {
            const url = DELETE_ASSET_URL.replace('__ID__', row.id);
            fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    assetRows.splice(idx, 1);
                    renderAssets();
                    showToast('Asset deleted.', 'info');
                } else {
                    showToast('Error deleting asset: ' + data.error, 'error');
                }
            })
            .catch(err => showToast('Error: ' + err.message, 'error'));
        } else {
            assetRows.splice(idx, 1);
            renderAssets();
            showToast('Asset deleted.', 'info');
            autoSave();
        }
    }

    function addInvestmentRow() {
        const name = prompt('Enter investment type:');
        if (!name) return;
        investments.push({
            investment_name: name,
            amount: 0,
            interest: 0,
            company: '',
            pan: ''
        });
        renderInvestments();
        showToast('Investment added.', 'success');
        autoSave();
    }

    function deleteInvestmentRow(idx) {
        const item = investments[idx];
        if (!confirm(`Delete investment "${item.investment_name || 'Unknown'}"?`)) return;

        if (item.id) {
            const url = DELETE_INVESTMENT_URL.replace('__ID__', item.id);
            fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    investments.splice(idx, 1);
                    renderInvestments();
                    showToast('Investment deleted.', 'info');
                } else {
                    showToast('Error deleting investment: ' + data.error, 'error');
                }
            })
            .catch(err => showToast('Error: ' + err.message, 'error'));
        } else {
            investments.splice(idx, 1);
            renderInvestments();
            showToast('Investment deleted.', 'info');
            autoSave();
        }
    }

    function addSecurityRow() {
        const name = prompt('Enter security deposit name:');
        if (!name) return;
        securityDeposits.push({
            deposit_name: name,
            amount: 0,
            deposit: 0,
            withdrawal: 0
        });
        renderSecurity();
        showToast('Security deposit added.', 'success');
        autoSave();
    }

    function deleteSecurityRow(idx) {
        const item = securityDeposits[idx];
        if (!confirm(`Delete security deposit "${item.deposit_name || 'Unknown'}"?`)) return;

        if (item.id) {
            const url = DELETE_SECURITY_URL.replace('__ID__', item.id);
            fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    securityDeposits.splice(idx, 1);
                    renderSecurity();
                    showToast('Security deposit deleted.', 'info');
                } else {
                    showToast('Error deleting security deposit: ' + data.error, 'error');
                }
            })
            .catch(err => showToast('Error: ' + err.message, 'error'));
        } else {
            securityDeposits.splice(idx, 1);
            renderSecurity();
            showToast('Security deposit deleted.', 'info');
            autoSave();
        }
    }

    // ─── UPDATE TOTALS ────────────────────────────────────────────────

    function updateAllTotals() {
        let totalOpening = 0,
            totalAddition = 0,
            totalRevaluation = 0,
            totalSale = 0,
            totalDepBase = 0,
            totalDep = 0,
            totalClosing = 0;

        assetRows.forEach((row) => {
            const opening = row.opening_wdv || 0;
            const addition = row.purchase_amount || 0;
            const revaluation = row.revaluation || 0;
            const sale = row.sale_amount || 0;
            const depBase = opening + addition + revaluation - sale;
            const halfYear = isHalfYear(row.purchase_date || row.put_to_use_date);
            let totalDepreciation = 0;
            (row.breakups || []).forEach(b => {
                totalDepreciation += calculateDepreciation(b.amount, b.rate, halfYear);
            });
            const closingWDV = depBase - totalDepreciation;

            totalOpening += opening;
            totalAddition += addition;
            totalRevaluation += revaluation;
            totalSale += sale;
            totalDepBase += depBase;
            totalDep += totalDepreciation;
            totalClosing += closingWDV;
        });

        const totOpening = document.getElementById('totOpening');
        const totAddition = document.getElementById('totAddition');
        const totRevaluation = document.getElementById('totRevaluation');
        const totSale = document.getElementById('totSale');
        const totDepBase = document.getElementById('totDepBase');
        const totDepAmt = document.getElementById('totDepAmt');
        const totClosingWDV = document.getElementById('totClosingWDV');

        if (totOpening) totOpening.value = fmtRaw(totalOpening);
        if (totAddition) totAddition.value = fmtRaw(totalAddition);
        if (totRevaluation) totRevaluation.value = fmtRaw(totalRevaluation);
        if (totSale) totSale.value = fmtRaw(totalSale);
        if (totDepBase) totDepBase.value = fmtRaw(totalDepBase);
        if (totDepAmt) totDepAmt.value = fmtRaw(totalDep);
        if (totClosingWDV) totClosingWDV.value = fmtRaw(totalClosing);

        const cardOpening = document.getElementById('cardOpeningWDV');
        const cardPurchase = document.getElementById('cardPurchase');
        const cardSale = document.getElementById('cardSale');
        const cardDepBase = document.getElementById('cardDepBase');
        const cardDepreciation = document.getElementById('cardDepreciation');
        const cardClosingWDV = document.getElementById('cardClosingWDV');

        if (cardOpening) cardOpening.textContent = fmt(totalOpening);
        if (cardPurchase) cardPurchase.textContent = fmt(totalAddition);
        if (cardSale) cardSale.textContent = fmt(totalSale);
        if (cardDepBase) cardDepBase.textContent = fmt(totalDepBase);
        if (cardDepreciation) cardDepreciation.textContent = fmt(totalDep);
        if (cardClosingWDV) cardClosingWDV.textContent = fmt(totalClosing);

        window._totalOpening = totalOpening;
        window._totalDepBase = totalDepBase;
        window._totalDep = totalDep;
        window._totalClosing = totalClosing;
    }

    function updateInvestTotals() {
        let totalBasic = 0,
            totalInterest = 0,
            totalValue = 0;
        investments.forEach((item) => {
            const basic = parseFloat(item.amount) || 0;
            const interest = parseFloat(item.interest) || 0;
            totalBasic += basic;
            totalInterest += interest;
            totalValue += basic + interest;
        });
        const totInvBasic = document.getElementById('totInvBasic');
        const totInvInterest = document.getElementById('totInvInterest');
        const totInvValue = document.getElementById('totInvValue');
        if (totInvBasic) totInvBasic.value = fmtRaw(totalBasic);
        if (totInvInterest) totInvInterest.value = fmtRaw(totalInterest);
        if (totInvValue) totInvValue.value = fmtRaw(totalValue);
        window._investTotal = totalValue;
    }

    function updateSecurityTotals() {
        let totalOpening = 0,
            totalDeposit = 0,
            totalWithdrawal = 0,
            totalNet = 0;
        securityDeposits.forEach((item) => {
            const opening = parseFloat(item.amount) || 0;
            const deposit = parseFloat(item.deposit) || 0;
            const withdrawal = parseFloat(item.withdrawal) || 0;
            totalOpening += opening;
            totalDeposit += deposit;
            totalWithdrawal += withdrawal;
            totalNet += opening + deposit - withdrawal;
        });
        const totSecOpening = document.getElementById('totSecOpening');
        const totSecDeposit = document.getElementById('totSecDeposit');
        const totSecWithdrawal = document.getElementById('totSecWithdrawal');
        const totSecNet = document.getElementById('totSecNet');
        if (totSecOpening) totSecOpening.value = fmtRaw(totalOpening);
        if (totSecDeposit) totSecDeposit.value = fmtRaw(totalDeposit);
        if (totSecWithdrawal) totSecWithdrawal.value = fmtRaw(totalWithdrawal);
        if (totSecNet) totSecNet.value = fmtRaw(totalNet);
        window._securityTotal = totalNet;
    }

    // ─── FLOAT PANEL ──────────────────────────────────────────────────

    function updateFloatPanel() {
        const opening = window._totalOpening || 0;
        const depBase = window._totalDepBase || 0;
        const dep = window._totalDep || 0;
        const closing = window._totalClosing || 0;
        const invest = window._investTotal || 0;
        const security = window._securityTotal || 0;
        const netWorth = closing + invest + security;

        const fpOpeningWDV = document.getElementById('fpOpeningWDV');
        const fpDepBase = document.getElementById('fpDepBase');
        const fpDepreciation = document.getElementById('fpDepreciation');
        const fpClosingWDV = document.getElementById('fpClosingWDV');
        const fpInvestments = document.getElementById('fpInvestments');
        const fpSecurity = document.getElementById('fpSecurity');
        const fpNetWorth = document.getElementById('fpNetWorth');

        if (fpOpeningWDV) fpOpeningWDV.textContent = fmt(opening);
        if (fpDepBase) fpDepBase.textContent = fmt(depBase);
        if (fpDepreciation) fpDepreciation.textContent = fmt(dep);
        if (fpClosingWDV) fpClosingWDV.textContent = fmt(closing);
        if (fpInvestments) fpInvestments.textContent = fmt(invest);
        if (fpSecurity) fpSecurity.textContent = fmt(security);
        if (fpNetWorth) fpNetWorth.textContent = fmt(netWorth);
    }

    // ─── FILTER ───────────────────────────────────────────────────────

    function filterAssets() {
        const q = document.getElementById('globalSearch')?.value?.toLowerCase?.() || '';
        const rows = assetsBody.querySelectorAll('tr');
        rows.forEach((row, idx) => {
            if (idx === rows.length - 1) return;
            const name = row.dataset.name || '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    }

    // ─── TOGGLE SECTION ──────────────────────────────────────────────

    function toggleSection(id) {
        const body = document.getElementById(id);
        const arrow = document.getElementById(id.replace('Body', 'Arrow'));
        if (body) body.classList.toggle('collapsed');
        if (arrow) arrow.classList.toggle('open');
    }

    // ─── AUTO SAVE ────────────────────────────────────────────────────

    function autoSave() {
        if (isSaving) return;
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            saveToDatabase('auto');
        }, 800);
    }

    // ─── SAVE TO DATABASE (MASTER FUNCTION) ──────────────────────────

    function saveToDatabase(action = 'draft') {
        if (isSaving) return;
        isSaving = true;

        // Find the save button to update its text and state
        const btn = document.getElementById('saveBtn') || document.querySelector('.btn-primary, .btn-save, .btn-submit, .btn-finalize');
        const originalText = btn ? btn.innerText : '';
        if (btn) {
            btn.disabled = true;
            if (action === 'submit') btn.innerText = 'Submitting...';
            else if (action === 'finalize') btn.innerText = 'Finalizing...';
            else btn.innerText = 'Saving...';
        }

        const data = {
            assets: assetRows.map(row => ({
                id: row.id || null,
                asset_name: row.asset_name || 'New Asset',
                unit: row.unit || 1,
                block: row.block || '',                     // ← block is now sent (it's already in row)
                opening_wdv: row.opening_wdv || 0,
                purchase_amount: row.purchase_amount || 0,
                revaluation: row.revaluation || 0,
                sale_amount: row.sale_amount || 0,
                purchase_date: row.purchase_date || row.put_to_use_date || null,
                sale_date: row.sale_date || null,
                put_to_use_date: row.put_to_use_date || row.purchase_date || null,
                depreciable_wdv: (row.opening_wdv || 0) + (row.purchase_amount || 0) + (row.revaluation || 0) - (row.sale_amount || 0),
                business_use_percentage: row.business_use_percentage || 100,
                depreciation_amount: 0,
                deduction_amount: 0,
                closing_wdv: 0,
                remarks: row.remarks || '',
                breakdowns: (row.breakups || []).map(b => ({
                    id: b.id || null,
                    amount: b.amount || 0,
                    depreciation_rate: b.rate || 0,
                    depreciation_amount: 0
                }))
            })),
            investments: investments.map(item => ({
                id: item.id || null,
                investment_name: item.investment_name || 'New Investment',
                amount: item.amount || 0,
                interest: item.interest || 0,
                company: item.company || '',
                pan: item.pan || '',
                remarks: item.remarks || ''
            })),
            security_deposits: securityDeposits.map(item => ({
                id: item.id || null,
                deposit_name: item.deposit_name || 'New Deposit',
                amount: item.amount || 0,
                deposit: item.deposit || 0,
                withdrawal: item.withdrawal || 0,
                remarks: item.remarks || ''
            }))
        };

        fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            isSaving = false;
            if (btn) { btn.disabled = false; btn.innerText = originalText; }

            if (data.success) {
                if (data.assets) {
                    assetRows = data.assets.map(asset => ({
                        ...asset,
                        breakups: asset.breakdowns ? asset.breakdowns.map(b => ({
                            id: b.id,
                            amount: parseFloat(b.amount) || 0,
                            rate: parseFloat(b.depreciation_rate) || 0
                        })) : []
                    }));
                }
                if (data.investments) investments = data.investments;
                if (data.security_deposits) securityDeposits = data.security_deposits;

                renderAssets();
                renderInvestments();
                renderSecurity();
                updateFloatPanel();

                if (action === 'submit') {
                    showToast('✅ Data submitted successfully to database!', 'success');
                } else if (action === 'finalize') {
                    showToast('✅ Report finalized and saved successfully!', 'success');
                    const wm = document.getElementById('wmText');
                    if (wm) wm.style.display = 'none';
                } else if (action === 'auto') {
                    // Silent auto-save
                } else {
                    // ✅ EXACTLY WHAT YOU ASKED FOR
                    showToast('✓ Changes saved successfully', 'success');
                }
            } else {
                showToast('❌ Save failed: ' + (data.error || 'Unknown error'), 'error');
            }
        })
        .catch(err => {
            isSaving = false;
            if (btn) { btn.disabled = false; btn.innerText = originalText; }
            showToast('❌ Error saving: ' + err.message, 'error');
            console.error('Save error:', err);
        });
    }

    // ─── SAVE DRAFT ──────────────────────────────────────────────────

    function saveDraft() {
        showToast('Saving...', 'info');
        saveToDatabase('draft');
    }

    // ─── SUBMIT ─────────────────────────────────────────────────────

    function submitReport() {
        showToast('Submitting report...', 'info');
        saveToDatabase('submit');
    }

    // ─── FINALIZE ────────────────────────────────────────────────────

    function finalizeReport() {
        if (!confirm('Are you sure you want to finalize this report?')) return;
        saveToDatabase('finalize');
    }

    // ─── EXPORT ─────────────────────────────────────────────────────

    function exportExcel() {
        showToast('Excel export ready! (Simulation)', 'success');
    }

    // ─── CA SIGNATURE UPLOAD ────────────────────────────────────────

    function handleCaSignatureUpload(file) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgData = e.target.result;
            const wrapper = document.getElementById('caSigDisplay');
            if (wrapper) {
                wrapper.innerHTML = `<img src="${imgData}" alt="CA Signature" />`;
            }
            try { localStorage.setItem('caSignature', imgData); } catch (ex) {}
        };
        reader.readAsDataURL(file);
    }

    function clearCaSignature() {
        const wrapper = document.getElementById('caSigDisplay');
        if (wrapper) wrapper.innerHTML = `<div class="ft-sign-line"></div>`;
        const upload = document.getElementById('caSigUpload');
        if (upload) upload.value = '';
        try { localStorage.removeItem('caSignature'); } catch (ex) {}
        showToast('Signature cleared.', 'info');
    }

    function loadSavedSignature() {
        try {
            const saved = localStorage.getItem('caSignature');
            if (saved) {
                const wrapper = document.getElementById('caSigDisplay');
                if (wrapper) wrapper.innerHTML = `<img src="${saved}" alt="CA Signature" />`;
            }
        } catch (ex) {}
    }

    const sigUpload = document.getElementById('caSigUpload');
    if (sigUpload) {
        sigUpload.addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) {
                handleCaSignatureUpload(this.files[0]);
            }
        });
    }

    // ─── KEYBOARD SHORTCUTS ────────────────────────────────────────────

    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveDraft();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });

    // ─── CLOCK ───────────────────────────────────────────────────────

    function tick() {
        const n = new Date();
        const el = document.getElementById('sbTime');
        if (el) {
            el.textContent =
                String(n.getHours()).padStart(2, '0') + ':' +
                String(n.getMinutes()).padStart(2, '0') + ':' +
                String(n.getSeconds()).padStart(2, '0');
        }
    }
    setInterval(tick, 1000);
    tick();

    // ─── DROPDOWN FUNCTIONS ─────────────────────────────────────────

    function addRowDropdown() {
        const dropdown = document.getElementById('addRowDropdown');
        if (dropdown) {
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    }

    function closeDropdown() {
        const dropdown = document.getElementById('addRowDropdown');
        if (dropdown) {
            dropdown.style.display = 'none';
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('addRowDropdown');
        const btn = e.target.closest('.btn-erp.btn-blue');
        if (dropdown && !dropdown.contains(e.target) && !btn) {
            dropdown.style.display = 'none';
        }
    });

    // ─── INIT ────────────────────────────────────────────────────────

    function init() {
        loadSavedSignature();
        loadDataFromDatabase();
        setTimeout(autoSave, 500);
        showToast('Assets Reconciliation loaded.', 'success');
    }

    document.addEventListener('DOMContentLoaded', init);

    // ─── EXPOSE TO GLOBAL ───────────────────────────────────────────

    window.addAssetRow = addAssetRow;
    window.deleteAssetRow = deleteAssetRow;
    window.addInvestmentRow = addInvestmentRow;
    window.deleteInvestmentRow = deleteInvestmentRow;
    window.addSecurityRow = addSecurityRow;
    window.deleteSecurityRow = deleteSecurityRow;
    window.onRowChange = onRowChange;
    window.onInvestChange = onInvestChange;
    window.onSecurityChange = onSecurityChange;
    window.filterAssets = filterAssets;
    window.toggleSection = toggleSection;
    window.openBreakupModal = openBreakupModal;
    window.closeBreakupModal = closeBreakupModal;
    window.onBreakupChange = onBreakupChange;
    window.addBreakupRow = addBreakupRow;
    window.deleteBreakupRow = deleteBreakupRow;
    window.saveBreakup = saveBreakup;
    window.exportExcel = exportExcel;
    window.saveDraft = saveDraft;
    window.submitReport = submitReport;
    window.finalizeReport = finalizeReport;
    window.clearCaSignature = clearCaSignature;
    window.handleCaSignatureUpload = handleCaSignatureUpload;
    window.showToast = showToast;
    window.addRowDropdown = addRowDropdown;
    window.closeDropdown = closeDropdown;
    window.calcAndUpdateAsset = calcAndUpdateAsset;
    window.saveToDatabase = saveToDatabase;
</script>