@if($side === 'debit')
    <tr class="st-r">
        <td colspan="3" style="border-right:1px solid #4a6fa5;font-family:var(--font-sans);font-weight:700;padding-left:8px;">{{ $label }}</td>
        <td class="td-rs" style="background:#dbeafe;color:#1565c0;">₹</td>
        <td class="td-am sub" id="{{ $id }}" style="border-right:2px solid #4a6fa5;">-</td>
        <td class="dc"></td>
        <td colspan="5"></td>
    </tr>
@else
    <tr class="st-r">
        <td colspan="5" style="border-right:2px solid #4a6fa5;"></td>
        <td class="dc"></td>
        <td colspan="3" style="border-right:1px solid #4a6fa5;font-family:var(--font-sans);font-weight:700;padding-left:8px;">{{ $label }}</td>
        <td class="td-rs" style="background:#dbeafe;color:#1565c0;">₹</td>
        <td class="td-am sub cr" id="{{ $id }}">-</td>
    </tr>
@endif