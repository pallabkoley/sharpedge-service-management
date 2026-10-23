<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Computation;
use App\Models\BalanceSheet;
use App\Models\BalanceSheetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BalanceSheetController extends Controller
{
    public function index(Computation $computation)
    {
        $balanceSheet = $computation->balanceSheet;

        if (!$balanceSheet) {
            DB::transaction(function () use ($computation, &$balanceSheet) {
                $balanceSheet = BalanceSheet::create([
                    'computation_id' => $computation->id,
                    'status'         => 'draft',
                    'prepared_by'    => Auth::id(),
                ]);
                $this->seedDefaultItems($balanceSheet);
            });
        }

        $liabilities = $balanceSheet->liabilityItems->groupBy('section');
        $assets      = $balanceSheet->assetItems->groupBy('section');

        return view('Backoffice.balance-sheet.index', compact('balanceSheet', 'liabilities', 'assets', 'computation'));
    }

    public function updateItem(Request $request, BalanceSheetItem $item)
    {
        if ($item->balanceSheet->status === 'finalized') {
            return response()->json(['error' => 'Balance Sheet is finalized.'], 403);
        }

        $item->update([
            'amount' => str_replace(['₹', ',', ' ', '(-)'], '', $request->amount) ?? 0,
            'updated_by' => Auth::id()
        ]);

        return response()->json(['success' => true, 'amount' => $item->amount]);
    }

    public function finalize(BalanceSheet $balanceSheet)
    {
        $balanceSheet->update(['status' => 'finalized']);
        return response()->json(['success' => true]);
    }

    private function seedDefaultItems(BalanceSheet $sheet)
    {
        $items = [
            // Capital (Liabilities) – with code mapping for JS IDs
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'cap2', 'label' => 'Capital as Per Last A/c', 'amount' => 1000000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'cap4', 'label' => 'Add: Net Profit', 'amount' => 924800, 'is_editable' => false],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'cap5', 'label' => 'Less: Drawings', 'amount' => -120000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'cap1', 'label' => 'Reserve Fund', 'amount' => 300000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'cap3', 'label' => 'Capital Realized', 'amount' => 200000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'gf1', 'label' => 'Fund for Income Tax', 'amount' => 45000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'gf2', 'label' => 'Fund for Defer Tax', 'amount' => 18000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'gf3', 'label' => 'Fund for Doubtful Debt', 'amount' => 22000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'gf4', 'label' => 'Fund for Bad Debts', 'amount' => 12000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'gf5', 'label' => 'Fund for Contingent Liab.', 'amount' => 30000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'gf6', 'label' => 'Fund for Unassets Liab.', 'amount' => 15000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'gf7', 'label' => 'Fund for Loss on Subsidiary', 'amount' => 8000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Capital Account', 'code' => 'gf11', 'label' => 'Accidental Loss Fund', 'amount' => 20000, 'is_editable' => true],

            // Secured Loans
            ['side' => 'liability', 'section' => 'Secured Loans', 'code' => 'sl1', 'label' => 'Long Term Loan', 'amount' => 1500000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Secured Loans', 'code' => 'sl2', 'label' => 'Term Loans', 'amount' => 850000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Secured Loans', 'code' => 'sl3', 'label' => 'Other Loans', 'amount' => 200000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Secured Loans', 'code' => 'sl4', 'label' => 'Cash Credit', 'amount' => 425000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Secured Loans', 'code' => 'sl5', 'label' => 'Over Draft', 'amount' => 175000, 'is_editable' => true],

            // Unsecured Loans
            ['side' => 'liability', 'section' => 'Unsecured Loans', 'code' => 'ul1', 'label' => 'Long Term Loans', 'amount' => 500000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Unsecured Loans', 'code' => 'ul2', 'label' => 'Other Loans', 'amount' => 200000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Unsecured Loans', 'code' => 'ul3', 'label' => 'Cash Credit', 'amount' => 80000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Unsecured Loans', 'code' => 'ul4', 'label' => 'Over Draft', 'amount' => 50000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Unsecured Loans', 'code' => 'ul5', 'label' => 'Other Loans', 'amount' => 70000, 'is_editable' => true],

            // Current Liabilities
            ['side' => 'liability', 'section' => 'Current Liabilities', 'code' => 'cl1', 'label' => 'Sundry Creditors', 'amount' => 680000, 'is_editable' => false],

            // Other Liabilities
            ['side' => 'liability', 'section' => 'Other Liabilities', 'code' => 'ol1', 'label' => 'Defer Tax', 'amount' => 36000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Other Liabilities', 'code' => 'ol2', 'label' => 'Outstanding GST Penalties', 'amount' => 18000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Other Liabilities', 'code' => 'oe1', 'label' => 'Outstanding Expenses (Trading)', 'amount' => 42000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Other Liabilities', 'code' => 'oe2', 'label' => 'Outstanding Expenses (P&L)', 'amount' => 28000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Other Liabilities', 'code' => 'at1', 'label' => 'Advances Taken', 'amount' => 150000, 'is_editable' => true],
            ['side' => 'liability', 'section' => 'Other Liabilities', 'code' => 'at2', 'label' => 'Other Advances', 'amount' => 80000, 'is_editable' => true],

            // Intangible Assets
            ['side' => 'asset', 'section' => 'Intangible Assets', 'code' => 'ia1', 'label' => 'Goodwill', 'amount' => 250000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Intangible Assets', 'code' => 'ia2', 'label' => 'Patents', 'amount' => 80000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Intangible Assets', 'code' => 'ia3', 'label' => 'Trademark', 'amount' => 55000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Intangible Assets', 'code' => 'ia4', 'label' => 'Copyright', 'amount' => 30000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Intangible Assets', 'code' => 'ia5', 'label' => 'Intangible Assets in Progress', 'amount' => 120000, 'is_editable' => true],

            // Fixed Assets
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa1_total', 'label' => 'Land', 'amount' => 800000, 'is_editable' => false],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa2_total', 'label' => 'Building', 'amount' => 1200000, 'is_editable' => false],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa2d', 'label' => 'Less: Depreciation', 'amount' => -120000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa3_total', 'label' => 'Plant & Machinery', 'amount' => 900000, 'is_editable' => false],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa3d', 'label' => 'Less: Depreciation', 'amount' => -135000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa4_total', 'label' => 'Furniture & Fixtures', 'amount' => 350000, 'is_editable' => false],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa5_total', 'label' => 'Electrical Fittings', 'amount' => 120000, 'is_editable' => false],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa5d', 'label' => 'Less: Depreciation', 'amount' => -48000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa6_total', 'label' => 'Vehicles', 'amount' => 500000, 'is_editable' => false],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa6d', 'label' => 'Less: Depreciation', 'amount' => -150000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa7_total', 'label' => 'Computer & Accessories', 'amount' => 240000, 'is_editable' => false],
            ['side' => 'asset', 'section' => 'Fixed Assets', 'code' => 'fa8_total', 'label' => 'Tools & Gold', 'amount' => 460000, 'is_editable' => false],

            // Investments
            ['side' => 'asset', 'section' => 'Investments', 'code' => 'inv1', 'label' => 'Long Term Investment', 'amount' => 500000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Investments', 'code' => 'inv2', 'label' => 'Advance for Building', 'amount' => 200000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Investments', 'code' => 'inv3', 'label' => 'Security Deposit', 'amount' => 75000, 'is_editable' => true],

            // Current Assets
            ['side' => 'asset', 'section' => 'Current Assets', 'code' => 'ca1', 'label' => 'Inventories', 'amount' => 1260000, 'is_editable' => false],
            ['side' => 'asset', 'section' => 'Current Assets', 'code' => 'ca2', 'label' => 'Loans & Advances', 'amount' => 420000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Current Assets', 'code' => 'ca3', 'label' => 'Sundry Debtors', 'amount' => 840000, 'is_editable' => false],
            ['side' => 'asset', 'section' => 'Current Assets', 'code' => 'ca3d', 'label' => 'Less: Bad Debt', 'amount' => -28000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Current Assets', 'code' => 'oa1', 'label' => 'Outstanding TDS', 'amount' => 18500, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Current Assets', 'code' => 'oa2', 'label' => 'TCS', 'amount' => 6200, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Current Assets', 'code' => 'oa3', 'label' => 'Advance Tax', 'amount' => 42000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Current Assets', 'code' => 'oa4', 'label' => 'Prepaid Expenses', 'amount' => 23000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Current Assets', 'code' => 'oa6', 'label' => 'GST Balance', 'amount' => 28000, 'is_editable' => true],

            // Cash at Bank
            ['side' => 'asset', 'section' => 'Cash at Bank', 'code' => 'bk1', 'label' => 'Bank Balance', 'amount' => 842000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Cash at Bank', 'code' => 'bk2', 'label' => 'Cash in Hand', 'amount' => 124000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Cash at Bank', 'code' => 'bk3', 'label' => 'Cash Book', 'amount' => 36000, 'is_editable' => true],
            ['side' => 'asset', 'section' => 'Cash at Bank', 'code' => 'bk4', 'label' => 'Imprest Cash', 'amount' => 18000, 'is_editable' => true],
        ];

        foreach ($items as $i => $itemData) {
            $itemData['balance_sheet_id'] = $sheet->id;
            $itemData['created_by'] = Auth::id();
            $itemData['display_order'] = $i + 1;
            BalanceSheetItem::create($itemData);
        }
    }
}