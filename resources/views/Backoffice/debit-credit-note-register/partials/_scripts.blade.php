<script>
    // ─── GLOBALS ──────────────────────────────────────────────────
    var MONTHS = ['Apr-23','May-23','Jun-23','Jul-23','Aug-23','Sep-23','Oct-23','Nov-23','Dec-23','Jan-24','Feb-24','Mar-24'];
    var GST_RATES = [0, 5, 12, 18, 28, 40];
    var currentTab = 'debit';
    var registerData = { debit: null, credit: null };
    var customRows = { debit: [], credit: [] };
    var currentMode = 'manual';
    var currentRegisterId = null;

    // ─── STUB FOR submitReport ──────────────────────────────────
    function submitReport() {
        showToast('Submit functionality is not implemented yet.', 'info');
    }

    // ─── HELPERS ──────────────────────────────────────────────────
    function fmt(n) {
        if (n === undefined || n === null || isNaN(n)) return '';
        return n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function parseVal(v) {
        if (typeof v === 'string') { v = v.replace(/[₹,\s]/g, '').trim(); if (v === '') return 0; return parseFloat(v) || 0; }
        return v || 0;
    }
    function setSpan(id, val) {
        var el = document.getElementById(id);
        if (el) { el.innerText = (val === 0 || val === null || val === undefined) ? '-' : fmt(val); }
    }
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }
    function getComputationId() {
        return document.getElementById('computationId')?.value || '';
    }
    function getBaseUrl() {
        return '/backoffice/computations/' + getComputationId() + '/debit-credit-registers';
    }

    // ─── TOAST ────────────────────────────────────────────────────
    function showToast(msg, type) {
        if (typeof type === 'undefined') type = 'success';
        var container = document.getElementById('toastContainer');
        if (!container) return;
        var t = document.createElement('div');
        var cls = type === 'success' ? 'success' : type === 'info' ? 'info' : type === 'warning' ? 'warning' : 'error';
        t.className = 'toast-erp ' + cls;
        var icon = type === 'success' ? 'check-circle-fill' : type === 'info' ? 'info-circle-fill' : type === 'warning' ? 'exclamation-triangle-fill' : 'exclamation-triangle-fill';
        t.innerHTML = '<i class="bi bi-' + icon + '"></i> ' + msg;
        container.appendChild(t);
        setTimeout(function() { t.remove(); }, 2800);
    }

    // ─── API CALLS ────────────────────────────────────────────────
    function apiRequest(method, url, data) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open(method, url, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', getCsrfToken());
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        resolve(JSON.parse(xhr.responseText));
                    } catch (e) {
                        reject({ error: 'Invalid JSON response', raw: xhr.responseText });
                    }
                } else {
                    try {
                        var err = JSON.parse(xhr.responseText);
                        reject(err);
                    } catch (e) {
                        reject({ error: 'Server error', status: xhr.status, raw: xhr.responseText });
                    }
                }
            };
            xhr.onerror = function() { reject({ error: 'Network error' }); };
            xhr.send(data ? JSON.stringify(data) : null);
        });
    }

    // ─── LOAD / CREATE REGISTER ──────────────────────────────────
    function loadOrCreateRegister(tab) {
        var url = getBaseUrl() + '/check/' + tab;
        apiRequest('GET', url)
            .then(function(data) {
                if (data.success && data.register) {
                    registerData[tab] = data.register;
                    currentRegisterId = data.register.id;
                    var hidden = document.getElementById('registerId');
                    if (hidden) hidden.value = data.register.id;
                    console.log('Loaded register ID:', data.register.id);
                    populateFooter(data.register);
                    renderTable(tab);
                } else {
                    createRegister(tab);
                }
            })
            .catch(function(err) {
                console.warn('Failed to load register, creating new one.', err);
                createRegister(tab);
            });
    }

    function createRegister(tab) {
        var url = getBaseUrl();
        var payload = { note_type: tab, gst_type: 'intra', calculation_mode: 'manual' };
        apiRequest('POST', url, payload)
            .then(function(data) {
                if (data.success) {
                    registerData[tab] = data.register;
                    currentRegisterId = data.register.id;
                    var hidden = document.getElementById('registerId');
                    if (hidden) hidden.value = data.register.id;
                    console.log('Created register ID:', data.register.id);
                    populateFooter(data.register);
                    renderTable(tab);
                    showToast('Register created!', 'success');
                } else {
                    showToast('Failed to create register', 'error');
                }
            })
            .catch(function() {
                showToast('Error creating register', 'error');
            });
    }

    function populateFooter(register) {
        if (!register) return;
        document.getElementById('footerPreparedBy').value = register.prepared_by || 'SharpEdge India';
        document.getElementById('footerCA').value = register.chartered_accountant || '';
        document.getElementById('footerMembership').value = register.membership_number || '';
        document.getElementById('footerPlace').value = register.place || '';
        document.getElementById('footerDate').value = register.signing_date || '';
        document.getElementById('itrAck').value = register.itr_acknowledgement_no || '';
        document.getElementById('auditAck').value = register.audit_acknowledgement_no || '';
        document.getElementById('ie10Ack').value = register.ie10_acknowledgement_no || '';
        document.getElementById('udin').value = register.udin || '';
        document.getElementById('caNameDisplay').innerText = register.chartered_accountant || 'Ankit Hansaria';
        document.getElementById('caMemDisplay').innerText = register.membership_number || '301586';
        document.getElementById('wmText').innerText = register.status === 'finalized' ? 'FINAL' : 'DRAFT';
    }

    // ─── RENDER TABLE ─────────────────────────────────────────────
    function renderTable(tab) {
        var register = registerData[tab];
        if (!register) { showToast('No register data', 'warning'); return; }
        var rows = register.rows || [];
        var isDebit = (tab === 'debit');
        var tbody = document.getElementById(isDebit ? 'debitBody' : 'creditBody');
        var prefix = isDebit ? 'd' : 'c';
        var custom = customRows[tab] || [];
        var rowMap = {};
        rows.forEach(function(r) { rowMap[r.row_label] = r; });

        console.log('rowMap:', rowMap);

        var html = '';

        MONTHS.forEach(function(month, idx) {
            var existing = rowMap[month];
            console.log('Month:', month, 'Existing:', existing, 'Row ID:', existing ? existing.id : null);

            var tax = existing ? parseVal(existing.taxable_value) : 0;
            var igst = existing ? parseVal(existing.igst) : 0;
            var cgst = existing ? parseVal(existing.cgst) : 0;
            var sgst = existing ? parseVal(existing.sgst) : 0;
            var invoice = existing ? parseVal(existing.invoice_value) : (tax + igst + cgst + sgst);
            var rem = existing ? (existing.remarks || '') : '';
            var rowId = existing ? existing.id : null;
            var hasBreakup = existing ? existing.has_breakup : false;

            html += '<tr class="month-row" data-row-id="' + rowId + '" data-month="' + month + '">';
            html += '<td class="td-month">' + month + '</td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'tax_' + idx + '" value="' + (tax === 0 ? '' : fmt(tax)) + '" oninput="onRowInput(\'' + prefix + '\', ' + idx + ')" onfocus="this.select()" /></td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'igst_' + idx + '" value="' + (igst === 0 ? '' : fmt(igst)) + '" oninput="onRowInput(\'' + prefix + '\', ' + idx + ')" onfocus="this.select()" /></td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'cgst_' + idx + '" value="' + (cgst === 0 ? '' : fmt(cgst)) + '" oninput="onRowInput(\'' + prefix + '\', ' + idx + ')" onfocus="this.select()" /></td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'sgst_' + idx + '" value="' + (sgst === 0 ? '' : fmt(sgst)) + '" oninput="onRowInput(\'' + prefix + '\', ' + idx + ')" onfocus="this.select()" /></td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'inv_' + idx + '" value="' + (invoice === 0 ? '' : fmt(invoice)) + '" readonly style="background:#f4f7fc;cursor:default;" /></td>';
            html += '<td class="td-remark"><input type="text" id="' + prefix + 'rem_' + idx + '" value="' + rem + '" placeholder="—" /></td>';
            html += '<td class="td-breakup">';
            if (rowId) {
                html += '<button class="btn-breakup" onclick="openBreakupModal(' + rowId + ', \'' + month + '\', ' + tax + ')"><i class="bi bi-list-ul"></i> Breakup';
                if (hasBreakup) html += ' <span class="badge bg-success" style="font-size:8px;padding:1px 4px;">✓</span>';
                html += '</button>';
            } else {
                html += '<button class="btn-breakup" disabled style="opacity:0.4;">No Row</button>';
            }
            html += '</td>';
            html += '<td class="td-actions">';
            if (rowId) {
                html += '<button class="action-btn del-btn" onclick="deleteRow(' + rowId + ')"><i class="bi bi-x-circle"></i></button>';
            } else {
                html += '<button class="action-btn del-btn disabled" disabled><i class="bi bi-x-circle"></i></button>';
            }
            html += '</td>';
            html += '</tr>';
        });

        // Custom rows
        custom.forEach(function(row, idx) {
            var cid = 'custom_' + idx;
            var taxVal = row.taxable || 0;
            var igstVal = row.igst || 0;
            var cgstVal = row.cgst || 0;
            var sgstVal = row.sgst || 0;
            var invVal = taxVal + igstVal + cgstVal + sgstVal;
            var monthName = row.month || 'Custom';

            html += '<tr class="month-row" id="row_' + prefix + '_' + cid + '">';
            html += '<td class="td-month"><input type="text" value="' + monthName + '" placeholder="Month" style="border:none;background:transparent;outline:none;font-weight:600;width:100%;font-size:11px;color:var(--text-dark);" onfocus="this.select()" onchange="updateCustomMonth(\'' + prefix + '\', ' + idx + ', this.value)" /></td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'tax_' + cid + '" value="' + (taxVal === 0 ? '' : fmt(taxVal)) + '" oninput="onRowInput(\'' + prefix + '\', \'' + cid + '\')" onfocus="this.select()" /></td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'igst_' + cid + '" value="' + (igstVal === 0 ? '' : fmt(igstVal)) + '" oninput="onRowInput(\'' + prefix + '\', \'' + cid + '\')" onfocus="this.select()" /></td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'cgst_' + cid + '" value="' + (cgstVal === 0 ? '' : fmt(cgstVal)) + '" oninput="onRowInput(\'' + prefix + '\', \'' + cid + '\')" onfocus="this.select()" /></td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'sgst_' + cid + '" value="' + (sgstVal === 0 ? '' : fmt(sgstVal)) + '" oninput="onRowInput(\'' + prefix + '\', \'' + cid + '\')" onfocus="this.select()" /></td>';
            html += '<td><input class="td-amt" type="text" id="' + prefix + 'inv_' + cid + '" value="' + (invVal === 0 ? '' : fmt(invVal)) + '" readonly style="background:#f4f7fc;cursor:default;" /></td>';
            html += '<td class="td-remark"><input type="text" id="' + prefix + 'rem_' + cid + '" value="' + (row.remark || '') + '" placeholder="—" /></td>';
            html += '<td class="td-breakup"><button class="btn-breakup disabled" disabled>—</button></td>';
            html += '<td class="td-actions"><button class="action-btn del-btn" onclick="deleteCustomRow(\'' + prefix + '\', ' + idx + ')"><i class="bi bi-x-circle"></i></button></td>';
            html += '</tr>';
        });

        // Total row
        html += '<tr class="total-row" id="totalRow_' + prefix + '">';
        html += '<td>TOTAL</td>';
        html += '<td><span class="td-amt total" id="' + prefix + 'tot_taxable">-</span></td>';
        html += '<td><span class="td-amt total" id="' + prefix + 'tot_igst">-</span></td>';
        html += '<td><span class="td-amt total" id="' + prefix + 'tot_cgst">-</span></td>';
        html += '<td><span class="td-amt total" id="' + prefix + 'tot_sgst">-</span></td>';
        html += '<td><span class="td-amt total" id="' + prefix + 'tot_inv">-</span></td>';
        html += '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
        html += '</tr>';

        tbody.innerHTML = html;
        calcTotal(prefix);
    }

    // ─── CALC TOTAL ──────────────────────────────────────────────
    function calcTotal(prefix) {
        var ids = { taxable: [], igst: [], cgst: [], sgst: [], inv: [] };
        var tbody = document.getElementById(prefix === 'd' ? 'debitBody' : 'creditBody');
        if (!tbody) return;
        var inputs = tbody.querySelectorAll('input.td-amt');
        for (var i = 0; i < inputs.length; i++) {
            var id = inputs[i].id;
            if (!id) continue;
            if (id.startsWith(prefix + 'tax_')) ids.taxable.push(id);
            else if (id.startsWith(prefix + 'igst_')) ids.igst.push(id);
            else if (id.startsWith(prefix + 'cgst_')) ids.cgst.push(id);
            else if (id.startsWith(prefix + 'sgst_')) ids.sgst.push(id);
            else if (id.startsWith(prefix + 'inv_')) ids.inv.push(id);
        }
        var sum = function(arr) { var total = 0; for (var j = 0; j < arr.length; j++) { var el = document.getElementById(arr[j]); if (el) total += parseVal(el.value || '0'); } return total; };
        setSpan(prefix + 'tot_taxable', sum(ids.taxable));
        setSpan(prefix + 'tot_igst', sum(ids.igst));
        setSpan(prefix + 'tot_cgst', sum(ids.cgst));
        setSpan(prefix + 'tot_sgst', sum(ids.sgst));
        setSpan(prefix + 'tot_inv', sum(ids.inv));
    }

    // ─── ROW INPUT ────────────────────────────────────────────────
function onRowInput(prefix, key) {

    var igstEl = document.getElementById(prefix + 'igst_' + key);
    var cgstEl = document.getElementById(prefix + 'cgst_' + key);
    var sgstEl = document.getElementById(prefix + 'sgst_' + key);
    var taxEl  = document.getElementById(prefix + 'tax_' + key);
    var invEl  = document.getElementById(prefix + 'inv_' + key);

    if (!igstEl || !cgstEl || !sgstEl || !taxEl || !invEl) {
        return;
    }

    var gstType = document.getElementById('gstType').value;

    var taxVal  = parseVal(taxEl.value);
    var igstVal = parseVal(igstEl.value);
    var cgstVal = parseVal(cgstEl.value);
    var sgstVal = parseVal(sgstEl.value);

    // Intra State
    if (gstType === 'intra') {

        // Whatever user types in CGST
        // automatically goes to SGST
        sgstVal = cgstVal;

        sgstEl.value = sgstVal === 0 ? '' : fmt(sgstVal);

        // IGST must be zero
        igstVal = 0;
        igstEl.value = '';

    }

    // Inter State
    else {

        // User types IGST only
        cgstVal = 0;
        sgstVal = 0;

        cgstEl.value = '';
        sgstEl.value = '';

    }

    var invoice = taxVal + igstVal + cgstVal + sgstVal;

    invEl.value = invoice === 0 ? '' : fmt(invoice);

    calcTotal(prefix);
}

    // ─── SWITCH TAB ──────────────────────────────────────────────
    function switchTab(tab) {
        currentTab = tab;
        var label = tab === 'debit' ? 'DEBIT NOTE REGISTER' : 'CREDIT NOTE REGISTER';
        document.getElementById('reportTypeDisplay').innerText = label;

        document.querySelectorAll('#registerTabs .nav-link').forEach(function(el) { el.classList.remove('active'); });
        document.getElementById(tab + '-tab').classList.add('active');
        document.querySelectorAll('.tab-pane').forEach(function(el) { el.classList.remove('show', 'active'); });
        document.getElementById(tab + 'Tab').classList.add('show', 'active');

        if (!registerData[tab]) {
            loadOrCreateRegister(tab);
        } else {
            renderTable(tab);
        }
    }

    // ─── SET MODE ─────────────────────────────────────────────────
    function setMode(mode) {
        currentMode = mode;
        document.getElementById('modeManual').classList.toggle('active', mode === 'manual');
        document.getElementById('modeAuto').classList.toggle('active', mode === 'auto');
    }

    function applyAutoGst() {
        if (currentMode !== 'auto') { showToast('Switch to Auto mode first.', 'info'); return; }
        var prefix = currentTab === 'debit' ? 'd' : 'c';
        var gstType = document.getElementById('gstType').value;
        var tbody = document.getElementById(currentTab === 'debit' ? 'debitBody' : 'creditBody');
        var rows = tbody.querySelectorAll('tr.month-row');
        rows.forEach(function(row) {
            var taxInput = row.querySelector('input[id^="' + prefix + 'tax_"]');
            var rateSelect = row.querySelector('select[id^="' + prefix + 'rate_"]');
            if (!taxInput || !rateSelect) return;
            var taxVal = parseVal(taxInput.value);
            var rate = parseFloat(rateSelect.value) || 0;
            if (taxVal > 0 && rate > 0) {
                var gstAmount = taxVal * (rate / 100);
                var igst = 0, cgst = 0, sgst = 0;
                if (gstType === 'intra') { cgst = gstAmount / 2; sgst = gstAmount / 2; } else { igst = gstAmount; }
                row.querySelector('input[id^="' + prefix + 'igst_"]').value = igst === 0 ? '' : fmt(igst);
                row.querySelector('input[id^="' + prefix + 'cgst_"]').value = cgst === 0 ? '' : fmt(cgst);
                row.querySelector('input[id^="' + prefix + 'sgst_"]').value = sgst === 0 ? '' : fmt(sgst);
                row.querySelector('input[id^="' + prefix + 'inv_"]').value = taxVal + igst + cgst + sgst === 0 ? '' : fmt(taxVal + igst + cgst + sgst);
            }
        });
        calcTotal(prefix);
        showToast('Auto GST applied!', 'success');
    }

    function onGstTypeChange() {
        if (currentMode === 'auto') { applyAutoGst(); }
    }

    // ─── SAVE DRAFT ──────────────────────────────────────────────
    function saveDraft() {
        var tab = currentTab;
        var register = registerData[tab];
        if (!register) { showToast('No register to save', 'warning'); return; }

        // ✅ Use the hidden input for the register ID (most reliable)
        var hidden = document.getElementById('registerId');
        var registerId = hidden ? parseInt(hidden.value) : null;
        if (!registerId) {
            // fallback to register.id
            registerId = register.id;
            console.warn('registerId hidden input not set, using register.id:', registerId);
        }
        if (!registerId) {
            showToast('Could not determine register ID.', 'error');
            return;
        }
        console.log('Saving register with ID:', registerId);

        var prefix = tab === 'debit' ? 'd' : 'c';
        var tbody = document.getElementById(tab === 'debit' ? 'debitBody' : 'creditBody');
        var rows = [];
        var rowEls = tbody.querySelectorAll('tr.month-row');

        rowEls.forEach(function(tr) {
            var rowId = tr.dataset.rowId ? parseInt(tr.dataset.rowId) : null;
            var month = tr.querySelector('.td-month')?.innerText?.trim() || '';
            var taxInput = tr.querySelector('input[id^="' + prefix + 'tax_"]');
            var igstInput = tr.querySelector('input[id^="' + prefix + 'igst_"]');
            var cgstInput = tr.querySelector('input[id^="' + prefix + 'cgst_"]');
            var sgstInput = tr.querySelector('input[id^="' + prefix + 'sgst_"]');
            var remInput = tr.querySelector('input[id^="' + prefix + 'rem_"]');
            if (!taxInput) return;

            console.log('Saving Row:', month, 'Row ID:', rowId);

            var taxVal = parseVal(taxInput.value);
            var igstVal = parseVal(igstInput?.value || 0);
            var cgstVal = parseVal(cgstInput?.value || 0);
            var sgstVal = parseVal(sgstInput?.value || 0);
            var rateSelect = tr.querySelector('select[id^="' + prefix + 'rate_"]');
            var rate = rateSelect ? parseFloat(rateSelect.value) || 0 : 0;

            rows.push({
                id: rowId,
                row_label: month,
                row_order: MONTHS.indexOf(month) + 1 || 0,
                taxable_value: taxVal,
                igst: igstVal,
                cgst: cgstVal,
                sgst: sgstVal,
                invoice_value: taxVal + igstVal + cgstVal + sgstVal,
                remarks: remInput?.value || null,
                gst_rate: rate
            });
        });

        // Custom rows
        var custom = customRows[tab] || [];
        custom.forEach(function(cr, idx) {
            var cid = 'custom_' + idx;
            var taxEl = document.getElementById(prefix + 'tax_' + cid);
            var igstEl = document.getElementById(prefix + 'igst_' + cid);
            var cgstEl = document.getElementById(prefix + 'cgst_' + cid);
            var sgstEl = document.getElementById(prefix + 'sgst_' + cid);
            var remEl = document.getElementById(prefix + 'rem_' + cid);
            var rateEl = document.getElementById(prefix + 'rate_' + cid);
            if (taxEl) {
                var taxVal = parseVal(taxEl.value);
                var igstVal = parseVal(igstEl?.value || 0);
                var cgstVal = parseVal(cgstEl?.value || 0);
                var sgstVal = parseVal(sgstEl?.value || 0);
                var rate = rateEl ? parseFloat(rateEl.value) || 0 : 0;
                rows.push({
                    id: null,
                    row_label: cr.month || 'Custom',
                    row_order: 99 + idx,
                    taxable_value: taxVal,
                    igst: igstVal,
                    cgst: cgstVal,
                    sgst: sgstVal,
                    invoice_value: taxVal + igstVal + cgstVal + sgstVal,
                    remarks: remEl?.value || null,
                    gst_rate: rate
                });
            }
        });

        var payload = {
            prepared_by: document.getElementById('footerPreparedBy').value,
            chartered_accountant: document.getElementById('footerCA').value,
            membership_number: document.getElementById('footerMembership').value,
            place: document.getElementById('footerPlace').value,
            signing_date: document.getElementById('footerDate').value,
            itr_acknowledgement_no: document.getElementById('itrAck').value,
            audit_acknowledgement_no: document.getElementById('auditAck').value,
            ie10_acknowledgement_no: document.getElementById('ie10Ack').value,
            udin: document.getElementById('udin').value,
            rows: rows
        };

        console.log('Payload:', payload);

        var url = getBaseUrl() + '/' + registerId;
        console.log('Save URL:', url);

        apiRequest('PUT', url, payload)
            .then(function(data) {
                if (data.success) {
                    registerData[tab] = data.register;
                    currentRegisterId = data.register.id;
                    var hidden = document.getElementById('registerId');
                    if (hidden) hidden.value = data.register.id;
                    populateFooter(data.register);
                    renderTable(tab);
                    showToast('Saved successfully!', 'success');
                } else {
                    showToast('Save failed: ' + (data.error || 'Unknown'), 'error');
                }
            })
            .catch(function(err) {
                showToast('Error: ' + (err.error || 'Unknown'), 'error');
            });
    }

    // ─── FINALIZE ────────────────────────────────────────────────
    function finalizeReport() {
        var tab = currentTab;
        var register = registerData[tab];
        if (!register) { showToast('No register to finalize', 'warning'); return; }
        if (register.status === 'finalized') { showToast('Already finalized', 'info'); return; }
        if (!confirm('Finalize this register? This cannot be undone.')) return;

        var hidden = document.getElementById('registerId');
        var registerId = hidden ? parseInt(hidden.value) : null;
        if (!registerId) {
            registerId = currentRegisterId || register.id;
        }
        if (!registerId) {
            showToast('Could not determine register ID.', 'error');
            return;
        }

        console.log('Finalizing register with ID:', registerId);
        var url = getBaseUrl() + '/' + registerId + '/finalize';
        console.log('Finalize URL:', url);

        apiRequest('POST', url)
            .then(function(data) {
                if (data.success) {
                    register.status = 'finalized';
                    document.getElementById('wmText').innerText = 'FINAL';
                    showToast('Finalized!', 'success');
                } else {
                    showToast('Finalize failed: ' + (data.error || 'Unknown'), 'error');
                }
            })
            .catch(function(err) {
                showToast('Error finalizing: ' + (err.error || 'Unknown'), 'error');
            });
    }

    // ─── ADD / DELETE CUSTOM ROW ─────────────────────────────────
    function addRow() {
        var tab = currentTab;
        if (!registerData[tab]) { showToast('Please load a register first', 'warning'); return; }
        customRows[tab].push({ month: 'Custom ' + (customRows[tab].length + 1), taxable: 0, igst: 0, cgst: 0, sgst: 0, remark: '' });
        renderTable(tab);
        showToast('Custom row added', 'success');
    }

    function deleteCustomRow(prefix, idx) {
        var tab = prefix === 'd' ? 'debit' : 'credit';
        if (customRows[tab].length === 0) return;
        if (!confirm('Delete this custom row?')) return;
        customRows[tab].splice(idx, 1);
        renderTable(tab);
        showToast('Custom row deleted', 'info');
    }

    function updateCustomMonth(prefix, idx, val) {
        var tab = prefix === 'd' ? 'debit' : 'credit';
        if (customRows[tab][idx]) customRows[tab][idx].month = val || 'Custom';
    }

    function deleteRow(rowId) {
        if (!confirm('Delete this row?')) return;
        var tab = currentTab;
        var register = registerData[tab];
        if (!register) return;
        register.rows = register.rows.filter(function(r) { return r.id != rowId; });
        renderTable(tab);
        showToast('Row marked for deletion. Save to persist.', 'info');
    }

    // ─── BREAKUP MODAL ────────────────────────────────────────────
    var breakupRowId = null;
    var breakupModalInstance = null;
    var currentBreakupData = [];
    var currentBreakupTarget = 0;

    function openBreakupModal(rowId, month, targetTaxable) {
        breakupRowId = rowId;
        currentBreakupTarget = targetTaxable;
        document.getElementById('breakupMonthLabel').textContent = month;

        var url = getBaseUrl() + '/row/' + rowId + '/breakups';
        apiRequest('GET', url)
            .then(function(data) {
                if (data.success) {
                    currentBreakupData = data.breakups || [];
                } else {
                    currentBreakupData = [];
                }
                renderBreakupModal();
            })
            .catch(function() {
                currentBreakupData = [];
                renderBreakupModal();
            });
    }

    function renderBreakupModal() {
        var breakups = currentBreakupData;
        if (breakups.length === 0) breakups.push({ taxable_value: 0, gst_rate: 18 });

        var html = '<div class="breakup-target">';
        html += '<div><span class="badge-month"><i class="bi bi-calendar3 me-1"></i>' + document.getElementById('breakupMonthLabel').textContent + '</span><span class="label ms-2">Target Taxable Value</span></div>';
        html += '<div><span class="value">₹ ' + fmt(currentBreakupTarget) + '</span></div>';
        html += '</div>';

        html += '<div class="breakup-toolbar"><button class="btn-sm-erp primary" onclick="addBreakupRow()"><i class="bi bi-plus-circle"></i> Add Row</button>';
        html += '<span style="font-size:10px;color:var(--slate-light);margin-left:6px;"><i class="bi bi-info-circle"></i> Enter amounts and select GST rates</span></div>';

        html += '<div class="breakup-table-wrap"><table class="breakup-table"><thead><tr><th style="width:36px;">#</th><th style="min-width:150px;">Amount (₹)</th><th style="min-width:120px;">GST Rate (%)</th><th style="width:50px;">&nbsp;</th></tr></thead><tbody id="breakupTableBody">';

        breakups.forEach(function(br, idx) {
            var id = br.id || null;
            html += '<tr id="br_row_' + idx + '">';
            html += '<td class="br-idx">' + (idx + 1) + '</td>';
            html += '<td class="br-amt"><input type="text" id="br_amt_' + idx + '" value="' + (br.taxable_value === 0 ? '' : fmt(br.taxable_value)) + '" oninput="onBreakupInput(' + idx + ')" onfocus="this.select()" /></td>';
            html += '<td class="br-rate"><select id="br_rate_' + idx + '" onchange="onBreakupInput(' + idx + ')">';
            [0,5,12,18,28,40].forEach(function(r) {
                var sel = r === (br.gst_rate || 18) ? 'selected' : '';
                html += '<option value="' + r + '" ' + sel + '>' + r + '%</option>';
            });
            html += '</select></td>';
            html += '<td class="br-actions">';
            if (id) {
                html += '<button class="del-btn" onclick="deleteBreakup(' + id + ')"><i class="bi bi-x-circle"></i></button>';
            } else {
                html += '<button class="del-btn" onclick="removeBreakupRow(' + idx + ')"><i class="bi bi-x-circle"></i></button>';
            }
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';

        var total = breakups.reduce(function(sum, br) { return sum + (br.taxable_value || 0); }, 0);
        var diff = total - currentBreakupTarget;
        var isMatched = Math.abs(diff) < 0.01;
        var statusHtml = isMatched ?
            '<span class="status-badge success"><i class="bi bi-check-circle-fill"></i> ✓ Matched</span>' :
            '<span class="status-badge warning"><i class="bi bi-exclamation-triangle-fill"></i> ⚠ Mismatch (' + fmt(Math.abs(diff)) + ')</span>';

        html += '<div class="breakup-summary" id="breakupSummary">';
        html += '<div class="sum-item"><span class="label">Breakup Total :</span><span class="val ' + (isMatched ? 'green' : 'red') + '">₹ ' + fmt(total) + '</span></div>';
        html += '<div class="sum-item"><span class="label">Difference :</span><span class="val ' + (isMatched ? 'green' : 'red') + '">' + (isMatched ? '₹ 0.00' : '₹ ' + fmt(Math.abs(diff))) + '</span></div>';
        html += '<div>' + statusHtml + '</div>';
        html += '</div>';

        document.getElementById('breakupModalBody').innerHTML = html;

        if (!breakupModalInstance) {
            var modalEl = document.getElementById('breakupModal');
            breakupModalInstance = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
        }
        breakupModalInstance.show();
    }

    function addBreakupRow() {
        currentBreakupData.push({ taxable_value: 0, gst_rate: 18 });
        renderBreakupModal();
    }

    function removeBreakupRow(idx) {
        if (currentBreakupData.length <= 1) { showToast('At least one row required', 'warning'); return; }
        if (!confirm('Remove this breakup row?')) return;
        currentBreakupData.splice(idx, 1);
        renderBreakupModal();
    }

    function deleteBreakup(breakupId) {
        if (!confirm('Delete this breakup?')) return;
        var url = getBaseUrl() + '/breakup/' + breakupId;
        apiRequest('DELETE', url)
            .then(function(data) {
                if (data.success) {
                    currentBreakupData = currentBreakupData.filter(function(b) { return b.id != breakupId; });
                    renderBreakupModal();
                    showToast('Breakup deleted', 'success');
                } else {
                    showToast('Delete failed', 'error');
                }
            })
            .catch(function() {
                showToast('Error deleting breakup', 'error');
            });
    }

    function onBreakupInput(idx) {
        var amtEl = document.getElementById('br_amt_' + idx);
        var rateEl = document.getElementById('br_rate_' + idx);
        if (amtEl) currentBreakupData[idx].taxable_value = parseVal(amtEl.value);
        if (rateEl) currentBreakupData[idx].gst_rate = parseFloat(rateEl.value) || 0;
        updateBreakupSummary();
    }

    function updateBreakupSummary() {
        var total = currentBreakupData.reduce(function(sum, br) { return sum + (br.taxable_value || 0); }, 0);
        var diff = total - currentBreakupTarget;
        var isMatched = Math.abs(diff) < 0.01;
        var summaryEl = document.getElementById('breakupSummary');
        if (!summaryEl) return;
        var statusHtml = isMatched ?
            '<span class="status-badge success"><i class="bi bi-check-circle-fill"></i> ✓ Matched</span>' :
            '<span class="status-badge warning"><i class="bi bi-exclamation-triangle-fill"></i> ⚠ Mismatch (' + fmt(Math.abs(diff)) + ')</span>';
        summaryEl.innerHTML = '<div class="sum-item"><span class="label">Breakup Total :</span><span class="val ' + (isMatched ? 'green' : 'red') + '">₹ ' + fmt(total) + '</span></div><div class="sum-item"><span class="label">Difference :</span><span class="val ' + (isMatched ? 'green' : 'red') + '">' + (isMatched ? '₹ 0.00' : '₹ ' + fmt(Math.abs(diff))) + '</span></div><div>' + statusHtml + '</div>';
    }

    function saveBreakup() {
        if (!breakupRowId) { showToast('No row selected', 'warning'); return; }
        var payload = { breakups: currentBreakupData };
        var url = getBaseUrl() + '/row/' + breakupRowId + '/breakups';
        apiRequest('POST', url, payload)
            .then(function(data) {
                if (data.success) {
                    var tab = currentTab;
                    var register = registerData[tab];
                    if (register) {
                        var rowIndex = register.rows.findIndex(function(r) { return r.id == breakupRowId; });
                        if (rowIndex !== -1) {
                            register.rows[rowIndex] = data.row;
                        }
                    }
                    if (breakupModalInstance) breakupModalInstance.hide();
                    showToast('Breakups saved!', 'success');
                    renderTable(tab);
                } else {
                    showToast('Save failed: ' + (data.error || 'Unknown'), 'error');
                }
            })
            .catch(function() {
                showToast('Error saving breakups', 'error');
            });
    }

    // ─── CA SIGNATURE ─────────────────────────────────────────────
    function handleCaSignatureUpload(file) {
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('caSigDisplay').innerHTML = '<img src="' + e.target.result + '" alt="CA Signature" />';
            try { localStorage.setItem('caSignature', e.target.result); } catch (ex) {}
        };
        reader.readAsDataURL(file);
    }
    function clearCaSignature() {
        document.getElementById('caSigDisplay').innerHTML = '<div class="ft-sign-line"></div>';
        try { localStorage.removeItem('caSignature'); } catch (ex) {}
        document.getElementById('caSigUpload').value = '';
    }
    function loadSavedSignature() {
        try { var saved = localStorage.getItem('caSignature'); if (saved) document.getElementById('caSigDisplay').innerHTML = '<img src="' + saved + '" alt="CA Signature" />'; } catch (ex) {}
    }

    // ─── KEYBOARD SHORTCUTS ────────────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') { e.preventDefault(); saveDraft(); }
        if (e.ctrlKey && e.key === 'p') { e.preventDefault(); window.print(); }
    });

    // ─── CLOCK ─────────────────────────────────────────────────────
    function tick() {
        var n = new Date();
        var el = document.getElementById('sbTime');
        if (el) {
            el.textContent = String(n.getHours()).padStart(2, '0') + ':' +
                String(n.getMinutes()).padStart(2, '0') + ':' +
                String(n.getSeconds()).padStart(2, '0');
        }
    }
    setInterval(tick, 1000);

    // ─── DOM READY ──────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('caSigUpload').addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) handleCaSignatureUpload(this.files[0]);
        });
        loadSavedSignature();
        setMode('manual');
        switchTab('debit');
        console.log('✅ Debit/Credit Note Register ready.');
    });

    // ─── GLOBAL EXPOSURE ──────────────────────────────────────────
    window.switchTab = switchTab;
    window.saveDraft = saveDraft;
    window.finalizeReport = finalizeReport;
    window.addRow = addRow;
    window.deleteCustomRow = deleteCustomRow;
    window.updateCustomMonth = updateCustomMonth;
    window.deleteRow = deleteRow;
    window.onRowInput = onRowInput;
    window.openBreakupModal = openBreakupModal;
    window.addBreakupRow = addBreakupRow;
    window.removeBreakupRow = removeBreakupRow;
    window.deleteBreakup = deleteBreakup;
    window.onBreakupInput = onBreakupInput;
    window.saveBreakup = saveBreakup;
    window.clearCaSignature = clearCaSignature;
    window.setMode = setMode;
    window.applyAutoGst = applyAutoGst;
    window.onGstTypeChange = onGstTypeChange;
    window.submitReport = submitReport;
</script>