<div class="cards-grid" id="debtorCards">
    <div class="stat-card">
        <div class="label">Total Debtors</div>
        <div class="value" id="cardTotalDebtors">{{ $debtors->count() }}</div>
        <div class="sub">Active debtors</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Bill Amount</div>
        <div class="value" id="cardTotalBill">₹{{ number_format($debtors->sum('bill_amount'), 0) }}</div>
        <div class="sub">All outstanding bills</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Amount Received</div>
        <div class="value" id="cardTotalReceived">₹{{ number_format($debtors->sum('amount_received'), 0) }}</div>
        <div class="sub">Payments collected</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Outstanding</div>
        <div class="value" id="cardTotalOutstanding">₹{{ number_format($debtors->sum(function($d) { return $d->bill_amount - $d->amount_received; }), 0) }}</div>
        <div class="sub">Receivable balance</div>
    </div>
</div>