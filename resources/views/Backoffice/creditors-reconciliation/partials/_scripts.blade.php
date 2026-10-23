<script>
    const COMPUTATION_ID = {{ $computation->id }};
    const CREDITORS_URL = "{{ route('backoffice.computations.creditors-reconciliation.data', ['computation' => $computation->id]) }}";
    const SAVE_URL = "{{ route('backoffice.computations.creditors-reconciliation.save', ['computation' => $computation->id]) }}";
    const ADD_ROW_URL = "{{ route('backoffice.computations.creditors-reconciliation.add-row', ['computation' => $computation->id]) }}";
    const DELETE_ROW_URL = "{{ route('backoffice.computations.creditors-reconciliation.delete-row', ['computation' => $computation->id, 'rowId' => '__ROW_ID__']) }}";
    const FINALIZE_URL = "{{ route('backoffice.computations.creditors-reconciliation.finalize', ['computation' => $computation->id]) }}";
    const UPLOAD_SIG_URL = "{{ route('backoffice.computations.creditors-reconciliation.upload-signature', ['computation' => $computation->id]) }}";
    const CLEAR_SIG_URL = "{{ route('backoffice.computations.creditors-reconciliation.clear-signature', ['computation' => $computation->id]) }}";

    let creditors = @json($creditors);
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

    function onCreditorChange(id) {
        const row = creditors.find(c => c.id === id);
        if (!row) return;

        const billAmount = parseNum(document.getElementById(`cbillAmount_${id}`).value);
        const amountPaid = parseNum(document.getElementById(`camountPaid_${id}`).value);
        const followUpDate = document.getElementById(`cfollowUp_${id}`).value;

        // Validation
        if (billAmount < 0) {
            document.getElementById(`cbillAmount_${id}`).classList.add('negative');
            showToast('Bill Amount cannot be negative.', 'warning');
            return;
        } else {
            document.getElementById(`cbillAmount_${id}`).classList.remove('negative');
        }

        if (amountPaid < 0) {
            document.getElementById(`camountPaid_${id}`).classList.add('negative');
            showToast('Amount Paid cannot be negative.', 'warning');
            return;
        } else {
            document.getElementById(`camountPaid_${id}`).classList.remove('negative');
        }

        if (amountPaid > billAmount && billAmount > 0) {
            document.getElementById(`camountPaid_${id}`).classList.add('negative');
            showToast('Amount Paid cannot exceed Bill Amount.', 'warning');
        } else {
            document.getElementById(`camountPaid_${id}`).classList.remove('negative');
        }

        row.bill_amount = billAmount;
        row.amount_paid = amountPaid;
        row.payment_followup_date = followUpDate;
        row.pan = document.getElementById(`cpan_${id}`).value;
        row.gstin = document.getElementById(`cgstin_${id}`).value;
        row.remarks = document.getElementById(`cremarks_${id}`).value;

        const outstanding = billAmount - amountPaid;
        document.getElementById(`coutstanding_${id}`).value = fmtRaw(outstanding);
        document.getElementById(`coutstanding_${id}`).style.color = outstanding < 0 ? 'var(--red)' : '';

        updateTotals();
        autoSave();
    }

    // ─── UPDATE TOTALS ─────────────────────────────────────────────────

    function updateTotals() {
        let totalBill = 0, totalPaid = 0, totalOutstanding = 0;

        creditors.forEach((row) => {
            const outstanding = row.bill_amount - row.amount_paid;
            totalBill += row.bill_amount;
            totalPaid += row.amount_paid;
            totalOutstanding += outstanding;
        });

        document.getElementById('totBillAmount').value = fmtRaw(totalBill);
        document.getElementById('totAmountPaid').value = fmtRaw(totalPaid);
        document.getElementById('totOutstanding').value = fmtRaw(totalOutstanding);

        document.getElementById('cardTotalCreditors').textContent = creditors.length;
        document.getElementById('cardTotalBill').textContent = fmt(totalBill);
        document.getElementById('cardTotalPaid').textContent = fmt(totalPaid);
        document.getElementById('cardTotalOutstanding').textContent = fmt(totalOutstanding);
        document.getElementById('creditorCount').textContent = creditors.length + ' records';
    }

    // ─── ADD ROW ────────────────────────────────────────────────────────

    function addCreditorRow() {
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
                creditors.push(data.creditor);
                renderRows();
                showToast('New row added.', 'success');
            }
        })
        .catch(err => showToast('Error adding row.', 'error'));
    }

    // ─── DELETE ROW ────────────────────────────────────────────────────

    function deleteCreditorRow(id) {
        const creditor = creditors.find(c => c.id === id);
        if (!confirm(`Delete creditor "${creditor?.creditor_name || 'Unknown'}"?`)) return;

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
                creditors = creditors.filter(c => c.id !== id);
                renderRows();
                showToast('Row deleted.', 'info');
            }
        })
        .catch(err => showToast('Error deleting row.', 'error'));
    }

    // ─── DUPLICATE ROW ─────────────────────────────────────────────────

    function duplicateCreditorRow(id) {
        const original = creditors.find(c => c.id === id);
        if (!original) return;

        const newRow = {
            creditor_name: original.creditor_name + ' (copy)',
            pan: original.pan,
            gstin: original.gstin,
            bill_amount: original.bill_amount,
            amount_paid: original.amount_paid,
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
                creditors.push(data.creditor);
                renderRows();
                showToast('Row duplicated.', 'success');
            }
        })
        .catch(err => showToast('Error duplicating row.', 'error'));
    }

    // ─── RENDER ROWS ────────────────────────────────────────────────────

    function renderRows() {
        const tbody = document.getElementById('creditorsBodyRows');
        let html = '';

        creditors.forEach((row, idx) => {
            const outstanding = row.bill_amount - row.amount_paid;
            html += `
                <tr data-creditor-id="${row.id}" data-name="${(row.creditor_name || '').toLowerCase()}">
                    <td style="text-align:center;font-weight:500;color:var(--text-muted);">${idx + 1}</td>
                    <td class="sticky-col" style="background:inherit;position:sticky;left:0;z-index:2;text-align:left;padding-left:6px;">
                        <span class="td-label">${row.creditor_name || 'New Creditor'}</span>
                    </td>
                    <td><input class="td-text uppercase" type="text" id="cpan_${row.id}" value="${row.pan || ''}" oninput="onCreditorChange(${row.id})" style="text-align:center;font-size:9px;" /></td>
                    <td><input class="td-text uppercase" type="text" id="cgstin_${row.id}" value="${row.gstin || ''}" oninput="onCreditorChange(${row.id})" style="text-align:center;font-size:9px;" /></td>
                    <td><input class="td-amt" type="text" id="cbillAmount_${row.id}" value="${fmtRaw(row.bill_amount)}" oninput="onCreditorChange(${row.id})" /></td>
                    <td><input class="td-date" type="date" id="cfollowUp_${row.id}" value="${row.payment_followup_date || ''}" oninput="onCreditorChange(${row.id})" /></td>
                    <td><input class="td-amt" type="text" id="camountPaid_${row.id}" value="${fmtRaw(row.amount_paid)}" oninput="onCreditorChange(${row.id})" /></td>
                    <td><input class="td-amt readonly ${outstanding < 0 ? 'negative' : ''}" type="text" id="coutstanding_${row.id}" value="${fmtRaw(outstanding)}" readonly style="${outstanding < 0 ? 'color:var(--red);' : ''}" /></td>
                    <td><input class="td-text" type="text" id="cremarks_${row.id}" value="${row.remarks || ''}" oninput="onCreditorChange(${row.id})" style="text-align:left;font-size:9px;" /></td>
                    <td>
                        <div style="display:flex;gap:2px;justify-content:center;flex-wrap:wrap;">
                            <button class="btn-erp btn-ghost btn-sm" onclick="duplicateCreditorRow(${row.id})" style="font-size:8px;padding:1px 4px;" title="Duplicate"><i class="bi bi-files"></i></button>
                            <button class="btn-erp btn-ghost btn-sm" onclick="deleteCreditorRow(${row.id})" style="font-size:8px;padding:1px 4px;color:var(--red);" title="Delete"><i class="bi bi-trash"></i></button>
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
                <td><input class="td-amt total-cell" type="text" id="totAmountPaid" value="0" readonly /></td>
                <td><input class="td-amt total-cell" type="text" id="totOutstanding" value="0" readonly /></td>
                <td></td><td></td>
            </tr>
        `;

        tbody.innerHTML = html;
        updateTotals();
    }

    // ─── FILTER TABLE ──────────────────────────────────────────────────

    function filterCreditors() {
        const q = document.getElementById('globalSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#creditorsBodyRows tr:not(.total-row)');

        rows.forEach((row) => {
            const name = row.dataset.name || '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    }

    // ─── TOGGLE SECTION ───────────────────────────────────────────────

    function toggleSection(id) {
        const body = document.getElementById(id);
        const arrow = document.getElementById('creditorsArrow');
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

        const data = creditors.map(row => ({
            id: row.id,
            creditor_name: row.creditor_name || 'New Creditor',
            pan: row.pan || '',
            gstin: row.gstin || '',
            bill_amount: row.bill_amount || 0,
            payment_followup_date: row.payment_followup_date || null,
            amount_paid: row.amount_paid || 0,
            remarks: row.remarks || ''
        }));

        fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ creditors: data })
        })
        .then(res => res.json())
        .then(data => {
            isSaving = false;
            if (data.success) {
                creditors = data.creditors || creditors;
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
        showToast('Creditors Reconciliation loaded.', 'success');
    });

    // Expose to global scope
    window.addCreditorRow = addCreditorRow;
    window.deleteCreditorRow = deleteCreditorRow;
    window.duplicateCreditorRow = duplicateCreditorRow;
    window.onCreditorChange = onCreditorChange;
    window.saveDraft = saveDraft;
    window.finalizeReport = finalizeReport;
    window.clearCaSignature = clearCaSignature;
    window.filterCreditors = filterCreditors;
    window.toggleSection = toggleSection;
    window.showToast = showToast;
</script>