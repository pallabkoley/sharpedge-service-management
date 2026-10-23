<tr data-debtor-id="{{ $debtor->id }}" data-name="{{ strtolower($debtor->debtor_name) }}">
    <td style="text-align:center;font-weight:500;color:var(--text-muted);">{{ $index + 1 }}</td>
    <td class="sticky-col" style="background:inherit;position:sticky;left:0;z-index:2;text-align:left;padding-left:6px;">
        <span class="td-label">{{ $debtor->debtor_name }}</span>
    </td>
    <td>
        <input class="td-text uppercase" type="text" id="dpan_{{ $debtor->id }}"
               value="{{ $debtor->pan ?? '' }}"
               oninput="onRowChange({{ $debtor->id }})"
               style="text-align:center;font-size:9px;" />
    </td>
    <td>
        <input class="td-text uppercase" type="text" id="dgstin_{{ $debtor->id }}"
               value="{{ $debtor->gstin ?? '' }}"
               oninput="onRowChange({{ $debtor->id }})"
               style="text-align:center;font-size:9px;" />
    </td>
    <td>
        <input class="td-amt" type="text" id="dbillAmount_{{ $debtor->id }}"
               value="{{ number_format($debtor->bill_amount, 0) }}"
               oninput="onRowChange({{ $debtor->id }})" />
    </td>
    <td>
        <input class="td-date" type="date" id="dfollowUp_{{ $debtor->id }}"
               value="{{ $debtor->payment_followup_date?->format('Y-m-d') ?? '' }}"
               oninput="onRowChange({{ $debtor->id }})" />
    </td>
    <td>
        <input class="td-amt" type="text" id="damountReceived_{{ $debtor->id }}"
               value="{{ number_format($debtor->amount_received, 0) }}"
               oninput="onRowChange({{ $debtor->id }})" />
    </td>
    <td>
        <input class="td-amt readonly" type="text" id="doutstanding_{{ $debtor->id }}"
               value="{{ number_format($debtor->outstanding, 0) }}"
               readonly style="font-weight:600;" />
    </td>
    <td>
        <input class="td-text" type="text" id="dremarks_{{ $debtor->id }}"
               value="{{ $debtor->remarks ?? '' }}"
               oninput="onRowChange({{ $debtor->id }})"
               style="text-align:left;font-size:9px;" />
    </td>
    <td>
        <div style="display:flex;gap:2px;justify-content:center;flex-wrap:wrap;">
            <button class="btn-erp btn-ghost btn-sm" onclick="duplicateRow({{ $debtor->id }})"
                    style="font-size:8px;padding:1px 4px;" title="Duplicate">
                <i class="bi bi-files"></i>
            </button>
            <button class="btn-erp btn-ghost btn-sm" onclick="deleteRow({{ $debtor->id }})"
                    style="font-size:8px;padding:1px 4px;color:var(--red);" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>