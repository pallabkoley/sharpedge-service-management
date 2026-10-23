<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice - {{ $invoiceNumber ?? 'INVOICE' }}</title>
    <style>
        /* Base styles for PDF */
        @page {
            margin: 5px !important;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px !important;
            line-height: 1.1;
            color: #000;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }
        
        /* Main container */
        .invoice-container {
            width: 100%;
            padding: 4px;
        }
        
        /* Header table - UPDATED STRUCTURE */
        .header-table {
            width: 98%;
            border-collapse: collapse;
            margin: 4px;
            border: 1px solid #000;
            table-layout: fixed;
        }
        
        .header-table td {
            border: 1px solid #000;
            padding: 4px 4px !important;
            vertical-align: top;
        }
        
        .header-table th {
            border: 1px solid #000;
            padding: 4px 4px !important;
            text-align: left;
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        /* Company info cell - UPDATED with logo */
        .company-info {
            width: 28%;
            font-size: 10px;
            padding: 6px !important;
        }
        
        /* Logo styling inside company block */
        .company-header {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            page-break-inside: avoid;
        }
        
        .logo-container {
            margin-top: 2px;
            flex-shrink: 0;
        }
        
        .company-logo {
            width: 80px;
            max-height: 70px;
            object-fit: contain;
        }
        
        .company-text {
            font-size: 9.5px;
            line-height: 1.2;
            flex: 1;
        }
        
        /* Invoice title */
        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 6px 0;
        }
        
        /* Details tables */
        .details-table {
            width: 98%;
            border-collapse: collapse;
            margin: 4px;
        }
        
        .details-table td {
            border: 1px solid #000;
            padding: 4px 4px !important;
            vertical-align: top;
        }
        
        /* Items table - COMPACT */
        .items-table {
            width: 98%;
            border-collapse: collapse;
            margin: 4px;
            font-size: 10px !important;
            table-layout: fixed;
        }
        
        .items-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 4px 4px !important;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table td {
            border: 1px solid #000;
            padding: 4px 4px !important;
            vertical-align: top;
        }
        
        /* Amount table - COMPACT */
        .amount-table {
            width: 200px;
            border-collapse: collapse;
            float: right;
            margin: 4px;
            margin-right:8px;
            font-size: 10px;
        }
        
        .amount-table td {
            padding: 4px 4px !important;
            border: none;
        }
        
        .amount-table tr:last-child td {
            border-top: 1px solid #000;
            font-weight: bold;
        }
        
        /* Footer - COMPACT */
        .footer {
            clear: both;
            margin-top: 5px;
            font-size: 10px !important;
        }
        
        .signature-section {
            margin-top: 5px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 150px;
            margin-top: 15px !important;
        }
        
        /* NEW: Status and Due Amount Styling */
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        
        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-partial {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .status-pending {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* New: Rejected status */
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .due-amount {
            font-weight: bold;
        }
        
        .due-positive {
            color: #dc3545;
        }
        
        .due-zero {
            color: #28a745;
        }
        
        .status-info-table {
            width: 100%;
            margin-top: 2px;
            font-size: 9px;
            border-collapse: collapse;
        }
        
        .status-info-table td {
            padding: 2px 4px;
            border: 1px solid #ddd;
        }
        
        .status-label {
            font-weight: bold;
            background-color: #f8f9fa;
            width: 30%;
        }
        
        /* Utility classes */
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .border-none {
            border: none !important;
        }
        
        .w-100 {
            width: 100%;
        }
        
        .no-margin {
            margin: 0 !important;
        }
        
        .no-padding {
            padding: 0 !important;
        }
        
        .compact {
            margin: 1px 0 !important;
            padding: 1px !important;
        }
        
        /* Clearfix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        .company-info {
            border: none !important;
        }
        
        /* Table cell specific widths */
        .sl-no { width: 3%; }
        .description { width: 30%; }
        .hsn { width: 8%; }
        .qty { width: 6%; }
        .rate { width: 10%; }
        .per { width: 6%; }
        .discount { width: 10%; }
        .amount { width: 10%; }
        
        /* Round off styling */
        .round-off-positive {
            color: #28a745;
        }
        .round-off-negative {
            color: #dc3545;
        }
    </style>
</head>
<body>
    @php
        // ============================================
        // SAFE FALLBACKS FOR ALL VARIABLES (CRITICAL)
        // ============================================
        // Ensure $order is always an object (even if null)
        $order = $order ?? null;

        // Latest payment
        $latestPayment = $latestPayment ?? (optional($order)->payments->first() ?? null);
        // FIX: Define $payment to avoid undefined variable errors later
        $payment = $latestPayment;

        // GST related
        $gstRate   = $gstRate   ?? config('app.gst_percentage', 18);
        $cgstRate  = $gstRate / 2;
        $sgstRate  = $gstRate / 2;
        $cgst      = $cgst ?? 0;
        $sgst      = $sgst ?? 0;

        // Invoice financials (use passed values, fallback to order or zero)
        $govtFee      = $govtFee      ?? ($order->final_govt_fee ?? $order->estimated_govt_fee ?? 0);
        $serviceFee   = $serviceFee   ?? ($order->base_price ?? 0);
        $discount     = $discount     ?? ($order->discount_amount ?? 0);
        $taxableAmount= $taxableAmount?? ($order->taxable_amount ?? 0);

        // Use order's final_amount and round_off if available (AUTHORITATIVE)
        $invoiceGrandTotal = $order->final_amount ?? $invoiceGrandTotal ?? 0;
        $invoiceRoundOff   = $order->round_off   ?? $invoiceRoundOff   ?? 0;

        // Invoice status flags (authoritative)
        $isFinal    = $isFinal    ?? (optional($order)->invoice_type === 'FINAL');
        $isRevised  = $isRevised  ?? (optional($order)->invoice_type === 'REVISED');
        
        // FIX 1: Rejection detection based on order_status, not payment
        $isRejected = $isRejected ?? (optional($order)->order_status === 'REJECTED');

        // FIX 2: Determine order status for badge (authoritative)
        $orderStatus = optional($order)->order_status ?? 'PENDING';
        
        // Map order status to display class and text
        $statusMap = [
            'PAID'            => ['class' => 'status-paid',    'text' => 'PAID'],
            'PARTIAL_PAYMENT' => ['class' => 'status-partial', 'text' => 'PARTIAL PAYMENT'],
            'REJECTED'        => ['class' => 'status-rejected','text' => 'REJECTED'],
            'PENDING'         => ['class' => 'status-pending', 'text' => 'PENDING'],
        ];
        $displayClass = $statusMap[$orderStatus]['class'] ?? 'status-pending';
        $displayText  = $statusMap[$orderStatus]['text']  ?? $orderStatus;

        // Rejection reason (still available for payment details, but not for badge)
        $rejectionReason = null;
        if (optional($latestPayment)->status === 'rejected' && optional($latestPayment)->rejection_reason) {
            $rejectionReason = $latestPayment->rejection_reason;
        }

        // FIX 3: Correct due calculation � NO DATABASE UPDATES HERE!
        $paidAmount = $order->paid_amount ?? 0;
        $dueAmount = $order->balance_amount ?? max($invoiceGrandTotal - $paidAmount, 0);

        // ============================================
        // INVOICE NUMBER LOGIC (UPDATED)
        // ============================================
        // Determine the date for financial year calculation (use order date if available)
        $invoiceDate = optional($order)->created_at ?? now();
        $year = $invoiceDate->year;
        $month = $invoiceDate->month;

        // Financial year runs from April to March
        if ($month <= 3) {
            $startYear = $year - 1;
            $endYear = $year;
        } else {
            $startYear = $year;
            $endYear = $year + 1;
        }

        // Format: 2025-26
        $financialYear = $startYear . '-' . substr($endYear, -2);

        // Final invoice number:
        // If $invoiceNumber is passed (e.g., from controller) use it.
        // Else if order already has an invoice_number, use that.
        // Otherwise generate a new one with financial year and a placeholder sequence (0034).
        // Replace '0034' with your actual sequence logic (e.g., next number from a counter).
        $invoiceNumber = $invoiceNumber 
            ?? (optional($order)->invoice_number 
                ?? 'TSE/' . $financialYear . '/0034');
    @endphp

    <!-- OPTIONAL: REJECTED WATERMARK -->
    @if($isRejected)
    <div style="
        position: fixed;
        top: 45%;
        left: 15%;
        transform: rotate(-30deg);
        font-size: 80px;
        color: rgba(220,53,69,0.15);
        font-weight: bold;
        z-index: -1;
        pointer-events: none;
    ">
        REJECTED
    </div>
    @endif

    <div class="invoice-container">
        <!-- Title with status - FIX 3: Use order_status for rejection, keep final/revised flags -->
        <div class="invoice-title">
            TAX INVOICE
            @if($isFinal)
                (FINAL)
            @elseif($orderStatus === 'REJECTED')
                (REJECTED)
            @elseif($isRevised)
                (REVISED)
            @endif
        </div>
        
        <!-- Main Header Table -->
        <table class="header-table">
            <tr>
                <!-- LEFT SECTION: Company Block with Logo -->
                <td rowspan="5" class="company-info">
                    <div class="company-header">
                        <!-- Logo Container -->
                        <div class="logo-container">
                            @php
                                $logoPath = public_path('sharpedge_logo/Sharpedge.jpeg');
                            @endphp

                            @if(file_exists($logoPath))
                                <img
                                    src="{{ $logoPath }}"
                                    class="company-logo"
                                    alt="SHARPEDGE SERVICES Logo"
                                >
                            @else
                                <div style="width: 90px; height: 80px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; text-align: center; background: #f5f5f5;">
                                    TSE INDIA<br>SERVICES PVT LTD
                                </div>
                            @endif
                        </div>
                        
                        <!-- Company Text Details -->
                        <div class="company-text">
                            <div style="font-size: 10px; font-weight: bold; margin-bottom: 2px;">
                                {{ config('app.company_name', 'TSE India Services Private Limited') }}
                            </div>

                            <div style="margin-bottom: 1px; line-height: 1.1;">
                                {{ config('app.company_address', 'Bhagirathi Plaza, 1/A Bhagirathi Lane') }}<br>
                                {{ config('app.company_location', 'Manikata Bazar, Serampur Uttarpura') }}<br>
                                {{ config('app.company_city', 'Hooghly') }} - {{ config('app.company_pincode', '712201') }}<br>
                                GSTIN/UIN: {{ config('app.gst_number', '19AALCT1411G1ZG') }}<br>
                                State: {{ config('app.state_name', 'West Bengal') }}, Code: {{ config('app.state_code', '19') }}
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- RIGHT SECTION: Invoice Details Block -->
               <th style ="width: 70px;">Invoice No.</th>
                <td style="width: 90px;">{{ $invoiceNumber }}</td>
                <th style="width: 80px;">Terms of Payment</th>
                <td style="width: 80px;">{{ optional($latestPayment)->payment_method ?? 'Cash' }}</td>
            </tr>
            
            <tr>
                <th>Dated</th>
                <td>
                    @php
                        $createdAt = optional($order)->created_at ?? null;
                        if ($createdAt instanceof \Carbon\Carbon) {
                            echo $createdAt->format('d-M-y');
                        } elseif (is_string($createdAt) && !empty($createdAt)) {
                            echo \Carbon\Carbon::parse($createdAt)->format('d-M-y');
                        } else {
                            echo now()->format('d-M-y');
                        }
                    @endphp
                </td>
                <th>Reference No.</th>
                <td>{{ optional($order)->reference_number ?? '' }}</td>
            </tr>
        
            <tr>
                <th>Order No.</th>
                <td>{{ optional($order)->order_id ?? 'N/A' }}</td>
                <th>Dated</th>
                <td>
                    @php
                        $orderDate = optional($order)->created_at ?? null;
                        if ($orderDate instanceof \Carbon\Carbon) {
                            echo $orderDate->format('d-M-y');
                        } elseif (is_string($orderDate) && !empty($orderDate)) {
                            echo \Carbon\Carbon::parse($orderDate)->format('d-M-y');
                        } else {
                            echo '';
                        }
                    @endphp
                </td>
            </tr>
            
            <tr>
                <th>Buyer's GSTIN</th>
                <td>{{ optional($order->client)->gst_number ?? 'N/A' }}</td>
                <th>Sales Person</th>
                <td>{{ optional($order->marketingStaff)->name ?? '' }}</td>
            </tr>
            
            <tr>
                <th>Destination</th>
                <td colspan="3">{{ optional($order->client)->city ?? '' }}, {{ optional($order->client)->state ?? '' }}</td>
            </tr>
        </table>
        
        <!-- Buyer Details - Single row only -->
        <table class="details-table">
            <tr>
                <td>
                    <div style="font-weight: bold; margin-bottom: 1px;">
                        Buyer (Bill to)
                    </div>

                    <div style="line-height: 1.1;">
                        <strong>M/S:</strong> {{ optional($order->client)->name ?? 'N/A' }}<br>

                        <strong>Address:</strong>
                        {{ optional($order->client)->address ?? 'N/A' }}<br>

                        {{ optional($order->client)->city ?? '' }},
                        {{ optional($order->client)->state ?? '' }}
                        {{ optional($order->client)->pincode ?? '' }}<br>

                        <strong>GSTIN:</strong>
                        {{ optional($order->client)->gst_number ?? 'N/A' }}
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Order Status and Due Amount Section - FIX 2: Use order_status for badge -->
        <table class="status-info-table">
            <tr>
                <td class="status-label">Order Status:</td>
                <td>
                    <span class="status-badge {{ $displayClass }}">
                        {{ $displayText }}
                    </span>
                </td>

                <td class="status-label">Total Amount:</td>
                <td>₹{{ number_format($invoiceGrandTotal, 2) }}</td>
            </tr>

            <tr>
                <td class="status-label">Paid Amount:</td>
                <td>₹{{ number_format($paidAmount, 2) }}</td>

                <td class="status-label">Due Amount:</td>
                <td>
                    <span class="due-amount {{ $dueAmount > 0 ? 'due-positive' : 'due-zero' }}">
                        ₹{{ number_format($dueAmount, 2) }}
                        <small>
                            ({{ $dueAmount > 0 ? 'Payment Pending' : 'Fully Paid' }})
                        </small>
                    </span>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="sl-no">Sl No.</th>
                    <th class="description">Description of Goods / Services</th>
                    <th class="hsn">HSN/SAC</th>
                    <th class="qty">Qty</th>
                    <th class="rate">Rate</th>
                    <th class="per">Per</th>
                    <th class="discount">Discount</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $itemCounter = 1;
                    $totalItems = count($order->items ?? []);
                    $itemsSubtotal = 0;
                    $itemsDiscount = 0;
                @endphp
                
                @forelse($order->items ?? [] as $item)
                    @php
                        $itemAmount = $item->amount ?? (($item->rate ?? $item->price ?? 0) * ($item->quantity ?? 1));
                        $itemDiscount = $item->discount ?? 0;
                        $itemsSubtotal += $itemAmount + $itemDiscount; // Add discount back to get original subtotal
                        $itemsDiscount += $itemDiscount;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $itemCounter++ }}</td>
                        <td>{{ Str::limit($item->service->service_name ?? $item->description ?? 'Service', 50) }}</td>
                        <td class="text-center">{{ $item->service->hsn_code ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                        <td class="text-right">₹{{ number_format($item->rate ?? $item->price ?? 0, 2) }}</td>
                        <td class="text-center">{{ $item->unit ?? 'Unit' }}</td>
                        <td class="text-right">
                            @if(isset($item->discount) && $item->discount > 0)
                                ₹{{ number_format($item->discount, 2) }}
                            @else
                                ₹0.00
                            @endif
                        </td>
                        <td class="text-right">₹{{ number_format($itemAmount, 2) }}</td>
                    </tr>
                @empty
                    @php
                        // Use values passed from controller for empty items (already set in fallbacks)
                    @endphp
                    <tr>
                        <td class="text-center">1</td>
                        <td>Service Fee</td>
                        <td class="text-center">-</td>
                        <td class="text-center">1</td>
                        <td class="text-right">₹{{ number_format($serviceFee, 2) }}</td>
                        <td class="text-center">Unit</td>
                        <td class="text-right">₹{{ number_format($discount, 2) }}</td>
                        <td class="text-right">₹{{ number_format($taxableAmount, 2) }}</td>
                    </tr>
                @endforelse
                
                <!-- Only add empty rows if absolutely necessary -->
                @if($totalItems < 2)
                    @for($i = $totalItems; $i < 1; $i++)
                        <tr>
                            <td class="text-center">{{ $itemCounter++ }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                @endif
                
                <!-- Total Row -->
                <tr>
                    <td colspan="6" class="text-right text-bold" style="border: none; padding: 4px !important;">Total Amount:</td>
                    <td colspan="2" class="text-right text-bold" style="border-left: 1px solid #000;padding: 4px !important;">
                        ₹{{ number_format($serviceFee ?? $itemsSubtotal, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Government Fee Breakdown -->
        @if($govtFee > 0)
        <table style="width: 98%; margin: 2px; font-size: 10px; border-collapse: collapse;">
            <tr>
                <td style="width: 70%; padding: 2px; border: 1px solid #ccc; vertical-align: top; line-height: 1.1;">
                    <strong>Government Fee Details:</strong><br>
                    @if($isRevised)
                        Revised Government Fee: ₹{{ number_format($govtFee, 2) }}<br>
                        <em>Previously: ₹{{ number_format($order->getOriginal('final_govt_fee') ?? $order->estimated_govt_fee ?? 0, 2) }}</em>
                    @else
                        Estimated Government Fee: ₹{{ number_format($order->estimated_govt_fee ?? 0, 2) }}<br>
                        Final Government Fee: ₹{{ number_format($govtFee, 2) }}
                    @endif
                </td>
                <td style="width: 30%; padding: 2px; border: 1px solid #ccc; vertical-align: top; line-height: 1.1;">
                    <strong>Government Fee:</strong><br>
                    ₹{{ number_format($govtFee, 2) }}
                </td>
            </tr>
        </table>
        @endif

        <!-- Compact Declaration and Amount Section -->
        <div style="clear: both; margin-top: 2px;">
            <!-- Right side - Compact Amount Breakdown -->
            <table class="amount-table">
                <tr>
                    <td>Service Fee:</td>
                    <td class="text-right">₹{{ number_format($serviceFee, 2) }}</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">₹{{ number_format($discount, 2) }}</td>
                </tr>
                <tr>
                    <td>Taxable Amount:</td>
                    <td class="text-right">₹{{ number_format($taxableAmount, 2) }}</td>
                </tr>
                <tr>
                    <td>CGST @ {{ number_format($cgstRate, 2) }}%:</td>
                    <td class="text-right">₹{{ number_format($cgst, 2) }}</td>
                </tr>
                <tr>
                    <td>SGST @ {{ number_format($sgstRate, 2) }}%:</td>
                    <td class="text-right">₹{{ number_format($sgst, 2) }}</td>
                </tr>
                @if($govtFee > 0)
                <tr>
                    <td>Government Fee:</td>
                    <td class="text-right">₹{{ number_format($govtFee, 2) }}</td>
                </tr>
                @endif
                <!-- ROUND OFF ROW (using authoritative order values) -->
                <tr>
                    <td>Round Off:</td>
                    <td class="text-right">
                        <span class="{{ $invoiceRoundOff >= 0 ? 'round-off-positive' : 'round-off-negative' }}">
                            {{ $invoiceRoundOff >= 0 ? '+' : '-' }}
                            ₹{{ number_format(abs($invoiceRoundOff), 2) }}
                        </span>
                    </td>
                </tr>
                <!-- GRAND TOTAL (authoritative from order) -->
                <tr>
                    <td class="text-bold">Grand Total:</td>
                    <td class="text-right text-bold">₹{{ number_format($invoiceGrandTotal, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- COMPACT Footer Section -->
        <div class="footer">
            <!-- Compact Bank & Payment Details -->
            <table style="width: 98%; margin: 2px 0; font-size: 10px; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding: 2px; border: 1px solid #ccc; vertical-align: top; line-height: 1.1;">
                        <strong>Bank Details:</strong><br>
                        Bank: State Bank of India<br>
                        A/C: TSE India Services Private Limited<br>
                        A/C No: 43258879826<br>
                        IFSC: SBIN000178
                    </td>

                    <td style="width: 50%; padding: 2px; border: 1px solid #ccc; vertical-align: top; line-height: 1.1;">
                        @php
                            // $payment is already defined at the top
                        @endphp
                        @if($payment)
                            <strong>Payment Details:</strong><br>
                            ID: {{ $payment->transaction_id ?? $payment->id }}<br>
                            Amount: ₹{{ number_format($payment->amount ?? $invoiceGrandTotal, 2) }}<br>
                            Method: {{ ucfirst($payment->payment_method ?? 'Cash') }}<br>
                            Status: 
                            @if($payment->status === 'rejected')
                                <span style="color: #dc3545; font-weight: bold;">REJECTED</span>
                                @if($payment->rejection_reason)
                                    <br>Reason: {{ $payment->rejection_reason }}
                                @endif
                            @else
                                {{ ucfirst($payment->status ?? 'Approved') }}
                            @endif
                            <br>
                            @if($payment->status === 'approved')
                                Approved: 
                                @php
                                    $approvedAt = $payment->approved_at ?? null;
                                    if ($approvedAt instanceof \Carbon\Carbon) {
                                        echo $approvedAt->format('d-M-y H:i');
                                    } elseif (is_string($approvedAt) && !empty($approvedAt)) {
                                        echo \Carbon\Carbon::parse($approvedAt)->format('d-M-y H:i');
                                    } else {
                                        echo now()->format('d-M-y H:i');
                                    }
                                @endphp
                            @elseif($payment->status === 'rejected')
                                Rejected: 
                                @php
                                    $rejectedAt = $payment->rejected_at ?? null;
                                    if ($rejectedAt instanceof \Carbon\Carbon) {
                                        echo $rejectedAt->format('d-M-y H:i');
                                    } elseif (is_string($rejectedAt) && !empty($rejectedAt)) {
                                        echo \Carbon\Carbon::parse($rejectedAt)->format('d-M-y H:i');
                                    } else {
                                        echo '';
                                    }
                                @endphp
                            @endif
                        @else
                            <strong>Payment Details:</strong><br>
                            Amount: ₹{{ number_format($invoiceGrandTotal, 2) }}<br>
                            Status: Pending<br>
                            Date: {{ now()->format('d-M-y H:i') }}
                        @endif
                    </td>
                </tr>
            </table>

            <!-- Compact Signature Section -->
            <div class="signature-section">
                <table style="width: 98%;">
                    <tr>
                        <td style="width: 50%;">
                            <div style="margin-top: 40px;">
                                <div class="signature-line"></div>
                                <div style="font-size: 12px;">Customer's Signature</div>
                            </div>
                        </td>
                        <td style="width: 50%; text-align: right;">
                            <div style="margin-top: 40px;">
                                <div class="signature-line" style="margin-left: auto;"></div>
                                <div style="font-size: 12px;">For {{ config('app.company_name', 'TSE India Services Private Limited') }}</div>
                                <div style="font-size: 12px; font-weight: bold;">Authorized Signatory</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Ultra Compact Footer Note -->
            <div style="text-align: center; margin-top: 3px; font-size: 10px; color: #666; line-height: 1.1;">
                <div>This is a Computer Generated Invoice</div>
                <div>Invoice generated: {{ now()->format('d-M-Y H:i') }}</div>
                @if($isRevised)
                    <div style="color: #d9534f; font-weight: bold;">REVISED INVOICE</div>
                @endif
                @if(optional($order)->verified_by && optional($order)->verified_at)
                    <div>Verified By: {{ $order->verified_by_name ?? 'Admin' }} on {{ $order->verified_at->format('d-M-Y H:i') }}</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>