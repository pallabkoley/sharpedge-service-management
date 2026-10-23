<div class="section-block">
    <table class="comp-table">
        <tr class="row-section"><td class="rn">EI</td><td colspan="3">EXEMPT INCOME <button class="collapse-btn" data-toggle="sec_exempt">▼</button></td><td class="amt" style="color:#ffdd44;">Sch. EI</td></tr>
        <tbody id="sec_exempt">
            <tr class="table-col-header"><th class="rn">#</th><th>Particulars</th><th class="col-note">Section</th><th class="col-amt">Amount (₹)</th><th class="col-amt2">Action</th></tr>
            <tbody id="exempt_rows">
                @forelse($computation->exemptIncomes ?? [] as $index => $item)
                <tr data-group="exempt" data-index="{{ $index }}">
                    <td class="rn">{{ $loop->iteration }}</td>
                    <td><input class="inp inp-text" name="exempt_incomes[{{ $index }}][particular]" value="{{ $item->particular }}"></td>
                    <td class="ctr"><input class="inp" style="text-align:center;" name="exempt_incomes[{{ $index }}][section]" value="{{ $item->section }}"></td>
                    <td class="amt"><input class="inp" name="exempt_incomes[{{ $index }}][amount]" value="{{ $item->amount }}"></td>
                    <td class="action-cell"><button class="dyn-btn del" data-group="exempt">✕</button></td>
                </tr>
                @empty
                <tr data-group="exempt" data-index="0">
                    <td class="rn">1</td>
                    <td><input class="inp inp-text" name="exempt_incomes[0][particular]" value="Agricultural Income"></td>
                    <td class="ctr"><input class="inp" style="text-align:center;" name="exempt_incomes[0][section]" value="10(1)"></td>
                    <td class="amt"><input class="inp" name="exempt_incomes[0][amount]" value="0"></td>
                    <td class="action-cell"><button class="dyn-btn del" data-group="exempt">✕</button></td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="tds-total"><td colspan="4" style="text-align:right;font-weight:bold;">Total Exempt Income</td><td><button class="dyn-btn add" data-group="exempt" data-tbody="exempt_rows" data-template="tpl_exempt">+ Add</button></td></tr>
            </tfoot>
        </tbody>
    </table>
    <template id="tpl_exempt">
        <tr data-group="exempt" data-index="__INDEX__">
            <td class="rn">__INDEX_PLUS_ONE__</td>
            <td><input class="inp inp-text" name="exempt_incomes[__INDEX__][particular]" placeholder="Particulars"></td>
            <td class="ctr"><input class="inp" style="text-align:center;" name="exempt_incomes[__INDEX__][section]" placeholder="Sec."></td>
            <td class="amt"><input class="inp" name="exempt_incomes[__INDEX__][amount]" value="0"></td>
            <td class="action-cell"><button class="dyn-btn del" data-group="exempt">✕</button></td>
        </tr>
    </template>
</div>