<tr data-invest-id="{{ $investment->id ?? '' }}">
    <td style="text-align:left;padding-left:8px;"><span class="td-label">{{ $investment->investment_name ?? 'New Investment' }}</span></td>
    <td><input class="td-amt" type="text" id="invBasic_{{ $index }}" value="{{ fmtRaw($investment->amount ?? 0) }}" oninput="onInvestChange({{ $index }})" /></td>
    <td><input class="td-amt" type="text" id="invInterest_{{ $index }}" value="{{ fmtRaw($investment->interest ?? 0) }}" oninput="onInvestChange({{ $index }})" /></td>
    <td><input class="td-amt readonly" type="text" id="invTotal_{{ $index }}" value="0" readonly /></td>
    <td><input class="td-amt" type="text" id="invCompany_{{ $index }}" value="{{ $investment->company ?? '' }}" oninput="onInvestChange({{ $index }})" style="text-align:left;" /></td>
    <td><input class="td-amt" type="text" id="invPan_{{ $index }}" value="{{ $investment->pan ?? '' }}" oninput="onInvestChange({{ $index }})" style="text-align:left;text-transform:uppercase;font-size:10px;" /></td>
    <td><button class="btn-erp btn-ghost btn-sm" onclick="deleteInvestmentRow({{ $index }})" style="font-size:10px;padding:2px 6px;"><i class="bi bi-trash"></i></button></td>
</tr>