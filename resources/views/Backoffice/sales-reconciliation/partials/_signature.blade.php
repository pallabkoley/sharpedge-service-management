<table class="footer-table">
    <tr>

        <!-- LEFT COLUMN -->
        <td class="ft-col-left">
            <table class="ft-inner">

                <!-- Prepared By -->
                <tr>
                    <td class="ft-label-sm">Prepared By</td>
                    <td class="ft-value-sm">
                        <input
                            type="text"
                            value="{{ $register?->prepared_by ?? 'SharpEdge India' }}"
                            id="prepared_by"
                        />
                    </td>
                </tr>

                <!-- Chartered Accountant -->
                <tr>
                    <td class="ft-label-sm">Chartered Accountant</td>
                    <td class="ft-value-sm">
                        <input
                            type="text"
                            value="{{ $register?->chartered_accountant ?? 'Ankit Hansaria' }}"
                            id="chartered_accountant"
                        />
                    </td>
                </tr>

                <!-- Membership Number -->
                <tr>
                    <td class="ft-label-sm">Membership Number</td>
                    <td class="ft-value-sm">
                        <input
                            type="text"
                            value="{{ $register?->membership_number ?? '301586' }}"
                            id="membership_number"
                            style="font-family:var(--font-mono);"
                        />
                    </td>
                </tr>

                <!-- Place -->
                <tr>
                    <td class="ft-label-sm">Place</td>
                    <td class="ft-value-sm">
                        <input
                            type="text"
                            value="{{ $register?->place ?? 'Kolkata' }}"
                            id="place"
                        />
                    </td>
                </tr>

                <!-- Date -->
                <tr>
                    <td class="ft-label-sm">Date</td>
                    <td class="ft-value-sm">
                        <input
                            type="date"
                            value="{{ $register?->signing_date?->format('Y-m-d') ?? '' }}"
                            id="signing_date"
                            style="font-family:var(--font-mono);"
                        />
                    </td>
                </tr>

                <!-- Assessee Signature -->
                <tr>
                    <td colspan="2" style="padding-top:6px;">

                        <div style="font-weight:600;color:#1a3a6c;font-size:10px;">
                            Signature of Assessee
                        </div>

                        <div class="ft-assessee-line">
                            <div class="ft-sign-line">
                                <input
                                    type="text"
                                    placeholder="Sign here"
                                    id="assessee_signature_text"
                                />
                            </div>
                        </div>

                    </td>
                </tr>

            </table>
        </td>


        <!-- DIVIDER -->
        <td class="ft-divider"></td>


        <!-- RIGHT COLUMN -->
        <td class="ft-col-right">
            <table class="ft-inner">

                <!-- CA Signature -->
                <tr>
                    <td colspan="2" style="text-align:center;padding-bottom:2px;">

                        <div style="font-weight:600;color:#1a3a6c;font-size:10px;">
                            Signature of Chartered Accountant
                        </div>

                        <div class="ft-ca-sig" id="caSigWrapper">

                            <div id="caSigDisplay">

                                @if (
                                    $register?->ca_signature &&
                                    Storage::disk('public')->exists($register->ca_signature)
                                )

                                    <img
                                        src="{{ Storage::url($register->ca_signature) }}"
                                        alt="CA Signature"
                                        style="
                                            max-height:38px;
                                            max-width:130px;
                                            border:1px solid #e0e7ef;
                                            border-radius:3px;
                                            padding:2px;
                                            background:#fff;
                                            display:inline-block;
                                        "
                                    />

                                @else

                                    <div class="ft-sign-line"></div>

                                @endif

                            </div>

                            <input
                                type="file"
                                id="caSigUpload"
                                accept="image/*"
                                style="display:none;"
                            />

                            <button
                                type="button"
                                class="ft-upload-btn"
                                onclick="document.getElementById('caSigUpload').click();"
                            >
                                <i class="bi bi-upload"></i>
                                Upload
                            </button>

                            <button
                                type="button"
                                class="ft-upload-btn"
                                onclick="clearCaSignature();"
                                style="margin-left:4px;"
                            >
                                <i class="bi bi-x-circle"></i>
                                Clear
                            </button>

                        </div>

                    </td>
                </tr>


                <!-- CA Details -->
                <tr>
                    <td
                        colspan="2"
                        style="text-align:center;padding:1px 0;"
                    >

                        <div class="ft-ca-name">
                            Chartered Accountant :
                            {{ $register?->chartered_accountant ?? 'Ankit Hansaria' }}
                        </div>

                        <div class="ft-ca-mem">
                            Membership Number :
                            {{ $register?->membership_number ?? '301586' }}
                        </div>

                    </td>
                </tr>


                <!-- Spacing -->
                <tr>
                    <td colspan="2" style="height:4px;"></td>
                </tr>


                <!-- ITR Acknowledgement -->
                <tr>
                    <td
                        class="ft-ack-label"
                        style="width:38%;"
                    >
                        ITR Acknowledgement No.
                    </td>

                    <td class="ft-ack-val">
                        <input
                            type="text"
                            value="{{ $register?->itr_acknowledgement_no ?? '' }}"
                            id="itr_acknowledgement_no"
                            placeholder="Enter ITR Ack No."
                        />
                    </td>
                </tr>


                <!-- Audit Acknowledgement -->
                <tr>
                    <td class="ft-ack-label">
                        Audit Acknowledgement No.
                    </td>

                    <td class="ft-ack-val">
                        <input
                            type="text"
                            value="{{ $register?->audit_acknowledgement_no ?? '' }}"
                            id="audit_acknowledgement_no"
                            placeholder="Enter Audit Ack No."
                        />
                    </td>
                </tr>


                <!-- 10IE Acknowledgement -->
                <tr>
                    <td class="ft-ack-label">
                        10IE Acknowledgement No.
                    </td>

                    <td class="ft-ack-val">
                        <input
                            type="text"
                            value="{{ $register?->ie10_acknowledgement_no ?? '' }}"
                            id="ie10_acknowledgement_no"
                            placeholder="Enter 10IE Ack No."
                        />
                    </td>
                </tr>


                <!-- UDIN -->
                <tr>
                    <td class="ft-ack-label">
                        UDIN
                    </td>

                    <td class="ft-ack-val">
                        <input
                            type="text"
                            value="{{ $register?->udin ?? '' }}"
                            id="udin"
                            placeholder="Enter UDIN"
                        />
                    </td>
                </tr>

            </table>
        </td>

    </tr>
</table>