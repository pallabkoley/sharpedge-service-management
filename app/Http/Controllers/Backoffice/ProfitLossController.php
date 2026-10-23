<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Computation;
use App\Models\ProfitLossAccount;
use App\Models\ProfitLossCategory;
use App\Models\ProfitLossRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfitLossController extends Controller
{
    /**
     * Display the Profit & Loss page.
     */
    public function index($computationId)
    {
        $computation = Computation::with([
            'profitLossAccount.categories.rows' => function ($query) {
                $query->orderBy('sort_order');
            }
        ])->findOrFail($computationId);

        // Get or create ProfitLossAccount
        $profitLossAccount = $computation->profitLossAccount ?? new ProfitLossAccount();
        if (!$profitLossAccount->exists) {
            $profitLossAccount = ProfitLossAccount::create([
                'computation_id' => $computation->id,
            ]);
            $computation->load('profitLossAccount.categories.rows');
        }

        $categories = $profitLossAccount->categories()
            ->with('rows')
            ->orderBy('sort_order')
            ->get();

        return view('Backoffice.profit-loss.index', compact('computation', 'profitLossAccount', 'categories'));
    }

    /**
     * Load all P&L data via AJAX.
     */
    public function getData($computationId)
    {
        try {
            $computation = Computation::with([
                'profitLossAccount.categories.rows' => function ($query) {
                    $query->orderBy('sort_order');
                }
            ])->findOrFail($computationId);

            $account = $computation->profitLossAccount;
            if (!$account) {
                return response()->json([
                    'success' => true,
                    'categories' => [],
                ]);
            }

            $categories = $account->categories()
                ->with('rows')
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'success' => true,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            Log::error('Profit & Loss Load Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save all P&L data (categories and rows) via AJAX.
     */
    public function saveData(Request $request, $computationId)
    {
        DB::beginTransaction();

        try {
            $computation = Computation::findOrFail($computationId);

            // Get or create ProfitLossAccount
            $account = $computation->profitLossAccount;
            if (!$account) {
                $account = ProfitLossAccount::create([
                    'computation_id' => $computation->id,
                ]);
            }

            $validator = Validator::make($request->all(), [
                'categories' => 'nullable|array',
                'categories.*.id' => 'nullable|exists:profit_loss_categories,id',
                'categories.*.category_name' => 'required|string|max:200',
                'categories.*.side' => 'required|in:debit,credit',
                'categories.*.sort_order' => 'nullable|integer|min:0',
                'categories.*.is_system' => 'nullable|boolean',

                'categories.*.rows' => 'nullable|array',
                'categories.*.rows.*.id' => 'nullable|exists:profit_loss_rows,id',
                'categories.*.rows.*.particular_name' => 'required|string|max:255',
                'categories.*.rows.*.amount' => 'nullable|numeric',
                'categories.*.rows.*.sort_order' => 'nullable|integer|min:0',
                'categories.*.rows.*.is_system' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // ─── Process Categories ──────────────────────────────
            $categoryIds = [];

            if ($request->has('categories')) {
                foreach ($request->categories as $catData) {
                    // Create or update category
                    if (isset($catData['id']) && $catData['id']) {
                        $category = ProfitLossCategory::where('id', $catData['id'])
                            ->where('profit_loss_account_id', $account->id)
                            ->first();
                        if ($category) {
                            $category->update([
                                'category_name' => $catData['category_name'],
                                'side' => $catData['side'],
                                'sort_order' => $catData['sort_order'] ?? 0,
                                'is_system' => $catData['is_system'] ?? false,
                            ]);
                            $categoryIds[] = $category->id;
                        }
                    } else {
                        // New category
                        $category = ProfitLossCategory::create([
                            'profit_loss_account_id' => $account->id,
                            'category_name' => $catData['category_name'],
                            'side' => $catData['side'],
                            'sort_order' => $catData['sort_order'] ?? 0,
                            'is_system' => $catData['is_system'] ?? false,
                        ]);
                        $categoryIds[] = $category->id;
                    }

                    // ─── Process Rows for this category ──────────
                    if (isset($catData['rows']) && is_array($catData['rows'])) {
                        $rowIds = [];
                        foreach ($catData['rows'] as $rowData) {
                            if (isset($rowData['id']) && $rowData['id']) {
                                $row = ProfitLossRow::where('id', $rowData['id'])
                                    ->where('category_id', $category->id)
                                    ->first();
                                if ($row) {
                                    $row->update([
                                        'particular_name' => $rowData['particular_name'],
                                        'amount' => $rowData['amount'] ?? 0,
                                        'sort_order' => $rowData['sort_order'] ?? 0,
                                        'is_system' => $rowData['is_system'] ?? false,
                                    ]);
                                    $rowIds[] = $row->id;
                                }
                            } else {
                                // New row
                                $row = ProfitLossRow::create([
                                    'category_id' => $category->id,
                                    'particular_name' => $rowData['particular_name'],
                                    'amount' => $rowData['amount'] ?? 0,
                                    'sort_order' => $rowData['sort_order'] ?? 0,
                                    'is_system' => $rowData['is_system'] ?? false,
                                ]);
                                $rowIds[] = $row->id;
                            }
                        }

                        // Delete rows not present in the request
                        $existingRowIds = $category->rows()->pluck('id')->toArray();
                        $deleteRowIds = array_diff($existingRowIds, $rowIds);
                        if (!empty($deleteRowIds)) {
                            ProfitLossRow::whereIn('id', $deleteRowIds)->delete();
                        }
                    } else {
                        // If no rows sent, delete all rows for this category
                        $category->rows()->delete();
                    }
                }

                // Delete categories not present in the request
                $existingCategoryIds = $account->categories()->pluck('id')->toArray();
                $deleteCategoryIds = array_diff($existingCategoryIds, $categoryIds);
                if (!empty($deleteCategoryIds)) {
                    ProfitLossCategory::whereIn('id', $deleteCategoryIds)->delete();
                }
            } else {
                // If no categories sent, delete all
                $account->categories()->delete();
            }

            DB::commit();

            // Reload fresh data
            $account->refresh();
            $account->load('categories.rows');

            return response()->json([
                'success' => true,
                'message' => 'Profit & Loss saved successfully!',
                'categories' => $account->categories,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profit & Loss Save Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to save: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a single row.
     */
    public function deleteRow($categoryId, $rowId)
    {
        try {
            $row = ProfitLossRow::where('id', $rowId)
                ->where('category_id', $categoryId)
                ->firstOrFail();
            $row->delete();

            return response()->json([
                'success' => true,
                'message' => 'Row deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Row Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete row: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a whole category (and its rows).
     */
    public function deleteCategory($categoryId)
    {
        try {
            $category = ProfitLossCategory::findOrFail($categoryId);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Category Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalize the Profit & Loss report.
     */
    public function finalize($computationId)
    {
        try {
            // Optionally update computation status or add a flag
            // For now, just return success
            return response()->json([
                'success' => true,
                'message' => 'Profit & Loss finalized successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Finalize Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to finalize: ' . $e->getMessage()
            ], 500);
        }
    }
}