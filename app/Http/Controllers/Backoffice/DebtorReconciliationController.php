<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Computation;
use App\Models\DebtorReconciliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebtorReconciliationController extends Controller
{
    public function index($computationId)
    {
        $computation = Computation::with('debtors')->findOrFail($computationId);
        return view('Backoffice.debtors-reconciliation.index', [
            'computation' => $computation,
            'debtors'     => $computation->debtors()->orderBy('sort_order')->get(),
        ]);
    }

    public function register($computationId) { /* same as index */ }
    public function unregister($computationId) { /* same as index */ }

    public function getData($computationId)
    {
        $computation = Computation::findOrFail($computationId);
        return response()->json([
            'debtors' => $computation->debtors()->orderBy('sort_order')->get(),
        ]);
    }

    public function saveDraft(Request $request, $computationId)
    {
        // Bulk save: expects array of debtors
        $data = $request->validate([
            'debtors' => 'required|array',
            'debtors.*.id' => 'nullable|exists:debtor_reconciliations,id',
            'debtors.*.debtor_name' => 'required|string|max:255',
            'debtors.*.pan' => 'nullable|string|max:20',
            'debtors.*.gstin' => 'nullable|string|max:30',
            'debtors.*.bill_amount' => 'nullable|numeric|min:0',
            'debtors.*.payment_followup_date' => 'nullable|date',
            'debtors.*.amount_received' => 'nullable|numeric|min:0',
            'debtors.*.remarks' => 'nullable|string|max:500',
        ]);

        $computation = Computation::findOrFail($computationId);
        $updated = [];
        foreach ($data['debtors'] as $row) {
            if (isset($row['id'])) {
                $debtor = $computation->debtors()->find($row['id']);
                if ($debtor) {
                    $debtor->update($row);
                    $updated[] = $debtor->fresh();
                }
            } else {
                // new row
                $maxSort = $computation->debtors()->max('sort_order') ?? 0;
                $new = $computation->debtors()->create([
                    'debtor_name' => $row['debtor_name'],
                    'pan' => $row['pan'] ?? null,
                    'gstin' => $row['gstin'] ?? null,
                    'bill_amount' => $row['bill_amount'] ?? 0,
                    'payment_followup_date' => $row['payment_followup_date'] ?? null,
                    'amount_received' => $row['amount_received'] ?? 0,
                    'remarks' => $row['remarks'] ?? null,
                    'sort_order' => $maxSort + 1,
                    'created_by' => Auth::id(),
                    'status' => true,
                ]);
                $updated[] = $new;
            }
        }

        return response()->json(['success' => true, 'debtors' => $updated]);
    }

    public function addRow($computationId)
    {
        $computation = Computation::findOrFail($computationId);
        $maxSort = $computation->debtors()->max('sort_order') ?? 0;
        $new = $computation->debtors()->create([
            'debtor_name' => 'New Debtor',
            'bill_amount' => 0,
            'amount_received' => 0,
            'sort_order' => $maxSort + 1,
            'created_by' => Auth::id(),
            'status' => true,
        ]);
        return response()->json(['success' => true, 'debtor' => $new]);
    }

    public function deleteRow($computationId, $rowId)
    {
        $computation = Computation::findOrFail($computationId);
        $debtor = $computation->debtors()->findOrFail($rowId);
        $debtor->delete();
        return response()->json(['success' => true]);
    }

    public function uploadSignature(Request $request, $computationId) { /* store image in DB or file */ }
    public function clearSignature($computationId) { /* remove signature */ }
    public function finalize($computationId) { /* mark as finalized */ }
}