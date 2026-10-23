{{-- Single month row – used for the 12 fixed months --}}
@php
    $inv = $taxable + $igst + $cgst + $sgst + $cgstTds + $sgstTds;
@endphp
<tr class="month-row" id="row_month_{{ $index }}">
    <td class="td-month">{{ $month }}</td>
    <td><input class="td-amt" type="text" id="tax_{{ $index }}" value="{{ $taxable ? number_format($taxable,0,'.',',') : '' }}" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="igst_{{ $index }}" value="{{ $igst ? number_format($igst,0,'.',',') : '' }}" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="cgst_{{ $index }}" value="{{ $cgst ? number_format($cgst,0,'.',',') : '' }}" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="sgst_{{ $index }}" value="{{ $sgst ? number_format($sgst,0,'.',',') : '' }}" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="sgsttds_{{ $index }}" value="{{ $sgstTds ? number_format($sgstTds,0,'.',',') : '' }}" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="cgsttds_{{ $index }}" value="{{ $cgstTds ? number_format($cgstTds,0,'.',',') : '' }}" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="inv_{{ $index }}" value="{{ $inv ? number_format($inv,0,'.',',') : '' }}" oninput="calcTotal()" onfocus="this.select()" readonly style="background:#f4f7fc;cursor:default;" /></td>
    <td class="td-remark"><input type="text" id="rem_{{ $index }}" value="{{ $remark }}" placeholder="—" /></td>
    <td class="td-actions"><button class="del-btn disabled" disabled><i class="bi bi-x-circle"></i></button></td>
</tr>