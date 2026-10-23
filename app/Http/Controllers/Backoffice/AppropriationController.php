<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\AppropriationAccount;
use App\Models\AppropriationItem;
use App\Models\Company;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppropriationController extends Controller
{
    /**
     * Display the appropriation account form.
     */
    public function index(Request $request)
{
    // Get or create default company (ID 1)
    $company = Company::firstOrCreate(
        ['id' => 1],
        ['name' => 'M/S SHARMA TRADING COMPANY']
    );
    $companyId = $request->get('company_id', $company->id);
    
    $financialYear = $this->getCurrentFinancialYear();
    $financialYearId = $request->get('financial_year_id', $financialYear->id);
    
    // Try to find existing appropriation account
    $appropriation = AppropriationAccount::where('company_id', $companyId)
        ->where('financial_year_id', $financialYearId)
        ->first();
    
    // If not exists, create a new draft with default rows
    if (!$appropriation) {
        $appropriation = $this->createNewDraft($companyId, $financialYearId);
    }
    
    // Load items grouped
    $debitItems = $appropriation->debitItems()->get()->groupBy('section');
    $creditItems = $appropriation->creditItems()->get()->groupBy('section');
    
    // Fetch net profit from P&L module (example placeholder)
    $netProfit = $appropriation->net_profit ?? 0;
    
    // Refresh company and financial year objects (they exist now)
    $company = Company::find($companyId);
    $financialYear = FinancialYear::find($financialYearId);
    
    return view('Backoffice.computation.appropriation', compact(
        'appropriation', 'debitItems', 'creditItems', 'netProfit', 'company', 'financialYear'
    ));
}
    /**
     * Save draft (full form submission)
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'appropriation_id' => 'required|exists:appropriation_accounts,id',
            'company_name' => 'nullable|string|max:255',
            'trade_code' => 'nullable|string|max:100',
            'financial_year_end' => 'nullable|string|max:20',
            'assessment_year' => 'nullable|string|max:20',
            'net_profit' => 'nullable|numeric',
            'prepared_by' => 'nullable|string|max:100',
            'place' => 'nullable|string|max:100',
            'itr_ack_no' => 'nullable|string|max:100',
            'audit_status' => 'nullable|string|max:50',
            'udin' => 'nullable|string|max:50',
            'rdis' => 'nullable|string|max:20',
            'audit_ack_no' => 'nullable|string|max:100',
            'audit_date' => 'nullable|date',
            'items' => 'array',
            'items.*.id' => 'nullable|exists:appropriation_items,id',
            'items.*.side' => 'required|in:debit,credit',
            'items.*.section' => 'required|string',
            'items.*.particulars' => 'required|string',
            'items.*.partner_detail' => 'nullable|string',
            'items.*.amount' => 'nullable|numeric',
            'items.*.is_deletable' => 'boolean',
        ]);
        
        DB::beginTransaction();
        try {
            $appropriation = AppropriationAccount::findOrFail($validated['appropriation_id']);
            
            // Update header fields
            $appropriation->update([
                'company_name' => $validated['company_name'] ?? $appropriation->company_name,
                'trade_code' => $validated['trade_code'] ?? $appropriation->trade_code,
                'financial_year_end' => $validated['financial_year_end'] ?? $appropriation->financial_year_end,
                'assessment_year' => $validated['assessment_year'] ?? $appropriation->assessment_year,
                'net_profit' => $validated['net_profit'] ?? $appropriation->net_profit,
                'prepared_by' => $validated['prepared_by'] ?? $appropriation->prepared_by,
                'place' => $validated['place'] ?? $appropriation->place,
                'itr_ack_no' => $validated['itr_ack_no'] ?? $appropriation->itr_ack_no,
                'audit_status' => $validated['audit_status'] ?? $appropriation->audit_status,
                'udin' => $validated['udin'] ?? $appropriation->udin,
                'rdis' => $validated['rdis'] ?? $appropriation->rdis,
                'audit_ack_no' => $validated['audit_ack_no'] ?? $appropriation->audit_ack_no,
                'audit_date' => $validated['audit_date'] ?? $appropriation->audit_date,
            ]);
            
            // Sync items: delete removed ones, update existing, create new
            $existingItemIds = $appropriation->items()->pluck('id')->toArray();
            $submittedIds = collect($validated['items'])->pluck('id')->filter()->toArray();
            $toDelete = array_diff($existingItemIds, $submittedIds);
            
            if (!empty($toDelete)) {
                AppropriationItem::whereIn('id', $toDelete)->delete();
            }
            
            foreach ($validated['items'] as $index => $itemData) {
                $itemData['sort_order'] = $index;
                if (isset($itemData['id']) && $itemData['id']) {
                    $item = AppropriationItem::find($itemData['id']);
                    $item->update($itemData);
                } else {
                    $itemData['appropriation_account_id'] = $appropriation->id;
                    AppropriationItem::create($itemData);
                }
            }
            
            // Recalculate totals
            $appropriation->recalculateTotals();
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Draft saved successfully', 'data' => $appropriation]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Finalize the appropriation account
     */
    public function finalize(Request $request)
    {
        $request->validate([
            'appropriation_id' => 'required|exists:appropriation_accounts,id',
        ]);
        
        $appropriation = AppropriationAccount::findOrFail($request->appropriation_id);
        
        if ($appropriation->status === 'finalized') {
            return response()->json(['success' => false, 'message' => 'Already finalized']);
        }
        
        $appropriation->status = 'finalized';
        $appropriation->save();
        
        return response()->json(['success' => true, 'message' => 'Appropriation Account Finalized']);
    }
    
    /**
     * Export PDF (placeholder)
     */
    public function exportPdf(Request $request)
    {
        // TODO: Generate PDF using dompdf or similar
        return redirect()->back()->with('info', 'PDF export coming soon.');
    }
    
    /**
     * Helper: Create a new draft with default rows
     */
    private function createNewDraft($companyId, $financialYearId)
    {
        $appropriation = AppropriationAccount::create([
            'company_id' => $companyId,
            'financial_year_id' => $financialYearId,
            'status' => 'draft',
            'net_profit' => 0,
            'total_debit' => 0,
            'total_credit' => 0,
            'closing_capital' => 0,
        ]);
        
        // Seed default debit items (from your existing HTML structure)
        $defaultDebits = [
            ['section' => 'interest_on_capital', 'particulars' => 'Interest on Capital', 'partner_detail' => 'Sharma (Proprietor)', 'amount' => 45000, 'is_deletable' => false],
            ['section' => 'bonus', 'particulars' => 'Bonus', 'partner_detail' => 'Sharma (Proprietor)', 'amount' => 30000, 'is_deletable' => false],
            ['section' => 'salary', 'particulars' => 'Salary', 'partner_detail' => 'Sharma (Proprietor)', 'amount' => 180000, 'is_deletable' => false],
            ['section' => 'general_reserve', 'particulars' => 'General Reserve', 'partner_detail' => 'General Reserve Fund', 'amount' => 50000, 'is_deletable' => false],
            ['section' => 'commission', 'particulars' => 'Commission', 'partner_detail' => 'Sharma (Proprietor)', 'amount' => 24000, 'is_deletable' => false],
            ['section' => 'drawings', 'particulars' => 'Drawings', 'partner_detail' => 'Sharma (Proprietor)', 'amount' => 120000, 'is_deletable' => false],
            ['section' => 'share_of_profit', 'particulars' => 'Share of Profit', 'partner_detail' => 'Sharma (Proprietor)', 'amount' => 575800, 'is_deletable' => false],
        ];
        
        foreach ($defaultDebits as $d) {
            $d['side'] = 'debit';
            $d['appropriation_account_id'] = $appropriation->id;
            AppropriationItem::create($d);
        }
        
        // Default credit items (system linked net profit not added yet)
        $defaultCredits = [
            ['section' => 'partners_current', 'particulars' => "Partner's Current Account", 'partner_detail' => null, 'amount' => 85000, 'is_deletable' => false],
            ['section' => 'interest_on_drawings', 'particulars' => 'Interest on Drawings', 'partner_detail' => null, 'amount' => 12000, 'is_deletable' => false],
            ['section' => 'other_income', 'particulars' => 'Other Income', 'partner_detail' => null, 'amount' => 28000, 'is_deletable' => false],
        ];
        
        foreach ($defaultCredits as $c) {
            $c['side'] = 'credit';
            $c['appropriation_account_id'] = $appropriation->id;
            AppropriationItem::create($c);
        }
        
        // Net profit row is system linked; we'll add it as a credit item with is_system_linked true
        AppropriationItem::create([
            'appropriation_account_id' => $appropriation->id,
            'side' => 'credit',
            'section' => 'net_profit_brought',
            'particulars' => 'Net Profit (P&L Account)',
            'partner_detail' => null,
            'amount' => 0,
            'is_system_linked' => true,
            'linked_source' => 'profit_loss_module',
            'is_deletable' => false,
        ]);
        
        return $appropriation;
    }
    
  private function getCurrentFinancialYear()
{
    return \App\Models\FinancialYear::firstOrCreate(
        ['is_active' => true],
        [
            'year_code'   => '2024-25',
            'start_date'  => '2024-04-01',
            'end_date'    => '2025-03-31',
            'is_active'   => true,
        ]
    );
}
}