@props(['idx', 'prefix', 'month', 'taxable' => 0, 'rate' => 18, 'igst' => 0, 'cgst' => 0, 'sgst' => 0, 'invoice' => 0, 'remark' => ''])

<tr class="month-row" id="row_{{ $prefix }}_custom_{{ $idx }}">
    <td class="td-month">
        <input type="text" value="{{ $month }}" placeholder="Month" style="border:none;background:transparent;outline:none;font-weight:600;width:100%;font-size:11px;color:#1a2332;" onfocus="this.select()" onchange="updateCustomMonth('{{ $prefix }}', {{ $idx }}, this.value)" />
    </td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}tax_custom_{{ $idx }}" value="{{ $taxable > 0 ? number_format($taxable, 2) : '' }}" oninput="onRowInput('{{ $prefix }}', 'custom_{{ $idx }}')" onfocus="this.select()" /></td>
    <td class="td-rate">
        <select id="{{ $prefix }}rate_custom_{{ $idx }}" onchange="onRateChange('{{ $prefix }}', 'custom_{{ $idx }}')">
            @foreach([0, 5, 12, 18, 28, 40] as $r)
                <option value="{{ $r }}" {{ $r == $rate ? 'selected' : '' }}>{{ $r }}%</option>
            @endforeach
        </select>
    </td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}igst_custom_{{ $idx }}" value="{{ $igst > 0 ? number_format($igst, 2) : '' }}" oninput="onRowInput('{{ $prefix }}', 'custom_{{ $idx }}')" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}cgst_custom_{{ $idx }}" value="{{ $cgst > 0 ? number_format($cgst, 2) : '' }}" oninput="onRowInput('{{ $prefix }}', 'custom_{{ $idx }}')" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}sgst_custom_{{ $idx }}" value="{{ $sgst > 0 ? number_format($sgst, 2) : '' }}" oninput="onRowInput('{{ $prefix }}', 'custom_{{ $idx }}')" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="{{ $prefix }}inv_custom_{{ $idx }}" value="{{ $invoice > 0 ? number_format($invoice, 2) : '' }}" readonly style="background:#f4f7fc;cursor:default;" /></td>
    <td class="td-remark"><input type="text" id="{{ $prefix }}rem_custom_{{ $idx }}" value="{{ $remark }}" placeholder="—" /></td>
    <td class="td-actions"><button class="del-btn" onclick="deleteCustomRow('{{ $prefix }}', {{ $idx }})"><i class="bi bi-x-circle"></i></button></td>
</tr>