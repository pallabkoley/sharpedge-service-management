<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Computation;
use App\Models\TradingAccount;
use App\Models\TradingAccountRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TradingAccountController extends Controller
{
    public function index($computationId)
    {
        $computation = Computation::with(['client', 'tradingAccount.rows'])->findOrFail($computationId);
        $tradingAccount = $computation->tradingAccount;

        // System rows titles – exact match with ComputationController defaults
        $debitSystem = [
            'opening_stock'      => ['label' => 'Opening Stock', 'source' => 'Previous Financial Year Closing Stock / Manual Entry'],
            'register_purchases' => ['label' => 'Register Purchases', 'source' => 'Register Purchase Reconciliation → Taxable Value + Exempted/Nil Rated Value'],
            'unregister_purchases' => ['label' => 'Unregister Purchases', 'source' => 'Unregister Purchase Reconciliation → Taxable Value + Exempted/Nil Rated Value'],
            'purchase_return'    => ['label' => 'Purchase Return', 'source' => 'Purchase Return / Debit Note', 'is_adjustment' => true],
            'unloading_charges'  => ['label' => 'Unloading Charges', 'source' => 'Manual Entry'],
            'wages'              => ['label' => 'Wages', 'source' => 'Manual Entry'],
            'fuel_expenses'      => ['label' => 'Fuel Expenses', 'source' => 'Manual Entry'],
            'carriage_inward'    => ['label' => 'Carriage Inward', 'source' => 'Manual Entry'],
            'sale_platform'      => ['label' => 'Sale Platform', 'source' => 'Manual Entry'],
        ];

        $creditSystem = [
            'sales_gst'          => ['label' => 'Sales as per GST', 'source' => 'GST Sales Reconciliation → Taxable Value + Exempted/Nil Rated Value'],
            'sales_no_gst'       => ['label' => 'Sales Without GST', 'source' => 'Manual Entry'],
            'pre_sale_discount'  => ['label' => 'Pre Sale Discount', 'source' => 'Manual Entry'],
            'post_sale_discount' => ['label' => 'Post Sale Discount', 'source' => 'Manual Entry'],
            'credit_note'        => ['label' => 'Credit Note', 'source' => 'Credit Note / Sales Return'],
            'closing_stock'      => ['label' => 'Closing Stock', 'source' => 'Manual Entry / Previous FY Stock'],
        ];

        // Build debit rows from DB, ONLY system rows for matching
        $debitRows = collect();
        if ($tradingAccount) {
            $dbDebitSystemRows = $tradingAccount->rows
                ->where('side', 'dr')
                ->where('row_type', 'system')
                ->sortBy('sort_order');

            foreach ($debitSystem as $key => $def) {
                $found = $dbDebitSystemRows->firstWhere('particular_name', $def['label']);
                if ($found) {
                    $debitRows->push($found);
                } else {
                    $debitRows->push((object) [
                        'id' => null,
                        'particular_name' => $def['label'],
                        'amount' => 0,
                        'source' => $def['source'],
                        'row_type' => 'system',
                        'side' => 'dr',
                        'is_adjustment' => $def['is_adjustment'] ?? false,
                    ]);
                }
            }

            $customDebit = $tradingAccount->rows
                ->where('side', 'dr')
                ->where('row_type', 'custom')
                ->sortBy('sort_order');
            $debitRows = $debitRows->merge($customDebit);
        } else {
            foreach ($debitSystem as $key => $def) {
                $debitRows->push((object) [
                    'id' => null,
                    'particular_name' => $def['label'],
                    'amount' => 0,
                    'source' => $def['source'],
                    'row_type' => 'system',
                    'side' => 'dr',
                    'is_adjustment' => $def['is_adjustment'] ?? false,
                ]);
            }
        }

        // Same for credit rows
        $creditRows = collect();
        if ($tradingAccount) {
            $dbCreditSystemRows = $tradingAccount->rows
                ->where('side', 'cr')
                ->where('row_type', 'system')
                ->sortBy('sort_order');

            foreach ($creditSystem as $key => $def) {
                $found = $dbCreditSystemRows->firstWhere('particular_name', $def['label']);
                if ($found) {
                    $creditRows->push($found);
                } else {
                    $creditRows->push((object) [
                        'id' => null,
                        'particular_name' => $def['label'],
                        'amount' => 0,
                        'source' => $def['source'],
                        'row_type' => 'system',
                        'side' => 'cr',
                    ]);
                }
            }

            $customCredit = $tradingAccount->rows
                ->where('side', 'cr')
                ->where('row_type', 'custom')
                ->sortBy('sort_order');
            $creditRows = $creditRows->merge($customCredit);
        } else {
            foreach ($creditSystem as $key => $def) {
                $creditRows->push((object) [
                    'id' => null,
                    'particular_name' => $def['label'],
                    'amount' => 0,
                    'source' => $def['source'],
                    'row_type' => 'system',
                    'side' => 'cr',
                ]);
            }
        }

        $combinedRows = $this->buildCombinedRows($debitRows, $creditRows);

        return view('Backoffice.trading-account.index', compact('computation', 'tradingAccount', 'combinedRows'));
    }

    private function buildCombinedRows($debitRows, $creditRows)
    {
        $debitSystem = $debitRows->filter(fn($r) => $r->row_type === 'system')->values();
        $debitCustom = $debitRows->filter(fn($r) => $r->row_type === 'custom')->values();
        $creditSystem = $creditRows->filter(fn($r) => $r->row_type === 'system')->values();
        $creditCustom = $creditRows->filter(fn($r) => $r->row_type === 'custom')->values();

        // 🛡️ ADDED: Filter out any custom rows that duplicate system row names
        $systemDebitNames = $debitSystem->pluck('particular_name')->toArray();
        $debitCustom = $debitCustom->filter(fn($row) => !in_array($row->particular_name, $systemDebitNames))->values();

        $systemCreditNames = $creditSystem->pluck('particular_name')->toArray();
        $creditCustom = $creditCustom->filter(fn($row) => !in_array($row->particular_name, $systemCreditNames))->values();

        $debitMap = [];
        foreach ($debitSystem as $row) {
            $debitMap[$row->particular_name] = $row;
        }
        $creditMap = [];
        foreach ($creditSystem as $row) {
            $creditMap[$row->particular_name] = $row;
        }

        $finalRows = [];

        // Row 1: Opening Stock -> Sales as per GST
        $finalRows[] = $this->makeRow($debitMap['Opening Stock'] ?? null, $creditMap['Sales as per GST'] ?? null);

        // Row 2: Register Purchases -> Sales Without GST
        $finalRows[] = $this->makeRow($debitMap['Register Purchases'] ?? null, $creditMap['Sales Without GST'] ?? null);

        // Row 3: Unregister Purchases -> Pre Sale Discount
        $finalRows[] = $this->makeRow($debitMap['Unregister Purchases'] ?? null, $creditMap['Pre Sale Discount'] ?? null);

        // Row 4: Purchase Return (adjustment) -> (empty)
        $finalRows[] = $this->makeRow($debitMap['Purchase Return'] ?? null, null);

        // Row 5: NET PURCHASE (subtotal) -> GROSS SALES (subtotal)
        $finalRows[] = (object) [
            'debit' => (object) ['label' => 'NET PURCHASE', 'amount' => null, 'source' => '', 'is_subtotal' => true],
            'credit' => (object) ['label' => 'GROSS SALES', 'amount' => null, 'source' => '', 'is_subtotal' => true],
        ];

        // Row 6: Unloading Charges -> (empty)
        $finalRows[] = $this->makeRow($debitMap['Unloading Charges'] ?? null, null);

        // Row 7: Wages -> Post Sale Discount
        $finalRows[] = $this->makeRow($debitMap['Wages'] ?? null, $creditMap['Post Sale Discount'] ?? null);

        // Row 8: Fuel Expenses -> Credit Note
        $finalRows[] = $this->makeRow($debitMap['Fuel Expenses'] ?? null, $creditMap['Credit Note'] ?? null);

        // Row 9: Carriage Inward -> (empty)
        $finalRows[] = $this->makeRow($debitMap['Carriage Inward'] ?? null, null);

        // Row 10: Sale Platform -> (empty)
        $finalRows[] = $this->makeRow($debitMap['Sale Platform'] ?? null, null);

        // Row 11: TOTAL DIRECT EXPENSE (subtotal) -> NET SALES (subtotal)
        $finalRows[] = (object) [
            'debit' => (object) ['label' => 'TOTAL DIRECT EXPENSE', 'amount' => null, 'source' => '', 'is_subtotal' => true],
            'credit' => (object) ['label' => 'NET SALES', 'amount' => null, 'source' => '', 'is_subtotal' => true],
        ];

        // Row 12: Closing Stock (credit only)
        $finalRows[] = $this->makeRow(null, $creditMap['Closing Stock'] ?? null);

        // Add filtered custom rows
        $maxCustom = max($debitCustom->count(), $creditCustom->count());
        for ($i = 0; $i < $maxCustom; $i++) {
            $d = $debitCustom->get($i);
            $c = $creditCustom->get($i);
            $finalRows[] = $this->makeRow($d, $c);
        }

        // Row 13: GROSS PROFIT / LOSS (full width)
        $finalRows[] = (object) [
            'debit' => (object) ['label' => 'GROSS PROFIT', 'amount' => null, 'source' => '', 'is_gp' => true],
            'credit' => null,
        ];

        // Row 14: TOTAL (full width)
        $finalRows[] = (object) [
            'debit' => (object) ['label' => 'TOTAL', 'amount' => null, 'source' => '', 'is_total' => true],
            'credit' => null,
        ];

        return $finalRows;
    }

    private function makeRow($debitRow, $creditRow)
    {
        return (object) [
            'debit' => $debitRow ? (object) [
                'id' => $debitRow->id,
                'label' => $debitRow->particular_name,
                'amount' => $debitRow->amount,
                'source' => $debitRow->source ?? '',
                'is_adjustment' => $debitRow->is_adjustment ?? false,
                'row_type' => $debitRow->row_type ?? 'system',
            ] : null,
            'credit' => $creditRow ? (object) [
                'id' => $creditRow->id,
                'label' => $creditRow->particular_name,
                'amount' => $creditRow->amount,
                'source' => $creditRow->source ?? '',
                'row_type' => $creditRow->row_type ?? 'system',
            ] : null,
        ];
    }

    // ====================================================================
    // 🔥 CRUD & SAVE OPERATIONS
    // ====================================================================

    public function saveRows(Request $request, $computationId)
    {
        try {
            DB::beginTransaction();

            $computation = Computation::findOrFail($computationId);
            $tradingAccount = $computation->tradingAccount;

            if (!$tradingAccount) {
                $tradingAccount = TradingAccount::create([
                    'computation_id' => $computation->id,
                    'adjustment_type' => null,
                ]);
            }

            $allRows = $request->input('rows', []);

            foreach ($allRows as $rowData) {
                $id = $rowData['id'] ?? null;
                $amount = $rowData['amount'] ?? 0;
                $rowType = $rowData['row_type'] ?? null;

                // Strict Rule: If id exists, UPDATE
                if ($id) {
                    $row = TradingAccountRow::where('id', $id)
                                           ->where('trading_account_id', $tradingAccount->id)
                                           ->first();
                    if ($row) {
                        $row->amount = $amount;
                        $row->save();
                    } else {
                        Log::warning("TradingAccount saveRows: Row with id {$id} not found for trading_account_id {$tradingAccount->id}");
                    }
                    continue;
                }

                // Only NEW CUSTOM rows can be created when id is null
                if ($rowType === 'custom' && !empty($rowData['particular_name'])) {
                    $maxSortOrder = TradingAccountRow::where('trading_account_id', $tradingAccount->id)->max('sort_order') ?? 0;

                    TradingAccountRow::create([
                        'trading_account_id' => $tradingAccount->id,
                        'side'               => $rowData['side'] ?? 'dr',
                        'row_type'           => 'custom',
                        'particular_name'    => $rowData['particular_name'],
                        'amount'             => $amount,
                        'sort_order'         => $maxSortOrder + 1,
                    ]);
                } else {
                    Log::warning("TradingAccount saveRows: Attempted to insert system row without id. Skipping.");
                }
            }

            if ($request->has('adjustment_type')) {
                $tradingAccount->adjustment_type = $request->input('adjustment_type');
                $tradingAccount->save();
            }

            DB::commit();

            $savedRows = $tradingAccount->rows()->get();

            return response()->json([
                'success' => true,
                'message' => '✓ Changes saved successfully',
                'rows' => $savedRows->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'particular_name' => $row->particular_name,
                        'side' => $row->side,
                        'row_type' => $row->row_type,
                        'amount' => $row->amount,
                        'sort_order' => $row->sort_order,
                    ];
                })->values(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TradingAccount saveRows error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '✕ Failed to save report'
            ], 500);
        }
    }

    public function addCustomRow(Request $request, $computationId)
    {
        try {
            $request->validate([
                'side' => 'required|in:dr,cr',
                'particular_name' => 'required|string|max:255',
            ]);

            $computation = Computation::findOrFail($computationId);
            $tradingAccount = $computation->tradingAccount;

            if (!$tradingAccount) {
                return response()->json(['success' => false, 'message' => 'Trading account not found'], 404);
            }

            $maxSortOrder = TradingAccountRow::where('trading_account_id', $tradingAccount->id)->max('sort_order') ?? 0;

            $row = TradingAccountRow::create([
                'trading_account_id' => $tradingAccount->id,
                'side'               => $request->side,
                'row_type'           => 'custom',
                'particular_name'    => $request->particular_name,
                'amount'             => 0,
                'sort_order'         => $maxSortOrder + 1,
            ]);

            return response()->json([
                'success' => true,
                'row' => [
                    'id' => $row->id,
                    'particular_name' => $row->particular_name,
                    'side' => $row->side,
                    'row_type' => $row->row_type,
                    'amount' => $row->amount,
                    'sort_order' => $row->sort_order,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('TradingAccount addCustomRow error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '✕ Failed to add row'
            ], 500);
        }
    }

    public function deleteRow($computationId, $rowId)
    {
        try {
            DB::beginTransaction();

            $computation = Computation::findOrFail($computationId);
            $tradingAccount = $computation->tradingAccount;

            if (!$tradingAccount) {
                return response()->json(['success' => false, 'message' => 'Trading account not found'], 404);
            }

            $row = TradingAccountRow::where('id', $rowId)
                                    ->where('trading_account_id', $tradingAccount->id)
                                    ->first();

            if (!$row) {
                return response()->json(['success' => false, 'message' => 'Row not found'], 404);
            }

            if ($row->row_type === 'system') {
                return response()->json(['success' => false, 'message' => 'Cannot delete system rows'], 403);
            }

            $row->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Row deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TradingAccount deleteRow error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '✕ Failed to delete row'
            ], 500);
        }
    }

    public function finalize(Request $request, $computationId)
    {
        try {
            DB::beginTransaction();

            $computation = Computation::findOrFail($computationId);
            $tradingAccount = $computation->tradingAccount;

            if (!$tradingAccount) {
                return response()->json(['success' => false, 'message' => 'Trading account not found'], 404);
            }

            $allRows = $request->input('rows', []);

            foreach ($allRows as $rowData) {
                if (!empty($rowData['id'])) {
                    $row = TradingAccountRow::where('id', $rowData['id'])
                                           ->where('trading_account_id', $tradingAccount->id)
                                           ->first();
                    if ($row) {
                        $row->amount = $rowData['amount'] ?? 0;
                        $row->save();
                    }
                }
            }

            if ($request->has('adjustment_type')) {
                $tradingAccount->adjustment_type = $request->input('adjustment_type');
                $tradingAccount->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✓ Report finalized successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TradingAccount finalize error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '✕ Failed to finalize report'
            ], 500);
        }
    }
}