<div class="section-header">
    <h5>📋 Creditors Reconciliation <span class="badge-type" id="creditorCount">{{ $creditors->count() }} records</span></h5>
    <div class="section-tools">
        <span class="collapse-toggle" onclick="toggleSection('creditorsBody')">
            <span class="arrow open" id="creditorsArrow">▶</span> Collapse
        </span>
    </div>
</div>
<div class="section-body" id="creditorsBody">
    <div class="table-wrap" id="creditorTableWrap">
        <table class="recon-table" id="creditorsTable">
            <thead>
                <tr>
                    <th style="width:30px;">SL</th>
                    <th class="sticky-col" style="min-width:120px;">Creditor Name</th>
                    <th style="min-width:100px;">PAN</th>
                    <th style="min-width:120px;">GSTIN</th>
                    <th style="min-width:110px;">Bill Amount <span class="sub-label">(₹)</span></th>
                    <th style="min-width:100px;">Payment Follow-up Date</th>
                    <th style="min-width:110px;">Amount Paid <span class="sub-label">(₹)</span></th>
                    <th style="min-width:110px;">Outstanding Payable <span class="sub-label">(₹)</span></th>
                    <th style="min-width:100px;">Remarks</th>
                    <th style="min-width:80px;">Actions</th>
                </tr>
            </thead>
            <tbody id="creditorsBodyRows">
                @forelse($creditors as $index => $creditor)
                    @include('Backoffice.creditors-reconciliation.components.creditor-row', [
                        'index' => $index,
                        'creditor' => $creditor
                    ])
                @empty
                    @include('Backoffice.creditors-reconciliation.components.empty-state')
                @endforelse
            </tbody>
        </table>
    </div>
</div>