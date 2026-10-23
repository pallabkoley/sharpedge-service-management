<div class="section-block">
    <table class="comp-table">
        <tr class="row-section"><td class="rn">14</td><td colspan="5">M. ADVANCE TAX DETAILS <button class="collapse-btn" data-toggle="sec_at">▼</button></td></tr>
        <tbody id="sec_at">
            <tr><td colspan="6" style="padding:0;border:none;">
                <table class="tds-table">
                    <tr><th style="width:28px;">#</th><th style="width:120px;">Date of Payment</th><th>BSR Code</th><th>Challan No.</th><th style="width:100px;">Instalment</th><th style="width:120px;">Amount Paid (₹)</th><th style="width:80px;">Action</th></tr>
                    <tbody id="at_rows">
                        @forelse($computation->advanceTaxEntries ?? [] as $index => $entry)
                        <tr data-group="at" data-index="{{ $index }}"><td class="ctr">{{ $loop->iteration }}</td><td><input class="inp inp-text" name="advance_tax_entries[{{ $index }}][payment_date]" value="{{ $entry->payment_date }}"></td><td><input class="inp inp-text" name="advance_tax_entries[{{ $index }}][bsr_code]" value="{{ $entry->bsr_code }}"></td><td><input class="inp inp-text" name="advance_tax_entries[{{ $index }}][challan_no]" value="{{ $entry->challan_no }}"></td><td class="ctr"><input class="inp" style="text-align:center;" name="advance_tax_entries[{{ $index }}][installment]" value="{{ $entry->installment }}"></td><td class="amt"><input class="inp" name="advance_tax_entries[{{ $index }}][amount_paid]" value="{{ $entry->amount_paid }}"></td><td class="action-cell"><button class="dyn-btn del" data-group="at">✕</button></td></tr>
                        @empty
                        <tr data-group="at" data-index="0"><td class="ctr">1</td><td><input class="inp inp-text" name="advance_tax_entries[0][payment_date]" value="15.06.2024"></td><td><input class="inp inp-text" name="advance_tax_entries[0][bsr_code]" value="0000640"></td><td><input class="inp inp-text" name="advance_tax_entries[0][challan_no]" value="85234"></td><td class="ctr"><input class="inp" style="text-align:center;" name="advance_tax_entries[0][installment]" value="1st (15%)"></td><td class="amt"><input class="inp" name="advance_tax_entries[0][amount_paid]" value="80000"></td><td class="action-cell"><button class="dyn-btn del" data-group="at">✕</button></td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="tds-total"><td colspan="5" style="text-align:right;font-weight:bold;">TOTAL ADVANCE TAX PAID</td><td class="amt"><input class="inp" id="at_total" readonly value="0"></td><td><button class="dyn-btn add" data-group="at" data-tbody="at_rows" data-template="tpl_at">+ Add Challan</button></td></tr>
                    </tfoot>
                </table>
            </td></tr>
        </tbody>
    </table>
    <template id="tpl_at"><tr data-group="at" data-index="__INDEX__"><td class="ctr">__INDEX_PLUS_ONE__</td><td><input class="inp inp-text" name="advance_tax_entries[__INDEX__][payment_date]" placeholder="Date"></td><td><input class="inp inp-text" name="advance_tax_entries[__INDEX__][bsr_code]" placeholder="BSR"></td><td><input class="inp inp-text" name="advance_tax_entries[__INDEX__][challan_no]" placeholder="Challan No."></td><td class="ctr"><input class="inp" style="text-align:center;" name="advance_tax_entries[__INDEX__][installment]" placeholder="Instalment"></td><td class="amt"><input class="inp" name="advance_tax_entries[__INDEX__][amount_paid]" value="0"></td><td class="action-cell"><button class="dyn-btn del" data-group="at">✕</button></td></tr></template>
</div>