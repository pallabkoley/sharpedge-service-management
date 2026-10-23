@php
    // =====================================================
    // PRODUCTION BALANCE SHEET – LEDGER‑DRIVEN (Mock Data)
    // =====================================================
    
    // Helper to format numbers with commas and parentheses for negatives
    function formatAmount($n) {
        if ($n === null || $n === '') return '-';
        $neg = $n < 0;
        $val = abs($n);
        $formatted = number_format($val, 0, '.', ',');
        return $neg ? '(' . $formatted . ')' : $formatted;
    }

    // Simulate ledger data (replace with DB calls)
    $ledgerData = [
        // Capital Account
        'cap_open' => 1200000, 'cap_profit' => 924800, 'cap_draw' => -120000,
        'cap_reserve' => 250000, 'cap_capres' => 0, 'cap_secprem' => 0, 'cap_reval' => 0,
        // Secured Loans
        'sec_term' => 1500000, 'sec_cc' => 425000, 'sec_od' => 175000, 'sec_vehicle' => 300000,
        // Unsecured
        'unsec_dir' => 0, 'unsec_other' => 0,
        // Current Liabilities
        'cl_sundry' => 680000, 'cl_bills' => 0, 'cl_gst' => 120000, 'cl_tds' => 45000,
        'cl_pfesi' => 32000, 'cl_ptax' => 8000, 'cl_salary' => 110000, 'cl_rent' => 45000,
        'cl_audit' => 25000, 'cl_interest' => 28000, 'cl_advcust' => 150000, 'cl_other' => 0,
        // Provisions
        'prov_tax' => 120000, 'prov_doubt' => 28000, 'prov_warr' => 0,
        // Deferred Tax
        'dtl' => 48000,
        // PPE
        'fa_land' => 1200000, 'fa_build' => 800000, 'dep_build' => -80000,
        'fa_plant' => 900000, 'dep_plant' => -135000,
        'fa_furn' => 350000, 'dep_furn' => -52500,
        'fa_veh' => 500000, 'dep_veh' => -150000,
        'fa_comp' => 240000, 'dep_comp' => -96000,
        'fa_office' => 120000, 'dep_office' => -18000,
        // Intangible
        'int_gw' => 250000, 'int_pt' => 135000, 'int_soft' => 85000,
        // Investments
        'inv_lt' => 500000, 'inv_oth' => 275000,
        // Inventories
        'inv_raw' => 420000, 'inv_wip' => 150000, 'inv_fg' => 540000, 'inv_transit' => 0, 'inv_pack' => 0,
        // Receivables
        'ca_debt' => 840000, 'ca_bills' => 0,   // ca_provdebt is derived from prov_doubt
        // Cash
        'cash_hand' => 124000, 'cash_sbi' => 350000, 'cash_hdfc' => 225000,
        'cash_icici' => 180000, 'cash_axis' => 87000, 'cash_fd' => 200000,
        // Advances
        'adv_supp' => 120000, 'adv_staff' => 25000, 'adv_loanemp' => 0,
        'adv_pre' => 23000, 'adv_accrued' => 32000,
        // Tax Assets
        'tax_gst' => 210000, 'tax_tds' => 38000, 'tax_adv' => 120000, 'tax_itc' => 45000,
    ];

    // Build HTML rows (same structure as before but with ledger values)
    $liabilitiesRows = [
        ['type' => 'section', 'label' => 'CAPITAL ACCOUNT', 'sectionId' => 'ls_cap'],
        ['type' => 'row', 'label' => 'Opening Capital (Sharma)', 'note' => '1', 'innerId' => 'cap_open', 'value' => $ledgerData['cap_open'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Add: Net Profit (from P&L)', 'note' => '2', 'innerId' => 'cap_profit', 'value' => $ledgerData['cap_profit'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Less: Drawings', 'note' => '3', 'innerId' => 'cap_draw', 'value' => $ledgerData['cap_draw'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Closing Capital', 'note' => '4', 'innerId' => 'cap_close', 'value' => null, 'isCalculated' => true, 'depth' => 1],
        ['type' => 'row', 'label' => 'General Reserve', 'note' => '5', 'innerId' => 'cap_reserve', 'value' => $ledgerData['cap_reserve'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Capital Reserve', 'note' => '6', 'innerId' => 'cap_capres', 'value' => $ledgerData['cap_capres'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Securities Premium', 'note' => '7', 'innerId' => 'cap_secprem', 'value' => $ledgerData['cap_secprem'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Revaluation Reserve', 'note' => '8', 'innerId' => 'cap_reval', 'value' => $ledgerData['cap_reval'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Capital & Reserves', 'totalId' => 'totalCapital'],

        ['type' => 'section', 'label' => 'SECURED LOANS', 'sectionId' => 'ls_sec'],
        ['type' => 'row', 'label' => 'Term Loans (Banks)', 'note' => '9', 'innerId' => 'sec_term', 'value' => $ledgerData['sec_term'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Cash Credit / Working Capital', 'note' => '10', 'innerId' => 'sec_cc', 'value' => $ledgerData['sec_cc'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Overdraft', 'note' => '11', 'innerId' => 'sec_od', 'value' => $ledgerData['sec_od'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Vehicle Loan', 'note' => '12', 'innerId' => 'sec_vehicle', 'value' => $ledgerData['sec_vehicle'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Secured Loans', 'totalId' => 'totalSecured'],

        ['type' => 'section', 'label' => 'UNSECURED LOANS', 'sectionId' => 'ls_unsec'],
        ['type' => 'row', 'label' => 'Loans from Directors / Partners', 'note' => '13', 'innerId' => 'unsec_dir', 'value' => $ledgerData['unsec_dir'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Other Unsecured Loans', 'note' => '14', 'innerId' => 'unsec_other', 'value' => $ledgerData['unsec_other'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Unsecured Loans', 'totalId' => 'totalUnsecured'],

        ['type' => 'section', 'label' => 'CURRENT LIABILITIES', 'sectionId' => 'ls_cl'],
        ['type' => 'row', 'label' => 'Sundry Creditors', 'note' => '15', 'innerId' => 'cl_sundry', 'value' => $ledgerData['cl_sundry'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Bills Payable', 'note' => '16', 'innerId' => 'cl_bills', 'value' => $ledgerData['cl_bills'], 'depth' => 1],
        ['type' => 'row', 'label' => 'GST Payable', 'note' => '17', 'innerId' => 'cl_gst', 'value' => $ledgerData['cl_gst'], 'depth' => 1],
        ['type' => 'row', 'label' => 'TDS Payable', 'note' => '18', 'innerId' => 'cl_tds', 'value' => $ledgerData['cl_tds'], 'depth' => 1],
        ['type' => 'row', 'label' => 'PF / ESI Payable', 'note' => '19', 'innerId' => 'cl_pfesi', 'value' => $ledgerData['cl_pfesi'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Professional Tax Payable', 'note' => '20', 'innerId' => 'cl_ptax', 'value' => $ledgerData['cl_ptax'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Outstanding Salary', 'note' => '21', 'innerId' => 'cl_salary', 'value' => $ledgerData['cl_salary'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Outstanding Rent', 'note' => '22', 'innerId' => 'cl_rent', 'value' => $ledgerData['cl_rent'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Audit Fees Payable', 'note' => '23', 'innerId' => 'cl_audit', 'value' => $ledgerData['cl_audit'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Interest Payable', 'note' => '24', 'innerId' => 'cl_interest', 'value' => $ledgerData['cl_interest'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Advance from Customers', 'note' => '25', 'innerId' => 'cl_advcust', 'value' => $ledgerData['cl_advcust'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Other Current Liabilities', 'note' => '26', 'innerId' => 'cl_other', 'value' => $ledgerData['cl_other'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Current Liabilities', 'totalId' => 'totalCurrentLiab'],

        ['type' => 'section', 'label' => 'PROVISIONS', 'sectionId' => 'ls_prov'],
        ['type' => 'row', 'label' => 'Provision for Tax', 'note' => '27', 'innerId' => 'prov_tax', 'value' => $ledgerData['prov_tax'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Provision for Doubtful Debts', 'note' => '28', 'innerId' => 'prov_doubt', 'value' => $ledgerData['prov_doubt'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Provision for Warranty', 'note' => '29', 'innerId' => 'prov_warr', 'value' => $ledgerData['prov_warr'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Provisions', 'totalId' => 'totalProvisions'],

        ['type' => 'section', 'label' => 'DEFERRED TAX LIABILITY', 'sectionId' => 'ls_dt'],
        ['type' => 'row', 'label' => 'Deferred Tax Liability', 'note' => '30', 'innerId' => 'dtl', 'value' => $ledgerData['dtl'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Deferred Tax', 'totalId' => 'totalDTL'],

        ['type' => 'section', 'label' => 'SUSPENSE ACCOUNT (Difference)', 'sectionId' => 'ls_suspense'],
        ['type' => 'row', 'label' => 'Difference (Balancing Figure)', 'note' => '31', 'innerId' => 'suspense', 'value' => 0, 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Suspense', 'totalId' => 'totalSuspense'],

        ['type' => 'grandtotal', 'label' => 'TOTAL LIABILITIES', 'totalId' => 'totalLiabilities'],
    ];

    // Assets side similarly built from $ledgerData
    $assetsRows = [
        ['type' => 'section', 'label' => 'PROPERTY, PLANT & EQUIPMENT', 'sectionId' => 'as_ppe'],
        ['type' => 'row', 'label' => 'Land', 'note' => 'A1', 'innerId' => 'fa_land', 'value' => $ledgerData['fa_land'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Building', 'note' => 'A2', 'innerId' => 'fa_build', 'value' => $ledgerData['fa_build'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Less: Depreciation (Building)', 'note' => 'A3', 'innerId' => 'dep_build', 'value' => $ledgerData['dep_build'], 'depth' => 2],
        ['type' => 'row', 'label' => 'Plant & Machinery', 'note' => 'A4', 'innerId' => 'fa_plant', 'value' => $ledgerData['fa_plant'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Less: Depreciation (Machinery)', 'note' => 'A5', 'innerId' => 'dep_plant', 'value' => $ledgerData['dep_plant'], 'depth' => 2],
        ['type' => 'row', 'label' => 'Furniture & Fixtures', 'note' => 'A6', 'innerId' => 'fa_furn', 'value' => $ledgerData['fa_furn'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Less: Depreciation (Furniture)', 'note' => 'A7', 'innerId' => 'dep_furn', 'value' => $ledgerData['dep_furn'], 'depth' => 2],
        ['type' => 'row', 'label' => 'Vehicles', 'note' => 'A8', 'innerId' => 'fa_veh', 'value' => $ledgerData['fa_veh'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Less: Depreciation (Vehicles)', 'note' => 'A9', 'innerId' => 'dep_veh', 'value' => $ledgerData['dep_veh'], 'depth' => 2],
        ['type' => 'row', 'label' => 'Computer & Accessories', 'note' => 'A10', 'innerId' => 'fa_comp', 'value' => $ledgerData['fa_comp'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Less: Depreciation (Computer)', 'note' => 'A11', 'innerId' => 'dep_comp', 'value' => $ledgerData['dep_comp'], 'depth' => 2],
        ['type' => 'row', 'label' => 'Office Equipment', 'note' => 'A12', 'innerId' => 'fa_office', 'value' => $ledgerData['fa_office'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Less: Depreciation (Office Eq.)', 'note' => 'A13', 'innerId' => 'dep_office', 'value' => $ledgerData['dep_office'], 'depth' => 2],
        ['type' => 'subtotal', 'label' => 'Total PPE (Net)', 'totalId' => 'totalPPE'],

        ['type' => 'section', 'label' => 'INTANGIBLE ASSETS', 'sectionId' => 'as_int'],
        ['type' => 'row', 'label' => 'Goodwill', 'note' => 'B1', 'innerId' => 'int_gw', 'value' => $ledgerData['int_gw'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Patents & Trademarks', 'note' => 'B2', 'innerId' => 'int_pt', 'value' => $ledgerData['int_pt'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Software', 'note' => 'B3', 'innerId' => 'int_soft', 'value' => $ledgerData['int_soft'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Intangible Assets', 'totalId' => 'totalIntang'],

        ['type' => 'section', 'label' => 'INVESTMENTS', 'sectionId' => 'as_inv'],
        ['type' => 'row', 'label' => 'Long Term Investments', 'note' => 'C1', 'innerId' => 'inv_lt', 'value' => $ledgerData['inv_lt'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Other Investments', 'note' => 'C2', 'innerId' => 'inv_oth', 'value' => $ledgerData['inv_oth'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Investments', 'totalId' => 'totalInv'],

        ['type' => 'section', 'label' => 'INVENTORIES', 'sectionId' => 'as_inv_det'],
        ['type' => 'row', 'label' => 'Raw Materials', 'note' => 'D1', 'innerId' => 'inv_raw', 'value' => $ledgerData['inv_raw'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Work in Progress', 'note' => 'D2', 'innerId' => 'inv_wip', 'value' => $ledgerData['inv_wip'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Finished Goods', 'note' => 'D3', 'innerId' => 'inv_fg', 'value' => $ledgerData['inv_fg'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Stock in Transit', 'note' => 'D4', 'innerId' => 'inv_transit', 'value' => $ledgerData['inv_transit'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Packing Materials', 'note' => 'D5', 'innerId' => 'inv_pack', 'value' => $ledgerData['inv_pack'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Inventories', 'totalId' => 'totalInventory'],

        ['type' => 'section', 'label' => 'TRADE RECEIVABLES', 'sectionId' => 'as_rec'],
        ['type' => 'row', 'label' => 'Sundry Debtors', 'note' => 'E1', 'innerId' => 'ca_debt', 'value' => $ledgerData['ca_debt'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Bills Receivable', 'note' => 'E2', 'innerId' => 'ca_bills', 'value' => $ledgerData['ca_bills'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Less: Provision for Doubtful Debts', 'note' => 'E3', 'innerId' => 'ca_provdebt', 'value' => -abs($ledgerData['prov_doubt']), 'isReadOnly' => true, 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Net Trade Receivables', 'totalId' => 'totalReceivables'],

        ['type' => 'section', 'label' => 'CASH & CASH EQUIVALENTS', 'sectionId' => 'as_cash'],
        ['type' => 'row', 'label' => 'Cash in Hand', 'note' => 'F1', 'innerId' => 'cash_hand', 'value' => $ledgerData['cash_hand'], 'depth' => 1],
        ['type' => 'row', 'label' => 'SBI Current Account', 'note' => 'F2', 'innerId' => 'cash_sbi', 'value' => $ledgerData['cash_sbi'], 'depth' => 1],
        ['type' => 'row', 'label' => 'HDFC Bank Account', 'note' => 'F3', 'innerId' => 'cash_hdfc', 'value' => $ledgerData['cash_hdfc'], 'depth' => 1],
        ['type' => 'row', 'label' => 'ICICI Bank Account', 'note' => 'F4', 'innerId' => 'cash_icici', 'value' => $ledgerData['cash_icici'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Axis Bank Account', 'note' => 'F5', 'innerId' => 'cash_axis', 'value' => $ledgerData['cash_axis'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Fixed Deposit', 'note' => 'F6', 'innerId' => 'cash_fd', 'value' => $ledgerData['cash_fd'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Cash & Bank', 'totalId' => 'totalCash'],

        ['type' => 'section', 'label' => 'LOANS & ADVANCES', 'sectionId' => 'as_adv'],
        ['type' => 'row', 'label' => 'Advance to Suppliers', 'note' => 'G1', 'innerId' => 'adv_supp', 'value' => $ledgerData['adv_supp'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Staff Advances', 'note' => 'G2', 'innerId' => 'adv_staff', 'value' => $ledgerData['adv_staff'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Loans to Employees', 'note' => 'G3', 'innerId' => 'adv_loanemp', 'value' => $ledgerData['adv_loanemp'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Prepaid Expenses', 'note' => 'G4', 'innerId' => 'adv_pre', 'value' => $ledgerData['adv_pre'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Accrued Income', 'note' => 'G5', 'innerId' => 'adv_accrued', 'value' => $ledgerData['adv_accrued'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Loans & Advances', 'totalId' => 'totalAdvances'],

        ['type' => 'section', 'label' => 'TAX RELATED ASSETS', 'sectionId' => 'as_tax'],
        ['type' => 'row', 'label' => 'GST Receivable', 'note' => 'H1', 'innerId' => 'tax_gst', 'value' => $ledgerData['tax_gst'], 'depth' => 1],
        ['type' => 'row', 'label' => 'TDS Receivable', 'note' => 'H2', 'innerId' => 'tax_tds', 'value' => $ledgerData['tax_tds'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Advance Tax', 'note' => 'H3', 'innerId' => 'tax_adv', 'value' => $ledgerData['tax_adv'], 'depth' => 1],
        ['type' => 'row', 'label' => 'Input Tax Credit', 'note' => 'H4', 'innerId' => 'tax_itc', 'value' => $ledgerData['tax_itc'], 'depth' => 1],
        ['type' => 'subtotal', 'label' => 'Total Tax Assets', 'totalId' => 'totalTaxAssets'],

        ['type' => 'grandtotal', 'label' => 'TOTAL ASSETS', 'totalId' => 'totalAssets'],
    ];

    $maxRows = max(count($liabilitiesRows), count($assetsRows));
@endphp

@extends('layouts.Backoffice')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<style>
:root {
  --navy: #0d1b2e; --navy-light: #1a2f4a; --tbl: #4a6fa5;
  --border: #dde3ec; --off-white: #f8f9fc;
  --font-mono: 'IBM Plex Mono', monospace;
  --font-sans: 'IBM Plex Sans', sans-serif;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body { background: var(--off-white); font-family: var(--font-sans); font-size: 12px; }
#app { display: grid; grid-template-columns: 1fr 280px; grid-template-rows: auto 1fr auto; height: 100vh; }

#topbar { grid-column: 1/3; background: white; border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 16px; height: 56px; gap: 12px; }
.tb-bc { font-size: 11.5px; color: #607080; }
.tb-srch { display: flex; align-items: center; gap: 6px; background: var(--off-white); border: 1px solid var(--border); border-radius: 6px; padding: 5px 10px; width: 220px; }
.tb-srch input { border: none; background: none; outline: none; font-size: 12px; width: 100%; }
.tb-ac { display: flex; align-items: center; gap: 6px; margin-left: auto; }
.btn-erp { display: flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 5px; font-size: 11px; font-weight: 600; cursor: pointer; border: 1px solid transparent; background: var(--off-white); border-color: var(--border); }
.btn-erp.btn-gn { background: #00897b; color: white; border-color: #00897b; }
.user-pill { display: flex; align-items: center; gap: 7px; padding: 4px 10px 4px 4px; border-radius: 20px; background: var(--off-white); border: 1px solid var(--border); cursor: pointer; }
.user-av { width: 26px; height: 26px; border-radius: 50%; background: linear-gradient(135deg, #1565c0, #00897b); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: white; }

#main { grid-column: 1; overflow-y: auto; padding: 16px 18px; background: var(--off-white); }
.paper { background: white; border: 2px solid var(--tbl); border-radius: 8px; overflow-x: auto; margin-bottom: 16px; position: relative; }
.wm { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 72px; font-weight: 700; color: rgba(200,210,230,.14); pointer-events: none; z-index: 1; white-space: nowrap; }
.hr-r { background: #1a3a6c; color: white; text-align: center; padding: 4px 6px; font-size: 11px; font-weight: 700; }
.hr-r.lt { background: #d6e4f7; color: #0d2a5e; }
.hr-r.tt { font-size: 13px; }
.hr-r.pd { background: #d6e4f7; color: #0d2a5e; font-size: 10px; }
.shi { background: transparent; border: none; outline: none; text-align: center; width: 100%; font-weight: 600; }

.bs-tbl { width: 100%; border-collapse: collapse; font-size: 11px; table-layout: fixed; }
.bs-tbl th, .bs-tbl td { border: 1px solid var(--tbl); padding: 4px 6px; vertical-align: middle; }
.col-h { background: #1a3a6c; color: white; text-align: center; font-size: 9.5px; font-weight: 700; }
.col-h2 { background: #2a5aa0; color: #c8ddf0; text-align: center; font-size: 9.5px; font-weight: 600; }
.sh-r td { background: #d6e4f7; color: #0d2a5e; font-weight: 700; font-size: 10.5px; }
.sh-toggle { float: right; cursor: pointer; font-size: 11px; color: #1565c0; }
.dr td { background: #f7faff; }
.dr:nth-child(even) td { background: white; }
.dr:hover td { background: #e3f0fd !important; }
.td-pt { padding-left: 8px; display: flex; align-items: center; gap: 4px; }
.bul { color: #2a5aa0; font-size: 8px; }
.td-note { text-align: center; font-family: var(--font-mono); font-size: 10px; color: #546e7a; width: 40px; }
.td-inner, .td-outer { text-align: right; font-family: var(--font-mono); font-size: 11px; white-space: nowrap; }
.td-inner input { width: 100%; text-align: right; font-family: var(--font-mono); font-size: 11px; border: none; background: transparent; padding: 2px 4px; margin: -2px -4px; }
.td-inner input:focus { outline: 2px solid #f9a825; border-radius: 2px; background: #fffde7; }
.st-r td { background: #dbeafe; color: #1565c0; font-weight: 700; }
.gt-r td { background: #1a3a6c; color: white; font-weight: 700; }
.dc { background: var(--tbl); width: 3px; padding: 0 !important; }
.sec-body.collapsed { display: none; }

.sum-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px; }
.sum-card { background: white; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; }
.sum-head { background: #1a3a6c; color: white; font-size: 10px; font-weight: 700; padding: 6px 12px; }
.sum-tbl { width: 100%; border-collapse: collapse; }
.sum-tbl td { padding: 5px 12px; border-bottom: 1px solid var(--border); }
.sum-tbl td:last-child { text-align: right; font-family: var(--font-mono); font-weight: 600; }
.sum-tbl .tot td { background: #1a3a6c; color: white; }

#right-panel { grid-column: 2; background: white; border-left: 1px solid var(--border); overflow-y: auto; padding: 14px 12px; display: flex; flex-direction: column; gap: 12px; }
.kpi-card { border: 1px solid var(--border); border-radius: 8px; padding: 10px 12px; background: var(--off-white); }
.kpi-label { font-size: 10px; color: #546e7a; font-weight: 500; }
.kpi-value { font-family: var(--font-mono); font-size: 14px; font-weight: 700; margin-top: 4px; }
.kpi-bar-wrap { height: 4px; background: #eef1f6; border-radius: 2px; margin-top: 6px; overflow: hidden; }
.kpi-bar { height: 100%; border-radius: 2px; transition: width .6s; }
.kpi-bar.green { background: #00897b; }
.kpi-bar.red { background: #c62828; }
.ratio-ring { width: 44px; height: 44px; }
.rp-notes { background: #e3f2fd; border: 1px solid #bbdefb; border-radius: 6px; padding: 8px 10px; font-size: 10px; }

#statusbar { grid-column: 1/3; background: var(--navy); color: #6a8ea8; display: flex; align-items: center; padding: 0 14px; height: 28px; font-size: 10px; gap: 16px; }
.s-dot { width: 6px; height: 6px; border-radius: 50%; background: #00897b; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
#statusTime { margin-left: auto; font-weight: 600; color: #5db8ff; }

@media print {
  #topbar, #right-panel, #statusbar { display: none; }
  #app { display: block; }
  .paper { border: 1px solid #000; box-shadow: none; }
  .wm { opacity: 0.1; }
}
</style>
@endsection

@section('content')
<div id="app">
    <div id="topbar">
        <div class="tb-bc">Financials › <span>Balance Sheet</span></div>
        <div class="tb-srch"><i class="bi bi-search"></i><input type="text" placeholder="Search ledger..."></div>
        <div class="tb-ac">
            <button class="btn-erp" onclick="saveDraft()"><i class="bi bi-cloud-arrow-up"></i>Save Draft</button>
            <button class="btn-erp" onclick="window.print()"><i class="bi bi-printer"></i>Print</button>
            <button class="btn-erp" onclick="exportPDF()"><i class="bi bi-file-pdf"></i>PDF</button>
            <button class="btn-erp btn-gn" id="finalizeBtn" onclick="finalize()"><i class="bi bi-check2-circle"></i>Finalize</button>
        </div>
    </div>

    <main id="main">
        <div class="paper">
            <div class="wm" id="wmTxt">DRAFT</div>
            <div class="hr-r">IN THE BOOKS OF</div>
            <div class="hr-r lt"><input class="shi" id="companyName" value="M/S SHARMA TRADING COMPANY"></div>
            <div class="hr-r" style="background:#2a5aa0;">TRADE CODE | <input class="shi" id="tradeCode" value="STC / 2024-25" style="display:inline;width:120px;color:white;"></div>
            <div class="hr-r lt"><input class="shi" id="tradeCode2" value="0   TRADE CODE   0 0"></div>
            <div class="hr-r tt">BALANCE SHEET</div>
            <div class="hr-r pd">FOR THE FINANCIAL YEAR ENDED <input class="shi" id="fyEnd" value="31-MAR-2025" style="display:inline;width:90px;"> | AY <input class="shi" id="ayVal" value="2025-26" style="display:inline;width:60px;"></div>

            <table class="bs-tbl">
                <colgroup>
                    <col style="width: 35%"><col style="width: 8%"><col style="width: 25%"><col style="width: 25%">
                    <col style="width: 3px">
                    <col style="width: 35%"><col style="width: 8%"><col style="width: 25%"><col style="width: 25%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="col-h" colspan="4">LIABILITIES</th>
                        <th style="background:#2a5aa0;"></th>
                        <th class="col-h" colspan="4">ASSETS</th>
                    </tr>
                    <tr>
                        <th class="col-h">PARTICULARS</th>
                        <th class="col-h2">No.</th>
                        <th class="col-h2">Inner Column (₹)</th>
                        <th class="col-h2">Outer Column (₹)</th>
                        <th></th>
                        <th class="col-h">PARTICULARS</th>
                        <th class="col-h2">No.</th>
                        <th class="col-h2">Inner Column (₹)</th>
                        <th class="col-h2">Outer Column (₹)</th>
                    </tr>
                </thead>

                <?php $prevSection = null; $rowCount = max(count($liabilitiesRows), count($assetsRows)); ?>
                @for($i = 0; $i < $rowCount; $i++)
                    @php
                        $liab = $liabilitiesRows[$i] ?? null;
                        $asset = $assetsRows[$i] ?? null;
                        $liabType = $liab['type'] ?? 'ghost';
                        $assetType = $asset['type'] ?? 'ghost';
                    @endphp
                    @if($liabType === 'section')
                        @if($prevSection !== null) </tbody> @endif
                        <tbody class="sec-body" id="{{ $liab['sectionId'] }}">
                        <tr class="sh-r">
                            <td colspan="4"><span class="sh-lnk">{{ $liab['label'] }}</span><span class="sh-toggle" onclick="toggleSection('{{ $liab['sectionId'] }}')"><i class="bi bi-dash-square" id="{{ $liab['sectionId'] }}-ico"></i></span></td>
                            <td class="dc"></td>
                            @if($assetType === 'section')
                                <td colspan="4"><span class="sh-lnk">{{ $asset['label'] }}</span><span class="sh-toggle" onclick="toggleSection('{{ $asset['sectionId'] }}')"><i class="bi bi-dash-square" id="{{ $asset['sectionId'] }}-ico"></i></span></td>
                            @else
                                <td colspan="4"></td>
                            @endif
                        </tr>
                        @php $prevSection = $liab['sectionId']; @endphp
                    @elseif($liabType === 'row')
                        <tr class="dr">
                            <td><div class="td-pt" style="padding-left: {{ ($liab['depth'] ?? 0) * 12 + 8 }}px;"><span class="bul">♦</span>{{ $liab['label'] }}</div></td>
                            <td class="td-note">{{ $liab['note'] }}</td>
                            <td class="td-inner">
                                @if(isset($liab['isReadOnly']) && $liab['isReadOnly'])
                                    <span id="{{ $liab['innerId'] }}_display">{{ formatAmount($liab['value']) }}</span>
                                    <input type="hidden" id="{{ $liab['innerId'] }}" value="{{ $liab['value'] }}">
                                @else
                                    <input type="text" id="{{ $liab['innerId'] }}" value="{{ formatAmount($liab['value']) }}" oninput="numericInputHandler(this)" onblur="formatInputBlur(this)" data-last-value="{{ $liab['value'] }}">
                                @endif
                            </td>
                            <td class="td-outer">@if(!empty($liab['totalId']))<span id="{{ $liab['totalId'] }}_outer"></span>@endif</td>
                            <td class="dc"></td>
                            @if($assetType === 'row')
                                <td><div class="td-pt" style="padding-left: {{ ($asset['depth'] ?? 0) * 12 + 8 }}px;"><span class="bul">♦</span>{{ $asset['label'] }}</div></td>
                                <td class="td-note">{{ $asset['note'] }}</td>
                                <td class="td-inner">
                                    @if(isset($asset['isReadOnly']) && $asset['isReadOnly'])
                                        <span id="{{ $asset['innerId'] }}_display">{{ formatAmount($asset['value']) }}</span>
                                        <input type="hidden" id="{{ $asset['innerId'] }}" value="{{ $asset['value'] }}">
                                    @else
                                        <input type="text" id="{{ $asset['innerId'] }}" value="{{ formatAmount($asset['value']) }}" oninput="numericInputHandler(this)" onblur="formatInputBlur(this)" data-last-value="{{ $asset['value'] }}">
                                    @endif
                                </td>
                                <td class="td-outer">@if(!empty($asset['totalId']))<span id="{{ $asset['totalId'] }}_outer"></span>@endif</td>
                            @elseif($assetType === 'subtotal')
                                <td colspan="2"><strong>{{ $asset['label'] }}</strong></td>
                                <td class="td-inner" id="{{ $asset['totalId'] }}_inner">-</td>
                                <td class="td-outer" id="{{ $asset['totalId'] }}_outer">-</td>
                            @elseif($assetType === 'grandtotal')
                                <td colspan="2"><strong>{{ $asset['label'] }}</strong></td>
                                <td class="td-inner" id="{{ $asset['totalId'] }}_inner">-</td>
                                <td class="td-outer" id="{{ $asset['totalId'] }}_outer">-</td>
                            @else
                                <td colspan="4"></td>
                            @endif
                        </tr>
                    @elseif($liabType === 'subtotal')
                        <tr class="st-r">
                            <td colspan="2"><strong>{{ $liab['label'] }}</strong></td>
                            <td class="td-inner" id="{{ $liab['totalId'] }}_inner">-</td>
                            <td class="td-outer" id="{{ $liab['totalId'] }}_outer">-</td>
                            <td class="dc"></td>
                            @if($assetType === 'subtotal')
                                <td colspan="2"><strong>{{ $asset['label'] }}</strong></td>
                                <td class="td-inner" id="{{ $asset['totalId'] }}_inner">-</td>
                                <td class="td-outer" id="{{ $asset['totalId'] }}_outer">-</td>
                            @elseif($assetType === 'grandtotal')
                                <td colspan="2"><strong>{{ $asset['label'] }}</strong></td>
                                <td class="td-inner" id="{{ $asset['totalId'] }}_inner">-</td>
                                <td class="td-outer" id="{{ $asset['totalId'] }}_outer">-</td>
                            @else
                                <td colspan="4"></td>
                            @endif
                        </tr>
                    @elseif($liabType === 'grandtotal')
                        <tr class="gt-r">
                            <td colspan="2"><strong>{{ $liab['label'] }}</strong></td>
                            <td class="td-inner" id="{{ $liab['totalId'] }}_inner">-</td>
                            <td class="td-outer" id="{{ $liab['totalId'] }}_outer">-</td>
                            <td class="dc"></td>
                            @if($assetType === 'grandtotal')
                                <td colspan="2"><strong>{{ $asset['label'] }}</strong></td>
                                <td class="td-inner" id="{{ $asset['totalId'] }}_inner">-</td>
                                <td class="td-outer" id="{{ $asset['totalId'] }}_outer">-</td>
                            @else
                                <td colspan="4"></td>
                            @endif
                        </tr>
                    @endif
                @endfor
                @if($prevSection !== null) </tbody> @endif
            </table>
        </div>

        <div class="sum-wrap">
            <div class="sum-card"><div class="sum-head">Capital & Liabilities Summary (Outer)</div>
                <table class="sum-tbl">
                    <tr><td>Total Capital & Reserves</td><td id="sum-cap_outer">-</td></tr>
                    <tr><td>Total Secured Loans</td><td id="sum-sl_outer">-</td></tr>
                    <tr><td>Total Unsecured Loans</td><td id="sum-ul_outer">-</td></tr>
                    <tr><td>Total Current Liabilities</td><td id="sum-cl_outer">-</td></tr>
                    <tr><td>Total Provisions & Deferred Tax</td><td id="sum-provdtl_outer">-</td></tr>
                    <tr class="tot"><td>Total Liabilities</td><td id="sum-liab_outer">-</td></tr>
                </table>
            </div>
            <div class="sum-card"><div class="sum-head">Assets Summary (Outer)</div>
                <table class="sum-tbl">
                    <tr><td>Total PPE (Net)</td><td id="sum-ppe_outer">-</td></tr>
                    <tr><td>Total Intangible Assets</td><td id="sum-int_outer">-</td></tr>
                    <tr><td>Total Investments</td><td id="sum-inv_outer">-</td></tr>
                    <tr><td>Total Current Assets</td><td id="sum-ca_outer">-</td></tr>
                    <tr class="tot"><td>Total Assets</td><td id="sum-asset_outer">-</td></tr>
                </table>
            </div>
        </div>
    </main>

    <aside id="right-panel">
        <div class="kpi-card"><div class="kpi-label">Total Assets (Outer)</div><div class="kpi-value" id="rp-asset">₹ —</div><div class="kpi-bar-wrap"><div class="kpi-bar green" id="rp-assetBar"></div></div></div>
        <div class="kpi-card"><div class="kpi-label">Total Liabilities (Outer)</div><div class="kpi-value" id="rp-liab">₹ —</div><div class="kpi-bar-wrap"><div class="kpi-bar red" id="rp-liabBar"></div></div></div>
        <div class="kpi-card"><div class="kpi-label">Net Worth</div><div class="kpi-value" id="rp-nw">₹ —</div></div>
        <div class="kpi-card"><div class="kpi-label">Current Ratio</div><svg class="ratio-ring" viewBox="0 0 44 44"><circle cx="22" cy="22" r="18" fill="none" stroke="#eef1f6" stroke-width="5"/><circle cx="22" cy="22" r="18" fill="none" stroke="#00897b" stroke-width="5" stroke-dasharray="113.1" stroke-dashoffset="60" stroke-linecap="round" transform="rotate(-90 22 22)" id="crRing"/></svg><div class="kpi-value" id="rp-cr">—</div></div>
        <div class="kpi-card"><div class="kpi-label">Working Capital</div><div class="kpi-value" id="rp-wc">₹ —</div></div>
        <div id="balanceAlert" class="rp-notes" style="background:#fff3e0; border-color:#ffb74d;"></div>
        <div class="rp-notes"><strong>Auditor Remarks</strong><br>Provisional Balance Sheet. Closing stock subject to physical verification.</div>
    </aside>

    <div id="statusbar">
        <span><iss="bi bi-ca clalendar3"></i> FY 2024–25</span>
        <span><i class="bi bi-table"></i> Balance Sheet</span>
        <span><span class="s-dot"></span> Auto-saved</span>
        <span id="statusTime">--:--:--</span>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Helper: parse amount from string (handles Cr, Dr, brackets, commas)
    function parseAmountFromInput(valueStr) {
        let s = valueStr.trim().toUpperCase();
        let multiplier = 1;
        if (s.includes('CR')) multiplier = 1;
        else if (s.includes('DR')) multiplier = -1;
        s = s.replace(/[₹,\s]/g, '');
        let neg = false;
        if (s.startsWith('(') && s.endsWith(')')) { neg = true; s = s.slice(1, -1); }
        else if (s.startsWith('-')) { neg = true; s = s.slice(1); }
        let num = parseFloat(s);
        if (isNaN(num)) return 0;
        let final = (neg ? -num : num) * multiplier;
        return final;
    }

    function formatAmount(n) {
        if (n === 0) return '0';
        let neg = n < 0;
        let val = Math.abs(n);
        let s = val.toFixed(0);
        let last = s.slice(-3);
        let rest = s.slice(0, s.length-3);
        if (rest) last = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + last;
        return neg ? '(' + last + ')' : last;
    }

    function numericInputHandler(input) {
        let raw = input.value;
        let num = parseAmountFromInput(raw);
        input.dataset.lastValue = num;
        input.value = formatAmount(num);
        calcAll();
    }

    function formatInputBlur(input) {
        let num = parseAmountFromInput(input.value);
        input.value = formatAmount(num);
        input.dataset.lastValue = num;
        calcAll();
    }

    function getNumericValue(innerId) {
        let input = document.getElementById(innerId);
        if (input && input.tagName === 'INPUT') {
            return parseAmountFromInput(input.value);
        }
        let span = document.getElementById(innerId + '_display');
        if (span) {
            let txt = span.innerText;
            return parseAmountFromInput(txt);
        }
        let hidden = document.getElementById(innerId);
        if (hidden && hidden.tagName === 'INPUT' && hidden.type === 'hidden') {
            return parseFloat(hidden.value) || 0;
        }
        return 0;
    }

    function setAmount(totalId, value, isOuter = false) {
        let el = document.getElementById(isOuter ? totalId + '_outer' : totalId + '_inner');
        if (el) el.innerText = formatAmount(value);
    }

    function setCurrency(id, value) {
        let el = document.getElementById(id);
        if (el) el.innerText = '₹ ' + formatAmount(value);
    }

    // ---------- Main calculation ----------
    function calcAll() {
        // ---- Liabilities ----
        let capOpen = getNumericValue('cap_open');
        let capProfit = getNumericValue('cap_profit');
        let capDraw = getNumericValue('cap_draw');
        let capClose = capOpen + capProfit + capDraw;
        setAmount('cap_close', capClose);
        let reserves = getNumericValue('cap_reserve') + getNumericValue('cap_capres') + getNumericValue('cap_secprem') + getNumericValue('cap_reval');
        let totalCapital = capClose + reserves;
        setAmount('totalCapital', totalCapital);
        setAmount('totalCapital', totalCapital, true);

        let totalSecured = getNumericValue('sec_term') + getNumericValue('sec_cc') + getNumericValue('sec_od') + getNumericValue('sec_vehicle');
        setAmount('totalSecured', totalSecured);
        setAmount('totalSecured', totalSecured, true);

        let totalUnsecured = getNumericValue('unsec_dir') + getNumericValue('unsec_other');
        setAmount('totalUnsecured', totalUnsecured);
        setAmount('totalUnsecured', totalUnsecured, true);

        let currLiab = getNumericValue('cl_sundry') + getNumericValue('cl_bills') + getNumericValue('cl_gst') + getNumericValue('cl_tds') +
                       getNumericValue('cl_pfesi') + getNumericValue('cl_ptax') + getNumericValue('cl_salary') + getNumericValue('cl_rent') +
                       getNumericValue('cl_audit') + getNumericValue('cl_interest') + getNumericValue('cl_advcust') + getNumericValue('cl_other');
        setAmount('totalCurrentLiab', currLiab);
        setAmount('totalCurrentLiab', currLiab, true);

        let provisions = getNumericValue('prov_tax') + getNumericValue('prov_doubt') + getNumericValue('prov_warr');
        setAmount('totalProvisions', provisions);
        setAmount('totalProvisions', provisions, true);

        let dtl = getNumericValue('dtl');
        setAmount('totalDTL', dtl);
        setAmount('totalDTL', dtl, true);

        let suspense = getNumericValue('suspense');
        let totalLiabExSuspense = totalCapital + totalSecured + totalUnsecured + currLiab + provisions + dtl;

        // ---- Assets ----
        let fa = getNumericValue('fa_land') + getNumericValue('fa_build') + getNumericValue('fa_plant') + getNumericValue('fa_furn') +
                 getNumericValue('fa_veh') + getNumericValue('fa_comp') + getNumericValue('fa_office');
        let dep = getNumericValue('dep_build') + getNumericValue('dep_plant') + getNumericValue('dep_furn') + getNumericValue('dep_veh') +
                  getNumericValue('dep_comp') + getNumericValue('dep_office');
        let totalPPE = fa - Math.abs(dep);
        setAmount('totalPPE', totalPPE);
        setAmount('totalPPE', totalPPE, true);

        let intang = getNumericValue('int_gw') + getNumericValue('int_pt') + getNumericValue('int_soft');
        setAmount('totalIntang', intang);
        setAmount('totalIntang', intang, true);

        let investments = getNumericValue('inv_lt') + getNumericValue('inv_oth');
        setAmount('totalInv', investments);
        setAmount('totalInv', investments, true);

        let inventory = getNumericValue('inv_raw') + getNumericValue('inv_wip') + getNumericValue('inv_fg') + getNumericValue('inv_transit') + getNumericValue('inv_pack');
        setAmount('totalInventory', inventory);
        setAmount('totalInventory', inventory, true);

        let provDoubtMaster = getNumericValue('prov_doubt'); // master provision
        // Update the contra asset field (read-only)
        let contraInput = document.getElementById('ca_provdebt');
        if (contraInput && contraInput.tagName === 'INPUT') {
            contraInput.value = formatAmount(-Math.abs(provDoubtMaster));
            contraInput.dataset.lastValue = -Math.abs(provDoubtMaster);
        }
        let receivables = getNumericValue('ca_debt') + getNumericValue('ca_bills') + getNumericValue('ca_provdebt');
        setAmount('totalReceivables', receivables);
        setAmount('totalReceivables', receivables, true);

        let cash = getNumericValue('cash_hand') + getNumericValue('cash_sbi') + getNumericValue('cash_hdfc') + getNumericValue('cash_icici') +
                   getNumericValue('cash_axis') + getNumericValue('cash_fd');
        setAmount('totalCash', cash);
        setAmount('totalCash', cash, true);

        let advances = getNumericValue('adv_supp') + getNumericValue('adv_staff') + getNumericValue('adv_loanemp') +
                       getNumericValue('adv_pre') + getNumericValue('adv_accrued');
        setAmount('totalAdvances', advances);
        setAmount('totalAdvances', advances, true);

        let taxAssets = getNumericValue('tax_gst') + getNumericValue('tax_tds') + getNumericValue('tax_adv') + getNumericValue('tax_itc');
        setAmount('totalTaxAssets', taxAssets);
        setAmount('totalTaxAssets', taxAssets, true);

        let totalAssets = totalPPE + intang + investments + inventory + receivables + cash + advances + taxAssets;
        setAmount('totalAssets', totalAssets);
        setAmount('totalAssets', totalAssets, true);

        // ---- Suspense (difference) ----
        let difference = totalAssets - totalLiabExSuspense;
        let suspenseValue = difference;
        let suspenseInput = document.getElementById('suspense');
        if (suspenseInput && suspenseInput.tagName === 'INPUT') {
            suspenseInput.value = formatAmount(suspenseValue);
            suspenseInput.dataset.lastValue = suspenseValue;
        }
        setAmount('totalSuspense', suspenseValue);
        setAmount('totalSuspense', suspenseValue, true);

        let totalLiab = totalLiabExSuspense + suspenseValue;
        setAmount('totalLiabilities', totalLiab);
        setAmount('totalLiabilities', totalLiab, true);

        // ---- Summary cards & right panel ----
        setCurrency('sum-cap_outer', totalCapital);
        setCurrency('sum-sl_outer', totalSecured);
        setCurrency('sum-ul_outer', totalUnsecured);
        setCurrency('sum-cl_outer', currLiab);
        setCurrency('sum-provdtl_outer', provisions + dtl);
        setCurrency('sum-liab_outer', totalLiab);

        setCurrency('sum-ppe_outer', totalPPE);
        setCurrency('sum-int_outer', intang);
        setCurrency('sum-inv_outer', investments);
        setCurrency('sum-ca_outer', inventory + receivables + cash + advances + taxAssets);
        setCurrency('sum-asset_outer', totalAssets);

        setCurrency('rp-asset', totalAssets);
        setCurrency('rp-liab', totalLiab);
        setCurrency('rp-nw', totalCapital);
        let wc = (inventory + receivables + cash + advances + taxAssets) - currLiab;
        document.getElementById('rp-wc').innerText = '₹ ' + formatAmount(wc);
        let cr = currLiab > 0 ? (inventory + receivables + cash + advances + taxAssets) / currLiab : 0;
        document.getElementById('rp-cr').innerText = cr.toFixed(2);
        let ring = document.getElementById('crRing');
        if (ring) ring.setAttribute('stroke-dashoffset', 113.1 - Math.min(cr/3,1)*113.1);
        let maxVal = Math.max(totalAssets, totalLiab, 1);
        document.getElementById('rp-assetBar').style.width = Math.min((totalAssets/maxVal)*90,90)+'%';
        document.getElementById('rp-liabBar').style.width = Math.min((totalLiab/maxVal)*90,90)+'%';

        // ---- Balance validation (true imbalance without suspense) ----
        let trueBalanced = Math.abs(totalAssets - totalLiabExSuspense) < 1;
        let alertDiv = document.getElementById('balanceAlert');
        if (trueBalanced) {
            alertDiv.innerHTML = '<strong>✓ Balanced</strong> (Assets = Liabilities)';
            alertDiv.style.background = '#e8f5e9';
            alertDiv.style.borderColor = '#66bb6a';
        } else {
            alertDiv.innerHTML = `<strong>❌ Imbalance:</strong> Difference = ₹ ${formatAmount(Math.abs(difference))}. Suspense account added automatically.`;
            alertDiv.style.background = '#ffebee';
            alertDiv.style.borderColor = '#ef9a9a';
        }
        document.getElementById('finalizeBtn').disabled = !trueBalanced;
    }

    // ---- Collapsible sections ----
    function toggleSection(id) {
        let body = document.getElementById(id);
        let ico = document.getElementById(id+'-ico');
        if (body) {
            body.classList.toggle('collapsed');
            if (ico) ico.className = body.classList.contains('collapsed') ? 'bi bi-plus-square' : 'bi bi-dash-square';
        }
    }

    // ---- Debounced auto-save (3 seconds after last change) ----
    let saveTimeout;
    function saveDraft() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            showToast('Draft saved', true);
            // Here you would send data to backend
        }, 3000);
    }

    function finalize() {
        let trueBalanced = document.getElementById('finalizeBtn').disabled === false;
        if (!trueBalanced) {
            showToast('Cannot finalize: Balance Sheet is imbalanced.', false);
            return;
        }
        if (confirm('Finalize Balance Sheet? This action cannot be undone.')) {
            document.getElementById('wmTxt').style.display = 'none';
            showToast('Balance Sheet Finalized', true);
        }
    }

    function exportPDF() { alert('PDF Export (implement with print CSS or library)'); }
    function showToast(msg, success) {
        let toast = document.createElement('div');
        toast.style.cssText = `position:fixed; bottom:40px; right:20px; background:${success ? '#00897b' : '#c62828'}; color:white; padding:8px 16px; border-radius:40px; z-index:9999; font-size:12px;`;
        toast.innerHTML = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }

    // ---- Live clock ----
    function updateClock() {
        let now = new Date();
        document.getElementById('statusTime').innerText = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }

    // ---- Attach event handlers ----
    document.querySelectorAll('.td-inner input').forEach(input => {
        input.addEventListener('input', function() { numericInputHandler(this); saveDraft(); });
        input.addEventListener('blur', function() { formatInputBlur(this); saveDraft(); });
    });
    setInterval(updateClock, 1000);
    updateClock();
    calcAll();
</script>
@endsection