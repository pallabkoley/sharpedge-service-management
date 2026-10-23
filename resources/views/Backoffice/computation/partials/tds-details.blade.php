<div class="section-block">
    <table class="comp-table">
        <tr class="row-section"><td class="rn">12</td><td colspan="5">K. TAX DEDUCTED AT SOURCE (TDS) DETAILS <button class="collapse-btn" data-toggle="sec_tds">▼</button></td></tr>
        <tbody id="sec_tds">
            <tr><td colspan="6" style="padding:0;border:none;">
                <table class="tds-table">
                    <tr><th style="width:28px;">#</th><th>TAN of Deductor</th><th>Name of Deductor</th><th style="width:70px;">Section</th><th style="width:110px;">Amount Paid (₹)</th><th style="width:110px;">TDS Deducted (₹)</th><th style="width:110px;">TDS Claimed (₹)</th><th style="width:80px;">Action</th></tr>
                    <tbody id="tds_rows">
                        @forelse($computation->tdsEntries ?? [] as $index => $entry)
                        <tr data-group="tds" data-index="{{ $index }}"><td class="ctr">{{ $loop->iteration }}</td><td><input class="inp inp-text" name="tds_entries[{{ $index }}][tan_number]" value="{{ $entry->tan_number }}"></td><td><input class="inp inp-text" name="tds_entries[{{ $index }}][deductor_name]" value="{{ $entry->deductor_name }}"></td><td class="ctr"><input class="inp" style="text-align:center;" name="tds_entries[{{ $index }}][section_code]" value="{{ $entry->section_code }}"></td><td class="amt"><input class="inp" name="tds_entries[{{ $index }}][amount_paid]" value="{{ $entry->amount_paid }}"></td><td class="amt"><input class="inp" name="tds_entries[{{ $index }}][tds_deducted]" value="{{ $entry->tds_deducted }}"></td><td class="amt"><input class="inp" name="tds_entries[{{ $index }}][tds_claimed]" value="{{ $entry->tds_claimed }}"></td><td class="action-cell"><button class="dyn-btn del" data-group="tds">✕</button></td></tr>
                        @empty
                        <tr data-group="tds" data-index="0"><td class="ctr">1</td><td><input class="inp inp-text" name="tds_entries[0][tan_number]" value="DELS98765A"></td><td><input class="inp inp-text" name="tds_entries[0][deductor_name]" value="EMPLOYER PVT. LTD."></td><td class="ctr"><input class="inp" style="text-align:center;" name="tds_entries[0][section_code]" value="192"></td><td class="amt"><input class="inp" name="tds_entries[0][amount_paid]" value="1080000"></td><td class="amt"><input class="inp" name="tds_entries[0][tds_deducted]" value="85000"></td><td class="amt"><input class="inp" name="tds_entries[0][tds_claimed]" value="85000"></td><td class="action-cell"><button class="dyn-btn del" data-group="tds">✕</button></td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="tds-total"><td colspan="3" style="text-align:right;font-weight:bold;">TOTAL TDS</td><td></td><td class="amt"><input class="inp" id="tds_total_amt" readonly value="0"></td><td class="amt"><input class="inp" id="tds_total_ded" readonly value="0"></td><td class="amt"><input class="inp" id="tds_total_cl" readonly value="0"></td><td><button class="dyn-btn add" data-group="tds" data-tbody="tds_rows" data-template="tpl_tds">+ Add</button></td></tr>
                    </tfoot>
                </table>
            </td></tr>
        </tbody>
    </table>
    <template id="tpl_tds"><tr data-group="tds" data-index="__INDEX__"><td class="ctr">__INDEX_PLUS_ONE__</td><td><input class="inp inp-text" name="tds_entries[__INDEX__][tan_number]" placeholder="TAN"></td><td><input class="inp inp-text" name="tds_entries[__INDEX__][deductor_name]" placeholder="Deductor"></td><td class="ctr"><input class="inp" style="text-align:center;" name="tds_entries[__INDEX__][section_code]" placeholder="Sec."></td><td class="amt"><input class="inp" name="tds_entries[__INDEX__][amount_paid]" value="0"></td><td class="amt"><input class="inp" name="tds_entries[__INDEX__][tds_deducted]" value="0"></td><td class="amt"><input class="inp" name="tds_entries[__INDEX__][tds_claimed]" value="0"></td><td class="action-cell"><button class="dyn-btn del" data-group="tds">✕</button></td></tr></template>
</div>