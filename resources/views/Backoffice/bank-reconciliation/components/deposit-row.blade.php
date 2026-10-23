<tr data-deposit-id="{{ $deposit->id }}">
    <td style="text-align:center;font-weight:500;color:var(--text-muted);">{{ $index + 1 }}</td>
    <td>
        <input class="td-date" type="date" 
               id="depDate_{{ $deposit->id }}" 
               value="{{ $deposit->deposit_date ? \Carbon\Carbon::parse($deposit->deposit_date)->format('Y-m-d') : '' }}" 
               oninput="onDepositChange({{ $deposit->id }})" 
               style="width:100%;" />
    </td>
    <td>
        <input class="td-amt" type="text" 
               id="depAmount_{{ $deposit->id }}" 
               value="{{ number_format($deposit->amount, 0) }}" 
               oninput="onDepositChange({{ $deposit->id }})" 
               style="width:100%;text-align:right;" />
    </td>
    <td class="td-actions">
        <button onclick="duplicateDepositRow({{ $deposit->id }})" title="Duplicate"><i class="bi bi-files"></i></button>
        <button class="del" onclick="deleteDepositRow({{ $deposit->id }})" title="Delete"><i class="bi bi-trash"></i></button>
    </td>
</tr>