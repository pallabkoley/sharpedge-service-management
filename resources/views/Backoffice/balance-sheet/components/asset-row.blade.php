<tr class="dr">
    <td class="td-rs">₹</td>
    <td class="td-am" 
        contenteditable="{{ $item->is_editable ? 'true' : 'false' }}" 
        data-id="{{ $item->id }}" 
        data-update-url="{{ route('backoffice.computations.balance-sheet.update-item', ['computation' => $balanceSheet->computation_id, 'item' => $item->id]) }}">
        {{ number_format($item->amount, 2) }}
    </td>
    <td>
        <div class="td-pt" style="align-items:flex-end;">
            <div class="main-line"><span class="bul">♦</span> {{ $item->label }}</div>
            @if($item->source_type)<div class="source-text" style="padding-right:18px;">(Source: {{ $item->source_type }})</div>@endif
        </div>
    </td>
</tr>