<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Computation;
use App\Models\CreditorReconciliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreditorReconciliationController extends Controller
{
    /**
     * Display the Creditors Reconciliation page.
     */
    public function index($computationId)
    {
        $computation = Computation::with('creditorReconciliations')->findOrFail($computationId);
        return view('Backoffice.creditors-reconciliation.index', [
            'computation' => $computation,
            'creditors'   => $computation->creditorReconciliations()->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Register view (same as index – kept for consistency).
     */
    public function register($computationId)
    {
        return $this->index($computationId);
    }

    /**
     * Unregister view (same as index).
     */
    public function unregister($computationId)
    {
        return $this->index($computationId);
    }

    /**
     * Get all creditor rows as JSON.
     */
    public function getData($computationId)
    {
        $computation = Computation::findOrFail($computationId);
        return response()->json([
            'creditors' => $computation->creditorReconciliations()->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Save all creditor rows (bulk save / auto-save).
     */
    public function saveDraft(Request $request, $computationId)
    {
        $data = $request->validate([
            'creditors' => 'required|array',
            'creditors.*.id' => 'nullable|exists:creditor_reconciliations,id',
            'creditors.*.creditor_name' => 'required|string|max:255',
            'creditors.*.pan' => 'nullable|string|max:20',
            'creditors.*.gstin' => 'nullable|string|max:30',
            'creditors.*.bill_amount' => 'nullable|numeric|min:0',
            'creditors.*.payment_followup_date' => 'nullable|date',
            'creditors.*.amount_paid' => 'nullable|numeric|min:0',
            'creditors.*.remarks' => 'nullable|string|max:500',
        ]);

        $computation = Computation::findOrFail($computationId);
        $updated = [];

        foreach ($data['creditors'] as $row) {
            if (isset($row['id'])) {
                $creditor = $computation->creditorReconciliations()->find($row['id']);
                if ($creditor) {
                    $creditor->update($row);
                    $creditor->updated_by = Auth::id();
                    $creditor->save();
                    $updated[] = $creditor->fresh();
                }
            } else {
                // New row
                $maxSort = $computation->creditorReconciliations()->max('sort_order') ?? 0;
                $new = $computation->creditorReconciliations()->create([
                    'creditor_name' => $row['creditor_name'],
                    'pan' => $row['pan'] ?? null,
                    'gstin' => $row['gstin'] ?? null,
                    'bill_amount' => $row['bill_amount'] ?? 0,
                    'payment_followup_date' => $row['payment_followup_date'] ?? null,
                    'amount_paid' => $row['amount_paid'] ?? 0,
                    'remarks' => $row['remarks'] ?? null,
                    'sort_order' => $maxSort + 1,
                    'created_by' => Auth::id(),
                    'status' => true,
                ]);
                $updated[] = $new;
            }
        }

        return response()->json(['success' => true, 'creditors' => $updated]);
    }

    /**
     * Add a single empty creditor row.
     */
    public function addRow($computationId)
    {
        $computation = Computation::findOrFail($computationId);
        $maxSort = $computation->creditorReconciliations()->max('sort_order') ?? 0;

        $new = $computation->creditorReconciliations()->create([
            'creditor_name' => 'New Creditor',
            'bill_amount' => 0,
            'amount_paid' => 0,
            'sort_order' => $maxSort + 1,
            'created_by' => Auth::id(),
            'status' => true,
        ]);

        return response()->json(['success' => true, 'creditor' => $new]);
    }

    /**
     * Delete a creditor row.
     */
    public function deleteRow($computationId, $rowId)
    {
        $computation = Computation::findOrFail($computationId);
        $creditor = $computation->creditorReconciliations()->findOrFail($rowId);
        $creditor->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Upload CA signature (placeholder – implement as needed).
     */
    public function uploadSignature(Request $request, $computationId)
    {
        // Example implementation: store signature image path in computation or separate table
        // For now, just return success.
        return response()->json(['success' => true, 'message' => 'Signature uploaded successfully.']);
    }

    /**
     * Clear CA signature.
     */
    public function clearSignature($computationId)
    {
        // Remove signature
        return response()->json(['success' => true, 'message' => 'Signature cleared.']);
    }

    /**
     * Finalize the creditors reconciliation report.
     */
    public function finalize($computationId)
    {
        $computation = Computation::findOrFail($computationId);
        // Mark as finalized – you can update computation status or add a flag
        // For example: $computation->update(['reconciliation_finalized' => true]);
        // Or just return success.
        return response()->json(['success' => true, 'message' => 'Report finalized.']);
    }
}