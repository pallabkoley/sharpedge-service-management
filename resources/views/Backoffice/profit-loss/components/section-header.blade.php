<tr class="sh-r" id="sec{{ $sectionId }}-hd">
    <td colspan="5" style="border-right:2px solid #4a6fa5;">
        <span class="sh-lnk">{{ $title }}</span>
        <span class="sh-toggle" onclick="toggleSec('{{ $sectionId }}')"><i class="bi bi-dash-square" id="{{ $sectionId }}-ico"></i></span>
    </td>
    <td class="dc"></td>
    <td colspan="5">@if($side === 'credit')<span class="sh-lnk">{{ $title }}</span>@endif</td>
</tr>