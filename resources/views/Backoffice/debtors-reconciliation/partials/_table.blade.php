<div class="section-header">
    <h5>📋 Debtors Reconciliation <span class="badge-type" id="debtorCount">{{ $debtors->count() }} records</span></h5>
    <div class="section-tools">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="tableSearch" placeholder="Search debtors…" oninput="filterTable()" />
        </div>
        <button class="btn-erp btn-blue" onclick="addRow()" style="padding:4px 12px;font-size:11px;">
            <i class="bi bi-plus-circle"></i> Add Row
        </button>
        <span class="collapse-toggle" onclick="toggleSection('debtorsBody')">
            <span class="arrow open" id="debtorsArrow">▶</span> Collapse
        </span>
    </div>
</div>
<div class="section-body" id="debtorsBody">
    <div class="table-wrap" id="debtorTableWrap">
        <table class="recon-table" id="debtorsTable">
            <thead>
                <tr>
                    <th style="width:30px;">SL</th>
                    <th class="sticky-col" style="min-width:120px;">Debtor Name</th>
                    <th style="min-width:100px;">PAN</th>
                    <th style="min-width:120px;">GSTIN</th>
                    <th style="min-width:110px;">Bill Amount <span class="sub-label">(₹)</span></th>
                    <th style="min-width:100px;">Payment Follow-up Date</th>
                    <th style="min-width:110px;">Amount Received <span class="sub-label">(₹)</span></th>
                    <th style="min-width:110px;">Outstanding <span class="sub-label">(₹)</span></th>
                    <th style="min-width:100px;">Remarks</th>
                    <th style="min-width:80px;">Actions</th>
                </tr>
            </thead>
            <tbody id="debtorsBodyRows">
                @forelse($debtors as $index => $debtor)
                    @include('Backoffice.debtors-reconciliation.components.debtor-row', [
                        'index' => $index,
                        'debtor' => $debtor
                    ])
                @empty
                    @include('Backoffice.debtors-reconciliation.components.empty-state')
                @endforelse
            </tbody>
        </table>
    </div>
</div>