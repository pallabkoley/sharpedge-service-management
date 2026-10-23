<div class="brs-body">

    <!-- LEFT COLUMN -->
    <div class="brs-left" id="brsLeft">
        <div class="field-row"><span class="lbl">Statement Date</span><div class="fld"><input type="date" id="statementDate" value="{{ $bankReconciliation->statement_date?->format('Y-m-d') ?? date('Y-m-d') }}" oninput="calcBRS()" /></div></div>
        <div class="field-row"><span class="lbl">Closing Balance as per Bank Statement</span><div class="fld"><input type="text" id="bankBalance" value="{{ $bankReconciliation->closing_balance_bank ?? 0 }}" oninput="calcBRS()" /></div></div>
        <div class="field-row"><span class="lbl indent">Less : Outstanding Cheques</span><div class="fld"><input class="readonly" type="text" id="totalCheques" value="0" readonly /></div></div>
        <div class="field-row"><span class="lbl indent">Add : Outstanding Deposits</span><div class="fld"><input class="readonly" type="text" id="totalDeposits" value="0" readonly /></div></div>
        <div class="field-row"><span class="lbl indent">Other Adjustments</span><div class="fld"><input type="text" id="otherAdjustments" value="0" oninput="calcBRS()" /></div></div>
        <div class="field-row" style="border-top:2px solid #4a6fa5;padding-top:4px;margin-top:2px;"><span class="lbl total">Adjusted Balance</span><div class="fld"><input class="readonly total-value" type="text" id="adjustedBalance" value="0" readonly /></div></div>
        <div class="field-row"><span class="lbl">Closing Balance as per Cash Book</span><div class="fld"><input type="text" id="cashBookBalance" value="{{ $bankReconciliation->closing_balance_cashbook ?? 0 }}" oninput="calcBRS()" /></div></div>
        <div class="field-row" style="border-top:2px solid #4a6fa5;padding-top:4px;margin-top:2px;"><span class="lbl total">Variance</span><div class="fld"><input class="readonly" type="text" id="variance" value="0" readonly /></div></div>
        <div class="field-row"><span class="lbl">Status</span><div class="fld"><span class="status-badge balanced" id="statusBadge">✔ Balanced</span></div></div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="brs-right" id="brsRight">

        <!-- Outstanding Cheques -->
        <div class="brs-table-wrap">
            <div class="tbl-header">
                <span class="tbl-title">Outstanding Cheques</span>
                <span class="tbl-total" id="chequeTotalLabel">Total: ₹0</span>
                <div class="tbl-actions">
                    <button onclick="addChequeRow()"><i class="bi bi-plus"></i> Add</button>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="brs-table brs-table-cheques" id="chequesTable">
                    <thead><tr><th>SL</th><th>Date</th><th>Cheque No</th><th>Amount</th><th style="width:50px;">&nbsp;</th></tr></thead>
                    <tbody id="chequesBodyRows">
                        @forelse($cheques as $index => $cheque)
                            @include('Backoffice.bank-reconciliation.components.cheque-row', ['index' => $index, 'cheque' => $cheque])
                        @empty
                            @include('Backoffice.bank-reconciliation.components.empty-state', ['type' => 'cheque'])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Outstanding Deposits -->
        <div class="brs-table-wrap">
            <div class="tbl-header">
                <span class="tbl-title">Outstanding Deposits</span>
                <span class="tbl-total" id="depositTotalLabel">Total: ₹0</span>
                <div class="tbl-actions">
                    <button onclick="addDepositRow()"><i class="bi bi-plus"></i> Add</button>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="brs-table brs-table-deposits" id="depositsTable">
                    <thead><tr><th>SL</th><th>Date</th><th>Amount</th><th style="width:50px;">&nbsp;</th></tr></thead>
                    <tbody id="depositsBodyRows">
                        @forelse($deposits as $index => $deposit)
                            @include('Backoffice.bank-reconciliation.components.deposit-row', ['index' => $index, 'deposit' => $deposit])
                        @empty
                            @include('Backoffice.bank-reconciliation.components.empty-state', ['type' => 'deposit'])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Other Adjustments -->
        <div class="brs-table-wrap">
            <div class="tbl-header">
                <span class="tbl-title">Other Adjustments</span>
                <span class="tbl-total" id="adjustmentTotalLabel">Total: ₹0</span>
                <div class="tbl-actions">
                    <button onclick="addAdjustmentRow()"><i class="bi bi-plus"></i> Add</button>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="brs-table brs-table-adjustments" id="adjustmentsTable">
                    <thead><tr><th>SL</th><th>Date</th><th>Adjustment Type</th><th>Amount</th><th style="width:50px;">&nbsp;</th></tr></thead>
                    <tbody id="adjustmentsBodyRows">
                        @forelse($adjustments as $index => $adjustment)
                            @include('Backoffice.bank-reconciliation.components.adjustment-row', ['index' => $index, 'adjustment' => $adjustment])
                        @empty
                            @include('Backoffice.bank-reconciliation.components.empty-state', ['type' => 'adjustment'])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /brs-right -->
</div><!-- /brs-body -->