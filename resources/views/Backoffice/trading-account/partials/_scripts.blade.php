<script>
    /* ================================================================
       TRADING ACCOUNT – COMPLETE ENGINE (ULTIMATE LABEL FIX)
       ================================================================ */

    // ─── CONFIG ──────────────────────────────────────────────────────
    const COMPUTATION_ID = {{ $computation->id }};
    const SAVE_URL = "{{ route('backoffice.computations.trading-account.store', ['computation' => $computation->id]) }}";
    const DELETE_ROW_URL = "{{ route('backoffice.computations.trading-account.row.delete', ['computation' => $computation->id, 'id' => '__ID__']) }}";

    let drCounter = 0;
    let crCounter = 0;
    let isSaving = false;
    let saveTimeout = null;

    // ─── HELPERS ──────────────────────────────────────────────────────

    /**
     * ✅ Strongly parse amount from any input format.
     * Removes all ₹, commas, spaces, and handles '-' or '--' as 0.
     */
    function parseAmt(id) {
        let el = document.getElementById(id);
        if (!el) return 0;
        let s = (el.innerText || el.textContent || '').replace(/[₹,\s]/g, '').trim();
        // Handle negative signs (allow either starting or ending negative)
        let neg = s.startsWith('-');
        if (neg) s = s.substring(1);
        else if (s.endsWith('-')) { neg = true; s = s.slice(0, -1); }
        
        if (s === '' || s === '-' || s === '--') return 0;
        let v = parseFloat(s) || 0;
        return neg ? -v : v;
    }

    function fmtINR(n) {
        if (isNaN(n) || n === null || n === undefined) return '-';
        let neg = n < 0;
        n = Math.abs(n);
        if (n === 0) return '-';
        let s = n.toFixed(0);
        let last3 = s.slice(-3);
        let rest = s.slice(0, s.length - 3);
        if (rest) last3 = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + last3;
        return (neg ? '(-) ' : '') + last3;
    }

    function setCell(id, val) {
        let el = document.getElementById(id);
        if (el) el.innerText = (val === 0 || val === null || val === undefined) ? '-' : fmtINR(val);
    }

    function setCellRaw(id, txt) {
        let el = document.getElementById(id);
        if (el) el.innerText = txt;
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

    // ─── ADJUSTMENT TOGGLE ──────────────────────────────────────────

    function toggleAdjustment(mode) {
        const prRow = document.getElementById('row-purchase-return');
        const dnRow = document.getElementById('row-debit-note');
        const prInput = document.getElementById('purchase-return-amount');
        const dnInput = document.getElementById('debit-note-amount');

        if (mode === 'purchaseReturn') {
            if (dnRow) dnRow.classList.add('hidden-row');
            if (prRow) prRow.classList.remove('hidden-row');
            if (prInput) {
                const val = window._storedPurchaseReturn || 0;
                prInput.innerText = val !== 0 ? fmtINR(val) : '-';
            }
            if (dnInput) dnInput.innerText = '0';
        } else {
            if (prRow) prRow.classList.add('hidden-row');
            if (dnRow) dnRow.classList.remove('hidden-row');
            if (dnInput) {
                const val = window._storedDebitNote || 0;
                dnInput.innerText = val !== 0 ? fmtINR(val) : '-';
            }
            if (prInput) prInput.innerText = '0';
        }
        calc();
    }

    // ─── ADD CUSTOM ROWS ────────────────────────────────────────────

    function addRowDebit() {
        drCounter++;
        const id = 'dr_custom_' + drCounter;
        const gpRow = document.getElementById('gpRow');
        if (!gpRow) return;

        const newRow = document.createElement('tr');
        newRow.className = 'dr custom';
        newRow.id = 'row_dr_' + id;
        newRow.dataset.side = 'dr';
        newRow.dataset.rowId = id;
        newRow.innerHTML = `
            <td>
                <div class="td-part">
                    <div class="main-line">
                        <span class="bullet">♦</span>
                        <input type="text" class="part-input" id="dr_part_${id}" value="New Debit Item" oninput="onRowNameChange('dr', '${id}')" style="border:none;background:transparent;outline:none;font-family:inherit;font-size:inherit;width:100%;" />
                        <span class="delete-btn" onclick="deleteRow('${id}', 'dr')" title="Remove row"><i class="bi bi-x-circle-fill"></i></span>
                    </div>
                    <div class="source-text">(Source: Manual Entry)</div>
                </div>
            </td>
            <td class="td-rs">₹</td>
            <td class="td-amt editable" contenteditable="true" id="dr_amt_${id}" oninput="onRowAmountChange('dr', '${id}')">-</td>
            <td class="td-rs"></td>
            <td class="td-amt blank" style="border-right:2px solid #4a6fa5;"></td>
            <td colspan="5"></td>
        `;
        gpRow.parentNode.insertBefore(newRow, gpRow);
        calc();
        autoSave();
        showToast('Debit row added.', 'success');
    }

    function addRowCredit() {
        crCounter++;
        const id = 'cr_custom_' + crCounter;
        const closingRow = document.querySelector('tr:has(#closing-stock)');
        if (!closingRow) return;

        const newRow = document.createElement('tr');
        newRow.className = 'dr custom';
        newRow.id = 'row_cr_' + id;
        newRow.dataset.side = 'cr';
        newRow.dataset.rowId = id;
        newRow.innerHTML = `
            <td colspan="5" style="border-right:2px solid #4a6fa5;"></td>
            <td>
                <div class="td-part">
                    <div class="main-line">
                        <span class="bullet">♦</span>
                        <input type="text" class="part-input" id="cr_part_${id}" value="New Credit Item" oninput="onRowNameChange('cr', '${id}')" style="border:none;background:transparent;outline:none;font-family:inherit;font-size:inherit;width:100%;" />
                        <span class="delete-btn" onclick="deleteRow('${id}', 'cr')" title="Remove row"><i class="bi bi-x-circle-fill"></i></span>
                    </div>
                    <div class="source-text">(Source: Manual Entry)</div>
                </div>
            </td>
            <td class="td-rs">₹</td>
            <td class="td-amt editable" contenteditable="true" id="cr_amt_${id}" oninput="onRowAmountChange('cr', '${id}')">-</td>
            <td class="td-rs">₹</td>
            <td class="td-amt blank"></td>
        `;
        closingRow.parentNode.insertBefore(newRow, closingRow);
        calc();
        autoSave();
        showToast('Credit row added.', 'success');
    }

    function onRowNameChange(side, id) {
        const input = document.getElementById(`${side}_part_${id}`);
        if (input) {
            const row = document.getElementById(`row_${side}_${id}`);
            if (row) row.dataset.name = input.value;
        }
    }

    function onRowAmountChange(side, id) {
        const el = document.getElementById(`${side}_amt_${id}`);
        if (!el) return;
        calc();
        autoSave();
    }

    function deleteRow(id, side) {
        const rowId = `row_${side}_${id}`;
        const row = document.getElementById(rowId);
        if (!row) return;

        const dbId = row.dataset.dbId;
        if (dbId) {
            if (!confirm('Delete this row from database?')) return;
            const url = DELETE_ROW_URL.replace('__ID__', dbId);
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    calc();
                    showToast('Row deleted successfully.', 'success');
                } else {
                    showToast('Error deleting row: ' + data.message, 'error');
                }
            })
            .catch(err => showToast('Error deleting row.', 'error'));
        } else {
            row.remove();
            calc();
            showToast('Row removed.', 'info');
        }
    }

    // ─── MAIN CALCULATION ──────────────────────────────────────────

    function calc() {
        // ─── DEBIT SIDE ──────────────────────────────────────────────
        const openingStock = parseAmt('opening-stock');
        const regPurch = parseAmt('register-purchases');
        const unregPurch = parseAmt('unregister-purchases');
        const purchaseReturn = parseAmt('purchase-return-amount');
        const debitNote = parseAmt('debit-note-amount');
        const unloading = parseAmt('unloading-charges');
        const wages = parseAmt('wages');
        const fuel = parseAmt('fuel-expenses');
        const carriage = parseAmt('carriage-inward');
        const platform = parseAmt('sale-platform');

        // ─── CREDIT SIDE ─────────────────────────────────────────────
        const salesGST = parseAmt('sales-as-per-gst');
        const salesNoGST = parseAmt('sales-without-gst');
        const preDiscount = parseAmt('pre-sale-discount');
        const salesReturn = parseAmt('sales-return');
        const postDiscount = parseAmt('post-sale-discount');
        const creditNote = parseAmt('credit-note');
        const closingStock = parseAmt('closing-stock');

        // ─── CUSTOM ROWS ─────────────────────────────────────────────
        let customDebitTotal = 0;
        document.querySelectorAll('#tBody tr.custom[data-side="dr"] .td-amt.editable').forEach(el => {
            const val = parseAmt(el.id);
            customDebitTotal += val;
        });

        let customCreditTotal = 0;
        document.querySelectorAll('#tBody tr.custom[data-side="cr"] .td-amt.editable').forEach(el => {
            const val = parseAmt(el.id);
            customCreditTotal += val;
        });

        // ─── CALCULATIONS ────────────────────────────────────────────
        const netPurchases = regPurch + unregPurch - purchaseReturn + debitNote + customDebitTotal;
        const directExpenses = unloading + wages + fuel + carriage + platform;
        const grossSales = salesGST + salesNoGST + preDiscount + customCreditTotal;
        const netSales = grossSales - salesReturn - creditNote - postDiscount;
        const cogs = openingStock + netPurchases + directExpenses - closingStock;
        const grossProfit = netSales - cogs;

        let debitTotal = openingStock + netPurchases + directExpenses;
        let creditTotal = netSales + closingStock;
        let gpOnDebit = true;
        let gpValue = 0;
        let lossValue = 0;

        if (grossProfit >= 0) {
            gpOnDebit = true;
            gpValue = grossProfit;
            debitTotal += gpValue;
        } else {
            gpOnDebit = false;
            lossValue = -grossProfit;
            creditTotal += lossValue;
        }

        let gpRatio = 0;
        if (netSales > 0) gpRatio = (grossProfit / netSales) * 100;

        const balanced = Math.abs(debitTotal - creditTotal) < 0.01;

        // ─── UPDATE UI – DEBIT SUBTOTALS ─────────────────────────────
        setCell('opening-stock-total', openingStock);
        setCell('net-purchase', netPurchases);
        setCell('direct-expense', directExpenses);

        // ─── UPDATE UI – CREDIT SUBTOTALS ────────────────────────────
        setCell('sales-gst-total', salesGST);
        setCell('sales-no-gst-total', salesNoGST);
        setCell('pre-sale-discount-total', preDiscount);
        setCell('sales-return-total', salesReturn);
        setCell('post-sale-discount-total', postDiscount);
        setCell('credit-note-total', creditNote);
        setCell('closing-stock-total', closingStock);

        // ─── GROSS SALES & NET SALES ─────────────────────────────────
        setCellRaw('gross-sales-label', '₹ ' + fmtINR(grossSales));
        setCellRaw('net-sales-label', '₹ ' + fmtINR(netSales));

        // ─── GP / LOSS ROW ────────────────────────────────────────────
        const gpLabel = document.getElementById('gp-label');
        const gpAmt1 = document.getElementById('gp-amt1');
        const gpRow = document.getElementById('gpRow');

        if (gpOnDebit) {
            gpLabel.innerText = 'GROSS PROFIT';
            gpAmt1.innerText = fmtINR(gpValue);
            gpAmt1.style.color = '#1b5e20';
            gpRow.className = 'gp-row';
            gpLabel.style.color = '#1b5e20';
        } else {
            gpLabel.innerText = 'GROSS LOSS';
            gpAmt1.innerText = fmtINR(lossValue);
            gpAmt1.style.color = '#b71c1c';
            gpRow.className = 'gp-row gp-loss';
            gpLabel.style.color = '#b71c1c';
        }

        // ─── GRAND TOTALS ─────────────────────────────────────────────
        setCell('dr-grand-t1', debitTotal);
        setCell('dr-grand-t2', debitTotal);
        setCell('cr-grand-t1', creditTotal);
        setCell('cr-grand-t2', creditTotal);

        // ─── RIGHT PANEL ──────────────────────────────────────────────
        updateRightPanel(grossProfit, netPurchases, directExpenses, closingStock, openingStock, gpRatio, netSales);

        // ─── BALANCE CHECK ────────────────────────────────────────────
        if (!balanced && window._balanceWarned !== true) {
            window._balanceWarned = true;
            setTimeout(() => { window._balanceWarned = false; }, 3000);
            showToast('⚠️ Debit & Credit totals do not match. Check entries.', 'warning');
        }

        window._calcResult = { netPurchases, directExpenses, grossSales, netSales, grossProfit, gpRatio, debitTotal, creditTotal, balanced };
        return window._calcResult;
    }

    // ─── RIGHT PANEL UPDATE ─────────────────────────────────────────

    function updateRightPanel(grossProfit, netPurchases, directExpenses, closingStock, openingStock, gpRatio, netSales) {
        const rpGp = document.getElementById('rp-gp');
        const rpPur = document.getElementById('rp-pur');
        const rpTradexp = document.getElementById('rp-tradexp');
        const rpCs = document.getElementById('rp-cs');
        const rpOs = document.getElementById('rp-os');
        const rpGpr = document.getElementById('rp-gpr');
        const rpGpRatio = document.getElementById('rp-gp-ratio');

        if (rpGp) rpGp.innerText = '₹ ' + fmtINR(grossProfit);
        if (rpPur) rpPur.innerText = '₹ ' + fmtINR(netPurchases);
        if (rpTradexp) rpTradexp.innerText = fmtINR(directExpenses);
        if (rpCs) rpCs.innerText = '₹ ' + fmtINR(closingStock);
        if (rpOs) rpOs.innerText = '₹ ' + fmtINR(openingStock);

        if (rpGpr && netSales > 0) {
            rpGpr.innerText = gpRatio.toFixed(2) + '%';
        } else if (rpGpr) {
            rpGpr.innerText = '##DIV/0!';
        }

        if (rpGpRatio && netSales > 0) {
            rpGpRatio.innerText = gpRatio.toFixed(2) + '%';
        } else if (rpGpRatio) {
            rpGpRatio.innerText = '0.00%';
        }
    }

    // ─── FINALIZE ────────────────────────────────────────────────────

    function finalizeReport() {
        if (confirm('Finalize Trading Account for FY 2024–25? This cannot be undone.')) {
            document.getElementById('wmText').style.display = 'none';
            const statusEl = document.getElementById('rp-status');
            if (statusEl) {
                statusEl.innerText = 'FINALIZED';
                statusEl.style.color = '#1b5e20';
            }
            showToast('Trading Account Finalized!', 'success');
            saveToDatabase(true);
        }
    }

    // ─── SAVE DRAFT ──────────────────────────────────────────────────

    function saveDraft() {
        showToast('Saving...', 'info');
        saveToDatabase(true);
    }

    function autoSave(showMessage = false) {
        if (isSaving) return;
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            saveToDatabase(showMessage);
        }, 500);
    }

    // ================================================================
    // 🔥 ULTIMATE FIX: Directly remove the `♦` symbol from the string
    // ================================================================
    function saveToDatabase(showMessage = false) {
        if (isSaving) return;
        isSaving = true;

        // ─── COLLECT DEBIT ROWS ──────────────────────────────────────
        const debitRows = [];
        document.querySelectorAll('#tBody tr[data-side="dr"]:not(.sub-row):not(.gp-row):not(.gt-row):not(.sec-head)').forEach(row => {
            let label = '';
            
            // ✅ ULTIMATE FIX: Grab the main line and strip the diamond symbol entirely
            const mainLine = row.querySelector('.td-part .main-line');
            if (mainLine) {
                let rawText = mainLine.innerText || mainLine.textContent || '';
                // 🛡️ REMOVE the black diamond symbol (♦) completely
                label = rawText.replace(/[♦]/g, '').trim();
            } else {
                // Fallback for custom rows
                const input = row.querySelector('.part-input');
                if (input) label = input.value.trim();
            }

            let amount = 0;
            const amtEl = row.querySelector('.td-amt.editable');
            if (amtEl) {
                amount = parseAmt(amtEl.id);
            } else {
                const blankAmt = row.querySelector('.td-amt:not(.blank)');
                if (blankAmt) amount = parseAmt(blankAmt.id);
            }

            const isSystem = row.classList.contains('system');
            const isAdjustment = row.id === 'row-purchase-return' || row.id === 'row-debit-note';
            const dbId = row.dataset.dbId;

            debitRows.push({
                id: dbId || null,
                particular_name: label,
                amount: amount,
                row_type: isSystem || isAdjustment ? 'system' : 'custom',
                side: 'dr'
            });
        });

        // ─── COLLECT CREDIT ROWS ─────────────────────────────────────
        const creditRows = [];
        document.querySelectorAll('#tBody tr[data-side="cr"]:not(.sub-row):not(.gp-row):not(.gt-row):not(.sec-head)').forEach(row => {
            let label = '';
            
            // ✅ ULTIMATE FIX: Same logic for Credit side
            const mainLine = row.querySelector('.td-part .main-line');
            if (mainLine) {
                let rawText = mainLine.innerText || mainLine.textContent || '';
                label = rawText.replace(/[♦]/g, '').trim();
            } else {
                const input = row.querySelector('.part-input');
                if (input) label = input.value.trim();
            }

            let amount = 0;
            const amtEl = row.querySelector('.td-amt.editable');
            if (amtEl) {
                amount = parseAmt(amtEl.id);
            } else {
                const blankAmt = row.querySelector('.td-amt:not(.blank)');
                if (blankAmt) amount = parseAmt(blankAmt.id);
            }

            const isSystem = row.classList.contains('system');
            const dbId = row.dataset.dbId;

            creditRows.push({
                id: dbId || null,
                particular_name: label,
                amount: amount,
                row_type: isSystem ? 'system' : 'custom',
                side: 'cr'
            });
        });

        const combinedRows = [...debitRows, ...creditRows];

        const data = {
            rows: combinedRows,
            adjustment_type: document.getElementById('adjustmentType').value
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
                if (data.rows) {
                    const allRows = document.querySelectorAll('#tBody tr[data-side="dr"]:not(.sub-row):not(.gp-row):not(.gt-row):not(.sec-head), #tBody tr[data-side="cr"]:not(.sub-row):not(.gp-row):not(.gt-row):not(.sec-head)');
                    allRows.forEach((row, idx) => {
                        if (data.rows[idx] && data.rows[idx].id) {
                            row.dataset.dbId = data.rows[idx].id;
                        }
                    });
                }
                
                if (showMessage) {
                    showToast('✓ Changes saved successfully', 'success');
                }
            } else {
                showToast('❌ Save failed: ' + (data.error || 'Unknown error'), 'error');
            }
        })
        .catch(err => {
            isSaving = false;
            showToast('❌ Error saving: ' + err.message, 'error');
            console.error('Save error:', err);
        });
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

    // ─── INIT ──────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function() {
        const adjType = document.getElementById('adjustmentType').value;
        toggleAdjustment(adjType);
        
        setTimeout(calc, 100);

        document.querySelectorAll('.td-amt.editable, .part-input').forEach(el => {
            el.addEventListener('input', function() {
                if (this.classList.contains('part-input')) return;
            });
            el.addEventListener('blur', function() {
                calc();
                autoSave();
            });
        });

        document.getElementById('adjustmentType').addEventListener('change', function() {
            toggleAdjustment(this.value);
        });

        showToast('Trading Account loaded.', 'success');
    });

    // ─── EXPOSE TO GLOBAL ───────────────────────────────────────────

    window.addRowDebit = addRowDebit;
    window.addRowCredit = addRowCredit;
    window.deleteRow = deleteRow;
    window.onRowNameChange = onRowNameChange;
    window.onRowAmountChange = onRowAmountChange;
    window.calc = calc;
    window.saveDraft = saveDraft;
    window.finalizeReport = finalizeReport;
    window.clearCaSignature = clearCaSignature;
    window.showToast = showToast;
    window.toggleAdjustment = toggleAdjustment;
    window.autoSave = autoSave;
    window.saveToDatabase = saveToDatabase;
</script>