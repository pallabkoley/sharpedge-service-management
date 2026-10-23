<div class="section-header">
    <h5>CREDIT NOTE REGISTER</h5>
    <span class="badge-type">Cr.</span>
</div>
<div class="table-wrap">
    <table class="recon-table table-bordered table-striped" id="creditTable">
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
        <tbody id="creditBody">
            @php
                $months = ['Apr-25','May-25','Jun-25','Jul-25','Aug-25','Sep-25','Oct-25','Nov-25','Dec-25','Jan-26','Feb-26','Mar-26'];
                $tax = [1850000,2120000,1980000,2250000,2400000,2100000,2560000,2780000,2950000,3100000,2800000,3200000];
                $igst = array_fill(0, 12, 0);
                $cgst = [166500,190800,178200,202500,216000,189000,230400,250200,265500,279000,252000,288000];
                $sgst = [166500,190800,178200,202500,216000,189000,230400,250200,265500,279000,252000,288000];
                $gstRates = [18,5,12,28,18,12,18,5,12,18,5,12];
            @endphp
            @foreach($months as $i => $month)
                @php
                    $inv = $tax[$i] + $igst[$i] + $cgst[$i] + $sgst[$i];
                @endphp
                <tr class="month-row" data-row-id="" data-month="{{ $month }}">
                    <td class="td-month">{{ $month }}</td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="c_tax_{{ $i }}" value="{{ number_format($tax[$i], 2) }}" oninput="onRowInput('c', {{ $i }})" onfocus="this.select()" /></td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="c_igst_{{ $i }}" value="{{ number_format($igst[$i], 2) }}" oninput="onRowInput('c', {{ $i }})" onfocus="this.select()" /></td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="c_cgst_{{ $i }}" value="{{ number_format($cgst[$i], 2) }}" oninput="onRowInput('c', {{ $i }})" onfocus="this.select()" /></td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="c_sgst_{{ $i }}" value="{{ number_format($sgst[$i], 2) }}" oninput="onRowInput('c', {{ $i }})" onfocus="this.select()" /></td>
                    <td><input class="td-amt form-control form-control-sm" type="text" id="c_inv_{{ $i }}" value="{{ number_format($inv, 2) }}" readonly style="background:#f4f7fc;cursor:default;" /></td>
                    <td class="td-remark"><input type="text" class="form-control form-control-sm" id="c_rem_{{ $i }}" value="" placeholder="—" /></td>
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
            <tr class="total-row">
                <td>TOTAL</td>
                <td><span class="td-amt total" id="c_tot_taxable">-</span></td>
                <td><span class="td-amt total" id="c_tot_igst">-</span></td>
                <td><span class="td-amt total" id="c_tot_cgst">-</span></td>
                <td><span class="td-amt total" id="c_tot_sgst">-</span></td>
                <td><span class="td-amt total" id="c_tot_inv">-</span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
</div>