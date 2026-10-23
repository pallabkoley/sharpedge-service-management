<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Computation;
use App\Models\DebitCreditNoteRegister;
use App\Models\DebitCreditNoteRegisterRow;
use App\Models\DebitCreditNoteBreakup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DebitCreditNoteRegisterController extends Controller
{
    public function index($computationId)
    {
        $computation = Computation::with([
            'debitNoteRegister.rows',
            'creditNoteRegister.rows'
        ])->findOrFail($computationId);

        return view('Backoffice.debit-credit-note-register.index', compact('computation'));
    }

    public function checkRegister($computationId, $type)
    {
        try {
            $register = DebitCreditNoteRegister::where('computation_id', $computationId)
                ->where('note_type', $type)
                ->where('status', 'draft')
                ->with('rows')
                ->latest('id')
                ->first();

            return response()->json([
                'success' => true,
                'register' => $register ?: null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request, $computationId)
    {
        try {
            $computation = Computation::findOrFail($computationId);

            $validator = Validator::make($request->all(), [
                'note_type'        => 'required|in:debit,credit',
                'gst_type'         => 'required|in:intra,inter',
                'calculation_mode' => 'required|in:manual,auto',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $register = DebitCreditNoteRegister::create([
                'computation_id'   => $computation->id,
                'note_type'        => $request->note_type,
                'gst_type'         => $request->gst_type,
                'calculation_mode' => $request->calculation_mode,
                'status'           => 'draft',
                'prepared_by'      => $request->prepared_by ?? 'SharpEdge India',
                'created_by'       => auth()->id(),
            ]);

            $financialYear = $computation->financial_year ?? date('Y') . '-' . (date('Y') + 1);
            $startYear = (int) substr($financialYear, 0, 4);
            $monthLabels = [];

            for ($i = 0; $i < 12; $i++) {
                $monthNum = $i + 4;
                if ($monthNum > 12) $monthNum -= 12;
                $year = $startYear + floor(($i + 4) / 13);
                $monthLabels[] = Carbon::createFromDate($year, $monthNum, 1)->format('M-y');
            }

            foreach ($monthLabels as $index => $label) {
                DebitCreditNoteRegisterRow::create([
                    'debit_credit_note_register_id' => $register->id,
                    'row_order'                     => $index + 1,
                    'row_type'                      => 'month',
                    'row_label'                     => $label,
                    'is_system_row'                 => 1,
                    'taxable_value'                 => 0,
                    'gst_rate'                      => 0,
                    'igst'                          => 0,
                    'cgst'                          => 0,
                    'sgst'                          => 0,
                    'invoice_value'                 => 0,
                    'has_breakup'                   => false,
                    'breakup_total'                 => 0,
                    'is_locked'                     => false,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'     => true,
                'message'     => 'Register created successfully!',
                'register_id' => $register->id,
                'register'    => $register->load('rows'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Failed to create register: ' . $e->getMessage()
            ], 500);
        }
    }

    // ─── SIMPLIFIED UPDATE METHOD – DIRECTLY UPDATES ROWS ───
public function update(Request $request, $computationId, $id)
{
    try {

        $register = DebitCreditNoteRegister::where('id', $id)
            ->where('computation_id', $computationId)
            ->with('rows')
            ->firstOrFail();

        DB::beginTransaction();

        $register->update(array_merge(
            $request->only([
                'prepared_by',
                'chartered_accountant',
                'membership_number',
                'place',
                'signing_date',
                'ca_signature',
                'assessee_signature',
                'itr_acknowledgement_no',
                'audit_acknowledgement_no',
                'ie10_acknowledgement_no',
                'udin',
            ]),
            [
                'updated_by' => auth()->id()
            ]
        ));

        $updatedIds = [];

        if ($request->has('rows')) {

            foreach ($request->rows as $rowData) {

                $row = DebitCreditNoteRegisterRow::where('id', $rowData['id'])
                    ->where('debit_credit_note_register_id', $register->id)
                    ->first();

                if (!$row) {
                    continue;
                }

                $taxable = floatval($rowData['taxable_value'] ?? 0);
                $gstRate = floatval($rowData['gst_rate'] ?? 0);

                if ($register->gst_type == 'inter') {

                    $igst = ($taxable * $gstRate) / 100;
                    $cgst = 0;
                    $sgst = 0;

                } else {

                    $cgst = ($taxable * $gstRate) / 200;
                    $sgst = ($taxable * $gstRate) / 200;
                    $igst = 0;
                }

                $invoice = $taxable + $igst + $cgst + $sgst;

                $row->update([
                    'taxable_value' => $taxable,
                    'gst_rate'      => $gstRate,
                    'igst'          => $rowData['igst'] ?? $igst,
                    'cgst'          => $rowData['cgst'] ?? $cgst,
                    'sgst'          => $rowData['sgst'] ?? $sgst,
                    'invoice_value' => $rowData['invoice_value'] ?? $invoice,
                    'remarks'       => $rowData['remarks'] ?? null,
                ]);

                $updatedIds[] = $row->id;

                if (!empty($rowData['breakups'])) {
                    $this->syncBreakups($row, $rowData['breakups']);
                }
            }
        }

        DB::commit();

        $register->refresh();
        $register->load('rows.breakups');

        return response()->json([
            'success' => true,
            'message' => 'Saved successfully.',
            'register' => $register,
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        \Log::error('Debit Credit Register Update Error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
    public function destroy($id)
    {
        try {
            $register = DebitCreditNoteRegister::findOrFail($id);
            $register->delete();

            return response()->json([
                'success' => true,
                'message' => 'Register deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Failed to delete: ' . $e->getMessage()
            ], 500);
        }
    }

    public function finalize($computation, $id)
    {
        try {
            $register = DebitCreditNoteRegister::where('id', $id)
                ->where('computation_id', $computation)
                ->firstOrFail();

            if ($register->status === 'finalized') {
                return response()->json([
                    'success' => false,
                    'message' => 'Already finalized!'
                ], 400);
            }

            $register->update([
                'status'       => 'finalized',
                'approved_by'  => auth()->id(),
                'approved_at'  => now(),
                'finalized_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Register finalized successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Finalize Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to finalize: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $register = DebitCreditNoteRegister::with('rows.breakups')->findOrFail($id);

            return response()->json([
                'success'  => true,
                'register' => $register
            ]);

        } catch (\Exception $e) {
            Log::error('Show Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Failed to load register: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRow(Request $request, $rowId)
    {
        try {
            $row = DebitCreditNoteRegisterRow::findOrFail($rowId);

            $validator = Validator::make($request->all(), [
                'taxable_value' => 'nullable|numeric|min:0',
                'gst_rate'      => 'nullable|numeric|min:0|max:100',
                'igst'          => 'nullable|numeric|min:0',
                'cgst'          => 'nullable|numeric|min:0',
                'sgst'          => 'nullable|numeric|min:0',
                'invoice_value' => 'nullable|numeric|min:0',
                'remarks'       => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            $row->update($request->only([
                'taxable_value',
                'gst_rate',
                'igst',
                'cgst',
                'sgst',
                'invoice_value',
                'remarks'
            ]));

            $row->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Row updated!',
                'row'     => $row
            ]);

        } catch (\Exception $e) {
            Log::error('UpdateRow Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Failed to update row: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBreakups($rowId)
    {
        try {
            $row = DebitCreditNoteRegisterRow::with('breakups')->findOrFail($rowId);

            return response()->json([
                'success'  => true,
                'breakups' => $row->breakups,
                'row'      => $row,
            ]);

        } catch (\Exception $e) {
            Log::error('GetBreakups Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Failed to load breakups: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveBreakups(Request $request, $rowId)
    {
        try {
            $row = DebitCreditNoteRegisterRow::findOrFail($rowId);

            $validator = Validator::make($request->all(), [
                'breakups'   => 'required|array',
                'breakups.*.id' => 'nullable|exists:debit_credit_note_breakups,id',
                'breakups.*.invoice_no' => 'nullable|string|max:100',
                'breakups.*.invoice_date' => 'nullable|date',
                'breakups.*.party_name' => 'nullable|string|max:255',
                'breakups.*.gstin' => 'nullable|string|max:50',
                'breakups.*.taxable_value' => 'nullable|numeric|min:0',
                'breakups.*.gst_rate' => 'nullable|numeric|min:0|max:100',
                'breakups.*.igst' => 'nullable|numeric|min:0',
                'breakups.*.cgst' => 'nullable|numeric|min:0',
                'breakups.*.sgst' => 'nullable|numeric|min:0',
                'breakups.*.invoice_value' => 'nullable|numeric|min:0',
                'breakups.*.remarks' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $updatedIds = [];
            $totalBreakup = 0;

            foreach ($request->breakups as $breakupData) {
                // GST calculation
                if (isset($breakupData['taxable_value']) && isset($breakupData['gst_rate'])) {
                    $taxable = floatval($breakupData['taxable_value']);
                    $gstRate = floatval($breakupData['gst_rate']);
                    $gstAmount = ($taxable * $gstRate) / 100;

                    if ($row->register->gst_type === 'inter') {
                        $breakupData['igst'] = $gstAmount;
                        $breakupData['cgst'] = 0;
                        $breakupData['sgst'] = 0;
                    } else {
                        $breakupData['cgst'] = $gstAmount / 2;
                        $breakupData['sgst'] = $gstAmount / 2;
                        $breakupData['igst'] = 0;
                    }
                    $breakupData['invoice_value'] = $taxable + $breakupData['igst'] + $breakupData['cgst'] + $breakupData['sgst'];
                }

                if (isset($breakupData['id'])) {
                    $breakup = DebitCreditNoteBreakup::find($breakupData['id']);
                    if ($breakup && $breakup->debit_credit_note_register_row_id == $row->id) {
                        $breakup->update($breakupData);
                        $updatedIds[] = $breakup->id;
                        $totalBreakup += $breakup->taxable_value ?? 0;
                    }
                } else {
                    $newBreakup = DebitCreditNoteBreakup::create(array_merge($breakupData, [
                        'debit_credit_note_register_row_id' => $row->id,
                    ]));
                    $updatedIds[] = $newBreakup->id;
                    $totalBreakup += $newBreakup->taxable_value ?? 0;
                }
            }

            $existingIds = $row->breakups()->pluck('id')->toArray();
            $deleteIds = array_diff($existingIds, $updatedIds);
            if (!empty($deleteIds)) {
                DebitCreditNoteBreakup::whereIn('id', $deleteIds)->delete();
            }

            $row->update([
                'has_breakup'   => count($updatedIds) > 0,
                'breakup_total' => $totalBreakup,
            ]);

            DB::commit();
            $row->refresh();
            $row->load('breakups');

            return response()->json([
                'success'  => true,
                'message'  => 'Breakups saved successfully!',
                'row'      => $row,
                'breakups' => $row->breakups,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SaveBreakups Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Failed to save breakups: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyBreakup($breakupId)
    {
        try {
            $breakup = DebitCreditNoteBreakup::findOrFail($breakupId);
            $row = $breakup->row;

            $breakup->delete();

            $remaining = $row->breakups()->sum('taxable_value') ?? 0;
            $row->update([
                'has_breakup'   => $row->breakups()->count() > 0,
                'breakup_total' => $remaining,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Breakup deleted!',
                'row'     => $row->fresh('breakups'),
            ]);

        } catch (\Exception $e) {
            Log::error('DestroyBreakup Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Failed to delete breakup: ' . $e->getMessage()
            ], 500);
        }
    }

    private function syncBreakups($row, array $breakupDataList)
    {
        // This method is kept for backward compatibility but not used in simplified update.
        // You can keep it empty.
    }
}