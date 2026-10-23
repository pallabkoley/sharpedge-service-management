<tr class="dep-r">
    <td colspan="2" style="border-right:1px solid #4a6fa5;font-family:var(--font-sans);font-weight:700;padding-left:8px;">♦ {{ $label }}</td>
    <td class="td-rs" style="background:#fce4ec;color:#880e4f;">₹</td>
    <td class="td-am" contenteditable="true" id="{{ $id }}" oninput="calc()" style="background:#fce4ec;color:#880e4f;font-weight:700;">{{ number_format($value, 0) }}</td>
    <td class="td-am blank" style="border-right:2px solid #4a6fa5;background:#fce4ec;"></td>
    <td class="dc"></td>
    <td colspan="5"></td>
</tr>