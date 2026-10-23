<table class="sig-tbl">
    <tr>
        <td style="width:22%;"><div class="sl">PROPRIETOR / PARTNER</div><div class="sv"><input class="sii" value="{{ $balanceSheet->computation->trade_name ?? 'M/s Sharma Trading Company' }}"></div></td>
        <td style="width:22%;"><div class="sl">PLACE</div><div class="sv"><input class="sii" value="New Delhi"></div></td>
        <td style="width:33%;"><div class="sl" style="font-size:8.5px;color:#1a3a6c;">ITR Acknowledgement Number :</div><div class="sv"><input class="sii" placeholder="e.g. 987654321098765"></div></td>
        <td rowspan="3" style="text-align:center;vertical-align:middle;">
            <div class="sl" style="text-align:center;">Signature Of Chartered Accountant</div>
            <div style="height:42px;border-bottom:1px solid #546e7a;margin:8px 10px;"></div>
            <div style="font-size:8.5px;color:#546e7a;text-align:center;">{{ $balanceSheet->computation->auditor_name ?? 'R. Kumar, FCA' }} &nbsp;|&nbsp; M.No. 123456 &nbsp;|&nbsp; FRN: 001234N</div>
        </td>
    </tr>
    <tr>
        <td><div class="sl">DATE</div><div class="sv"><input class="sii" value="{{ date('d-m-Y') }}"></div></td>
        <td><div class="sl">AUDIT STATUS</div><div class="sv"><input class="sii" value="NON AUDITED"></div></td>
        <td><div class="sl" style="font-size:8.5px;color:#1a3a6c;">Audit Acknowledgement Number :</div><div class="sv"><input class="sii" placeholder="e.g. 123456789012345"></div></td>
    </tr>
    <tr>
        <td><div class="sl">UDIN</div><div class="sv"><input class="sii" placeholder="24XXXXXX…"></div></td>
        <td><div class="sl" style="font-size:8.5px;">RDIS</div><div class="sv"><input class="sii" value="NO"></div></td>
        <td><div class="sl" style="font-size:8.5px;color:#1a3a6c;">Audit Ack. No.</div><div class="sv"><input class="sii" placeholder="Audit Ack. No."></div></td>
    </tr>
</table>