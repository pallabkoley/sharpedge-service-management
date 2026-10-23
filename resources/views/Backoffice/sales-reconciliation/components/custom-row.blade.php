{{-- Template for a custom row – used by JavaScript to clone --}}
<tr class="month-row">
    <td class="td-month">
        <input type="text" value="Custom" placeholder="Month" style="border:none;background:transparent;outline:none;font-weight:600;width:100%;font-size:11px;color:var(--text-dark);" onfocus="this.select()" />
    </td>
    <td><input class="td-amt" type="text" id="tax_custom" value="" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="igst_custom" value="" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="cgst_custom" value="" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="sgst_custom" value="" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="sgsttds_custom" value="" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="cgsttds_custom" value="" oninput="calcTotal()" onfocus="this.select()" /></td>
    <td><input class="td-amt" type="text" id="inv_custom" value="" oninput="calcTotal()" onfocus="this.select()" readonly style="background:#f4f7fc;cursor:default;" /></td>
    <td class="td-remark"><input type="text" id="rem_custom" value="" placeholder="—" /></td>
    <td class="td-actions"><button class="del-btn" onclick="deleteCustomRow(0)"><i class="bi bi-x-circle"></i></button></td>
</tr>