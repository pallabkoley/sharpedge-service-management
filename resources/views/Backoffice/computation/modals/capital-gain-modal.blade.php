{{-- resources/views/Backoffice/computation/modals/capital-gain-modal.blade.php --}}

<div class="modal fade" id="cgModal" tabindex="-1" aria-labelledby="cgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#2c2c2c;color:#fff;padding:10px 20px;">
                <h5 class="modal-title" id="cgModalLabel" style="font-size:14px;font-weight:bold;">Capital Gain Computation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:15px 20px;background:#f9f9f9;">
                
                <p style="font-size:12px;color:#666;margin-bottom:15px;font-style:italic;border-bottom:1px solid #ddd;padding-bottom:8px;">
                    Capital Gain Computation — Enter values below and calculations update live.
                </p>

                {{-- ============================================================
                SECTION 1: SHORT TERM CAPITAL GAIN (STCG)
                ============================================================ --}}
                <table style="width:100%;border-collapse:collapse;margin-bottom:15px;border:1px solid #ddd;background:#fff;">
                    <thead>
                        <tr>
                            <th colspan="2" style="background:#1a3a5c;color:#fff;padding:6px 12px;text-align:left;font-size:12px;font-weight:bold;border-bottom:2px solid #0f2a4a;">
                                SHORT TERM CAPITAL GAIN (STCG)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;width:70%;">Sale Consideration</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_stcg_sale" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;">Cost of Acquisition</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_stcg_acq" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;">Brokerage Charges</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_stcg_brokerage" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;">Cost of Improvement</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_stcg_improve" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr style="background:#f0f4f8;">
                            <td style="padding:6px 12px;font-weight:bold;font-size:12px;border-top:2px solid #d0dce8;">
                                <strong>STCG = Sale - CA - Brokerage - Improvement</strong>
                            </td>
                            <td style="padding:6px 12px;text-align:right;font-weight:bold;font-size:13px;color:#1a3a5c;border-top:2px solid #d0dce8;">
                                <span id="cg_stcg_result">0</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- ============================================================
                SECTION 2: LONG TERM CAPITAL GAIN (LTCG)
                ============================================================ --}}
                <table style="width:100%;border-collapse:collapse;margin-bottom:15px;border:1px solid #ddd;background:#fff;">
                    <thead>
                        <tr>
                            <th colspan="2" style="background:#1a3a5c;color:#fff;padding:6px 12px;text-align:left;font-size:12px;font-weight:bold;border-bottom:2px solid #0f2a4a;">
                                LONG TERM CAPITAL GAIN (LTCG)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;width:70%;">Sale Consideration</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_ltcg_sale" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;">Indexed Cost of Acquisition</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_ltcg_acq" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;">Brokerage Charges</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_ltcg_brokerage" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;">Indexed Cost of Improvement</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_ltcg_improve" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr style="background:#f0f4f8;">
                            <td style="padding:6px 12px;font-weight:bold;font-size:12px;border-top:2px solid #d0dce8;">
                                <strong>LTCG Before Exemption</strong>
                            </td>
                            <td style="padding:6px 12px;text-align:right;font-weight:bold;font-size:13px;color:#1a3a5c;border-top:2px solid #d0dce8;">
                                <span id="cg_ltcg_result">0</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- ============================================================
                SECTION 3: EXEMPTIONS (DEDUCT FROM LTCG)
                ============================================================ --}}
                <table style="width:100%;border-collapse:collapse;margin-bottom:15px;border:1px solid #ddd;background:#fff;">
                    <thead>
                        <tr>
                            <th colspan="2" style="background:#1a3a5c;color:#fff;padding:6px 12px;text-align:left;font-size:12px;font-weight:bold;border-bottom:2px solid #0f2a4a;">
                                EXEMPTIONS (DEDUCT FROM LTCG)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;width:70%;">Exemption u/s 54</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_exempt_54" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;">Exemption u/s 54EC</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_exempt_54ec" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 12px;border-bottom:1px solid #eee;font-size:12px;">Exemption u/s 54F</td>
                            <td style="padding:2px 12px;border-bottom:1px solid #eee;text-align:right;">
                                <input type="text" id="cg_exempt_54f" value="0" style="width:140px;border:none;background:transparent;text-align:right;font-size:12px;font-weight:bold;outline:none;padding:3px 5px;border-bottom:1px solid #ccc;" onkeyup="recalcCgModal()" onchange="recalcCgModal()">
                            </td>
                        </tr>
                        <tr style="background:#f0f4f8;">
                            <td style="padding:6px 12px;font-weight:bold;font-size:12px;border-top:2px solid #d0dce8;">
                                <strong>Taxable LTCG = LTCG - Exemptions</strong>
                            </td>
                            <td style="padding:6px 12px;text-align:right;font-weight:bold;font-size:13px;color:#1a3a5c;border-top:2px solid #d0dce8;">
                                <span id="cg_ltcg_taxable">0</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- ============================================================
                SECTION 4: FINAL SUMMARY
                ============================================================ --}}
                <div style="background:#1a3a5c;color:#fff;padding:10px 15px;border-radius:0;margin-top:5px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:14px;font-weight:bold;">
                        <span>TOTAL CAPITAL GAIN (STCG + Taxable LTCG)</span>
                        <span id="cg_final_total" style="font-size:16px;color:#ffdd44;">0</span>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="background:#f5f5f5;padding:8px 15px;">
                <button type="button" class="dyn-btn" style="background:#666;color:#fff;border:none;padding:4px 20px;font-size:12px;cursor:pointer;border-radius:2px;" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="dyn-btn" style="background:#2c6c2c;color:#fff;border:none;padding:4px 20px;font-size:12px;cursor:pointer;border-radius:2px;" onclick="saveCgModal()">✅ Save</button>
            </div>
        </div>
    </div>
</div>