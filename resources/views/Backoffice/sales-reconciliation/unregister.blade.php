<script>
    (function() {
        'use strict';

        // ─── CONFIG ──────────────────────────────────────────────────
        const computationId = {{ $computation->id ?? 0 }};

        // ✅ CSRF Token – try multiple sources
        const csrfToken = 
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            document.querySelector('input[name="_token"]')?.value ||
            document.getElementById('csrf-token')?.value ||
            '';

        if (!csrfToken) {
            console.error('❌ CSRF token is missing! Please add <meta name="csrf-token" content="{{ csrf_token() }}"> to your layout.');
            showToast('CSRF token missing. Please refresh.', 'error');
        }

        let currentType = 'register';
        let autoSaveTimer = null;
        const DEBOUNCE_DELAY = 500; // ms

        // ─── HELPERS ──────────────────────────────────────────────────
        function fmt(n) {
            if (n === undefined || n === null || isNaN(n)) return '';
            return n.toLocaleString('en-IN', { maximumFractionDigits: 0 });
        }

        function parseVal(v) {
            if (typeof v === 'string') {
                v = v.replace(/[₹,\s]/g, '').trim();
                if (v === '') return 0;
                return parseFloat(v) || 0;
            }
            return v || 0;
        }

        function showToast(msg, type = 'success') {
            const existing = document.querySelector('.toast-custom');
            if (existing) existing.remove();

            const t = document.createElement('div');
            const cls = type === 'success' ? 'toast-success' : type === 'info' ? 'toast-info' : 'toast-error';
            t.className = 'toast-custom ' + cls;
            const icon = type === 'success' ? 'check-circle-fill' : type === 'info' ? 'info-circle-fill' : 'exclamation-triangle-fill';
            t.innerHTML = `<i class="bi bi-${icon}"></i> ${msg}`;
            document.body.appendChild(t);
            setTimeout(() => t.remove(), 2800);
        }

        // ✅ UPDATED: accepts index to set row_order
        function getRowData(rowElement, index) {
            const id = rowElement.dataset.rowId;
            const type = rowElement.dataset.rowType || 'custom';
            const labelInput = rowElement.querySelector('.custom-label-input');
            const taxableInput = rowElement.querySelector('input[data-field="taxable_value"]');
            const igstInput = rowElement.querySelector('input[data-field="igst"]');
            const cgstInput = rowElement.querySelector('input[data-field="cgst"]');
            const sgstInput = rowElement.querySelector('input[data-field="sgst"]');
            const sgstTdsInput = rowElement.querySelector('input[data-field="sgst_tds"]');
            const cgstTdsInput = rowElement.querySelector('input[data-field="cgst_tds"]');
            const remarksInput = rowElement.querySelector('input[data-field="remarks"]');

            return {
                id: id ? parseInt(id) : null,
                row_order: index + 1,
                row_type: type,
                row_label: labelInput ? labelInput.value : rowElement.querySelector('.td-month')?.innerText?.trim() || '',
                taxable_value: taxableInput ? parseVal(taxableInput.value) : 0,
                igst: igstInput ? parseVal(igstInput.value) : 0,
                cgst: cgstInput ? parseVal(cgstInput.value) : 0,
                sgst: sgstInput ? parseVal(sgstInput.value) : 0,
                sgst_tds: sgstTdsInput ? parseVal(sgstTdsInput.value) : 0,
                cgst_tds: cgstTdsInput ? parseVal(cgstTdsInput.value) : 0,
                remarks: remarksInput ? remarksInput.value : '',
            };
        }

        // ✅ UPDATED: pass index to getRowData
        function getAllRowsData() {
            const rows = document.querySelectorAll('#tableBody tr.month-row');
            const data = [];
            rows.forEach((row, index) => {
                data.push(getRowData(row, index));
            });
            return data;
        }

        function getHeaderData() {
            return {
                prepared_by: document.getElementById('prepared_by')?.value || '',
                chartered_accountant: document.getElementById('chartered_accountant')?.value || '',
                membership_number: document.getElementById('membership_number')?.value || '',
                place: document.getElementById('place')?.value || '',
                signing_date: document.getElementById('signing_date')?.value || '',
                itr_acknowledgement_no: document.getElementById('itr_acknowledgement_no')?.value || '',
                audit_acknowledgement_no: document.getElementById('audit_acknowledgement_no')?.value || '',
                ie10_acknowledgement_no: document.getElementById('ie10_acknowledgement_no')?.value || '',
                udin: document.getElementById('udin')?.value || '',
            };
        }

        // ─── AJAX HELPERS ────────────────────────────────────────────
        async function apiRequest(url, method = 'GET', data = null, isFile = false) {
            const headers = {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            };

            if (!isFile) {
                headers['Content-Type'] = 'application/json';
            }

            const options = {
                method: method,
                headers: headers,
            };

            if (data) {
                if (isFile) {
                    options.body = data;
                } else {
                    options.body = JSON.stringify(data);
                }
            }

            console.log('📤 API Request:', { url, method, headers, data: options.body });

            try {
                const response = await fetch(url, options);
                const result = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && result.errors) {
                        const errors = Object.values(result.errors).flat().join('\n');
                        showToast('Validation errors:\n' + errors, 'error');
                        throw new Error('Validation failed: ' + errors);
                    }
                    if (response.status === 419) {
                        showToast('Session expired. Refreshing...', 'error');
                        setTimeout(() => location.reload(), 1500);
                        throw new Error('CSRF token mismatch. Page will reload.');
                    }
                    throw new Error(result.message || `Request failed with status ${response.status}`);
                }

                return result;
            } catch (error) {
                console.error('❌ API Error:', error);
                throw error;
            }
        }

        // ─── SAVE OPERATIONS ──────────────────────────────────────────
        async function saveDraft() {
            try {
                const rows = getAllRowsData();
                const header = getHeaderData();
                const data = {
                    type: currentType,
                    rows: rows,
                    header: header,
                };

                const url = `/backoffice/computations/${computationId}/sales-reconciliation/save`;
                const result = await apiRequest(url, 'POST', data);

                if (result.success) {
                    showToast('Draft saved successfully!', 'success');
                    if (result.rows) {
                        updateRowIds(result.rows);
                    }
                }
            } catch (error) {
                showToast(error.message || 'Failed to save draft', 'error');
            }
        }

        function updateRowIds(rows) {
            const rowElements = document.querySelectorAll('#tableBody tr.month-row');
            rowElements.forEach((el, index) => {
                if (index < rows.length && rows[index].id) {
                    el.dataset.rowId = rows[index].id;
                }
            });
        }

        // ─── AUTO-SAVE ON INPUT ──────────────────────────────────────
        function triggerAutoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                saveDraft();
            }, DEBOUNCE_DELAY);
        }

        // ─── ADD ROW ──────────────────────────────────────────────────
        async function addRow() {
            try {
                const url = `/backoffice/computations/${computationId}/sales-reconciliation/add-row`;
                const result = await apiRequest(url, 'POST', { type: currentType });

                if (result.success && result.rows) {
                    renderTableRows(result.rows);
                    showToast('New row added!', 'success');
                }
            } catch (error) {
                showToast(error.message || 'Failed to add row', 'error');
            }
        }

        // ─── DELETE ROW ──────────────────────────────────────────────
        async function deleteCustomRow(rowId) {
            if (!rowId) {
                showToast('Cannot delete this row', 'error');
                return;
            }

            if (!confirm('Delete this custom row?')) return;

            try {
                const url = `/backoffice/computations/${computationId}/sales-reconciliation/delete-row/${rowId}`;
                const result = await apiRequest(url, 'DELETE', { type: currentType });

                if (result.success && result.rows) {
                    renderTableRows(result.rows);
                    showToast('Row deleted!', 'info');
                }
            } catch (error) {
                showToast(error.message || 'Failed to delete row', 'error');
            }
        }

        // ─── RENDER TABLE ROWS ────────────────────────────────────────
        function renderTableRows(rows) {
            const tbody = document.getElementById('tableBody');
            if (!tbody) return;

            const totalRow = document.getElementById('totalRow');
            const monthRows = tbody.querySelectorAll('tr.month-row');
            monthRows.forEach(el => el.remove());

            rows.forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.className = 'month-row';
                tr.dataset.rowId = row.id;
                tr.dataset.rowType = row.row_type;

                const isCustom = row.row_type === 'custom';

                // Month column
                const tdMonth = document.createElement('td');
                tdMonth.className = 'td-month';
                if (isCustom) {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.value = row.row_label || 'Custom';
                    input.className = 'custom-label-input';
                    input.style.cssText = 'border:none;background:transparent;outline:none;font-weight:600;width:100%;font-size:11px;color:var(--text-dark);';
                    input.dataset.field = 'row_label';
                    input.dataset.rowId = row.id;
                    input.addEventListener('change', triggerAutoSave);
                    tdMonth.appendChild(input);
                } else {
                    tdMonth.textContent = row.row_label;
                }
                tr.appendChild(tdMonth);

                // Number fields
                const fields = ['taxable_value', 'igst', 'cgst', 'sgst', 'sgst_tds', 'cgst_tds'];
                fields.forEach(field => {
                    const td = document.createElement('td');
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'td-amt';
                    input.value = row[field] ? fmt(row[field]) : '';
                    input.dataset.field = field;
                    input.dataset.rowId = row.id;
                    input.addEventListener('input', function() {
                        // Recalculate invoice
                        const rowData = getRowData(this.closest('tr'), 0);
                        const invoiceInput = this.closest('tr').querySelector('input[data-field="invoice_value"]');
                        if (invoiceInput) {
                            const total = rowData.taxable_value + rowData.igst + rowData.cgst + rowData.sgst + rowData.sgst_tds + rowData.cgst_tds;
                            invoiceInput.value = total ? fmt(total) : '';
                        }
                        // Sync CGST/SGST
                        const fieldName = this.dataset.field;
                        if (fieldName === 'cgst') {
                            const sgstInput = this.closest('tr').querySelector('input[data-field="sgst"]');
                            if (sgstInput && sgstInput.value !== this.value) {
                                sgstInput.value = this.value;
                            }
                        } else if (fieldName === 'sgst') {
                            const cgstInput = this.closest('tr').querySelector('input[data-field="cgst"]');
                            if (cgstInput && cgstInput.value !== this.value) {
                                cgstInput.value = this.value;
                            }
                        } else if (fieldName === 'cgst_tds') {
                            const sgstTdsInput = this.closest('tr').querySelector('input[data-field="sgst_tds"]');
                            if (sgstTdsInput && sgstTdsInput.value !== this.value) {
                                sgstTdsInput.value = this.value;
                            }
                        } else if (fieldName === 'sgst_tds') {
                            const cgstTdsInput = this.closest('tr').querySelector('input[data-field="cgst_tds"]');
                            if (cgstTdsInput && cgstTdsInput.value !== this.value) {
                                cgstTdsInput.value = this.value;
                            }
                        }
                        calcTotal();
                        triggerAutoSave();
                    });
                    input.addEventListener('focus', function() { this.select(); });
                    td.appendChild(input);
                    tr.appendChild(td);
                });

                // Invoice value (readonly)
                const tdInvoice = document.createElement('td');
                const invoiceInput = document.createElement('input');
                invoiceInput.type = 'text';
                invoiceInput.className = 'td-amt';
                invoiceInput.value = row.invoice_value ? fmt(row.invoice_value) : '';
                invoiceInput.readOnly = true;
                invoiceInput.style.cssText = 'background:#f4f7fc;cursor:default;';
                invoiceInput.dataset.field = 'invoice_value';
                invoiceInput.dataset.rowId = row.id;
                tdInvoice.appendChild(invoiceInput);
                tr.appendChild(tdInvoice);

                // Remarks
                const tdRemark = document.createElement('td');
                tdRemark.className = 'td-remark';
                const remarkInput = document.createElement('input');
                remarkInput.type = 'text';
                remarkInput.value = row.remarks || '';
                remarkInput.placeholder = '—';
                remarkInput.dataset.field = 'remarks';
                remarkInput.dataset.rowId = row.id;
                remarkInput.addEventListener('change', triggerAutoSave);
                tdRemark.appendChild(remarkInput);
                tr.appendChild(tdRemark);

                // Actions
                const tdActions = document.createElement('td');
                tdActions.className = 'td-actions';
                const delBtn = document.createElement('button');
                delBtn.className = 'del-btn' + (isCustom ? '' : ' disabled');
                if (isCustom) {
                    delBtn.onclick = function() { deleteCustomRow(row.id); };
                } else {
                    delBtn.disabled = true;
                }
                const icon = document.createElement('i');
                icon.className = 'bi bi-x-circle';
                delBtn.appendChild(icon);
                tdActions.appendChild(delBtn);
                tr.appendChild(tdActions);

                if (totalRow) {
                    tbody.insertBefore(tr, totalRow);
                } else {
                    tbody.appendChild(tr);
                }
            });

            calcTotal();
        }

        // ─── CALCULATE TOTAL ──────────────────────────────────────────
        function calcTotal() {
            const rows = document.querySelectorAll('#tableBody tr.month-row');
            let totals = {
                taxable: 0,
                igst: 0,
                cgst: 0,
                sgst: 0,
                sgst_tds: 0,
                cgst_tds: 0,
                invoice: 0,
            };

            rows.forEach(row => {
                const data = getRowData(row, 0);
                totals.taxable += data.taxable_value;
                totals.igst += data.igst;
                totals.cgst += data.cgst;
                totals.sgst += data.sgst;
                totals.sgst_tds += data.sgst_tds;
                totals.cgst_tds += data.cgst_tds;
                const invoiceInput = row.querySelector('input[data-field="invoice_value"]');
                if (invoiceInput) {
                    totals.invoice += parseVal(invoiceInput.value);
                }
            });

            document.getElementById('tot_taxable').textContent = totals.taxable ? fmt(totals.taxable) : '-';
            document.getElementById('tot_igst').textContent = totals.igst ? fmt(totals.igst) : '-';
            document.getElementById('tot_cgst').textContent = totals.cgst ? fmt(totals.cgst) : '-';
            document.getElementById('tot_sgst').textContent = totals.sgst ? fmt(totals.sgst) : '-';
            document.getElementById('tot_sgsttds').textContent = totals.sgst_tds ? fmt(totals.sgst_tds) : '-';
            document.getElementById('tot_cgsttds').textContent = totals.cgst_tds ? fmt(totals.cgst_tds) : '-';
            document.getElementById('tot_inv').textContent = totals.invoice ? fmt(totals.invoice) : '-';
        }

        // ─── SWITCH TEMPLATE ──────────────────────────────────────────
        async function switchTemplate(type) {
            currentType = type;
            const label = type === 'register' ? 'REGISTER SALES RECONCILIATION STATEMENT' : 'UNREGISTER SALES RECONCILIATION STATEMENT';
            const titleEl = document.getElementById('reportTypeDisplay');
            const titleInput = document.getElementById('reportTitle');
            if (titleEl) titleEl.innerText = label;
            if (titleInput) titleInput.value = type === 'register' ? 'SALES RECONCILIATION STATEMENT (REGISTER)' : 'SALES RECONCILIATION STATEMENT (UNREGISTER)';

            document.querySelectorAll('.template-toggle label').forEach(el => el.classList.remove('active-label'));
            const activeLabel = document.getElementById(type === 'register' ? 'lblRegister' : 'lblUnregister');
            if (activeLabel) activeLabel.classList.add('active-label');

            try {
                const url = `/backoffice/computations/${computationId}/sales-reconciliation/data?type=${type}`;
                const result = await apiRequest(url, 'GET');

                if (result.rows) {
                    renderTableRows(result.rows);
                    if (result.reconciliation) {
                        document.getElementById('prepared_by').value = result.reconciliation.prepared_by || '';
                        document.getElementById('chartered_accountant').value = result.reconciliation.chartered_accountant || '';
                        document.getElementById('membership_number').value = result.reconciliation.membership_number || '';
                        document.getElementById('place').value = result.reconciliation.place || '';
                        document.getElementById('signing_date').value = result.reconciliation.signing_date || '';
                        document.getElementById('itr_acknowledgement_no').value = result.reconciliation.itr_acknowledgement_no || '';
                        document.getElementById('audit_acknowledgement_no').value = result.reconciliation.audit_acknowledgement_no || '';
                        document.getElementById('ie10_acknowledgement_no').value = result.reconciliation.ie10_acknowledgement_no || '';
                        document.getElementById('udin').value = result.reconciliation.udin || '';
                        if (result.reconciliation.ca_signature) {
                            const wrapper = document.getElementById('caSigDisplay');
                            wrapper.innerHTML = `<img src="/storage/${result.reconciliation.ca_signature}" alt="CA Signature" style="max-height:38px;max-width:130px;border:1px solid #e0e7ef;border-radius:3px;padding:2px;background:#fff;display:inline-block;" />`;
                        } else {
                            document.getElementById('caSigDisplay').innerHTML = `<div class="ft-sign-line"></div>`;
                        }
                    }
                }
            } catch (error) {
                showToast(error.message || 'Failed to load data', 'error');
            }
        }

        // ─── UPLOAD SIGNATURE ──────────────────────────────────────────
        async function uploadSignature(signatureType) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = async function(e) {
                const file = this.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('type', currentType);
                formData.append('signature_type', signatureType);
                formData.append('signature', file);

                try {
                    const url = `/backoffice/computations/${computationId}/sales-reconciliation/upload-signature`;
                    const result = await apiRequest(url, 'POST', formData, true);

                    if (result.success) {
                        const wrapper = document.getElementById('caSigDisplay');
                        wrapper.innerHTML = `<img src="${result.url}" alt="CA Signature" style="max-height:38px;max-width:130px;border:1px solid #e0e7ef;border-radius:3px;padding:2px;background:#fff;display:inline-block;" />`;
                        showToast('Signature uploaded!', 'success');
                    }
                } catch (error) {
                    showToast(error.message || 'Failed to upload signature', 'error');
                }
            };
            input.click();
        }

        async function clearSignature() {
            if (!confirm('Clear the CA signature?')) return;

            try {
                const url = `/backoffice/computations/${computationId}/sales-reconciliation/clear-signature`;
                const result = await apiRequest(url, 'POST', {
                    type: currentType,
                    signature_type: 'ca',
                });

                if (result.success) {
                    document.getElementById('caSigDisplay').innerHTML = `<div class="ft-sign-line"></div>`;
                    showToast('Signature cleared!', 'info');
                }
            } catch (error) {
                showToast(error.message || 'Failed to clear signature', 'error');
            }
        }

        // ─── SUBMIT / FINALIZE ────────────────────────────────────────
        async function submitReport() {
            if (!confirm('Submit this Sales Reconciliation Statement?')) return;
            await finalizeReport();
        }

        async function finalizeReport() {
            if (!confirm('Finalize this Sales Reconciliation Statement? This cannot be undone.')) return;

            try {
                const url = `/backoffice/computations/${computationId}/sales-reconciliation/finalize`;
                const result = await apiRequest(url, 'POST', { type: currentType });

                if (result.success) {
                    document.getElementById('wmText').style.display = 'none';
                    showToast('Report Finalized!', 'success');
                    document.querySelectorAll('#tableBody input').forEach(inp => inp.disabled = true);
                    document.querySelectorAll('.del-btn').forEach(btn => btn.disabled = true);
                }
            } catch (error) {
                showToast(error.message || 'Failed to finalize report', 'error');
            }
        }

        // ─── KEYBOARD SHORTCUTS ──────────────────────────────────────
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                saveDraft();
            }
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            if (e.key === 'F5') {
                e.preventDefault();
                calcTotal();
            }
        });

        // ─── INIT ──────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial data based on page type (default 'register')
            var initialType = window.initialPageType || 'register';
            switchTemplate(initialType);

            // Upload button
            document.getElementById('caSigUpload')?.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    const file = this.files[0];
                    const formData = new FormData();
                    formData.append('type', currentType);
                    formData.append('signature_type', 'ca');
                    formData.append('signature', file);

                    const url = `/backoffice/computations/${computationId}/sales-reconciliation/upload-signature`;
                    apiRequest(url, 'POST', formData, true)
                        .then(result => {
                            if (result.success) {
                                const wrapper = document.getElementById('caSigDisplay');
                                wrapper.innerHTML = `<img src="${result.url}" alt="CA Signature" style="max-height:38px;max-width:130px;border:1px solid #e0e7ef;border-radius:3px;padding:2px;background:#fff;display:inline-block;" />`;
                                showToast('Signature uploaded!', 'success');
                            }
                        })
                        .catch(error => {
                            showToast(error.message || 'Failed to upload signature', 'error');
                        });
                }
            });

            // Clear signature button
            document.querySelector('.ft-upload-btn:last-child')?.addEventListener('click', clearSignature);

            // Expose functions globally
            window.addRow = addRow;
            window.deleteCustomRow = deleteCustomRow;
            window.finalizeReport = finalizeReport;
            window.saveDraft = saveDraft;
            window.switchTemplate = switchTemplate;
            window.calcTotal = calcTotal;
            window.uploadSignature = uploadSignature;
            window.clearSignature = clearSignature;
            window.submitReport = submitReport;
        });

    })();
</script>