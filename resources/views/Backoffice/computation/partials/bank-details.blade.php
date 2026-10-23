<div class="section-block">
    <table class="comp-table">
        <tr class="row-section">
            <td class="rn">BK</td>
            <td colspan="5">BANK DETAILS <button class="collapse-btn" data-toggle="sec_bank">▼</button></td>
        </tr>
        <tbody id="sec_bank">
            <tr><td colspan="6" style="padding:0;border:none;">
                <table class="tds-table">
                    <tr><th style="width:28px;">#</th><th>Bank Name</th><th>Account Number</th><th>IFSC</th><th style="width:80px;">Action</th></tr>
                    <tbody id="bank_rows">
                        @forelse($computation->bankAccounts ?? [] as $index => $bank)
                        <tr data-group="bank" data-index="{{ $index }}">
                            <td class="ctr">{{ $loop->iteration }}</td>
                            <td><input class="inp inp-text" name="bank_accounts[{{ $index }}][bank_name]" value="{{ $bank->bank_name }}"></td>
                            <td><input class="inp inp-text" name="bank_accounts[{{ $index }}][account_number]" value="{{ $bank->account_number }}"></td>
                            <td><input class="inp inp-text" name="bank_accounts[{{ $index }}][ifsc]" value="{{ $bank->ifsc }}"></td>
                            <td class="action-cell"><button class="dyn-btn del" data-group="bank">✕</button></td>
                        </tr>
                        @empty
                        <tr data-group="bank" data-index="0"><td class="ctr">1</td><td><input class="inp inp-text" name="bank_accounts[0][bank_name]" value=""></td><td><input class="inp inp-text" name="bank_accounts[0][account_number]" value=""></td><td><input class="inp inp-text" name="bank_accounts[0][ifsc]" value=""></td><td class="action-cell"><button class="dyn-btn del" data-group="bank">✕</button></td></tr>
                        @endforelse
                    </tbody>
                    <tfoot><tr class="tds-total"><td colspan="4" style="text-align:right;font-weight:bold;">Total Banks</td><td><button class="dyn-btn add" data-group="bank" data-tbody="bank_rows" data-template="tpl_bank">+ Add Bank</button></td></tr></tfoot>
                </table>
            </td></tr>
        </tbody>
    </table>
    <template id="tpl_bank">
        <tr data-group="bank" data-index="__INDEX__"><td class="ctr">__INDEX_PLUS_ONE__</td><td><input class="inp inp-text" name="bank_accounts[__INDEX__][bank_name]" placeholder="Bank Name"></td><td><input class="inp inp-text" name="bank_accounts[__INDEX__][account_number]" placeholder="Account No."></td><td><input class="inp inp-text" name="bank_accounts[__INDEX__][ifsc]" placeholder="IFSC"></td><td class="action-cell"><button class="dyn-btn del" data-group="bank">✕</button></td></tr>
    </template>
</div>