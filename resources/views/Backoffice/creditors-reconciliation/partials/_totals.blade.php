<div class="cards-grid" id="creditorCards">
    <div class="stat-card">
        <div class="label">Total Creditors</div>
        <div class="value" id="cardTotalCreditors">{{ $creditors->count() }}</div>
        <div class="sub">Active creditors</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Bill Amount</div>
        <div class="value" id="cardTotalBill">₹{{ number_format($creditors->sum('bill_amount'), 0) }}</div>
        <div class="sub">All outstanding bills</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Amount Paid</div>
        <div class="value" id="cardTotalPaid">₹{{ number_format($creditors->sum('amount_paid'), 0) }}</div>
        <div class="sub">Payments made</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Outstanding Payable</div>
        <div class="value" id="cardTotalOutstanding">₹{{ number_format($creditors->sum(function($c) { return $c->bill_amount - $c->amount_paid; }), 0) }}</div>
        <div class="sub">Payable balance</div>
    </div>
</div>