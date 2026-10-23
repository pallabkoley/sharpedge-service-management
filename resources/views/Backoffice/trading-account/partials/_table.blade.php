<div style="overflow-x:auto;">
    <table class="ta-table" id="tradingTable">
        <colgroup>
            <col style="width:28%">
            <col style="width:24px">
            <col style="width:9%">
            <col style="width:24px">
            <col style="width:9%">
            <col style="width:28%">
            <col style="width:24px">
            <col style="width:9%">
            <col style="width:24px">
            <col style="width:9%">
        </colgroup>

        <thead>
            <tr>
                <th class="col-head" colspan="5" style="border-right:2px solid #fff;">
                    PARTICULARS &nbsp;&nbsp; AMOUNT &nbsp;&nbsp;&nbsp; AMOUNT
                </th>
                <th class="col-head" colspan="5">
                    PARTICULARS &nbsp;&nbsp; AMOUNT &nbsp;&nbsp;&nbsp; AMOUNT
                </th>
            </tr>

            <tr>
                <th class="col-head-debit">PARTICULARS</th>
                <th class="col-subhead">Rs</th>
                <th class="col-subhead">AMOUNT</th>
                <th class="col-subhead">Rs</th>
                <th class="col-subhead" style="border-right:2px solid #fff;">AMOUNT</th>

                <th class="col-head-credit">PARTICULARS</th>
                <th class="col-subhead">Rs</th>
                <th class="col-subhead">AMOUNT</th>
                <th class="col-subhead">Rs</th>
                <th class="col-subhead">AMOUNT</th>
            </tr>
        </thead>

        <tbody id="tBody">

            {{-- =========================================================
                 SECTION HEADER (with Adjustment Dropdown)
            ========================================================== --}}
            <tr class="sec-head">
                <td colspan="5" style="border-right:2px solid #4a6fa5;">
                    <span class="sec-lnk">◈ PURCHASES &amp; DIRECT EXPENSES</span>

                    <span class="adj-label">Adjustment:</span>

                    <select class="adj-selector" id="adjustmentType">
                        <option
                            value="purchaseReturn"
                            {{ ($tradingAccount->adjustment_type ?? 'purchaseReturn') == 'purchaseReturn' ? 'selected' : '' }}
                        >
                            Purchase Return (−)
                        </option>

                        <option
                            value="debitNote"
                            {{ ($tradingAccount->adjustment_type ?? '') == 'debitNote' ? 'selected' : '' }}
                        >
                            Debit Note (+)
                        </option>
                    </select>
                </td>

                <td colspan="5">
                    <span class="sec-lnk">◈ SALES &amp; CLOSING STOCK</span>
                </td>
            </tr>

            {{-- =========================================================
                 DYNAMIC ROWS FROM $combinedRows
            ========================================================== --}}
            @foreach($combinedRows as $row)

                {{-- SUBTOTAL ROW --}}
                @if(isset($row->debit->is_subtotal) && $row->debit->is_subtotal)
                    <tr class="sub-row">
                        <td colspan="3">
                            <div style="padding:2px 8px;font-weight:600;color:#0d47a1;font-family:var(--font-sans);">
                                {{ $row->debit->label }}
                            </div>
                        </td>
                        <td class="td-rs">₹</td>
                        <td class="td-amt sub" id="{{ Str::slug($row->debit->label, '-') }}" style="border-right:2px solid #4a6fa5;">-</td>
                        <td colspan="5" class="sub-row">
                            <div style="display:flex;justify-content:space-between;padding:2px 8px;font-weight:600;color:#0d47a1;font-family:var(--font-sans);">
                                <span>{{ $row->credit->label }}</span>
                                <span style="font-family:var(--font-mono);" id="{{ Str::slug($row->credit->label, '-') }}-label">-</span>
                            </div>
                        </td>
                    </tr>
                    @continue
                @endif

                {{-- GROSS PROFIT / LOSS ROW --}}
                @if(isset($row->debit->is_gp) && $row->debit->is_gp)
                    <tr class="gp-row" id="gpRow">
                        <td colspan="3">
                            <div style="padding:2px 8px;font-weight:700;font-style:italic;color:#1b5e20;font-family:var(--font-sans);" id="gp-label">
                                {{ $row->debit->label }}
                            </div>
                        </td>
                        <td class="td-rs">₹</td>
                        <td class="td-amt" id="gp-amt1" style="border-right:2px solid #4a6fa5;">-</td>
                        <td colspan="5"></td>
                    </tr>
                    @continue
                @endif

                {{-- TOTAL ROW --}}
                @if(isset($row->debit->is_total) && $row->debit->is_total)
                    <tr class="gt-row">
                        <td style="font-family:var(--font-sans);font-size:11px;letter-spacing:.5px;text-transform:uppercase;font-weight:700;padding:5px 8px;" colspan="1">
                            TOTAL
                        </td>
                        <td class="td-rs" style="background:#1a3a6c;color:#fff;">₹</td>
                        <td class="td-amt" id="dr-grand-t1" style="background:#1a3a6c;color:#fff;font-weight:700;">-</td>
                        <td class="td-rs" style="background:#1a3a6c;color:#fff;">₹</td>
                        <td class="td-amt" id="dr-grand-t2" style="background:#1a3a6c;color:#a0c4ff;font-weight:700;border-right:2px solid #a0c4ff;">-</td>
                        <td style="font-family:var(--font-sans);font-size:11px;letter-spacing:.5px;text-transform:uppercase;font-weight:700;padding:5px 8px;" colspan="1">
                            TOTAL
                        </td>
                        <td class="td-rs" style="background:#1a3a6c;color:#fff;">₹</td>
                        <td class="td-amt" id="cr-grand-t1" style="background:#1a3a6c;color:#fff;font-weight:700;">-</td>
                        <td class="td-rs" style="background:#1a3a6c;color:#fff;">₹</td>
                        <td class="td-amt" id="cr-grand-t2" style="background:#1a3a6c;color:#a0c4ff;font-weight:700;">-</td>
                    </tr>
                    @continue
                @endif

                {{-- NORMAL ROW (Debit + Credit) --}}
                @php
                    $debitObj = $row->debit ?? null;
                    $creditObj = $row->credit ?? null;
                    $rowType = $debitObj->row_type ?? $creditObj->row_type ?? 'system';
                    $side = $debitObj ? 'dr' : 'cr';
                    
                    // 🔥 NEW LOGIC: Check if the row is manual entry (Based on source text OR if it's a custom row)
                    $debitIsEditable = $debitObj ? (str_contains($debitObj->source ?? '', 'Manual Entry') || $debitObj->row_type === 'custom') : false;
                    $creditIsEditable = $creditObj ? (str_contains($creditObj->source ?? '', 'Manual Entry') || $creditObj->row_type === 'custom') : false;
                @endphp

                <tr
                    class="{{ $rowType === 'custom' ? 'dr custom' : 'dr system' }}"
                    data-side="{{ $side }}"
                    data-row-type="{{ $rowType }}"
                    data-db-id="{{ $debitObj->id ?? $creditObj->id ?? '' }}"
                >
                    {{-- DEBIT SIDE --}}
                    @if($debitObj)
                        <td>
                            <div class="td-part">
                                <div class="main-line">
                                    <span class="bullet">♦</span>
                                    <span>{{ $debitObj->label }}</span>
                                </div>
                                <div class="source-text">
                                    ({{ $debitObj->source ?? 'Source: Manual Entry' }})
                                </div>
                            </div>
                        </td>
                        <td class="td-rs">₹</td>
                        {{-- ✅ FIXED: Only manual entry fields are contenteditable. Others will be read-only. --}}
                        <td
                            class="td-amt editable manual-entry"
                            id="{{ Str::slug($debitObj->label, '-') }}"
                            contenteditable="{{ $debitIsEditable ? 'true' : 'false' }}"
                            data-particular="{{ $debitObj->label }}"
                            data-side="dr"
                            oninput="calc(); autoSave();"
                            style="{{ !$debitIsEditable ? 'background:#f8f9fa; cursor:default;' : '' }}"
                        >
                            -
                        </td>
                        <td class="td-rs"></td>
                        <td class="td-amt blank" style="border-right:2px solid #4a6fa5;"></td>
                    @else
                        <td colspan="5" style="border-right:2px solid #4a6fa5;"></td>
                    @endif

                    {{-- CREDIT SIDE --}}
                    @if($creditObj)
                        <td>
                            <div class="td-part">
                                <div class="main-line">
                                    <span class="bullet">♦</span>
                                    <span>{{ $creditObj->label }}</span>
                                </div>
                                <div class="source-text">
                                    ({{ $creditObj->source ?? 'Source: Manual Entry' }})
                                </div>
                            </div>
                        </td>
                        <td class="td-rs">₹</td>
                        {{-- ✅ FIXED: Only manual entry fields are contenteditable. Others will be read-only. --}}
                        <td
                            class="td-amt editable manual-entry"
                            id="{{ Str::slug($creditObj->label, '-') }}"
                            contenteditable="{{ $creditIsEditable ? 'true' : 'false' }}"
                            data-particular="{{ $creditObj->label }}"
                            data-side="cr"
                            oninput="calc(); autoSave();"
                            style="{{ !$creditIsEditable ? 'background:#f8f9fa; cursor:default;' : '' }}"
                        >
                            -
                        </td>
                        <td class="td-rs">₹</td>
                        <td class="td-amt blank" id="{{ Str::slug($creditObj->label, '-') }}-total">-</td>
                    @else
                        <td colspan="5"></td>
                    @endif
                </tr>
            @endforeach

        </tbody>
    </table>
</div>