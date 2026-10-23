<script>
    const COMPUTATION_ID = {{ $computation->id }};
    const BANK_RECON_ID = {{ $bankReconciliation->id ?? 0 }};

    // Routes
    const SAVE_URL = "{{ route('backoffice.computations.bank-reconciliation.save', ['computation' => $computation->id]) }}";
    const ADD_CHEQUE_URL = "{{ route('backoffice.computations.bank-reconciliation.add-cheque', ['computation' => $computation->id]) }}";
    const DELETE_CHEQUE_URL = "{{ route('backoffice.computations.bank-reconciliation.delete-cheque', ['computation' => $computation->id, 'rowId' => '__ROW_ID__']) }}";
    const ADD_DEPOSIT_URL = "{{ route('backoffice.computations.bank-reconciliation.add-deposit', ['computation' => $computation->id]) }}";
    const DELETE_DEPOSIT_URL = "{{ route('backoffice.computations.bank-reconciliation.delete-deposit', ['computation' => $computation->id, 'rowId' => '__ROW_ID__']) }}";
    const ADD_ADJUSTMENT_URL = "{{ route('backoffice.computations.bank-reconciliation.add-adjustment', ['computation' => $computation->id]) }}";
    const DELETE_ADJUSTMENT_URL = "{{ route('backoffice.computations.bank-reconciliation.delete-adjustment', ['computation' => $computation->id, 'rowId' => '__ROW_ID__']) }}";
    const FINALIZE_URL = "{{ route('backoffice.computations.bank-reconciliation.finalize', ['computation' => $computation->id]) }}";

    // Data arrays from server – ensure amount is numeric
    let cheques = @json($cheques).map(r => ({ ...r, amount: parseFloat(r.amount) || 0 }));
    let deposits = @json($deposits).map(r => ({ ...r, amount: parseFloat(r.amount) || 0 }));
    let adjustments = @json($adjustments).map(r => ({ ...r, amount: parseFloat(r.amount) || 0 }));

    let isSaving = false;
    let saveTimeout = null;

    // ─── HELPERS ──────────────────────────────────────────────────────

    function formatIndian(n) {
        if (n === undefined || n === null || isNaN(n)) return '₹0';
        const num = Math.round(n);
        if (num === 0) return '₹0';
        const sign = num < 0 ? '-' : '';
        const abs = Math.abs(num);
        const s = abs.toString();
        const last3 = s.slice(-3);
        const rest = s.slice(0, -3);
        if (rest === '') return sign + '₹' + last3;
        const formattedRest = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',');
        return sign + '₹' + formattedRest + ',' + last3;
    }

    function parseIndian(v) {
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

    function fmtRaw(n) {
        if (n === undefined || n === null || isNaN(n)) return 0;
        return Math.round(n);
    }

    // sumAmounts – always parse to number and sum (including negatives)
    function sumAmounts(arr) {
        let total = 0;
        if (!Array.isArray(arr)) return 0;
        arr.forEach(row => {
            const val = parseFloat(row.amount);
            if (!isNaN(val)) {
                total += val;
            }
        });
        return total;
    }

    // ─── TOAST ──────────────────────────────────────────────────────────

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

    // ─── RENDER FUNCTIONS (Only for add/delete/duplicate) ─────────────

    function renderCheques() {
        let html = '';
        let total = 0;
        cheques.forEach((row, idx) => {
            const id = row.id || 'new_' + idx;
            total += parseFloat(row.amount) || 0;
            let dateVal = row.cheque_date || '';
            if (dateVal && dateVal.includes('T')) {
                dateVal = dateVal.split('T')[0];
            }
            html += `
                <tr data-cheque-id="${id}">
                    <td style="text-align:center;font-weight:500;color:var(--text-muted);">${idx+1}</td>
                    <td><input class="td-date" type="date" id="chqDate_${id}" value="${dateVal}" oninput="onChequeChange('${id}')" style="width:100%;" /></td>
                    <td><input class="td-text" type="text" id="chqNumber_${id}" value="${row.cheque_no || ''}" oninput="onChequeChange('${id}')" style="text-align:center;font-family:var(--font-mono);width:100%;" /></td>
                    <td><input class="td-amt" type="text" id="chqAmount_${id}" value="${formatIndian(row.amount)}" oninput="onChequeChange('${id}')" style="width:100%;text-align:right;" /></td>
                    <td class="td-actions">
                        <button onclick="duplicateChequeRow('${id}')" title="Duplicate"><i class="bi bi-files"></i></button>
                        <button class="del" onclick="deleteChequeRow('${id}')" title="Delete"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        });
        html += `
            <tr class="total-row">
                <td></td><td></td>
                <td style="text-align:right;padding-right:8px;font-weight:700;text-transform:uppercase;font-family:var(--font-sans);">TOTAL</td>
                <td><input class="td-amt total-cell" type="text" id="chqTotal" value="${formatIndian(total)}" readonly style="width:100%;text-align:right;font-weight:700;" /></td>
                <td></td>
            </tr>
        `;
        const tbody = document.getElementById('chequesBodyRows');
        if (tbody) tbody.innerHTML = html;
        const label = document.getElementById('chequeTotalLabel');
        if (label) label.textContent = 'Total: ' + formatIndian(total);
        // Update summary without re-rendering
        updateTotalsAndSummary();
    }

    function renderDeposits() {
        let html = '';
        let total = 0;
        deposits.forEach((row, idx) => {
            const id = row.id || 'new_' + idx;
            total += parseFloat(row.amount) || 0;
            let dateVal = row.deposit_date || '';
            if (dateVal && dateVal.includes('T')) {
                dateVal = dateVal.split('T')[0];
            }
            html += `
                <tr data-deposit-id="${id}">
                    <td style="text-align:center;font-weight:500;color:var(--text-muted);">${idx+1}</td>
                    <td><input class="td-date" type="date" id="depDate_${id}" value="${dateVal}" oninput="onDepositChange('${id}')" style="width:100%;" /></td>
                    <td><input class="td-amt" type="text" id="depAmount_${id}" value="${formatIndian(row.amount)}" oninput="onDepositChange('${id}')" style="width:100%;text-align:right;" /></td>
                    <td class="td-actions">
                        <button onclick="duplicateDepositRow('${id}')" title="Duplicate"><i class="bi bi-files"></i></button>
                        <button class="del" onclick="deleteDepositRow('${id}')" title="Delete"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        });
        html += `
            <tr class="total-row">
                <td></td>
                <td style="text-align:right;padding-right:8px;font-weight:700;text-transform:uppercase;font-family:var(--font-sans);">TOTAL</td>
                <td><input class="td-amt total-cell" type="text" id="depTotal" value="${formatIndian(total)}" readonly style="width:100%;text-align:right;font-weight:700;" /></td>
                <td></td>
            </tr>
        `;
        const tbody = document.getElementById('depositsBodyRows');
        if (tbody) tbody.innerHTML = html;
        const label = document.getElementById('depositTotalLabel');
        if (label) label.textContent = 'Total: ' + formatIndian(total);
        updateTotalsAndSummary();
    }

    function renderAdjustments() {
        let html = '';
        let total = 0;
        const types = ['Bank Charges', 'Interest', 'Bounce Charge', 'NEFT Charge', 'RTGS Charge', 'Others'];
        adjustments.forEach((row, idx) => {
            const id = row.id || 'new_' + idx;
            total += parseFloat(row.amount) || 0;
            let dateVal = row.adjustment_date || '';
            if (dateVal && dateVal.includes('T')) {
                dateVal = dateVal.split('T')[0];
            }
            const opts = types.map(t => `<option value="${t}" ${t === row.adjustment_type ? 'selected' : ''}>${t}</option>`).join('');
            html += `
                <tr data-adjustment-id="${id}">
                    <td style="text-align:center;font-weight:500;color:var(--text-muted);">${idx+1}</td>
                    <td><input class="td-date" type="date" id="adjDate_${id}" value="${dateVal}" oninput="onAdjustmentChange('${id}')" style="width:100%;" /></td>
                    <td><select class="td-select" id="adjType_${id}" onchange="onAdjustmentChange('${id}')" style="width:100%;">${opts}</select></td>
                    <td><input class="td-amt" type="text" id="adjAmount_${id}" value="${formatIndian(row.amount)}" oninput="onAdjustmentChange('${id}')" style="width:100%;text-align:right;" /></td>
                    <td class="td-actions">
                        <button onclick="duplicateAdjustmentRow('${id}')" title="Duplicate"><i class="bi bi-files"></i></button>
                        <button class="del" onclick="deleteAdjustmentRow('${id}')" title="Delete"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        });
        html += `
            <tr class="total-row">
                <td></td><td></td>
                <td style="text-align:right;padding-right:8px;font-weight:700;text-transform:uppercase;font-family:var(--font-sans);">TOTAL</td>
                <td><input class="td-amt total-cell" type="text" id="adjTotal" value="${formatIndian(total)}" readonly style="width:100%;text-align:right;font-weight:700;" /></td>
                <td></td>
            </tr>
        `;
        const tbody = document.getElementById('adjustmentsBodyRows');
        if (tbody) tbody.innerHTML = html;
        const label = document.getElementById('adjustmentTotalLabel');
        if (label) label.textContent = 'Total: ' + formatIndian(total);
        updateTotalsAndSummary();
    }

    // ─── CHANGE HANDLERS (No full re-render) ─────────────────────────

    function onChequeChange(id) {
        const row = cheques.find(r => (r.id || 'new_' + cheques.indexOf(r)) == id);
        if (!row) return;
        row.cheque_date = document.getElementById(`chqDate_${id}`)?.value || '';
        row.cheque_no = document.getElementById(`chqNumber_${id}`)?.value || '';
        const amt = parseIndian(document.getElementById(`chqAmount_${id}`)?.value || '0');
        if (amt < 0) { showToast('Amount cannot be negative.', 'warning'); return; }
        row.amount = amt;
        // Just update totals without re-rendering all rows
        updateTotalsAndSummary();
        autoSave();
    }

    function onDepositChange(id) {
        const row = deposits.find(r => (r.id || 'new_' + deposits.indexOf(r)) == id);
        if (!row) return;
        row.deposit_date = document.getElementById(`depDate_${id}`)?.value || '';
        const amt = parseIndian(document.getElementById(`depAmount_${id}`)?.value || '0');
        if (amt < 0) { showToast('Amount cannot be negative.', 'warning'); return; }
        row.amount = amt;
        updateTotalsAndSummary();
        autoSave();
    }

    function onAdjustmentChange(id) {
        const row = adjustments.find(r => (r.id || 'new_' + adjustments.indexOf(r)) == id);
        if (!row) return;
        row.adjustment_date = document.getElementById(`adjDate_${id}`)?.value || '';
        row.adjustment_type = document.getElementById(`adjType_${id}`)?.value || 'Others';
        row.amount = parseIndian(document.getElementById(`adjAmount_${id}`)?.value || '0');
        updateTotalsAndSummary();
        autoSave();
    }

    // ─── UPDATE TOTALS WITHOUT RE-RENDERING ──────────────────────────

    function updateTotalsAndSummary() {
        const totalCheques = sumAmounts(cheques);
        const totalDeposits = sumAmounts(deposits);
        const otherAdj = parseIndian(document.getElementById('otherAdjustments')?.value || '0');
        const totalAdj = sumAmounts(adjustments) + otherAdj;

        // Update total row inputs if they exist
        const chqTotal = document.getElementById('chqTotal');
        if (chqTotal) chqTotal.value = formatIndian(totalCheques);
        const depTotal = document.getElementById('depTotal');
        if (depTotal) depTotal.value = formatIndian(totalDeposits);
        const adjTotal = document.getElementById('adjTotal');
        if (adjTotal) adjTotal.value = formatIndian(totalAdj);

        // Update header labels
        const chqLabel = document.getElementById('chequeTotalLabel');
        if (chqLabel) chqLabel.textContent = 'Total: ' + formatIndian(totalCheques);
        const depLabel = document.getElementById('depositTotalLabel');
        if (depLabel) depLabel.textContent = 'Total: ' + formatIndian(totalDeposits);
        const adjLabel = document.getElementById('adjustmentTotalLabel');
        if (adjLabel) adjLabel.textContent = 'Total: ' + formatIndian(totalAdj);

        // Recalculate the main BRS (left column and summary strip)
        calcBRS();
    }

    // ─── MAIN CALCULATION (calcBRS) – only updates read-only fields ──

    function calcBRS() {
        const bankBalanceEl = document.getElementById('bankBalance');
        const otherAdjEl = document.getElementById('otherAdjustments');
        const cashBookEl = document.getElementById('cashBookBalance');

        const bankBalance = parseIndian(bankBalanceEl?.value || '0');
        const otherAdjManual = parseIndian(otherAdjEl?.value || '0');
        const cashBook = parseIndian(cashBookEl?.value || '0');

        const totalCheques = sumAmounts(cheques);
        const totalDeposits = sumAmounts(deposits);
        const totalAdj = sumAmounts(adjustments) + otherAdjManual;

        const adjusted = bankBalance - totalCheques + totalDeposits + totalAdj;
        const variance = adjusted - cashBook;
        const isBalanced = Math.abs(variance) < 0.01;

        // Update left column read-only fields
        const totalChequesEl = document.getElementById('totalCheques');
        const totalDepositsEl = document.getElementById('totalDeposits');
        const adjustedBalanceEl = document.getElementById('adjustedBalance');
        const varianceEl = document.getElementById('variance');
        const statusBadge = document.getElementById('statusBadge');

        if (totalChequesEl) totalChequesEl.value = fmtRaw(totalCheques);
        if (totalDepositsEl) totalDepositsEl.value = fmtRaw(totalDeposits);
        if (adjustedBalanceEl) adjustedBalanceEl.value = fmtRaw(adjusted);
        if (varianceEl) {
            varianceEl.value = fmtRaw(variance);
            varianceEl.className = 'readonly ' + (isBalanced ? 'variance-zero' : 'variance-not-zero');
        }
        if (statusBadge) {
            if (isBalanced) {
                statusBadge.className = 'status-badge balanced';
                statusBadge.textContent = '✔ Balanced';
            } else {
                statusBadge.className = 'status-badge not-balanced';
                statusBadge.textContent = '✘ Not Balanced';
            }
        }

        // Update summary strip
        const sumMap = {
            'sumBankBalance': bankBalance,
            'sumCheques': totalCheques,
            'sumDeposits': totalDeposits,
            'sumAdjustments': totalAdj,
            'sumAdjusted': adjusted,
            'sumCashBook': cashBook,
            'sumVariance': variance
        };
        for (const [id, val] of Object.entries(sumMap)) {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = formatIndian(val);
                el.className = 'val';
                if (val > 0) el.classList.add('positive');
                else if (val < 0) el.classList.add('negative');
                else el.classList.add('zero');
            }
        }

        const statusBadgeSum = document.getElementById('sumStatusBadge');
        if (statusBadgeSum) {
            if (isBalanced) {
                statusBadgeSum.className = 'status-badge balanced';
                statusBadgeSum.textContent = '✔ Balanced';
            } else {
                statusBadgeSum.className = 'status-badge not-balanced';
                statusBadgeSum.textContent = '✘ Not Balanced';
            }
        }

        // Optional floating panel – check existence
        const fpBankBalance = document.getElementById('fpBankBalance');
        const fpCheques = document.getElementById('fpCheques');
        const fpDeposits = document.getElementById('fpDeposits');
        const fpAdjusted = document.getElementById('fpAdjusted');
        const fpVariance = document.getElementById('fpVariance');

        if (fpBankBalance) fpBankBalance.textContent = formatIndian(bankBalance);
        if (fpCheques) fpCheques.textContent = formatIndian(totalCheques);
        if (fpDeposits) fpDeposits.textContent = formatIndian(totalDeposits);
        if (fpAdjusted) fpAdjusted.textContent = formatIndian(adjusted);
        if (fpVariance) {
            fpVariance.textContent = formatIndian(variance);
            fpVariance.style.color = isBalanced ? 'var(--emerald)' : 'var(--red)';
        }
    }

    // ─── ADD / DELETE / DUPLICATE (these re-render) ──────────────────

    // ---------- CHEQUES ----------
    function addChequeRow() {
        cheques.push({ cheque_date: '', cheque_no: '', amount: 0 });
        renderCheques();
        autoSave();
    }

    function deleteChequeRow(id) {
        if (!confirm('Delete this cheque row?')) return;
        const idx = cheques.findIndex(r => (r.id || 'new_' + cheques.indexOf(r)) == id);
        if (idx === -1) return;
        const row = cheques[idx];
        if (row.id) {
            const url = DELETE_CHEQUE_URL.replace('__ROW_ID__', row.id);
            fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(res => res.json())
                .then(data => { if (data.success) { cheques.splice(idx, 1); renderCheques(); showToast('Cheque deleted.', 'info'); } });
        } else {
            cheques.splice(idx, 1);
            renderCheques();
        }
    }

    function duplicateChequeRow(id) {
        const idx = cheques.findIndex(r => (r.id || 'new_' + cheques.indexOf(r)) == id);
        if (idx === -1) return;
        const row = { ...cheques[idx] };
        delete row.id;
        cheques.splice(idx + 1, 0, row);
        renderCheques();
        autoSave();
    }

    // ---------- DEPOSITS ----------
    function addDepositRow() {
        deposits.push({ deposit_date: '', amount: 0 });
        renderDeposits();
        autoSave();
    }

    function deleteDepositRow(id) {
        if (!confirm('Delete this deposit row?')) return;
        const idx = deposits.findIndex(r => (r.id || 'new_' + deposits.indexOf(r)) == id);
        if (idx === -1) return;
        const row = deposits[idx];
        if (row.id) {
            const url = DELETE_DEPOSIT_URL.replace('__ROW_ID__', row.id);
            fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(res => res.json())
                .then(data => { if (data.success) { deposits.splice(idx, 1); renderDeposits(); showToast('Deposit deleted.', 'info'); } });
        } else {
            deposits.splice(idx, 1);
            renderDeposits();
        }
    }

    function duplicateDepositRow(id) {
        const idx = deposits.findIndex(r => (r.id || 'new_' + deposits.indexOf(r)) == id);
        if (idx === -1) return;
        const row = { ...deposits[idx] };
        delete row.id;
        deposits.splice(idx + 1, 0, row);
        renderDeposits();
        autoSave();
    }

    // ---------- ADJUSTMENTS ----------
    function addAdjustmentRow() {
        adjustments.push({ adjustment_date: '', adjustment_type: 'Others', amount: 0, remarks: '' });
        renderAdjustments();
        autoSave();
    }

    function deleteAdjustmentRow(id) {
        if (!confirm('Delete this adjustment row?')) return;
        const idx = adjustments.findIndex(r => (r.id || 'new_' + adjustments.indexOf(r)) == id);
        if (idx === -1) return;
        const row = adjustments[idx];
        if (row.id) {
            const url = DELETE_ADJUSTMENT_URL.replace('__ROW_ID__', row.id);
            fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(res => res.json())
                .then(data => { if (data.success) { adjustments.splice(idx, 1); renderAdjustments(); showToast('Adjustment deleted.', 'info'); } });
        } else {
            adjustments.splice(idx, 1);
            renderAdjustments();
        }
    }

    function duplicateAdjustmentRow(id) {
        const idx = adjustments.findIndex(r => (r.id || 'new_' + adjustments.indexOf(r)) == id);
        if (idx === -1) return;
        const row = { ...adjustments[idx] };
        delete row.id;
        adjustments.splice(idx + 1, 0, row);
        renderAdjustments();
        autoSave();
    }

    // ─── SAVE DRAFT ──────────────────────────────────────────────────

    function saveDraft() {
        showToast('Saving...', 'info');
        autoSave();
    }

    function autoSave() {
        if (isSaving) return;
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            isSaving = true;
            const data = {
                statement_date: document.getElementById('statementDate')?.value || null,
                closing_balance_bank: parseIndian(document.getElementById('bankBalance')?.value || '0'),
                closing_balance_cashbook: parseIndian(document.getElementById('cashBookBalance')?.value || '0'),
                cheques: cheques.map(r => ({ ...r, amount: r.amount || 0 })),
                deposits: deposits.map(r => ({ ...r, amount: r.amount || 0 })),
                adjustments: adjustments.map(r => ({ ...r, amount: r.amount || 0 }))
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
                if (data.success) {
                    if (data.cheques) cheques = data.cheques.map(r => ({ ...r, amount: parseFloat(r.amount) || 0 }));
                    if (data.deposits) deposits = data.deposits.map(r => ({ ...r, amount: parseFloat(r.amount) || 0 }));
                    if (data.adjustments) adjustments = data.adjustments.map(r => ({ ...r, amount: parseFloat(r.amount) || 0 }));
                    renderCheques();
                    renderDeposits();
                    renderAdjustments();
                    showToast('Auto-saved.', 'success');
                } else {
                    showToast('Save failed: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(err => {
                isSaving = false;
                showToast('Error saving: ' + err.message, 'error');
                console.error('Save error:', err);
            });
        }, 800);
    }

    // ─── FINALIZE ────────────────────────────────────────────────────

    function finalizeReport() {
        const variance = parseIndian(document.getElementById('variance')?.value || '0');
        if (Math.abs(variance) > 0.01) {
            if (!confirm('Variance is not zero. Are you sure you want to finalize?')) return;
        }
        fetch(FINALIZE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('wmText').style.display = 'none';
                showToast('Report finalized!', 'success');
            }
        })
        .catch(err => showToast('Error finalizing: ' + err.message, 'error'));
    }

    // ─── CA SIGNATURE ──────────────────────────────────────────────────

    const sigUpload = document.getElementById('caSigUpload');
    if (sigUpload) {
        sigUpload.addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const imgData = ev.target.result;
                    const display = document.getElementById('caSigDisplay');
                    if (display) {
                        display.innerHTML = `<img src="${imgData}" alt="CA Signature" style="max-height:34px;max-width:120px;border:1px solid #e0e7ef;border-radius:3px;padding:2px;background:#fff;" />`;
                        showToast('Signature uploaded.', 'success');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function clearCaSignature() {
        const display = document.getElementById('caSigDisplay');
        if (display) display.innerHTML = `<div class="ft-sign-line"></div>`;
        const upload = document.getElementById('caSigUpload');
        if (upload) upload.value = '';
        showToast('Signature cleared.', 'info');
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

    // ─── INIT ──────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function() {
        // Ensure all amounts are numeric after initial load
        cheques = cheques.map(r => ({ ...r, amount: parseFloat(r.amount) || 0 }));
        deposits = deposits.map(r => ({ ...r, amount: parseFloat(r.amount) || 0 }));
        adjustments = adjustments.map(r => ({ ...r, amount: parseFloat(r.amount) || 0 }));

        renderCheques();
        renderDeposits();
        renderAdjustments();
        // calcBRS is called inside each render function, but also call once to ensure summary is up to date.
        // However, render functions already call updateTotalsAndSummary -> calcBRS, so it's fine.
        showToast('Bank Reconciliation loaded.', 'success');
    });

    // Expose to global scope
    window.addChequeRow = addChequeRow;
    window.deleteChequeRow = deleteChequeRow;
    window.duplicateChequeRow = duplicateChequeRow;
    window.onChequeChange = onChequeChange;
    window.addDepositRow = addDepositRow;
    window.deleteDepositRow = deleteDepositRow;
    window.duplicateDepositRow = duplicateDepositRow;
    window.onDepositChange = onDepositChange;
    window.addAdjustmentRow = addAdjustmentRow;
    window.deleteAdjustmentRow = deleteAdjustmentRow;
    window.duplicateAdjustmentRow = duplicateAdjustmentRow;
    window.onAdjustmentChange = onAdjustmentChange;
    window.calcBRS = calcBRS;
    window.saveDraft = saveDraft;
    window.finalizeReport = finalizeReport;
    window.clearCaSignature = clearCaSignature;
    window.showToast = showToast;
</script>