<tr class="dr">
    <td colspan="5" style="border-right:2px solid #4a6fa5;"></td>
    <td class="dc"></td>
    <td><div class="td-pt"><span class="bul">♦</span>{{ $label }}</div></td>
    <td class="td-rs">₹</td>
    <td class="td-am" contenteditable="true" id="{{ $id }}" oninput="calc()">{{ number_format($value, 0) }}</td>
    <td class="td-rs">₹</td>
    <td class="td-am cr" id="{{ $id }}T">-</td>
</tr>