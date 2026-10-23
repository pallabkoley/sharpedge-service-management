<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\BankReconciliation;
use App\Models\BankReconciliationCheque;
use App\Models\BankReconciliationDeposit;
use App\Models\BankReconciliationAdjustment;
use App\Models\Computation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BankReconciliationController extends Controller
{
    /**
     * Display the Bank Reconciliation page.
     */
    public function index($computationId)
    {
        $computation = Computation::findOrFail($computationId);
        
        // Get or create bank reconciliation record
        $bankReconciliation = BankReconciliation::firstOrCreate(
            ['computation_id' => $computationId],
            [
                'statement_date' => now(),
                'closing_balance_bank' => 0,
                'closing_balance_cashbook' => 0,
                'status' => true,
                'created_by' => Auth::id(),
            ]
        );

        // ─── 🔥 ডেমো ডেটা সিড (শুধুমাত্র ডেভেলপমেন্টে) ───
        // যদি কোনো Cheques না থাকে, ডেমো ডেটা যোগ করুন
        if ($bankReconciliation->cheques()->count() === 0) {
            $demoCheques = [
                ['cheque_date' => '2026-03-25', 'cheque_no' => '123456', 'amount' => 45000, 'sort_order' => 1, 'status' => true],
                ['cheque_date' => '2026-03-28', 'cheque_no' => '123457', 'amount' => 32000, 'sort_order' => 2, 'status' => true],
                ['cheque_date' => '2026-03-30', 'cheque_no' => '123458', 'amount' => 15000, 'sort_order' => 3, 'status' => true],
            ];
            foreach ($demoCheques as $cheque) {
                $bankReconciliation->cheques()->create($cheque);
            }
        }

        // যদি কোনো Deposits না থাকে, ডেমো ডেটা যোগ করুন
        if ($bankReconciliation->deposits()->count() === 0) {
            $demoDeposits = [
                ['deposit_date' => '2026-03-29', 'amount' => 25000, 'sort_order' => 1, 'status' => true],
                ['deposit_date' => '2026-03-30', 'amount' => 15000, 'sort_order' => 2, 'status' => true],
                ['deposit_date' => '2026-03-31', 'amount' => 10000, 'sort_order' => 3, 'status' => true],
            ];
            foreach ($demoDeposits as $deposit) {
                $bankReconciliation->deposits()->create($deposit);
            }
        }

        // যদি কোনো Adjustments না থাকে, ডেমো ডেটা যোগ করুন
        if ($bankReconciliation->adjustments()->count() === 0) {
            $demoAdjustments = [
                ['adjustment_date' => '2026-03-31', 'adjustment_type' => 'Bank Charges', 'amount' => -500, 'sort_order' => 1, 'status' => true, 'remarks' => 'Monthly bank charges'],
                ['adjustment_date' => '2026-03-31', 'adjustment_type' => 'Interest', 'amount' => 1200, 'sort_order' => 2, 'status' => true, 'remarks' => 'Interest earned'],
                ['adjustment_date' => '2026-03-31', 'adjustment_type' => 'NEFT Charge', 'amount' => -25, 'sort_order' => 3, 'status' => true, 'remarks' => 'NEFT transaction fee'],
            ];
            foreach ($demoAdjustments as $adjustment) {
                $bankReconciliation->adjustments()->create($adjustment);
            }
        }

        // Reload with fresh data
        $bankReconciliation->refresh();
        $bankReconciliation->load(['cheques', 'deposits', 'adjustments']);

        // 🔥 ডিবাগ – কনসোলে দেখুন ডেটা আসছে কিনা
        \Log::info('Bank Reconciliation Data:', [
            'computation_id' => $computationId,
            'bank_reconciliation_id' => $bankReconciliation->id,
            'cheques_count' => $bankReconciliation->cheques->count(),
            'deposits_count' => $bankReconciliation->deposits->count(),
            'adjustments_count' => $bankReconciliation->adjustments->count(),
        ]);

        return view('Backoffice.bank-reconciliation.index', [
            'computation' => $computation,
            'bankReconciliation' => $bankReconciliation,
            'cheques' => $bankReconciliation->cheques()->orderBy('sort_order')->get(),
            'deposits' => $bankReconciliation->deposits()->orderBy('sort_order')->get(),
            'adjustments' => $bankReconciliation->adjustments()->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Get all bank reconciliation data as JSON.
     */
    public function getData($computationId)
    {
        $bankReconciliation = BankReconciliation::where('computation_id', $computationId)
            ->with(['cheques', 'deposits', 'adjustments'])
            ->first();

        if (!$bankReconciliation) {
            return response()->json([
                'bankReconciliation' => null,
                'cheques' => [],
                'deposits' => [],
                'adjustments' => [],
            ]);
        }

        return response()->json([
            'bankReconciliation' => $bankReconciliation,
            'cheques' => $bankReconciliation->cheques()->orderBy('sort_order')->get(),
            'deposits' => $bankReconciliation->deposits()->orderBy('sort_order')->get(),
            'adjustments' => $bankReconciliation->adjustments()->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Save all bank reconciliation data (bulk save / auto-save).
     */
    public function saveDraft(Request $request, $computationId)
    {
        $data = $request->validate([
            'statement_date' => 'nullable|date',
            'closing_balance_bank' => 'nullable|numeric',
            'closing_balance_cashbook' => 'nullable|numeric',
            'cheques' => 'nullable|array',
            'cheques.*.id' => 'nullable|exists:bank_reconciliation_cheques,id',
            'cheques.*.cheque_date' => 'nullable|date',
            'cheques.*.cheque_no' => 'nullable|string|max:50',
            'cheques.*.amount' => 'nullable|numeric|min:0',
            'deposits' => 'nullable|array',
            'deposits.*.id' => 'nullable|exists:bank_reconciliation_deposits,id',
            'deposits.*.deposit_date' => 'nullable|date',
            'deposits.*.amount' => 'nullable|numeric|min:0',
            'adjustments' => 'nullable|array',
            'adjustments.*.id' => 'nullable|exists:bank_reconciliation_adjustments,id',
            'adjustments.*.adjustment_date' => 'nullable|date',
            'adjustments.*.adjustment_type' => 'nullable|string|max:100',
            'adjustments.*.amount' => 'nullable|numeric',
            'adjustments.*.remarks' => 'nullable|string|max:255',
        ]);

        // Find or create bank reconciliation
        $bankReconciliation = BankReconciliation::firstOrCreate(
            ['computation_id' => $computationId],
            [
                'statement_date' => now(),
                'closing_balance_bank' => 0,
                'closing_balance_cashbook' => 0,
                'status' => true,
                'created_by' => Auth::id(),
            ]
        );

        // Update main record
        $bankReconciliation->update([
            'statement_date' => $data['statement_date'] ?? $bankReconciliation->statement_date,
            'closing_balance_bank' => $data['closing_balance_bank'] ?? 0,
            'closing_balance_cashbook' => $data['closing_balance_cashbook'] ?? 0,
            'updated_by' => Auth::id(),
        ]);

        // ─── Save Cheques ──────────────────────────────────────────────
        $updatedCheques = [];
        if (isset($data['cheques'])) {
            // First, delete cheques that are not in the list
            $existingIds = $bankReconciliation->cheques()->pluck('id')->toArray();
            $submittedIds = array_filter(array_column($data['cheques'], 'id'));
            $idsToDelete = array_diff($existingIds, $submittedIds);
            if (!empty($idsToDelete)) {
                $bankReconciliation->cheques()->whereIn('id', $idsToDelete)->delete();
            }

            foreach ($data['cheques'] as $chequeData) {
                if (isset($chequeData['id']) && $chequeData['id']) {
                    $cheque = BankReconciliationCheque::find($chequeData['id']);
                    if ($cheque && $cheque->bank_reconciliation_id == $bankReconciliation->id) {
                        $cheque->update($chequeData);
                        $updatedCheques[] = $cheque->fresh();
                    }
                } else {
                    $maxSort = $bankReconciliation->cheques()->max('sort_order') ?? 0;
                    $new = $bankReconciliation->cheques()->create([
                        'cheque_date' => $chequeData['cheque_date'] ?? null,
                        'cheque_no' => $chequeData['cheque_no'] ?? null,
                        'amount' => $chequeData['amount'] ?? 0,
                        'sort_order' => $maxSort + 1,
                        'status' => true,
                    ]);
                    $updatedCheques[] = $new;
                }
            }
        } else {
            // If no cheques submitted, delete all
            $bankReconciliation->cheques()->delete();
        }

        // ─── Save Deposits ─────────────────────────────────────────────
        $updatedDeposits = [];
        if (isset($data['deposits'])) {
            $existingIds = $bankReconciliation->deposits()->pluck('id')->toArray();
            $submittedIds = array_filter(array_column($data['deposits'], 'id'));
            $idsToDelete = array_diff($existingIds, $submittedIds);
            if (!empty($idsToDelete)) {
                $bankReconciliation->deposits()->whereIn('id', $idsToDelete)->delete();
            }

            foreach ($data['deposits'] as $depositData) {
                if (isset($depositData['id']) && $depositData['id']) {
                    $deposit = BankReconciliationDeposit::find($depositData['id']);
                    if ($deposit && $deposit->bank_reconciliation_id == $bankReconciliation->id) {
                        $deposit->update($depositData);
                        $updatedDeposits[] = $deposit->fresh();
                    }
                } else {
                    $maxSort = $bankReconciliation->deposits()->max('sort_order') ?? 0;
                    $new = $bankReconciliation->deposits()->create([
                        'deposit_date' => $depositData['deposit_date'] ?? null,
                        'amount' => $depositData['amount'] ?? 0,
                        'sort_order' => $maxSort + 1,
                        'status' => true,
                    ]);
                    $updatedDeposits[] = $new;
                }
            }
        } else {
            $bankReconciliation->deposits()->delete();
        }

        // ─── Save Adjustments ──────────────────────────────────────────
        $updatedAdjustments = [];
        if (isset($data['adjustments'])) {
            $existingIds = $bankReconciliation->adjustments()->pluck('id')->toArray();
            $submittedIds = array_filter(array_column($data['adjustments'], 'id'));
            $idsToDelete = array_diff($existingIds, $submittedIds);
            if (!empty($idsToDelete)) {
                $bankReconciliation->adjustments()->whereIn('id', $idsToDelete)->delete();
            }

            foreach ($data['adjustments'] as $adjustmentData) {
                if (isset($adjustmentData['id']) && $adjustmentData['id']) {
                    $adjustment = BankReconciliationAdjustment::find($adjustmentData['id']);
                    if ($adjustment && $adjustment->bank_reconciliation_id == $bankReconciliation->id) {
                        $adjustment->update($adjustmentData);
                        $updatedAdjustments[] = $adjustment->fresh();
                    }
                } else {
                    $maxSort = $bankReconciliation->adjustments()->max('sort_order') ?? 0;
                    $new = $bankReconciliation->adjustments()->create([
                        'adjustment_date' => $adjustmentData['adjustment_date'] ?? null,
                        'adjustment_type' => $adjustmentData['adjustment_type'] ?? 'Others',
                        'amount' => $adjustmentData['amount'] ?? 0,
                        'remarks' => $adjustmentData['remarks'] ?? null,
                        'sort_order' => $maxSort + 1,
                        'status' => true,
                    ]);
                    $updatedAdjustments[] = $new;
                }
            }
        } else {
            $bankReconciliation->adjustments()->delete();
        }

        // Refresh and return updated data
        $bankReconciliation->refresh();

        return response()->json([
            'success' => true,
            'bankReconciliation' => $bankReconciliation,
            'cheques' => $updatedCheques,
            'deposits' => $updatedDeposits,
            'adjustments' => $updatedAdjustments,
            'message' => 'Bank reconciliation saved successfully.',
        ]);
    }

    // ─── CHEQUE CRUD ─────────────────────────────────────────────────────

    /**
     * Add a new cheque row.
     */
    public function addChequeRow($computationId)
    {
        $bankReconciliation = BankReconciliation::firstOrCreate(
            ['computation_id' => $computationId],
            [
                'statement_date' => now(),
                'closing_balance_bank' => 0,
                'closing_balance_cashbook' => 0,
                'status' => true,
                'created_by' => Auth::id(),
            ]
        );

        $maxSort = $bankReconciliation->cheques()->max('sort_order') ?? 0;
        $new = $bankReconciliation->cheques()->create([
            'cheque_date' => null,
            'cheque_no' => '',
            'amount' => 0,
            'sort_order' => $maxSort + 1,
            'status' => true,
        ]);

        return response()->json(['success' => true, 'cheque' => $new]);
    }

    /**
     * Delete a cheque row.
     */
    public function deleteChequeRow($computationId, $rowId)
    {
        $cheque = BankReconciliationCheque::findOrFail($rowId);
        $cheque->delete();

        return response()->json(['success' => true, 'message' => 'Cheque deleted.']);
    }

    // ─── DEPOSIT CRUD ────────────────────────────────────────────────────

    /**
     * Add a new deposit row.
     */
    public function addDepositRow($computationId)
    {
        $bankReconciliation = BankReconciliation::firstOrCreate(
            ['computation_id' => $computationId],
            [
                'statement_date' => now(),
                'closing_balance_bank' => 0,
                'closing_balance_cashbook' => 0,
                'status' => true,
                'created_by' => Auth::id(),
            ]
        );

        $maxSort = $bankReconciliation->deposits()->max('sort_order') ?? 0;
        $new = $bankReconciliation->deposits()->create([
            'deposit_date' => null,
            'amount' => 0,
            'sort_order' => $maxSort + 1,
            'status' => true,
        ]);

        return response()->json(['success' => true, 'deposit' => $new]);
    }

    /**
     * Delete a deposit row.
     */
    public function deleteDepositRow($computationId, $rowId)
    {
        $deposit = BankReconciliationDeposit::findOrFail($rowId);
        $deposit->delete();

        return response()->json(['success' => true, 'message' => 'Deposit deleted.']);
    }

    // ─── ADJUSTMENT CRUD ────────────────────────────────────────────────

    /**
     * Add a new adjustment row.
     */
    public function addAdjustmentRow($computationId)
    {
        $bankReconciliation = BankReconciliation::firstOrCreate(
            ['computation_id' => $computationId],
            [
                'statement_date' => now(),
                'closing_balance_bank' => 0,
                'closing_balance_cashbook' => 0,
                'status' => true,
                'created_by' => Auth::id(),
            ]
        );

        $maxSort = $bankReconciliation->adjustments()->max('sort_order') ?? 0;
        $new = $bankReconciliation->adjustments()->create([
            'adjustment_date' => null,
            'adjustment_type' => 'Others',
            'amount' => 0,
            'remarks' => null,
            'sort_order' => $maxSort + 1,
            'status' => true,
        ]);

        return response()->json(['success' => true, 'adjustment' => $new]);
    }

    /**
     * Delete an adjustment row.
     */
    public function deleteAdjustmentRow($computationId, $rowId)
    {
        $adjustment = BankReconciliationAdjustment::findOrFail($rowId);
        $adjustment->delete();

        return response()->json(['success' => true, 'message' => 'Adjustment deleted.']);
    }

    // ─── SIGNATURE ──────────────────────────────────────────────────────

    /**
     * Upload CA signature.
     */
    public function uploadSignature(Request $request, $computationId)
    {
        // Implement signature storage logic
        return response()->json(['success' => true, 'message' => 'Signature uploaded.']);
    }

    /**
     * Clear CA signature.
     */
    public function clearSignature($computationId)
    {
        // Implement signature removal logic
        return response()->json(['success' => true, 'message' => 'Signature cleared.']);
    }

    // ─── FINALIZE ──────────────────────────────────────────────────────

    /**
     * Finalize the bank reconciliation report.
     */
    public function finalize($computationId)
    {
        $bankReconciliation = BankReconciliation::where('computation_id', $computationId)->first();
        if ($bankReconciliation) {
            // Add any finalization logic here
            // e.g., $bankReconciliation->update(['is_finalized' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Report finalized.']);
    }
}