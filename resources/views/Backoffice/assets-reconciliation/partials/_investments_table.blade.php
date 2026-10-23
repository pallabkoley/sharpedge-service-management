{{-- ─── INVESTMENTS TABLE ─── --}}
<div class="section-header" style="border-top:2px solid #4a6fa5;">
    <h5>📈 Investments</h5>
    <div class="section-tools">
        <span class="collapse-toggle" onclick="toggleSection('investmentsBody')">
            <span class="arrow open" id="investmentsArrow">▶</span> Collapse
        </span>
        <button class="btn-erp btn-ghost btn-sm" onclick="addInvestmentRow()" style="font-size:10px;padding:2px 10px;">
            <i class="bi bi-plus-circle"></i> Add
        </button>
    </div>
</div>
<div class="section-body" id="investmentsBody">
    <div class="table-wrap" style="max-height:200px;">
        <table class="recon-table" style="min-width:700px;">
            <thead>
                <tr>
                    <th style="min-width:150px;">Investment Type</th>
                    <th style="min-width:100px;">Basic <span class="sub-label">(₹)</span></th>
                    <th style="min-width:100px;">Interest <span class="sub-label">(₹)</span></th>
                    <th style="min-width:110px;">Total Value <span class="sub-label">(₹)</span></th>
                    <th style="min-width:140px;">Company Name</th>
                    <th style="min-width:110px;">Company PAN</th>
                    <th style="width:40px;">&nbsp;</th>
                </tr>
            </thead>
            <tbody id="investmentsBodyRows">
                {{-- Rows will be injected by JavaScript --}}
            </tbody>
        </table>
    </div>
</div>