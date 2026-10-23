<table class="bs-tbl">
    <colgroup>
        <col style="width:27%"><col style="width:22px"><col style="width:8%"><col style="width:9%">
        <col style="width:3px">
        <col style="width:27%"><col style="width:22px"><col style="width:8%"><col style="width:9%">
    </colgroup>
    <thead>
        <tr>
            <th class="col-h" colspan="4" style="border-right:2px solid #fff;">LIABILITIES</th>
            <th style="background:#2a5aa0;border:none;padding:0;"></th>
            <th class="col-h" colspan="4">ASSETS</th>
        </tr>
        <tr>
            <th class="col-h" style="background:#0d2a5e;">PARTICULARS</th>
            <th class="col-h2">Note</th><th class="col-h2">Amount</th><th class="col-h2" style="border-right:2px solid #fff;">Total</th>
            <th style="background:#2a5aa0;border:none;padding:0;"></th>
            <th class="col-h" style="background:#0d2a5e;">PARTICULARS</th>
            <th class="col-h2">Note</th><th class="col-h2">Amount</th><th class="col-h2">Total</th>
        </tr>
    </thead>
    <tbody>
        @php
            // সেকশন পেয়ারিং – ডাটাবেসের স্লাগ (snake) এবং ব্লেডের টাইটেল
            $sectionPairs = [
                ['liab_slug' => 'capital_account', 'asset_slug' => 'intangible_assets', 'liab_title' => 'Capital Account', 'asset_title' => 'Intangible Assets'],
                ['liab_slug' => 'secured_loans', 'asset_slug' => 'fixed_assets', 'liab_title' => 'Secured Loans', 'asset_title' => 'Fixed Assets'],
                ['liab_slug' => 'unsecured_loans', 'asset_slug' => 'investments', 'liab_title' => 'Unsecured Loans', 'asset_title' => 'Investments'],
                ['liab_slug' => 'current_liabilities', 'asset_slug' => 'current_assets', 'liab_title' => 'Current Liabilities', 'asset_title' => 'Current Assets'],
                ['liab_slug' => 'other_liabilities', 'asset_slug' => 'cash_at_bank', 'liab_title' => 'Other Liabilities', 'asset_title' => 'Cash at Bank'],
            ];
        @endphp

        @foreach($sectionPairs as $pair)
            @php
                $liabItems = $liabilities[$pair['liab_slug']] ?? collect([]);
                $assetItems = $assets[$pair['asset_slug']] ?? collect([]);
                $maxRows = max(count($liabItems), count($assetItems));
            @endphp

            {{-- SECTION HEADER (Left & Right) --}}
            <tr class="sh-r">
                <td colspan="4" style="border-right:2px solid var(--tbl);">
                    <span class="sh-lnk">{{ $pair['liab_title'] }}</span>
                    <span class="sh-toggle" onclick="tog('l_{{ $pair['liab_slug'] }}')"><i class="bi bi-dash-square" id="l_{{ $pair['liab_slug'] }}-ico"></i></span>
                </td>
                <td class="dc"></td>
                <td colspan="4">
                    <span class="sh-lnk">{{ $pair['asset_title'] }}</span>
                    <span class="sh-toggle" onclick="tog('a_{{ $pair['asset_slug'] }}')"><i class="bi bi-dash-square" id="a_{{ $pair['asset_slug'] }}-ico"></i></span>
                </td>
            </tr>

            {{-- SIDE-BY-SIDE ROWS --}}
            <tbody class="sec-body" id="l_{{ $pair['liab_slug'] }}">
                @for($i = 0; $i < $maxRows; $i++)
                    @php
                        $liabItem = $liabItems[$i] ?? null;
                        $assetItem = $assetItems[$i] ?? null;
                    @endphp

                    <tr class="dr">
                        {{-- LEFT (LIABILITY) --}}
                        @if($liabItem)
                            <td>
                                <div class="td-pt">
                                    <div class="main-line"><span class="bul">♦</span> {{ $liabItem->label }}</div>
                                    @if($liabItem->source_type)
                                        <div class="source-text">(Source: {{ $liabItem->source_type }})</div>
                                    @endif
                                </div>
                            </td>
                            <td class="td-note">{{ $liabItem->code ?? '' }}</td>
                            <td class="td-am" contenteditable="{{ $liabItem->is_editable ? 'true' : 'false' }}" 
                                id="{{ $liabItem->code }}" 
                                data-id="{{ $liabItem->id }}" 
                                data-update-url="{{ route('backoffice.computations.balance-sheet.update-item', ['computation' => $balanceSheet->computation_id, 'item' => $liabItem->id]) }}"
                                onblur="calc()">
                                {{ number_format($liabItem->amount, 2) }}
                            </td>
                            <td class="td-am blank" style="border-right:2px solid var(--tbl);"></td>
                        @else
                            <td colspan="4" style="border-right:2px solid var(--tbl);"></td>
                        @endif

                        {{-- DIVIDER --}}
                        <td class="dc"></td>

                        {{-- RIGHT (ASSET) --}}
                        @if($assetItem)
                            <td>
                                <div class="td-pt">
                                    <div class="main-line"><span class="bul">♦</span> {{ $assetItem->label }}</div>
                                    @if($assetItem->source_type)
                                        <div class="source-text">(Source: {{ $assetItem->source_type }})</div>
                                    @endif
                                </div>
                            </td>
                            <td class="td-note">{{ $assetItem->code ?? '' }}</td>
                            <td class="td-am" contenteditable="{{ $assetItem->is_editable ? 'true' : 'false' }}" 
                                id="{{ $assetItem->code }}" 
                                data-id="{{ $assetItem->id }}" 
                                data-update-url="{{ route('backoffice.computations.balance-sheet.update-item', ['computation' => $balanceSheet->computation_id, 'item' => $assetItem->id]) }}"
                                onblur="calc()">
                                {{ number_format($assetItem->amount, 2) }}
                            </td>
                            <td class="td-am blank"></td>
                        @else
                            <td colspan="4"></td>
                        @endif
                    </tr>
                @endfor
            </tbody>

            {{-- SUBTOTAL ROW --}}
            <tr class="st-r">
                {{-- LIABILITIES SUBTOTAL --}}
                <td colspan="2" style="border-right:1px solid var(--tbl);font-family:var(--font-sans);font-weight:700;padding-left:8px;">Total {{ $pair['liab_title'] }}</td>
                <td class="td-rs" style="background:#dbeafe;color:#1565c0;">₹</td>
                <td class="td-am sub" id="l_{{ $pair['liab_slug'] }}Tot" style="border-right:2px solid var(--tbl);">-</td>
                
                {{-- DIVIDER --}}
                <td class="dc"></td>
                
                {{-- ASSETS SUBTOTAL --}}
                <td colspan="2" style="font-family:var(--font-sans);font-weight:700;padding-left:8px;">Total {{ $pair['asset_title'] }}</td>
                <td class="td-rs" style="background:#dbeafe;color:#1565c0;">₹</td>
                <td class="td-am sub" id="a_{{ $pair['asset_slug'] }}Tot">-</td>
            </tr>
        @endforeach
    </tbody>

    {{-- GRAND TOTAL ROW --}}
    <tfoot>
        <tr class="gt-r">
            <td style="font-family:var(--font-sans);font-size:10.5px;letter-spacing:.5px;text-transform:uppercase;font-weight:700;padding:5px 8px;">TOTAL LIABILITIES</td>
            <td class="td-note" style="background:#1a3a6c;color:#a0c4ff;">₹</td>
            <td class="td-am" id="liabGT" style="background:#1a3a6c;color:#7cc4fa;font-weight:700;border-right:2px solid #a0c4ff;">-</td>
            <td class="td-am blank" style="background:#1a3a6c;border-right:2px solid #a0c4ff;"> </td>
            <td class="dc" style="background:#a0c4ff!important;"> </td>
            <td style="font-family:var(--font-sans);font-size:10.5px;letter-spacing:.5px;text-transform:uppercase;font-weight:700;padding:5px 8px;">TOTAL ASSETS</td>
            <td class="td-note" style="background:#1a3a6c;color:#a0c4ff;">₹</td>
            <td class="td-am" id="assetGT" style="background:#1a3a6c;color:#7cc4fa;font-weight:700;">-</td>
            <td class="td-am blank" style="background:#1a3a6c;"> </td>
        </tr>
    </tfoot>
</table>