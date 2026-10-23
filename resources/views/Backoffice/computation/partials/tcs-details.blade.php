<div class="section-block">
    <table class="comp-table">
        <tr class="row-section"><td class="rn">13</td><td colspan="5">L. TAX COLLECTED AT SOURCE (TCS) DETAILS <button class="collapse-btn" data-toggle="sec_tcs">▼</button></td></tr>
        <tbody id="sec_tcs">
            <tr><td colspan="6" style="padding:0;border:none;">
                <table class="tds-table">
                    <tr><th style="width:28px;">#</th><th>TAN of Collector</th><th>Name of Collector</th><th style="width:70px;">Section</th><th style="width:110px;">Amount Collected (₹)</th><th style="width:110px;">TCS Collected (₹)</th><th style="width:110px;">TCS Claimed (₹)</th><th style="width:80px;">Action</th></tr>
                    <tbody id="tcs_rows">
                        @forelse($computation->tcsEntries ?? [] as $index => $entry)
                        <tr data-group="tcs" data-index="{{ $index }}"><td class="ctr">{{ $loop->iteration }}</td><td><input class="inp inp-text" name="tcs_entries[{{ $index }}][tan_number]" value="{{ $entry->tan_number }}"></td><td><input class="inp inp-text" name="tcs_entries[{{ $index }}][collector_name]" value="{{ $entry->collector_name }}"></td><td class="ctr"><input class="inp" style="text-align:center;" name="tcs_entries[{{ $index }}][section_code]" value="{{ $entry->section_code }}"></td><td class="amt"><input class="inp" name="tcs_entries[{{ $index }}][amount_collected]" value="{{ $entry->amount_collected }}"></td><td class="amt"><input class="inp" name="tcs_entries[{{ $index }}][tcs_collected]" value="{{ $entry->tcs_collected }}"></td><td class="amt"><input class="inp" name="tcs_entries[{{ $index }}][tcs_claimed]" value="{{ $entry->tcs_claimed }}"></td><td class="action-cell"><button class="dyn-btn del" data-group="tcs">✕</button></td></tr>
                        @empty
                        <tr data-group="tcs" data-index="0"><td class="ctr">1</td><td><input class="inp inp-text" name="tcs_entries[0][tan_number]" value=""></td><td><input class="inp inp-text" name="tcs_entries[0][collector_name]" value="NIL — No TCS Transactions"></td><td></td><td class="amt"><input class="inp" name="tcs_entries[0][amount_collected]" value="0"></td><td class="amt"><input class="inp" name="tcs_entries[0][tcs_collected]" value="0"></td><td class="amt"><input class="inp" name="tcs_entries[0][tcs_claimed]" value="0"></td><td class="action-cell"><button class="dyn-btn del" data-group="tcs">✕</button></td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="tds-total"><td colspan="3" style="text-align:right;font-weight:bold;">TOTAL TCS</td><td></td><td class="amt"><input class="inp" id="tcs_total_amt" readonly value="0"></td><td class="amt"><input class="inp" id="tcs_total_col" readonly value="0"></td><td class="amt"><input class="inp" id="tcs_total_cl" readonly value="0"></td><td><button class="dyn-btn add" data-group="tcs" data-tbody="tcs_rows" data-template="tpl_tcs">+ Add</button></td></tr>
                    </tfoot>
                </table>
            </td></tr>
        </tbody>
    </table>
    <template id="tpl_tcs"><tr data-group="tcs" data-index="__INDEX__"><td class="ctr">__INDEX_PLUS_ONE__</td><td><input class="inp inp-text" name="tcs_entries[__INDEX__][tan_number]" placeholder="TAN"></td><td><input class="inp inp-text" name="tcs_entries[__INDEX__][collector_name]" placeholder="Collector"></td><td class="ctr"><input class="inp" style="text-align:center;" name="tcs_entries[__INDEX__][section_code]" placeholder="Sec."></td><td class="amt"><input class="inp" name="tcs_entries[__INDEX__][amount_collected]" value="0"></td><td class="amt"><input class="inp" name="tcs_entries[__INDEX__][tcs_collected]" value="0"></td><td class="amt"><input class="inp" name="tcs_entries[__INDEX__][tcs_claimed]" value="0"></td><td class="action-cell"><button class="dyn-btn del" data-group="tcs">✕</button></td></tr></template>
</div>