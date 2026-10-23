<div class="section-header">
    <h5>DEBIT NOTE REGISTER</h5>
    <span class="badge-type">Dr.</span>
</div>
<div class="table-wrap">
    <table class="recon-table table-bordered table-striped" id="debitTable">
        <thead>
            <tr>
                <th style="width:8%;">Month</th>
                <th style="width:14%;">Taxable Value <span class="sub-label">(₹)</span></th>
                <th style="width:12%;">IGST <span class="sub-label">(₹)</span></th>
                <th style="width:12%;">CGST <span class="sub-label">(₹)</span></th>
                <th style="width:12%;">SGST <span class="sub-label">(₹)</span></th>
                <th style="width:15%;">Invoice Value <span class="sub-label">(₹)</span></th>
                <th style="width:12%;">Remarks</th>
                <th style="width:10%;">Breakup</th>
                <th style="width:5%;">&nbsp;</th>
            </tr>
        </thead>
        <tbody id="debitBody">
            {{-- ✅ ডামি ডেটা (JS কাজ না করলেও দেখা যাবে) --}}
            @php
                $months = ['Apr-25','May-25','Jun-25','Jul-25','Aug-25','Sep-25','Oct-25','Nov-25','Dec-25','Jan-26','Feb-26','Mar-26'];
                $tax = [2850000,3120000,2980000,3250000,3400000,3100000,3560000,3780000,3950000,4100000,3800000,4200000];
                $igst = array_fill(0, 12, 0);
                $cgst = [256500,280800,268200,292500,306000,279000,320400,340200,355500,369000,342000,378000];
                $sgst = [256500,280800,268200,292500,306000,279000,320400,340200,355500,369000,342000,378000];
                $gstRates = [18,5,12,28,18,12,18,5,12,18,5,12];
            @endphp
            @foreach($months as $i => $month)
                @php
                    $inv = $tax[$i] + $igst[$i] + $cgst[$i] + $sgst[$i];
                @endphp
                <tr class="month-row" data-row-id="" data-month="{{ $month }}">
                    <td class="td-month">{{ $month }}</td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="d_tax_{{ $i }}" value="{{ number_format($tax[$i], 2) }}" oninput="onRowInput('d', {{ $i }})" onfocus="this.select()" /></td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="d_igst_{{ $i }}" value="{{ number_format($igst[$i], 2) }}" oninput="onRowInput('d', {{ $i }})" onfocus="this.select()" /></td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="d_cgst_{{ $i }}" value="{{ number_format($cgst[$i], 2) }}" oninput="onRowInput('d', {{ $i }})" onfocus="this.select()" /></td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="d_sgst_{{ $i }}" value="{{ number_format($sgst[$i], 2) }}" oninput="onRowInput('d', {{ $i }})" onfocus="this.select()" /></td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="d_inv_{{ $i }}" value="{{ number_format($inv, 2) }}" readonly style="background:#f4f7fc;cursor:default;" /></td>
                    <td class="td-remark"><input type="text" class="form-control form-control-sm" id="d_rem_{{ $i }}" value="" placeholder="—" /></td>
                    <td class="td-breakup">
                        <button class="btn-breakup btn btn-sm btn-primary" onclick="openBreakupModal(null, '{{ $month }}', {{ $tax[$i] }})">
                            <i class="bi bi-list-ul"></i> Breakup
                        </button>
                    </td>
                    <td class="td-actions">
                        <button class="action-btn del-btn disabled" disabled><i class="bi bi-x-circle"></i></button>
                    </td>
                </tr>
            @endforeach
            {{-- Total Row --}}
            <tr class="total-row">
                <td>TOTAL</td>
                <td><span class="td-amt total" id="d_tot_taxable">-</span></td>
                <td><span class="td-amt total" id="d_tot_igst">-</span></td>
                <td><span class="td-amt total" id="d_tot_cgst">-</span></td>
                <td><span class="td-amt total" id="d_tot_sgst">-</span></td>
                <td><span class="td-amt total" id="d_tot_inv">-</span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
</div>