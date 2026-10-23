<div class="table-wrap">
    <table class="recon-table" id="reconTable">
        <thead>
            <tr>
                <th style="min-width:60px;">Month</th>
                <th style="min-width:100px;">Taxable Value <span class="sub-label">(₹)</span></th>
                <th style="min-width:110px;">Exempted / Nil Rated <span class="sub-label">(₹)</span></th>
                <th style="min-width:85px;">IGST <span class="sub-label">(₹)</span></th>
                <th style="min-width:85px;">CGST <span class="sub-label">(₹)</span></th>
                <th style="min-width:85px;">SGST <span class="sub-label">(₹)</span></th>
                <th style="min-width:85px;">CGST TDS <span class="sub-label">(₹)</span></th>
                <th style="min-width:85px;">SGST TDS <span class="sub-label">(₹)</span></th>
                <th style="min-width:110px;">Invoice Value <span class="sub-label">(₹)</span></th>
                <th style="min-width:120px;">Remarks</th>
                <th style="min-width:45px;width:45px;">&nbsp;</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            {{-- JavaScript will render month rows --}}
            {{-- Total row – always present, JavaScript updates the values --}}
            <tr class="total-row" id="totalRow">
                <td>TOTAL</td>
                <td><span class="td-amt total" id="tot_taxable">-</span></td>
                <td><span class="td-amt total" id="tot_exempt">-</span></td>
                <td><span class="td-amt total" id="tot_igst">-</span></td>
                <td><span class="td-amt total" id="tot_cgst">-</span></td>
                <td><span class="td-amt total" id="tot_sgst">-</span></td>
                <td><span class="td-amt total" id="tot_cgsttds">-</span></td>
                <td><span class="td-amt total" id="tot_sgsttds">-</span></td>
                <td><span class="td-amt total" id="tot_inv">-</span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
</div>