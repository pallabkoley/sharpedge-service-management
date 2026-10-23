@props(['prefix'])

<tr class="total-row" id="totalRow_{{ $prefix }}">
    <td>TOTAL</td>
    <td><span class="td-amt total" id="{{ $prefix }}tot_taxable">-</span></td>
    <td>&nbsp;</td>
    <td><span class="td-amt total" id="{{ $prefix }}tot_igst">-</span></td>
    <td><span class="td-amt total" id="{{ $prefix }}tot_cgst">-</span></td>
    <td><span class="td-amt total" id="{{ $prefix }}tot_sgst">-</span></td>
    <td><span class="td-amt total" id="{{ $prefix }}tot_inv">-</span></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>