<div class="declaration-area">
    <div class="master-block-title" style="background:#2c2c2c;color:#fff;padding:4px 8px;font-size:11px;font-weight:bold;text-transform:uppercase;margin-bottom:8px;">VERIFICATION & DECLARATION</div>
    <div class="declaration-text">
        I, the undersigned, hereby declare that this computation of income and tax payable has been prepared based on the information, documents, books of accounts and other records as provided by the assessee / client. The figures are compiled to the best of my knowledge and information. The assessee is responsible for the correctness of the information provided. This computation is prepared for the purpose of Income Tax Return filing for Assessment Year 2025-26 under the Income Tax Act, 1961. No responsibility is accepted for any error, omission or misrepresentation arising out of incorrect information supplied by the assessee.<br><br>
        <em>"I/We have examined the accounts and records of M/S SHARMA TRADING CO. (PAN: ABCRS1234P) and certify that to the best of my/our information and belief and on the basis of information and explanation given, the computation reflects the true state of affairs."</em>
    </div>
    <div class="sig-grid">
        {{-- Prepared By --}}
        <div class="sig-block">
            <div class="sig-title">Prepared By</div>
            <div class="sig-row">
                <label>Name:</label>
                <input type="text" name="verification[prepared_by_name]" value="{{ old('verification.prepared_by_name', $computation->verification->prepared_by_name ?? '') }}" class="inp-text">
            </div>
            <div class="sig-row">
                <label>Membership No.:</label>
                <input type="text" name="verification[prepared_membership_no]" value="{{ old('verification.prepared_membership_no', $computation->verification->prepared_membership_no ?? '') }}" class="inp-text">
            </div>
            <div class="sig-row">
                <label>Firm Name:</label>
                <input type="text" name="verification[prepared_firm_name]" value="{{ old('verification.prepared_firm_name', $computation->verification->prepared_firm_name ?? '') }}" class="inp-text">
            </div>
            <div class="sig-row">
                <label>UDIN:</label>
                <input type="text" name="verification[prepared_udin]" value="{{ old('verification.prepared_udin', $computation->verification->prepared_udin ?? '') }}" class="inp-text">
            </div>
            <div class="sig-space"></div>
            <div class="sig-label">Signature of Chartered Accountant</div>
        </div>

        {{-- Verified By --}}
        <div class="sig-block">
            <div class="sig-title">Verified By</div>
            <div class="sig-row">
                <label>Name:</label>
                <input type="text" name="verification[verified_by_name]" value="{{ old('verification.verified_by_name', $computation->verification->verified_by_name ?? '') }}" class="inp-text">
            </div>
            <div class="sig-row">
                <label>Place:</label>
                <input type="text" name="verification[verified_place]" value="{{ old('verification.verified_place', $computation->verification->verified_place ?? '') }}" class="inp-text">
            </div>
            <div class="sig-row">
                <label>Date:</label>
                <input type="date" name="verification[verified_date]" value="{{ old('verification.verified_date', optional($computation->verification->verified_date)->format('Y-m-d') ?? '') }}" class="inp-text">
            </div>
            <div class="sig-row">
                <label>Seal / Stamp:</label>
                <input type="text" name="verification[verified_seal]" value="{{ old('verification.verified_seal', $computation->verification->verified_seal ?? '') }}" class="inp-text" placeholder="[FIRM SEAL]">
            </div>
            <div class="sig-space"></div>
            <div class="sig-label">Signature with Date & Seal</div>
        </div>

        {{-- Assessee's Confirmation --}}
        <div class="sig-block">
            <div class="sig-title">Assessee's Confirmation</div>
            <div class="sig-row">
                <label>Name:</label>
                <input type="text" name="verification[assessee_name]" value="{{ old('verification.assessee_name', $computation->verification->assessee_name ?? $computation->client->name ?? '') }}" class="inp-text">
            </div>
            <div class="sig-row">
                <label>PAN:</label>
                <input type="text" name="verification[assessee_pan]" value="{{ old('verification.assessee_pan', $computation->verification->assessee_pan ?? $computation->client->pan ?? '') }}" class="inp-text">
            </div>
            <div class="sig-row">
                <label>Date:</label>
                <input type="date" name="verification[assessee_date]" value="{{ old('verification.assessee_date', optional($computation->verification->assessee_date)->format('Y-m-d') ?? date('Y-m-d')) }}" class="inp-text">
            </div>
            <div class="sig-row">
                <label>Place:</label>
                <input type="text" name="verification[assessee_place]" value="{{ old('verification.assessee_place', $computation->verification->assessee_place ?? '') }}" class="inp-text">
            </div>
            <div class="sig-space"></div>
            <div class="sig-label">Assessee's Signature</div>
        </div>
    </div>
</div>