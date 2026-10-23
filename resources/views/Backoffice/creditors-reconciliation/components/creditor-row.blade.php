<tr data-creditor-id="{{ $creditor->id }}" data-name="{{ strtolower($creditor->creditor_name) }}">
    <td style="text-align:center;font-weight:500;color:var(--text-muted);">{{ $index + 1 }}</td>
    <td class="sticky-col" style="background:inherit;position:sticky;left:0;z-index:2;text-align:left;padding-left:6px;">
        <span class="td-label">{{ $creditor->creditor_name }}</span>
    </td>
    <td>
        <input class="td-text uppercase" type="text" id="cpan_{{ $creditor->id }}"
               value="{{ $creditor->pan ?? '' }}"
               oninput="onCreditorChange({{ $creditor->id }})"
               style="text-align:center;font-size:9px;" />
    </td>
    <td>
        <input class="td-text uppercase" type="text" id="cgstin_{{ $creditor->id }}"
               value="{{ $creditor->gstin ?? '' }}"
               oninput="onCreditorChange({{ $creditor->id }})"
               style="text-align:center;font-size:9px;" />
    </td>
    <td>
        <input class="td-amt" type="text" id="cbillAmount_{{ $creditor->id }}"
               value="{{ number_format($creditor->bill_amount, 0) }}"
               oninput="onCreditorChange({{ $creditor->id }})" />
    </td>
    <td>
        <input class="td-date" type="date" id="cfollowUp_{{ $creditor->id }}"
               value="{{ $creditor->payment_followup_date?->format('Y-m-d') ?? '' }}"
               oninput="onCreditorChange({{ $creditor->id }})" />
    </td>
    <td>
        <input class="td-amt" type="text" id="camountPaid_{{ $creditor->id }}"
               value="{{ number_format($creditor->amount_paid, 0) }}"
               oninput="onCreditorChange({{ $creditor->id }})" />
    </td>
    <td>
        <input class="td-amt readonly" type="text" id="coutstanding_{{ $creditor->id }}"
               value="{{ number_format($creditor->outstanding_payable, 0) }}"
               readonly style="font-weight:600;" />
    </td>
    <td>
        <input class="td-text" type="text" id="cremarks_{{ $creditor->id }}"
               value="{{ $creditor->remarks ?? '' }}"
               oninput="onCreditorChange({{ $creditor->id }})"
               style="text-align:left;font-size:9px;" />
    </td>
    <td>
        <div style="display:flex;gap:2px;justify-content:center;flex-wrap:wrap;">
            <button class="btn-erp btn-ghost btn-sm" onclick="duplicateCreditorRow({{ $creditor->id }})"
                    style="font-size:8px;padding:1px 4px;" title="Duplicate">
                <i class="bi bi-files"></i>
            </button>
            <button class="btn-erp btn-ghost btn-sm" onclick="deleteCreditorRow({{ $creditor->id }})"
                    style="font-size:8px;padding:1px 4px;color:var(--red);" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>