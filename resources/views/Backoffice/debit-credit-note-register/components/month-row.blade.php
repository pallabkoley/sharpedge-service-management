@props(['idx', 'prefix', 'month', 'taxable' => 0, 'rate' => 18, 'igst' => 0, 'cgst' => 0, 'sgst' => 0, 'invoice' => 0, 'remark' => ''])

<tr class="month-row" id="row_{{ $prefix }}_{{ $idx }}">
    <td class="td-month">{{ $month }}</td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}tax_{{ $idx }}" value="{{ $taxable > 0 ? number_format($taxable, 2) : '' }}" oninput="onRowInput('{{ $prefix }}', {{ $idx }})" onfocus="this.select()" /></td>
    <td class="td-rate">
        <select id="{{ $prefix }}rate_{{ $idx }}" onchange="onRateChange('{{ $prefix }}', {{ $idx }})">
            @foreach([0, 5, 12, 18, 28, 40] as $r)
                <option value="{{ $r }}" {{ $r == $rate ? 'selected' : '' }}>{{ $r }}%</option>
            @endforeach
        </select>
    </td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}igst_{{ $idx }}" value="{{ $igst > 0 ? number_format($igst, 2) : '' }}" oninput="onRowInput('{{ $prefix }}', {{ $idx }})" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}cgst_{{ $idx }}" value="{{ $cgst > 0 ? number_format($cgst, 2) : '' }}" oninput="onRowInput('{{ $prefix }}', {{ $idx }})" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}sgst_{{ $idx }}" value="{{ $sgst > 0 ? number_format($sgst, 2) : '' }}" oninput="onRowInput('{{ $prefix }}', {{ $idx }})" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}inv_{{ $idx }}" value="{{ $invoice > 0 ? number_format($invoice, 2) : '' }}" readonly style="background:#f4f7fc;cursor:default;" /></td>
    <td class="td-remark"><input type="text" id="{{ $prefix }}rem_{{ $idx }}" value="{{ $remark }}" placeholder="—" /></td>
    <td class="td-actions"><button class="del-btn disabled" disabled><i class="bi bi-x-circle"></i></button></td>
</tr>