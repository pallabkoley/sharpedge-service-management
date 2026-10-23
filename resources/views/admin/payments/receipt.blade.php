<h2 style="text-align:center;">Payment Receipt</h2>

<hr>

<p><strong>Receipt ID:</strong> {{ $payment->id }}</p>
<p><strong>Client:</strong> {{ $payment->client->name }}</p>
<p><strong>Amount:</strong> ₹{{ number_format($payment->amount, 2) }}</p>
<p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
<p><strong>Date:</strong> {{ $payment->payment_date }}</p>
<p><strong>Received By:</strong> {{ $payment->receivedByStaff?->name ?? 'System' }}</p>
<p><strong>Remarks:</strong> {{ $payment->remarks ?? '-' }}</p>

<hr>

<p style="text-align:center;">Thank You!</p>