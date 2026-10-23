<div class="pdf-footer">
    <div class="footer-divider"></div>
    <table class="footer-table">
        <tr>
            <td class="footer-left">
                Generated: {{ now()->format('d-m-Y h:i:s A') }}
            </td>
            <td class="footer-center">
                System Generated Document
            </td>
            <td class="footer-right">
                Page {PAGE_NUM} of {PAGE_COUNT}
            </td>
        </tr>
    </table>
</div>