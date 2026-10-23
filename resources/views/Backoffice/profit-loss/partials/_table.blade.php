<table class="pl-tbl" id="plTable">
    <colgroup>
        <col style="width:30%">
        <col style="width:22px">
        <col style="width:9%">
        <col style="width:22px">
        <col style="width:9%">
        <col style="width:3px">
        <col style="width:30%">
        <col style="width:22px">
        <col style="width:9%">
        <col style="width:22px">
        <col style="width:9%">
    </colgroup>
    <thead>
        <tr>
            <th class="ch" colspan="5" style="border-right:2px solid #fff;">PARTICULARS &nbsp;|&nbsp; AMOUNT (Dr.)</th>
            <th style="background:#2a5aa0;border:none;padding:0;width:3px;"></th>
            <th class="ch" colspan="5">PARTICULARS &nbsp;|&nbsp; AMOUNT (Cr.)</th>
        </tr>
        <tr>
            <th class="ch" style="background:#0d2a5e;font-size:9.5px;">PARTICULARS</th>
            <th class="ch2">Rs</th><th class="ch2">AMOUNT</th>
            <th class="ch2">Rs</th><th class="ch2" style="border-right:2px solid #fff;">TOTAL</th>
            <th style="background:#2a5aa0;border:none;padding:0;"></th>
            <th class="ch" style="background:#0d2a5e;font-size:9.5px;">PARTICULARS</th>
            <th class="ch2">Rs</th><th class="ch2">AMOUNT</th>
            <th class="ch2">Rs</th><th class="ch2">TOTAL</th>
        </tr>
    </thead>
    <tbody id="plBody">

        {{-- GROSS PROFIT b/d --}}
        @include('Backoffice.profit-loss.components.gross-profit-row')

        {{-- SECTION 1: General Expenses --}}
        @include('Backoffice.profit-loss.components.section-header', ['sectionId' => 'sec1', 'title' => 'General Expenses', 'side' => 'debit'])
        <tbody class="sec-body" id="sec1">
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's1r1', 'label' => 'Freight outward', 'value' => 42000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's1r2', 'label' => 'Consumption of stores &amp; spares', 'value' => 118000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's1r3', 'label' => 'Rents', 'value' => 240000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's1r4', 'label' => 'Repairs to building', 'value' => 58000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's1r5', 'label' => 'Repairs to Machinery', 'value' => 34500])
        </tbody>
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 's1Tot', 'label' => 'Total General Expenses', 'side' => 'debit'])
        @include('Backoffice.profit-loss.components.income-row', ['id' => 'ci1', 'label' => 'Discount Received', 'value' => 65000])
        @include('Backoffice.profit-loss.components.income-row', ['id' => 'ci2', 'label' => 'Incentive Received', 'value' => 38500])
        @include('Backoffice.profit-loss.components.income-row', ['id' => 'ci3', 'label' => 'Interest on Security Deposit', 'value' => 12000])
        @include('Backoffice.profit-loss.components.income-row', ['id' => 'ci4', 'label' => 'Rent Received', 'value' => 96000])
        @include('Backoffice.profit-loss.components.income-row', ['id' => 'ci5', 'label' => 'Any Others', 'value' => 22000])
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 'ciTot', 'label' => 'Total Other Income', 'side' => 'credit'])

        {{-- SECTION 2: Compensation --}}
        @include('Backoffice.profit-loss.components.section-header', ['sectionId' => 'sec2', 'title' => 'Compensation to Employees', 'side' => 'debit'])
        <tbody class="sec-body" id="sec2">
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's2r1', 'label' => 'Salaries and wages', 'value' => 840000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's2r2', 'label' => 'Bonus', 'value' => 84000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's2r3', 'label' => 'Reimbursement of medical exp.', 'value' => 36000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's2r4', 'label' => 'Leave encashment', 'value' => 18000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's2r5', 'label' => 'Leave travel benefits', 'value' => 22000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's2r6', 'label' => 'Contribution to provident fund', 'value' => 100800])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's2r7', 'label' => 'Contribution to gratuity fund', 'value' => 42000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's2r8', 'label' => 'Other employee benefits', 'value' => 14000])
        </tbody>
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 's2Tot', 'label' => 'Total Compensation to Employees', 'side' => 'debit'])
        @include('Backoffice.profit-loss.components.income-row', ['id' => 'ci6', 'label' => 'Profit on Sale of Assets', 'value' => 125000])
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 'ci6Tot', 'label' => 'Total Others Profits', 'side' => 'credit'])

        {{-- SECTION 3: Insurance --}}
        @include('Backoffice.profit-loss.components.section-header', ['sectionId' => 'sec3', 'title' => 'Insurance', 'side' => 'debit'])
        <tbody class="sec-body" id="sec3">
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's3r1', 'label' => 'Medical Insurance', 'value' => 62000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's3r2', 'label' => 'Life Insurance', 'value' => 120000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's3r3', 'label' => 'Keyman Insurance', 'value' => 240000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's3r4', 'label' => 'Other Insurance', 'value' => 18000])
        </tbody>
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 's3Tot', 'label' => 'Total Expenditure on Insurance', 'side' => 'debit'])

        {{-- SECTION 4: Sundry Expenses --}}
        @include('Backoffice.profit-loss.components.section-header', ['sectionId' => 'sec4', 'title' => 'Sundry Expenses', 'side' => 'debit'])
        <tbody class="sec-body" id="sec4">
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's4r1', 'label' => 'Workmen welfare', 'value' => 28000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's4r2', 'label' => 'Entertainment', 'value' => 54000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's4r3', 'label' => 'Hospitality', 'value' => 36000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's4r4', 'label' => 'Conference', 'value' => 18500])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's4r5', 'label' => 'Sales promotion', 'value' => 72000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's4r6', 'label' => 'Advertisement', 'value' => 144000])
        </tbody>
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 's4Tot', 'label' => 'Total Sundry Expenses', 'side' => 'debit'])

        {{-- SECTION 5: Professional Fees --}}
        @include('Backoffice.profit-loss.components.section-header', ['sectionId' => 'sec5', 'title' => 'Professional / Consultancy Fees', 'side' => 'debit'])
        <tbody class="sec-body" id="sec5">
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's5r1', 'label' => 'Legal Fees', 'value' => 60000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's5r2', 'label' => 'Accounting Charges &amp; Audit', 'value' => 120000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's5r3', 'label' => 'Consultancy Charges', 'value' => 84000])
        </tbody>
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 's5Tot', 'label' => 'Total Professional Fees', 'side' => 'debit'])

        {{-- SECTION 6: Travel & Admin --}}
        @include('Backoffice.profit-loss.components.section-header', ['sectionId' => 'sec6', 'title' => 'Travel &amp; Administrative Expenses', 'side' => 'debit'])
        <tbody class="sec-body" id="sec6">
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's6r1', 'label' => 'Hotel &amp; Lodging', 'value' => 36000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's6r2', 'label' => 'Travelling Expenses', 'value' => 48000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's6r3', 'label' => 'Conveyance', 'value' => 24000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's6r4', 'label' => 'Telephone', 'value' => 18000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's6r5', 'label' => 'Guest House', 'value' => 30000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's6r6', 'label' => 'Club Expenses', 'value' => 12000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's6r7', 'label' => 'Festival Celebration', 'value' => 9000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's6r8', 'label' => 'Scholarship', 'value' => 6000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's6r9', 'label' => 'Donation', 'value' => 15000])
        </tbody>
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 's6Tot', 'label' => 'Total Travel &amp; Admin', 'side' => 'debit'])

        {{-- SECTION 7: Rates & Taxes --}}
        @include('Backoffice.profit-loss.components.section-header', ['sectionId' => 'sec7', 'title' => 'Rates &amp; Taxes', 'side' => 'debit'])
        <tbody class="sec-body" id="sec7">
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's7r1', 'label' => 'CGST', 'value' => 36400])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's7r2', 'label' => 'SGST', 'value' => 36400])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's7r3', 'label' => 'IGST', 'value' => 12000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's7r4', 'label' => 'UTGST', 'value' => 0])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's7r5', 'label' => 'Other taxes', 'value' => 8200])
        </tbody>
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 's7Tot', 'label' => 'Total Rates &amp; Taxes', 'side' => 'debit'])

        {{-- SECTION 8: Other Expenses --}}
        @include('Backoffice.profit-loss.components.section-header', ['sectionId' => 'sec8', 'title' => 'Other Expenses', 'side' => 'debit'])
        <tbody class="sec-body" id="sec8">
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's8r1', 'label' => 'Office Maintenance', 'value' => 48000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's8r2', 'label' => 'Printing &amp; Stationary', 'value' => 12000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's8r3', 'label' => 'Postage', 'value' => 3600])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's8r4', 'label' => 'Electricity Charges', 'value' => 96000])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's8r5', 'label' => 'Bank Charges', 'value' => 8400])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's8r6', 'label' => 'Books &amp; Periodicals', 'value' => 4200])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's8r7', 'label' => 'Loss on Sale of Assets', 'value' => 0])
            @include('Backoffice.profit-loss.components.expense-row', ['id' => 's8r8', 'label' => 'Profit Transfer to General Fund', 'value' => 0])
        </tbody>
        @include('Backoffice.profit-loss.components.subtotal-row', ['id' => 's8Tot', 'label' => 'Total Other Expenses', 'side' => 'debit'])

        {{-- DEPRECIATION & BAD DEBTS --}}
        @include('Backoffice.profit-loss.components.depreciation-row', ['id' => 'depAmt', 'label' => 'Depreciation', 'value' => 84000])
        @include('Backoffice.profit-loss.components.depreciation-row', ['id' => 'bdAmt', 'label' => 'Bad Debts', 'value' => 28000])

        {{-- NET PROFIT --}}
        @include('Backoffice.profit-loss.components.net-profit-row')

        {{-- GRAND TOTAL --}}
        @include('Backoffice.profit-loss.components.total-row')

    </tbody>
</table>