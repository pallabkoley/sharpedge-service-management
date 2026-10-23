<tr class="sub-row">
    @if($side === 'debit')
        <td colspan="3">
            <div style="padding:2px 8px;font-weight:600;color:#0d47a1;font-family:var(--font-sans);">{{ $label }}</div>
        </td>
        <td class="td-rs">₹</td>
        <td class="td-amt sub" id="{{ $id }}" style="border-right:2px solid #4a6fa5;">-</td>
        <td colspan="5"></td>
    @else
        <td colspan="5" style="border-right:2px solid #4a6fa5;"></td>
        <td colspan="5" class="sub-row">
            <div style="display:flex;justify-content:space-between;padding:2px 8px;font-weight:600;color:#0d47a1;font-family:var(--font-sans);">
                <span>{{ $label }}</span>
                <span style="font-family:var(--font-mono);" id="{{ $id }}">-</span>
            </div>
        </td>
    @endif
</tr>