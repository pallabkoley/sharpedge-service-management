<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Computation;
use App\Models\PurchaseReconciliation;
use App\Models\PurchaseReconciliationRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PurchaseReconciliationController extends Controller
{
    /**
     * Months array for system rows.
     */
    protected const MONTHS = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];

    /**
     * Display the Purchase Reconciliation page (both register & unregister).
     */
    public function index(Computation $computation)
    {
        // Load both register and unregister reconciliations with their rows
        $computation->load([
            'client',
            'registerPurchaseReconciliation.rows' => function ($query) {
                $query->orderBy('row_order');
            },
            'unregisterPurchaseReconciliation.rows' => function ($query) {
                $query->orderBy('row_order');
            },
        ]);

        $register = $computation->registerPurchaseReconciliation;
        $registerRows = $register ? $register->rows : collect();

        $unregister = $computation->unregisterPurchaseReconciliation;
        $unregisterRows = $unregister ? $unregister->rows : collect();

        // Compute totals for register (since that's the default active tab)
        $totals = [
            'taxable'   => $registerRows->sum('taxable_value'),
            'exempted'  => $registerRows->sum('exempted_value'),
            'igst'      => $registerRows->sum('igst'),
            'cgst'      => $registerRows->sum('cgst'),
            'sgst'      => $registerRows->sum('sgst'),
            'cgst_tds'  => $registerRows->sum('cgst_tds'),
            'sgst_tds'  => $registerRows->sum('sgst_tds'),
            'invoice'   => $registerRows->sum('invoice_value'),
        ];

        return view('Backoffice.purchase-reconciliation.index', [
            'computation'    => $computation,
            'register'       => $register,
            'registerRows'   => $registerRows,
            'unregister'     => $unregister,
            'unregisterRows' => $unregisterRows,
            'totals'         => $totals,
        ]);
    }

    /**
     * Get or create a reconciliation for the given computation and type.
     */
    protected function getOrCreateReconciliation(Computation $computation, string $type): PurchaseReconciliation
    {
        $reconciliation = PurchaseReconciliation::where('computation_id', $computation->id)
            ->where('reconciliation_type', $type)
            ->first();

        if (!$reconciliation) {
            $reconciliation = DB::transaction(function () use ($computation, $type) {
                $reconciliation = PurchaseReconciliation::create([
                    'computation_id'      => $computation->id,
                    'reconciliation_type' => $type,
                    'status'              => 'draft',
                    'prepared_by'         => 'SharpEdge India',
                    'chartered_accountant' => 'Ankit Hansaria',
                    'membership_number'   => '301586',
                    'place'               => 'Kolkata',
                ]);

                foreach (self::MONTHS as $index => $month) {
                    PurchaseReconciliationRow::create([
                        'purchase_reconciliation_id' => $reconciliation->id,
                        'row_order'                  => $index + 1,
                        'row_type'                   => 'month',
                        'row_label'                  => $month,
                        'taxable_value'              => 0,
                        'exempted_value'             => 0,
                        'igst'                       => 0,
                        'cgst'                       => 0,
                        'sgst'                       => 0,
                        'cgst_tds'                   => 0,
                        'sgst_tds'                   => 0,
                        'invoice_value'              => 0,
                        'remarks'                    => null,
                        'is_system_row'              => 1,
                    ]);
                }

                return $reconciliation;
            });
        }

        return $reconciliation;
    }

    /**
     * Get reconciliation data as JSON (for AJAX).
     */
    public function getData(Request $request, Computation $computation)
    {
        $type = $request->input('type', 'register');
        $reconciliation = $this->getOrCreateReconciliation($computation, $type);
        $rows = $reconciliation->rows()->orderBy('row_order')->get();

        return response()->json([
            'reconciliation' => $reconciliation,
            'rows'           => $rows,
        ]);
    }

    /**
     * Save draft of the purchase reconciliation.
     */
    public function saveDraft(Request $request, Computation $computation)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', Rule::in(['register', 'unregister'])],
            'rows' => ['required', 'array'],
            'rows.*.id'               => ['nullable', 'integer'],
            'rows.*.row_order'        => ['nullable', 'integer'],
            'rows.*.row_label'        => ['nullable', 'string', 'max:100'],
            'rows.*.taxable_value'    => ['nullable', 'numeric', 'min:0'],
            'rows.*.exempted_value'   => ['nullable', 'numeric', 'min:0'],
            'rows.*.igst'             => ['nullable', 'numeric', 'min:0'],
            'rows.*.cgst'             => ['nullable', 'numeric', 'min:0'],
            'rows.*.sgst'             => ['nullable', 'numeric', 'min:0'],
            'rows.*.cgst_tds'         => ['nullable', 'numeric', 'min:0'],
            'rows.*.sgst_tds'         => ['nullable', 'numeric', 'min:0'],
            'rows.*.remarks'          => ['nullable', 'string', 'max:500'],
            'header'                  => ['nullable', 'array'],
            'header.prepared_by'      => ['nullable', 'string', 'max:150'],
            'header.chartered_accountant' => ['nullable', 'string', 'max:150'],
            'header.membership_number'    => ['nullable', 'string', 'max:100'],
            'header.place'                => ['nullable', 'string', 'max:100'],
            'header.signing_date'         => ['nullable', 'date'],
            'header.itr_acknowledgement_no' => ['nullable', 'string', 'max:150'],
            'header.audit_acknowledgement_no' => ['nullable', 'string', 'max:150'],
            'header.ie10_acknowledgement_no'  => ['nullable', 'string', 'max:150'],
            'header.udin'                 => ['nullable', 'string', 'max:150'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $computation) {
                $type = $request->input('type', 'register');
                $reconciliation = $this->getOrCreateReconciliation($computation, $type);

                // Update header
                $header = $request->input('header', []);
                if (!empty($header)) {
                    $reconciliation->update([
                        'prepared_by'    => $header['prepared_by'] ?? $reconciliation->prepared_by,
                        'chartered_accountant' => $header['chartered_accountant'] ?? $reconciliation->chartered_accountant,
                        'membership_number'    => $header['membership_number'] ?? $reconciliation->membership_number,
                        'place'                => $header['place'] ?? $reconciliation->place,
                        'signing_date'         => $header['signing_date'] ?? $reconciliation->signing_date,
                        'itr_acknowledgement_no' => $header['itr_acknowledgement_no'] ?? $reconciliation->itr_acknowledgement_no,
                        'audit_acknowledgement_no' => $header['audit_acknowledgement_no'] ?? $reconciliation->audit_acknowledgement_no,
                        'ie10_acknowledgement_no'  => $header['ie10_acknowledgement_no'] ?? $reconciliation->ie10_acknowledgement_no,
                        'udin'                 => $header['udin'] ?? $reconciliation->udin,
                    ]);
                }

                // Save rows
                $rows = $request->input('rows', []);
                $maxOrder = PurchaseReconciliationRow::where('purchase_reconciliation_id', $reconciliation->id)
                    ->max('row_order') ?? 0;

                foreach ($rows as $rowData) {
                    $taxable       = (float) ($rowData['taxable_value'] ?? 0);
                    $exempted      = (float) ($rowData['exempted_value'] ?? 0);
                    $igst          = (float) ($rowData['igst'] ?? 0);
                    $cgst          = (float) ($rowData['cgst'] ?? 0);
                    $sgst          = (float) ($rowData['sgst'] ?? 0);
                    $cgstTds       = (float) ($rowData['cgst_tds'] ?? 0);
                    $sgstTds       = (float) ($rowData['sgst_tds'] ?? 0);
                    $invoiceValue  = $taxable + $exempted + $igst + $cgst + $sgst + $cgstTds + $sgstTds;

                    if (isset($rowData['id']) && !empty($rowData['id'])) {
                        // Update existing row
                        $row = PurchaseReconciliationRow::where('purchase_reconciliation_id', $reconciliation->id)
                            ->where('id', $rowData['id'])
                            ->first();
                        if ($row) {
                            $updateData = [
                                'taxable_value'  => $taxable,
                                'exempted_value' => $exempted,
                                'igst'           => $igst,
                                'cgst'           => $cgst,
                                'sgst'           => $sgst,
                                'cgst_tds'       => $cgstTds,
                                'sgst_tds'       => $sgstTds,
                                'invoice_value'  => $invoiceValue,
                                'remarks'        => $rowData['remarks'] ?? null,
                            ];
                            if ($row->row_type === 'custom' && isset($rowData['row_label'])) {
                                $updateData['row_label'] = $rowData['row_label'];
                            }
                            $row->update($updateData);
                        }
                    } else {
                        // Create new custom row
                        $maxOrder++;
                        PurchaseReconciliationRow::create([
                            'purchase_reconciliation_id' => $reconciliation->id,
                            'row_order'                  => $rowData['row_order'] ?? $maxOrder,
                            'row_type'                   => 'custom',
                            'row_label'                  => $rowData['row_label'] ?? 'Custom ' . ($maxOrder - 12),
                            'taxable_value'              => $taxable,
                            'exempted_value'             => $exempted,
                            'igst'                       => $igst,
                            'cgst'                       => $cgst,
                            'sgst'                       => $sgst,
                            'cgst_tds'                   => $cgstTds,
                            'sgst_tds'                   => $sgstTds,
                            'invoice_value'              => $invoiceValue,
                            'remarks'                    => $rowData['remarks'] ?? null,
                            'is_system_row'              => 0,
                        ]);
                    }
                }

                $reconciliation->update(['status' => 'draft']);
            });

            // Reload and return fresh data
            $reconciliation = PurchaseReconciliation::where('computation_id', $computation->id)
                ->where('reconciliation_type', $request->input('type', 'register'))
                ->first();

            if ($reconciliation) {
                $reconciliation->load(['rows' => function ($query) {
                    $query->orderBy('row_order');
                }]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Draft saved successfully!',
                'reconciliation' => $reconciliation,
                'rows' => $reconciliation ? $reconciliation->rows : [],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a new custom row.
     */
    public function addRow(Request $request, Computation $computation)
    {
        $validator = Validator::make($request->all(), [
            'type'      => ['required', Rule::in(['register', 'unregister'])],
            'row_label' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $computation) {
                $type = $request->input('type', 'register');
                $reconciliation = $this->getOrCreateReconciliation($computation, $type);

                $maxOrder = PurchaseReconciliationRow::where('purchase_reconciliation_id', $reconciliation->id)
                    ->max('row_order') ?? 0;

                $customCount = PurchaseReconciliationRow::where('purchase_reconciliation_id', $reconciliation->id)
                    ->where('row_type', 'custom')
                    ->count();

                PurchaseReconciliationRow::create([
                    'purchase_reconciliation_id' => $reconciliation->id,
                    'row_order'                  => $maxOrder + 1,
                    'row_type'                   => 'custom',
                    'row_label'                  => $request->input('row_label', 'Custom ' . ($customCount + 1)),
                    'taxable_value'              => 0,
                    'exempted_value'             => 0,
                    'igst'                       => 0,
                    'cgst'                       => 0,
                    'sgst'                       => 0,
                    'cgst_tds'                   => 0,
                    'sgst_tds'                   => 0,
                    'invoice_value'              => 0,
                    'remarks'                    => null,
                    'is_system_row'              => 0,
                ]);
            });

            // Reload
            $reconciliation = PurchaseReconciliation::where('computation_id', $computation->id)
                ->where('reconciliation_type', $request->input('type', 'register'))
                ->first();

            if ($reconciliation) {
                $reconciliation->load(['rows' => function ($query) {
                    $query->orderBy('row_order');
                }]);
            }

            return response()->json([
                'success' => true,
                'message' => 'New row added successfully!',
                'reconciliation' => $reconciliation,
                'rows' => $reconciliation ? $reconciliation->rows : [],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add row: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a custom row.
     */
    public function deleteRow(Request $request, Computation $computation, $rowId)
    {
        try {
            $row = PurchaseReconciliationRow::where('id', $rowId)
                ->where('row_type', 'custom')
                ->whereHas('reconciliation', function ($query) use ($computation) {
                    $query->where('computation_id', $computation->id);
                })
                ->first();

            if (!$row) {
                return response()->json([
                    'success' => false,
                    'message' => 'Row not found or cannot be deleted.'
                ], 404);
            }

            $reconciliationId = $row->purchase_reconciliation_id;
            $row->delete();

            // Reorder remaining rows
            $remainingRows = PurchaseReconciliationRow::where('purchase_reconciliation_id', $reconciliationId)
                ->orderBy('row_order')
                ->get();

            foreach ($remainingRows as $index => $remainingRow) {
                $remainingRow->update(['row_order' => $index + 1]);
            }

            // Reload
            $reconciliation = PurchaseReconciliation::where('computation_id', $computation->id)
                ->where('reconciliation_type', $request->input('type', 'register'))
                ->first();

            if ($reconciliation) {
                $reconciliation->load(['rows' => function ($query) {
                    $query->orderBy('row_order');
                }]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Row deleted successfully!',
                'reconciliation' => $reconciliation,
                'rows' => $reconciliation ? $reconciliation->rows : [],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete row: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload CA or Assessee signature.
     * Stores directly in "Purchase Sales_Signature" folder.
     */
    public function uploadSignature(Request $request, Computation $computation)
    {
        $validator = Validator::make($request->all(), [
            'type'           => ['required', Rule::in(['register', 'unregister'])],
            'signature_type' => ['required', Rule::in(['ca', 'assessee'])],
            'signature'      => ['required', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $type = $request->input('type', 'register');
            $signatureType = $request->input('signature_type');
            $reconciliation = $this->getOrCreateReconciliation($computation, $type);

            $file = $request->file('signature');
            $extension = $file->getClientOriginalExtension();
            $filename = 'purchase_signature_' . $signatureType . '_' . time() . '_' . uniqid() . '.' . $extension;

            // ✅ Store directly in "Purchase Sales_Signature" folder
            $path = $file->storeAs('Purchase Sales_Signature', $filename, 'public');

            $field = $signatureType === 'ca' ? 'ca_signature' : 'assessee_signature';

            if ($reconciliation->$field && Storage::disk('public')->exists($reconciliation->$field)) {
                Storage::disk('public')->delete($reconciliation->$field);
            }

            $reconciliation->update([$field => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Signature uploaded successfully!',
                'path'    => $path,
                'url'     => Storage::url($path),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload signature: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear CA or Assessee signature.
     */
    public function clearSignature(Request $request, Computation $computation)
    {
        $validator = Validator::make($request->all(), [
            'type'           => ['required', Rule::in(['register', 'unregister'])],
            'signature_type' => ['required', Rule::in(['ca', 'assessee'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $type = $request->input('type', 'register');
            $signatureType = $request->input('signature_type');
            $reconciliation = $this->getOrCreateReconciliation($computation, $type);

            $field = $signatureType === 'ca' ? 'ca_signature' : 'assessee_signature';

            if ($reconciliation->$field && Storage::disk('public')->exists($reconciliation->$field)) {
                Storage::disk('public')->delete($reconciliation->$field);
            }

            $reconciliation->update([$field => null]);

            return response()->json([
                'success' => true,
                'message' => 'Signature cleared successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear signature: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalize the purchase reconciliation.
     */
    public function finalize(Request $request, Computation $computation)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', Rule::in(['register', 'unregister'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $type = $request->input('type', 'register');
            $reconciliation = $this->getOrCreateReconciliation($computation, $type);
            $reconciliation->update(['status' => 'finalized']);

            return response()->json([
                'success' => true,
                'message' => 'Report finalized successfully!',
                'reconciliation' => $reconciliation,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to finalize report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Separate view for Register Purchase.
     */
    public function register(Computation $computation)
    {
        $reconciliation = $this->getOrCreateReconciliation($computation, 'register');
        $rows = $reconciliation->rows()->orderBy('row_order')->get();

        return view('Backoffice.purchase-reconciliation.register', [
            'computation'    => $computation,
            'reconciliation' => $reconciliation,
            'rows'           => $rows,
        ]);
    }

    /**
     * Separate view for Unregister Purchase.
     */
    public function unregister(Computation $computation)
    {
        $reconciliation = $this->getOrCreateReconciliation($computation, 'unregister');
        $rows = $reconciliation->rows()->orderBy('row_order')->get();

        return view('Backoffice.purchase-reconciliation.unregister', [
            'computation'    => $computation,
            'reconciliation' => $reconciliation,
            'rows'           => $rows,
        ]);
    }
}