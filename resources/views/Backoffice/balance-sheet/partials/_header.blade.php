<div class="hr-r">IN THE BOOKS OF</div>
<div class="hr-r lt"><input class="shi" value="{{ $balanceSheet->computation->trade_name ?? 'M/S SHARMA TRADING COMPANY' }}" style="font-weight:700;font-size:12px;color:#0d2a5e;"></div>
<div class="hr-r" style="font-size:10px;font-weight:500;padding:2px 6px;background:#2a5aa0;">
  TRADE CODE &nbsp;|&nbsp; <input class="shi" value="STC / 2024-25" style="display:inline;width:110px;font-size:10px;color:#fff;">
</div>
<div class="hr-r lt"><input class="shi" value="0 &nbsp;&nbsp; TRADE CODE &nbsp;&nbsp; 0 0" style="font-size:10px;font-weight:500;"></div>
<div class="hr-r tt">BALANCE SHEET</div>
<div class="hr-r pd">
  FOR THE FINANCIAL YEAR ENDED &nbsp;
  <input class="shi" value="{{ $balanceSheet->computation->assessment_year ?? '31-MAR-2025' }}" style="display:inline;width:95px;font-size:10px;color:#0d2a5e;font-weight:700;">
  &nbsp;|&nbsp; AY &nbsp;<input class="shi" value="{{ $balanceSheet->computation->assessment_year ?? '2025-26' }}" style="display:inline;width:65px;font-size:10px;color:#0d2a5e;font-weight:700;">
</div>