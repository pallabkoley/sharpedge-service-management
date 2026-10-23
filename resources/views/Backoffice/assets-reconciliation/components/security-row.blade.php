<tr data-security-id="{{ $security->id ?? '' }}">
    <td style="text-align:left;padding-left:8px;"><span class="td-label">{{ $security->deposit_name ?? 'New Deposit' }}</span></td>
    <td><input class="td-amt" type="text" id="secOpening_{{ $index }}" value="{{ fmtRaw($security->amount ?? 0) }}" oninput="onSecurityChange({{ $index }})" /></td>
    <td><input class="td-amt" type="text" id="secDeposit_{{ $index }}" value="{{ fmtRaw($security->deposit ?? 0) }}" oninput="onSecurityChange({{ $index }})" /></td>
    <td><input class="td-amt" type="text" id="secWithdrawal_{{ $index }}" value="{{ fmtRaw($security->withdrawal ?? 0) }}" oninput="onSecurityChange({{ $index }})" /></td>
    <td><input class="td-amt readonly" type="text" id="secNet_{{ $index }}" value="0" readonly /></td>
    <td><button class="btn-erp btn-ghost btn-sm" onclick="deleteSecurityRow({{ $index }})" style="font-size:10px;padding:2px 6px;"><i class="bi bi-trash"></i></button></td>
</tr>