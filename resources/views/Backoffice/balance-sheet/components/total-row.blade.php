@if($side === 'l')
    <tr class="st-r">
        <td style="font-family:var(--font-sans);font-weight:700;">Total {{ $section }}</td>
        <td class="td-rs" style="background:#dbeafe;">₹</td>
        <td class="td-am sub" data-total-id="l_{{ Str::slug($section) }}">-</td>
    </tr>
@else
    <tr class="st-r">
        <td class="td-rs" style="background:#dbeafe;">₹</td>
        <td class="td-am sub" data-total-id="a_{{ Str::slug($section) }}">-</td>
        <td style="font-family:var(--font-sans);font-weight:700;">Total {{ $section }}</td>
    </tr>
@endif