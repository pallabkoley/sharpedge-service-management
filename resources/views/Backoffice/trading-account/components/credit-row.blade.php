<tr class="dr {{ $row->row_type ?? 'system' }}" data-row-id="{{ $row->id ?? '' }}" data-side="cr">
    <td colspan="5" style="border-right:2px solid #4a6fa5;"></td>
    <td>
        <div class="td-part">
            <div class="main-line">
                @if(($row->row_type ?? 'system') === 'custom')
                    <input type="text" class="part-input" id="cr_part_{{ $row->id ?? 'new_'.$index }}" value="{{ $row->particular_name ?? 'New Item' }}" oninput="onRowChange('cr', '{{ $row->id ?? 'new_'.$index }}')" />
                    <span class="delete-btn" onclick="deleteRow('{{ $row->id ?? 'new_'.$index }}', 'cr')" title="Remove row"><i class="bi bi-x-circle-fill"></i></span>
                @else
                    <span>{{ $row->particular_name }}</span>
                @endif
            </div>
            <div class="source-text">{{ $row->source ?? '(Source: Manual Entry)' }}</div>
        </div>
    </td>
    <td class="td-rs">₹</td>
    <td class="td-amt {{ ($row->row_type ?? 'system') === 'custom' ? 'editable' : 'blank' }}" 
        contenteditable="{{ ($row->row_type ?? 'system') === 'custom' ? 'true' : 'false' }}" 
        id="{{ $row->id ?? 'cr_amt_'.$index }}"
        oninput="calc()">
        {{ $row->amount > 0 ? number_format($row->amount, 0) : '-' }}
    </td>
    <td class="td-rs">₹</td>
    <td class="td-amt blank"></td>
</tr>