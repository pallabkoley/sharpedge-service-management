<tr class="dr {{ $row->row_type ?? 'system' }}" data-row-id="{{ $row->id ?? '' }}" data-side="dr">
    <td>
        <div class="td-part">
            <div class="main-line">
                <span class="bullet">♦</span>
                @if(($row->row_type ?? 'system') === 'custom')
                    <input type="text" class="part-input" id="dr_part_{{ $row->id ?? 'new_'.$index }}" value="{{ $row->particular_name ?? 'New Item' }}" oninput="onRowChange('dr', '{{ $row->id ?? 'new_'.$index }}')" />
                    <span class="delete-btn" onclick="deleteRow('{{ $row->id ?? 'new_'.$index }}', 'dr')" title="Remove row"><i class="bi bi-x-circle-fill"></i></span>
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
        id="{{ $row->id ?? 'dr_amt_'.$index }}"
        oninput="calc()">
        {{ $row->amount > 0 ? number_format($row->amount, 0) : '-' }}
    </td>
    <td class="td-rs"></td>
    <td class="td-amt blank" style="border-right:2px solid #4a6fa5;"></td>
    <td colspan="5"></td>
</tr>