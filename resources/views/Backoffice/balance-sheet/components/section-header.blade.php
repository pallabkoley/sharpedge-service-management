<tr class="sh-r">
    @if($side === 'l')
        <td colspan="3">
            <span style="font-weight:700;color:#0d2a5e;">{{ $section }}</span>
            <span class="sh-toggle" onclick="tog('{{ $side }}_{{ Str::slug($section) }}')"><i class="bi bi-dash-square"></i></span>
        </td>
    @else
        <td colspan="3" style="text-align:right;">
            <span class="sh-toggle" onclick="tog('{{ $side }}_{{ Str::slug($section) }}')"><i class="bi bi-dash-square"></i></span>
            <span style="font-weight:700;color:#0d2a5e;">{{ $section }}</span>
        </td>
    @endif
</tr>