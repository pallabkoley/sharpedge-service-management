<tr data-adjustment-id="{{ $adjustment->id }}">
    <td style="text-align:center;font-weight:500;color:var(--text-muted);">{{ $index + 1 }}</td>
    <td>
        <input class="td-date" type="date" 
               id="adjDate_{{ $adjustment->id }}" 
               value="{{ $adjustment->adjustment_date ? \Carbon\Carbon::parse($adjustment->adjustment_date)->format('Y-m-d') : '' }}" 
               oninput="onAdjustmentChange({{ $adjustment->id }})" 
               style="width:100%;" />
    </td>
    <td>
        <select class="td-select" id="adjType_{{ $adjustment->id }}" onchange="onAdjustmentChange({{ $adjustment->id }})" style="width:100%;">
            <option value="Bank Charges" {{ $adjustment->adjustment_type == 'Bank Charges' ? 'selected' : '' }}>Bank Charges</option>
            <option value="Interest" {{ $adjustment->adjustment_type == 'Interest' ? 'selected' : '' }}>Interest</option>
            <option value="Bounce Charge" {{ $adjustment->adjustment_type == 'Bounce Charge' ? 'selected' : '' }}>Bounce Charge</option>
            <option value="NEFT Charge" {{ $adjustment->adjustment_type == 'NEFT Charge' ? 'selected' : '' }}>NEFT Charge</option>
            <option value="RTGS Charge" {{ $adjustment->adjustment_type == 'RTGS Charge' ? 'selected' : '' }}>RTGS Charge</option>
            <option value="Others" {{ $adjustment->adjustment_type == 'Others' || empty($adjustment->adjustment_type) ? 'selected' : '' }}>Others</option>
        </select>
    </td>
    <td>
        <input class="td-amt" type="text" 
               id="adjAmount_{{ $adjustment->id }}" 
               value="{{ number_format($adjustment->amount, 0) }}" 
               oninput="onAdjustmentChange({{ $adjustment->id }})" 
               style="width:100%;text-align:right;" />
    </td>
    <td class="td-actions">
        <button onclick="duplicateAdjustmentRow({{ $adjustment->id }})" title="Duplicate"><i class="bi bi-files"></i></button>
        <button class="del" onclick="deleteAdjustmentRow({{ $adjustment->id }})" title="Delete"><i class="bi bi-trash"></i></button>
    </td>
</tr>