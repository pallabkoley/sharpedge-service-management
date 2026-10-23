<tr data-cheque-id="{{ $cheque->id }}">
    <td style="text-align:center;font-weight:500;color:var(--text-muted);">{{ $index + 1 }}</td>
    <td>
        <input class="td-date" type="date" 
               id="chqDate_{{ $cheque->id }}" 
               value="{{ $cheque->cheque_date ? \Carbon\Carbon::parse($cheque->cheque_date)->format('Y-m-d') : '' }}" 
               oninput="onChequeChange({{ $cheque->id }})" 
               style="width:100%;" />
    </td>
    <td>
        <input class="td-text" type="text" 
               id="chqNumber_{{ $cheque->id }}" 
               value="{{ $cheque->cheque_no ?? '' }}" 
               oninput="onChequeChange({{ $cheque->id }})" 
               style="text-align:center;font-family:var(--font-mono);width:100%;" />
    </td>
    <td>
        <input class="td-amt" type="text" 
               id="chqAmount_{{ $cheque->id }}" 
               value="{{ number_format($cheque->amount, 0) }}" 
               oninput="onChequeChange({{ $cheque->id }})" 
               style="width:100%;text-align:right;" />
    </td>
    <td class="td-actions">
        <button onclick="duplicateChequeRow({{ $cheque->id }})" title="Duplicate"><i class="bi bi-files"></i></button>
        <button class="del" onclick="deleteChequeRow({{ $cheque->id }})" title="Delete"><i class="bi bi-trash"></i></button>
    </td>
</tr>