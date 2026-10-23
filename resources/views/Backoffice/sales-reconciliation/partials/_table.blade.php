<div class="table-wrap">
    <table class="recon-table" id="reconTable">
        <thead>
            <tr>
                <th style="min-width:70px;">Month</th>
                <th style="min-width:100px;">Taxable Value <span class="sub-label">(₹)</span></th>
                <th style="min-width:90px;">IGST <span class="sub-label">(₹)</span></th>
                <th style="min-width:90px;">CGST <span class="sub-label">(₹)</span></th>
                <th style="min-width:90px;">SGST <span class="sub-label">(₹)</span></th>
                <th style="min-width:90px;">SGST TDS <span class="sub-label">(₹)</span></th>
                <th style="min-width:90px;">CGST TDS <span class="sub-label">(₹)</span></th>
                <th style="min-width:110px;">Invoice Value <span class="sub-label">(₹)</span></th>
                <th style="min-width:120px;">Remarks</th>
                <th style="min-width:50px;width:50px;">&nbsp;</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @forelse ($registerRows as $row)
                <tr class="month-row" data-row-id="{{ $row->id }}" data-row-type="{{ $row->row_type }}">
                    <td class="td-month">
                        @if ($row->row_type === 'custom')
                            <input type="text" value="{{ $row->row_label }}" placeholder="Custom" class="custom-label-input" style="border:none;background:transparent;outline:none;font-weight:600;width:100%;font-size:11px;color:var(--text-dark);" />
                        @else
                            {{ $row->row_label }}
                        @endif
                    </td>
                    <td><input class="td-amt" type="text" value="{{ number_format($row->taxable_value, 0, '.', ',') }}" data-field="taxable_value" data-row-id="{{ $row->id }}" /></td>
                    <td><input class="td-amt" type="text" value="{{ number_format($row->igst, 0, '.', ',') }}" data-field="igst" data-row-id="{{ $row->id }}" /></td>
                    <td><input class="td-amt" type="text" value="{{ number_format($row->cgst, 0, '.', ',') }}" data-field="cgst" data-row-id="{{ $row->id }}" /></td>
                    <td><input class="td-amt" type="text" value="{{ number_format($row->sgst, 0, '.', ',') }}" data-field="sgst" data-row-id="{{ $row->id }}" /></td>
                    <td><input class="td-amt" type="text" value="{{ number_format($row->sgst_tds, 0, '.', ',') }}" data-field="sgst_tds" data-row-id="{{ $row->id }}" /></td>
                    <td><input class="td-amt" type="text" value="{{ number_format($row->cgst_tds, 0, '.', ',') }}" data-field="cgst_tds" data-row-id="{{ $row->id }}" /></td>
                    <td><input class="td-amt" type="text" value="{{ number_format($row->invoice_value, 0, '.', ',') }}" readonly style="background:#f4f7fc;cursor:default;" /></td>
                    <td class="td-remark"><input type="text" value="{{ $row->remarks }}" placeholder="—" data-field="remarks" data-row-id="{{ $row->id }}" /></td>
                    <td class="td-actions">
                        @if ($row->row_type === 'custom')
                            <button class="del-btn" onclick="deleteCustomRow({{ $row->id }})"><i class="bi bi-x-circle"></i></button>
                        @else
                            <button class="del-btn disabled" disabled><i class="bi bi-x-circle"></i></button>
                        @endif
                    </td>
                </tr>
            @empty
                {{-- Fallback: if no rows, show empty message --}}
                <tr><td colspan="10" style="text-align:center;padding:20px;">No data found. Please save draft to generate rows.</td></tr>
            @endforelse

            {{-- Total Row --}}
            <tr class="total-row" id="totalRow">
                <td>TOTAL</td>
                <td><span class="td-amt total" id="tot_taxable">{{ number_format($totals['taxable'] ?? 0, 0, '.', ',') }}</span></td>
                <td><span class="td-amt total" id="tot_igst">{{ number_format($totals['igst'] ?? 0, 0, '.', ',') }}</span></td>
                <td><span class="td-amt total" id="tot_cgst">{{ number_format($totals['cgst'] ?? 0, 0, '.', ',') }}</span></td>
                <td><span class="td-amt total" id="tot_sgst">{{ number_format($totals['sgst'] ?? 0, 0, '.', ',') }}</span></td>
                <td><span class="td-amt total" id="tot_sgsttds">{{ number_format($totals['sgst_tds'] ?? 0, 0, '.', ',') }}</span></td>
                <td><span class="td-amt total" id="tot_cgsttds">{{ number_format($totals['cgst_tds'] ?? 0, 0, '.', ',') }}</span></td>
                <td><span class="td-amt total" id="tot_inv">{{ number_format($totals['invoice'] ?? 0, 0, '.', ',') }}</span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
</div>