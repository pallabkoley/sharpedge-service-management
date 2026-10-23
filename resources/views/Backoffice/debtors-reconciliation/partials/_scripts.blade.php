<script>
    const COMPUTATION_ID = {{ $computation->id }};
    const DEBTORS_URL = "{{ route('backoffice.computations.debtors-reconciliation.data', ['computation' => $computation->id]) }}";
    const SAVE_URL = "{{ route('backoffice.computations.debtors-reconciliation.save', ['computation' => $computation->id]) }}";
    const ADD_ROW_URL = "{{ route('backoffice.computations.debtors-reconciliation.add-row', ['computation' => $computation->id]) }}";
    // 🔥 Use a placeholder for rowId that we can replace in JavaScript
    const DELETE_ROW_URL = "{{ route('backoffice.computations.debtors-reconciliation.delete-row', ['computation' => $computation->id, 'rowId' => '__ROW_ID__']) }}";
    const FINALIZE_URL = "{{ route('backoffice.computations.debtors-reconciliation.finalize', ['computation' => $computation->id]) }}";
    const UPLOAD_SIG_URL = "{{ route('backoffice.computations.debtors-reconciliation.upload-signature', ['computation' => $computation->id]) }}";
    const CLEAR_SIG_URL = "{{ route('backoffice.computations.debtors-reconciliation.clear-signature', ['computation' => $computation->id]) }}";

    let debtors = @json($debtors);
    let saveTimeout = null;
    let isSaving = false;

    // ─── FORMATTING HELPERS ──────────────────────────────────────────────

    function fmt(n) {
        if (n === undefined || n === null || isNaN(n)) return '₹0';
        return '₹' + Math.round(n).toLocaleString('en-IN');
    }

    function fmtRaw(n) {
        if (n === undefined || n === null || isNaN(n)) return 0;
        return Math.round(n);
    }

    function parseNum(v) {
        if (typeof v === 'string') {
            v = v.replace(/[₹,\s]/g, '').trim();
            if (v === '' || v === '-') return 0;
            return parseFloat(v) || 0;
        }
        return v || 0;
    }

    // ─── ROW CHANGE HANDLER ─────────────────────────────────────────────

    function onRowChange(id) {
        const row = debtors.find(d => d.id === id);
        if (!row) return;

        const billAmount = parseNum(document.getElementById(`dbillAmount_${id}`).value);
        const amountReceived = parseNum(document.getElementById(`damountReceived_${id}`).value);
        const followUpDate = document.getElementById(`dfollowUp_${id}`).value;

        // Validation
        if (billAmount < 0) {
            document.getElementById(`dbillAmount_${id}`).classList.add('negative');
            showToast('Bill Amount cannot be negative.', 'warning');
            return;
        } else {
            document.getElementById(`dbillAmount_${id}`).classList.remove('negative');
        }

        if (amountReceived < 0) {
            document.getElementById(`damountReceived_${id}`).classList.add('negative');
            showToast('Amount Received cannot be negative.', 'warning');
            return;
        } else {
            document.getElementById(`damountReceived_${id}`).classList.remove('negative');
        }

        if (amountReceived > billAmount && billAmount > 0) {
            document.getElementById(`damountReceived_${id}`).classList.add('negative');
            showToast('Amount Received cannot exceed Bill Amount.', 'warning');
        } else {
            document.getElementById(`damountReceived_${id}`).classList.remove('negative');
        }

        // Update row data
        row.bill_amount = billAmount;
        row.amount_received = amountReceived;
        row.payment_followup_date = followUpDate;
        row.pan = document.getElementById(`dpan_${id}`).value;
        row.gstin = document.getElementById(`dgstin_${id}`).value;
        row.remarks = document.getElementById(`dremarks_${id}`).value;

        // Update outstanding
        const outstanding = billAmount - amountReceived;
        document.getElementById(`doutstanding_${id}`).value = fmtRaw(outstanding);
        document.getElementById(`doutstanding_${id}`).style.color = outstanding < 0 ? 'var(--red)' : '';

        updateTotals();
        autoSave();
    }

    // ─── UPDATE TOTALS ─────────────────────────────────────────────────

    function updateTotals() {
        let totalBill = 0,
            totalReceived = 0,
            totalOutstanding = 0;

        debtors.forEach((row) => {
            const outstanding = row.bill_amount - row.amount_received;
            totalBill += row.bill_amount;
            totalReceived += row.amount_received;
            totalOutstanding += outstanding;
        });

        document.getElementById('totBillAmount').value = fmtRaw(totalBill);
        document.getElementById('totAmountReceived').value = fmtRaw(totalReceived);
        document.getElementById('totOutstanding').value = fmtRaw(totalOutstanding);

        document.getElementById('cardTotalDebtors').textContent = debtors.length;
        document.getElementById('cardTotalBill').textContent = fmt(totalBill);
        document.getElementById('cardTotalReceived').textContent = fmt(totalReceived);
        document.getElementById('cardTotalOutstanding').textContent = fmt(totalOutstanding);
        document.getElementById('debtorCount').textContent = debtors.length + ' records';
    }

    // ─── ADD ROW ────────────────────────────────────────────────────────

    function addRow() {
        fetch(ADD_ROW_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                debtors.push(data.debtor);
                renderRows();
                showToast('New row added.', 'success');
            }
        })
        .catch(err => showToast('Error adding row.', 'error'));
    }

    // ─── DELETE ROW ────────────────────────────────────────────────────

    function deleteRow(id) {
        const debtor = debtors.find(d => d.id === id);
        if (!confirm(`Delete debtor "${debtor?.debtor_name || 'Unknown'}"?`)) return;

        // Replace placeholder with actual row ID
        const url = DELETE_ROW_URL.replace('__ROW_ID__', id);

        fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                debtors = debtors.filter(d => d.id !== id);
                renderRows();
                showToast('Row deleted.', 'info');
            }
        })
        .catch(err => showToast('Error deleting row.', 'error'));
    }

    // ─── DUPLICATE ROW ─────────────────────────────────────────────────

    function duplicateRow(id) {
        const original = debtors.find(d => d.id === id);
        if (!original) return;

        const newRow = {
            debtor_name: original.debtor_name + ' (copy)',
            pan: original.pan,
            gstin: original.gstin,
            bill_amount: original.bill_amount,
            amount_received: original.amount_received,
            payment_followup_date: original.payment_followup_date,
            remarks: original.remarks
        };

        fetch(ADD_ROW_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(newRow)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                debtors.push(data.debtor);
                renderRows();
                showToast('Row duplicated.', 'success');
            }
        })
        .catch(err => showToast('Error duplicating row.', 'error'));
    }

    // ─── RENDER ROWS ────────────────────────────────────────────────────

    function renderRows() {
        const tbody = document.getElementById('debtorsBodyRows');
        let html = '';

        debtors.forEach((row, idx) => {
            const outstanding = row.bill_amount - row.amount_received;
            html += `
                <tr data-debtor-id="${row.id}" data-name="${(row.debtor_name || '').toLowerCase()}">
                    <td style="text-align:center;font-weight:500;color:var(--text-muted);">${idx + 1}</td>
                    <td class="sticky-col" style="background:inherit;position:sticky;left:0;z-index:2;text-align:left;padding-left:6px;">
                        <span class="td-label">${row.debtor_name || 'New Debtor'}</span>
                    </td>
                    <td><input class="td-text uppercase" type="text" id="dpan_${row.id}" value="${row.pan || ''}" oninput="onRowChange(${row.id})" style="text-align:center;font-size:9px;" /></td>
                    <td><input class="td-text uppercase" type="text" id="dgstin_${row.id}" value="${row.gstin || ''}" oninput="onRowChange(${row.id})" style="text-align:center;font-size:9px;" /></td>
                    <td><input class="td-amt" type="text" id="dbillAmount_${row.id}" value="${fmtRaw(row.bill_amount)}" oninput="onRowChange(${row.id})" /></td>
                    <td><input class="td-date" type="date" id="dfollowUp_${row.id}" value="${row.payment_followup_date || ''}" oninput="onRowChange(${row.id})" /></td>
                    <td><input class="td-amt" type="text" id="damountReceived_${row.id}" value="${fmtRaw(row.amount_received)}" oninput="onRowChange(${row.id})" /></td>
                    <td><input class="td-amt readonly ${outstanding < 0 ? 'negative' : ''}" type="text" id="doutstanding_${row.id}" value="${fmtRaw(outstanding)}" readonly style="${outstanding < 0 ? 'color:var(--red);' : ''}" /></td>
                    <td><input class="td-text" type="text" id="dremarks_${row.id}" value="${row.remarks || ''}" oninput="onRowChange(${row.id})" style="text-align:left;font-size:9px;" /></td>
                    <td>
                        <div style="display:flex;gap:2px;justify-content:center;flex-wrap:wrap;">
                            <button class="btn-erp btn-ghost btn-sm" onclick="duplicateRow(${row.id})" style="font-size:8px;padding:1px 4px;" title="Duplicate"><i class="bi bi-files"></i></button>
                            <button class="btn-erp btn-ghost btn-sm" onclick="deleteRow(${row.id})" style="font-size:8px;padding:1px 4px;color:var(--red);" title="Delete"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });

        // Total row
        html += `
            <tr class="total-row">
                <td></td>
                <td class="sticky-col" style="background:#1a3a6c;position:sticky;left:0;z-index:2;text-align:left;padding-left:6px;"><span class="td-label" style="color:#fff;"><strong>TOTAL</strong></span></td>
                <td></td><td></td>
                <td><input class="td-amt total-cell" type="text" id="totBillAmount" value="0" readonly /></td>
                <td></td>
                <td><input class="td-amt total-cell" type="text" id="totAmountReceived" value="0" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totOutstanding" value="0" readonly /></td>
                <td></td><td></td>
            </tr>
        `;

        tbody.innerHTML = html;
        updateTotals();
    }

    // ─── FILTER TABLE ──────────────────────────────────────────────────

    function filterTable() {
        const q = document.getElementById('tableSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#debtorsBodyRows tr:not(.total-row)');

        rows.forEach((row) => {
            const name = row.dataset.name || '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    }

    // ─── TOGGLE SECTION ───────────────────────────────────────────────

    function toggleSection(id) {
        const body = document.getElementById(id);
        const arrow = document.getElementById('debtorsArrow');
        body.classList.toggle('collapsed');
        if (arrow) arrow.classList.toggle('open');
    }

    // ─── AUTO SAVE ─────────────────────────────────────────────────────

    function autoSave() {
        if (isSaving) return;
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            saveDraft();
        }, 800);
    }

    function saveDraft() {
        if (isSaving) return;
        isSaving = true;

        const data = debtors.map(row => ({
            id: row.id,
            debtor_name: row.debtor_name || 'New Debtor',
            pan: row.pan || '',
            gstin: row.gstin || '',
            bill_amount: row.bill_amount || 0,
            payment_followup_date: row.payment_followup_date || null,
            amount_received: row.amount_received || 0,
            remarks: row.remarks || ''
        }));

        fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ debtors: data })
        })
        .then(res => res.json())
        .then(data => {
            isSaving = false;
            if (data.success) {
                debtors = data.debtors || debtors;
                renderRows();
                showToast('Auto-saved.', 'success');
            }
        })
        .catch(() => {
            isSaving = false;
            showToast('Error saving data.', 'error');
        });
    }

    // ─── FINALIZE ──────────────────────────────────────────────────────

    function finalizeReport() {
        if (!confirm('Finalize this report? This will lock the data.')) return;

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
        .catch(err => showToast('Error finalizing report.', 'error'));
    }

    // ─── CA SIGNATURE ──────────────────────────────────────────────────

    document.getElementById('caSigUpload').addEventListener('change', function(e) {
        if (this.files && this.files.length > 0) {
            const file = this.files[0];
            const reader = new FileReader();
            reader.onload = function(ev) {
                const imgData = ev.target.result;
                document.getElementById('caSigDisplay').innerHTML =
                    `<img src="${imgData}" alt="CA Signature" style="max-height:38px;max-width:130px;border:1px solid #e0e7ef;border-radius:3px;padding:2px;background:#fff;" />`;
                showToast('Signature uploaded.', 'success');
            };
            reader.readAsDataURL(file);
        }
    });

    function clearCaSignature() {
        document.getElementById('caSigDisplay').innerHTML = `<div class="ft-sign-line"></div>`;
        document.getElementById('caSigUpload').value = '';
        showToast('Signature cleared.', 'info');
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
        renderRows();
        showToast('Debtors Reconciliation loaded.', 'success');
    });

    // Expose to global scope
    window.addRow = addRow;
    window.deleteRow = deleteRow;
    window.duplicateRow = duplicateRow;
    window.onRowChange = onRowChange;
    window.saveDraft = saveDraft;
    window.finalizeReport = finalizeReport;
    window.clearCaSignature = clearCaSignature;
    window.filterTable = filterTable;
    window.toggleSection = toggleSection;
    window.showToast = showToast;
</script>